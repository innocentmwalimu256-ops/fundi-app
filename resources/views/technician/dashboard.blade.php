@extends('layouts.app')

@section('title', __('Dashboard') . ' — FUNDI')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    <!-- 1. Executive Hero Header for Technician -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-900 via-slate-800 to-teal-950 p-6 sm:p-8 text-white shadow-lg border border-slate-700/50">
        <!-- Background glows -->
        <div class="absolute -right-16 -top-16 w-64 h-64 rounded-full bg-teal-500/10 blur-3xl pointer-events-none"></div>
        <div class="absolute -left-16 -bottom-16 w-64 h-64 rounded-full bg-blue-500/10 blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="space-y-2">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="px-2.5 py-0.5 rounded-full bg-teal-400/20 text-teal-300 text-[10px] font-black uppercase tracking-wider border border-teal-400/30 flex items-center">
                        <span class="w-1.5 h-1.5 rounded-full bg-teal-400 mr-1.5 animate-pulse"></span>
                        {{ __('Technician Workspace') }}
                    </span>
                    <span class="text-xs text-slate-400">&bull;</span>
                    <span class="text-xs text-emerald-400 font-medium flex items-center">
                        <i data-lucide="shield-check" class="w-3.5 h-3.5 mr-1"></i> {{ __('Verified Specialist') }}
                    </span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight">
                    {{ __('Welcome back') }}, {{ $technician->full_name }}
                </h1>
                <p class="text-xs sm:text-sm text-slate-300 max-w-2xl font-normal leading-relaxed">
                    {{ __($profile->professional_title ?? 'Master Technician') }} &bull; {{ $profile->location ?? 'Dar es Salaam' }} &bull; {{ __('Review incoming customer leads, dispatch quotations, and advance job stages.') }}
                </p>
            </div>

            <!-- Fast Action Controls: Availability & Subscription -->
            <div class="flex flex-wrap items-center gap-3 flex-shrink-0">
                <a href="{{ route('technician.availability') }}" class="btn-tap px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs backdrop-blur-md border border-white/15 transition flex items-center space-x-2">
                    <span class="relative flex h-2 w-2">
                        @if(($profile->availability_status ?? 'available') === 'available')
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-400"></span>
                        @else
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-400"></span>
                        @endif
                    </span>
                    <span>{{ ($profile->availability_status ?? 'available') === 'available' ? __('Status: Available') : __('Status: Busy') }}</span>
                </a>

                @if(!$subscription || !$subscription->isActive())
                    <a href="{{ route('technician.subscription') }}" class="btn-tap px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-black text-xs shadow-lg shadow-amber-500/20 transition flex items-center space-x-2">
                        <i data-lucide="lock" class="w-3.5 h-3.5"></i>
                        <span>{{ __('Activate Plan') }}</span>
                    </a>
                @else
                    <a href="{{ route('technician.subscription') }}" class="btn-tap px-4 py-2.5 rounded-xl bg-emerald-500/20 hover:bg-emerald-500/30 text-emerald-300 border border-emerald-400/30 font-bold text-xs transition flex items-center space-x-1.5">
                        <i data-lucide="check" class="w-3.5 h-3.5"></i>
                        <span>{{ optional($subscription->plan)->name ?? __('Active Plan') }}</span>
                    </a>
                @endif
            </div>
        </div>
    </div>

    <!-- 2. Subscription Inactive Warning Banner -->
    @if(!$subscription || !$subscription->isActive())
    <div class="bg-gradient-to-r from-amber-600 via-amber-700 to-teal-800 rounded-2xl p-5 text-white shadow-md flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center space-x-3.5">
            <div class="w-10 h-10 rounded-xl bg-white/20 backdrop-blur-md text-white flex items-center justify-center font-bold flex-shrink-0">
                <i data-lucide="lock" class="w-5 h-5"></i>
            </div>
            <div>
                <h3 class="text-sm sm:text-base font-bold text-white">{{ __('Subscription Required to Receive Direct Customer Calls') }}</h3>
                <p class="text-xs text-white/90 mt-0.5">
                    {{ __('Subscribe to unlock direct WhatsApp contacts, appear in search results, and quote on requests.') }}
                </p>
            </div>
        </div>
        <a href="{{ route('technician.subscription') }}" class="btn-tap px-4 py-2.5 rounded-xl bg-slate-950 hover:bg-slate-900 text-white font-bold text-xs shadow-md transition text-center flex items-center justify-center space-x-2 flex-shrink-0">
            <span>{{ __('Activate Subscription') }}</span>
            <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
        </a>
    </div>
    @endif

    <!-- 3. 4 Metric KPI Telemetry Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Metric 1: Incoming Requests -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-xs hover:border-amber-400 hover:shadow-sm transition group">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">{{ __('Incoming Requests') }}</span>
                <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-700 border border-amber-200/80 flex items-center justify-center transition group-hover:scale-105">
                    <i data-lucide="inbox" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="mt-3">
                <div class="text-2xl sm:text-3xl font-black text-slate-900 font-mono tracking-tight">{{ $newRequestsCount }}</div>
                <div class="flex items-center space-x-1.5 mt-1 text-[11px] text-amber-700 font-medium">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                    <span>{{ __('Awaiting your quotation') }}</span>
                </div>
            </div>
        </div>

        <!-- Metric 2: Ongoing Jobs -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-xs hover:border-teal-400 hover:shadow-sm transition group">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">{{ __('Ongoing Jobs') }}</span>
                <div class="w-9 h-9 rounded-xl bg-teal-50 text-teal-700 border border-teal-200/80 flex items-center justify-center transition group-hover:scale-105">
                    <i data-lucide="activity" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="mt-3">
                <div class="text-2xl sm:text-3xl font-black text-slate-900 font-mono tracking-tight">{{ $activeJobsCount }}</div>
                <div class="flex items-center space-x-1.5 mt-1 text-[11px] text-teal-700 font-medium">
                    <span class="w-1.5 h-1.5 rounded-full bg-teal-500"></span>
                    <span>{{ __('In execution pipeline') }}</span>
                </div>
            </div>
        </div>

        <!-- Metric 3: Completed Jobs -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-xs hover:border-emerald-400 hover:shadow-sm transition group">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">{{ __('Completed') }}</span>
                <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-200/80 flex items-center justify-center transition group-hover:scale-105">
                    <i data-lucide="check-circle-2" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="mt-3">
                <div class="text-2xl sm:text-3xl font-black text-slate-900 font-mono tracking-tight">{{ $completedJobsCount }}</div>
                <div class="flex items-center space-x-1.5 mt-1 text-[11px] text-emerald-700 font-medium">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    <span>{{ __('Delivered & verified') }}</span>
                </div>
            </div>
        </div>

        <!-- Metric 4: Rating & Reviews -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-xs hover:border-amber-400 hover:shadow-sm transition group">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">{{ __('Artisan Rating') }}</span>
                <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-700 border border-amber-200/80 flex items-center justify-center transition group-hover:scale-105">
                    <i data-lucide="star" class="w-4 h-4 fill-amber-400 text-amber-400"></i>
                </div>
            </div>
            <div class="mt-3">
                <div class="text-2xl sm:text-3xl font-black text-slate-900 font-mono tracking-tight">
                    {{ ($profile->average_rating ?? 0) > 0 ? number_format($profile->average_rating, 1) : '5.0' }}
                </div>
                <div class="flex items-center space-x-1.5 mt-1 text-[11px] text-slate-500 font-medium">
                    <i data-lucide="star" class="w-3 h-3 text-amber-400 fill-amber-400"></i>
                    <span>{{ $profile->total_reviews ?? 0 }} {{ __('verified client reviews') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 4. Main 2-Column Split Layout (8 Col Left / 4 Col Right) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- LEFT COLUMN (8 cols): Incoming Requests & Active Jobs -->
        <div class="lg:col-span-8 space-y-6">
            
            <!-- Card 1: Incoming Job Requests -->
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="px-6 py-4.5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <div>
                        <h2 class="text-sm sm:text-base font-bold text-slate-900">{{ __('Incoming Job Requests') }}</h2>
                        <p class="text-[11px] text-slate-500">{{ __('Review client requirements and submit formal quotations') }}</p>
                    </div>
                    <a href="{{ route('technician.requests.index') }}" class="text-xs font-bold text-teal-700 hover:text-teal-800 transition flex items-center space-x-1">
                        <span>{{ __('View all') }}</span>
                        <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                    </a>
                </div>

                @if(isset($recentRequests) && count($recentRequests) > 0)
                    <div class="divide-y divide-slate-100">
                        @foreach($recentRequests as $req)
                            <div class="p-5 hover:bg-slate-50/80 transition flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <div class="space-y-1.5 flex-1 min-w-0">
                                    <div class="flex items-center space-x-2">
                                        <span class="text-[11px] font-mono font-bold text-teal-800 bg-teal-50 px-2 py-0.5 rounded-md border border-teal-200">
                                            {{ $req->reference_no ?? ('REQ-' . str_pad($req->id, 6, '0', STR_PAD_LEFT)) }}
                                        </span>
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $req->status_badge_classes }}">
                                            {{ $req->status_label }}
                                        </span>
                                    </div>
                                    <h3 class="text-sm font-bold text-slate-900 truncate">
                                        {{ $req->service->name }} {{ __('from') }} {{ $req->client->full_name }}
                                    </h3>
                                    <p class="text-xs text-slate-500 line-clamp-1">{{ $req->description }}</p>
                                    <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-slate-400 pt-0.5">
                                        <span><i data-lucide="map-pin" class="w-3 h-3 inline mr-1 text-slate-400"></i>{{ $req->location }}</span>
                                        <span>&bull;</span>
                                        <span>{{ $req->created_at ? $req->created_at->diffForHumans() : __('Recently') }}</span>
                                    </div>
                                </div>

                                <div class="flex items-center space-x-2 flex-shrink-0">
                                    <a href="{{ route('technician.requests.show', $req->id) }}" class="btn-tap px-3.5 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs shadow-xs transition flex items-center space-x-1.5">
                                        <span>{{ __('Review & Quote') }}</span>
                                        <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-8 text-center space-y-3">
                        <div class="w-12 h-12 rounded-2xl bg-slate-50 text-slate-400 flex items-center justify-center mx-auto border border-slate-200">
                            <i data-lucide="inbox" class="w-6 h-6"></i>
                        </div>
                        <div class="space-y-1">
                            <h4 class="text-sm font-bold text-slate-900">{{ __('No pending requests right now') }}</h4>
                            <p class="text-xs text-slate-500 max-w-sm mx-auto">{{ __('Ensure your subscription is active and your availability is set to Available to receive client matches.') }}</p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Card 2: Active Job Stage Execution -->
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="px-6 py-4.5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <div>
                        <h2 class="text-sm sm:text-base font-bold text-slate-900">{{ __('Active Job Execution Pipeline') }}</h2>
                        <p class="text-[11px] text-slate-500">{{ __('Live stage progression for scheduled and in-progress jobs') }}</p>
                    </div>
                    <a href="{{ route('technician.jobs.index') }}" class="text-xs font-bold text-teal-700 hover:text-teal-800 transition flex items-center space-x-1">
                        <span>{{ __('All active jobs') }}</span>
                        <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                    </a>
                </div>

                @if(isset($upcomingJobs) && count($upcomingJobs) > 0)
                    <div class="divide-y divide-slate-100">
                        @foreach($upcomingJobs as $jobReq)
                            <div class="p-5 space-y-3.5">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                    <div>
                                        <div class="flex items-center space-x-2">
                                            <span class="text-xs font-mono font-bold text-slate-600">{{ $jobReq->reference_no }}</span>
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $jobReq->status_badge_classes }}">
                                                {{ $jobReq->status_label }}
                                            </span>
                                        </div>
                                        <h3 class="text-sm font-bold text-slate-900 mt-1">{{ $jobReq->service->name }}</h3>
                                        <p class="text-xs text-slate-500">{{ __('Client:') }} <strong class="text-slate-800">{{ $jobReq->client->full_name }}</strong> &bull; {{ $jobReq->location }}</p>
                                    </div>

                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('messages.index', ['request_id' => $jobReq->id]) }}" class="btn-tap px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition flex items-center space-x-1">
                                            <i data-lucide="message-square" class="w-3.5 h-3.5"></i>
                                            <span>{{ __('Chat') }}</span>
                                        </a>
                                        <a href="{{ route('technician.requests.show', $jobReq->id) }}" class="btn-tap px-3 py-1.5 rounded-xl bg-teal-50 hover:bg-teal-600 hover:text-white text-teal-700 font-bold text-xs transition border border-teal-200">
                                            {{ __('Details') }} &rarr;
                                        </a>
                                    </div>
                                </div>

                                <!-- Job Stage Update Buttons -->
                                <div class="grid grid-cols-3 gap-2 pt-2 border-t border-slate-100">
                                    <form method="POST" action="{{ route('technician.jobs.status', $jobReq->id) }}">
                                        @csrf
                                        <input type="hidden" name="status" value="on_the_way">
                                        <button type="submit" {{ $jobReq->status === 'on_the_way' ? 'disabled' : '' }} class="btn-tap w-full py-2 rounded-xl text-center text-xs font-bold transition {{ $jobReq->status === 'on_the_way' ? 'bg-teal-700 text-white' : 'bg-teal-50 text-teal-800 hover:bg-teal-100 border border-teal-200' }}">
                                            {{ __('On The Way') }}
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('technician.jobs.status', $jobReq->id) }}">
                                        @csrf
                                        <input type="hidden" name="status" value="in_progress">
                                        <button type="submit" {{ $jobReq->status === 'in_progress' ? 'disabled' : '' }} class="btn-tap w-full py-2 rounded-xl text-center text-xs font-bold transition {{ $jobReq->status === 'in_progress' ? 'bg-blue-700 text-white' : 'bg-blue-50 text-blue-800 hover:bg-blue-100 border border-blue-200' }}">
                                            {{ __('At Work') }}
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('technician.jobs.status', $jobReq->id) }}">
                                        @csrf
                                        <input type="hidden" name="status" value="completed">
                                        <button type="submit" {{ $jobReq->status === 'completed' ? 'disabled' : '' }} class="btn-tap w-full py-2 rounded-xl text-center text-xs font-bold transition {{ $jobReq->status === 'completed' ? 'bg-emerald-700 text-white' : 'bg-emerald-50 text-emerald-800 hover:bg-emerald-100 border border-emerald-200' }}">
                                            {{ __('Complete') }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-8 text-center text-xs text-slate-400">
                        {{ __('No active jobs in execution right now.') }}
                    </div>
                @endif
            </div>

        </div>

        <!-- RIGHT COLUMN (4 cols): Quick Actions & Artisan Standards -->
        <div class="lg:col-span-4 space-y-6">
            
            <!-- Quick Actions Stack -->
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs p-6 space-y-3">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 px-1">{{ __('Quick Actions') }}</h3>

                <div class="space-y-2">
                    <a href="{{ route('technician.requests.index') }}" class="btn-tap flex items-center justify-between p-3.5 rounded-2xl bg-slate-50/80 hover:bg-teal-50/70 border border-slate-200/70 hover:border-teal-200 transition group">
                        <div class="flex items-center space-x-3">
                            <div class="w-9 h-9 rounded-xl bg-teal-600 text-white flex items-center justify-center flex-shrink-0 shadow-xs">
                                <i data-lucide="inbox" class="w-4.5 h-4.5"></i>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-slate-900 group-hover:text-teal-700 transition">{{ __('Job Requests') }}</h4>
                                <p class="text-[11px] text-slate-500">{{ __('View customer leads') }}</p>
                            </div>
                        </div>
                        <i data-lucide="chevron-right" class="w-4 h-4 text-slate-400 group-hover:text-teal-600 transition"></i>
                    </a>

                    <a href="{{ route('technician.subscription') }}" class="btn-tap flex items-center justify-between p-3.5 rounded-2xl bg-slate-50/80 hover:bg-teal-50/70 border border-slate-200/70 hover:border-teal-200 transition group">
                        <div class="flex items-center space-x-3">
                            <div class="w-9 h-9 rounded-xl bg-slate-900 text-teal-400 flex items-center justify-center flex-shrink-0 shadow-xs">
                                <i data-lucide="credit-card" class="w-4.5 h-4.5"></i>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-slate-900 group-hover:text-teal-700 transition">{{ __('Subscription Plan') }}</h4>
                                <p class="text-[11px] text-slate-500">{{ __('Manage active membership') }}</p>
                            </div>
                        </div>
                        <i data-lucide="chevron-right" class="w-4 h-4 text-slate-400 group-hover:text-teal-600 transition"></i>
                    </a>

                    <a href="{{ route('technician.portfolios.index') }}" class="btn-tap flex items-center justify-between p-3.5 rounded-2xl bg-slate-50/80 hover:bg-teal-50/70 border border-slate-200/70 hover:border-teal-200 transition group">
                        <div class="flex items-center space-x-3">
                            <div class="w-9 h-9 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center flex-shrink-0 shadow-xs">
                                <i data-lucide="image" class="w-4.5 h-4.5"></i>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-slate-900 group-hover:text-teal-700 transition">{{ __('Work Portfolio') }}</h4>
                                <p class="text-[11px] text-slate-500">{{ __('Showcase recent jobs') }}</p>
                            </div>
                        </div>
                        <i data-lucide="chevron-right" class="w-4 h-4 text-slate-400 group-hover:text-teal-600 transition"></i>
                    </a>

                    <a href="{{ route('technician.reviews.index') }}" class="btn-tap flex items-center justify-between p-3.5 rounded-2xl bg-slate-50/80 hover:bg-teal-50/70 border border-slate-200/70 hover:border-teal-200 transition group">
                        <div class="flex items-center space-x-3">
                            <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-700 border border-amber-200 flex items-center justify-center flex-shrink-0 shadow-xs">
                                <i data-lucide="star" class="w-4.5 h-4.5"></i>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-slate-900 group-hover:text-teal-700 transition">{{ __('Client Reviews') }}</h4>
                                <p class="text-[11px] text-slate-500">{{ __('Ratings and feedback') }}</p>
                            </div>
                        </div>
                        <i data-lucide="chevron-right" class="w-4 h-4 text-slate-400 group-hover:text-teal-600 transition"></i>
                    </a>

                    <a href="{{ route('messages.index') }}" class="btn-tap flex items-center justify-between p-3.5 rounded-2xl bg-slate-50/80 hover:bg-teal-50/70 border border-slate-200/70 hover:border-teal-200 transition group">
                        <div class="flex items-center space-x-3">
                            <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-700 border border-blue-200 flex items-center justify-center flex-shrink-0 shadow-xs">
                                <i data-lucide="message-square" class="w-4.5 h-4.5"></i>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-slate-900 group-hover:text-teal-700 transition">{{ __('Messages') }}</h4>
                                <p class="text-[11px] text-slate-500">{{ __('Direct customer communication') }}</p>
                            </div>
                        </div>
                        <i data-lucide="chevron-right" class="w-4 h-4 text-slate-400 group-hover:text-teal-600 transition"></i>
                    </a>
                </div>
            </div>

            <!-- Artisan Rules & Guarantee Card -->
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs p-6 space-y-4">
                <div class="flex items-center space-x-3">
                    <div class="w-9 h-9 rounded-xl bg-teal-50 text-teal-700 border border-teal-200 flex items-center justify-center flex-shrink-0">
                        <i data-lucide="award" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider">{{ __('Fundi Direct Standards') }}</h3>
                        <p class="text-[11px] text-slate-500">{{ __('Professional service etiquette') }}</p>
                    </div>
                </div>

                <div class="space-y-3 text-xs text-slate-600 pt-2 border-t border-slate-100">
                    <div class="flex items-start space-x-2.5">
                        <i data-lucide="check" class="w-4 h-4 text-emerald-600 flex-shrink-0 mt-0.5"></i>
                        <span class="text-[11px] leading-relaxed"><strong>{{ __('Fast Response:') }}</strong> {{ __('Respond to customer quotes within 15 minutes.') }}</span>
                    </div>
                    <div class="flex items-start space-x-2.5">
                        <i data-lucide="check" class="w-4 h-4 text-emerald-600 flex-shrink-0 mt-0.5"></i>
                        <span class="text-[11px] leading-relaxed"><strong>{{ __('100% Retained Income:') }}</strong> {{ __('Keep 100% of your labour fee. No job commission.') }}</span>
                    </div>
                    <div class="flex items-start space-x-2.5">
                        <i data-lucide="check" class="w-4 h-4 text-emerald-600 flex-shrink-0 mt-0.5"></i>
                        <span class="text-[11px] leading-relaxed"><strong>{{ __('Customer Satisfaction:') }}</strong> {{ __('High ratings keep you ranked top in directory search.') }}</span>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection
