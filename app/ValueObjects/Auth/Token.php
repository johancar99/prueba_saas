<?php

namespace App\ValueObjects\Auth;

use InvalidArgumentException;

class Token
{
    private string $value;
    private ?string $plainTextValue;

    public function __construct(string $value, ?string $plainTextValue = null)
    {
        if (empty($value)) {
            throw new InvalidArgumentException('Token cannot be empty');
        }
        
        $this->value = $value;
        $this->plainTextValue = $plainTextValue;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getPlainTextValue(): ?string
    {
        return $this->plainTextValue;
    }

    public function equals(Token $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
} 