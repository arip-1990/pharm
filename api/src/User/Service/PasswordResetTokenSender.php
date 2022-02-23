<?php

namespace App\User\Service;

use App\Type\Email;
use App\User\Entity\Token;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email as MimeEmail;
use Twig\Environment;

final class PasswordResetTokenSender
{
    public function __construct(private MailerInterface $mailer, private Environment $twig) {}

    public function send(Email $email, Token $token): void
    {
        $message = (new MimeEmail())
            ->subject('Сброс пароля')
            ->to($email->getValue())
            ->html($this->twig->render('auth/password/confirm.html.twig', ['token' => $token]), 'text/html');

        $this->mailer->send($message);
    }
}
