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

        $contacts = $warming->contactList->contacts->sortBy('id')->values();
        if ($contacts->isEmpty()) {
            $warming->update(['status' => 'paused']);
            return ['error' => 'Brak kontaktów na liście'];
        }

        $target = min($target, $contacts->count());
        if ($target <= 0) {
            return ['skipped' => 'no_target_for_today'];
        }

        // jeżeli dzienny limit osiągnięty, przejdź do kolejnego dnia
        if ($warming->sent_today >= $target) {
            return $this->advanceDay($warming, $schedule);
        }

        // wysyłamy małą porcję, aby żądanie cron nie wisiało (reszta w kolejnym uruchomieniu crona)
        $remaining = $target - $warming->sent_today;
        $batchSize = min($remaining, 10); // maks 10 maili na jedno wywołanie crona
        $startIndex = $warming->sent_today;
        $batch = $contacts->slice($startIndex, $batchSize);

        $transport = new EsmtpTransport(
            $warming->sendingIdentity->smtp_host,
            (int) $warming->sendingIdentity->smtp_port,
            $warming->sendingIdentity->smtp_encryption ?: null
        );
        $transport->setUsername($warming->sendingIdentity->smtp_username);
        $transport->setPassword($warming->sendingIdentity->smtp_password);

        $sent = 0;
        foreach ($batch as $contact) {
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
        }

        $warming->update([
            'sent_today' => $warming->sent_today + $sent,
            'total_sent' => $warming->total_sent + $sent,
            'last_run_at' => now(),
        ]);

        if ($warming->sent_today >= $target) {
            return $this->advanceDay($warming, $schedule, $sent, $target);
        }

        return ['sent' => $sent, 'target' => $target];
    }

    private function advanceDay(Warming $warming, array $schedule, int $justSent = 0, int $target = 0): array
    {
        if ($warming->day_current >= $warming->day_total) {
            $warming->update(['status' => 'finished', 'finished_at' => now()]);
            return ['finished' => true, 'sent' => $justSent, 'target' => $target];
        }

        $warming->update([
            'day_current' => $warming->day_current + 1,
            'daily_target' => $schedule[$warming->day_current] ?? $warming->daily_target,
            'sent_today' => 0,
            'last_run_at' => now(),
        ]);

        return ['advanced_day' => true, 'sent' => $justSent, 'target' => $target];
    }
}
