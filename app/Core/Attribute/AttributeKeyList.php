<?php

namespace App\Core\Attribute;

use App\Models\Attribute;

class AttributeKeyList
{
    /**
     * @param array<int, AttributeKey> $attributes
     */
    public readonly array $attributes;

    public function __construct(array $attributes)
    {
        $this->attributes = array_map(
            fn($attribute) => $this->mapAttribute($attribute),
            $attributes
        );
    }

    private function mapAttribute(mixed $attribute)
    {
        if ($attribute instanceof Attribute) {
            return $attribute->key;
        }

        if ($attribute instanceof AttributeKey) {
            return $attribute;
        }

        if (is_string($attribute)) {
            return new AttributeKey($attribute);
        }

        return null;
    }
}
