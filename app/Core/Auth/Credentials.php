<?php

namespace App\Core\Auth;

class Credentials
{
    public function __construct(
        public readonly string $email,
        public readonly PlainPassword $password
    ) { }
}