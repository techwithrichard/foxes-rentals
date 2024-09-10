<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IdentityDocument extends Model
{
    protected $fillable = [
        'name',
        'path',
        'tenant_id',
    ];



}
