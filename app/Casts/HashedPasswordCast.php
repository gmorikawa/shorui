<?php

namespace App\Casts;

use App\Core\Auth\HashedPassword;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class HashedPasswordCast implements CastsAttributes
{
    /**
     * Cast the raw value from the database into the complex object.
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): ?HashedPassword
    {
        if (!$value) {
            return null;
        }

        return new HashedPassword($value);
    }

    /**
     * Transform the complex object back into a raw database string.
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if (!$value) {
            return null;
        }

        if (!$value instanceof HashedPassword) {
            throw new \InvalidArgumentException('The value must be an instance of HashedPassword.');
        }

        return $value->password;
    }
}