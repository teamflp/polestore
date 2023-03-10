<?php

namespace App\Classe;

use App\Entity\User;
use Psr\Log\LoggerInterface;

class RegisterEmailService
{
    private LoggerInterface $logger;
    private Mail $mail;

    public function __construct(LoggerInterface $logger, Mail $mail)
    {
        $this->logger = $logger;
        $this->mail = $mail;
    }

    public function sendRegistrationConfirmationEmail(User $user): void
    {
        $content = "Bonjour, ". $user->getFirstName(). ' '. $user->getLastName() . "\n Bienvenue sur notre boutique.\n\n";
        $this->mail->send($user->getEmail(), $user->getFirstName(). ' '. $user->getLastName(), 'Bienvenue sur notre boutique', $content);
        $this->logger->info('Email de confirmation d\'inscription envoyé', ['to_email' => $user->getEmail()]);
    }
}