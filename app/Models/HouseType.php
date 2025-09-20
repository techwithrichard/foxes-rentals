<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HouseType extends Model
{
    use HasUuids;

    protected $fillable = [
        'name',
        'description',
        'bedrooms',
        'bathrooms',
        'features',
    ];

    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];
}
