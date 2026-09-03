<?php

namespace App\Core\Auth;

/**
 * Wrapper class to represent a plain text password.
 */
class PlainPassword
{
    public readonly string $password;

    public function __construct(string $password)
    {
        $this->password = $password;
    }

    public function __toString(): string
    {
        return $this->password;
    }
}
