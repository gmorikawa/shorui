<?php

namespace App\Core\Attribute;

/**
 * Wrapper class to represent an attribute key.
 */
class AttributeKey
{
    public readonly string $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
