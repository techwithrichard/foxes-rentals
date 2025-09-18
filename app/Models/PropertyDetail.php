<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyDetail extends Model
{
    use HasUuids, HasFactory;

    protected $fillable = [
        'property_id',
        'detail_type',
        'detail_data',
    ];

    protected $casts = [
        'detail_data' => 'array',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(PropertyConsolidated::class);
    }

    // Helper methods for different property types
    public function getRentalData(): ?array
    {
        return $this->detail_type === 'rental' ? $this->detail_data : null;
    }

    public function getSaleData(): ?array
    {
        return $this->detail_type === 'sale' ? $this->detail_data : null;
    }

    public function getLeaseData(): ?array
    {
        return $this->detail_type === 'lease' ? $this->detail_data : null;
    }

    public function setRentalData(array $data): void
    {
        $this->update([
            'detail_type' => 'rental',
            'detail_data' => $data,
        ]);
    }

    public function setSaleData(array $data): void
    {
        $this->update([
            'detail_type' => 'sale',
            'detail_data' => $data,
        ]);
    }

    public function setLeaseData(array $data): void
    {
        $this->update([
            'detail_type' => 'lease',
            'detail_data' => $data,
        ]);
    }
}
