<?php

namespace App\ValueObjects\User;

use InvalidArgumentException;

class UserId
{
    private int $value;

    public function __construct(int $value)
    {
        if ($value < 0) {
            throw new InvalidArgumentException('User ID must be a non-negative integer');
        }
        $this->value = $value;
    }

    public static function generate(): self
    {
        // ID temporal para nuevos usuarios (será reemplazado por la BD)
        return new self(0);
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function equals(UserId $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }
} 