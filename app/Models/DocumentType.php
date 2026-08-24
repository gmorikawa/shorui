<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class DocumentType extends Model
{
    use HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'name',
        'description',
    ];

    public function attributes(): BelongsToMany
    {
        return $this->belongsToMany(
            Attribute::class,
            'document_type_attributes',
            'type_id',
            'attribute_key',
            'id',
            'key'
        );
    }
}
