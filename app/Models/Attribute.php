<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'key',
        'label',
        'description',
    ];
}
