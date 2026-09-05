<?php

namespace App\Core\Document;

/**
 * Wrapper class to represent a document ID.
 */
class DocumentID
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
