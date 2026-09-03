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

    /**
     * Get a list of all user roles as strings with comma separation.
     *
     * @return string
     */
    public static function toStringList(): string
    {
        return implode(',', array_map(fn($role) => $role->value, self::cases()));
    }
}