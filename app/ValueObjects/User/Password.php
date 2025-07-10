<?php

namespace App\ValueObjects\User;

use InvalidArgumentException;

class Password
{
    private string $value;
    private bool $isHashed;

    public function __construct(string $value, bool $isHashed = false)
    {
        if (empty($value)) {
            throw new InvalidArgumentException('Password cannot be empty');
        }
        
        if (!$isHashed && strlen($value) < 8) {
            throw new InvalidArgumentException('Password must be at least 8 characters long');
        }
        
        if (!$isHashed && strlen($value) > 255) {
            throw new InvalidArgumentException('Password cannot exceed 255 characters');
        }
        
        $this->value = $value;
        $this->isHashed = $isHashed;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function isHashed(): bool
    {
        return $this->isHashed;
    }

    public function hash(): self
    {
        if ($this->isHashed) {
            return $this;
        }
        
        return new self(password_hash($this->value, PASSWORD_DEFAULT), true);
    }

    public function verify(string $plainPassword): bool
    {
        if (!$this->isHashed) {
            return $this->value === $plainPassword;
        }
        
        return password_verify($plainPassword, $this->value);
    }

    public function needsRehash(): bool
    {
        if (!$this->isHashed) {
            return true;
        }
        
        return password_needs_rehash($this->value, PASSWORD_DEFAULT);
    }

    public function equals(Password $other): bool
    {
        return $this->value === $other->value && $this->isHashed === $other->isHashed;
    }

    public function __toString(): string
    {
        return $this->value;
    }
} 