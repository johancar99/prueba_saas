<?php

namespace App\ValueObjects\Company;

class CompanyEmail
{
    private string $value;

    public function __construct(string $value)
    {
        // Primero hacer trim para validar correctamente
        $trimmedValue = trim($value);
        
        if (empty($trimmedValue)) {
            throw new \InvalidArgumentException('Company email cannot be empty');
        }

        if (strlen($trimmedValue) > 255) {
            throw new \InvalidArgumentException('Company email cannot exceed 255 characters');
        }

        if (!filter_var($trimmedValue, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Invalid email format');
        }

        $this->value = strtolower($trimmedValue);
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function equals(CompanyEmail $other): bool
    {
        return $this->value === $other->getValue();
    }

    public function __toString(): string
    {
        return $this->value;
    }
} 