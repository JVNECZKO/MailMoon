<?php

namespace App\Services;

use App\Models\Warming;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
use Symfony\Component\Mime\Email;

class WarmingSenderService
{
    public function run(): array
    {
        $warmings = Warming::with(['sendingIdentity', 'contactList.contacts'])
            ->where('status', 'running')
            ->get();

        $summary = [];

        foreach ($warmings as $warming) {
            $summary[$warming->id] = $this->runWarming($warming);
        }

        return $summary;
    }

    private function runWarming(Warming $warming): array
    {
        $schedule = $warming->schedule ?? [];
        $target = $schedule[$warming->day_current - 1] ?? $warming->daily_target ?? 0;

        $contacts = $warming->contactList->contacts;
        if ($contacts->isEmpty()) {
            $warming->update(['status' => 'paused']);
            return ['error' => 'Brak kontaktów na liście'];
        }

        $transport = new EsmtpTransport(
            $warming->sendingIdentity->smtp_host,
            (int) $warming->sendingIdentity->smtp_port,
            $warming->sendingIdentity->smtp_encryption ?: null
        );
        $transport->setUsername($warming->sendingIdentity->smtp_username);
        $transport->setPassword($warming->sendingIdentity->smtp_password);

        $sent = 0;
        $currentIndex = 0;

        foreach ($contacts as $contact) {
            if ($sent >= $target) {
                break;
            }

            try {
                $email = (new Email())
                    ->from($warming->sendingIdentity->from_email)
                    ->to($contact->email)
                    ->subject($warming->subject)
                    ->text($warming->body ?? 'Test MailMoon');

                $transport->send($email);
                $sent++;
            } catch (\Throwable $e) {
                Log::warning('Warming send failed', [
                    'warming_id' => $warming->id,
                    'error' => $e->getMessage(),
                ]);
            }

            $currentIndex++;
            sleep(max(1, (int) $warming->send_interval_seconds));
        }

        $warming->update([
            'sent_today' => $sent,
            'total_sent' => $warming->total_sent + $sent,
            'last_run_at' => now(),
        ]);

        // przejście do kolejnego dnia
        if ($warming->day_current >= $warming->day_total) {
            $warming->update(['status' => 'finished', 'finished_at' => now()]);
        } else {
            $warming->update([
                'day_current' => $warming->day_current + 1,
                'daily_target' => $schedule[$warming->day_current] ?? $warming->daily_target,
            ]);
        }

        return ['sent' => $sent, 'target' => $target];
    }
}
