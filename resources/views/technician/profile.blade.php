@extends('layouts.app')

@section('title', __('Professional Profile Settings') . ' — FUNDI')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">{{ __('Technician Profile & Coverage') }}</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">{{ __('Manage your marketplace presentation, service categories, and coverage districts') }}</p>
        </div>
        <div class="flex items-center space-x-2.5 self-start sm:self-auto">
            <a href="{{ route('technician.portfolios.index') }}" class="btn-tap px-3.5 py-2 rounded-xl bg-teal-50 text-teal-800 border border-teal-200 text-xs font-bold hover:bg-teal-100 transition flex items-center space-x-1.5 shadow-xs">
                <i data-lucide="image" class="w-4 h-4"></i>
                <span>{{ __('Portfolio Works') }}</span>
            </a>
            <a href="{{ route('client.technicians.show', $technician->id) }}" target="_blank" class="btn-tap px-3.5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-teal-300 text-xs font-bold transition flex items-center space-x-1.5 shadow-xs">
                <span>{{ __('Public Profile') }}</span>
                <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
            </a>
        </div>
    </div>

    <!-- Main Card -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs space-y-6">
        
        <form method="POST" action="{{ route('technician.profile.update') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- User Avatar & Identity Header -->
            <div class="flex items-center space-x-4 p-4 rounded-2xl bg-slate-50 border border-slate-200/80">
                <div class="w-16 h-16 rounded-2xl bg-slate-900 text-teal-300 font-black text-xl flex items-center justify-center shadow-xs flex-shrink-0">
                    {{ $technician->initials }}
                </div>
                <div class="min-w-0 flex-1">
                    <h3 class="text-sm font-bold text-slate-900 truncate">{{ $technician->full_name }}</h3>
                    <p class="text-xs text-teal-700 font-semibold">{{ __($profile->professional_title ?? 'Master Technician') }} &bull; {{ $profile->location ?? 'Dar es Salaam' }}</p>
                    <p class="text-[11px] text-slate-400 mt-0.5">{{ __('Rating:') }} {{ number_format($profile->average_rating ?? 0, 1) }}★ &bull; {{ $profile->completed_jobs_count ?? 0 }} {{ __('jobs completed') }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">{{ __('Full Name') }}</label>
                    <input type="text" name="full_name" value="{{ old('full_name', $technician->full_name) }}" required class="w-full py-2.5 px-4 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-teal-500 focus:outline-none bg-slate-50/50">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">{{ __('Phone Number') }}</label>
                    <input type="text" name="phone" value="{{ old('phone', $technician->phone) }}" required class="w-full py-2.5 px-4 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-teal-500 focus:outline-none bg-slate-50/50">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">{{ __('Professional Title / Trade') }}</label>
                    <input type="text" name="professional_title" value="{{ old('professional_title', $profile->professional_title ?? '') }}" required placeholder="{{ __('e.g. Master Electrician & AC Specialist') }}" class="w-full py-2.5 px-4 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-teal-500 focus:outline-none bg-slate-50/50">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">{{ __('Years of Experience') }}</label>
                    <input type="number" name="years_experience" value="{{ old('years_experience', $profile->years_experience ?? 3) }}" required min="0" max="50" class="w-full py-2.5 px-4 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-teal-500 focus:outline-none bg-slate-50/50">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">{{ __('Primary Base Location') }}</label>
                <input type="text" name="location" value="{{ old('location', $profile->location ?? 'Dar es Salaam') }}" required placeholder="{{ __('e.g. Kinondoni, Dar es Salaam') }}" class="w-full py-2.5 px-4 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-teal-500 focus:outline-none bg-slate-50/50">
            </div>

            <!-- Multi-District Service Coverage -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">{{ __('Service Coverage Districts in Dar es Salaam') }}</label>
                <div class="grid grid-cols-2 sm:grid-cols-5 gap-2.5">
                    @foreach($darDistricts as $dist)
                    <label class="p-3 rounded-xl border border-slate-200 bg-slate-50 hover:bg-teal-50/40 hover:border-teal-300 flex items-center space-x-2 text-xs font-bold cursor-pointer transition select-none">
                        <input type="checkbox" name="service_areas[]" value="{{ $dist }}" {{ in_array($dist, $selectedAreas) ? 'checked' : '' }} class="w-4 h-4 rounded text-teal-600 border-slate-300 focus:ring-teal-500">
                        <span>{{ $dist }}</span>
                    </label>
                    @endforeach
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">{{ __('Professional Bio & Scope') }}</label>
                <textarea name="bio" rows="4" required class="w-full p-4 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-teal-500 focus:outline-none bg-slate-50/50 leading-relaxed">{{ old('bio', $profile->bio ?? '') }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">{{ __('Technical Skills (Comma separated)') }}</label>
                @php $skillsStr = !empty($profile->skills) ? implode(', ', $profile->skills) : ''; @endphp
                <input type="text" name="skills" value="{{ old('skills', $skillsStr) }}" required placeholder="{{ __('e.g. House Wiring, Circuit Breakers, Inverters, Conduit Piping') }}" class="w-full py-2.5 px-4 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-teal-500 focus:outline-none bg-slate-50/50">
            </div>

            <!-- Services Offered Picker -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">{{ __('Offered Trade Categories') }}</label>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5">
                    @foreach($services as $svc)
                    <label class="p-3 rounded-xl border border-slate-200 bg-slate-50 hover:bg-teal-50/40 hover:border-teal-300 flex items-center space-x-2 text-xs font-semibold cursor-pointer transition select-none">
                        <input type="checkbox" name="services[]" value="{{ $svc->id }}" {{ in_array($svc->id, $selectedServices) ? 'checked' : '' }} class="w-4 h-4 rounded text-teal-600 border-slate-300 focus:ring-teal-500">
                        <span class="font-bold text-slate-800">{{ __($svc->name) }}</span>
                    </label>
                    @endforeach
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end">
                <button type="submit" class="btn-tap px-6 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs shadow-xs transition flex items-center space-x-2">
                    <i data-lucide="check" class="w-4 h-4"></i>
                    <span>{{ __('Save Profile & Coverage Settings') }}</span>
                </button>
            </div>

        </form>

    </div>

</div>
@endsection
