<?php

namespace App\ValueObjects\Plan;

use InvalidArgumentException;

class MonthlyPrice
{
    private float $value;

    public function __construct(float $value)
    {
        if ($value < 0) {
            throw new InvalidArgumentException('Monthly price cannot be negative');
        }
        
        if ($value > 999999.99) {
            throw new InvalidArgumentException('Monthly price cannot exceed 999,999.99');
        }
        
        $this->value = round($value, 2);
    }

    public function getValue(): float
    {
        return $this->value;
    }

    public function equals(MonthlyPrice $other): bool
    {
        return $this->value === $other->value;
    }

    public function add(MonthlyPrice $other): self
    {
        return new self($this->value + $other->value);
    }

    public function subtract(MonthlyPrice $other): self
    {
        $result = $this->value - $other->value;
        if ($result < 0) {
            throw new InvalidArgumentException('Result cannot be negative');
        }
        return new self($result);
    }

    public function multiply(float $factor): self
    {
        if ($factor <= 0) {
            throw new InvalidArgumentException('Factor must be positive');
        }
        return new self($this->value * $factor);
    }

    public function __toString(): string
    {
        return number_format($this->value, 2);
    }
} 