<?php

namespace App\User\Command\ChangePassword;

use App\Flusher;
use App\Type\Id;
use App\User\Entity\UserRepository;
use App\User\Service\PasswordHasher;

final class Handler
{
    public function __construct(private UserRepository $repo, private PasswordHasher $hasher, private Flusher $flusher) {}

    public function handle(Command $command): void
    {
        $user = $this->repo->get(new Id($command->id));

        $user->changePassword($command->current, $command->new, $this->hasher);

        $this->flusher->flush();
    }
}
