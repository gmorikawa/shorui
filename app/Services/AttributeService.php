<?php

namespace App\Services;

use App\Core\Attribute\AttributeKey;
use App\Core\Attribute\CreateAttribute;
use App\Core\Attribute\UpdateAttribute;
use App\Exceptions\NotFoundException;
use App\Models\Attribute;
use Illuminate\Database\Eloquent\Collection;

class AttributeService
{
    /**
     * Retrieves all attributes.
     *
     * @return Collection
     */
    public function findAll(): Collection
    {
        return Attribute::all();
    }

    /**
     * Finds an attribute by its key.
     *
     * @param AttributeKey $key The key of the attribute to find.
     * @return Attribute
     * @throws NotFoundException
     */
    public function findByKey(AttributeKey $key): Attribute
    {
        $attribute = Attribute::find($key->value);

        if (!$attribute) {
            throw new NotFoundException('Attribute not found');
        }

        return $attribute;
    }

    /**
     * Creates a new attribute.
     *
     * @param CreateAttribute $data The data for the new attribute.
     * @return Attribute
     */
    public function create(CreateAttribute $data): Attribute
    {
        return Attribute::create([
            'key' => $data->key,
            'label' => $data->label,
            'description' => $data->description,
        ]);
    }

    /**
     * Updates an existing attribute by its key.
     * 
     * @param AttributeKey $key The key of the attribute to update.
     * @param UpdateAttribute $data The data to update the attribute with.
     * @throws NotFoundException
     */
    public function update(AttributeKey $key, UpdateAttribute $data): Attribute
    {
        $attribute = $this->findByKey($key);

        $attribute->update([
            'label' => $data->label,
            'description' => $data->description,
        ]);

        return $attribute;
    }

    /**
     * Deletes an attribute by its key.
     *
     * @param AttributeKey $key The key of the attribute to delete.
     * @throws NotFoundException
     */
    public function delete(AttributeKey $key): void
    {
        $attribute = $this->findByKey($key);

        $attribute->delete();
    }
}