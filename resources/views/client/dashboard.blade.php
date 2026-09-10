@extends('layouts.app')

@section('title', __('Dashboard') . ' — FUNDI')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 space-y-10">

    <!-- 1. Clean & Minimal Welcome Header -->
    <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200/80 shadow-xs space-y-5">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="space-y-1">
                <div class="flex items-center space-x-2">
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                        {{ __('Welcome') }}, {{ $user->first_name }} 👋
                    </h1>
                    <span class="px-2 py-0.5 rounded-full bg-teal-50 text-teal-700 text-[11px] font-bold border border-teal-200">
                        {{ __('Client') }}
                    </span>
                </div>
                <p class="text-xs sm:text-sm text-slate-500 font-medium max-w-xl">
                    {{ __('Find verified technicians quickly or post your request to get reliable help in Dar es Salaam.') }}
                </p>
            </div>

            <!-- Fast Action Buttons -->
            <div class="flex items-center space-x-3 self-start md:self-auto flex-shrink-0">
                <a href="{{ route('client.requests.create') }}" class="px-4 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 active:scale-[0.98] text-white font-bold text-xs shadow-xs transition flex items-center space-x-2">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    <span>{{ __('Post Service Request') }}</span>
                </a>
            </div>
        </div>

        <!-- Clean Search Bar -->
        <form method="GET" action="{{ route('client.technicians.index') }}" class="pt-2">
            <div class="flex flex-col sm:flex-row items-center gap-2">
                <div class="flex-1 w-full flex items-center space-x-3 px-4 py-2.5 bg-slate-50 rounded-xl border border-slate-200 focus-within:border-teal-500 focus-within:bg-white transition">
                    <i data-lucide="search" class="w-4 h-4 text-slate-400"></i>
                    <input type="text" name="search" placeholder="{{ __('Search services e.g. Electrical, Plumbing, AC, Carpentry...') }}" class="w-full text-xs sm:text-sm text-slate-900 placeholder-slate-400 focus:outline-none bg-transparent font-medium">
                </div>
                <button type="submit" class="w-full sm:w-auto px-6 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs transition flex items-center justify-center space-x-1.5 flex-shrink-0">
                    <span>{{ __('Search') }}</span>
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </button>
            </div>
        </form>
    </div>

    <!-- 2. Explore Trade Categories -->
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg sm:text-xl font-extrabold text-slate-900 tracking-tight">{{ __('Explore Trade Services') }}</h2>
                <p class="text-xs text-slate-500 mt-0.5">{{ __('Select a category to connect with certified local specialists') }}</p>
            </div>
            <a href="{{ route('client.services.index') }}" class="text-xs font-bold text-teal-700 hover:text-teal-800 transition flex items-center space-x-1">
                <span>{{ __('View all services') }}</span>
                <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
            </a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
            @php
                $trades = [
                    ['name' => 'Electrical', 'label' => __('Electrical'), 'icon' => 'zap', 'color' => 'bg-amber-50 text-amber-600 border-amber-200', 'desc' => __('Wiring, sockets & lights')],
                    ['name' => 'Plumbing', 'label' => __('Plumbing'), 'icon' => 'droplets', 'color' => 'bg-blue-50 text-blue-600 border-blue-200', 'desc' => __('Leaks, pipes & pumps')],
                    ['name' => 'AC & Cooling', 'label' => __('AC & Cooling'), 'icon' => 'wind', 'color' => 'bg-cyan-50 text-cyan-600 border-cyan-200', 'desc' => __('AC service & gas refill')],
                    ['name' => 'Carpentry', 'label' => __('Carpentry'), 'icon' => 'hammer', 'color' => 'bg-emerald-50 text-emerald-600 border-emerald-200', 'desc' => __('Doors, locks & roofing')],
                    ['name' => 'Painting', 'label' => __('Painting'), 'icon' => 'paint-bucket', 'color' => 'bg-purple-50 text-purple-600 border-purple-200', 'desc' => __('Interior & exterior coats')],
                    ['name' => 'Construction', 'label' => __('Construction'), 'icon' => 'hard-hat', 'color' => 'bg-rose-50 text-rose-600 border-rose-200', 'desc' => __('Masonry, tiles & fixes')],
                ];
            @endphp

            @foreach($trades as $t)
                <a href="{{ route('client.technicians.index', ['search' => $t['name']]) }}" class="p-4 rounded-2xl bg-white border border-slate-200 hover:border-teal-500 hover:shadow-card transition flex flex-col justify-between space-y-3 group">
                    <div class="w-10 h-10 rounded-xl {{ $t['color'] }} border flex items-center justify-center transition group-hover:scale-110">
                        <i data-lucide="{{ $t['icon'] }}" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h4 class="text-xs sm:text-sm font-bold text-slate-900 group-hover:text-teal-700 transition">{{ $t['label'] }}</h4>
                        <p class="text-[11px] text-slate-400 mt-0.5 line-clamp-1">{{ $t['desc'] }}</p>
                    </div>
                </a>
            @endforeach
        </div>
    </div>

    <!-- 3. Active Requests OR Onboarding Stepper -->
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg sm:text-xl font-extrabold text-slate-900 tracking-tight">{{ __('Active Service Requests') }}</h2>
                <p class="text-xs text-slate-500 mt-0.5">{{ __('Live status updates, quotes, and technician communication') }}</p>
            </div>
            @if(isset($recentRequests) && count($recentRequests) > 0)
            <a href="{{ route('client.requests.index') }}" class="text-xs font-bold text-teal-700 hover:underline">
                {{ __('View all') }} ({{ count($recentRequests) }}) &rarr;
            </a>
            @endif
        </div>

        @if(isset($recentRequests) && count($recentRequests) > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($recentRequests as $req)
                    <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-sm hover:shadow-card transition flex flex-col justify-between space-y-5">
                        <div class="flex items-start justify-between">
                            <div class="space-y-1">
                                <span class="text-xs font-mono font-bold text-teal-700 bg-teal-50 px-2.5 py-1 rounded-lg border border-teal-200">
                                    {{ $req->reference_no ?? ('REQ-' . str_pad($req->id, 6, '0', STR_PAD_LEFT)) }}
                                </span>
                                <h3 class="text-base font-bold text-slate-900 mt-2">{{ $req->service->name ?? $req->title ?? __('Service Request') }}</h3>
                                <p class="text-xs text-slate-500">
                                    {{ __('Assigned Technician:') }} <strong class="text-slate-900">{{ $req->technician->full_name ?? __('Matching Technician...') }}</strong>
                                </p>
                            </div>
                            <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider
                                @if(in_array($req->status, ['in_progress', 'accepted', 'scheduled'])) bg-blue-50 text-blue-700 border border-blue-200
                                @elseif($req->status === 'completed') bg-emerald-50 text-emerald-700 border border-emerald-200
                                @else bg-amber-50 text-amber-700 border border-amber-200 @endif">
                                {{ $req->status_label ?? ucfirst(str_replace('_', ' ', $req->status)) }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between pt-4 border-t border-slate-100 text-xs">
                            <span class="text-xs text-slate-400">
                                {{ __('Requested') }} {{ $req->created_at ? $req->created_at->diffForHumans() : __('Recently') }}
                            </span>
                            <a href="{{ route('client.requests.show', $req->id) }}" class="px-4 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs shadow-sm transition">
                                {{ __('Track Live Progress') }} &rarr;
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- Welcoming Onboarding Card for New Clients -->
            <div class="rounded-3xl bg-white border border-slate-200 p-6 sm:p-8 shadow-sm space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-5">
                    <div class="space-y-1">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-teal-700 bg-teal-50 px-2.5 py-0.5 rounded-md">{{ __('How to Get Started') }}</span>
                        <h3 class="text-base sm:text-lg font-bold text-slate-900">{{ __('Get your home or office repairs fixed in 3 transparent steps') }}</h3>
                    </div>
                    <a href="{{ route('client.requests.create') }}" class="px-5 py-3 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs shadow-sm transition flex items-center justify-center space-x-2 self-start sm:self-auto">
                        <i data-lucide="plus" class="w-4 h-4"></i>
                        <span>{{ __('Create Your First Request') }}</span>
                    </a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5 text-xs">
                    <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/70 space-y-2.5">
                        <div class="w-8 h-8 rounded-xl bg-teal-600 text-white font-bold flex items-center justify-center font-mono text-sm shadow-xs">1</div>
                        <h4 class="text-sm font-bold text-slate-900">{{ __('Post Request & Photos') }}</h4>
                        <p class="text-slate-500 leading-relaxed">{{ __('Choose your service trade, describe what needs fixing, and upload clear photos.') }}</p>
                    </div>

                    <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/70 space-y-2.5">
                        <div class="w-8 h-8 rounded-xl bg-teal-600 text-white font-bold flex items-center justify-center font-mono text-sm shadow-xs">2</div>
                        <h4 class="text-sm font-bold text-slate-900">{{ __('Connect with Fundi') }}</h4>
                        <p class="text-slate-500 leading-relaxed">{{ __('Pay a small TZS 2,000 connection fee to instantly unlock verified Phone & WhatsApp access.') }}</p>
                    </div>

                    <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/70 space-y-2.5">
                        <div class="w-8 h-8 rounded-xl bg-teal-600 text-white font-bold flex items-center justify-center font-mono text-sm shadow-xs">3</div>
                        <h4 class="text-sm font-bold text-slate-900">{{ __('Settle Directly (0% Fee)') }}</h4>
                        <p class="text-slate-500 leading-relaxed">{{ __('Agree on labour and materials directly with your fundi. 0% commission deducted on labour.') }}</p>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- 4. Verified Technicians Near You (Live Marketplace Showcase) -->
    <div class="space-y-4 pt-2">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg sm:text-xl font-extrabold text-slate-900 tracking-tight">{{ __('Verified Technicians Near You') }}</h2>
                <p class="text-xs text-slate-500 mt-0.5">{{ __('Top-rated artisans with active subscriptions ready for immediate hire') }}</p>
            </div>
            <a href="{{ route('client.technicians.index') }}" class="text-xs font-bold text-teal-700 hover:text-teal-800 transition flex items-center space-x-1">
                <span>{{ __('Browse all technicians') }}</span>
                <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @forelse($featuredTechnicians ?? [] as $tech)
                <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:shadow-card transition flex flex-col justify-between space-y-4">
                    <div class="flex items-start space-x-3.5">
                        <div class="w-12 h-12 rounded-xl bg-slate-900 text-teal-300 flex items-center justify-center font-bold text-sm flex-shrink-0 shadow-xs">
                            {{ $tech->initials }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center space-x-1.5">
                                <h4 class="text-sm font-bold text-slate-900 truncate">{{ $tech->full_name }}</h4>
                                <span class="px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-700 text-[10px] font-bold flex items-center flex-shrink-0 border border-emerald-200" title="{{ __('Verified Fundi') }}">
                                    <i data-lucide="check" class="w-3 h-3 mr-0.5"></i> {{ __('VERIFIED') }}
                                </span>
                            </div>
                            <p class="text-xs text-teal-700 font-semibold truncate mt-0.5">{{ $tech->technicianProfile->specialty ?? __('Certified Artisan') }}</p>
                            <p class="text-xs text-slate-400 mt-0.5 flex items-center">
                                <i data-lucide="map-pin" class="w-3 h-3 mr-1 text-slate-400"></i>
                                {{ $tech->technicianProfile->location ?? 'Dar es Salaam' }}
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-3 border-t border-slate-100 text-xs">
                        <div class="flex items-center space-x-1 font-bold text-slate-900">
                            @if(($tech->technicianProfile->average_rating ?? 0) > 0)
                                <i data-lucide="star" class="w-4 h-4 fill-amber-400 text-amber-400"></i>
                                <span>{{ number_format($tech->technicianProfile->average_rating, 1) }}</span>
                                @if(($tech->technicianProfile->total_reviews ?? 0) > 0)
                                    <span class="text-slate-400 font-normal text-[11px]">({{ $tech->technicianProfile->total_reviews }} {{ __('reviews') }})</span>
                                @endif
                            @else
                                <span class="px-2 py-0.5 rounded bg-teal-50 text-teal-700 font-bold text-[10px] border border-teal-100">{{ __('New') }}</span>
                                <span class="text-slate-400 font-normal text-[11px]">({{ $tech->technicianProfile->completed_jobs_count ?? 0 }} {{ __('jobs') }})</span>
                            @endif
                        </div>
                        <a href="{{ route('client.technicians.show', $tech->id) }}" class="px-3.5 py-1.5 rounded-xl bg-teal-50 hover:bg-teal-600 hover:text-white text-teal-700 font-bold transition text-xs border border-teal-200">
                            {{ __('View Profile') }} &rarr;
                        </a>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-xs text-slate-400 col-span-full bg-white rounded-2xl border border-slate-200">
                    {{ __('No active artisans listed right now. Check back shortly.') }}
                </div>
            @endforelse
        </div>
    </div>

    <!-- 5. Platform Guarantee & Trust Strip -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 pt-4 border-t border-slate-200 text-xs">
        <div class="flex items-center space-x-3 p-4 rounded-2xl bg-white border border-slate-200/80 shadow-xs">
            <div class="w-9 h-9 rounded-xl bg-teal-50 text-teal-700 flex items-center justify-center flex-shrink-0">
                <i data-lucide="shield-check" class="w-5 h-5"></i>
            </div>
            <div>
                <h4 class="font-bold text-slate-900">{{ __('NIDA & Cert Vetted') }}</h4>
                <p class="text-[11px] text-slate-500">{{ __('Every artisan is verified') }}</p>
            </div>
        </div>

        <div class="flex items-center space-x-3 p-4 rounded-2xl bg-white border border-slate-200/80 shadow-xs">
            <div class="w-9 h-9 rounded-xl bg-teal-50 text-teal-700 flex items-center justify-center flex-shrink-0">
                <i data-lucide="phone-call" class="w-5 h-5"></i>
            </div>
            <div>
                <h4 class="font-bold text-slate-900">{{ __('Direct WhatsApp & Call') }}</h4>
                <p class="text-[11px] text-slate-500">{{ __('Direct phone unlocking') }}</p>
            </div>
        </div>

        <div class="flex items-center space-x-3 p-4 rounded-2xl bg-white border border-slate-200/80 shadow-xs">
            <div class="w-9 h-9 rounded-xl bg-teal-50 text-teal-700 flex items-center justify-center flex-shrink-0">
                <i data-lucide="percent" class="w-5 h-5"></i>
            </div>
            <div>
                <h4 class="font-bold text-slate-900">{{ __('0% Labour Commission') }}</h4>
                <p class="text-[11px] text-slate-500">{{ __('Direct cash/M-Pesa payment') }}</p>
            </div>
        </div>

        <div class="flex items-center space-x-3 p-4 rounded-2xl bg-white border border-slate-200/80 shadow-xs">
            <div class="w-9 h-9 rounded-xl bg-teal-50 text-teal-700 flex items-center justify-center flex-shrink-0">
                <i data-lucide="star" class="w-5 h-5"></i>
            </div>
            <div>
                <h4 class="font-bold text-slate-900">{{ __('100% Verified Reviews') }}</h4>
                <p class="text-[11px] text-slate-500">{{ __('Real customer ratings') }}</p>
            </div>
        </div>
    </div>

</div>
@endsection
