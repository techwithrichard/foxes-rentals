<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertiesForSale extends Model
{
    use HasFactory;

    // Specify the table if the model name doesn't match the table name
    protected $table = 'properties_for_sale';

    // Allow mass assignment for the following attributes
    protected $fillable = [
        'name', 'location', 'price', 'bedrooms', 'bathrooms', 'garage', 
        'balcony', 'square_footage', 'description', 'images', 'features'
    ];

    // Cast attributes to their appropriate data types
    protected $casts = [
        'images' => 'array',  // Cast the images as an array
        'features' => 'array' // Cast the features as an array
    ];
}
