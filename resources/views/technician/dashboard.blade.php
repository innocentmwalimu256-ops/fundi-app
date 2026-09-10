@extends('layouts.app')

@section('title', __('Dashboard') . ' — FUNDI')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

    <!-- 1. Top Greeting & Availability Card (Clean, Smart Header) -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            
            <div class="flex items-start sm:items-center space-x-4">
                <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-slate-900 text-teal-300 font-black text-xl flex items-center justify-center flex-shrink-0 shadow-xs">
                    {{ $technician->initials }}
                </div>

                <div class="space-y-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">
                            {{ __('Welcome back') }}, {{ $technician->full_name }} 👋
                        </h1>
                        <span class="inline-flex items-center space-x-1 px-2.5 py-0.5 rounded-full bg-teal-50 text-teal-700 text-xs font-bold border border-teal-200">
                            <i data-lucide="shield-check" class="w-3.5 h-3.5"></i>
                            <span>{{ __('Verified Fundi') }}</span>
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm text-slate-500 font-medium">
                        {{ __($profile->professional_title ?? 'Master Technician') }} • <i data-lucide="map-pin" class="w-3.5 h-3.5 inline text-slate-400 -mt-0.5"></i> {{ $profile->location ?? 'Dar es Salaam' }}
                    </p>
                </div>
            </div>

            <!-- Availability Status Capsule -->
            <div class="flex items-center justify-between sm:justify-end space-x-4 bg-slate-50 p-3.5 rounded-2xl border border-slate-200/80 self-stretch lg:self-auto">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">{{ __('Your Availability') }}</span>
                    <div class="flex items-center space-x-1.5 mt-0.5">
                        <span class="relative flex h-2.5 w-2.5">
                            @if(($profile->availability_status ?? 'available') === 'available')
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                            @else
                                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-amber-500"></span>
                            @endif
                        </span>
                        <span class="text-xs font-bold {{ ($profile->availability_status ?? 'available') === 'available' ? 'text-emerald-700' : 'text-amber-700' }}">
                            {{ ($profile->availability_status ?? 'available') === 'available' ? __('Online (Available)') : __('Busy (Unavailable)') }}
                        </span>
                    </div>
                </div>
                <a href="{{ route('technician.availability') }}" class="px-3.5 py-2 rounded-xl bg-white hover:bg-slate-100 text-slate-800 text-xs font-bold border border-slate-200 transition shadow-xs">
                    {{ __('Change') }}
                </a>
            </div>

        </div>
    </div>

    <!-- 2. Subscription Status Alert / Banner -->
    @if(!$subscription || !$subscription->isActive())
    <div class="bg-gradient-to-r from-amber-500 via-amber-600 to-teal-700 rounded-3xl p-6 text-white shadow-lg flex flex-col md:flex-row md:items-center justify-between gap-5">
        <div class="flex items-start sm:items-center space-x-4">
            <div class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur-md text-white flex items-center justify-center font-bold flex-shrink-0">
                <i data-lucide="lock" class="w-6 h-6"></i>
            </div>
            <div>
                <div class="flex items-center space-x-2">
                    <span class="px-2.5 py-0.5 rounded-full bg-white/20 text-white text-[10px] font-black uppercase tracking-wider">
                        {{ __('Subscription Required') }}
                    </span>
                </div>
                <h3 class="text-base sm:text-lg font-black text-white mt-1">{{ __('Clients cannot see you until you activate your subscription') }}</h3>
                <p class="text-xs text-white/90 mt-0.5 max-w-xl">
                    {{ __('Subscribe from TZS 10,000 to activate your profile online, receive job requests, and unlock WhatsApp contacts.') }}
                </p>
            </div>
        </div>
        <a href="{{ route('technician.subscription') }}" class="px-6 py-3.5 rounded-2xl bg-slate-950 hover:bg-slate-900 text-white font-black text-xs uppercase tracking-wider shadow-xl transition text-center flex items-center justify-center space-x-2 flex-shrink-0">
            <span>{{ __('Activate Subscription Now') }}</span>
            <i data-lucide="arrow-right" class="w-4 h-4"></i>
        </a>
    </div>
    @else
    <!-- Active Subscription Status Banner -->
    <div class="bg-white rounded-2xl p-5 border border-emerald-200 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center space-x-3.5">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold flex-shrink-0 border border-emerald-200">
                <i data-lucide="check-circle-2" class="w-5 h-5"></i>
            </div>
            <div>
                <div class="flex items-center space-x-2">
                    <span class="text-xs font-bold text-slate-900">{{ $subscription->plan ? $subscription->plan->name : __('Active') }} {{ __('Plan') }}</span>
                    <span class="px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 text-[10px] font-bold border border-emerald-200">
                        {{ __('ACTIVE') }} ● {{ __('DAYS REMAINING') }}: {{ $subscription->days_remaining }}
                    </span>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">{{ __('Your profile is live and clients can send you service requests.') }}</p>
            </div>
        </div>
        <a href="{{ route('technician.subscription') }}" class="text-xs font-bold text-teal-700 hover:text-teal-800 transition">
            {{ __('Manage Plan') }} &rarr;
        </a>
    </div>
    @endif

    <!-- 3. KPI Telemetry Metric Cards (4 Cards) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- New Requests -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">{{ __('New Requests') }}</p>
                <h3 class="text-2xl sm:text-3xl font-black text-amber-600 mt-1">{{ $newRequestsCount }}</h3>
                <span class="text-[11px] text-slate-400 mt-0.5 block">{{ __('Awaiting quotation') }}</span>
            </div>
            <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center border border-amber-200/60">
                <i data-lucide="inbox" class="w-5 h-5"></i>
            </div>
        </div>

        <!-- Active Jobs -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">{{ __('Ongoing Jobs') }}</p>
                <h3 class="text-2xl sm:text-3xl font-black text-teal-600 mt-1">{{ $activeJobsCount }}</h3>
                <span class="text-[11px] text-slate-400 mt-0.5 block">{{ __('In Progress') }}</span>
            </div>
            <div class="w-11 h-11 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center border border-teal-200/60">
                <i data-lucide="wrench" class="w-5 h-5"></i>
            </div>
        </div>

        <!-- Completed Jobs -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">{{ __('Completed Jobs') }}</p>
                <h3 class="text-2xl sm:text-3xl font-black text-emerald-600 mt-1">{{ $completedJobsCount }}</h3>
                <span class="text-[11px] text-slate-400 mt-0.5 block">{{ __('Confirmed finished jobs') }}</span>
            </div>
            <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-200/60">
                <i data-lucide="check-circle" class="w-5 h-5"></i>
            </div>
        </div>

        <!-- Average Rating -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">{{ __('Average Rating') }}</p>
                @if(($profile->average_rating ?? 0) > 0)
                    <h3 class="text-2xl sm:text-3xl font-black text-slate-900 mt-1 flex items-center">
                        <span class="text-amber-500 mr-1 text-xl">★</span> {{ number_format($profile->average_rating, 1) }}
                    </h3>
                    <span class="text-[11px] text-slate-400 mt-0.5 block">
                        @if(($profile->total_reviews ?? 0) > 0)
                            {{ $profile->total_reviews }} {{ __('client reviews') }}
                        @else
                            {{ __('Subscription Plan Rating') }} ({{ optional($subscription ?? auth()->user()->subscription)->plan->name ?? __('Active Plan') }})
                        @endif
                    </span>
                @else
                    <h3 class="text-2xl sm:text-3xl font-black text-slate-400 mt-1 flex items-center">
                        <span class="text-slate-300 mr-1 text-xl">★</span> --
                    </h3>
                    <span class="text-[11px] text-amber-700 font-bold mt-0.5 block">{{ __('No Active Subscription') }}</span>
                @endif
            </div>
            <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center border border-amber-200/60">
                <i data-lucide="star" class="w-5 h-5 {{ ($profile->average_rating ?? 0) > 0 ? 'fill-amber-400 text-amber-400' : 'text-slate-300 stroke-slate-300' }}"></i>
            </div>
        </div>

    </div>

    <!-- 4. Main Two-Column Workflow Dashboard -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Left 7 Cols: Incoming Requests & Action Queue -->
        <div class="lg:col-span-7 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-base sm:text-lg font-black text-slate-900 tracking-tight">{{ __('Incoming Job Requests') }}</h2>
                    <p class="text-xs text-slate-500">{{ __('Review client issues, accept requests, and send quotations') }}</p>
                </div>
                <a href="{{ route('technician.requests.index') }}" class="text-xs font-bold text-teal-700 hover:text-teal-800 flex items-center">
                    <span>{{ __('View all') }}</span>
                    <i data-lucide="chevron-right" class="w-4 h-4 ml-0.5"></i>
                </a>
            </div>

            <div class="space-y-3">
                @forelse($recentRequests as $req)
                <div class="bg-white rounded-2xl p-5 border border-slate-200 hover:border-teal-500 hover:shadow-card transition flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex items-start space-x-3.5">
                        <div class="w-11 h-11 rounded-xl bg-slate-900 text-teal-300 font-bold text-xs flex items-center justify-center flex-shrink-0">
                            {{ $req->client->initials }}
                        </div>
                        <div class="space-y-1 min-w-0">
                            <div class="flex items-center space-x-2">
                                <span class="text-xs font-bold text-slate-900 font-mono">{{ $req->reference_no }}</span>
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold border {{ $req->status_badge_classes }}">
                                    {{ $req->status_label }}
                                </span>
                            </div>
                            <h4 class="text-sm font-bold text-slate-900 truncate">
                                {{ $req->service->name }} {{ __('from') }} {{ $req->client->full_name }}
                            </h4>
                            <p class="text-xs text-slate-500 line-clamp-1">{{ $req->description }}</p>
                            <div class="flex items-center space-x-3 text-xs text-slate-400 pt-0.5">
                                <span><i data-lucide="map-pin" class="w-3.5 h-3.5 inline mr-0.5"></i> {{ $req->location }}</span>
                                <span><i data-lucide="calendar" class="w-3.5 h-3.5 inline mr-0.5"></i> {{ $req->preferred_date->format('d M Y') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center space-x-2 sm:self-center flex-shrink-0 pt-2 sm:pt-0 border-t sm:border-t-0 border-slate-100">
                        <a href="{{ route('technician.requests.show', $req->id) }}" class="w-full sm:w-auto px-4 py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-xl shadow-xs transition text-center">
                            {{ __('Review & Quote') }} &rarr;
                        </a>
                    </div>
                </div>
                @empty
                <div class="p-8 text-center bg-white rounded-2xl border border-slate-200 text-slate-400 text-xs space-y-2">
                    <i data-lucide="inbox" class="w-8 h-8 mx-auto text-slate-300"></i>
                    <p class="font-bold text-slate-700">{{ __('No new requests at the moment') }}</p>
                    <p class="text-[11px] text-slate-400 max-w-sm mx-auto">
                        {{ __('Ensure your subscription is active and availability is set to Available to receive job requests.') }}
                    </p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Right 5 Cols: Active Jobs & Live Execution -->
        <div class="lg:col-span-5 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-base sm:text-lg font-black text-slate-900 tracking-tight">{{ __('Ongoing Jobs') }}</h2>
                    <p class="text-xs text-slate-500">{{ __('Update your job stages when departing, arriving, or completing work') }}</p>
                </div>
                <a href="{{ route('technician.jobs.index') }}" class="text-xs font-bold text-teal-700 hover:text-teal-800 flex items-center">
                    <span>{{ __('View active jobs') }}</span>
                    <i data-lucide="chevron-right" class="w-4 h-4 ml-0.5"></i>
                </a>
            </div>

            <div class="space-y-3">
                @forelse($upcomingJobs as $jobReq)
                <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs space-y-4">
                    <div class="flex items-start justify-between">
                        <div>
                            <span class="text-xs font-bold text-slate-400 font-mono">{{ $jobReq->reference_no }}</span>
                            <h3 class="text-sm font-bold text-slate-900 mt-0.5">{{ $jobReq->service->name }}</h3>
                            <p class="text-xs text-slate-600">{{ __('Client') }}: <strong>{{ $jobReq->client->full_name }}</strong></p>
                            <p class="text-xs text-slate-400 mt-0.5"><i data-lucide="map-pin" class="w-3.5 h-3.5 inline mr-0.5 text-slate-400"></i>{{ $jobReq->location }}</p>
                        </div>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $jobReq->status_badge_classes }}">
                            {{ $jobReq->status_label }}
                        </span>
                    </div>

                    <!-- One-Tap Status Update Actions -->
                    <div class="pt-3 border-t border-slate-100 space-y-1.5">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ __('Update Job Stage') }}:</p>
                        <div class="grid grid-cols-3 gap-1.5 text-center">
                            <form method="POST" action="{{ route('technician.jobs.status', $jobReq->id) }}">
                                @csrf
                                <input type="hidden" name="status" value="on_the_way">
                                <button type="submit" {{ $jobReq->status === 'on_the_way' ? 'disabled' : '' }} class="w-full py-2 px-1 rounded-xl text-center text-[11px] font-bold transition {{ $jobReq->status === 'on_the_way' ? 'bg-teal-700 text-white' : 'bg-teal-50 text-teal-800 hover:bg-teal-100 border border-teal-200' }}">
                                    {{ __('On The Way') }}
                                </button>
                            </form>

                            <form method="POST" action="{{ route('technician.jobs.status', $jobReq->id) }}">
                                @csrf
                                <input type="hidden" name="status" value="in_progress">
                                <button type="submit" {{ $jobReq->status === 'in_progress' ? 'disabled' : '' }} class="w-full py-2 px-1 rounded-xl text-center text-[11px] font-bold transition {{ $jobReq->status === 'in_progress' ? 'bg-blue-700 text-white' : 'bg-blue-50 text-blue-800 hover:bg-blue-100 border border-blue-200' }}">
                                    {{ __('At Work') }}
                                </button>
                            </form>

                            <form method="POST" action="{{ route('technician.jobs.status', $jobReq->id) }}">
                                @csrf
                                <input type="hidden" name="status" value="completed">
                                <button type="submit" {{ $jobReq->status === 'completed' ? 'disabled' : '' }} class="w-full py-2 px-1 rounded-xl text-center text-[11px] font-bold transition {{ $jobReq->status === 'completed' ? 'bg-emerald-700 text-white' : 'bg-emerald-50 text-emerald-800 hover:bg-emerald-100 border border-emerald-200' }}">
                                    {{ __('Complete Job') }}
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="flex items-center justify-between text-xs pt-1 border-t border-slate-100">
                        <a href="{{ route('technician.requests.show', $jobReq->id) }}" class="text-teal-700 font-bold hover:underline">
                            {{ __('Job Details') }} &rarr;
                        </a>
                        <a href="{{ route('messages.index', ['request_id' => $jobReq->id]) }}" class="text-slate-500 hover:text-slate-800 flex items-center font-medium">
                            <i data-lucide="message-square" class="w-3.5 h-3.5 mr-1"></i> {{ __('Messages') }}
                        </a>
                    </div>
                </div>
                @empty
                <div class="p-8 text-center bg-white rounded-2xl border border-slate-200 text-slate-400 text-xs space-y-1">
                    <i data-lucide="briefcase" class="w-8 h-8 mx-auto text-slate-300"></i>
                    <p class="font-bold text-slate-700">{{ __('No active jobs at the moment') }}</p>
                    <p class="text-[11px] text-slate-400">{{ __('Accepted jobs will appear here in real-time.') }}</p>
                </div>
                @endforelse
            </div>
        </div>

    </div>

</div>
@endsection
