@extends('layouts.app')

@section('title', __('Dashboard') . ' — FUNDI')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    <!-- 1. Top Section Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-200/80">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2.5 py-0.5 rounded-md bg-teal-50 text-teal-700 text-[11px] font-bold border border-teal-200 uppercase tracking-wider">
                    {{ __('Technician Workspace') }}
                </span>
                <span class="px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 text-[10px] font-bold border border-emerald-200 flex items-center">
                    <i data-lucide="check" class="w-3 h-3 mr-0.5"></i> {{ __('Verified Specialist') }}
                </span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight mt-1.5">
                {{ __('Dashboard') }}
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 font-medium mt-0.5">
                {{ __($profile->professional_title ?? 'Master Technician') }} &bull; {{ $profile->location ?? 'Dar es Salaam' }} &bull; {{ __('Manage jobs, send quotations, and track earnings.') }}
            </p>
        </div>

        <div class="flex items-center space-x-3 flex-shrink-0">
            <!-- Availability Toggle Button -->
            <a href="{{ route('technician.availability') }}" class="btn-tap px-3.5 py-2 rounded-xl bg-white hover:bg-slate-50 text-slate-800 text-xs font-bold border border-slate-200 shadow-xs transition flex items-center space-x-2">
                <span class="relative flex h-2.5 w-2.5">
                    @if(($profile->availability_status ?? 'available') === 'available')
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                    @else
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-amber-500"></span>
                    @endif
                </span>
                <span>{{ ($profile->availability_status ?? 'available') === 'available' ? __('Online (Available)') : __('Busy (Unavailable)') }}</span>
            </a>

            @if(!$subscription || !$subscription->isActive())
                <a href="{{ route('technician.subscription') }}" class="btn-tap px-4 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs shadow-xs transition flex items-center space-x-1.5">
                    <i data-lucide="lock" class="w-3.5 h-3.5"></i>
                    <span>{{ __('Activate Plan') }}</span>
                </a>
            @endif
        </div>
    </div>

    <!-- 2. Subscription Alert if Inactive -->
    @if(!$subscription || !$subscription->isActive())
    <div class="bg-gradient-to-r from-amber-600 to-teal-700 rounded-2xl p-5 text-white shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center space-x-3.5">
            <div class="w-10 h-10 rounded-xl bg-white/20 backdrop-blur-md text-white flex items-center justify-center font-bold flex-shrink-0">
                <i data-lucide="lock" class="w-5 h-5"></i>
            </div>
            <div>
                <h3 class="text-sm sm:text-base font-bold text-white">{{ __('Subscription Required to Receive Direct Leads') }}</h3>
                <p class="text-xs text-white/90 mt-0.5">
                    {{ __('Subscribe to activate your profile online, receive job requests, and unlock direct WhatsApp connections.') }}
                </p>
            </div>
        </div>
        <a href="{{ route('technician.subscription') }}" class="btn-tap px-4 py-2.5 rounded-xl bg-slate-950 hover:bg-slate-900 text-white font-bold text-xs shadow-md transition text-center flex items-center justify-center space-x-2 flex-shrink-0">
            <span>{{ __('Activate Now') }}</span>
            <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
        </a>
    </div>
    @endif

    <!-- 3. 4 Metric KPI Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Metric 1: Incoming Requests -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-xs hover:border-teal-300 transition flex flex-col justify-between space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('Incoming Requests') }}</span>
                <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-700 border border-amber-200 flex items-center justify-center">
                    <i data-lucide="inbox" class="w-4 h-4"></i>
                </div>
            </div>
            <div>
                <div class="text-2xl sm:text-3xl font-black text-slate-900 font-mono">{{ $newRequestsCount }}</div>
                <p class="text-[11px] text-slate-400 font-medium mt-0.5">{{ __('Awaiting quotation') }}</p>
            </div>
        </div>

        <!-- Metric 2: Ongoing Jobs -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-xs hover:border-teal-300 transition flex flex-col justify-between space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('Ongoing Jobs') }}</span>
                <div class="w-9 h-9 rounded-xl bg-teal-50 text-teal-700 border border-teal-200 flex items-center justify-center">
                    <i data-lucide="activity" class="w-4 h-4"></i>
                </div>
            </div>
            <div>
                <div class="text-2xl sm:text-3xl font-black text-slate-900 font-mono">{{ $activeJobsCount }}</div>
                <p class="text-[11px] text-slate-400 font-medium mt-0.5">{{ __('Currently in execution') }}</p>
            </div>
        </div>

        <!-- Metric 3: Completed Jobs -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-xs hover:border-teal-300 transition flex flex-col justify-between space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('Completed') }}</span>
                <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center justify-center">
                    <i data-lucide="check-circle-2" class="w-4 h-4"></i>
                </div>
            </div>
            <div>
                <div class="text-2xl sm:text-3xl font-black text-slate-900 font-mono">{{ $completedJobsCount }}</div>
                <p class="text-[11px] text-slate-400 font-medium mt-0.5">{{ __('Successfully delivered') }}</p>
            </div>
        </div>

        <!-- Metric 4: Rating & Reviews -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-xs hover:border-teal-300 transition flex flex-col justify-between space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('Artisan Rating') }}</span>
                <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-700 border border-amber-200 flex items-center justify-center">
                    <i data-lucide="star" class="w-4 h-4 fill-amber-400 text-amber-400"></i>
                </div>
            </div>
            <div>
                <div class="text-2xl sm:text-3xl font-black text-slate-900 font-mono">
                    {{ ($profile->average_rating ?? 0) > 0 ? number_format($profile->average_rating, 1) : '5.0' }}
                </div>
                <p class="text-[11px] text-slate-400 font-medium mt-0.5">
                    {{ $profile->total_reviews ?? 0 }} {{ __('verified reviews') }}
                </p>
            </div>
        </div>
    </div>

    <!-- 4. Main 2-Column Split Layout (8 Col Left / 4 Col Right) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- LEFT COLUMN (8 cols): Incoming Requests & Ongoing Jobs -->
        <div class="lg:col-span-8 space-y-6">
            
            <!-- Card: Incoming Job Requests -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">{{ __('Incoming Job Requests') }}</h2>
                        <p class="text-[11px] text-slate-500">{{ __('Review client requirements and submit quotations') }}</p>
                    </div>
                    <a href="{{ route('technician.requests.index') }}" class="text-xs font-bold text-teal-700 hover:text-teal-800 transition flex items-center space-x-1">
                        <span>{{ __('View all') }}</span>
                        <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                    </a>
                </div>

                @if(isset($recentRequests) && count($recentRequests) > 0)
                    <div class="divide-y divide-slate-100">
                        @foreach($recentRequests as $req)
                            <div class="p-5 hover:bg-slate-50/70 transition flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <div class="space-y-1.5 flex-1 min-w-0">
                                    <div class="flex items-center space-x-2">
                                        <span class="text-[11px] font-mono font-bold text-teal-700 bg-teal-50 px-2 py-0.5 rounded border border-teal-200">
                                            {{ $req->reference_no ?? ('REQ-' . str_pad($req->id, 6, '0', STR_PAD_LEFT)) }}
                                        </span>
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $req->status_badge_classes }}">
                                            {{ $req->status_label }}
                                        </span>
                                    </div>
                                    <h3 class="text-sm font-bold text-slate-900 truncate">
                                        {{ $req->service->name }} {{ __('from') }} {{ $req->client->full_name }}
                                    </h3>
                                    <p class="text-xs text-slate-500 line-clamp-1">{{ $req->description }}</p>
                                    <p class="text-[11px] text-slate-400 flex items-center space-x-3">
                                        <span><i data-lucide="map-pin" class="w-3 h-3 inline mr-1 text-slate-400"></i>{{ $req->location }}</span>
                                        <span>&bull;</span>
                                        <span>{{ $req->created_at ? $req->created_at->diffForHumans() : __('Recently') }}</span>
                                    </p>
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
                            <h4 class="text-sm font-bold text-slate-900">{{ __('No pending requests at the moment') }}</h4>
                            <p class="text-xs text-slate-500 max-w-sm mx-auto">{{ __('Ensure your subscription is active and your availability is set to Online to receive client matches.') }}</p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Card: Ongoing Jobs Stage Execution -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">{{ __('Active Job Execution') }}</h2>
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
                                        <a href="{{ route('messages.index', ['request_id' => $jobReq->id]) }}" class="btn-tap px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition flex items-center space-x-1">
                                            <i data-lucide="message-square" class="w-3.5 h-3.5"></i>
                                            <span>{{ __('Chat') }}</span>
                                        </a>
                                        <a href="{{ route('technician.requests.show', $jobReq->id) }}" class="btn-tap px-3 py-1.5 rounded-lg bg-teal-50 hover:bg-teal-600 hover:text-white text-teal-700 font-bold text-xs transition border border-teal-200">
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

        <!-- RIGHT COLUMN (4 cols): Quick Actions & Subscription info -->
        <div class="lg:col-span-4 space-y-6">
            
            <!-- Quick Actions Stack -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 space-y-3">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 px-1">{{ __('Quick Actions') }}</h3>

                <div class="space-y-1.5">
                    <a href="{{ route('technician.requests.index') }}" class="btn-tap flex items-center justify-between p-3 rounded-xl bg-slate-50/80 hover:bg-teal-50/70 border border-slate-200/70 hover:border-teal-200 transition group">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-lg bg-teal-600 text-white flex items-center justify-center flex-shrink-0">
                                <i data-lucide="inbox" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-slate-900 group-hover:text-teal-700 transition">{{ __('Job Requests') }}</h4>
                                <p class="text-[10px] text-slate-500">{{ __('View customer leads') }}</p>
                            </div>
                        </div>
                        <i data-lucide="chevron-right" class="w-4 h-4 text-slate-400 group-hover:text-teal-600 transition"></i>
                    </a>

                    <a href="{{ route('technician.subscription') }}" class="btn-tap flex items-center justify-between p-3 rounded-xl bg-slate-50/80 hover:bg-teal-50/70 border border-slate-200/70 hover:border-teal-200 transition group">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-lg bg-slate-900 text-teal-400 flex items-center justify-center flex-shrink-0">
                                <i data-lucide="credit-card" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-slate-900 group-hover:text-teal-700 transition">{{ __('Subscription Plan') }}</h4>
                                <p class="text-[10px] text-slate-500">{{ __('Manage active membership') }}</p>
                            </div>
                        </div>
                        <i data-lucide="chevron-right" class="w-4 h-4 text-slate-400 group-hover:text-teal-600 transition"></i>
                    </a>

                    <a href="{{ route('technician.portfolios.index') }}" class="btn-tap flex items-center justify-between p-3 rounded-xl bg-slate-50/80 hover:bg-teal-50/70 border border-slate-200/70 hover:border-teal-200 transition group">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center flex-shrink-0">
                                <i data-lucide="image" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-slate-900 group-hover:text-teal-700 transition">{{ __('Work Portfolio') }}</h4>
                                <p class="text-[10px] text-slate-500">{{ __('Showcase recent jobs') }}</p>
                            </div>
                        </div>
                        <i data-lucide="chevron-right" class="w-4 h-4 text-slate-400 group-hover:text-teal-600 transition"></i>
                    </a>

                    <a href="{{ route('technician.reviews.index') }}" class="btn-tap flex items-center justify-between p-3 rounded-xl bg-slate-50/80 hover:bg-teal-50/70 border border-slate-200/70 hover:border-teal-200 transition group">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-700 border border-amber-200 flex items-center justify-center flex-shrink-0">
                                <i data-lucide="star" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-slate-900 group-hover:text-teal-700 transition">{{ __('Client Reviews') }}</h4>
                                <p class="text-[10px] text-slate-500">{{ __('Ratings and feedback') }}</p>
                            </div>
                        </div>
                        <i data-lucide="chevron-right" class="w-4 h-4 text-slate-400 group-hover:text-teal-600 transition"></i>
                    </a>

                    <a href="{{ route('messages.index') }}" class="btn-tap flex items-center justify-between p-3 rounded-xl bg-slate-50/80 hover:bg-teal-50/70 border border-slate-200/70 hover:border-teal-200 transition group">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-700 border border-blue-200 flex items-center justify-center flex-shrink-0">
                                <i data-lucide="message-square" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-slate-900 group-hover:text-teal-700 transition">{{ __('Messages') }}</h4>
                                <p class="text-[10px] text-slate-500">{{ __('Direct customer communication') }}</p>
                            </div>
                        </div>
                        <i data-lucide="chevron-right" class="w-4 h-4 text-slate-400 group-hover:text-teal-600 transition"></i>
                    </a>
                </div>
            </div>

            <!-- Artisan Rules & Guarantee Card -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 space-y-4">
                <div class="flex items-center space-x-2.5">
                    <div class="w-8 h-8 rounded-lg bg-teal-50 text-teal-700 border border-teal-200 flex items-center justify-center flex-shrink-0">
                        <i data-lucide="award" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider">{{ __('Fundi Direct Standards') }}</h3>
                        <p class="text-[10px] text-slate-500">{{ __('Professional service etiquette') }}</p>
                    </div>
                </div>

                <div class="space-y-2.5 text-xs text-slate-600 pt-1 border-t border-slate-100">
                    <div class="flex items-start space-x-2.5">
                        <i data-lucide="check" class="w-4 h-4 text-emerald-600 flex-shrink-0 mt-0.5"></i>
                        <span class="text-[11px] leading-tight"><strong>{{ __('Fast Response:') }}</strong> {{ __('Respond to customer quotes within 15 minutes.') }}</span>
                    </div>
                    <div class="flex items-start space-x-2.5">
                        <i data-lucide="check" class="w-4 h-4 text-emerald-600 flex-shrink-0 mt-0.5"></i>
                        <span class="text-[11px] leading-tight"><strong>{{ __('100% Retained Income:') }}</strong> {{ __('Keep 100% of your labour fee. No job commission.') }}</span>
                    </div>
                    <div class="flex items-start space-x-2.5">
                        <i data-lucide="check" class="w-4 h-4 text-emerald-600 flex-shrink-0 mt-0.5"></i>
                        <span class="text-[11px] leading-tight"><strong>{{ __('Customer Satisfaction:') }}</strong> {{ __('High ratings keep you ranked top in directory search.') }}</span>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection
