<?php

namespace App\Core\Document;

class UpdateDocument
{
    public readonly ?string $title;
    public readonly ?string $description;
    public readonly ?string $typeId;
    public readonly ?array $attributes;
    public readonly string $folderId;
    public readonly string $fileId;

    public function __construct(
        ?string $title,
        ?string $description,
        ?string $typeId,
        ?array $attributes,
        string $folderId,
        string $fileId,
    ) {
        $this->title = $title;
        $this->description = $description;
        $this->typeId = $typeId;
        $this->attributes = $attributes;
        $this->folderId = $folderId;
        $this->fileId = $fileId;
    }
}
