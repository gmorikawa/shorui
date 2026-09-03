<?php

namespace App\Core\User;

use App\Core\Auth\PlainPassword;

class UpdateUser
{
    public readonly string $name;
    public readonly string $email;
    public readonly PlainPassword $currentPassword;

    public function __construct(
        string $name,
        string $email,
        PlainPassword $currentPassword
    ) {
        $this->name = $name;
        $this->email = $email;
        $this->currentPassword = $currentPassword;
    }
}
