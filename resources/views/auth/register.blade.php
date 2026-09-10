@extends('layouts.app')

@section('title', __('Create Account') . ' — FUNDI')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center py-8 sm:py-12 px-4 sm:px-6 lg:px-8" x-data="{ intent: 'client' }">
    <div class="max-w-xl w-full bg-white rounded-3xl p-6 sm:p-10 shadow-card border border-[#E5E7EB] space-y-6">
        
        <!-- Header (Section 20) -->
        <div class="space-y-1">
            <h1 class="text-2xl font-black text-navy-900 tracking-tight" x-text="intent === 'technician' ? '{{ __('Join as a Verified Fundi') }}' : '{{ __('Create your FUNDI account') }}'"></h1>
            <p class="text-xs text-[#667085]" x-text="intent === 'technician' ? '{{ __('Submit your credentials, get verified, and connect with clients daily.') }}' : '{{ __('Find, request, and rate verified technicians across Tanzania.') }}'"></p>
        </div>

        <!-- Role Selector Switcher -->
        <div class="space-y-1.5">
            <label class="block text-xs font-bold text-navy-900">{{ __('Account Type') }}</label>
            <div class="grid grid-cols-2 gap-3 text-xs">
                <button type="button" @click="intent = 'client'" 
                    :class="intent === 'client' ? 'border-teal-500 bg-[#F0FDFB] text-teal-900 font-bold ring-1 ring-teal-500' : 'border-[#E5E7EB] bg-[#F7F8F7] text-[#667085] font-semibold'" 
                    class="p-3.5 rounded-xl border text-left transition flex items-center space-x-2.5">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0" :class="intent === 'client' ? 'bg-teal-500 text-white' : 'bg-slate-200 text-[#667085]'">
                        <i data-lucide="user" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <p class="font-bold">{{ __('Hire a Fundi') }}</p>
                        <span class="text-[11px] font-normal text-[#667085]">{{ __('Client Account') }}</span>
                    </div>
                </button>

                <button type="button" @click="intent = 'technician'" 
                    :class="intent === 'technician' ? 'border-teal-500 bg-[#F0FDFB] text-teal-900 font-bold ring-1 ring-teal-500' : 'border-[#E5E7EB] bg-[#F7F8F7] text-[#667085] font-semibold'" 
                    class="p-3.5 rounded-xl border text-left transition flex items-center space-x-2.5">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0" :class="intent === 'technician' ? 'bg-teal-500 text-white' : 'bg-slate-200 text-[#667085]'">
                        <i data-lucide="wrench" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <p class="font-bold">{{ __('Become a Fundi') }}</p>
                        <span class="text-[11px] font-normal text-[#667085]">{{ __('Technician Account') }}</span>
                    </div>
                </button>
            </div>
        </div>

        @if($errors->any())
            <div class="p-3.5 text-xs text-rose-900 rounded-xl bg-rose-50 border border-rose-200">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register.submit') }}" data-turbo="false" class="space-y-4">
            @csrf
            <input type="hidden" name="intent" :value="intent">

            <!-- Full Name -->
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-navy-900">{{ __('Full Name') }}</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-[#98A2B3]">
                        <i data-lucide="user" class="w-4 h-4"></i>
                    </span>
                    <input type="text" name="full_name" value="{{ old('full_name') }}" required placeholder="e.g. John Mwakyusa" class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-[#E5E7EB] text-xs font-semibold text-navy-900 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 bg-[#F7F8F7]">
                </div>
            </div>

            <!-- Email -->
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-navy-900">{{ __('Email Address') }}</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-[#98A2B3]">
                        <i data-lucide="mail" class="w-4 h-4"></i>
                    </span>
                    <input type="email" name="email" value="{{ old('email') }}" required placeholder="john@example.com" class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-[#E5E7EB] text-xs font-semibold text-navy-900 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 bg-[#F7F8F7]">
                </div>
            </div>

            <!-- Phone -->
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-navy-900">{{ __('Phone Number') }}</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-[#98A2B3]">
                        <i data-lucide="phone" class="w-4 h-4"></i>
                    </span>
                    <input type="tel" name="phone" value="{{ old('phone') }}" required placeholder="0712345678" class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-[#E5E7EB] text-xs font-semibold text-navy-900 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 bg-[#F7F8F7]">
                </div>
            </div>

            <!-- Password & Confirmation -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-navy-900">{{ __('Password') }}</label>
                    <input type="password" name="password" required placeholder="••••••••" class="w-full px-3.5 py-2.5 rounded-xl border border-[#E5E7EB] text-xs font-semibold text-navy-900 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 bg-[#F7F8F7]">
                </div>
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-navy-900">{{ __('Confirm Password') }}</label>
                    <input type="password" name="password_confirmation" required placeholder="••••••••" class="w-full px-3.5 py-2.5 rounded-xl border border-[#E5E7EB] text-xs font-semibold text-navy-900 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 bg-[#F7F8F7]">
                </div>
            </div>

            <!-- Submit Button -->
            <div class="pt-2">
                <button type="submit" class="w-full py-3.5 px-4 rounded-xl bg-teal-500 hover:bg-teal-600 text-white font-bold text-xs shadow-xs active:scale-[0.98] transition flex items-center justify-center space-x-2">
                    <span x-text="intent === 'technician' ? '{{ __('Continue to Technician Verification') }} →' : '{{ __('Create Account') }}'"></span>
                </button>
            </div>
        </form>

        <div class="pt-3 border-t border-[#E5E7EB] text-center text-xs text-[#667085]">
            {{ __('Already registered on FUNDI?') }} 
            <a href="{{ route('login') }}" class="font-bold text-teal-600 hover:text-teal-700 underline ml-0.5">
                {{ __('Sign In') }}
            </a>
        </div>

    </div>
</div>
@endsection
