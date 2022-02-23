<?php

namespace App\User\Command\ChangeEmail\Request;

use App\Flusher;
use App\Type\Email;
use App\Type\Id;
use App\User\Entity\UserRepository;
use App\User\Service\NewEmailConfirmTokenSender;
use App\User\Service\Tokenizer;

final class Handler
{
    public function __construct(
        private UserRepository $repo,
        private Tokenizer $tokenizer,
        private NewEmailConfirmTokenSender $sender,
        private Flusher $flusher
    ) {}

    public function handle(Command $command): void
    {
        $user = $this->repo->get(new Id($command->id));

        $email = new Email($command->email);

        if ($this->repo->hasByEmail($email)) {
            throw new \DomainException('Email уже используется.');
        }

        $date = new \DateTimeImmutable();

        $user->requestEmailChanging($token = $this->tokenizer->generate($date), $date, $email);

        $this->flusher->flush();

        $this->sender->send($email, $token);
    }
}
