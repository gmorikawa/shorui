<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'ADMIN';
    case MEMBER = 'MEMBER';

    public function label(): string
    {
        return match ($this) {
            self::ADMIN => 'Administrator',
            self::MEMBER => 'Member',
        };
    }

    public static function fromString(string $value): self
    {
        return match (strtoupper($value)) {
            'ADMIN' => self::ADMIN,
            'MEMBER' => self::MEMBER,
            default => throw new \InvalidArgumentException("Invalid user role: $value"),
        };
    }
}