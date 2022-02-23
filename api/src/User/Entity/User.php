<?php

namespace App\User\Entity;

use App\Type\Email;
use App\Type\Id;
use App\Type\Phone;
use App\Type\Status;
use App\User\Service\PasswordHasher;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
#[ORM\Table(name: 'users')]
final class User
{
    #[ORM\Id]
    #[ORM\Column(type: 'id')]
    private Id $id;
    #[ORM\Column(type: 'email', unique: true)]
    private Email $email;
    #[ORM\Column(type: 'phone', unique: true, nullable: true)]
    private ?Phone $phone;
    #[ORM\Embedded(class: Name::class, columnPrefix: 'name')]
    private Name $name;
    #[ORM\Column(type: 'status', length: 16)]
    private Status $status;
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $password = null;
    #[ORM\Embedded(class: Token::class)]
    private ?Token $confirmToken = null;
    #[ORM\Embedded(class: Token::class)]
    private ?Token $resetToken = null;
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $tmp = null;
    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;
    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $updatedAt;
    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $deletedAt = null;
    #[ORM\ManyToOne(targetEntity: Role::class, inversedBy: 'users')]
    #[ORM\JoinColumn(name: 'role_id', referencedColumnName: 'id', onDelete: 'cascade')]
    private Role $role;
    #[ORM\ManyToMany(targetEntity: Grant::class, inversedBy: "users")]
    #[ORM\JoinTable(name: 'users_grants')]
    private Collection $grants;

    private function __construct(Id $id, Email $email, Name $name, \DateTimeImmutable $date, Phone $phone = null)
    {
        $this->id = $id;
        $this->email = $email;
        $this->name = $name;
        $this->status = Status::wait();
        $this->createdAt = $date;
        $this->updatedAt = $date;
        $this->role = Role::user();
        $this->phone = $phone;
        $this->grants = new ArrayCollection();
    }

    public static function requestJoinByEmail(Id $id, Email $email, Name $name, \DateTimeImmutable $date, string $hash, Token $token): self
    {
        $user = new self($id, $email, $name, $date);
        $user->password = $hash;
        $user->confirmToken = $token;
        return $user;
    }

    public function confirmJoin(string $token, \DateTimeImmutable $date): void
    {
        if ($this->confirmToken === null) {
            throw new \DomainException('Подтверждение не требуется.');
        }
        $this->confirmToken->validate($token, $date);
        $this->status = Status::active();
        $this->confirmToken = null;
    }

    public function requestPasswordReset(Token $token, \DateTimeImmutable $date): void
    {
        if (!$this->isActive()) {
            throw new \DomainException('Пользователь не активен.');
        }
        if ($this->resetToken !== null && !$this->resetToken->isExpiredTo($date)) {
            throw new \DomainException('Сброс уже запрошен.');
        }
        $this->resetToken = $token;
    }

    public function resetPassword(string $token, \DateTimeImmutable $date, string $hash): void
    {
        if ($this->resetToken === null) {
            throw new \DomainException('Сброс не запрашивается.');
        }
        $this->resetToken->validate($token, $date);
        $this->resetToken = null;
        $this->password = $hash;
    }

    public function changePassword(string $current, string $new, PasswordHasher $hasher): void
    {
        if ($this->password === null) {
            throw new \DomainException('У пользователя нет пароля.');
        }
        if (!$hasher->validate($current, $this->password)) {
            throw new \DomainException('Неверный пароль.');
        }
        $this->password = $hasher->hash($new);
    }

    public function requestEmailChanging(Token $token, \DateTimeImmutable $date, Email $email): void
    {
        if (!$this->isActive()) {
            throw new \DomainException('Пользователь не активен.');
        }
        if ($this->email->isEqualTo($email)) {
            throw new \DomainException('Электронная почта та же.');
        }
        if ($this->resetToken !== null && !$this->resetToken->isExpiredTo($date)) {
            throw new \DomainException('Изменение уже запрошено.');
        }
        $this->tmp = $email->getValue();
        $this->resetToken = $token;
    }

    public function confirmEmailChanging(string $token, \DateTimeImmutable $date): void
    {
        if ($this->tmp === null || $this->resetToken === null) {
            throw new \DomainException('Изменение не запрашивается.');
        }
        $this->resetToken->validate($token, $date);
        $this->email = new Email($this->tmp);
        $this->tmp = null;
        $this->resetToken = null;
    }

    public function changeRole(Role $role): void
    {
        $this->role = $role;
    }

    public function remove(): void
    {
        if (!$this->isWait()) {
            throw new \DomainException('Не удалось удалить активного пользователя.');
        }
    }

    public function isWait(): bool
    {
        return $this->status->isWait();
    }

    public function isActive(): bool
    {
        return $this->status->isActive();
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getRole(): Role
    {
        return $this->role;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function getConfirmToken(): ?Token
    {
        return $this->confirmToken;
    }

    public function getResetToken(): ?Token
    {
        return $this->resetToken;
    }

    #[ORM\PostLoad]
    public function checkEmbeds(): void
    {
        if ($this->confirmToken && $this->confirmToken->isEmpty())
            $this->confirmToken = null;
        if ($this->resetToken && $this->resetToken->isEmpty())
            $this->resetToken = null;
    }
}
