<?php

namespace App\User\Command\ResetPassword\Request;

use App\Flusher;
use App\Type\Email;
use App\User\Entity\UserRepository;
use App\User\Service\PasswordResetTokenSender;
use App\User\Service\Tokenizer;

final class Handler
{
    public function __construct(
        private UserRepository $repo,
        private Tokenizer $tokenizer,
        private Flusher $flusher,
        private PasswordResetTokenSender $sender
    ) {}

    public function handle(Command $command): void
    {
        $email = new Email($command->email);

        $user = $this->repo->getByEmail($email);

        $date = new \DateTimeImmutable();

        $user->requestPasswordReset($token = $this->tokenizer->generate($date), $date);

        $this->flusher->flush();

        $this->sender->send($email, $token);
    }
}
