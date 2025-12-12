<?php

namespace App\Services;

use App\Models\Campaign;
use App\Models\CampaignMessage;
use App\Models\SendingIdentity;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
use Symfony\Component\Mailer\Transport\TransportInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class CampaignSenderService
{
    public function send(Campaign $campaign, int $batchSize = 10, bool $respectDelay = false): array
    {
        $campaign->load(['contactList.contacts', 'sendingIdentity']);
        $identity = $campaign->sendingIdentity;

        if ($campaign->status === 'paused') {
            Log::info('Campaign paused - skipping send', ['campaign_id' => $campaign->id]);
            return ['sent' => 0, 'failed' => 0, 'rescheduled' => true];
        }

        Log::info('Campaign send started', [
            'campaign_id' => $campaign->id,
            'identity_id' => $identity?->id,
            'contact_count' => $campaign->contactList?->contacts?->count(),
            'send_mode' => $identity?->send_mode,
        ]);

        if (!$identity->is_active) {
            throw new \RuntimeException('Wybrana tożsamość nadawcy jest nieaktywna.');
        }

        if ($campaign->contactList->contacts->isEmpty()) {
            $campaign->update([
                'status' => 'failed',
                'scheduled_at' => null,
            ]);

            return [
                'sent' => 0,
                'failed' => 0,
                'rescheduled' => false,
                'error' => 'Lista kontaktów jest pusta.',
            ];
        }

        $totalMessages = $campaign->messages()->count();
        if ($totalMessages === 0) {
            $this->createMessages($campaign);
            $totalMessages = $campaign->messages()->count();
        }

        $campaign->update([
            'status' => 'sending',
            'scheduled_at' => null,
        ]);

        $transport = $this->buildTransport($identity);
        $sent = 0;
        $failed = 0;
        $rescheduled = false;

        $unsentTotal = $campaign->messages()->whereNull('sent_at')->count();
        $messages = $campaign->messages()
            ->with('contact')
            ->whereNull('sent_at')
            ->limit($batchSize)
            ->get();
        Log::info('Campaign messages loaded', [
            'campaign_id' => $campaign->id,
            'messages_total' => $messages->count(),
            'unsent_total' => $unsentTotal,
            'batch_size' => $batchSize,
        ]);

        if ($messages->isEmpty()) {
            $campaign->update(['status' => $sent > 0 ? 'sent' : 'failed']);
            Log::warning('Campaign send: no messages to process', [
                'campaign_id' => $campaign->id,
                'total_messages' => $totalMessages,
                'unsent_total' => $unsentTotal,
            ]);
            return [
                'sent' => $sent,
                'failed' => $failed,
                'rescheduled' => false,
            ];
        }

        foreach ($messages as $message) {
            try {
                $windowStatus = $this->waitForWindow($campaign);

                if ($windowStatus instanceof \DateTimeInterface) {
                    $campaign->update([
                        'status' => 'scheduled',
                        'scheduled_at' => $windowStatus,
                    ]);
                    $rescheduled = true;
                    break;
                }

                $subject = $this->pickSubject($campaign);
                $baseHtml = $this->pickContent($campaign);
                $html = $this->prepareHtml($campaign, $message, $baseHtml);
                $sentMessage = $this->sendEmail($transport, $campaign, $message, $subject, $html);

                $message->update([
                    'sent_at' => now(),
                    'message_id' => $sentMessage?->getMessageId(),
                ]);

                $sent++;
            } catch (\Throwable $e) {
                $failed++;
                Log::warning('Błąd wysyłki kampanii', [
                    'campaign_id' => $campaign->id,
                    'message_id' => $message->id,
                    'error' => $e->getMessage(),
                    'trace' => substr($e->getTraceAsString(), 0, 2000),
                ]);

                // jeśli serwer odmawia (np. limity), przerwij, by nie wyzerować sesji
                if (str_contains($e->getMessage(), 'Too many') || str_contains($e->getMessage(), 'rate') || str_contains($e->getMessage(), 'AUTH')) {
                    break;
                }
            }

            $min = max(0, (int) $campaign->send_interval_seconds);
            $max = max($min, (int) ($campaign->send_interval_max_seconds ?? $min));
            $delay = $min === $max ? $min : random_int($min, $max);

            if ($respectDelay && $delay > 0) {
                sleep($delay);
            }
        }

        $remaining = $campaign->messages()->whereNull('sent_at')->count();
        if ($remaining > 0) {
            $campaign->update(['status' => 'sending']);
            $rescheduled = true;
        } elseif (!$rescheduled) {
            $status = ($sent > 0) ? 'sent' : (($failed > 0) ? 'failed' : $campaign->status);
            $campaign->update(['status' => $status]);
        }

        Log::info('Campaign send finished', [
            'campaign_id' => $campaign->id,
            'sent' => $sent,
            'failed' => $failed,
            'rescheduled' => $rescheduled,
            'final_status' => $campaign->status,
        ]);

        return [
            'sent' => $sent,
            'failed' => $failed,
            'rescheduled' => $rescheduled,
        ];
    }

    private function createMessages(Campaign $campaign): void
    {
        $now = now();
        $campaign->contactList->contacts()
            ->select('contacts.id', 'contacts.email')
            ->chunk(100, function ($contacts) use ($campaign, $now) {
                $records = [];
                foreach ($contacts as $contact) {
                    $records[] = [
                        'user_id' => $campaign->user_id,
                        'campaign_id' => $campaign->id,
                        'contact_id' => $contact->id,
                        'to_email' => $contact->email,
                        'unsubscribe_token' => $campaign->enable_unsubscribe ? Str::uuid()->toString() : null,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
                if ($records) {
                    CampaignMessage::insert($records);
                }
            });
    }

    private function buildTransport(SendingIdentity $identity): TransportInterface
    {
        $transport = new EsmtpTransport(
            $identity->smtp_host,
            (int) $identity->smtp_port,
            $identity->smtp_encryption ?: null
        );

        $transport->setUsername($identity->smtp_username);
        $transport->setPassword($identity->smtp_password);

        return $transport;
    }

    private function sendEmail(TransportInterface $transport, Campaign $campaign, CampaignMessage $message, string $subject, string $html): ?SentMessage
    {
        $identity = $campaign->sendingIdentity;

        $email = (new Email())
            ->from(new Address($identity->from_email, $identity->name))
            ->to($message->to_email)
            ->subject($subject)
            ->html($html);

        $sentMessage = $transport->send($email);

        if ($identity->send_mode === 'imap') {
            $this->appendToImapSent($identity, $email);
        }

        return $sentMessage;
    }

    private function prepareHtml(Campaign $campaign, CampaignMessage $message, string $html): string
    {
        if ($campaign->track_clicks) {
            $html = $this->rewriteLinks($html, $message);
        }

        if ($campaign->track_opens) {
            $pixelUrl = route('tracking.open', [
                'message' => $message->id,
                'token' => $message->unsubscribe_token ?? 'open',
            ], true);

            $pixel = '<img src="' . htmlspecialchars($pixelUrl, ENT_QUOTES) . '" width="1" height="1" style="display:none;">';
            $html = $this->appendToBody($html, $pixel);
        }

        if ($campaign->enable_unsubscribe && $message->unsubscribe_token) {
            $unsubscribeUrl = route('tracking.unsubscribe', [
                'token' => $message->unsubscribe_token,
            ], true);

            $unsubscribe = '<p style="font-size:12px;color:#6b7280;">Jeśli nie chcesz otrzymywać tych wiadomości, <a href="'
                . htmlspecialchars($unsubscribeUrl, ENT_QUOTES)
                . '">kliknij tutaj, aby się wypisać</a>.</p>';

            $html = $this->appendToBody($html, $unsubscribe);
        }

        return $html;
    }

    private function pickSubject(Campaign $campaign): string
    {
        $subjects = array_values(array_filter([
            $campaign->subject,
            ...($campaign->extra_subjects ?? []),
        ]));

        if (empty($subjects)) {
            return $campaign->subject;
        }

        return $subjects[array_rand($subjects)];
    }

    private function pickContent(Campaign $campaign): string
    {
        $contents = array_values(array_filter([
            $campaign->html_content,
            ...($campaign->extra_contents ?? []),
        ]));

        if (empty($contents)) {
            return $campaign->html_content;
        }

        return $contents[array_rand($contents)];
    }

    private function rewriteLinks(string $html, CampaignMessage $message): string
    {
        return preg_replace_callback('/<a\s[^>]*href=[\"\\\']([^\"\\\']+)[\"\\\'][^>]*>/i', function ($matches) use ($message) {
            $href = $matches[1];

            if (!str_starts_with($href, 'http')) {
                return $matches[0];
            }

            $trackedUrl = route('tracking.click', [
                'message' => $message->id,
                'token' => $message->unsubscribe_token ?? 'track',
            ], true) . '?url=' . urlencode($href);

            return str_replace($href, $trackedUrl, $matches[0]);
        }, $html);
    }

    private function appendToBody(string $html, string $snippet): string
    {
        $position = stripos($html, '</body>');

        if ($position !== false) {
            return substr_replace($html, $snippet . '</body>', $position, strlen('</body>'));
        }

        return $html . $snippet;
    }

    private function waitForWindow(Campaign $campaign): ?\DateTimeInterface
    {
        $schedule = $campaign->sending_window_schedule;
        if (is_array($schedule) && $this->hasEnabledDay($schedule)) {
            return $this->nextStartFromSchedule($schedule);
        }

        if (! $campaign->sending_window_enabled || ! $campaign->sending_window_start || ! $campaign->sending_window_end) {
            return null;
        }

        $now = now();
        $start = now()->setTimeFromTimeString($campaign->sending_window_start);
        $end = now()->setTimeFromTimeString($campaign->sending_window_end);

        if ($now->betweenIncluded($start, $end)) {
            return null;
        }

        if ($now->lessThan($start)) {
            return $start;
        }

        $nextStart = $start->addDay();
        return $nextStart;
    }

    private function nextStartFromSchedule(array $schedule): ?\DateTimeInterface
    {
        $now = now();

        for ($i = 0; $i < 7; $i++) {
            $day = strtolower($now->copy()->addDays($i)->englishDayOfWeek);
            $config = $schedule[$day] ?? null;

            if (!$config || !($config['enabled'] ?? false) || empty($config['start']) || empty($config['end'])) {
                continue;
            }

            $start = $now->copy()->addDays($i)->setTimeFromTimeString($config['start']);
            $end = $now->copy()->addDays($i)->setTimeFromTimeString($config['end']);

            if ($i === 0 && $now->betweenIncluded($start, $end)) {
                return null;
            }

            if ($i === 0 && $now->lessThan($start)) {
                return $start;
            }

            if ($i > 0) {
                return $start;
            }
        }

        return null;
    }

    private function hasEnabledDay(array $schedule): bool
    {
        foreach ($schedule as $config) {
            if ($config['enabled'] ?? false) {
                return true;
            }
        }

        return false;
    }

    private function appendToImapSent(SendingIdentity $identity, Email $email): void
    {
        try {
            if (! function_exists('imap_open')) {
                return;
            }

            $host = $identity->imap_host ?? $identity->smtp_host;
            $port = $identity->imap_port ?? 993;
            $encryption = $identity->imap_encryption; // '', ssl, tls
            $username = $identity->imap_username ?? $identity->smtp_username;
            $password = $identity->imap_password ?? $identity->smtp_password;

            if (! $host || ! $username || ! $password) {
                return;
            }

            if (! $encryption) {
                $encryption = 'none';
            }

            if ($encryption === 'ssl' && (int) $port === 143) {
                $port = 993;
            }
            if ($encryption === 'tls' && (int) $port === 993) {
                $port = 143;
            }

            $flags = '/imap';
            if ($encryption === 'tls') {
                $flags .= '/tls/novalidate-cert';
            } elseif ($encryption === 'ssl') {
                $flags .= '/ssl/novalidate-cert';
            } else { // none / plain
                $flags .= '/notls/novalidate-cert';
            }

            $folders = [
                $identity->imap_sent_folder ?: 'Sent',
                'INBOX.Sent',
                'Sent',
                'INBOX/Sent',
            ];

            foreach ($folders as $folder) {
                $mailbox = sprintf('{%s:%d%s}%s', $host, (int) $port, $flags, $folder);
                $stream = @imap_open($mailbox, $username, $password, 0, 1);

                if ($stream) {
                    $rawMessage = $email->toString();
                    @imap_append($stream, $mailbox, $rawMessage);
                    @imap_close($stream);
                    break;
                }
            }
        } catch (\Throwable $e) {
            Log::warning('IMAP append failed', [
                'identity_id' => $identity->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
