<?php

namespace App\ValueObjects\Company;

class CompanyPhone
{
    private string $value;

    public function __construct(string $value)
    {
        if (empty(trim($value))) {
            throw new \InvalidArgumentException('Company phone cannot be empty');
        }

        // Remove all non-digit characters
        $cleanPhone = preg_replace('/[^0-9]/', '', $value);

        if (strlen($cleanPhone) < 7 || strlen($cleanPhone) > 15) {
            throw new \InvalidArgumentException('Invalid phone number format');
        }

        $this->value = $cleanPhone;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function equals(CompanyPhone $other): bool
    {
        return $this->value === $other->getValue();
    }

    public function __toString(): string
    {
        return $this->value;
    }
} 