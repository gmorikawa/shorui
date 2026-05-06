<?php

namespace App\Enums;

enum FileState: string
{
    case UPLOADING = 'UPLOADING';
    case AVAILABLE = 'AVAILABLE';
    case CORRUPTED = 'CORRUPTED';

    public static function fromString(string $state): self
    {
        return match ($state) {
            'UPLOADING' => self::UPLOADING,
            'AVAILABLE' => self::AVAILABLE,
            'CORRUPTED' => self::CORRUPTED,
            default => throw new \InvalidArgumentException("Invalid file state: $state"),
        };
    }
}