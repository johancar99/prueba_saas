<?php

namespace App\ValueObjects\User;

use InvalidArgumentException;

class Name
{
    private string $value;

    public function __construct(string $value)
    {
        $trimmedValue = trim($value);
        
        if (empty($trimmedValue)) {
            throw new InvalidArgumentException('Name cannot be empty');
        }
        
        if (strlen($trimmedValue) < 2) {
            throw new InvalidArgumentException('Name must be at least 2 characters long');
        }
        
        if (strlen($trimmedValue) > 255) {
            throw new InvalidArgumentException('Name cannot exceed 255 characters');
        }
        
        $this->value = $trimmedValue;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function equals(Name $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
} 