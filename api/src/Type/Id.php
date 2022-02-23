<?php

namespace App\Type;


use Symfony\Component\Uid\Uuid;

final class Id
{
    private string $value;

    public function __construct(string $value)
    {
        $this->value = mb_strtolower($value);
    }

    public function __toString(): string
    {
        return $this->getValue();
    }

    public static function generate(): self
    {
        return new self(Uuid::v4()->toRfc4122());
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function equal(self $id): bool
    {
        return $this->value === $id->value;
    }
}
