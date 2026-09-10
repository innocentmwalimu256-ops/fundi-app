<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceJob extends Model
{
    use HasFactory;

    protected $table = 'service_jobs';

    protected $fillable = [
        'request_id',
        'scheduled_date',
        'scheduled_time',
        'status',
        'started_at',
        'completed_at',
        'confirmed_at',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_date' => 'date',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'confirmed_at' => 'datetime',
        ];
    }

    public function serviceRequest(): BelongsTo
    {
        return $this->belongsTo(ServiceRequest::class, 'request_id');
    }
}
