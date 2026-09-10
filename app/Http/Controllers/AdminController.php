<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Complaint;
use App\Models\Notification;
use App\Models\Review;
use App\Models\Service;
use App\Models\ServiceJob;
use App\Models\ServiceRequest;
use App\Models\TechnicianApplication;
use App\Models\TechnicianProfile;
use App\Models\User;
use App\Models\UserReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function dashboard()
    {
        $kpis = [
            'total_users' => User::count(),
            'clients' => User::where('role', 'client')->count(),
            'technicians' => User::where('role', 'technician')->count(),
            'pending_applications' => TechnicianApplication::where('status', 'pending')->count(),
            'active_jobs' => ServiceRequest::whereIn('status', ['quotation_accepted', 'scheduled', 'on_the_way', 'in_progress'])->count(),
            'completed_jobs' => ServiceRequest::whereIn('status', ['completed', 'client_confirmed', 'reviewed'])->count(),
            'open_complaints' => Complaint::where('status', 'open')->count(),
            'average_rating' => round(Review::where('status', 'published')->avg('rating') ?: 5.0, 1),
        ];

        $pendingApplications = TechnicianApplication::where('status', 'pending')
            ->with(['user', 'service'])
            ->latest()
            ->take(5)
            ->get();

        $recentRequests = ServiceRequest::with(['client', 'technician', 'service'])
            ->latest()
            ->take(6)
            ->get();

        $recentComplaints = Complaint::with(['client', 'serviceRequest', 'evidences'])
            ->whereIn('status', ['open', 'under_review'])
            ->latest()
            ->take(5)
            ->get();

        $recentLogs = AuditLog::with('user')
            ->latest()
            ->take(8)
            ->get();

        $popularServices = Service::withCount('requests')
            ->orderByDesc('requests_count')
            ->take(5)
            ->get();

        // Job Performance Breakdown
        $totalJobs = max(1, ServiceRequest::count());
        $jobDistribution = [
            'completed' => round((ServiceRequest::whereIn('status', ['completed', 'client_confirmed', 'reviewed'])->count() / $totalJobs) * 100),
            'in_progress' => round((ServiceRequest::whereIn('status', ['scheduled', 'on_the_way', 'in_progress'])->count() / $totalJobs) * 100),
            'cancelled' => round((ServiceRequest::where('status', 'cancelled')->count() / $totalJobs) * 100),
            'disputed' => round((Complaint::count() / $totalJobs) * 100),
        ];

        $topTechnicians = User::where('role', 'technician')
            ->whereHas('technicianProfile', fn($q) => $q->where('verification_status', 'approved'))
            ->with('technicianProfile')
            ->take(5)
            ->get()
            ->sortByDesc(fn($u) => $u->technicianProfile->average_rating ?? 5.0);

        return view('admin.dashboard', compact(
            'kpis',
            'pendingApplications',
            'recentRequests',
            'recentComplaints',
            'recentLogs',
            'popularServices',
            'jobDistribution',
            'topTechnicians'
        ));
    }

    public function users(Request $request)
    {
        $role = $request->input('role');
        $status = $request->input('status');
        $search = $request->input('q');

        $query = User::with(['technicianProfile', 'services']);

        if ($role) {
            $query->where('role', $role);
        }
        if ($status) {
            $query->where('status', $status);
        }
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(15)->withQueryString();

        return view('admin.users.index', compact('users', 'role', 'status', 'search'));
    }

    public function toggleUserStatus(Request $request, $id)
    {
        $user = User::findOrFail($id);
        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot suspend your own admin account.');
        }

        $newStatus = $user->status === 'active' ? 'suspended' : 'active';
        $user->update(['status' => $newStatus]);

        AuditLog::log(
            $newStatus === 'suspended' ? 'suspend_user' : 'activate_user',
            "Admin {$newStatus} user {$user->full_name} (#{$user->id})",
            'User',
            $user->id
        );

        return back()->with('success', "User {$user->full_name} status updated to {$newStatus}.");
    }

    public function technicians(Request $request)
    {
        $status = $request->input('verification_status');
        $search = $request->input('q');

        $query = User::where('role', 'technician')->with(['technicianProfile', 'services', 'serviceAreas']);

        if ($status) {
            $query->whereHas('technicianProfile', function ($q) use ($status) {
                $q->where('verification_status', $status);
            });
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $technicians = $query->latest()->paginate(15)->withQueryString();

        return view('admin.technicians.index', compact('technicians', 'status', 'search'));
    }

    public function applications(Request $request)
    {
        $status = $request->input('status', 'pending');

        $applications = TechnicianApplication::when($status !== 'all', function ($q) use ($status) {
                $q->where('status', $status);
            })
            ->with(['user', 'service', 'reviewer'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $pendingCount = TechnicianApplication::where('status', 'pending')->count();

        return view('admin.applications.index', compact('applications', 'status', 'pendingCount'));
    }

    public function applicationShow($id)
    {
        $application = TechnicianApplication::with(['user', 'service', 'reviewer'])->findOrFail($id);
        $userDocuments = $application->user->documents;

        return view('admin.applications.show', compact('application', 'userDocuments'));
    }

    public function approveApplication(Request $request, $id)
    {
        $admin = Auth::user();
        $application = TechnicianApplication::findOrFail($id);
        $user = $application->user;

        DB::transaction(function () use ($application, $user, $admin) {
            $application->update([
                'status' => 'approved',
                'reviewed_by' => $admin->id,
                'reviewed_at' => now(),
            ]);

            $user->update(['role' => 'technician']);

            TechnicianProfile::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'professional_title' => $application->professional_title,
                    'years_experience' => $application->years_experience,
                    'location' => $application->location,
                    'service_area' => $application->service_area,
                    'bio' => $application->bio,
                    'skills' => $application->skills,
                    'verification_status' => 'approved',
                    'rejection_reason' => null,
                ]
            );

            if ($application->service_id) {
                $user->services()->syncWithoutDetaching([$application->service_id]);
            }
        });

        AuditLog::log('approve_technician', "Admin approved technician verification for {$user->full_name} (#{$user->id})", 'TechnicianApplication', $application->id);

        Notification::send(
            $user->id,
            'application_approved',
            'Congratulations! You are now a Verified FUNDI Technician',
            'Your technician application has been approved! Choose your subscription plan (Starter, Professional, or Premium) to start receiving client service requests.',
            route('technician.subscription')
        );

        return redirect()->route('admin.applications.index')->with('success', "Technician application for {$user->full_name} has been APPROVED.");
    }

    public function rejectApplication(Request $request, $id)
    {
        $admin = Auth::user();
        $application = TechnicianApplication::findOrFail($id);
        $user = $application->user;

        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        DB::transaction(function () use ($application, $user, $admin, $request) {
            $application->update([
                'status' => 'rejected',
                'rejection_reason' => $request->input('rejection_reason'),
                'reviewed_by' => $admin->id,
                'reviewed_at' => now(),
            ]);

            if ($user->technicianProfile) {
                $user->technicianProfile->update([
                    'verification_status' => 'rejected',
                    'rejection_reason' => $request->input('rejection_reason'),
                ]);
            }
        });

        AuditLog::log('reject_technician', "Admin rejected technician application for {$user->full_name}", 'TechnicianApplication', $application->id);

        Notification::send(
            $user->id,
            'application_rejected',
            'Technician Application Update',
            "Your technician application was not approved. Reason: {$request->input('rejection_reason')}",
            route('client.technician-application.status')
        );

        return redirect()->route('admin.applications.index')->with('info', "Technician application for {$user->full_name} was REJECTED.");
    }

    public function services()
    {
        $services = Service::withCount(['technicians', 'requests'])->orderBy('display_order')->get();
        return view('admin.services.index', compact('services'));
    }

    public function storeService(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:services,name',
            'description' => 'nullable|string',
            'icon' => 'required|string|max:50',
            'display_order' => 'nullable|integer',
        ]);

        $service = Service::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'],
            'icon' => $validated['icon'],
            'status' => 'active',
            'display_order' => $validated['display_order'] ?? 0,
        ]);

        AuditLog::log('create_service', "Admin added new service category: {$service->name}", 'Service', $service->id);

        return back()->with('success', 'Service category created successfully.');
    }

    public function updateService(Request $request, $id)
    {
        $service = Service::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:services,name,' . $service->id,
            'description' => 'nullable|string',
            'icon' => 'required|string|max:50',
            'display_order' => 'nullable|integer',
        ]);

        $service->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'],
            'icon' => $validated['icon'],
            'display_order' => $validated['display_order'] ?? 0,
        ]);

        AuditLog::log('update_service', "Admin updated service category: {$service->name}", 'Service', $service->id);

        return back()->with('success', 'Service category updated successfully.');
    }

    public function toggleServiceStatus($id)
    {
        $service = Service::findOrFail($id);
        $newStatus = $service->status === 'active' ? 'disabled' : 'active';
        $service->update(['status' => $newStatus]);

        AuditLog::log(
            $newStatus === 'disabled' ? 'disable_service' : 'enable_service',
            "Admin {$newStatus} service: {$service->name}",
            'Service',
            $service->id
        );

        return back()->with('success', "Service {$service->name} status changed to {$newStatus}.");
    }

    public function requests(Request $request)
    {
        $status = $request->input('status');
        $search = $request->input('q');

        $query = ServiceRequest::with(['client', 'technician.technicianProfile', 'service', 'latestQuotation', 'job', 'cancellationRecord']);

        if ($status) {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('reference_no', 'like', "%{$search}%")
                  ->orWhereHas('client', function ($cq) use ($search) {
                      $cq->where('full_name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('technician', function ($tq) use ($search) {
                      $tq->where('full_name', 'like', "%{$search}%");
                  });
            });
        }

        $requests = $query->latest()->paginate(15)->withQueryString();

        return view('admin.requests.index', compact('requests', 'status', 'search'));
    }

    public function requestShow($id)
    {
        $request = ServiceRequest::with([
            'client',
            'technician.technicianProfile',
            'service',
            'images',
            'quotations',
            'latestQuotation',
            'job',
            'messages.sender',
            'review',
            'complaint.evidences',
            'cancellationRecord.canceller'
        ])->findOrFail($id);

        return view('admin.requests.show', compact('request'));
    }

    public function jobs(Request $request)
    {
        $status = $request->input('status');

        $query = ServiceRequest::whereIn('status', ['quotation_accepted', 'scheduled', 'on_the_way', 'in_progress', 'completed', 'client_confirmed', 'reviewed', 'cancelled'])
            ->with(['client', 'technician', 'service', 'job', 'latestQuotation']);

        if ($status) {
            $query->where('status', $status);
        }

        $jobs = $query->latest()->paginate(15)->withQueryString();

        return view('admin.jobs.index', compact('jobs', 'status'));
    }

    public function reviews()
    {
        $reviews = Review::with(['client', 'technician', 'serviceRequest'])->latest()->paginate(15);
        return view('admin.reviews.index', compact('reviews'));
    }

    public function toggleReviewStatus(Request $request, $id)
    {
        $review = Review::findOrFail($id);
        $newStatus = $request->input('status', 'hidden');

        $review->update(['status' => $newStatus]);

        if ($review->technician && $review->technician->technicianProfile) {
            $review->technician->technicianProfile->recalculateRating();
        }

        AuditLog::log('moderate_review', "Admin changed review #{$review->id} status to {$newStatus}", 'Review', $review->id);

        return back()->with('success', "Review status updated to {$newStatus}.");
    }

    public function complaints(Request $request)
    {
        $status = $request->input('status');

        $query = Complaint::with(['client', 'serviceRequest.technician', 'resolver', 'evidences']);

        if ($status) {
            $query->where('status', $status);
        }

        $complaints = $query->latest()->paginate(15)->withQueryString();

        return view('admin.complaints.index', compact('complaints', 'status'));
    }

    public function complaintShow($id)
    {
        $complaint = Complaint::with(['client', 'serviceRequest.technician', 'serviceRequest.messages', 'serviceRequest.latestQuotation', 'resolver', 'evidences'])->findOrFail($id);
        return view('admin.complaints.show', compact('complaint'));
    }

    public function resolveComplaint(Request $request, $id)
    {
        $admin = Auth::user();
        $complaint = Complaint::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:under_review,resolved,closed',
            'resolution' => 'required|string|max:3000',
        ]);

        $complaint->update([
            'status' => $validated['status'],
            'resolution' => $validated['resolution'],
            'resolved_by' => $admin->id,
            'resolved_at' => now(),
        ]);

        AuditLog::log('resolve_complaint', "Admin resolved complaint #{$complaint->id} with status {$validated['status']}", 'Complaint', $complaint->id);

        Notification::send(
            $complaint->client_id,
            'complaint_update',
            'Dispute/Complaint Update',
            "Your complaint for {$complaint->serviceRequest->reference_no} has been updated: {$validated['status']}. Resolution: {$validated['resolution']}"
        );

        return back()->with('success', 'Complaint resolution saved.');
    }

    public function userReports(Request $request)
    {
        $status = $request->input('status');

        $reports = UserReport::with(['reporter', 'reportedUser', 'serviceRequest'])
            ->when($status, fn($q) => $q->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.reports.user-reports', compact('reports', 'status'));
    }

    public function updateUserReportStatus(Request $request, $id)
    {
        $report = UserReport::findOrFail($id);
        $status = $request->input('status', 'reviewed');

        $report->update(['status' => $status]);

        AuditLog::log('moderate_user_report', "Admin set user report #{$id} status to {$status}", 'UserReport', $id);

        return back()->with('success', 'Report status updated.');
    }

    public function reports()
    {
        $userGrowth = User::selectRaw('DATE(created_at) as date, count(*) as count')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->take(30)
            ->get();

        $serviceStats = Service::withCount('requests')->orderByDesc('requests_count')->get();

        $jobStats = [
            'total' => ServiceRequest::whereIn('status', ['completed', 'client_confirmed', 'reviewed', 'scheduled', 'on_the_way', 'in_progress', 'cancelled'])->count(),
            'completed' => ServiceRequest::whereIn('status', ['completed', 'client_confirmed', 'reviewed'])->count(),
            'active' => ServiceRequest::whereIn('status', ['scheduled', 'on_the_way', 'in_progress'])->count(),
            'cancelled' => ServiceRequest::where('status', 'cancelled')->count(),
        ];

        $topTechnicians = User::where('role', 'technician')
            ->whereHas('technicianProfile', function ($q) {
                $q->where('verification_status', 'approved');
            })
            ->with('technicianProfile')
            ->get()
            ->sortByDesc(fn($u) => $u->technicianProfile->completed_jobs_count ?? 0)
            ->take(10);

        return view('admin.reports.index', compact('userGrowth', 'serviceStats', 'jobStats', 'topTechnicians'));
    }

    public function exportPlatformCsv()
    {
        $filename = 'fundi_platform_report_' . date('Y-m-d_His') . '.csv';

        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF"); // UTF-8 BOM for Excel

            // Section 1: Executive KPI Summary
            fputcsv($handle, ['FUNDI PLATFORM - EXECUTIVE ANALYTICS REPORT']);
            fputcsv($handle, ['Generated At', date('Y-m-d H:i:s'), 'Generated By', Auth::user()->full_name]);
            fputcsv($handle, []);

            fputcsv($handle, ['METRIC', 'VALUE']);
            fputcsv($handle, ['Total Registered Users', User::count()]);
            fputcsv($handle, ['Total Clients', User::where('role', 'client')->count()]);
            fputcsv($handle, ['Total Technicians', User::where('role', 'technician')->count()]);
            fputcsv($handle, ['Total Service Requests', ServiceRequest::count()]);
            fputcsv($handle, ['Completed Jobs', ServiceRequest::whereIn('status', ['completed', 'client_confirmed', 'reviewed'])->count()]);
            fputcsv($handle, ['Active / In-Flight Jobs', ServiceRequest::whereIn('status', ['scheduled', 'on_the_way', 'in_progress'])->count()]);
            fputcsv($handle, ['Cancelled Jobs', ServiceRequest::where('status', 'cancelled')->count()]);
            
            $subRev = \App\Models\SubscriptionPayment::where('status', 'success')->sum('amount');
            $clientRev = ServiceRequest::where('connection_fee_status', 'paid')->sum('connection_fee');
            fputcsv($handle, ['Technician Subscription Revenue (TZS)', number_format($subRev, 2)]);
            fputcsv($handle, ['Client Connection Fees Revenue (TZS)', number_format($clientRev, 2)]);
            fputcsv($handle, ['Total Business Revenue (TZS)', number_format($subRev + $clientRev, 2)]);
            fputcsv($handle, []);

            // Section 2: Service Categories Demand
            fputcsv($handle, ['SERVICE CATEGORIES BREAKDOWN']);
            fputcsv($handle, ['Category Name', 'Total Requests', 'Demand Share %']);
            $services = Service::withCount('requests')->orderByDesc('requests_count')->get();
            $totalReq = ServiceRequest::count() ?: 1;
            foreach ($services as $srv) {
                $pct = round(($srv->requests_count / $totalReq) * 100, 1);
                fputcsv($handle, [$srv->name, $srv->requests_count, $pct . '%']);
            }
            fputcsv($handle, []);

            // Section 3: Top Technicians
            fputcsv($handle, ['TOP PERFORMING TECHNICIANS']);
            fputcsv($handle, ['Full Name', 'Phone', 'Title', 'Location', 'Completed Jobs', 'Rating']);
            $techs = User::where('role', 'technician')
                ->whereHas('technicianProfile', function ($q) {
                    $q->where('verification_status', 'approved');
                })
                ->with('technicianProfile')
                ->get()
                ->sortByDesc(fn($u) => $u->technicianProfile->completed_jobs_count ?? 0)
                ->take(20);

            foreach ($techs as $t) {
                fputcsv($handle, [
                    $t->full_name,
                    $t->phone,
                    $t->technicianProfile->professional_title ?? 'N/A',
                    $t->technicianProfile->location ?? 'N/A',
                    $t->technicianProfile->completed_jobs_count ?? 0,
                    number_format($t->technicianProfile->average_rating ?? 5.0, 1),
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    public function executiveSummary()
    {
        $admin = Auth::user();
        $totalUsers = User::count();
        $totalClients = User::where('role', 'client')->count();
        $totalTechs = User::where('role', 'technician')->count();

        $jobStats = [
            'total' => ServiceRequest::whereIn('status', ['completed', 'client_confirmed', 'reviewed', 'scheduled', 'on_the_way', 'in_progress', 'cancelled'])->count(),
            'completed' => ServiceRequest::whereIn('status', ['completed', 'client_confirmed', 'reviewed'])->count(),
            'active' => ServiceRequest::whereIn('status', ['scheduled', 'on_the_way', 'in_progress'])->count(),
            'cancelled' => ServiceRequest::where('status', 'cancelled')->count(),
        ];

        $subRev = \App\Models\SubscriptionPayment::where('status', 'success')->sum('amount');
        $clientRev = ServiceRequest::where('connection_fee_status', 'paid')->sum('connection_fee');
        $totalProfit = $subRev + $clientRev;

        $serviceStats = Service::withCount('requests')->orderByDesc('requests_count')->get();

        $topTechnicians = User::where('role', 'technician')
            ->whereHas('technicianProfile', function ($q) {
                $q->where('verification_status', 'approved');
            })
            ->with('technicianProfile')
            ->get()
            ->sortByDesc(fn($u) => $u->technicianProfile->completed_jobs_count ?? 0)
            ->take(10);

        $recentJobs = ServiceRequest::with(['client', 'technician', 'service'])
            ->latest()
            ->take(10)
            ->get();

        return view('admin.reports.executive_summary', compact(
            'admin',
            'totalUsers',
            'totalClients',
            'totalTechs',
            'jobStats',
            'subRev',
            'clientRev',
            'totalProfit',
            'serviceStats',
            'topTechnicians',
            'recentJobs'
        ));
    }

    public function auditLogs(Request $request)
    {
        $action = $request->input('action');
        $search = $request->input('q');

        $query = AuditLog::with('user');

        if ($action) {
            $query->where('action', $action);
        }
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('full_name', 'like', "%{$search}%");
                  });
            });
        }

        $logs = $query->latest()->paginate(25)->withQueryString();

        return view('admin.audit-logs.index', compact('logs', 'action', 'search'));
    }

    public function profile()
    {
        $admin = Auth::user();
        $auditCount = AuditLog::where('user_id', $admin->id)->count();
        return view('admin.profile', compact('admin', 'auditCount'));
    }

    public function updateProfile(Request $request)
    {
        $admin = Auth::user();

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $admin->id,
            'phone' => 'required|string|max:20|unique:users,phone,' . $admin->id,
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $admin->avatar = $avatarPath;
        }

        $admin->full_name = trim($validated['first_name'] . ' ' . $validated['last_name']);
        $admin->email = $validated['email'];
        $admin->phone = $validated['phone'];
        $admin->save();

        AuditLog::log('update_admin_profile', "Admin {$admin->full_name} updated their profile information", 'User', $admin->id);

        return back()->with('success', 'Taarifa za Admin profile zimesasishwa kikamilifu.');
    }

    public function updatePassword(Request $request)
    {
        $admin = Auth::user();

        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if (!Hash::check($request->current_password, $admin->password)) {
            return back()->withErrors(['current_password' => 'Nenosiri la sasa si sahihi.'])->withInput();
        }

        $admin->password = Hash::make($request->password);
        $admin->save();

        AuditLog::log('change_admin_password', "Admin {$admin->full_name} changed their account password", 'User', $admin->id);

        return back()->with('success', 'Nenosiri la Admin limebadilishwa kikamilifu.');
    }

    public function settings()
    {
        return view('admin.settings');
    }
}
