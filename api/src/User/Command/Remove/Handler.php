<?php

namespace App\User\Command\Remove;

use App\Flusher;
use App\Type\Id;
use App\User\Entity\UserRepository;

final class Handler
{
    public function __construct(private UserRepository $repo, private Flusher $flusher) {}

    public function handle(Command $command): void
    {
        $user = $this->repo->get(new Id($command->id));

        $user->remove();

        $this->repo->remove($user);

        $this->flusher->flush();
    }
}
