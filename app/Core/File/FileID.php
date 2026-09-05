<?php

namespace App\Core\File;

/**
 * Wrapper class to represent a file ID.
 */
class FileID
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
