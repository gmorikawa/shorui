<?php

namespace App\Core\Attribute;

class CreateAttribute
{
    public readonly string $key;
    public readonly string $label;
    public readonly ?string $description;

    public function __construct(
        string $key,
        string $label,
        ?string $description,
    ) {
        $this->key = $key;
        $this->label = $label;
        $this->description = $description;
    }
}
