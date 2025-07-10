<?php

namespace App\ValueObjects\Plan;

use InvalidArgumentException;

class Features
{
    private array $features;

    public function __construct(array $features)
    {
        if (empty($features)) {
            throw new InvalidArgumentException('Features cannot be empty');
        }
        
        // Validar que todas las características sean strings
        foreach ($features as $feature) {
            if (!is_string($feature)) {
                throw new InvalidArgumentException('All features must be strings');
            }
            
            if (empty(trim($feature))) {
                throw new InvalidArgumentException('Feature cannot be empty');
            }
        }
        
        $this->features = array_unique(array_map('trim', $features));
    }

    public function getFeatures(): array
    {
        return $this->features;
    }

    public function hasFeature(string $feature): bool
    {
        return in_array(trim($feature), $this->features);
    }

    public function addFeature(string $feature): self
    {
        $trimmedFeature = trim($feature);
        
        if (empty($trimmedFeature)) {
            throw new InvalidArgumentException('Feature cannot be empty');
        }
        
        $newFeatures = $this->features;
        $newFeatures[] = $trimmedFeature;
        
        return new self(array_unique($newFeatures));
    }

    public function removeFeature(string $feature): self
    {
        $trimmedFeature = trim($feature);
        $newFeatures = array_filter($this->features, function($f) use ($trimmedFeature) {
            return $f !== $trimmedFeature;
        });
        
        if (empty($newFeatures)) {
            throw new InvalidArgumentException('Cannot remove all features');
        }
        
        return new self(array_values($newFeatures));
    }

    public function count(): int
    {
        return count($this->features);
    }

    public function isEmpty(): bool
    {
        return empty($this->features);
    }

    public function equals(Features $other): bool
    {
        if (count($this->features) !== count($other->features)) {
            return false;
        }
        
        sort($this->features);
        sort($other->features);
        
        return $this->features === $other->features;
    }

    public function __toString(): string
    {
        return implode(', ', $this->features);
    }
} 