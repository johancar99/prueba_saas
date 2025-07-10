<?php

namespace App\ValueObjects\Company;

class CompanyName
{
    private string $value;

    public function __construct(string $value)
    {
        if (empty(trim($value))) {
            throw new \InvalidArgumentException('Company name cannot be empty');
        }

        if (strlen($value) > 255) {
            throw new \InvalidArgumentException('Company name cannot exceed 255 characters');
        }

        $this->value = trim($value);
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function equals(CompanyName $other): bool
    {
        return $this->value === $other->getValue();
    }

    public function __toString(): string
    {
        return $this->value;
    }
} 