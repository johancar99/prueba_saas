<?php

namespace App\ValueObjects\User;

class CompanyId
{
    private ?int $value;

    public function __construct(?int $value = null)
    {
        $this->value = $value;
    }

    public function getValue(): ?int
    {
        return $this->value;
    }

    public function isNull(): bool
    {
        return $this->value === null;
    }

    public function equals(?CompanyId $other): bool
    {
        if ($other === null) {
            return $this->isNull();
        }
        return $this->value === $other->getValue();
    }

    public static function fromNullable(?int $value): self
    {
        return new self($value);
    }
} 