<?php

namespace App\ValueObjects\Plan;

use InvalidArgumentException;

class UserLimit
{
    private int $value;

    public function __construct(int $value)
    {
        if ($value < 1) {
            throw new InvalidArgumentException('User limit must be at least 1');
        }
        
        if ($value > 1000000) {
            throw new InvalidArgumentException('User limit cannot exceed 1,000,000');
        }
        
        $this->value = $value;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function equals(UserLimit $other): bool
    {
        return $this->value === $other->value;
    }

    public function isUnlimited(): bool
    {
        return $this->value === -1; // -1 representa ilimitado
    }

    public function canAddUser(int $currentUsers): bool
    {
        if ($this->isUnlimited()) {
            return true;
        }
        
        return $currentUsers < $this->value;
    }

    public function getRemainingSlots(int $currentUsers): int
    {
        if ($this->isUnlimited()) {
            return -1; // -1 representa ilimitado
        }
        
        return max(0, $this->value - $currentUsers);
    }

    public function __toString(): string
    {
        if ($this->isUnlimited()) {
            return 'Unlimited';
        }
        
        return (string) $this->value;
    }
} 