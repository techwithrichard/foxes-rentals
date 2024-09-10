<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketCount extends Model
{
    use HasUuids;

    protected $guarded = [];
    protected $casts = [
        'day' => 'date',
    ];

    //disable timestamps
    public $timestamps = false;

    //increment count
    public function incrementCount()
    {
        $this->count++;
        $this->save();
    }


}
