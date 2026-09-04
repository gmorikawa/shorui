<?php

namespace App\Services;

use App\Core\DocumentType\CreateDocumentType;
use App\Core\DocumentType\DocumentTypeID;
use App\Core\DocumentType\UpdateDocumentType;
use App\Exceptions\NotFoundException;
use App\Models\DocumentType;
use Illuminate\Database\Eloquent\Collection;

class DocumentTypeService
{
    /**
     * Get all document types with their attributes.
     *
     * @return Collection|DocumentType[]
     */
    public function findAll(): Collection
    {
        return DocumentType::all();
    }

    /**
     * Find a document type by its ID.
     *
     * @param DocumentTypeID $id
     * @return DocumentType
     * @throws NotFoundException
     */
    public function findById(DocumentTypeID $id): DocumentType
    {
        $type = DocumentType::find($id->value);

        if (!$type) {
            throw new NotFoundException("Document type with ID $id not found");
        }

        return $type;
    }

    /**
     * Create a new document type.
     *
     * @param CreateDocumentType $data
     * @return DocumentType
     */
    public function create(CreateDocumentType $data): DocumentType
    {
        $type = DocumentType::create([
            'name' => $data->name,
            'description' => $data->description,
        ]);

        if ($data->attributes !== []) {
            $type->attributes()->sync($data->attributes);
        }

        return $type;
    }

    /**
     * Update an existing document type.
     *
     * @param DocumentTypeID $id
     * @param UpdateDocumentType $data
     * @return DocumentType
     * @throws NotFoundException
     */
    public function update(DocumentTypeID $id, UpdateDocumentType $data): DocumentType
    {
        $type = $this->findById($id);

        $type->update([
            'name' => $data->name,
            'description' => $data->description,
        ]);

        if ($data->attributes !== null) {
            $type->attributes()->sync($data->attributes);
        }

        return $type;
    }

    /**
     * Delete a document type by its ID.
     *
     * @param DocumentTypeID $id
     * @throws NotFoundException
     */
    public function delete(DocumentTypeID $id): void
    {
        $this->findById($id)->delete();
    }
}
