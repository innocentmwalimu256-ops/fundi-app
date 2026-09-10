@extends('layouts.app')

@section('title', __('Create Account') . ' — FUNDI')

@section('content')
<div class="min-h-screen flex flex-col justify-between items-center py-6 sm:py-10 px-4 sm:px-6 lg:px-8 bg-[#F7F8F7] relative" x-data="{ intent: 'client' }">
    
    <!-- Top Right Floating Language Switcher (SW | EN) -->
    <div class="w-full max-w-lg flex justify-end">
        <div class="relative" x-data="{ langOpen: false }">
            <button @click="langOpen = !langOpen" class="flex items-center space-x-1.5 px-3 py-1.5 rounded-xl border border-[#E5E7EB] bg-white hover:bg-slate-50 text-xs font-bold text-navy-900 transition shadow-subtle cursor-pointer">
                <i data-lucide="globe" class="w-3.5 h-3.5 text-teal-600"></i>
                <span>{{ app()->getLocale() === 'en' ? 'EN' : 'SW' }}</span>
                <i data-lucide="chevron-down" class="w-3 h-3 text-[#98A2B3]"></i>
            </button>
            <div x-show="langOpen" @click.away="langOpen = false" x-cloak class="absolute right-0 mt-2 w-32 bg-white rounded-xl shadow-elevated border border-[#E5E7EB] py-1.5 z-50 text-xs font-semibold">
                <a href="{{ route('language.switch', 'sw') }}" class="flex items-center justify-between px-3.5 py-2 hover:bg-[#F0FDFB] hover:text-teal-700 transition {{ app()->getLocale() === 'sw' ? 'text-teal-700 font-bold bg-[#F0FDFB]' : 'text-[#111827]' }}">
                    <span>Kiswahili</span>
                    @if(app()->getLocale() === 'sw') <i data-lucide="check" class="w-3.5 h-3.5 text-teal-600"></i> @endif
                </a>
                <a href="{{ route('language.switch', 'en') }}" class="flex items-center justify-between px-3.5 py-2 hover:bg-[#F0FDFB] hover:text-teal-700 transition {{ app()->getLocale() === 'en' ? 'text-teal-700 font-bold bg-[#F0FDFB]' : 'text-[#111827]' }}">
                    <span>English</span>
                    @if(app()->getLocale() === 'en') <i data-lucide="check" class="w-3.5 h-3.5 text-teal-600"></i> @endif
                </a>
            </div>
        </div>
    </div>

    <!-- Center Card -->
    <div class="w-full max-w-lg my-auto">
        <div class="bg-white rounded-3xl p-6 sm:p-9 shadow-card border border-[#E5E7EB] space-y-6">
            
            <!-- Brand Logo & Header -->
            <div class="text-center space-y-2">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-navy-900 text-teal-400 font-black shadow-xs mb-1">
                    <i data-lucide="wrench" class="w-6 h-6"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-black tracking-tight text-navy-900">FUNDI</h1>
                    <p class="text-xs font-semibold text-teal-600 tracking-wide">{{ __('Find. Connect. Fix.') }}</p>
                </div>
            </div>

            <div class="text-center">
                <h2 class="text-lg font-bold text-navy-900" x-text="intent === 'technician' ? '{{ __('Join as a Verified Fundi') }}' : '{{ __('Create Account') }}'"></h2>
                <p class="text-xs text-[#667085] mt-0.5" x-text="intent === 'technician' ? '{{ __('Submit your credentials, get verified, and connect with clients daily.') }}' : '{{ __('Find, request, and rate verified technicians across Tanzania.') }}'"></p>
            </div>

            <!-- Role Selector Switcher -->
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-navy-900">{{ __('Account Type') }}</label>
                <div class="grid grid-cols-2 gap-2.5 sm:gap-3 text-xs">
                    <button type="button" @click="intent = 'client'" 
                        :class="intent === 'client' ? 'border-teal-500 bg-[#F0FDFB] text-teal-900 font-bold ring-1 ring-teal-500 shadow-xs' : 'border-[#E5E7EB] bg-[#F7F8F7] text-[#667085] font-semibold hover:border-slate-300'" 
                        class="p-2.5 sm:p-3.5 rounded-2xl border text-left transition flex items-center space-x-2 sm:space-x-2.5 cursor-pointer">
                        <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl flex items-center justify-center flex-shrink-0" :class="intent === 'client' ? 'bg-teal-500 text-white shadow-xs' : 'bg-slate-200 text-[#667085]'">
                            <i data-lucide="user" class="w-3.5 h-3.5 sm:w-4 sm:h-4"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="font-bold text-xs truncate">{{ __('Hire a Fundi') }}</p>
                            <span class="text-[10px] font-normal text-[#667085] block truncate">{{ __('Client') }}</span>
                        </div>
                    </button>

                    <button type="button" @click="intent = 'technician'" 
                        :class="intent === 'technician' ? 'border-teal-500 bg-[#F0FDFB] text-teal-900 font-bold ring-1 ring-teal-500 shadow-xs' : 'border-[#E5E7EB] bg-[#F7F8F7] text-[#667085] font-semibold hover:border-slate-300'" 
                        class="p-2.5 sm:p-3.5 rounded-2xl border text-left transition flex items-center space-x-2 sm:space-x-2.5 cursor-pointer">
                        <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl flex items-center justify-center flex-shrink-0" :class="intent === 'technician' ? 'bg-teal-500 text-white shadow-xs' : 'bg-slate-200 text-[#667085]'">
                            <i data-lucide="wrench" class="w-3.5 h-3.5 sm:w-4 sm:h-4"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="font-bold text-xs truncate">{{ __('Become a Fundi') }}</p>
                            <span class="text-[10px] font-normal text-[#667085] block truncate">{{ __('Technician') }}</span>
                        </div>
                    </button>
                </div>
            </div>

            @if($errors->any())
                <div class="p-3.5 text-xs text-rose-900 rounded-xl bg-rose-50 border border-rose-200 space-y-1">
                    @foreach ($errors->all() as $error)
                        <div class="flex items-center space-x-1.5">
                            <i data-lucide="alert-circle" class="w-4 h-4 text-[#F04438] flex-shrink-0"></i>
                            <span>{{ $error }}</span>
                        </div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('register.submit') }}" data-turbo="false" class="space-y-4">
                @csrf
                <input type="hidden" name="intent" :value="intent">

                <!-- Full Name -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-navy-900">{{ __('Full Name') }}</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-[#98A2B3] pointer-events-none">
                            <i data-lucide="user" class="w-4 h-4"></i>
                        </span>
                        <input type="text" name="full_name" value="{{ old('full_name') }}" required placeholder="e.g. Innocent Steven" class="w-full pl-10 pr-4 py-3 rounded-xl border border-[#E5E7EB] text-xs font-semibold text-navy-900 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 bg-[#F7F8F7] transition">
                    </div>
                </div>

                <!-- Email -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-navy-900">{{ __('Email Address') }}</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-[#98A2B3] pointer-events-none">
                            <i data-lucide="mail" class="w-4 h-4"></i>
                        </span>
                        <input type="email" name="email" value="{{ old('email') }}" required placeholder="innocent@example.com" class="w-full pl-10 pr-4 py-3 rounded-xl border border-[#E5E7EB] text-xs font-semibold text-navy-900 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 bg-[#F7F8F7] transition">
                    </div>
                </div>

                <!-- Phone -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-navy-900">{{ __('Phone Number') }}</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-[#98A2B3] pointer-events-none">
                            <i data-lucide="phone" class="w-4 h-4"></i>
                        </span>
                        <input type="tel" name="phone" value="{{ old('phone') }}" required placeholder="0712345678" class="w-full pl-10 pr-4 py-3 rounded-xl border border-[#E5E7EB] text-xs font-semibold text-navy-900 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 bg-[#F7F8F7] transition">
                    </div>
                </div>

                <!-- Password & Confirmation -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-navy-900">{{ __('Password') }}</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-[#98A2B3] pointer-events-none">
                                <i data-lucide="lock" class="w-4 h-4"></i>
                            </span>
                            <input type="password" name="password" required placeholder="••••••••" class="w-full pl-10 pr-4 py-3 rounded-xl border border-[#E5E7EB] text-xs font-semibold text-navy-900 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 bg-[#F7F8F7] transition">
                        </div>
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-navy-900">{{ __('Confirm Password') }}</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-[#98A2B3] pointer-events-none">
                                <i data-lucide="shield-check" class="w-4 h-4"></i>
                            </span>
                            <input type="password" name="password_confirmation" required placeholder="••••••••" class="w-full pl-10 pr-4 py-3 rounded-xl border border-[#E5E7EB] text-xs font-semibold text-navy-900 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 bg-[#F7F8F7] transition">
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <button type="submit" class="w-full py-3.5 px-4 rounded-xl bg-teal-500 hover:bg-teal-600 text-white font-bold text-xs shadow-xs hover:shadow-subtle btn-tap flex items-center justify-center space-x-2 cursor-pointer">
                        <span x-text="intent === 'technician' ? '{{ __('Continue to Technician Verification') }}' : '{{ __('Create Account') }}'"></span>
                        <i data-lucide="arrow-right" class="w-4 h-4"></i>
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

    <!-- Clean Bottom Minimal Attribution -->
    <div class="text-center text-[11px] text-[#98A2B3] pt-4">
        {{ __('FUNDI Platform • 100% Verified Artisan Marketplace') }}
    </div>

</div>
@endsection
