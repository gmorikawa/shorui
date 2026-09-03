<?php

namespace App\Core\User;

/**
 * Wrapper class to represent a user ID.
 */
class UserID
{
    public readonly string $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function __toString(): string
    {
        return $this->id;
    }
}
