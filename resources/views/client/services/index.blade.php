@extends('layouts.app')

@section('title', __('All Services') . ' — FUNDI')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-8">

    <!-- Header & Search -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">{{ __('Browse Services') }}</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">{{ __('Explore all categories of technical and skilled trade services in Dar es Salaam') }}</p>
        </div>

        <form action="{{ route('client.services.index') }}" method="GET" class="w-full md:w-80">
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                    <i data-lucide="search" class="w-4 h-4"></i>
                </span>
                <input type="text" name="q" value="{{ $search }}" placeholder="{{ __('Search service categories...') }}" class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 bg-white">
            </div>
        </form>
    </div>

    <!-- Categories Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        @php
            $icons = [
                'Electrical' => 'zap',
                'Plumbing' => 'droplets',
                'AC Repair' => 'wind',
                'Phone Repair' => 'smartphone',
                'Computer Repair' => 'monitor',
                'Carpentry' => 'hammer',
                'Welding' => 'flame',
                'Painting' => 'paint-bucket',
                'Masonry' => 'hard-hat',
                'Appliance Repair' => 'tv',
            ];
        @endphp

        @forelse($services as $svc)
        <a href="{{ route('client.services.show', $svc->id) }}" class="p-6 rounded-2xl bg-white border border-slate-200 hover:border-teal-500 hover:shadow-card transition flex flex-col justify-between space-y-4 group">
            <div class="flex items-start justify-between">
                <div class="w-12 h-12 rounded-xl bg-teal-50 text-teal-700 group-hover:bg-teal-600 group-hover:text-white transition flex items-center justify-center shadow-xs">
                    <i data-lucide="{{ $icons[$svc->name] ?? 'wrench' }}" class="w-6 h-6"></i>
                </div>
                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700 group-hover:bg-teal-50 group-hover:text-teal-800 transition">
                    {{ $svc->technicians_count }} {{ __('Fundis') }}
                </span>
            </div>

            <div>
                <h2 class="text-base font-bold text-slate-900 group-hover:text-teal-700 transition">
                    {{ __($svc->name) }}
                </h2>
                <p class="text-xs text-slate-500 mt-1 line-clamp-2 leading-relaxed">
                    {{ __($svc->description ?? 'Reliable technicians available for hire in this specialty.') }}
                </p>
            </div>

            <div class="flex items-center text-xs font-bold text-teal-700 pt-3 border-t border-slate-100 group-hover:translate-x-1 transition-transform">
                <span>{{ __('View verified specialists') }}</span>
                <i data-lucide="arrow-right" class="w-4 h-4 ml-1"></i>
            </div>
        </a>
        @empty
        <div class="col-span-full p-12 text-center bg-white rounded-3xl border border-slate-200">
            <i data-lucide="folder-search" class="w-12 h-12 mx-auto text-slate-300 mb-3"></i>
            <h3 class="text-base font-bold text-slate-800">{{ __('No Services Found') }}</h3>
            <p class="text-xs text-slate-500 mt-1">{{ __('Try adjusting your search keywords.') }}</p>
        </div>
        @endforelse
    </div>

</div>
@endsection
