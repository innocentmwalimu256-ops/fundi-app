<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ServiceRequestController;
use App\Http\Controllers\SnippeWebhookController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\TechnicianController;
use Illuminate\Support\Facades\Route;

// Snippe Payment Webhook Endpoint (Exempt from CSRF)
Route::post('/api/webhook/snippe', [SnippeWebhookController::class, 'handle'])->name('webhook.snippe');
Route::post('/snippe/payment/webhook', [SnippeWebhookController::class, 'handle'])->name('webhook.snippe.alt');

// Root Route: Shows Login Page Directly (or redirects to dashboard if already logged in)
Route::get('/', [AuthController::class, 'showLogin'])->name('home');

// Guest Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('password.email');

    // Presentation Demo Control Center
    Route::get('/demo', function () {
        return view('auth.demo');
    })->name('demo');
});

// Language Switcher (Swahili & English)
Route::get('/language/{locale}', [\App\Http\Controllers\LanguageController::class, 'switch'])->name('language.switch');

// Logout Route (Supports both GET and POST for seamless browser back-navigation)
Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('logout');

// Authenticated Shared Routes
Route::middleware('auth')->group(function () {
    // In-System Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');

    // Request-Scoped Messaging
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::post('/messages/{requestId}', [MessageController::class, 'store'])->name('messages.store');

    // Favorites & Reports
    Route::post('/technicians/{id}/favorite', [ClientController::class, 'toggleFavorite'])->name('technicians.favorite');
    Route::post('/technicians/{id}/report', [ClientController::class, 'reportUser'])->name('technicians.report');

    // Digital Receipt (View / Print)
    Route::get('/requests/{id}/receipt', [ServiceRequestController::class, 'receipt'])->name('requests.receipt');
});

// Client Routes
Route::middleware(['auth', 'role:client'])->group(function () {
    Route::get('/dashboard', [ClientController::class, 'dashboard'])->name('client.dashboard');
    Route::get('/services', [ClientController::class, 'services'])->name('client.services.index');
    Route::get('/services/{id}', [ClientController::class, 'serviceShow'])->name('client.services.show');
    Route::get('/technicians', [ClientController::class, 'technicians'])->name('client.technicians.index');
    Route::get('/technicians/{id}', [ClientController::class, 'technicianProfile'])->name('client.technicians.show');

    // Service Requests & Lifecycle
    Route::get('/requests', [ServiceRequestController::class, 'index'])->name('client.requests.index');
    Route::get('/requests/create', [ServiceRequestController::class, 'create'])->name('client.requests.create');
    Route::post('/requests', [ServiceRequestController::class, 'store'])->name('client.requests.store');
    Route::get('/requests/{id}', [ServiceRequestController::class, 'show'])->name('client.requests.show');
    Route::post('/requests/{id}/pay-fee', [ServiceRequestController::class, 'payConnectionFee'])->name('client.requests.pay-fee');
    Route::post('/requests/{id}/accept-quotation', [ServiceRequestController::class, 'acceptQuotation'])->name('client.requests.accept-quotation');
    Route::post('/requests/{id}/reject-quotation', [ServiceRequestController::class, 'rejectQuotation'])->name('client.requests.reject-quotation');
    Route::post('/requests/{id}/cancel', [ServiceRequestController::class, 'cancelRequest'])->name('client.requests.cancel');
    Route::post('/requests/{id}/confirm-completion', [ServiceRequestController::class, 'confirmCompletion'])->name('client.requests.confirm-completion');
    Route::post('/requests/{id}/review', [ServiceRequestController::class, 'storeReview'])->name('client.requests.review');
    Route::post('/requests/{id}/complaint', [ServiceRequestController::class, 'storeComplaint'])->name('client.requests.complaint');

    // Favorites & Profile
    Route::get('/favorites', [ClientController::class, 'favorites'])->name('client.favorites.index');
    Route::get('/profile', [ClientController::class, 'profile'])->name('client.profile');
    Route::post('/profile', [ClientController::class, 'updateProfile'])->name('client.profile.update');

    // Become a Technician Onboarding
    Route::get('/become-technician', [ClientController::class, 'becomeTechnician'])->name('client.become-technician');
    Route::post('/become-technician', [ClientController::class, 'storeTechnicianApplication'])->name('client.become-technician.submit');
    Route::get('/technician-application/status', [ClientController::class, 'technicianApplicationStatus'])->name('client.technician-application.status');
});

