@extends('layouts.app')

@section('title', __('Find Technicians') . ' — FUNDI')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">{{ __('Discover Verified Technicians') }}</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">{{ __('Compare certified professionals, review ratings, and request services') }}</p>
        </div>
        <span class="text-xs text-slate-500 font-bold bg-white px-3.5 py-2 rounded-xl border border-slate-200 self-start md:self-auto shadow-xs">
            {{ __('Showing') }} {{ $technicians->total() }} {{ __('Technicians') }}
        </span>
    </div>

    <!-- Enhanced Filter & Search Bar -->
    <div class="bg-white rounded-2xl p-5 sm:p-6 border border-slate-200 shadow-xs space-y-4">
        <form method="GET" action="{{ route('client.technicians.index') }}" class="space-y-4">
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <!-- Search Input -->
                <div class="relative sm:col-span-2">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i data-lucide="search" class="w-4 h-4"></i>
                    </div>
                    <input type="text" name="q" value="{{ $search }}" placeholder="{{ __('Search by name, skill, title...') }}" class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold focus:ring-2 focus:ring-teal-500 bg-slate-50/50">
                </div>

                <!-- Service Category Dropdown -->
                <div>
                    <select name="service" class="w-full py-2.5 px-3 rounded-xl border border-slate-200 text-xs font-semibold focus:ring-2 focus:ring-teal-500 bg-slate-50/50 text-slate-700">
                        <option value="">{{ __('All Service Trades') }}</option>
                        @foreach($services as $svc)
                            <option value="{{ $svc->id }}" {{ (string)$selectedService === (string)$svc->id ? 'selected' : '' }}>
                                {{ __($svc->name) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Location / District Input -->
                <div>
                    <input type="text" name="location" value="{{ $location }}" placeholder="{{ __('District (e.g. Kinondoni, Ilala)...') }}" class="w-full py-2.5 px-3 rounded-xl border border-slate-200 text-xs font-semibold focus:ring-2 focus:ring-teal-500 bg-slate-50/50 text-slate-700">
                </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-6 gap-3 pt-2 border-t border-slate-100 text-xs">
                <!-- Min Rating -->
                <div>
                    <select name="rating" class="w-full py-2 px-3 rounded-xl border border-slate-200 text-xs bg-slate-50/50 text-slate-700">
                        <option value="">{{ __('Any Rating') }}</option>
                        <option value="4.5" {{ $rating === '4.5' ? 'selected' : '' }}>★ 4.5 & {{ __('Above') }}</option>
                        <option value="4.0" {{ $rating === '4.0' ? 'selected' : '' }}>★ 4.0 & {{ __('Above') }}</option>
                    </select>
                </div>

                <!-- Min Experience -->
                <div>
                    <select name="experience" class="w-full py-2 px-3 rounded-xl border border-slate-200 text-xs bg-slate-50/50 text-slate-700">
                        <option value="">{{ __('Any Experience') }}</option>
                        <option value="3" {{ $experience === '3' ? 'selected' : '' }}>3+ {{ __('Years') }}</option>
                        <option value="5" {{ $experience === '5' ? 'selected' : '' }}>5+ {{ __('Years') }}</option>
                    </select>
                </div>

                <!-- Availability -->
                <div>
                    <select name="availability" class="w-full py-2 px-3 rounded-xl border border-slate-200 text-xs bg-slate-50/50 text-slate-700">
                        <option value="">{{ __('Any Availability') }}</option>
                        <option value="available" {{ $availability === 'available' ? 'selected' : '' }}>{{ __('Available Now') }}</option>
                        <option value="busy" {{ $availability === 'busy' ? 'selected' : '' }}>{{ __('Busy') }}</option>
                    </select>
                </div>

                <!-- Sort By -->
                <div>
                    <select name="sort" class="w-full py-2 px-3 rounded-xl border border-slate-200 text-xs bg-slate-50/50 text-slate-700 font-bold">
                        <option value="smart_match" {{ $sortBy === 'smart_match' ? 'selected' : '' }}>⚡ {{ __('Smart Match') }}</option>
                        <option value="highest_rated" {{ $sortBy === 'highest_rated' ? 'selected' : '' }}>{{ __('Highest Rated') }}</option>
                        <option value="most_experienced" {{ $sortBy === 'most_experienced' ? 'selected' : '' }}>{{ __('Most Experienced') }}</option>
                        <option value="most_jobs" {{ $sortBy === 'most_jobs' ? 'selected' : '' }}>{{ __('Most Completed Jobs') }}</option>
                    </select>
                </div>

                <!-- Verified Only Checkbox -->
                <div class="flex items-center space-x-2 sm:col-span-2">
                    <label class="flex items-center space-x-2 text-xs font-bold text-slate-700 cursor-pointer">
                        <input type="checkbox" name="verified_only" value="1" {{ $verifiedOnly ? 'checked' : '' }} class="w-4 h-4 rounded text-teal-600 border-slate-300 focus:ring-teal-500">
                        <span>✓ {{ __('Verified Only') }}</span>
                    </label>

                    <button type="submit" class="ml-auto px-5 py-2 bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs rounded-xl shadow-xs transition cursor-pointer">
                        {{ __('Apply Filters') }}
                    </button>
                </div>
            </div>

        </form>
    </div>

    <!-- Technicians Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($technicians as $tech)
        @php 
            $tp = $tech->technicianProfile;
            $isFav = in_array($tech->id, $savedTechIds);
        @endphp
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs hover:border-teal-500 hover:shadow-card transition flex flex-col justify-between space-y-4 relative">
            
            <!-- Top Badges -->
            <div class="flex items-center space-x-1.5 absolute top-4 right-14">
                @if($tech->isFeaturedTechnician())
                    <span class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-xl bg-amber-50 text-amber-900 border border-amber-300 text-[10px] font-black shadow-xs">
                        <span>⭐ {{ __('Featured') }}</span>
                    </span>
                @endif
                <span class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-xl bg-teal-50 text-teal-800 border border-teal-200 text-[11px] font-black">
                    <span>⚡ {{ $tech->match_score ?? 90 }}% {{ __('Match') }}</span>
                </span>
            </div>

            <!-- Favorite Toggle Button -->
            <div class="absolute top-4 right-4">
                <form method="POST" action="{{ route('technicians.favorite', $tech->id) }}">
                    @csrf
                    <button type="submit" class="p-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 transition shadow-xs text-slate-400 hover:text-rose-500 {{ $isFav ? 'text-rose-600 bg-rose-50 border-rose-200' : '' }}" title="{{ $isFav ? __('Saved') : __('Save Technician') }}">
                        <i data-lucide="heart" class="w-4 h-4 {{ $isFav ? 'fill-rose-500 stroke-rose-500' : '' }}"></i>
                    </button>
                </form>
            </div>

            <!-- Header Profile -->
            <div class="flex items-start space-x-4">
                <div class="w-14 h-14 rounded-2xl bg-slate-900 text-teal-300 font-black text-base flex items-center justify-center flex-shrink-0 shadow-xs">
                    {{ $tech->initials }}
                </div>
                <div class="space-y-1 pr-24">
                    <div class="flex items-center space-x-1">
                        <h3 class="text-sm font-bold text-slate-900">{{ $tech->full_name }}</h3>
                        @if($tp && $tp->verification_status === 'approved')
                            <i data-lucide="check-circle" class="w-4 h-4 text-emerald-600 fill-emerald-100" title="{{ __('Verified Fundi') }}"></i>
                        @endif
                    </div>
                    <p class="text-xs text-teal-700 font-semibold truncate">{{ __($tp->professional_title ?? 'Master Technician') }}</p>
                    
                    <div class="flex items-center space-x-3 text-xs pt-0.5">
                        @if(($tp->average_rating ?? 0) > 0)
                            <span class="font-bold text-amber-500 flex items-center">
                                ★ {{ number_format($tp->average_rating, 1) }}
                                @if(($tp->total_reviews ?? 0) > 0)
                                    <span class="text-slate-400 font-normal ml-1">({{ $tp->total_reviews }})</span>
                                @endif
                            </span>
                        @else
                            <span class="font-bold text-teal-700 bg-teal-50 px-2 py-0.5 rounded text-[10px] flex items-center border border-teal-100">
                                {{ __('New') }}
                            </span>
                        @endif
                        <span class="text-slate-400">• {{ $tp->years_experience ?? 1 }}+ {{ __('Yrs Exp') }}</span>
                        <span class="text-slate-400">• {{ $tp->completed_jobs_count ?? 0 }} {{ __('Jobs') }}</span>
                    </div>
                </div>
            </div>

            <!-- Description -->
            <p class="text-xs text-slate-500 line-clamp-2 leading-relaxed">
                {{ __($tp->bio ?? 'Certified professional technician providing guaranteed trade services across Dar es Salaam.') }}
            </p>

            <!-- Skills & Services Tags -->
            <div class="flex flex-wrap gap-1.5 pt-1">
                @foreach($tech->services->take(2) as $s)
                    <span class="px-2.5 py-0.5 rounded-lg bg-teal-50 text-teal-700 font-bold text-[10px] border border-teal-200/50">
                        {{ __($s->name) }}
                    </span>
                @endforeach
                @if($tech->serviceAreas->isNotEmpty())
                    <span class="px-2.5 py-0.5 rounded-lg bg-slate-100 text-slate-600 text-[10px]">
                        📍 {{ $tech->serviceAreas->pluck('area_name')->take(2)->implode(', ') }}
                    </span>
                @endif
            </div>

            <!-- Card Actions -->
            <div class="grid grid-cols-2 gap-2 pt-3 border-t border-slate-100">
                <a href="{{ route('client.technicians.show', $tech->id) }}" class="py-2.5 px-3 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold text-xs text-center transition">
                    {{ __('View Portfolio') }}
                </a>
                <a href="{{ route('client.requests.create', ['technician_id' => $tech->id, 'service_id' => $selectedService]) }}" class="py-2.5 px-3 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs text-center shadow-xs transition">
                    {{ __('Request Fundi') }}
                </a>
            </div>

        </div>
        @empty
        <div class="col-span-full p-12 text-center bg-white rounded-3xl border border-slate-200 space-y-3">
            <i data-lucide="search-x" class="w-12 h-12 mx-auto text-slate-300"></i>
            <h3 class="text-sm font-bold text-slate-800">{{ __('No Technicians Match Your Filters') }}</h3>
            <p class="text-xs text-slate-400 max-w-md mx-auto">{{ __('Try clearing location or rating filters to see more available certified technicians.') }}</p>
            <a href="{{ route('client.technicians.index') }}" class="inline-block px-4 py-2 bg-teal-600 text-white text-xs font-bold rounded-xl shadow-xs">
                {{ __('Reset All Filters') }}
            </a>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="pt-4">
        {{ $technicians->links() }}
    </div>

</div>
@endsection
