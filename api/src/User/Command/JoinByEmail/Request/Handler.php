<?php

namespace App\User\Command\JoinByEmail\Request;

use App\Flusher;
use App\Type\Email;
use App\Type\Id;
use App\User\Entity\Name;
use App\User\Entity\User;
use App\User\Entity\UserRepository;
use App\User\Service\JoinConfirmationSender;
use App\User\Service\PasswordHasher;
use App\User\Service\Tokenizer;

final class Handler
{
    public function __construct(
        private UserRepository $repo,
        private PasswordHasher $hasher,
        private Tokenizer $tokenizer,
        private Flusher $flusher,
        private JoinConfirmationSender $sender
    ) {}

    public function handle(Command $command): void
    {
        $email = new Email($command->email);

        if ($this->repo->hasByEmail($email)) {
            throw new \DomainException('Пользователь уже существует.');
        }

        $name = explode(' ', $command->name);
        if (count($name) > 1) $name = new Name($name[1], $name[0], $name[2] ?? null);
        else $name = new Name($name[0]);
        $date = new \DateTimeImmutable();

        $user = User::requestJoinByEmail(
            Id::generate(),
            $email,
            $name,
            $date,
            $this->hasher->hash($command->password),
            $token = $this->tokenizer->generate($date)
        );

        $this->repo->add($user);

        $this->flusher->flush();

        $this->sender->send($email, $token);
    }
}
