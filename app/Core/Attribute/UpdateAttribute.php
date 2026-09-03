<?php

namespace App\Core\Attribute;

class UpdateAttribute
{
    public readonly string $label;
    public readonly ?string $description;

    public function __construct(
        string $label,
        ?string $description,
    ) {
        $this->label = $label;
        $this->description = $description;
    }
}
