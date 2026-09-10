@extends('layouts.app')

@section('title', __('Professional Profile') . ' - FUNDI')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

    <div class="bg-white rounded-3xl p-6 sm:p-10 border border-slate-200 shadow-sm space-y-6">
        
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">{{ __('Professional Profile') }}</h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">{{ __('Manage your marketplace presentation, service categories, and coverage districts') }}</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('technician.portfolios.index') }}" class="px-3.5 py-2 rounded-xl bg-teal-50 text-teal-800 border border-teal-200 text-xs font-bold hover:bg-teal-100 transition flex items-center">
                    <i data-lucide="image" class="w-3.5 h-3.5 mr-1"></i>
                    <span>{{ __('Portfolio Projects') }}</span>
                </a>
                <a href="{{ route('client.technicians.show', $technician->id) }}" target="_blank" class="text-xs font-bold text-brand-700 hover:underline flex items-center">
                    <span>{{ __('Public View') }}</span>
                    <i data-lucide="external-link" class="w-3.5 h-3.5 ml-1"></i>
                </a>
            </div>
        </div>

        <form method="POST" action="{{ route('technician.profile.update') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Avatar -->
            <div class="flex items-center space-x-4">
                <div class="w-16 h-16 rounded-2xl bg-brand-900 text-white font-black text-xl flex items-center justify-center shadow-md">
                    {{ $technician->initials }}
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">{{ __('Profile Photo') }}</label>
                    <input type="file" name="avatar" accept="image/*" class="text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-brand-50 file:text-brand-700">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">{{ __('Full Name') }}</label>
                    <input type="text" name="full_name" value="{{ old('full_name', $technician->full_name) }}" required class="w-full py-2.5 px-4 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-brand-500 bg-slate-50">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">{{ __('Phone Number') }}</label>
                    <input type="text" name="phone" value="{{ old('phone', $technician->phone) }}" required class="w-full py-2.5 px-4 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-brand-500 bg-slate-50">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">{{ __('Professional Title') }}</label>
                    <input type="text" name="professional_title" value="{{ old('professional_title', $profile->professional_title ?? '') }}" required class="w-full py-2.5 px-4 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-brand-500 bg-slate-50">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">{{ __('Years of Experience') }}</label>
                    <input type="number" name="years_experience" value="{{ old('years_experience', $profile->years_experience ?? 3) }}" required min="0" max="50" class="w-full py-2.5 px-4 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-brand-500 bg-slate-50">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">{{ __('Primary Base Location') }}</label>
                <input type="text" name="location" value="{{ old('location', $profile->location ?? 'Dar es Salaam') }}" required class="w-full py-2.5 px-4 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-brand-500 bg-slate-50">
            </div>

            <!-- Multi-District Service Coverage (Feature 4) -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">{{ __('Service Coverage Districts in Dar es Salaam') }}</label>
                <div class="grid grid-cols-2 sm:grid-cols-5 gap-2">
                    @foreach($darDistricts as $dist)
                    <label class="p-3 rounded-xl border border-slate-200 bg-slate-50 hover:bg-white flex items-center space-x-2 text-xs font-bold cursor-pointer">
                        <input type="checkbox" name="service_areas[]" value="{{ $dist }}" {{ in_array($dist, $selectedAreas) ? 'checked' : '' }} class="w-4 h-4 rounded text-brand-700 border-slate-300 focus:ring-brand-500">
                        <span>{{ $dist }}</span>
                    </label>
                    @endforeach
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">{{ __('Professional Bio & Scope') }}</label>
                <textarea name="bio" rows="4" required class="w-full p-4 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-brand-500 bg-slate-50 leading-relaxed">{{ old('bio', $profile->bio ?? '') }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">{{ __('Technical Skills (Comma separated)') }}</label>
                @php $skillsStr = !empty($profile->skills) ? implode(', ', $profile->skills) : ''; @endphp
                <input type="text" name="skills" value="{{ old('skills', $skillsStr) }}" required class="w-full py-2.5 px-4 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-brand-500 bg-slate-50">
            </div>

            <!-- Services Offered Picker -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">{{ __('Offered Trade Categories') }}</label>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                    @foreach($services as $svc)
                    <label class="p-3 rounded-xl border border-slate-200 bg-slate-50 hover:bg-white flex items-center space-x-2 text-xs font-semibold cursor-pointer">
                        <input type="checkbox" name="services[]" value="{{ $svc->id }}" {{ in_array($svc->id, $selectedServices) ? 'checked' : '' }} class="w-4 h-4 rounded text-brand-700 border-slate-300 focus:ring-brand-500">
                        <span>{{ __($svc->name) }}</span>
                    </label>
                    @endforeach
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100">
                <button type="submit" class="px-6 py-3 rounded-xl bg-brand-700 hover:bg-brand-800 text-white font-bold text-xs shadow transition">
                    {{ __('Save Profile & Coverage Settings') }}
                </button>
            </div>

        </form>

    </div>

</div>
@endsection
