<?php

namespace App\User\Command\ResetPassword\Reset;

use Symfony\Component\Validator\Constraints as Assert;

final class Command
{
    #[Assert\NotBlank]
    public string $token = '';

    #[Assert\NotBlank]
    #[Assert\Length(min: 6)]
    public string $password = '';
}
