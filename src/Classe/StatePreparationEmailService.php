<?php

namespace App\Classe;

use App\Entity\Order;
use Psr\Log\LoggerInterface;

class StatePreparationEmailService
{

    private LoggerInterface $logger;
    private Mail $mail;

    public function __construct(LoggerInterface $logger, Mail $mail)
    {
        $this->logger = $logger;
        $this->mail = $mail;
    }

    public function sendOrderPreparationEmail(Order $order): void
    {
        /*$content = "Bonjour, ". $order->getUser()->getFirstName(). ' '. $order->getUser()->getLastName();
        $this->mail->send($order->getUser()->getEmail(), $order->getUser()->getFirstName(). ' '. $order->getUser()->getLastName(), 'Votre commande est en cours de préparation.', $content);
        $this->logger->info('Email de confirmation envoyé', ['to_email' => $order->getUser()->getEmail()]);*/

       /* $user = $order->getUser();
        $content = <<<EOD
            Bonjour {$user->getFirstName()} {$user->getLastName()},
            Nous souhaitons vous informer que votre commande n°{$order->getRef()} est actuellement en cours de préparation / livraison.
            
            Nous mettons tout en œuvre pour que votre commande vous parvienne dans les délais impartis et en parfait état.
            
            Si vous avez des questions ou des préoccupations concernant votre commande, n'hésitez pas à nous contacter à l'adresse e-mail ou au numéro de téléphone figurant sur notre site web.
            
            Nous vous remercions pour votre confiance et votre fidélité.
        EOD;

        $this->mail->send($user->getEmail(), "{$user->getFirstName()} {$user->getLastName()}", "État de votre commande n°{$order->getRef()}", $content);
        $this->logger->info('Email de confirmation envoyé', ['to_email' => $user->getEmail()]);*/

        $user = $order->getUser();
        $orderId = $order->getRef();

        $subject = "État de votre commande n°$orderId";

        $content = "Bonjour {$user->getFirstName()} {$user->getLastName()},<br>";
        $content .= "Nous souhaitons vous informer que votre commande n°$orderId est actuellement en cours de préparation.<br>";
        $content .= "Nous mettons tout en œuvre pour que votre commande vous parvienne dans les délais impartis et en parfait état.<br><br>";
        $content .= "Si vous avez des questions ou des préoccupations concernant votre commande, n'hésitez pas à nous contacter à l'adresse e-mail ou au numéro de téléphone figurant sur notre site web.<br><br>";
        $content .= "Nous vous remercions pour votre confiance et votre fidélité.";

        $this->mail->send($user->getEmail(), "{$user->getFirstName()} {$user->getLastName()}", $subject, $content);

        $this->logger->info('Email de confirmation envoyé', ['to_email' => $user->getEmail()]);

    }
}