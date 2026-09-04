<?php

namespace App\Core\DocumentType;

/**
 * Wrapper class to represent a document type ID.
 */
class DocumentTypeID
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
