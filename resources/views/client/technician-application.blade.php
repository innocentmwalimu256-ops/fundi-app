@extends('layouts.app')

@section('title', __('Join as a Verified Fundi') . ' — FUNDI')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

    <!-- Header Banner -->
    <div class="bg-gradient-to-r from-slate-950 via-slate-900 to-teal-950 rounded-3xl p-6 sm:p-10 text-white shadow-xl space-y-3 border border-slate-800">
        <div class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-full bg-teal-500/20 text-teal-300 text-xs font-bold border border-teal-500/30">
            <i data-lucide="award" class="w-3.5 h-3.5"></i>
            <span>{{ __('Artisan Verification') }}</span>
        </div>
        <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white">{{ __('Join the FUNDI Artisan Network') }}</h1>
        <p class="text-xs sm:text-sm text-slate-300 max-w-2xl leading-relaxed">
            {{ __('Get new clients, receive service requests directly, send official quotations, and keep 100% of your labour earnings (0% Platform Fee).') }}
        </p>
    </div>

    @if($application && $application->status === 'pending')
    <div class="p-5 rounded-2xl bg-amber-50 border border-amber-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center flex-shrink-0">
                <i data-lucide="clock" class="w-5 h-5"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-amber-950">{{ __('Your Application is Under Review') }}</h3>
                <p class="text-xs text-amber-800">{{ __('Submitted on') }} {{ $application->created_at->format('d M Y') }}. {{ __('Your credentials and documents are being vetted.') }}</p>
            </div>
        </div>
        <a href="{{ route('client.technician-application.status') }}" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs rounded-xl shadow-xs self-start sm:self-auto transition">
            {{ __('Check Application Status') }} &rarr;
        </a>
    </div>
    @endif

    <!-- Application Form Card -->
    <div class="bg-white rounded-3xl p-6 sm:p-10 border border-slate-200 shadow-xs space-y-6">
        
        <form method="POST" action="{{ route('client.become-technician.submit') }}" enctype="multipart/form-data" class="space-y-8">
            @csrf

            <!-- Section 1: Professional Information -->
            <div class="space-y-4">
                <div class="flex items-center space-x-3 border-b border-slate-100 pb-3">
                    <span class="w-7 h-7 rounded-xl bg-teal-600 text-white text-xs font-bold flex items-center justify-center shadow-xs">1</span>
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">{{ __('Your Expertise & Primary Trade') }}</h2>
                        <p class="text-[11px] text-slate-500">{{ __('Choose your primary category and professional title') }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">{{ __('Professional Title') }}</label>
                        <input type="text" name="professional_title" value="{{ old('professional_title', $user->technicianProfile->professional_title ?? '') }}" required placeholder="{{ __('e.g. Certified Electrical Technician & Solar Specialist') }}" class="w-full py-3 px-4 rounded-xl border border-slate-200 text-xs sm:text-sm text-slate-900 focus:ring-2 focus:ring-teal-500 bg-slate-50/50">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">{{ __('Primary Service Category') }}</label>
                        <select name="service_id" required class="w-full py-3 px-4 rounded-xl border border-slate-200 text-xs sm:text-sm text-slate-900 focus:ring-2 focus:ring-teal-500 bg-slate-50/50">
                            <option value="">{{ __('Choose service category...') }}</option>
                            @foreach($services as $svc)
                                <option value="{{ $svc->id }}">{{ __($svc->name) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">{{ __('Years of Experience') }}</label>
                        <input type="number" name="years_experience" value="{{ old('years_experience') }}" required min="0" max="50" placeholder="3" class="w-full py-3 px-4 rounded-xl border border-slate-200 text-xs sm:text-sm text-slate-900 focus:ring-2 focus:ring-teal-500 bg-slate-50/50">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">{{ __('Base Location') }}</label>
                        <input type="text" name="location" value="{{ old('location') }}" required placeholder="{{ __('e.g. Kinondoni, Dar es Salaam') }}" class="w-full py-3 px-4 rounded-xl border border-slate-200 text-xs sm:text-sm text-slate-900 focus:ring-2 focus:ring-teal-500 bg-slate-50/50">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">{{ __('Coverage Areas') }}</label>
                        <input type="text" name="service_area" value="{{ old('service_area') }}" placeholder="{{ __('e.g. Kinondoni, Ilala, Temeke') }}" class="w-full py-3 px-4 rounded-xl border border-slate-200 text-xs sm:text-sm text-slate-900 focus:ring-2 focus:ring-teal-500 bg-slate-50/50">
                    </div>
                </div>
            </div>

            <!-- Section 2: Bio & Skills -->
            <div class="space-y-4 pt-2">
                <div class="flex items-center space-x-3 border-b border-slate-100 pb-3">
                    <span class="w-7 h-7 rounded-xl bg-teal-600 text-white text-xs font-bold flex items-center justify-center shadow-xs">2</span>
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">{{ __('Professional Bio & Skills') }}</h2>
                        <p class="text-[11px] text-slate-500">{{ __('Explain your expertise and background to potential clients') }}</p>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">{{ __('Professional Bio & Scope') }}</label>
                    <textarea name="bio" rows="4" required placeholder="{{ __('Describe your background, training, typical projects, and commitment to quality work...') }}" class="w-full p-4 rounded-xl border border-slate-200 text-xs sm:text-sm text-slate-900 focus:ring-2 focus:ring-teal-500 bg-slate-50/50 leading-relaxed">{{ old('bio') }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">{{ __('Technical Skills (Comma separated)') }}</label>
                    <input type="text" name="skills" value="{{ old('skills') }}" required placeholder="{{ __('e.g. House Wiring, Circuit Breakers, Solar Power') }}" class="w-full py-3 px-4 rounded-xl border border-slate-200 text-xs sm:text-sm text-slate-900 focus:ring-2 focus:ring-teal-500 bg-slate-50/50">
                </div>
            </div>

            <!-- Section 3: Verification Documents -->
            <div class="space-y-4 pt-2">
                <div class="flex items-center space-x-3 border-b border-slate-100 pb-3">
                    <span class="w-7 h-7 rounded-xl bg-teal-600 text-white text-xs font-bold flex items-center justify-center shadow-xs">3</span>
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">{{ __('Verification Documents (ID & Certificates)') }}</h2>
                        <p class="text-[11px] text-slate-500">{{ __('Upload valid credentials to qualify for the VERIFIED badge') }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="p-4 rounded-2xl border-2 border-dashed border-slate-200 hover:border-teal-400 bg-slate-50/50 transition space-y-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">{{ __('NIDA ID / Voter ID / Passport') }}</label>
                        <p class="text-[11px] text-slate-400">PDF, JPG au PNG (Max 5MB)</p>
                        <input type="file" name="id_document" accept=".pdf,.jpg,.jpeg,.png" class="w-full text-xs text-slate-600 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100 cursor-pointer">
                    </div>

                    <div class="p-4 rounded-2xl border-2 border-dashed border-slate-200 hover:border-teal-400 bg-slate-50/50 transition space-y-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">{{ __('VETA / Vocational Certificate (Optional)') }}</label>
                        <p class="text-[11px] text-slate-400">PDF, JPG au PNG (Max 5MB)</p>
                        <input type="file" name="certificate_document" accept=".pdf,.jpg,.jpeg,.png" class="w-full text-xs text-slate-600 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100 cursor-pointer">
                    </div>
                </div>
            </div>

            <!-- Submit CTA Button -->
            <div class="pt-4 border-t border-slate-100">
                <button type="submit" class="w-full py-4 px-6 rounded-2xl bg-teal-600 hover:bg-teal-700 active:scale-[0.99] text-white font-black text-sm shadow-lg shadow-teal-600/20 transition flex items-center justify-center space-x-2 cursor-pointer">
                    <i data-lucide="send" class="w-4 h-4"></i>
                    <span>{{ __('Submit Verification Application') }}</span>
                </button>
            </div>

        </form>

    </div>

</div>
@endsection
