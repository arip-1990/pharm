<?php

namespace App\User\Command\ChangePassword;

use Symfony\Component\Validator\Constraints as Assert;

final class Command
{
    #[Assert\NotBlank]
    #[Assert\Uuid]
    public string $id = '';

    #[Assert\NotBlank]
    #[Assert\Length(min: 6)]
    public string $current = '';

    #[Assert\NotBlank]
    #[Assert\Length(min: 6)]
    public string $new = '';
}
