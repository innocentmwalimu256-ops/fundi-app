@extends('layouts.app')

@section('title', __('Saved Technicians') . ' — FUNDI')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">{{ __('Saved Technicians') }}</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">{{ __('Quick access to your favorite skilled fundis') }}</p>
        </div>
        <span class="text-xs font-bold text-slate-500">{{ $favorites->count() }} {{ __('Saved') }}</span>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse($favorites as $fav)
        @php 
            $tech = $fav->technician;
            $profile = $tech->technicianProfile;
        @endphp
        <div class="bg-white rounded-3xl p-5 border border-slate-200 hover:border-teal-300 hover:shadow-lg transition flex flex-col justify-between space-y-4">
            
            <div class="flex items-start justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 rounded-2xl bg-slate-900 text-teal-300 font-bold text-base flex items-center justify-center shadow-xs">
                        {{ $tech->initials }}
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">{{ $tech->full_name }}</h3>
                        <p class="text-xs text-teal-700 font-medium">{{ __($profile->professional_title ?? 'Verified Fundi') }}</p>
                        <div class="flex items-center text-xs text-slate-500 mt-1">
                            @if(($profile->average_rating ?? 0) > 0)
                                <span class="text-amber-500 font-bold flex items-center mr-1">
                                    <i data-lucide="star" class="w-3 h-3 fill-amber-400 stroke-amber-400 mr-0.5"></i>
                                    {{ number_format($profile->average_rating, 1) }}
                                </span>
                                <span>({{ $profile->total_reviews ?? 0 }} {{ __('reviews') }})</span>
                            @else
                                <span class="px-2 py-0.5 rounded bg-teal-50 text-teal-700 font-bold text-[10px] border border-teal-100">{{ __('New') }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('technicians.favorite', $tech->id) }}">
                    @csrf
                    <button type="submit" class="p-2 rounded-full bg-rose-50 text-rose-600 border border-rose-200 hover:bg-rose-100 transition" title="{{ __('Remove from favorites') }}">
                        <i data-lucide="heart" class="w-4 h-4 fill-rose-600"></i>
                    </button>
                </form>
            </div>

            <div class="flex items-center justify-between text-xs text-slate-500 pt-2 border-t border-slate-100">
                <span class="flex items-center">
                    <i data-lucide="map-pin" class="w-3.5 h-3.5 mr-1 text-slate-400"></i> {{ $profile->location ?? 'Dar es Salaam' }}
                </span>
                <span class="font-bold text-slate-700">{{ $profile->years_experience ?? 2 }}+ {{ __('Yrs Exp') }}</span>
            </div>

            <div class="grid grid-cols-2 gap-2 pt-1">
                <a href="{{ route('client.technicians.show', $tech->id) }}" class="w-full py-2.5 px-3 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-800 text-xs font-bold text-center transition">
                    {{ __('View Profile') }}
                </a>
                <a href="{{ route('client.requests.create', ['technician_id' => $tech->id]) }}" class="w-full py-2.5 px-3 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold text-center transition shadow-xs">
                    {{ __('Request Fundi') }}
                </a>
            </div>

        </div>
        @empty
        <div class="col-span-full p-12 text-center bg-white rounded-3xl border border-slate-200 space-y-3">
            <div class="w-14 h-14 rounded-2xl bg-rose-50 text-rose-500 flex items-center justify-center mx-auto">
                <i data-lucide="heart" class="w-7 h-7"></i>
            </div>
            <h3 class="text-base font-bold text-slate-800">{{ __('No Saved Technicians Yet') }}</h3>
            <p class="text-xs text-slate-500 max-w-sm mx-auto">
                {{ __('When you find trustworthy fundis you like, click the heart icon on their profile to save them here.') }}
            </p>
            <div class="pt-2">
                <a href="{{ route('client.technicians.index') }}" class="inline-flex items-center px-5 py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-xl shadow-xs transition">
                    {{ __('Find Technicians') }}
                </a>
            </div>
        </div>
        @endforelse
    </div>

</div>
@endsection
