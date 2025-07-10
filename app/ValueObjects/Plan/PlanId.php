<?php

namespace App\ValueObjects\Plan;

use InvalidArgumentException;

class PlanId
{
    private int $value;

    public function __construct(int $value)
    {
        if ($value < 0) {
            throw new InvalidArgumentException('Plan ID must be a non-negative integer');
        }
        $this->value = $value;
    }

    public static function generate(): self
    {
        // Generar un ID temporal para nuevos planes
        // En producción, esto sería manejado por la base de datos
        return new self(mt_rand(1, 999999));
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function equals(PlanId $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }
} 