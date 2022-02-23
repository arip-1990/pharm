<?php

namespace App\User\Command\ChangeEmail\Confirm;

use Symfony\Component\Validator\Constraints as Assert;

final class Command
{
    #[Assert\NotBlank]
    public string $token = '';
}
