<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Expense extends Model
{
    use HasUuids, LogsActivity;

    protected $fillable = [
        'property_id',
        'house_id',
        'expense_type_id',
        'amount',
        'description',
        'date',
        'status',
        'receipt_path',
        'notes',
    ];

    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];

    protected $casts = [
        'amount' => 'decimal:2',
        'incurred_on' => 'date',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['amount', 'incurred_on', 'description','category.name','property.name','house.name','landlord.name'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }


    public function category(): BelongsTo
    {
        return $this->belongsTo(ExpenseType::class, 'expense_type_id')
            ->withDefault(['name' => __('Uncategorized')]);
    }

    public function landlord(): BelongsTo
    {
        return $this->belongsTo(User::class, 'landlord_id','id');
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function house(): BelongsTo
    {
        return $this->belongsTo(House::class);
    }


}
