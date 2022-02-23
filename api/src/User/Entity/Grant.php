<?php

namespace App\User\Entity;

use App\Type\Id;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'grants')]
final class Grant
{
    #[ORM\Id]
    #[ORM\Column(type: 'id')]
    private Id $id;
    #[ORM\Column(type: 'string')]
    private string $name;
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $description;

    public function __construct(string $name, string $description = null)
    {
        $this->id = Id::generate();
        $this->name = $name;
        $this->description = $description;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
