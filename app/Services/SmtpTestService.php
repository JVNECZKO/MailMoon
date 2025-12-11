<?php

namespace App\Services;

use App\Models\SendingIdentity;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
use Symfony\Component\Mime\Email;

class SmtpTestService
{
    public function sendTestEmail(SendingIdentity $identity, string $recipient): void
    {
        $transport = new EsmtpTransport(
            $identity->smtp_host,
            (int) $identity->smtp_port,
            $identity->smtp_encryption ?: null
        );

        $transport->setUsername($identity->smtp_username);
        $transport->setPassword($identity->smtp_password);

        $email = (new Email())
            ->from($identity->from_email)
            ->to($recipient)
            ->subject('MailMoon SMTP test')
            ->text('SMTP konfiguracja działa poprawnie dla ' . $identity->name);

        $transport->send($email);
    }
}
