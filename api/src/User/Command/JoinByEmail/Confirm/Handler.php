<?php

namespace App\User\Command\JoinByEmail\Confirm;

use App\Flusher;
use App\User\Entity\UserRepository;

final class Handler
{
    public function __construct(private UserRepository $repo, private Flusher $flusher) {}

    public function handle(Command $command): void
    {
        if (!$user = $this->repo->findByConfirmToken($command->token)) {
            throw new \DomainException('Неправильный токен.');
        }

        $user->confirmJoin($command->token, new \DateTimeImmutable());

        $this->flusher->flush();
    }
}
