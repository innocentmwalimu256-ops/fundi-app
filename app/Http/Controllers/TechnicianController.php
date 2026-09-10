<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Notification;
use App\Models\Quotation;
use App\Models\Review;
use App\Models\Service;
use App\Models\ServiceJob;
use App\Models\ServiceRequest;
use App\Models\TechnicianAvailability;
use App\Models\TechnicianPortfolio;
use App\Models\TechnicianProfile;
use App\Models\TechnicianServiceArea;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TechnicianController extends Controller
{
    public function dashboard()
    {
        $technician = Auth::user();
        $profile = $technician->technicianProfile;

        $newRequestsCount = ServiceRequest::where('technician_id', $technician->id)
            ->whereIn('status', ['requested', 'pending'])
            ->count();

        $activeJobsCount = ServiceRequest::where('technician_id', $technician->id)
            ->whereIn('status', ['quotation_accepted', 'scheduled', 'on_the_way', 'in_progress'])
            ->count();

        $completedJobsCount = ServiceRequest::where('technician_id', $technician->id)
            ->whereIn('status', ['completed', 'client_confirmed', 'reviewed'])
            ->count();

        $pendingQuotationsCount = ServiceRequest::where('technician_id', $technician->id)
            ->where('status', 'quotation_pending')
            ->count();

        $recentRequests = ServiceRequest::where('technician_id', $technician->id)
            ->with(['client', 'service', 'latestQuotation'])
            ->latest()
            ->take(5)
            ->get();

        $upcomingJobs = ServiceRequest::where('technician_id', $technician->id)
            ->whereIn('status', ['scheduled', 'on_the_way', 'in_progress'])
            ->with(['client', 'service', 'job'])
            ->latest()
            ->take(5)
            ->get();

        $recentReviews = Review::where('technician_id', $technician->id)
            ->where('status', 'published')
            ->with('client')
            ->latest()
            ->take(4)
            ->get();

        $subscription = $technician->subscription;
        $activeSubscription = $subscription;

        return view('technician.dashboard', compact(
            'technician',
            'profile',
            'subscription',
            'activeSubscription',
            'newRequestsCount',
            'activeJobsCount',
            'completedJobsCount',
            'pendingQuotationsCount',
            'recentRequests',
            'upcomingJobs',
            'recentReviews'
        ));
    }

    public function requests(Request $request)
    {
        $technician = Auth::user();
        $tab = $request->input('tab', 'new');

        $query = ServiceRequest::where('technician_id', $technician->id)
            ->with(['client', 'service', 'latestQuotation', 'job']);

        if ($tab === 'new') {
            $query->whereIn('status', ['requested', 'pending']);
        } elseif ($tab === 'accepted') {
            $query->whereIn('status', ['accepted', 'quotation_pending']);
        } elseif ($tab === 'jobs') {
            $query->whereIn('status', ['quotation_accepted', 'scheduled', 'on_the_way', 'in_progress', 'completed']);
        } elseif ($tab === 'history') {
            $query->whereIn('status', ['client_confirmed', 'reviewed', 'declined', 'cancelled']);
        }

        $requests = $query->latest()->paginate(10)->withQueryString();

        $counts = [
            'new' => ServiceRequest::where('technician_id', $technician->id)->whereIn('status', ['requested', 'pending'])->count(),
            'accepted' => ServiceRequest::where('technician_id', $technician->id)->whereIn('status', ['accepted', 'quotation_pending'])->count(),
            'jobs' => ServiceRequest::where('technician_id', $technician->id)->whereIn('status', ['quotation_accepted', 'scheduled', 'on_the_way', 'in_progress', 'completed'])->count(),
            'history' => ServiceRequest::where('technician_id', $technician->id)->whereIn('status', ['client_confirmed', 'reviewed', 'declined', 'cancelled'])->count(),
        ];

        return view('technician.requests.index', compact('requests', 'tab', 'counts'));
    }

    public function requestShow($id)
    {
        $technician = Auth::user();
        $request = ServiceRequest::where('technician_id', $technician->id)
            ->with([
                'client',
                'service',
                'images',
                'quotations',
                'latestQuotation',
                'job',
                'messages.sender',
                'review',
                'complaint',
                'cancellationRecord'
            ])
            ->findOrFail($id);

        $canViewContact = \App\Services\SubscriptionService::canViewProtectedContact($technician, $request);

        return view('technician.requests.show', compact('request', 'canViewContact'));
    }

    public function acceptRequest(Request $request, $id)
    {
        $technician = Auth::user();
        $serviceRequest = ServiceRequest::where('technician_id', $technician->id)->findOrFail($id);

        if (!in_array($serviceRequest->status, ['requested', 'pending'])) {
            return back()->with('error', 'This request is not pending.');
        }

        $serviceRequest->update(['status' => 'accepted']);

        AuditLog::log('accept_request', "Technician {$technician->full_name} accepted request {$serviceRequest->reference_no}", 'ServiceRequest', $serviceRequest->id);

        Notification::send(
            $serviceRequest->client_id,
            'request_accepted',
            'Service Request Accepted!',
            "Technician {$technician->full_name} accepted your request {$serviceRequest->reference_no} and is preparing a quotation.",
            route('client.requests.show', $serviceRequest->id)
        );

        return back()->with('success', 'Request accepted! You can now send an itemized quotation to the client.');
    }

    public function declineRequest(Request $request, $id)
    {
        $technician = Auth::user();
        $serviceRequest = ServiceRequest::where('technician_id', $technician->id)->findOrFail($id);

        $request->validate([
            'cancellation_reason' => 'required|string|max:500',
        ]);

        $serviceRequest->update([
            'status' => 'declined',
            'cancellation_reason' => $request->input('cancellation_reason'),
            'cancelled_by' => $technician->id,
        ]);

        AuditLog::log('decline_request', "Technician {$technician->full_name} declined request {$serviceRequest->reference_no}", 'ServiceRequest', $serviceRequest->id);

        Notification::send(
            $serviceRequest->client_id,
            'request_declined',
            'Service Request Declined',
            "Technician {$technician->full_name} was unable to accept your request. Reason: {$request->input('cancellation_reason')}"
        );

        return back()->with('info', 'Service request declined.');
    }

    public function storeQuotation(Request $request, $id)
    {
        $technician = Auth::user();
        $serviceRequest = ServiceRequest::where('technician_id', $technician->id)->findOrFail($id);

        $validated = $request->validate([
            'labour_cost' => 'required|numeric|min:0',
            'materials_cost' => 'nullable|numeric|min:0',
            'transport_cost' => 'nullable|numeric|min:0',
            'other_cost' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:2000',
            'estimated_duration' => 'nullable|string|max:100',
            'valid_until' => 'nullable|date|after_or_equal:today',
        ]);

        $labour = (float) $validated['labour_cost'];
        $materials = (float) ($validated['materials_cost'] ?? 0);
        $transport = (float) ($validated['transport_cost'] ?? 0);
        $other = (float) ($validated['other_cost'] ?? 0);
        $discount = (float) ($validated['discount'] ?? 0);

        $total = ($labour + $materials + $transport + $other) - $discount;
        if ($total < 0) {
            $total = 0;
        }

        $quotation = Quotation::create([
            'request_id' => $serviceRequest->id,
            'labour_cost' => $labour,
            'materials_cost' => $materials,
            'transport_cost' => $transport,
            'other_cost' => $other,
            'discount' => $discount,
            'total_cost' => $total,
            'notes' => $validated['notes'],
            'estimated_duration' => $validated['estimated_duration'] ?? '1 Day',
            'valid_until' => $validated['valid_until'] ?? now()->addDays(7),
            'status' => 'pending',
        ]);

        $serviceRequest->update(['status' => 'quotation_pending']);

        AuditLog::log('create_quotation', "Technician {$technician->full_name} sent quotation of TZS {$total} for request {$serviceRequest->reference_no}", 'Quotation', $quotation->id);

        Notification::send(
            $serviceRequest->client_id,
            'quotation_received',
            'Quotation Received!',
            "Technician {$technician->full_name} prepared a quotation of {$quotation->formatted_total} for your request {$serviceRequest->reference_no}.",
            route('client.requests.show', $serviceRequest->id)
        );

        return back()->with('success', 'Quotation sent to client successfully!');
    }

    public function jobs(Request $request)
    {
        $technician = Auth::user();
        $status = $request->input('status', 'all');

        $query = ServiceRequest::where('technician_id', $technician->id)
            ->whereIn('status', ['quotation_accepted', 'scheduled', 'on_the_way', 'in_progress', 'completed', 'client_confirmed', 'reviewed'])
            ->with(['client', 'service', 'job', 'latestQuotation']);

        if ($status === 'active') {
            $query->whereIn('status', ['quotation_accepted', 'scheduled', 'on_the_way', 'in_progress']);
        } elseif ($status === 'completed') {
            $query->whereIn('status', ['completed', 'client_confirmed', 'reviewed']);
        }

        $jobs = $query->latest()->paginate(10)->withQueryString();

        return view('technician.jobs.index', compact('jobs', 'status'));
    }

    public function updateJobStatus(Request $request, $id)
    {
        $technician = Auth::user();
        $serviceRequest = ServiceRequest::where('technician_id', $technician->id)->findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:on_the_way,in_progress,completed',
        ]);

        $newStatus = $validated['status'];

        $serviceRequest->update(['status' => $newStatus]);

        if ($serviceRequest->job) {
            $jobData = ['status' => $newStatus];
            if ($newStatus === 'in_progress' && !$serviceRequest->job->started_at) {
                $jobData['started_at'] = now();
            } elseif ($newStatus === 'completed') {
                $jobData['completed_at'] = now();
            }
            $serviceRequest->job->update($jobData);
        }

        AuditLog::log('update_job_status', "Technician updated job {$serviceRequest->reference_no} status to {$newStatus}", 'ServiceRequest', $serviceRequest->id);

        $notificationMessages = [
            'on_the_way' => "Technician {$technician->full_name} is on the way to your location for {$serviceRequest->reference_no}!",
            'in_progress' => "Technician {$technician->full_name} has started work on your job ({$serviceRequest->reference_no}).",
            'completed' => "Technician {$technician->full_name} has completed the work! Please inspect and confirm completion.",
        ];

        Notification::send(
            $serviceRequest->client_id,
            'job_' . $newStatus,
            'Job Status Update: ' . ucfirst(str_replace('_', ' ', $newStatus)),
            $notificationMessages[$newStatus],
            route('client.requests.show', $serviceRequest->id)
        );

        return back()->with('success', 'Job status updated to ' . ucfirst(str_replace('_', ' ', $newStatus)));
    }

    public function updatePaymentStatus(Request $request, $id)
    {
        $technician = Auth::user();
        $serviceRequest = ServiceRequest::where('technician_id', $technician->id)->findOrFail($id);

        $validated = $request->validate([
            'payment_status' => 'required|in:unpaid,paid',
        ]);

        $serviceRequest->update(['payment_status' => $validated['payment_status']]);

        AuditLog::log('payment_status_update', "Payment status updated to {$validated['payment_status']} for {$serviceRequest->reference_no}", 'ServiceRequest', $serviceRequest->id);

        return back()->with('success', 'Payment status updated to ' . ucfirst($validated['payment_status']));
    }

    public function availability()
    {
        $technician = Auth::user();
        $profile = $technician->technicianProfile;
        $availabilities = $technician->availabilities;

        $daysOfWeek = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

        return view('technician.availability', compact('technician', 'profile', 'availabilities', 'daysOfWeek'));
    }

    public function updateAvailability(Request $request)
    {
        $technician = Auth::user();
        $profile = $technician->technicianProfile;

        $validated = $request->validate([
            'availability_status' => 'required|in:available,busy,offline',
            'days' => 'nullable|array',
            'start_time' => 'nullable|array',
            'end_time' => 'nullable|array',
        ]);

        $profile->update([
            'availability_status' => $validated['availability_status'],
        ]);

        // Update day schedules
        $daysOfWeek = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $selectedDays = $validated['days'] ?? [];

        foreach ($daysOfWeek as $day) {
            $isAvail = in_array($day, $selectedDays);
            $start = $validated['start_time'][$day] ?? '08:00:00';
            $end = $validated['end_time'][$day] ?? '18:00:00';

            TechnicianAvailability::updateOrCreate(
                ['technician_id' => $technician->id, 'day_of_week' => $day],
                ['start_time' => $start, 'end_time' => $end, 'is_available' => $isAvail]
            );
        }

        return back()->with('success', 'Weekly availability schedule updated successfully.');
    }

    public function portfolios()
    {
        $technician = Auth::user();
        $portfolios = $technician->portfolios()->with('service')->latest()->get();
        $services = $technician->services->isNotEmpty() ? $technician->services : \App\Models\Service::where('is_active', true)->get();

        return view('technician.portfolios.index', compact('technician', 'portfolios', 'services'));
    }

    public function storePortfolio(Request $request)
    {
        $technician = Auth::user();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'service_id' => 'nullable|exists:services,id',
            'description' => 'required|string|min:10|max:2000',
            'project_date' => 'nullable|string|max:100',
            'image' => 'required|image|max:5120',
        ]);

        $imagePath = $request->file('image')->store('portfolios', 'public');

        TechnicianPortfolio::create([
            'technician_id' => $technician->id,
            'service_id' => $validated['service_id'] ?? null,
            'title' => $validated['title'],
            'description' => $validated['description'],
            'project_date' => $validated['project_date'] ?? date('F Y'),
            'image_path' => $imagePath,
        ]);

        AuditLog::log('portfolio_added', "Technician {$technician->full_name} added portfolio item '{$validated['title']}'", 'TechnicianPortfolio', $technician->id);

        return back()->with('success', 'New portfolio project added to your public profile!');
    }

    public function deletePortfolio($id)
    {
        $technician = Auth::user();
        $portfolio = TechnicianPortfolio::where('technician_id', $technician->id)->findOrFail($id);

        if ($portfolio->image_path) {
            Storage::disk('public')->delete($portfolio->image_path);
        }

        $portfolio->delete();

        return back()->with('success', 'Portfolio project removed.');
    }

    public function reviews()
    {
        $technician = Auth::user();
        $reviews = Review::where('technician_id', $technician->id)
            ->with(['client', 'serviceRequest'])
            ->latest()
            ->paginate(15);

        $profile = $technician->technicianProfile;

        return view('technician.reviews.index', compact('technician', 'profile', 'reviews'));
    }

    public function profile()
    {
        $technician = Auth::user();
        $profile = $technician->technicianProfile;
        $services = Service::where('status', 'active')->get();
        $selectedServices = $technician->services->pluck('id')->toArray();
        $selectedAreas = $technician->serviceAreas->pluck('area_name')->toArray();

        $darDistricts = ['Kinondoni', 'Ilala', 'Temeke', 'Ubungo', 'Kigamboni'];

        return view('technician.profile', compact('technician', 'profile', 'services', 'selectedServices', 'selectedAreas', 'darDistricts'));
    }

    public function updateProfile(Request $request)
    {
        $technician = Auth::user();
        $profile = $technician->technicianProfile;

        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:users,phone,' . $technician->id,
            'professional_title' => 'required|string|max:255',
            'years_experience' => 'required|integer|min:0|max:50',
            'location' => 'required|string|max:255',
            'bio' => 'required|string|min:20|max:2000',
            'skills' => 'required|string',
            'services' => 'required|array',
            'services.*' => 'exists:services,id',
            'service_areas' => 'nullable|array',
            'avatar' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $technician->avatar = $avatarPath;
        }

        $technician->full_name = $validated['full_name'];
        $technician->phone = $validated['phone'];
        $technician->save();

        $skillsArray = array_values(array_filter(array_map('trim', explode(',', $validated['skills']))));

        $profile->update([
            'professional_title' => $validated['professional_title'],
            'years_experience' => $validated['years_experience'],
            'location' => $validated['location'],
            'bio' => $validated['bio'],
            'skills' => $skillsArray,
        ]);

        $technician->services()->sync($validated['services']);

        // Sync service coverage areas
        TechnicianServiceArea::where('technician_id', $technician->id)->delete();
        if (!empty($validated['service_areas'])) {
            foreach ($validated['service_areas'] as $area) {
                TechnicianServiceArea::create([
                    'technician_id' => $technician->id,
                    'area_name' => $area,
                ]);
            }
        }

        return back()->with('success', 'Technician profile and coverage areas updated successfully.');
    }
}
