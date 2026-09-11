@extends('layouts.app')

@section('title', __('Dashboard') . ' — FUNDI')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    @php
        $activeCount = \App\Models\ServiceRequest::where('client_id', $user->id)
            ->whereNotIn('status', ['completed', 'client_confirmed', 'reviewed', 'declined', 'cancelled'])
            ->count();
        $ongoingCount = \App\Models\ServiceRequest::where('client_id', $user->id)
            ->whereIn('status', ['accepted', 'scheduled', 'on_the_way', 'in_progress'])
            ->count();
        $completedCount = \App\Models\ServiceRequest::where('client_id', $user->id)
            ->whereIn('status', ['completed', 'client_confirmed', 'reviewed'])
            ->count();
        $savedCount = \App\Models\Favorite::where('user_id', $user->id)->count();
    @endphp

    <!-- 1. Executive Hero Welcome Banner -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-900 via-slate-800 to-teal-950 p-6 sm:p-8 text-white shadow-lg border border-slate-700/50">
        <!-- Subtle background glow -->
        <div class="absolute -right-16 -top-16 w-64 h-64 rounded-full bg-teal-500/10 blur-3xl pointer-events-none"></div>
        <div class="absolute -left-16 -bottom-16 w-64 h-64 rounded-full bg-blue-500/10 blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="space-y-2">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="px-2.5 py-0.5 rounded-full bg-teal-400/20 text-teal-300 text-[10px] font-black uppercase tracking-wider border border-teal-400/30 flex items-center">
                        <span class="w-1.5 h-1.5 rounded-full bg-teal-400 mr-1.5 animate-pulse"></span>
                        {{ __('Client Workspace') }}
                    </span>
                    <span class="text-xs text-slate-400">&bull;</span>
                    <span class="text-xs text-slate-300 font-medium flex items-center">
                        <i data-lucide="map-pin" class="w-3.5 h-3.5 text-teal-400 mr-1"></i> Dar es Salaam, TZ
                    </span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight">
                    {{ __('Welcome back') }}, {{ $user->first_name }}
                </h1>
                <p class="text-xs sm:text-sm text-slate-300 max-w-2xl font-normal leading-relaxed">
                    {{ __('Find certified local artisans across 12+ trades or post a service request to receive quotes within minutes.') }}
                </p>
            </div>

            <!-- Fast Action CTA Buttons -->
            <div class="flex flex-wrap items-center gap-3 flex-shrink-0">
                <a href="{{ route('client.requests.create') }}" class="btn-tap px-4 py-2.5 rounded-xl bg-teal-500 hover:bg-teal-400 text-slate-950 font-black text-xs shadow-lg shadow-teal-500/20 transition flex items-center space-x-2">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    <span>{{ __('Post Service Request') }}</span>
                </a>
                <a href="{{ route('client.technicians.index') }}" class="btn-tap px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs backdrop-blur-md border border-white/15 transition flex items-center space-x-2">
                    <i data-lucide="search" class="w-4 h-4 text-teal-300"></i>
                    <span>{{ __('Browse Directory') }}</span>
                </a>
            </div>
        </div>
    </div>

    <!-- 2. 4 Executive Telemetry KPI Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Metric 1: Active Requests -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-xs hover:border-teal-400 hover:shadow-sm transition group">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">{{ __('Active Requests') }}</span>
                <div class="w-9 h-9 rounded-xl bg-teal-50 text-teal-700 border border-teal-200/80 flex items-center justify-center transition group-hover:scale-105">
                    <i data-lucide="inbox" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="mt-3">
                <div class="text-2xl sm:text-3xl font-black text-slate-900 font-mono tracking-tight">{{ $activeCount }}</div>
                <div class="flex items-center space-x-1.5 mt-1 text-[11px] text-teal-700 font-medium">
                    <span class="w-1.5 h-1.5 rounded-full bg-teal-500"></span>
                    <span>{{ __('Live matching underway') }}</span>
                </div>
            </div>
        </div>

        <!-- Metric 2: Ongoing Jobs -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-xs hover:border-blue-400 hover:shadow-sm transition group">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">{{ __('Ongoing Jobs') }}</span>
                <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-700 border border-blue-200/80 flex items-center justify-center transition group-hover:scale-105">
                    <i data-lucide="activity" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="mt-3">
                <div class="text-2xl sm:text-3xl font-black text-slate-900 font-mono tracking-tight">{{ $ongoingCount }}</div>
                <div class="flex items-center space-x-1.5 mt-1 text-[11px] text-blue-700 font-medium">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                    <span>{{ __('Assigned & active') }}</span>
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
                <div class="text-2xl sm:text-3xl font-black text-slate-900 font-mono tracking-tight">{{ $completedCount }}</div>
                <div class="flex items-center space-x-1.5 mt-1 text-[11px] text-emerald-700 font-medium">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    <span>{{ __('Delivered successfully') }}</span>
                </div>
            </div>
        </div>

        <!-- Metric 4: Saved Specialists -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-xs hover:border-amber-400 hover:shadow-sm transition group">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">{{ __('Saved Fundis') }}</span>
                <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-700 border border-amber-200/80 flex items-center justify-center transition group-hover:scale-105">
                    <i data-lucide="bookmark" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="mt-3">
                <div class="text-2xl sm:text-3xl font-black text-slate-900 font-mono tracking-tight">{{ $savedCount }}</div>
                <div class="flex items-center space-x-1.5 mt-1 text-[11px] text-amber-700 font-medium">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                    <span>{{ __('Quick direct access') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Main Section: Requests, Categories & Verified Artisans (Full Width Clean Layout) -->
    <div class="space-y-6">
        
        <!-- Card 1: Recent Service Requests Feed -->
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="px-6 py-4.5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <div>
                    <h2 class="text-sm sm:text-base font-bold text-slate-900">{{ __('Recent Service Requests') }}</h2>
                    <p class="text-[11px] text-slate-500">{{ __('Live updates, quotation reviews, and technician execution') }}</p>
                </div>
                <a href="{{ route('client.requests.index') }}" class="text-xs font-bold text-teal-700 hover:text-teal-800 transition flex items-center space-x-1">
                    <span>{{ __('View all') }}</span>
                    <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                </a>
            </div>

            @if(isset($activeRequests) && count($activeRequests) > 0)
                <div class="divide-y divide-slate-100">
                    @foreach($activeRequests as $req)
                        <div class="p-5 hover:bg-slate-50/80 transition flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div class="space-y-1.5 flex-1 min-w-0">
                                <div class="flex items-center space-x-2">
                                    <span class="text-[11px] font-mono font-bold text-teal-800 bg-teal-50 px-2 py-0.5 rounded-md border border-teal-200">
                                        {{ $req->reference_no ?? ('REQ-' . str_pad($req->id, 6, '0', STR_PAD_LEFT)) }}
                                    </span>
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider
                                        @if(in_array($req->status, ['in_progress', 'accepted', 'scheduled'])) bg-blue-50 text-blue-700 border border-blue-200
                                        @elseif($req->status === 'completed') bg-emerald-50 text-emerald-700 border border-emerald-200
                                        @else bg-amber-50 text-amber-700 border border-amber-200 @endif">
                                        {{ $req->status_label ?? ucfirst(str_replace('_', ' ', $req->status)) }}
                                    </span>
                                </div>
                                <h3 class="text-sm font-bold text-slate-900 truncate">
                                    {{ $req->service->name ?? $req->title ?? __('Service Request') }}
                                </h3>
                                <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-slate-500 pt-0.5">
                                    <span class="flex items-center">
                                        <i data-lucide="user-check" class="w-3.5 h-3.5 text-teal-600 mr-1"></i>
                                        <strong class="text-slate-800 ml-0.5">{{ $req->technician->full_name ?? __('Matching Technician...') }}</strong>
                                    </span>
                                    <span>&bull;</span>
                                    <span class="text-slate-400 flex items-center">
                                        <i data-lucide="clock" class="w-3.5 h-3.5 text-slate-400 mr-1"></i>
                                        {{ $req->created_at ? $req->created_at->diffForHumans() : __('Recently') }}
                                    </span>
                                </div>
                            </div>

                            <div class="flex items-center space-x-2 flex-shrink-0">
                                <a href="{{ route('client.requests.show', $req->id) }}" class="btn-tap px-3.5 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs shadow-xs transition flex items-center space-x-1.5">
                                    <span>{{ __('Track Progress') }}</span>
                                    <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="p-8 text-center space-y-3">
                    <div class="w-12 h-12 rounded-2xl bg-teal-50 text-teal-700 flex items-center justify-center mx-auto border border-teal-200">
                        <i data-lucide="wrench" class="w-6 h-6"></i>
                    </div>
                    <div class="space-y-1">
                        <h4 class="text-sm font-bold text-slate-900">{{ __('No active service requests right now') }}</h4>
                        <p class="text-xs text-slate-500 max-w-sm mx-auto">{{ __('Need help with plumbing, electrical, AC, or carpentry? Post a request in under 60 seconds.') }}</p>
                    </div>
                    <div class="pt-2">
                        <a href="{{ route('client.requests.create') }}" class="btn-tap inline-flex items-center space-x-2 px-4 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs shadow-xs transition">
                            <i data-lucide="plus" class="w-4 h-4"></i>
                            <span>{{ __('Post Your First Request') }}</span>
                        </a>
                    </div>
                </div>
            @endif
        </div>

        <!-- Card 2: Explore Trade Categories Grid -->
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs p-6 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-sm sm:text-base font-bold text-slate-900">{{ __('Popular Trade Categories') }}</h2>
                    <p class="text-[11px] text-slate-500">{{ __('Connect directly with certified specialists in your neighborhood') }}</p>
                </div>
                <a href="{{ route('client.services.index') }}" class="text-xs font-bold text-teal-700 hover:text-teal-800 transition flex items-center space-x-1">
                    <span>{{ __('All services') }}</span>
                    <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                </a>
            </div>

            @php
                $trades = [
                    ['name' => 'Electrical', 'label' => __('Electrical'), 'icon' => 'zap', 'color' => 'bg-amber-50 text-amber-700 border-amber-200', 'desc' => __('Wiring, sockets & breakers')],
                    ['name' => 'Plumbing', 'label' => __('Plumbing'), 'icon' => 'droplets', 'color' => 'bg-blue-50 text-blue-700 border-blue-200', 'desc' => __('Pipes, leaks & water pumps')],
                    ['name' => 'AC & Cooling', 'label' => __('AC & Cooling'), 'icon' => 'wind', 'color' => 'bg-cyan-50 text-cyan-700 border-cyan-200', 'desc' => __('AC service & gas refill')],
                    ['name' => 'Carpentry', 'label' => __('Carpentry'), 'icon' => 'hammer', 'color' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'desc' => __('Doors, cabinets & locks')],
                    ['name' => 'Painting', 'label' => __('Painting'), 'icon' => 'brush', 'color' => 'bg-purple-50 text-purple-700 border-purple-200', 'desc' => __('Interior & exterior coats')],
                    ['name' => 'Construction', 'label' => __('Construction'), 'icon' => 'hard-hat', 'color' => 'bg-rose-50 text-rose-700 border-rose-200', 'desc' => __('Tiles, masonry & repairs')],
                ];
            @endphp

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
                @foreach($trades as $t)
                    <a href="{{ route('client.technicians.index', ['search' => $t['name']]) }}" class="p-4 rounded-2xl bg-slate-50/70 border border-slate-200/80 hover:border-teal-500 hover:bg-white hover:shadow-xs transition group flex flex-col justify-between space-y-3">
                        <div class="w-9 h-9 rounded-xl {{ $t['color'] }} border flex items-center justify-center transition group-hover:scale-105">
                            <i data-lucide="{{ $t['icon'] }}" class="w-4.5 h-4.5"></i>
                        </div>
                        <div>
                            <h4 class="text-xs sm:text-sm font-bold text-slate-900 group-hover:text-teal-700 transition">{{ $t['label'] }}</h4>
                            <p class="text-[11px] text-slate-400 mt-0.5 line-clamp-1">{{ $t['desc'] }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Card 3: Top Verified Technicians Showcase -->
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs p-6 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-sm sm:text-base font-bold text-slate-900">{{ __('Verified Technicians Near You') }}</h2>
                    <p class="text-[11px] text-slate-500">{{ __('Top-rated artisans with active subscriptions ready for hire') }}</p>
                </div>
                <a href="{{ route('client.technicians.index') }}" class="text-xs font-bold text-teal-700 hover:text-teal-800 transition flex items-center space-x-1">
                    <span>{{ __('Browse all') }}</span>
                    <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse($recommendedTechnicians ?? [] as $tech)
                    <div class="p-4.5 rounded-2xl border border-slate-200/80 hover:border-teal-400 hover:shadow-xs transition bg-white flex flex-col justify-between space-y-3.5">
                        <div class="flex items-start space-x-3.5">
                            <div class="w-11 h-11 rounded-xl bg-slate-900 text-teal-300 flex items-center justify-center font-bold text-xs flex-shrink-0 shadow-xs">
                                {{ $tech->initials }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center space-x-1.5">
                                    <h4 class="text-xs sm:text-sm font-bold text-slate-900 truncate">{{ $tech->full_name }}</h4>
                                    <span class="px-1.5 py-0.2 rounded bg-emerald-50 text-emerald-700 text-[9px] font-bold border border-emerald-200 flex items-center flex-shrink-0">
                                        <i data-lucide="check" class="w-2.5 h-2.5 mr-0.5"></i> Verified
                                    </span>
                                </div>
                                <p class="text-xs text-teal-700 font-semibold truncate mt-0.5">{{ $tech->technicianProfile->specialty ?? __('Certified Artisan') }}</p>
                                <p class="text-[11px] text-slate-400 flex items-center mt-0.5">
                                    <i data-lucide="map-pin" class="w-3 h-3 mr-1 text-slate-400"></i>
                                    {{ $tech->technicianProfile->location ?? 'Dar es Salaam' }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-3 border-t border-slate-100 text-xs">
                            <div class="flex items-center space-x-1 font-bold text-slate-900 text-[11px]">
                                <i data-lucide="star" class="w-3.5 h-3.5 fill-amber-400 text-amber-400"></i>
                                <span>{{ number_format($tech->technicianProfile->average_rating ?? 4.9, 1) }}</span>
                                <span class="text-slate-400 font-normal">({{ $tech->technicianProfile->completed_jobs_count ?? 0 }} {{ __('jobs') }})</span>
                            </div>
                            <a href="{{ route('client.technicians.show', $tech->id) }}" class="btn-tap px-3 py-1.5 rounded-xl bg-teal-50 hover:bg-teal-600 hover:text-white text-teal-700 font-bold transition text-xs border border-teal-200">
                                {{ __('View Profile') }}
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-xs text-slate-400 col-span-full">
                        {{ __('No active artisans listed right now.') }}
                    </div>
                @endforelse
            </div>
        </div>

    </div>

</div>
@endsection
