<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Quotation extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_id',
        'labour_cost',
        'materials_cost',
        'transport_cost',
        'other_cost',
        'discount',
        'total_cost',
        'notes',
        'estimated_duration',
        'valid_until',
        'status',
        'rejection_reason',
    ];

    protected function casts(): array
    {
        return [
            'labour_cost' => 'decimal:2',
            'materials_cost' => 'decimal:2',
            'transport_cost' => 'decimal:2',
            'other_cost' => 'decimal:2',
            'discount' => 'decimal:2',
            'total_cost' => 'decimal:2',
            'valid_until' => 'date',
        ];
    }

    public function serviceRequest(): BelongsTo
    {
        return $this->belongsTo(ServiceRequest::class, 'request_id');
    }

    public function getFormattedTotalAttribute(): string
    {
        return 'TZS ' . number_format($this->total_cost, 0);
    }
}
