<?php

namespace App\User\Service;

use App\User\Entity\Token;
use Symfony\Component\Uid\Uuid;

final class Tokenizer
{
    public function __construct(private \DateInterval $interval) {}

    public function generate(\DateTimeImmutable $date): Token
    {
        return new Token(
            Uuid::v4()->toRfc4122(),
            $date->add($this->interval)
        );
    }
}
