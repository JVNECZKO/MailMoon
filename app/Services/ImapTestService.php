<?php

namespace App\Services;

use App\Models\SendingIdentity;

class ImapTestService
{
    public function appendTest(SendingIdentity $identity): bool|string
    {
        if (! function_exists('imap_open')) {
            return 'Funkcja IMAP (imap_open) jest niedostępna na serwerze.';
        }

        $host = $identity->imap_host ?? $identity->smtp_host;
        $port = $identity->imap_port ?? 993;
        $encryption = $identity->imap_encryption; // '', ssl, tls
        $username = $identity->imap_username ?? $identity->smtp_username;
        $password = $identity->imap_password ?? $identity->smtp_password;

        if (! $host || ! $username || ! $password) {
            return 'Brak konfiguracji IMAP (host/login/hasło).';
        }

        // domyśl do plain/notls gdy brak
        if (!$encryption) {
            $encryption = 'none';
        }

        // dostosuj port do szyfrowania
        if ($encryption === 'ssl' && (int) $port === 143) {
            $port = 993;
        }
        if ($encryption === 'tls' && (int) $port === 993) {
            $port = 143;
        }

        try {
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

            $lastError = null;
            foreach ($folders as $folder) {
                $mailbox = sprintf('{%s:%d%s}%s', $host, (int) $port, $flags, $folder);
                $stream = @imap_open($mailbox, $username, $password, 0, 1);

                if (! $stream) {
                    $lastError = imap_last_error();
                    continue;
                }

                $dummyMessage = "From: {$identity->from_email}\r\n"
                    . "To: {$identity->from_email}\r\n"
                    . "Subject: MailMoon IMAP test\r\n"
                    . "\r\n"
                    . "Test append to Sent folder.";

                $ok = @imap_append($stream, $mailbox, $dummyMessage);
                @imap_close($stream);

                if ($ok) {
                    return true;
                }

                $lastError = imap_last_error();
            }

            return 'Nie można połączyć z IMAP: ' . ($lastError ?: 'brak szczegółów');
        } catch (\Throwable $e) {
            return 'IMAP błąd: ' . $e->getMessage();
        }
    }
}
