<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TechnicianDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'technician_id',
        'document_type',
        'file_path',
        'original_name',
        'verification_status',
        'uploaded_at',
    ];

    public function technician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'technician_id');
    }
}
