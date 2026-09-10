@extends('layouts.app')

@section('title', __('Sign In') . ' — FUNDI')

@section('content')
<div class="min-h-screen flex flex-col justify-between items-center py-8 px-4 sm:px-6 lg:px-8 bg-[#F7F8F7] relative">
    
    <!-- Top Right Floating Language Switcher (SW | EN) -->
    <div class="w-full max-w-md flex justify-end">
        <div class="relative" x-data="{ langOpen: false }">
            <button @click="langOpen = !langOpen" class="flex items-center space-x-1.5 px-3 py-1.5 rounded-xl border border-[#E5E7EB] bg-white hover:bg-slate-50 text-xs font-bold text-navy-900 transition shadow-subtle">
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

    <!-- Center Login Card -->
    <div class="w-full max-w-md my-auto">
        <div class="bg-white rounded-3xl p-7 sm:p-9 shadow-card border border-[#E5E7EB] space-y-6">
            
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
                <h2 class="text-lg font-bold text-navy-900">{{ __('Welcome Back') }}</h2>
                <p class="text-xs text-[#667085] mt-0.5">{{ __('Sign in to continue to your workspace') }}</p>
            </div>

            <!-- Error Alerts -->
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

            @if(session('success'))
                <div class="p-3.5 text-xs text-teal-900 rounded-xl bg-[#F0FDFB] border border-teal-200 flex items-center space-x-2">
                    <i data-lucide="check-circle" class="w-4 h-4 text-teal-600 flex-shrink-0"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <!-- Quick Demo Role Switcher -->
            <div class="space-y-1.5">
                <label class="block text-[11px] font-bold text-[#667085] uppercase tracking-wider text-center">
                    {{ __('⚡ Quick 1-Click Login (Chagua Akaunti Kuingia Moja kwa Moja)') }}
                </label>
                <div class="grid grid-cols-3 gap-2">
                    <button type="button" onclick="fillLogin('admin@fundi.test', 'password')" class="py-2.5 px-1.5 rounded-xl border border-[#E5E7EB] bg-[#F7F8F7] hover:bg-[#F0FDFB] hover:border-teal-500 hover:text-teal-700 text-center text-xs font-bold text-navy-900 transition flex flex-col items-center">
                        <span class="text-xs">👑 Admin</span>
                        <span class="text-[10px] text-[#98A2B3] font-normal">Msimamizi</span>
                    </button>
                    <button type="button" onclick="fillLogin('innocentsteven206@gmail.com', 'password')" class="py-2.5 px-1.5 rounded-xl border border-[#E5E7EB] bg-[#F7F8F7] hover:bg-[#F0FDFB] hover:border-teal-500 hover:text-teal-700 text-center text-xs font-bold text-navy-900 transition flex flex-col items-center">
                        <span class="text-xs">🔧 Guzman</span>
                        <span class="text-[10px] text-teal-600 font-bold">Fundi (Tech)</span>
                    </button>
                    <button type="button" onclick="fillLogin('leryn@fundi.test', 'password')" class="py-2.5 px-1.5 rounded-xl border border-[#E5E7EB] bg-[#F7F8F7] hover:bg-[#F0FDFB] hover:border-teal-500 hover:text-teal-700 text-center text-xs font-bold text-navy-900 transition flex flex-col items-center">
                        <span class="text-xs">👤 Leryn</span>
                        <span class="text-[10px] text-teal-600 font-bold">Mteja (Client)</span>
                    </button>
                </div>
            </div>

            <div class="relative flex items-center justify-center">
                <div class="border-t border-[#E5E7EB] w-full"></div>
                <span class="bg-white px-3 text-[11px] font-semibold text-[#98A2B3] uppercase tracking-wider absolute">au weka taarifa zako</span>
            </div>

            <!-- Login Form -->
            <form id="login-form" method="POST" action="{{ route('login.submit') }}" data-turbo="false" class="space-y-4">
                @csrf

                <!-- Email or Phone -->
                <div class="space-y-1.5">
                    <label for="login" class="block text-xs font-bold text-navy-900">
                        {{ __('Email Address or Phone Number') }}
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#98A2B3]">
                            <i data-lucide="user" class="w-4 h-4"></i>
                        </div>
                        <input type="text" id="login" name="login" value="{{ old('login') }}" required autofocus
                            placeholder="{{ __('e.g. client@fundi.test or 0712345678') }}"
                            class="w-full pl-10 pr-4 py-3 rounded-xl border border-[#E5E7EB] text-xs font-semibold text-navy-900 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 bg-[#F7F8F7] transition">
                    </div>
                </div>

                <!-- Password -->
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between">
                        <label for="password" class="block text-xs font-bold text-navy-900">
                            {{ __('Password') }}
                        </label>
                        <a href="{{ route('password.request') }}" class="text-xs font-bold text-teal-600 hover:text-teal-700 transition">
                            {{ __('Forgot password?') }}
                        </a>
                    </div>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#98A2B3]">
                            <i data-lucide="lock" class="w-4 h-4"></i>
                        </div>
                        <input type="password" id="password" name="password" required
                            placeholder="••••••••••••"
                            class="w-full pl-10 pr-4 py-3 rounded-xl border border-[#E5E7EB] text-xs font-semibold text-navy-900 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 bg-[#F7F8F7] transition">
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between pt-0.5">
                    <label class="flex items-center space-x-2 text-xs text-[#667085] cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded text-teal-600 border-[#E5E7EB] focus:ring-teal-500">
                        <span>{{ __('Remember this device') }}</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <div class="pt-1">
                    <button type="submit" class="w-full py-3.5 px-4 rounded-xl bg-teal-500 hover:bg-teal-600 text-white font-bold text-xs shadow-xs active:scale-[0.98] transition flex items-center justify-center space-x-2">
                        <span>{{ __('Sign In') }}</span>
                        <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </button>
                </div>
            </form>

            <div class="pt-2 border-t border-[#E5E7EB] text-center text-xs text-[#667085]">
                {{ __("Don't have an account?") }} 
                <a href="{{ route('register') }}" class="font-bold text-teal-600 hover:text-teal-700 underline ml-0.5">
                    {{ __('Create account') }}
                </a>
            </div>

        </div>
    </div>

    <!-- Clean Bottom Minimal Attribution -->
    <div class="text-center text-[11px] text-[#98A2B3] pt-4">
        {{ __('FUNDI Platform • 100% Verified Artisan Marketplace') }}
    </div>

</div>

@push('scripts')
<script>
    function fillLogin(email, password) {
        var loginInput = document.getElementById('login');
        var passwordInput = document.getElementById('password');
        var form = document.getElementById('login-form');
        if (loginInput && passwordInput && form) {
            loginInput.value = email;
            passwordInput.value = password;
            form.submit();
        }
    }
</script>
@endpush
@endsection