// Technician Routes
Route::prefix('technician')->name('technician.')->middleware(['auth', 'role:technician'])->group(function () {
    
    // Subscription & Plan Selection (Available to both active & expired technicians)
    Route::get('/subscription', [SubscriptionController::class, 'index'])->name('subscription');
    Route::get('/subscription/expired', [SubscriptionController::class, 'expired'])->name('subscription.expired');
    Route::get('/subscription/{slug}/checkout', [SubscriptionController::class, 'checkout'])->name('subscription.checkout');
    Route::post('/subscription/{slug}/pay', [SubscriptionController::class, 'processPayment'])->name('subscription.pay');

    // Dashboard, Profile & Information (Accessible to both active & expired technicians)
    Route::get('/dashboard', [TechnicianController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile', [TechnicianController::class, 'profile'])->name('profile');
    Route::post('/profile', [TechnicianController::class, 'updateProfile'])->name('profile.update');
    Route::get('/availability', [TechnicianController::class, 'availability'])->name('availability');
    Route::post('/availability', [TechnicianController::class, 'updateAvailability'])->name('availability.update');
    Route::get('/portfolios', [TechnicianController::class, 'portfolios'])->name('portfolios.index');
    Route::post('/portfolios', [TechnicianController::class, 'storePortfolio'])->name('portfolios.store');
    Route::delete('/portfolios/{id}', [TechnicianController::class, 'deletePortfolio'])->name('portfolios.destroy');
    Route::get('/reviews', [TechnicianController::class, 'reviews'])->name('reviews.index');

    // Operational Marketplace Features (Protected by subscription.active middleware)
    Route::middleware('subscription.active')->group(function () {
        Route::get('/requests', [TechnicianController::class, 'requests'])->name('requests.index');
        Route::get('/requests/{id}', [TechnicianController::class, 'requestShow'])->name('requests.show');
        Route::post('/requests/{id}/accept', [TechnicianController::class, 'acceptRequest'])->name('requests.accept');
        Route::post('/requests/{id}/decline', [TechnicianController::class, 'declineRequest'])->name('requests.decline');
        Route::post('/requests/{id}/quotation', [TechnicianController::class, 'storeQuotation'])->name('requests.quotation');

        // Jobs
        Route::get('/jobs', [TechnicianController::class, 'jobs'])->name('jobs.index');
        Route::get('/jobs/{id}', [TechnicianController::class, 'requestShow'])->name('jobs.show');
        Route::post('/jobs/{id}/status', [TechnicianController::class, 'updateJobStatus'])->name('jobs.status');
        Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    });
});

// Admin Routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // Users
    Route::get('/users', [AdminController::class, 'users'])->name('users.index');
    Route::post('/users/{id}/toggle-status', [AdminController::class, 'toggleUserStatus'])->name('users.toggle-status');

    // Technicians & Verification Applications
    Route::get('/technicians', [AdminController::class, 'technicians'])->name('technicians.index');
    Route::get('/applications', [AdminController::class, 'applications'])->name('applications.index');
    Route::get('/applications/{id}', [AdminController::class, 'applicationShow'])->name('applications.show');
    Route::post('/applications/{id}/approve', [AdminController::class, 'approveApplication'])->name('applications.approve');
    Route::post('/applications/{id}/reject', [AdminController::class, 'rejectApplication'])->name('applications.reject');

    // Services Management
    Route::get('/services', [AdminController::class, 'services'])->name('services.index');
    Route::post('/services', [AdminController::class, 'storeService'])->name('services.store');
    Route::put('/services/{id}', [AdminController::class, 'updateService'])->name('services.update');
    Route::post('/services/{id}/toggle-status', [AdminController::class, 'toggleServiceStatus'])->name('services.toggle-status');

    // Requests & Jobs Monitoring
    Route::get('/requests', [AdminController::class, 'requests'])->name('requests.index');
    Route::get('/requests/{id}', [AdminController::class, 'requestShow'])->name('requests.show');
    Route::get('/jobs', [AdminController::class, 'jobs'])->name('jobs.index');

    // Reviews Moderation
    Route::get('/reviews', [AdminController::class, 'reviews'])->name('reviews.index');
    Route::post('/reviews/{id}/status', [AdminController::class, 'toggleReviewStatus'])->name('reviews.status');

    // Complaints & Safety Reports
    Route::get('/complaints', [AdminController::class, 'complaints'])->name('complaints.index');
    Route::get('/complaints/{id}', [AdminController::class, 'complaintShow'])->name('complaints.show');
    Route::post('/complaints/{id}/resolve', [AdminController::class, 'resolveComplaint'])->name('complaints.resolve');
    Route::get('/user-reports', [AdminController::class, 'userReports'])->name('user-reports.index');
    Route::post('/user-reports/{id}', [AdminController::class, 'updateUserReportStatus'])->name('user-reports.update');

    // Subscriptions, Plans & Revenue Monetization (v4.0)
    Route::get('/subscriptions', [SubscriptionController::class, 'adminIndex'])->name('subscriptions.index');
    Route::get('/subscriptions/plans', [SubscriptionController::class, 'plansIndex'])->name('subscriptions.plans');
    Route::post('/subscriptions/plans', [SubscriptionController::class, 'storePlan'])->name('subscriptions.plans.store');
    Route::put('/subscriptions/plans/{id}', [SubscriptionController::class, 'updatePlan'])->name('subscriptions.plans.update');
    Route::get('/subscriptions/payments', [SubscriptionController::class, 'paymentsIndex'])->name('subscriptions.payments');
    Route::post('/subscriptions/payments/{id}/verify', [SubscriptionController::class, 'verifyPayment'])->name('subscriptions.payments.verify');
    Route::post('/subscriptions/payments/{id}/reject', [SubscriptionController::class, 'rejectPayment'])->name('subscriptions.payments.reject');
    Route::post('/subscriptions/payments/{id}/toggle', [SubscriptionController::class, 'togglePaymentStatus'])->name('subscriptions.payments.toggle');
    Route::post('/subscriptions/manual-activate', [SubscriptionController::class, 'manualActivate'])->name('subscriptions.manual-activate');
    Route::post('/subscriptions/client-payments/{id}/verify', [SubscriptionController::class, 'verifyClientFee'])->name('subscriptions.client-payments.verify');
    Route::post('/subscriptions/client-payments/{id}/reject', [SubscriptionController::class, 'rejectClientFee'])->name('subscriptions.client-payments.reject');
    Route::post('/subscriptions/client-payments/{id}/toggle', [SubscriptionController::class, 'toggleClientFeeStatus'])->name('subscriptions.client-payments.toggle');
    Route::get('/subscriptions/revenue', [SubscriptionController::class, 'revenueReport'])->name('subscriptions.revenue');
    Route::get('/subscriptions/revenue/export-csv', [SubscriptionController::class, 'exportRevenueCsv'])->name('subscriptions.revenue.export.csv');

    // Reports & Audit Logs & Profile
    Route::get('/reports', [AdminController::class, 'reports'])->name('reports.index');
    Route::get('/reports/export-csv', [AdminController::class, 'exportPlatformCsv'])->name('reports.export.csv');
    Route::get('/reports/executive-summary', [AdminController::class, 'executiveSummary'])->name('reports.executive-summary');
    Route::get('/audit-logs', [AdminController::class, 'auditLogs'])->name('audit-logs.index');
    Route::get('/profile', [AdminController::class, 'profile'])->name('profile.index');
    Route::put('/profile', [AdminController::class, 'updateProfile'])->name('profile.update');
    Route::put('/profile/password', [AdminController::class, 'updatePassword'])->name('profile.password');
    Route::get('/settings', [AdminController::class, 'settings'])->name('settings.index');
});
