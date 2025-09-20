<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketAttachment extends Model
{
    use HasUuids;

    protected $fillable = [
        'ticket_id',
        'reply_id',
        'file_name',
        'file_path',
        'file_size',
        'mime_type',
    ];

    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];
}
