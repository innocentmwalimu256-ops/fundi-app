@extends('layouts.admin')

@section('title', __('Admin Overview') . ' — FUNDI')
@section('page_title', __('System Control & Analytics Center'))

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">

    <!-- 1. Header Banner -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-200">
        <div>
            <div class="flex items-center space-x-2.5">
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">{{ __('Platform Overview') }}</h1>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-teal-50 text-teal-700 border border-teal-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-teal-500 mr-1.5 animate-pulse"></span>
                    {{ __('Live System Active') }}
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-1">{{ __('Real-time marketplace telemetry, artisan verification, and financial ledger.') }}</p>
        </div>

        <div class="flex items-center space-x-2.5">
            <a href="{{ route('admin.subscriptions.payments') }}" class="px-3.5 py-2 rounded-xl bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 font-bold text-xs shadow-sm transition flex items-center space-x-1.5">
                <i data-lucide="wallet" class="w-4 h-4 text-teal-600"></i>
                <span>{{ __('Payment Ledger') }}</span>
            </a>
            <a href="{{ route('admin.applications.index') }}" class="px-3.5 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs shadow-sm transition flex items-center space-x-1.5">
                <i data-lucide="badge-check" class="w-4 h-4"></i>
                <span>{{ __('Verify Artisans') }} ({{ $kpis['pending_applications'] ?? 0 }})</span>
            </a>
        </div>
    </div>

    <!-- 2. KPI Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
        
        <!-- Total Users -->
        <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm flex flex-col justify-between space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">{{ __('Total Users') }}</span>
                <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center">
                    <i data-lucide="users" class="w-4 h-4"></i>
                </div>
            </div>
            <div>
                <h3 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">{{ number_format($kpis['total_users'] ?? 0) }}</h3>
                <p class="text-[11px] text-slate-500 mt-1">
                    <span class="font-bold text-slate-700">{{ $kpis['clients'] ?? 0 }} {{ __('Clients') }}</span> • <span class="font-bold text-teal-700">{{ $kpis['technicians'] ?? 0 }} {{ __('Techs') }}</span>
                </p>
            </div>
        </div>

        <!-- Pending Applications -->
        <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm flex flex-col justify-between space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">{{ __('Pending Verification') }}</span>
                <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                    <i data-lucide="award" class="w-4 h-4"></i>
                </div>
            </div>
            <div>
                <h3 class="text-2xl sm:text-3xl font-black text-amber-600 tracking-tight">{{ $kpis['pending_applications'] ?? 0 }}</h3>
                <p class="text-[11px] text-amber-600 font-semibold mt-1">{{ __('Awaiting NIDA & Certificate Review') }}</p>
            </div>
        </div>

        <!-- Active Jobs -->
        <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm flex flex-col justify-between space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">{{ __('Active Service Jobs') }}</span>
                <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                    <i data-lucide="calendar" class="w-4 h-4"></i>
                </div>
            </div>
            <div>
                <h3 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">{{ $kpis['active_jobs'] ?? 0 }}</h3>
                <p class="text-[11px] text-slate-500 mt-1">
                    <span class="text-emerald-600 font-bold">{{ $kpis['completed_jobs'] ?? 0 }} {{ __('Completed') }}</span> • {{ __('100% Verified') }}
                </p>
            </div>
        </div>

        <!-- Platform Revenue -->
        <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm flex flex-col justify-between space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">{{ __('Platform Revenue') }}</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <i data-lucide="trending-up" class="w-4 h-4"></i>
                </div>
            </div>
            <div>
                @php
                    $totalRev = (\App\Models\SubscriptionPayment::where('status', 'success')->sum('amount') ?? 0) +
                                (\App\Models\ServiceRequest::where('connection_fee_status', 'paid')->sum('connection_fee') ?: (\App\Models\ServiceRequest::where('connection_fee_status', 'paid')->count() * 500));
                @endphp
                <h3 class="text-2xl sm:text-3xl font-black text-emerald-700 tracking-tight font-mono">TZS {{ number_format($totalRev) }}</h3>
                <p class="text-[11px] text-slate-500 mt-1">{{ __('Subscriptions + Connection Fees') }}</p>
            </div>
        </div>

    </div>

    <!-- 3. Operations & Verification Desk (2 Columns) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Left 7 Cols: Service Category Demand -->
        <div class="lg:col-span-7 bg-white rounded-xl p-5 sm:p-6 border border-slate-200 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">{{ __('Trade Demand by Category') }}</h2>
                    <p class="text-xs text-slate-500">{{ __('Client service requests across sectors') }}</p>
                </div>
                <span class="text-xs font-bold text-teal-700 bg-teal-50 px-2.5 py-1 rounded-lg">{{ __('Real-time') }}</span>
            </div>

            <div class="space-y-3.5 pt-1">
                @foreach($popularServices as $s)
                @php 
                    $maxCount = max(1, $popularServices->max('requests_count'));
                    $pct = round(($s->requests_count / $maxCount) * 100);
                @endphp
                <div class="space-y-1.5">
                    <div class="flex justify-between items-center text-xs font-bold">
                        <span class="text-slate-800 flex items-center space-x-2">
                            <span class="w-2 h-2 rounded-full bg-teal-500"></span>
                            <span>{{ __($s->name) }}</span>
                        </span>
                        <span class="font-mono text-slate-900">{{ $s->requests_count }} {{ __('requests') }} <span class="text-slate-400 font-normal">({{ $pct }}%)</span></span>
                    </div>
                    <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                        <div class="h-full bg-teal-600 rounded-full transition-all duration-500" style="width: {{ max(5, $pct) }}%;"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Right 5 Cols: Technician Verification Queue -->
        <div class="lg:col-span-5 bg-white rounded-xl p-5 sm:p-6 border border-slate-200 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center space-x-2">
                    <i data-lucide="shield-alert" class="w-4 h-4 text-amber-500"></i>
                    <h2 class="text-sm font-bold text-slate-900">{{ __('Verification Queue') }}</h2>
                </div>
                <a href="{{ route('admin.applications.index') }}" class="text-xs font-bold text-teal-700 hover:underline">
                    {{ __('View all') }} ({{ $kpis['pending_applications'] ?? 0 }})
                </a>
            </div>

            <div class="divide-y divide-slate-100">
                @forelse($pendingApplications ?? [] as $app)
                <div class="py-3 first:pt-0 last:pb-0 flex items-center justify-between gap-3">
                    <div class="flex items-center space-x-3">
                        <div class="w-9 h-9 rounded-xl bg-slate-900 text-teal-300 font-bold text-xs flex items-center justify-center flex-shrink-0">
                            {{ $app->user->initials ?? 'T' }}
                        </div>
                        <div class="min-w-0">
                            <h4 class="text-xs font-bold text-slate-900 truncate">{{ $app->user->full_name }}</h4>
                            <p class="text-[11px] text-teal-700 font-semibold truncate">{{ __($app->professional_title ?? 'Artisan') }}</p>
                            <p class="text-[10px] text-slate-400">{{ $app->location ?? 'Dar es Salaam' }} • {{ $app->years_experience ?? 1 }} {{ __('yrs exp') }}</p>
                        </div>
                    </div>

                    <a href="{{ route('admin.applications.show', $app->id) }}" class="px-3 py-1.5 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-xl shadow-xs transition flex-shrink-0">
                        {{ __('Review') }} →
                    </a>
                </div>
                @empty
                <div class="py-8 text-center text-slate-400 text-xs">
                    {{ __('No pending artisan applications awaiting review.') }}
                </div>
                @endforelse
            </div>
        </div>

    </div>

    <!-- 4. Recent Service Requests Table -->
    <div class="bg-white rounded-xl p-5 sm:p-6 border border-slate-200 shadow-sm space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 pb-3">
            <div>
                <h2 class="text-sm font-bold text-slate-900">{{ __('Recent Service Requests') }}</h2>
                <p class="text-xs text-slate-500">{{ __('Live client bookings and technician assignments') }}</p>
            </div>
            <a href="{{ route('admin.requests.index') }}" class="text-xs font-bold text-teal-700 hover:underline">
                {{ __('View all requests') }} →
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider text-[10px] border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-4">{{ __('Reference') }}</th>
                        <th class="py-3 px-4">{{ __('Client') }}</th>
                        <th class="py-3 px-4">{{ __('Technician') }}</th>
                        <th class="py-3 px-4">{{ __('Service') }}</th>
                        <th class="py-3 px-4">{{ __('Connection Fee') }}</th>
                        <th class="py-3 px-4">{{ __('Status') }}</th>
                        <th class="py-3 px-4 text-right">{{ __('Action') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentRequests as $req)
                    <tr class="hover:bg-slate-50/70 transition">
                        <td class="py-3 px-4 font-mono font-bold text-slate-900">
                            {{ $req->reference_no ?? ('REQ-' . str_pad($req->id, 6, '0', STR_PAD_LEFT)) }}
                        </td>
                        <td class="py-3 px-4 font-semibold text-slate-900">{{ $req->client->full_name ?? __('Client') }}</td>
                        <td class="py-3 px-4 text-slate-600">{{ $req->technician->full_name ?? __('Assigned Fundi') }}</td>
                        <td class="py-3 px-4 text-slate-800 font-medium">{{ __($req->service->name ?? 'General') }}</td>
                        <td class="py-3 px-4">
                            @if($req->connection_fee_status === 'paid')
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <i data-lucide="check" class="w-3.5 h-3.5 inline text-emerald-600"></i> {{ __('PAID') }} (TZS 2,000)
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                    {{ __('PENDING') }}
                                </span>
                            @endif
                        </td>
                        <td class="py-3 px-4">
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold
                                @if(in_array($req->status, ['completed', 'reviewed'])) bg-emerald-50 text-emerald-700 border border-emerald-200
                                @elseif(in_array($req->status, ['in_progress', 'accepted'])) bg-blue-50 text-blue-700 border border-blue-200
                                @else bg-slate-100 text-slate-600 border border-slate-200 @endif">
                                {{ __(ucfirst(str_replace('_', ' ', $req->status))) }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-right">
                            <a href="{{ route('admin.requests.show', $req->id) }}" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-900 hover:text-white text-slate-800 font-bold transition text-[11px]">
                                {{ __('Manage') }}
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-6 text-center text-xs text-slate-400">{{ __('No service requests logged yet.') }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
