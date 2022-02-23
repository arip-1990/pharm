<?php

namespace App\User\Command\ResetPassword\Reset;

use App\Flusher;
use App\User\Entity\UserRepository;
use App\User\Service\PasswordHasher;

final class Handler
{
    public function __construct(private UserRepository $repo, private PasswordHasher $hasher, private Flusher $flusher) {}

    public function handle(Command $command): void
    {
        if (!$user = $this->repo->findByResetToken($command->token)) {
            throw new \DomainException('Токен не найден.');
        }

        $user->resetPassword($command->token, new \DateTimeImmutable(), $this->hasher->hash($command->password));

        $this->flusher->flush();
    }
}
