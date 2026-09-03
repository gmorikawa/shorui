<?php

namespace App\Core\Auth;

use Illuminate\Support\Facades\Hash;

class PasswordHasher
{
    /**
     * Hash a plain password.
     *
     * @param PlainPassword $plain
     * @return HashedPassword
     */
    public function hash(PlainPassword $plain): HashedPassword
    {
        return new HashedPassword(Hash::make($plain->password));
    }

    /**
     * Check if a plain password matches a hashed password.
     *
     * @param PlainPassword $plain
     * @param HashedPassword $hashed
     * @return bool
     */
    public function check(HashedPassword $hashed, PlainPassword $plain): bool
    {
        return Hash::check($plain->password, $hashed->password);
    }
}