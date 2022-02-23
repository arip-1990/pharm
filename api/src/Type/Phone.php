<?php

namespace App\Type;

final class Phone
{
    private string $value;

    public function __construct(string $value)
    {
        $this->value = preg_replace('/^8(\d{10})/', '7$1', $value);
    }

    public function format(bool $mobile = true): string
    {
        if ($mobile)
            return preg_replace('/^(7)(\d{3})(\d{3})(\d{2})(\d{2})/', '+$1($2) $3 $4 $5', $this->value);
        return preg_replace('/^(7)(\d{4})(\d{3})(\d{3})/', '+$1($2) $3-$4', $this->value);
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
