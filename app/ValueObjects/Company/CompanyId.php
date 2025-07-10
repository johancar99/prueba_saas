<?php

namespace App\ValueObjects\Company;

class CompanyId
{
    private int $value;

    public function __construct(int $value)
    {
        $this->value = $value;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function equals(CompanyId $other): bool
    {
        return $this->value === $other->getValue();
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }
} 