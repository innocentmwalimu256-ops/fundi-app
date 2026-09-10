<?php

namespace App\Services;

use App\Models\User;
use App\Models\Service;

class SmartMatchingService
{
    /**
     * Calculate match score percentage (0-100%) between a technician and client search criteria.
     */
    public static function calculateMatchScore(User $technician, ?int $serviceId = null, ?string $clientLocation = null): int
    {
        $score = 0;
        $profile = $technician->technicianProfile;

        // 1. Service Category Match (Weight: 40%)
        if ($serviceId && $technician->services->contains('id', $serviceId)) {
            $score += 40;
        } elseif (!$serviceId && $technician->services->isNotEmpty()) {
            $score += 30;
        }

        // 2. Location & Service Coverage Area Match (Weight: 25%)
        if ($clientLocation) {
            $clientLocLower = strtolower($clientLocation);
            $techLocLower = strtolower($profile->location ?? '');
            $techAreas = $technician->serviceAreas->pluck('area_name')->map(fn($a) => strtolower($a))->toArray();

            $locationMatched = false;
            if (str_contains($techLocLower, $clientLocLower) || str_contains($clientLocLower, $techLocLower)) {
                $locationMatched = true;
            } else {
                foreach ($techAreas as $area) {
                    if (str_contains($clientLocLower, $area) || str_contains($area, $clientLocLower)) {
                        $locationMatched = true;
                        break;
                    }
                }
            }

            if ($locationMatched) {
                $score += 25;
            } else {
                $score += 10; // Same broader region default
            }
        } else {
            $score += 20;
        }

        // 3. Availability Match (Weight: 15%)
        if (($profile->availability_status ?? '') === 'available') {
            $score += 15;
        } elseif (($profile->availability_status ?? '') === 'busy') {
            $score += 8;
        }

        // 4. Rating Match (Weight: 10%)
        $rating = $profile->average_rating ?? 4.5;
        $score += (int) round(($rating / 5.0) * 10);

        // 5. Experience & Completion Performance (Weight: 10%)
        $completedJobs = $profile->completed_jobs_count ?? 0;
        $experience = $profile->years_experience ?? 1;

        if ($completedJobs >= 50 || $experience >= 4) {
            $score += 10;
        } elseif ($completedJobs >= 10 || $experience >= 2) {
            $score += 7;
        } else {
            $score += 5;
        }

        // Return bounded between 65% and 98% for realistic marketplace dynamics
        return min(98, max(65, $score));
    }
}
