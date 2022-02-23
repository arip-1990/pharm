<?php

namespace App\User\Command\JoinByEmail\Request;

use Symfony\Component\Validator\Constraints as Assert;

final class Command
{
    #[Assert\NotBlank]
    #[Assert\Email]
    public string $email = '';

    #[Assert\NotBlank]
    #[Assert\Length(min: 6)]
    public string $password = '';

    #[Assert\NotBlank]
    #[Assert\Length(min: 3)]
    public string $name = '';
}
