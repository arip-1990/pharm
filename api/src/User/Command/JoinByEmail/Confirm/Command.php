<?php

namespace App\User\Command\JoinByEmail\Confirm;

use Symfony\Component\Validator\Constraints as Assert;

final class Command
{
    #[Assert\NotBlank]
    public string $token = '';
}
