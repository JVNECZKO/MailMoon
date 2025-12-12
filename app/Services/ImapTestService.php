<?php

namespace App\Services;

use App\Models\SendingIdentity;

class ImapTestService
{
    public function appendTest(SendingIdentity $identity): bool|string
    {
        if (! function_exists('imap_open')) {
            return 'Funkcja IMAP nie jest dostępna (brak rozszerzenia).';
        }

        $host = $identity->imap_host ?? $identity->smtp_host;
        $port = $identity->imap_port ?? 993;
        $encryption = $identity->imap_encryption; // '', ssl, tls
        $username = $identity->imap_username ?? $identity->smtp_username;
        $password = $identity->imap_password ?? $identity->smtp_password;

        if (! $host || ! $username || ! $password) {
            return 'Brak konfiguracji IMAP (host/login/hasło).';
        }

        $flags = '/imap';
        if ($encryption === 'tls') {
            $flags .= '/tls/novalidate-cert/auth=LOGIN';
        } elseif ($encryption === 'ssl') {
            $flags .= '/ssl/novalidate-cert/auth=LOGIN';
        } else {
            $flags .= '/notls/novalidate-cert/auth=LOGIN';
        }

        $mailbox = sprintf('{%s:%d%s}Sent', $host, $port, $flags);

        $stream = @imap_open($mailbox, $username, $password, 0, 1);

        if (! $stream) {
            return 'Nie można połączyć z IMAP: ' . (imap_last_error() ?: 'brak szczegółów');
        }

        $dummyMessage = "From: {$identity->from_email}\r\n"
            . "To: {$identity->from_email}\r\n"
            . "Subject: MailMoon IMAP test\r\n"
            . "\r\n"
            . "Test append to Sent folder.";

        $ok = @imap_append($stream, $mailbox, $dummyMessage);
        @imap_close($stream);

        if (! $ok) {
            return 'IMAP append nie powiódł się: ' . (imap_last_error() ?: 'brak szczegółów');
        }

        return true;
    }
}
