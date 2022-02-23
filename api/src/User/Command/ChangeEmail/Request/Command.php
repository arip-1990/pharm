<?php

namespace App\User\Command\ChangeEmail\Request;

use Symfony\Component\Validator\Constraints as Assert;

final class Command
{
    #[Assert\NotBlank]
    #[Assert\Uuid]
    public string $id = '';

    #[Assert\NotBlank]
    #[Assert\Email]
    public string $email = '';
}
