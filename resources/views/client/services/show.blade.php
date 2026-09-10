@extends('layouts.app')

@section('title', __($service->name) . ' ' . __('Technicians') . ' — FUNDI')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-8">

    <!-- Back button & Service Header -->
    <div class="space-y-4">
        <a href="{{ route('client.services.index') }}" class="inline-flex items-center text-xs font-bold text-slate-500 hover:text-teal-700 transition">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-1"></i> {{ __('Back to All Services') }}
        </a>

        <div class="p-6 sm:p-8 rounded-3xl bg-white border border-slate-200 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-2">
                <div class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-full bg-teal-50 text-teal-700 text-xs font-bold border border-teal-200">
                    <i data-lucide="tag" class="w-3.5 h-3.5"></i>
                    <span>{{ __('Service Category') }}</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">{{ __($service->name) }}</h1>
                <p class="text-xs sm:text-sm text-slate-500 font-medium max-w-2xl leading-relaxed">
                    {{ __($service->description ?? 'Connect directly with verified and background-checked technicians for high-quality repairs and installation.') }}
                </p>
            </div>

            <div class="flex items-center space-x-3 flex-shrink-0">
                <a href="{{ route('client.requests.create', ['service_id' => $service->id]) }}" class="px-5 py-3 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold shadow-xs active:scale-[0.98] transition flex items-center space-x-2">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    <span>{{ __('Post Service Request') }} ({{ __($service->name) }})</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Technicians Grid -->
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg sm:text-xl font-black text-slate-900 tracking-tight">{{ __('Verified Technicians Near You') }} ({{ __($service->name) }})</h2>
                <p class="text-xs text-slate-500">{{ __('Active and verified artisans ready for immediate hire') }}</p>
            </div>
            <a href="{{ route('client.technicians.index', ['search' => $service->name]) }}" class="text-xs font-bold text-teal-700 hover:underline">
                {{ __('View all technicians') }} &rarr;
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @forelse($technicians as $tech)
            @php $profile = $tech->technicianProfile; @endphp
            <div class="bg-white rounded-2xl p-5 border border-slate-200 hover:border-teal-500 hover:shadow-card transition flex flex-col justify-between space-y-4">
                
                <div class="flex items-start space-x-3.5">
                    <div class="relative flex-shrink-0">
                        <div class="w-14 h-14 rounded-2xl bg-slate-900 text-teal-300 flex items-center justify-center font-black text-base shadow-xs">
                            {{ $tech->initials }}
                        </div>
                        <div class="absolute -bottom-1 -right-1 w-5 h-5 rounded-full bg-emerald-500 text-white flex items-center justify-center border-2 border-white shadow-xs" title="{{ __('Verified Fundi') }}">
                            <i data-lucide="check" class="w-3 h-3 stroke-[3]"></i>
                        </div>
                    </div>

                    <div class="flex-1 min-w-0">
                        <div class="flex items-center space-x-1.5">
                            <h3 class="text-sm font-bold text-slate-900 truncate">{{ $tech->full_name }}</h3>
                        </div>
                        <p class="text-xs font-semibold text-teal-700 truncate mt-0.5">
                            {{ __($profile->professional_title ?? ($service->name . ' Technician')) }}
                        </p>
                        
                        <div class="flex items-center space-x-2 mt-1.5 text-xs text-slate-500">
                            @if(($profile->total_reviews ?? 0) > 0)
                            <span class="flex items-center text-amber-500 font-bold">
                                <i data-lucide="star" class="w-3.5 h-3.5 fill-amber-400 stroke-amber-400 mr-1"></i>
                                {{ number_format($profile->average_rating, 1) }}
                            </span>
                            <span>•</span>
                            <span>{{ $profile->total_reviews }} {{ __('reviews') }}</span>
                            @else
                            <span class="font-bold text-teal-700 bg-teal-50 px-2 py-0.5 rounded text-[10px] border border-teal-100">
                                {{ __('New') }} (0 {{ __('reviews') }})
                            </span>
                            @endif
                        </div>
                    </div>
                </div>

                <p class="text-xs text-slate-500 line-clamp-2 leading-relaxed font-normal">
                    {{ $profile->bio ?? 'Professional ' . strtolower($service->name) . ' specialist with extensive field experience and guaranteed quality service.' }}
                </p>

                <div class="flex items-center justify-between text-xs pt-3 border-t border-slate-100">
                    <div class="flex items-center text-slate-500 truncate">
                        <i data-lucide="map-pin" class="w-3.5 h-3.5 mr-1 text-slate-400"></i>
                        <span>{{ $profile->location ?? 'Dar es Salaam, Kinondoni' }}</span>
                    </div>
                    <span class="text-xs font-bold text-slate-700">{{ $profile->years_experience ?? 5 }}+ {{ __('Yrs Exp') }}</span>
                </div>

                <div class="grid grid-cols-2 gap-2 pt-1">
                    <a href="{{ route('client.technicians.show', $tech->id) }}" class="w-full py-2.5 px-3 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-800 text-xs font-bold text-center transition">
                        {{ __('View Profile') }}
                    </a>
                    <a href="{{ route('client.requests.create', ['technician_id' => $tech->id, 'service_id' => $service->id]) }}" class="w-full py-2.5 px-3 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold text-center transition shadow-xs">
                        {{ __('Request Fundi') }}
                    </a>
                </div>

            </div>
            @empty
            <div class="col-span-full p-12 text-center bg-white rounded-3xl border border-slate-200">
                <i data-lucide="users" class="w-12 h-12 mx-auto text-slate-300 mb-3"></i>
                <h3 class="text-base font-bold text-slate-800">{{ __('No Verified Technicians Yet') }}</h3>
                <p class="text-xs text-slate-500 mt-1">{{ __('There are currently no verified technicians listed in this category.') }}</p>
            </div>
            @endforelse
        </div>

        <div class="mt-4">
            {{ $technicians->links() }}
        </div>
    </div>

</div>
@endsection
