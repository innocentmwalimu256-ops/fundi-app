<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ServiceRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference_no',
        'client_id',
        'technician_id',
        'service_id',
        'description',
        'location',
        'preferred_date',
        'preferred_time',
        'urgency',
        'status',
        'contact_unlocked',
        'payment_status',
        'service_cost_agreed',
        'connection_fee',
        'connection_fee_status',
        'connection_fee_reference',
        'cancellation_reason',
        'cancelled_by',
    ];

    protected function casts(): array
    {
        return [
            'preferred_date' => 'date',
        ];
    }

    public static function generateReferenceNo(): string
    {
        $year = date('Y');
        $count = static::whereYear('created_at', $year)->count() + 1;
        return sprintf('REQ-%s-%06d', $year, $count);
    }

    // Relationships
    public function cancellationRecord(): HasOne
    {
        return $this->hasOne(CancellationRecord::class, 'request_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function technician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'technician_id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(RequestImage::class, 'request_id');
    }

    public function quotations(): HasMany
    {
        return $this->hasMany(Quotation::class, 'request_id');
    }

    public function latestQuotation(): HasOne
    {
        return $this->hasOne(Quotation::class, 'request_id')->latestOfMany();
    }

    public function acceptedQuotation(): HasOne
    {
        return $this->hasOne(Quotation::class, 'request_id')->where('status', 'accepted');
    }

    public function job(): HasOne
    {
        return $this->hasOne(ServiceJob::class, 'request_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'request_id')->oldest('sent_at');
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class, 'request_id');
    }

    public function complaint(): HasOne
    {
        return $this->hasOne(Complaint::class, 'request_id');
    }

    // Status Badges & Labels
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'requested', 'pending' => 'Pending Technician',
            'accepted' => 'Technician Accepted',
            'declined' => 'Technician Declined',
            'cancelled' => 'Cancelled',
            'quotation_pending' => 'Quotation Sent',
            'quotation_accepted' => 'Quotation Accepted',
            'scheduled' => 'Job Scheduled',
            'on_the_way' => 'Technician On The Way',
            'in_progress' => 'Job In Progress',
            'completed' => 'Work Completed (Pending Client Confirmation)',
            'client_confirmed' => 'Completed & Confirmed',
            'reviewed' => 'Completed & Reviewed',
            default => ucfirst(str_replace('_', ' ', $this->status)),
        };
    }

    public function getStatusBadgeClassesAttribute(): string
    {
        return match ($this->status) {
            'requested', 'pending' => 'bg-amber-100 text-amber-800 border-amber-200',
            'accepted', 'quotation_pending' => 'bg-blue-100 text-blue-800 border-blue-200',
            'quotation_accepted', 'scheduled' => 'bg-indigo-100 text-indigo-800 border-indigo-200',
            'on_the_way', 'in_progress' => 'bg-teal-100 text-teal-800 border-teal-200',
            'completed' => 'bg-orange-100 text-orange-800 border-orange-200',
            'client_confirmed', 'reviewed' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            'declined', 'cancelled' => 'bg-rose-100 text-rose-800 border-rose-200',
            default => 'bg-slate-100 text-slate-800 border-slate-200',
        };
    }

    public function getUrgencyBadgeClassesAttribute(): string
    {
        return match ($this->urgency) {
            'urgent' => 'bg-red-50 text-red-700 border-red-200',
            'high' => 'bg-amber-50 text-amber-700 border-amber-200',
            'normal' => 'bg-blue-50 text-blue-700 border-blue-200',
            'low' => 'bg-slate-50 text-slate-700 border-slate-200',
            default => 'bg-slate-50 text-slate-700 border-slate-200',
        };
    }

    // Lifecycle helper
    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['requested', 'pending', 'accepted', 'quotation_pending', 'quotation_accepted', 'scheduled']);
    }

    public function canAcceptQuotation(): bool
    {
        return in_array($this->status, ['accepted', 'quotation_pending']) && $this->latestQuotation && $this->latestQuotation->status === 'pending';
    }

    public function canConfirmCompletion(): bool
    {
        return $this->status === 'completed';
    }

    public function canLeaveReview(): bool
    {
        return in_array($this->status, ['client_confirmed']) && !$this->review;
    }
}
