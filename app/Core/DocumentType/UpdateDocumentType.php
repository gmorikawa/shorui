<?php

namespace App\Core\DocumentType;

class UpdateDocumentType
{
    public readonly string $name;
    public readonly ?string $description;
    public readonly ?array $attributes;

    public function __construct(
        string $name,
        ?string $description,
        ?array $attributes,
    ) {
        $this->name = $name;
        $this->description = $description;
        $this->attributes = $attributes;
    }
}
