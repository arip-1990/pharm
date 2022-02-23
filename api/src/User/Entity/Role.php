<?php

namespace App\User\Entity;

use App\Type\Id;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'roles')]
final class Role
{
    #[ORM\Id]
    #[ORM\Column(type: 'id')]
    private Id $id;
    #[ORM\Column(type: 'string', length: 16)]
    private string $name;
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $description;
    #[ORM\ManyToMany(targetEntity: Grant::class, inversedBy: "roles")]
    #[ORM\JoinTable(name: 'roles_grants')]
    private Collection $grants;

    public const USER = 'user';
    public const MANAGER = 'manager';
    public const ADMIN = 'admin';

    public function __construct(string $name, string $description = null)
    {
        $this->id = Id::generate();
        $this->name = $name;
        $this->description = $description;
        $this->grants = new ArrayCollection();
    }

    public function isUser(): bool
    {
        return $this->name === self::USER;
    }

    public function isManager(): bool
    {
        return $this->name === self::MANAGER;
    }

    public function isAdmin(): bool
    {
        return $this->name === self::ADMIN;
    }

    public static function user(): self
    {
        return new self(self::USER);
    }

    public static function manager(): self
    {
        return new self(self::MANAGER);
    }

    public static function admin(): self
    {
        return new self(self::ADMIN);
    }

    public function getName(): string
    {
        return $this->name;
    }
}
