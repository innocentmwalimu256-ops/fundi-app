<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Favorite;
use App\Models\Notification;
use App\Models\Review;
use App\Models\Service;
use App\Models\ServiceJob;
use App\Models\ServiceRequest;
use App\Models\TechnicianApplication;
use App\Models\TechnicianDocument;
use App\Models\TechnicianProfile;
use App\Models\User;
use App\Models\UserReport;
use App\Services\SmartMatchingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ClientController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        
        $popularServices = Service::where('status', 'active')
            ->orderBy('display_order')
            ->take(8)
            ->get();

        $recommendedTechnicians = User::where('role', 'technician')
            ->where('status', 'active')
            ->whereHas('technicianProfile', function ($query) {
                $query->where('verification_status', 'approved');
            })
            ->whereHas('subscription', function ($sq) {
                $sq->whereIn('status', ['active', 'free_trial'])->where('expires_at', '>', now());
            })
            ->with(['technicianProfile', 'services', 'reviewsReceived', 'serviceAreas', 'portfolios'])
            ->take(6)
            ->get();

        // Calculate smart matching percentage for each
        foreach ($recommendedTechnicians as $tech) {
            $tech->match_score = SmartMatchingService::calculateMatchScore($tech);
        }

        $recommendedTechnicians = $recommendedTechnicians->sortByDesc('match_score')->values();

        $activeRequests = ServiceRequest::where('client_id', $user->id)
            ->whereNotIn('status', ['completed', 'client_confirmed', 'reviewed', 'declined', 'cancelled'])
            ->with(['technician.technicianProfile', 'service', 'latestQuotation', 'job'])
            ->latest()
            ->take(3)
            ->get();

        $recentCompleted = ServiceRequest::where('client_id', $user->id)
            ->whereIn('status', ['completed', 'client_confirmed', 'reviewed'])
            ->with(['technician.technicianProfile', 'service', 'review'])
            ->latest()
            ->take(3)
            ->get();

        // Live Platform Trust Statistics (Real data from DB)
        $platformStats = [
            'verified_techs' => TechnicianProfile::where('verification_status', 'approved')->count(),
            'completed_jobs' => ServiceRequest::whereIn('status', ['completed', 'client_confirmed', 'reviewed'])->count(),
            'avg_rating' => round(Review::where('status', 'published')->avg('rating') ?: 4.9, 1),
            'services_count' => Service::where('status', 'active')->count(),
        ];

        return view('client.dashboard', compact('user', 'popularServices', 'recommendedTechnicians', 'activeRequests', 'recentCompleted', 'platformStats'));
    }

    public function services(Request $request)
    {
        $search = $request->input('q');
        
        $services = Service::where('status', 'active')
            ->when($search, function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            })
            ->withCount(['technicians' => function ($query) {
                $query->where('status', 'active')
                    ->whereHas('technicianProfile', function ($p) {
                        $p->where('verification_status', 'approved');
                    })
                    ->whereHas('subscription', function ($sq) {
                        $sq->whereIn('status', ['active', 'free_trial'])->where('expires_at', '>', now());
                    });
            }])
            ->orderBy('display_order')
            ->get();

        return view('client.services.index', compact('services', 'search'));
    }

    public function serviceShow($id)
    {
        $service = Service::where('status', 'active')->findOrFail($id);

        $technicians = User::where('role', 'technician')
            ->where('status', 'active')
            ->whereHas('services', function ($q) use ($service) {
                $q->where('services.id', $service->id);
            })
            ->whereHas('technicianProfile', function ($q) {
                $q->where('verification_status', 'approved');
            })
            ->whereHas('subscription', function ($sq) {
                $sq->whereIn('status', ['active', 'free_trial'])->where('expires_at', '>', now());
            })
            ->with(['technicianProfile', 'services', 'serviceAreas'])
            ->paginate(12);

        foreach ($technicians as $tech) {
            $tech->match_score = SmartMatchingService::calculateMatchScore($tech, $service->id);
        }

        return view('client.services.show', compact('service', 'technicians'));
    }

    public function technicians(Request $request)
    {
        $services = Service::where('status', 'active')->get();
        $selectedService = $request->input('service');
        $location = $request->input('location');
        $rating = $request->input('rating');
        $experience = $request->input('experience');
        $availability = $request->input('availability');
        $verifiedOnly = $request->boolean('verified_only', true);
        $search = $request->input('q');
        $sortBy = $request->input('sort', 'smart_match');

        $query = User::where('role', 'technician')
            ->where('status', 'active')
            ->whereHas('subscription', function ($sq) {
                $sq->whereIn('status', ['active', 'free_trial'])->where('expires_at', '>', now());
            })
            ->with(['technicianProfile', 'services', 'reviewsReceived', 'serviceAreas', 'portfolios', 'subscription.plan']);

        if ($verifiedOnly) {
            $query->whereHas('technicianProfile', function ($q) {
                $q->where('verification_status', 'approved');
            });
        }

        if ($selectedService) {
            $query->whereHas('services', function ($q) use ($selectedService) {
                $q->where('services.id', $selectedService);
            });
        }

        if ($location) {
            $query->where(function ($q) use ($location) {
                $q->whereHas('technicianProfile', function ($pq) use ($location) {
                    $pq->where('location', 'like', "%{$location}%")
                       ->orWhere('service_area', 'like', "%{$location}%");
                })->orWhereHas('serviceAreas', function ($sq) use ($location) {
                    $sq->where('area_name', 'like', "%{$location}%");
                });
            });
        }

        if ($availability) {
            $query->whereHas('technicianProfile', function ($q) use ($availability) {
                $q->where('availability_status', $availability);
            });
        }

        if ($experience) {
            $query->whereHas('technicianProfile', function ($q) use ($experience) {
                $q->where('years_experience', '>=', (int) $experience);
            });
        }

        if ($rating) {
            $query->whereHas('technicianProfile', function ($q) use ($rating) {
                $q->where('average_rating', '>=', (float) $rating);
            });
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhereHas('technicianProfile', function ($pq) use ($search) {
                      $pq->where('professional_title', 'like', "%{$search}%")
                         ->orWhere('bio', 'like', "%{$search}%");
                  })
                  ->orWhereHas('services', function ($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Sorting
        if ($sortBy === 'highest_rated') {
            $query->join('technician_profiles', 'users.id', '=', 'technician_profiles.user_id')
                ->orderByDesc('technician_profiles.average_rating')
                ->select('users.*');
        } elseif ($sortBy === 'most_experienced') {
            $query->join('technician_profiles', 'users.id', '=', 'technician_profiles.user_id')
                ->orderByDesc('technician_profiles.years_experience')
                ->select('users.*');
        } elseif ($sortBy === 'most_jobs') {
            $query->join('technician_profiles', 'users.id', '=', 'technician_profiles.user_id')
                ->orderByDesc('technician_profiles.completed_jobs_count')
                ->select('users.*');
        } else {
            $query->latest();
        }

        $technicians = $query->paginate(12)->withQueryString();

        // Attach smart match score and featured priority
        foreach ($technicians as $tech) {
            $tech->match_score = SmartMatchingService::calculateMatchScore($tech, $selectedService ? (int)$selectedService : null, $location);
        }

        if ($sortBy === 'smart_match') {
            $collection = $technicians->getCollection()->sortByDesc(function ($t) {
                return ($t->isFeaturedTechnician() ? 1000 : 0) + $t->match_score;
            })->values();
            $technicians->setCollection($collection);
        }

        $savedTechIds = Auth::check() 
            ? Favorite::where('client_id', Auth::id())->pluck('technician_id')->toArray() 
            : [];

        return view('client.technicians.index', compact(
            'technicians',
            'services',
            'selectedService',
            'location',
            'rating',
            'experience',
            'availability',
            'verifiedOnly',
            'search',
            'sortBy',
            'savedTechIds'
        ));
    }

    public function technicianProfile($id)
    {
        $technician = User::where('role', 'technician')
            ->with([
                'technicianProfile', 
                'services', 
                'reviewsReceived.client',
                'portfolios.service',
                'availabilities',
                'serviceAreas',
                'subscription.plan'
            ])
            ->findOrFail($id);

        if (!$technician->hasActiveSubscription()) {
            return redirect()->route('client.technicians.index')
                ->with('warning', 'Fundi huyu hapatikani kwa sasa kwa sababu subscription yake haiko hai.');
        }

        $isFavorite = Auth::check() && Favorite::where('client_id', Auth::id())->where('technician_id', $technician->id)->exists();

        $reviews = $technician->reviewsReceived()->where('status', 'published')->latest()->get();

        $ratingStats = [
            'quality' => round($reviews->avg('quality') ?: 5.0, 1),
            'professionalism' => round($reviews->avg('professionalism') ?: 5.0, 1),
            'communication' => round($reviews->avg('communication') ?: 5.0, 1),
            'punctuality' => round($reviews->avg('punctuality') ?: 5.0, 1),
        ];

        $matchScore = SmartMatchingService::calculateMatchScore($technician);

        return view('client.technicians.show', compact('technician', 'isFavorite', 'reviews', 'ratingStats', 'matchScore'));
    }

    public function reportUser(Request $request, $id)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'reason' => 'required|string|max:255',
            'details' => 'required|string|min:10|max:2000',
            'request_id' => 'nullable|exists:service_requests,id',
        ]);

        UserReport::create([
            'reporter_id' => $user->id,
            'reported_user_id' => $id,
            'request_id' => $validated['request_id'] ?? null,
            'reason' => $validated['reason'],
            'details' => $validated['details'],
            'status' => 'pending',
        ]);

        AuditLog::log('user_reported', "User #{$id} reported by {$user->full_name}", 'UserReport', $id);

        return back()->with('success', 'Your safety report has been submitted to platform administrators for investigation.');
    }

    public function becomeTechnician()
    {
        $user = Auth::user();
        $services = Service::where('status', 'active')->get();
        $application = TechnicianApplication::where('user_id', $user->id)->latest()->first();

        return view('client.technician-application', compact('user', 'services', 'application'));
    }

    public function storeTechnicianApplication(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'professional_title' => 'required|string|max:255',
            'service_id' => 'required|exists:services,id',
            'years_experience' => 'required|integer|min:0|max:50',
            'location' => 'required|string|max:255',
            'service_area' => 'nullable|string|max:255',
            'bio' => 'required|string|min:20|max:2000',
            'skills' => 'required|string',
            'id_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'certificate_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $skillsArray = array_values(array_filter(array_map('trim', explode(',', $validated['skills']))));

        $idPath = null;
        if ($request->hasFile('id_document')) {
            $idPath = $request->file('id_document')->store('technician_docs/ids', 'public');
        }

        $certPath = null;
        if ($request->hasFile('certificate_document')) {
            $certPath = $request->file('certificate_document')->store('technician_docs/certs', 'public');
        }

        $application = TechnicianApplication::create([
            'user_id' => $user->id,
            'professional_title' => $validated['professional_title'],
            'service_id' => $validated['service_id'],
            'years_experience' => $validated['years_experience'],
            'location' => $validated['location'],
            'service_area' => $validated['service_area'] ?? $validated['location'],
            'bio' => $validated['bio'],
            'skills' => $skillsArray,
            'id_document_path' => $idPath,
            'certificate_document_path' => $certPath,
            'status' => 'pending',
        ]);

        // Create preliminary profile
        TechnicianProfile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'professional_title' => $validated['professional_title'],
                'bio' => $validated['bio'],
                'years_experience' => $validated['years_experience'],
                'location' => $validated['location'],
                'service_area' => $validated['service_area'] ?? $validated['location'],
                'verification_status' => 'pending',
                'skills' => $skillsArray,
            ]
        );

        if ($idPath) {
            TechnicianDocument::create([
                'technician_id' => $user->id,
                'document_type' => 'National ID / Passport',
                'file_path' => $idPath,
                'original_name' => $request->file('id_document')->getClientOriginalName(),
                'verification_status' => 'pending',
            ]);
        }

        if ($certPath) {
            TechnicianDocument::create([
                'technician_id' => $user->id,
                'document_type' => 'Vocational / Academic Certificate',
                'file_path' => $certPath,
                'original_name' => $request->file('certificate_document')->getClientOriginalName(),
                'verification_status' => 'pending',
            ]);
        }

        $user->services()->syncWithoutDetaching([$validated['service_id']]);

        AuditLog::log('technician_application_submitted', "Technician application submitted by {$user->full_name}", 'TechnicianApplication', $application->id);

        Notification::send(
            $user->id,
            'application_submitted',
            'Application Under Review',
            'Your application to become a verified technician has been submitted. Our administrators are reviewing your credentials.'
        );

        return redirect()->route('client.technician-application.status')->with('success', 'Your application has been successfully submitted for administrator review.');
    }

    public function technicianApplicationStatus()
    {
        $user = Auth::user();
        $application = TechnicianApplication::where('user_id', $user->id)->with('service')->latest()->first();

        return view('client.technician-application-status', compact('user', 'application'));
    }

    public function favorites()
    {
        $user = Auth::user();
        $favorites = Favorite::where('client_id', $user->id)
            ->with(['technician.technicianProfile', 'technician.services', 'technician.serviceAreas'])
            ->latest()
            ->get();

        return view('client.favorites.index', compact('favorites'));
    }

    public function toggleFavorite(Request $request, $technicianId)
    {
        $client = Auth::user();
        $favorite = Favorite::where('client_id', $client->id)->where('technician_id', $technicianId)->first();

        if ($favorite) {
            $favorite->delete();
            $status = 'removed';
            $message = 'Removed from your saved technicians.';
        } else {
            Favorite::create([
                'client_id' => $client->id,
                'technician_id' => $technicianId,
            ]);
            $status = 'added';
            $message = 'Technician saved to your favorites!';
        }

        if ($request->wantsJson()) {
            return response()->json(['status' => $status, 'message' => $message]);
        }

        return back()->with('success', $message);
    }

    public function profile()
    {
        $user = Auth::user();
        return view('client.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:users,phone,' . $user->id,
            'avatar' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $avatarPath;
        }

        $user->full_name = $validated['full_name'];
        $user->phone = $validated['phone'];
        $user->save();

        return back()->with('success', 'Your profile details have been updated.');
    }
}
