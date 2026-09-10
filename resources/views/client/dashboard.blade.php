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

    <!-- 1. Top Section Header (Matching Reference UI) -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-200/80">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2.5 py-0.5 rounded-md bg-teal-50 text-teal-700 text-[11px] font-bold border border-teal-200 uppercase tracking-wider">
                    {{ __('Client Workspace') }}
                </span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight mt-1.5">
                {{ __('Dashboard') }}
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 font-medium mt-0.5">
                {{ __('Manage your active requests, connect with verified technicians, and track jobs in real time.') }}
            </p>
        </div>

        <div class="flex items-center space-x-3 flex-shrink-0">
            <a href="{{ route('client.requests.create') }}" class="btn-tap px-4 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs shadow-xs transition flex items-center space-x-2">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>{{ __('Post Service Request') }}</span>
            </a>
        </div>
    </div>

    <!-- 2. 4 Metric KPI Cards (Matching Reference Layout) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Metric 1: Active Requests -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-xs hover:border-teal-300 transition flex flex-col justify-between space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('Active Requests') }}</span>
                <div class="w-9 h-9 rounded-xl bg-teal-50 text-teal-700 border border-teal-200 flex items-center justify-center">
                    <i data-lucide="inbox" class="w-4 h-4"></i>
                </div>
            </div>
            <div>
                <div class="text-2xl sm:text-3xl font-black text-slate-900 font-mono">{{ $activeCount }}</div>
                <p class="text-[11px] text-slate-400 font-medium mt-0.5">{{ __('Underway or matching') }}</p>
            </div>
        </div>

        <!-- Metric 2: Ongoing Jobs -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-xs hover:border-teal-300 transition flex flex-col justify-between space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('Ongoing Jobs') }}</span>
                <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-700 border border-blue-200 flex items-center justify-center">
                    <i data-lucide="activity" class="w-4 h-4"></i>
                </div>
            </div>
            <div>
                <div class="text-2xl sm:text-3xl font-black text-slate-900 font-mono">{{ $ongoingCount }}</div>
                <p class="text-[11px] text-slate-400 font-medium mt-0.5">{{ __('Technicians assigned') }}</p>
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
                <div class="text-2xl sm:text-3xl font-black text-slate-900 font-mono">{{ $completedCount }}</div>
                <p class="text-[11px] text-slate-400 font-medium mt-0.5">{{ __('Successfully fixed') }}</p>
            </div>
        </div>

        <!-- Metric 4: Saved Specialists -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-xs hover:border-teal-300 transition flex flex-col justify-between space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('Saved Fundis') }}</span>
                <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-700 border border-amber-200 flex items-center justify-center">
                    <i data-lucide="bookmark" class="w-4 h-4"></i>
                </div>
            </div>
            <div>
                <div class="text-2xl sm:text-3xl font-black text-slate-900 font-mono">{{ $savedCount }}</div>
                <p class="text-[11px] text-slate-400 font-medium mt-0.5">{{ __('Direct contact list') }}</p>
            </div>
        </div>
    </div>

    <!-- 3. Main 2-Column Split Layout (8 Col Left / 4 Col Right) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- LEFT COLUMN (8 cols): Recent Requests & Featured Technicians -->
        <div class="lg:col-span-8 space-y-6">
            
            <!-- Card: Recent Service Requests -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">{{ __('Recent Service Requests') }}</h2>
                        <p class="text-[11px] text-slate-500">{{ __('Real-time status updates and direct fundi connections') }}</p>
                    </div>
                    <a href="{{ route('client.requests.index') }}" class="text-xs font-bold text-teal-700 hover:text-teal-800 transition flex items-center space-x-1">
                        <span>{{ __('View all') }}</span>
                        <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                    </a>
                </div>

                @if(isset($activeRequests) && count($activeRequests) > 0)
                    <div class="divide-y divide-slate-100">
                        @foreach($activeRequests as $req)
                            <div class="p-5 hover:bg-slate-50/70 transition flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <div class="space-y-1.5 flex-1 min-w-0">
                                    <div class="flex items-center space-x-2">
                                        <span class="text-[11px] font-mono font-bold text-teal-700 bg-teal-50 px-2 py-0.5 rounded border border-teal-200">
                                            {{ $req->reference_no ?? ('REQ-' . str_pad($req->id, 6, '0', STR_PAD_LEFT)) }}
                                        </span>
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider
                                            @if(in_array($req->status, ['in_progress', 'accepted', 'scheduled'])) bg-blue-50 text-blue-700 border border-blue-200
                                            @elseif($req->status === 'completed') bg-emerald-50 text-emerald-700 border border-emerald-200
                                            @else bg-amber-50 text-amber-700 border border-amber-200 @endif">
                                            {{ $req->status_label ?? ucfirst(str_replace('_', ' ', $req->status)) }}
                                        </span>
                                    </div>
                                    <h3 class="text-sm font-bold text-slate-900 truncate">
                                        {{ $req->service->name ?? $req->title ?? __('Service Request') }}
                                    </h3>
                                    <p class="text-xs text-slate-500 flex items-center space-x-2">
                                        <span>{{ __('Technician:') }} <strong class="text-slate-800">{{ $req->technician->full_name ?? __('Matching Technician...') }}</strong></span>
                                        <span>&bull;</span>
                                        <span>{{ $req->created_at ? $req->created_at->diffForHumans() : __('Recently') }}</span>
                                    </p>
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

            <!-- Card: Explore Categories Grid -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">{{ __('Popular Trade Categories') }}</h2>
                        <p class="text-[11px] text-slate-500">{{ __('Select a category to connect with certified local specialists') }}</p>
                    </div>
                    <a href="{{ route('client.services.index') }}" class="text-xs font-bold text-teal-700 hover:text-teal-800 transition flex items-center space-x-1">
                        <span>{{ __('All services') }}</span>
                        <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                    </a>
                </div>

                @php
                    $trades = [
                        ['name' => 'Electrical', 'label' => __('Electrical'), 'icon' => 'zap', 'color' => 'bg-amber-50 text-amber-700 border-amber-200', 'desc' => __('Wiring, sockets & lights')],
                        ['name' => 'Plumbing', 'label' => __('Plumbing'), 'icon' => 'droplets', 'color' => 'bg-blue-50 text-blue-700 border-blue-200', 'desc' => __('Pipes, leaks & pumps')],
                        ['name' => 'AC & Cooling', 'label' => __('AC & Cooling'), 'icon' => 'wind', 'color' => 'bg-cyan-50 text-cyan-700 border-cyan-200', 'desc' => __('Service & gas refill')],
                        ['name' => 'Carpentry', 'label' => __('Carpentry'), 'icon' => 'hammer', 'color' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'desc' => __('Doors, locks & roofing')],
                        ['name' => 'Painting', 'label' => __('Painting'), 'icon' => 'brush', 'color' => 'bg-purple-50 text-purple-700 border-purple-200', 'desc' => __('Interior & exterior coats')],
                        ['name' => 'Construction', 'label' => __('Construction'), 'icon' => 'hard-hat', 'color' => 'bg-rose-50 text-rose-700 border-rose-200', 'desc' => __('Masonry & renovations')],
                    ];
                @endphp

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    @foreach($trades as $t)
                        <a href="{{ route('client.technicians.index', ['search' => $t['name']]) }}" class="p-3.5 rounded-xl bg-slate-50/70 border border-slate-200/80 hover:border-teal-500 hover:bg-white transition group flex flex-col justify-between space-y-2.5">
                            <div class="w-8 h-8 rounded-lg {{ $t['color'] }} border flex items-center justify-center transition group-hover:scale-105">
                                <i data-lucide="{{ $t['icon'] }}" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-slate-900 group-hover:text-teal-700 transition">{{ $t['label'] }}</h4>
                                <p class="text-[10px] text-slate-400 truncate">{{ $t['desc'] }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- Card: Verified Technicians Showcase -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">{{ __('Verified Technicians Near You') }}</h2>
                        <p class="text-[11px] text-slate-500">{{ __('Direct access to top-rated local artisans') }}</p>
                    </div>
                    <a href="{{ route('client.technicians.index') }}" class="text-xs font-bold text-teal-700 hover:text-teal-800 transition flex items-center space-x-1">
                        <span>{{ __('Browse all') }}</span>
                        <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                    </a>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @forelse($recommendedTechnicians ?? [] as $tech)
                        <div class="p-4 rounded-xl border border-slate-200/80 hover:border-teal-400 hover:shadow-xs transition bg-white flex flex-col justify-between space-y-3">
                            <div class="flex items-start space-x-3">
                                <div class="w-10 h-10 rounded-xl bg-navy-900 text-teal-300 flex items-center justify-center font-bold text-xs flex-shrink-0">
                                    {{ $tech->initials }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center space-x-1.5">
                                        <h4 class="text-xs font-bold text-slate-900 truncate">{{ $tech->full_name }}</h4>
                                        <span class="px-1.5 py-0.2 rounded bg-emerald-50 text-emerald-700 text-[9px] font-bold border border-emerald-200 flex items-center flex-shrink-0">
                                            <i data-lucide="check" class="w-2.5 h-2.5 mr-0.5"></i> Verified
                                        </span>
                                    </div>
                                    <p class="text-[11px] text-teal-700 font-semibold truncate">{{ $tech->technicianProfile->specialty ?? __('Certified Artisan') }}</p>
                                    <p class="text-[10px] text-slate-400 flex items-center mt-0.5">
                                        <i data-lucide="map-pin" class="w-3 h-3 mr-1 text-slate-400"></i>
                                        {{ $tech->technicianProfile->location ?? 'Dar es Salaam' }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center justify-between pt-2.5 border-t border-slate-100 text-xs">
                                <div class="flex items-center space-x-1 font-bold text-slate-900 text-[11px]">
                                    <i data-lucide="star" class="w-3.5 h-3.5 fill-amber-400 text-amber-400"></i>
                                    <span>{{ number_format($tech->technicianProfile->average_rating ?? 4.9, 1) }}</span>
                                    <span class="text-slate-400 font-normal">({{ $tech->technicianProfile->completed_jobs_count ?? 0 }} {{ __('jobs') }})</span>
                                </div>
                                <a href="{{ route('client.technicians.show', $tech->id) }}" class="btn-tap px-3 py-1 rounded-lg bg-teal-50 hover:bg-teal-600 hover:text-white text-teal-700 font-bold transition text-[11px] border border-teal-200">
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

        <!-- RIGHT COLUMN (4 cols): Quick Actions & Trust Assurance -->
        <div class="lg:col-span-4 space-y-6">
            
            <!-- Quick Actions Stack (Matching Reference Menu Layout) -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 space-y-3">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 px-1">{{ __('Quick Actions') }}</h3>

                <div class="space-y-1.5">
                    <a href="{{ route('client.requests.create') }}" class="btn-tap flex items-center justify-between p-3 rounded-xl bg-slate-50/80 hover:bg-teal-50/70 border border-slate-200/70 hover:border-teal-200 transition group">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-lg bg-teal-600 text-white flex items-center justify-center flex-shrink-0">
                                <i data-lucide="plus" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-slate-900 group-hover:text-teal-700 transition">{{ __('Post New Request') }}</h4>
                                <p class="text-[10px] text-slate-500">{{ __('Get quotes from artisans') }}</p>
                            </div>
                        </div>
                        <i data-lucide="chevron-right" class="w-4 h-4 text-slate-400 group-hover:text-teal-600 transition"></i>
                    </a>

                    <a href="{{ route('client.technicians.index') }}" class="btn-tap flex items-center justify-between p-3 rounded-xl bg-slate-50/80 hover:bg-teal-50/70 border border-slate-200/70 hover:border-teal-200 transition group">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-lg bg-slate-900 text-teal-400 flex items-center justify-center flex-shrink-0">
                                <i data-lucide="search" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-slate-900 group-hover:text-teal-700 transition">{{ __('Find Technicians') }}</h4>
                                <p class="text-[10px] text-slate-500">{{ __('Direct directory search') }}</p>
                            </div>
                        </div>
                        <i data-lucide="chevron-right" class="w-4 h-4 text-slate-400 group-hover:text-teal-600 transition"></i>
                    </a>

                    <a href="{{ route('client.services.index') }}" class="btn-tap flex items-center justify-between p-3 rounded-xl bg-slate-50/80 hover:bg-teal-50/70 border border-slate-200/70 hover:border-teal-200 transition group">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center flex-shrink-0">
                                <i data-lucide="layers" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-slate-900 group-hover:text-teal-700 transition">{{ __('Browse Services') }}</h4>
                                <p class="text-[10px] text-slate-500">{{ __('Explore standard trades') }}</p>
                            </div>
                        </div>
                        <i data-lucide="chevron-right" class="w-4 h-4 text-slate-400 group-hover:text-teal-600 transition"></i>
                    </a>

                    <a href="{{ route('client.favorites.index') }}" class="btn-tap flex items-center justify-between p-3 rounded-xl bg-slate-50/80 hover:bg-teal-50/70 border border-slate-200/70 hover:border-teal-200 transition group">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-700 border border-amber-200 flex items-center justify-center flex-shrink-0">
                                <i data-lucide="bookmark" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-slate-900 group-hover:text-teal-700 transition">{{ __('Saved Artisans') }}</h4>
                                <p class="text-[10px] text-slate-500">{{ __('Quick access bookmarks') }}</p>
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
                                <p class="text-[10px] text-slate-500">{{ __('Direct chat with fundis') }}</p>
                            </div>
                        </div>
                        <i data-lucide="chevron-right" class="w-4 h-4 text-slate-400 group-hover:text-teal-600 transition"></i>
                    </a>
                </div>
            </div>

            <!-- Platform Direct Guarantee Card -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 space-y-4">
                <div class="flex items-center space-x-2.5">
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center justify-center flex-shrink-0">
                        <i data-lucide="shield-check" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider">{{ __('Platform Guarantee') }}</h3>
                        <p class="text-[10px] text-slate-500">{{ __('Safe, transparent & direct') }}</p>
                    </div>
                </div>

                <div class="space-y-2.5 text-xs text-slate-600 pt-1 border-t border-slate-100">
                    <div class="flex items-start space-x-2.5">
                        <i data-lucide="check" class="w-4 h-4 text-emerald-600 flex-shrink-0 mt-0.5"></i>
                        <span class="text-[11px] leading-tight"><strong>{{ __('Verified Credentials:') }}</strong> {{ __('NIDA and trade certificate vetted technicians.') }}</span>
                    </div>
                    <div class="flex items-start space-x-2.5">
                        <i data-lucide="check" class="w-4 h-4 text-emerald-600 flex-shrink-0 mt-0.5"></i>
                        <span class="text-[11px] leading-tight"><strong>{{ __('0% Labour Commission:') }}</strong> {{ __('Settle directly with your fundi via cash or M-Pesa.') }}</span>
                    </div>
                    <div class="flex items-start space-x-2.5">
                        <i data-lucide="check" class="w-4 h-4 text-emerald-600 flex-shrink-0 mt-0.5"></i>
                        <span class="text-[11px] leading-tight"><strong>{{ __('Direct Phone & WhatsApp:') }}</strong> {{ __('Instant connection without middlemen.') }}</span>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection
