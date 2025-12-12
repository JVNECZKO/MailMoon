<?php

namespace App\Services;

use App\Models\SendingIdentity;

class ImapTestService
{
    public function appendTest(SendingIdentity $identity): bool|string
    {
        if (! class_exists(\Webklex\IMAP\Client::class)) {
            return 'Pakiet IMAP nie jest dostępny.';
        }

        $host = $identity->imap_host ?? $identity->smtp_host;
        $port = $identity->imap_port ?? 993;
        $encryption = $identity->imap_encryption; // '', ssl, tls
        $username = $identity->imap_username ?? $identity->smtp_username;
        $password = $identity->imap_password ?? $identity->smtp_password;

        if (! $host || ! $username || ! $password) {
            return 'Brak konfiguracji IMAP (host/login/hasło).';
        }

        // dostosuj port do szyfrowania
        if ($encryption === 'ssl' && (int) $port === 143) {
            $port = 993;
        }
        if ($encryption === 'tls' && (int) $port === 993) {
            $port = 143;
        }

        try {
            $config = [
                'host'          => $host,
                'port'          => (int) $port,
                'protocol'      => 'imap',
                'encryption'    => $encryption ?: 'ssl',
                'validate_cert' => false,
                'username'      => $username,
                'password'      => $password,
                'authentication'=> 'login',
                'timeout'       => 10,
            ];

            $client = new \Webklex\IMAP\Client($config);
            $client->connect();

            $folderName = $identity->imap_sent_folder ?? 'Sent';
            $folder = $client->getFolder($folderName) ?: $client->getFolder('INBOX.Sent');

            if (! $folder) {
                return 'Nie znaleziono folderu „Sent”.';
            }

            $dummyMessage = "From: {$identity->from_email}\r\n"
                . "To: {$identity->from_email}\r\n"
                . "Subject: MailMoon IMAP test\r\n"
                . "\r\n"
                . "Test append to Sent folder.";

            $folder->appendMessage($dummyMessage);
            $client->disconnect();

            return true;
        } catch (\Throwable $e) {
            return 'IMAP błąd: ' . $e->getMessage();
        }
    }
}
