<?php

namespace App\Type;

final class Email
{
    private string $value;

    public function __construct(string $value)
    {
        $this->value = mb_strtolower($value);
    }

    public function isEqualTo(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
