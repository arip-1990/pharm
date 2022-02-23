<?php

namespace App\User\Command\ChangeRole;

use Symfony\Component\Validator\Constraints as Assert;

final class Command
{
    #[Assert\NotBlank]
    #[Assert\Uuid]
    public string $id = '';

    #[Assert\NotBlank]
    public string $role = '';
}
