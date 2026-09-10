@extends('layouts.app')

@section('title', __('Request Service') . ' — FUNDI')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

    <!-- Breadcrumbs -->
    <a href="{{ url()->previous() }}" class="inline-flex items-center text-xs font-bold text-slate-500 hover:text-teal-700 transition">
        <i data-lucide="arrow-left" class="w-4 h-4 mr-1"></i> {{ __('Back') }}
    </a>

    <!-- Main Card Form -->
    <div class="bg-white rounded-3xl p-6 sm:p-10 border border-slate-200 shadow-xs space-y-6">
        
        <div>
            <div class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-teal-50 text-teal-800 border border-teal-200 mb-2">
                <i data-lucide="send" class="w-3.5 h-3.5 mr-1.5"></i> {{ __('Post Service Request') }}
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">{{ __('Submit Service Request') }}</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                {{ __('Describe your problem clearly. Pay a nominal connection fee of TZS 2,000 to instantly unlock verified technician contact and receive formal quotation.') }}
            </p>
        </div>

        <!-- Selected Technician Preview if provided -->
        @if($technician)
        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-between">
            <div class="flex items-center space-x-3.5">
                <div class="w-12 h-12 rounded-xl bg-slate-900 text-teal-300 font-black text-sm flex items-center justify-center shadow-xs">
                    {{ $technician->initials }}
                </div>
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ __('Selected Technician') }}</span>
                    <h3 class="text-sm font-bold text-slate-900">{{ $technician->full_name }}</h3>
                    <p class="text-xs text-teal-700 font-semibold">{{ __($technician->technicianProfile->professional_title ?? 'Verified Specialist') }}</p>
                </div>
            </div>
            <span class="text-xs font-bold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-200 flex items-center space-x-1">
                <i data-lucide="check" class="w-3.5 h-3.5"></i>
                <span>{{ __('VERIFIED') }}</span>
            </span>
        </div>
        @endif

        <form id="requestForm" method="POST" action="{{ route('client.requests.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Hidden or Select Technician -->
            @if($technician)
                <input type="hidden" name="technician_id" value="{{ $technician->id }}">
            @else
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">{{ __('Select Technician') }}</label>
                    <select name="technician_id" required class="w-full py-3 px-4 rounded-xl border border-slate-200 text-xs sm:text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-teal-500 bg-slate-50/50">
                        <option value="">{{ __('Choose an available technician...') }}</option>
                        @foreach(\App\Models\User::where('role', 'technician')->where('status', 'active')->whereHas('subscription', fn($sq) => $sq->whereIn('status', ['active', 'free_trial'])->where('expires_at', '>', now()))->get() as $t)
                            <option value="{{ $t->id }}">{{ $t->full_name }} ({{ __($t->technicianProfile->professional_title ?? 'Technician') }})</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <!-- Service Category -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">{{ __('Service Category') }}</label>
                <select name="service_id" required class="w-full py-3 px-4 rounded-xl border border-slate-200 text-xs sm:text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-teal-500 bg-slate-50/50">
                    <option value="">{{ __('Choose service category...') }}</option>
                    @foreach($services as $svc)
                        <option value="{{ $svc->id }}" {{ (old('service_id') == $svc->id || ($selectedService && $selectedService->id == $svc->id)) ? 'selected' : '' }}>
                            {{ __($svc->name) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Problem Description -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">{{ __('Problem Description') }}</label>
                <textarea name="description" rows="4" required placeholder="{{ __('e.g. The main circuit breaker trips whenever the bedroom AC is turned on. Need full inspection and replacement of faulty breaker.') }}" class="w-full p-4 rounded-xl border border-slate-200 text-xs sm:text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-teal-500 bg-slate-50/50 leading-relaxed">{{ old('description') }}</textarea>
                <p class="text-[11px] text-slate-400 mt-1">{{ __('Provide accurate details so the technician can arrive with the right tools and materials.') }}</p>
            </div>

            <!-- Location & Scheduling -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="sm:col-span-1">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">{{ __('Location') }}</label>
                    <input type="text" name="location" value="{{ old('location', 'Dar es Salaam, Kinondoni') }}" required placeholder="{{ __('e.g. Mikocheni B, House #14') }}" class="w-full py-3 px-4 rounded-xl border border-slate-200 text-xs sm:text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-teal-500 bg-slate-50/50">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">{{ __('Preferred Date') }}</label>
                    <input type="date" name="preferred_date" value="{{ old('preferred_date', now()->addDay()->format('Y-m-d')) }}" required min="{{ now()->format('Y-m-d') }}" class="w-full py-3 px-4 rounded-xl border border-slate-200 text-xs sm:text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-teal-500 bg-slate-50/50">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">{{ __('Preferred Time') }}</label>
                    <input type="time" name="preferred_time" value="{{ old('preferred_time', '14:00') }}" class="w-full py-3 px-4 rounded-xl border border-slate-200 text-xs sm:text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-teal-500 bg-slate-50/50">
                </div>
            </div>

            <!-- Urgency Level -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">{{ __('Urgency') }}</label>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <label class="p-3 rounded-xl border border-slate-200 bg-slate-50 hover:bg-white cursor-pointer flex items-center space-x-2 text-xs font-bold text-slate-700 has-[:checked]:border-teal-600 has-[:checked]:bg-teal-50 has-[:checked]:text-teal-900 transition">
                        <input type="radio" name="urgency" value="low" class="text-teal-600 focus:ring-teal-500">
                        <span>{{ __('Low') }}</span>
                    </label>
                    <label class="p-3 rounded-xl border border-slate-200 bg-slate-50 hover:bg-white cursor-pointer flex items-center space-x-2 text-xs font-bold text-slate-700 has-[:checked]:border-teal-600 has-[:checked]:bg-teal-50 has-[:checked]:text-teal-900 transition">
                        <input type="radio" name="urgency" value="normal" checked class="text-teal-600 focus:ring-teal-500">
                        <span>{{ __('Standard') }}</span>
                    </label>
                    <label class="p-3 rounded-xl border border-slate-200 bg-slate-50 hover:bg-white cursor-pointer flex items-center space-x-2 text-xs font-bold text-slate-700 has-[:checked]:border-amber-500 has-[:checked]:bg-amber-50 has-[:checked]:text-amber-900 transition">
                        <input type="radio" name="urgency" value="high" class="text-amber-600 focus:ring-amber-500">
                        <span>{{ __('High Priority') }}</span>
                    </label>
                    <label class="p-3 rounded-xl border border-slate-200 bg-slate-50 hover:bg-white cursor-pointer flex items-center space-x-2 text-xs font-bold text-slate-700 has-[:checked]:border-rose-600 has-[:checked]:bg-rose-50 has-[:checked]:text-rose-900 transition">
                        <input type="radio" name="urgency" value="urgent" class="text-rose-600 focus:ring-rose-500">
                        <span>🚨 {{ __('Urgent') }}</span>
                    </label>
                </div>
            </div>

            <!-- Photos / Image Uploads -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">{{ __('Problem Photos (Optional)') }}</label>
                <div class="border-2 border-dashed border-slate-200 hover:border-teal-400 rounded-2xl p-6 text-center bg-slate-50/50 cursor-pointer transition">
                    <i data-lucide="image-plus" class="w-8 h-8 text-slate-400 mx-auto mb-2"></i>
                    <p class="text-xs font-bold text-slate-700">{{ __('Upload photos here (JPEG, PNG)') }}</p>
                    <input type="file" name="images[]" multiple accept="image/*" class="mt-3 text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100">
                </div>
            </div>

            <!-- Connection Fee Direct In-App Checkout Box (TZS 2,000) -->
            <div class="p-6 rounded-3xl bg-gradient-to-br from-slate-900 via-slate-950 to-teal-950 text-white space-y-4 border border-teal-500/20 shadow-xl" x-data="{ feeMethod: 'mpesa' }">
                <div class="flex items-center justify-between border-b border-white/10 pb-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-2xl bg-teal-400 text-slate-950 font-black flex items-center justify-center flex-shrink-0">
                            <i data-lucide="shield-check" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-white">{{ __('Technician Connection Fee (In-App Checkout)') }}</h3>
                            <p class="text-[11px] text-slate-300">{{ __('Pay securely within the app to dispatch your request directly to the technician.') }}</p>
                        </div>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <span class="text-2xl font-black text-teal-300 font-mono">TZS 2,000</span>
                        <span class="text-[10px] text-slate-400 block">{{ __('Connection Fee') }}</span>
                    </div>
                </div>

                <!-- Network Picker -->
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-300 mb-2">{{ __('Select Payment Method') }}</label>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                        <label :class="feeMethod === 'mpesa' ? 'bg-teal-500/20 border-teal-400 text-teal-300 ring-2 ring-teal-400/30' : 'bg-white/5 border-white/10 text-slate-300'" class="p-2.5 rounded-xl border cursor-pointer flex items-center space-x-2 text-xs font-bold transition">
                            <input type="radio" name="payment_method" value="mpesa" class="sr-only" x-model="feeMethod">
                            <span class="w-6 h-6 rounded-lg bg-rose-600 text-white flex items-center justify-center font-black text-[10px]">M</span>
                            <span>M-Pesa</span>
                        </label>

                        <label :class="feeMethod === 'tigopesa' ? 'bg-teal-500/20 border-teal-400 text-teal-300 ring-2 ring-teal-400/30' : 'bg-white/5 border-white/10 text-slate-300'" class="p-2.5 rounded-xl border cursor-pointer flex items-center space-x-2 text-xs font-bold transition">
                            <input type="radio" name="payment_method" value="tigopesa" class="sr-only" x-model="feeMethod">
                            <span class="w-6 h-6 rounded-lg bg-blue-600 text-white flex items-center justify-center font-black text-[10px]">T</span>
                            <span>Tigo Pesa</span>
                        </label>

                        <label :class="feeMethod === 'airtelmoney' ? 'bg-teal-500/20 border-teal-400 text-teal-300 ring-2 ring-teal-400/30' : 'bg-white/5 border-white/10 text-slate-300'" class="p-2.5 rounded-xl border cursor-pointer flex items-center space-x-2 text-xs font-bold transition">
                            <input type="radio" name="payment_method" value="airtelmoney" class="sr-only" x-model="feeMethod">
                            <span class="w-6 h-6 rounded-lg bg-red-600 text-white flex items-center justify-center font-black text-[10px]">A</span>
                            <span>Airtel</span>
                        </label>

                        <label :class="feeMethod === 'card' ? 'bg-teal-500/20 border-teal-400 text-teal-300 ring-2 ring-teal-400/30' : 'bg-white/5 border-white/10 text-slate-300'" class="p-2.5 rounded-xl border cursor-pointer flex items-center space-x-2 text-xs font-bold transition">
                            <input type="radio" name="payment_method" value="card" class="sr-only" x-model="feeMethod">
                            <span class="w-6 h-6 rounded-lg bg-slate-800 text-white flex items-center justify-center font-black text-[10px]">💳</span>
                            <span>{{ __('Card') }}</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-300 mb-1">{{ __('Payment Phone Number') }}</label>
                    <input type="text" name="payment_phone" value="{{ auth()->user()->phone }}" placeholder="{{ __('07XXXXXXXX or 2557XXXXXXXX') }}" class="w-full py-2.5 px-3 rounded-xl bg-white/10 border border-white/20 text-xs text-white placeholder-slate-400 font-mono font-bold focus:ring-2 focus:ring-teal-400 focus:outline-none">
                </div>

                <p class="text-[11px] text-slate-400">
                    💡 <em>{{ __('Note: Labour and material costs are settled directly with your technician upon completion (0% platform deduction).') }}</em>
                </p>

                <!-- Submit CTA -->
                <div class="pt-4 border-t border-white/10">
                    <button type="submit" class="w-full py-4 px-6 rounded-2xl bg-teal-500 hover:bg-teal-400 active:scale-[0.99] text-slate-950 font-black text-sm shadow-lg shadow-teal-500/25 transition flex items-center justify-center space-x-2">
                        <i data-lucide="check-circle" class="w-5 h-5"></i>
                        <span>{{ __('Pay Fee TZS 2,000 & Submit Request Instantly') }}</span>
                    </button>
                </div>
            </div>

        </form>

    </div>

</div>
@endsection
