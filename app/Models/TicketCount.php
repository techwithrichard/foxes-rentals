<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketCount extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_id',
        'open_tickets',
        'closed_tickets',
        'total_tickets',
    ];

    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];
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
