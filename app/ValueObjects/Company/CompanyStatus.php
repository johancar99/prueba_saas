<?php

namespace App\ValueObjects\Company;

class CompanyStatus
{
    private bool $value;

    public function __construct(bool $value)
    {
        $this->value = $value;
    }

    public function getValue(): bool
    {
        return $this->value;
    }

    public function isActive(): bool
    {
        return $this->value;
    }

    public function isInactive(): bool
    {
        return !$this->value;
    }

    public function equals(CompanyStatus $other): bool
    {
        return $this->value === $other->getValue();
    }

    public function __toString(): string
    {
        return $this->value ? 'active' : 'inactive';
    }
} 