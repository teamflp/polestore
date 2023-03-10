<?php

namespace App\Classe;

use Mailjet\Client;
use Mailjet\Resources;
use Psr\Log\LoggerInterface;

class Mail
{
    const FROM_EMAIL = 'paterne81@hotmail.fr';
    const FROM_NAME = 'Boutique Poles';
    const TEMPLATE_ID = 4627741;

    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function send(string $to_email, string $to_name, string $subject, string $content): void
    {
        $mj = new Client(MailConfig::getApiKey(), MailConfig::getApiSecretKey(), true, ['version' => 'v3.1', 'timeout' => 30]);
        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => self::FROM_EMAIL,
                        'Name' => self::FROM_NAME
                    ],
                    'To' => [
                        [
                            'Email' => $to_email,
                            'Name' => $to_name
                        ]
                    ],
                    'TemplateID' => self::TEMPLATE_ID,
                    'TemplateLanguage' => true,
                    'Subject' => $subject,
                    'Variables' => [
                        'content' => $content
                    ]
                ]
            ]
        ];

        try {
            $response = $mj -> post(Resources ::$Email, ['body' => $body]);
            $response->success();
            $this->logger->info('Email envoyé', ['to_email' => $to_email, 'subject' => $subject]);
        } catch (\Exception $e) {
            $this->logger->error('Error sending email', ['error' => $e->getMessage()]);
        }
    }
}