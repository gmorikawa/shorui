<?php

namespace App\Core\Folder;

/**
 * Wrapper class to represent a folder ID.
 */
class FolderID
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
    