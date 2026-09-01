<?php

namespace App\Services;

use App\Exceptions\NotFoundException;
use App\Models\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AttributeService
{
    public function findAll(): Collection
    {
        return Attribute::all();
    }

    public function findById(string $key): Attribute
    {
        $attribute = Attribute::find($key);

        if (!$attribute) {
            throw new NotFoundException('Attribute not found');
        }

        return $attribute;
    }

    /**
     * @throws ValidationException
     */
    public function create(array $data): Attribute
    {
        $validator = Validator::make($data, [
            'key' => 'required|string|max:50|unique:attributes,key',
            'label' => 'required|string|max:255|unique:attributes,label',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return Attribute::create($validator->validated());
    }

    /**
     * @throws NotFoundException
     * @throws ValidationException
     */
    public function update(string $key, array $data): Attribute
    {
        $attribute = $this->findById($key);

        $validator = Validator::make($data, [
            'label' => 'sometimes|string|max:255|unique:attributes,label,' . $key . ',key',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $attribute->update($validator->validated());

        return $attribute;
    }

    /**
     * @throws NotFoundException
     */
    public function delete(string $key): void
    {
        $this->findById($key)->delete();
    }
}