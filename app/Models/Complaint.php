<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Complaint extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'request_id',
        'category',
        'description',
        'status',
        'resolution',
        'resolved_by',
        'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'resolved_at' => 'datetime',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function serviceRequest(): BelongsTo
    {
        return $this->belongsTo(ServiceRequest::class, 'request_id');
    }

    public function evidences(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ComplaintEvidence::class, 'complaint_id');
    }

    public function resolver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            'technician_no_show' => 'Technician Did Not Arrive',
            'poor_service' => 'Poor Service Quality',
            'misconduct' => 'Professional Misconduct',
            'payment_issue' => 'Payment Disagreement',
            'unfinished' => 'Job Left Unfinished',
            default => ucfirst(str_replace('_', ' ', $this->category)),
        };
    }
}
