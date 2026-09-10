<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TechnicianProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'professional_title',
        'bio',
        'years_experience',
        'location',
        'service_area',
        'availability_status',
        'verification_status',
        'rejection_reason',
        'average_rating',
        'total_reviews',
        'completed_jobs_count',
        'completion_rate',
        'response_rate',
        'avg_response_time',
        'skills',
        'working_hours',
    ];

    protected function casts(): array
    {
        return [
            'skills' => 'array',
            'working_hours' => 'array',
            'average_rating' => 'decimal:2',
            'years_experience' => 'integer',
            'total_reviews' => 'integer',
            'completed_jobs_count' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function recalculateRating(): void
    {
        $reviews = Review::where('technician_id', $this->user_id)
            ->where('status', 'published')
            ->get();

        $count = $reviews->count();
        $completedCount = ServiceRequest::where('technician_id', $this->user_id)
            ->whereIn('status', ['completed', 'client_confirmed', 'reviewed'])
            ->count();

        // 1. Determine Subscription Base Rating (B)
        $user = $this->user;
        $sub = $user ? $user->subscription : null;
        $baseRating = 0.00;

        if ($sub && $sub->isActive() && $sub->plan) {
            $planSlug = strtolower($sub->plan->slug);
            $baseRating = match ($planSlug) {
                'premium' => 3.00,          // 3.0 Stars for Premium (TZS 35,000)
                'professional' => 2.50,     // 2.5 Stars for Professional (TZS 20,000)
                'basic', 'starter' => 2.00, // 2.0 Stars for Starter/Basic (TZS 10,000)
                default => 2.00,
            };
        }

        if ($count === 0) {
            // If fundi has no client reviews yet, rating equals the base subscription stars
            $finalRating = $baseRating;
        } else {
            // Confidence / Damping quota factor K: requires a high volume of positive verified client reviews to climb to 4.0, 4.5, and 5.0
            $k = 6;
            $sumClientRatings = $reviews->sum('rating');
            $rawScore = (($baseRating * $k) + $sumClientRatings) / ($k + $count);
            $finalRating = min(5.00, max(1.00, $rawScore));
        }

        $this->update([
            'average_rating' => round($finalRating, 2),
            'total_reviews' => $count,
            'completed_jobs_count' => $completedCount,
        ]);
    }
}
