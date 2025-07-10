<?php

namespace App\ValueObjects\Company;

class CompanyAddress
{
    private string $value;

    public function __construct(string $value)
    {
        if (empty(trim($value))) {
            throw new \InvalidArgumentException('Company address cannot be empty');
        }

        if (strlen($value) > 500) {
            throw new \InvalidArgumentException('Company address cannot exceed 500 characters');
        }

        $this->value = trim($value);
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function equals(CompanyAddress $other): bool
    {
        return $this->value === $other->getValue();
    }

    public function __toString(): string
    {
        return $this->value;
    }
} 