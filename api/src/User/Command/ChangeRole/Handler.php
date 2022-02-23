<?php

namespace App\User\Command\ChangeRole;

use App\Flusher;
use App\Type\Id;
use App\User\Entity\Role;
use App\User\Entity\UserRepository;

final class Handler
{
    public function __construct(private UserRepository $repo, private Flusher $flusher) {}

    public function handle(Command $command): void
    {
        $user = $this->repo->get(new Id($command->id));

        $user->changeRole(new Role($command->role));

        $this->flusher->flush();
    }
}
