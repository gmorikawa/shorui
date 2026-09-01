<?php

namespace App\Services;

use App\Exceptions\NotFoundException;
use App\Models\DocumentType;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class DocumentTypeService
{
    public function findAll(): Collection
    {
        return DocumentType::all()->load('attributes');
    }

    public function findById(string $id): DocumentType
    {
        $type = DocumentType::find($id);

        if (!$type) {
            throw new NotFoundException("Document type with ID $id not found");
        }

        return $type;
    }

    /**
     * @throws ValidationException
     */
    public function create(array $data): DocumentType
    {
        $validator = Validator::make($data, [
            'name' => 'required|string|max:255|unique:document_types,name',
            'description' => 'nullable|string',
            'attributes' => 'sometimes|array',
            'attributes.*' => 'string|exists:attributes,key',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $data = $validator->validated();
        $type = DocumentType::create(collect($data)->except('attributes')->all());

        if (array_key_exists('attributes', $data)) {
            $type->attributes()->sync($data['attributes']);
        }

        return $type->load('attributes');
    }

    /**
     * @throws NotFoundException
     * @throws ValidationException
     */
    public function update(string $id, array $data): DocumentType
    {
        $type = $this->findById($id);

        $validator = Validator::make($data, [
            'name' => 'sometimes|string|max:255|unique:document_types,name,' . $id,
            'description' => 'nullable|string',
            'attributes' => 'sometimes|array',
            'attributes.*' => 'string|exists:attributes,key',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $data = $validator->validated();
        $type->update(collect($data)->except('attributes')->all());

        if (array_key_exists('attributes', $data)) {
            $type->attributes()->sync($data['attributes']);
        }

        return $type->load('attributes');
    }

    /**
     * @throws NotFoundException
     */
    public function delete(string $id): void
    {
        $this->findById($id)->delete();
    }
}
