<?php

namespace App\User\Service;

use App\Type\Email;
use App\User\Entity\Token;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email as MimeEmail;
use Twig\Environment;

final class JoinConfirmationSender
{
    public function __construct(private MailerInterface $mailer, private Environment $twig) {}

    public function send(Email $email, Token $token): void
    {
        $message = (new MimeEmail())
            ->subject('Подтверждение почты')
            ->to($email->getValue())
            ->html($this->twig->render('auth/join/confirm.html.twig', ['token' => $token]), 'text/html');

        $this->mailer->send($message);
    }
}
