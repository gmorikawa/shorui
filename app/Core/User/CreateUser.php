<?php

namespace App\Core\User;

use App\Core\Auth\PlainPassword;
use App\Enums\UserRole;

class CreateUser
{
    public readonly string $name;
    public readonly string $email;
    public readonly PlainPassword $password;
    public readonly UserRole $role;

    public function __construct(
        string $name,
        string $email,
        PlainPassword $password,
        UserRole $role
    ) {
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->role = $role;
    }
}
