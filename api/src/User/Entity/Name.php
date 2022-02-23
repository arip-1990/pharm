<?php

namespace App\User\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
final class Name
{
    #[ORM\Column(type: 'string')]
    private string $first;
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $last;
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $middle;

    public function __construct(string $firstName, string $lastName = null, string $middleName = null)
    {
        $this->first = $firstName;
        $this->last = $lastName;
        $this->middle = $middleName;
    }

    public function getFirstName(): string
    {
        return $this->first;
    }

    public function getLastName(): string
    {
        return $this->last;
    }

    public function getMiddleName(): ?string
    {
        return $this->middle;
    }

    public function __toString(): string
    {
        return $this->first . ' ' . $this->last;
    }
}
