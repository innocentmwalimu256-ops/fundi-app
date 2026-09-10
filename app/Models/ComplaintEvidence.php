<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ComplaintEvidence extends Model
{
    protected $table = 'complaint_evidences';

    protected $fillable = [
        'complaint_id',
        'file_path',
        'file_type',
    ];

    public function complaint(): BelongsTo
    {
        return $this->belongsTo(Complaint::class, 'complaint_id');
    }
}
