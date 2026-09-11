<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Reset Password') }} — FUNDI</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['"Plus Jakarta Sans"', 'sans-serif'] },
                    colors: {
                        brand: {
                            500: '#0e8fe5',
                            700: '#035ba0',
                            900: '#0b416e',
                        }
                    }
                }
            }
        }
    </script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="min-h-full flex items-center justify-center p-4 bg-gradient-to-br from-slate-950 via-slate-900 to-brand-950 font-sans py-8">

    <div class="w-full max-w-md">
        
        <!-- Top Language Switcher -->
        <div class="flex justify-end mb-4">
            <div class="relative" x-data="{ langOpen: false }">
                <button @click="langOpen = !langOpen" class="flex items-center space-x-1.5 px-3 py-1.5 rounded-xl border border-white/20 bg-slate-800/80 hover:bg-slate-700 text-xs font-bold text-white transition shadow-sm">
                    <i data-lucide="globe" class="w-3.5 h-3.5 text-teal-400"></i>
                    <span>{{ app()->getLocale() === 'en' ? 'EN' : 'SW' }}</span>
                    <i data-lucide="chevron-down" class="w-3 h-3 text-slate-400"></i>
                </button>
                <div x-show="langOpen" @click.away="langOpen = false" x-cloak class="absolute right-0 mt-2 w-32 bg-white rounded-xl shadow-2xl border border-slate-100 py-1.5 z-50 text-xs font-semibold">
                    <a href="{{ route('language.switch', 'sw') }}" class="flex items-center justify-between px-3.5 py-2 hover:bg-teal-50 hover:text-teal-700 transition {{ app()->getLocale() === 'sw' ? 'text-teal-700 font-bold bg-teal-50' : 'text-slate-800' }}">
                        <span>Kiswahili</span>
                        @if(app()->getLocale() === 'sw') <i data-lucide="check" class="w-3.5 h-3.5 text-teal-600"></i> @endif
                    </a>
                    <a href="{{ route('language.switch', 'en') }}" class="flex items-center justify-between px-3.5 py-2 hover:bg-teal-50 hover:text-teal-700 transition {{ app()->getLocale() === 'en' ? 'text-teal-700 font-bold bg-teal-50' : 'text-slate-800' }}">
                        <span>English</span>
                        @if(app()->getLocale() === 'en') <i data-lucide="check" class="w-3.5 h-3.5 text-teal-600"></i> @endif
                    </a>
                </div>
            </div>
        </div>

        <div class="text-center mb-6">
            <a href="{{ route('login') }}" class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-tr from-brand-700 to-teal-500 text-white shadow-xl shadow-brand-500/20 mb-2">
                <i data-lucide="wrench" class="w-7 h-7"></i>
            </a>
            <h1 class="text-2xl font-black tracking-tight text-white">FUNDI</h1>
            <p class="text-xs font-semibold tracking-wide text-teal-400">{{ __('Find. Connect. Fix.') }}</p>
        </div>

        <div class="bg-white/95 backdrop-blur-xl rounded-3xl p-6 sm:p-8 shadow-2xl border border-white/20 space-y-5">
            
            <div>
                <h2 class="text-xl font-bold text-slate-900">{{ __('Forgot Password?') }}</h2>
                <p class="text-xs text-slate-500 mt-0.5">{{ __('Enter your registered email or phone number to create a new password.') }}</p>
            </div>

            @if($errors->any())
                <div class="p-3 text-xs text-rose-800 rounded-xl bg-rose-50 border border-rose-200">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="space-y-3.5">
                @csrf

                <!-- Identifier -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">{{ __('Email Address or Phone Number') }}</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                            <i data-lucide="user" class="w-4 h-4"></i>
                        </span>
                        <input type="text" name="identifier" value="{{ old('identifier') }}" required placeholder="{{ __('Your email or phone number') }}" class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 bg-slate-50/50">
                    </div>
                </div>

                <!-- New Password -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">{{ __('New Password') }}</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                            <i data-lucide="lock" class="w-4 h-4"></i>
                        </span>
                        <input type="password" name="password" required placeholder="••••••••" class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 bg-slate-50/50">
                    </div>
                </div>

                <!-- Confirm Password -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">{{ __('Confirm Password') }}</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                            <i data-lucide="lock" class="w-4 h-4"></i>
                        </span>
                        <input type="password" name="password_confirmation" required placeholder="••••••••" class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 bg-slate-50/50">
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full py-3.5 px-4 rounded-xl bg-brand-700 hover:bg-brand-800 text-white text-xs font-bold uppercase tracking-wider shadow-lg shadow-brand-700/20 transition transform active:scale-[0.99] flex items-center justify-center space-x-2">
                        <span>{{ __('Save New Password') }}</span>
                        <i data-lucide="check" class="w-4 h-4"></i>
                    </button>
                </div>
            </form>

            <!-- WhatsApp Support Option -->
            <div class="pt-3 border-t border-slate-100 space-y-2">
                <p class="text-center text-[11px] text-slate-500">{{ __('Need direct assistance from Admin?') }}</p>
                <a href="{{ $adminWhatsappUrl }}" target="_blank" class="w-full py-2.5 px-3 rounded-xl bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center justify-center space-x-2 transition">
                    <i data-lucide="message-circle" class="w-4 h-4 text-emerald-600"></i>
                    <span>{{ __('Contact Admin on WhatsApp') }} ({{ $adminPhone }})</span>
                </a>
            </div>

            <div class="text-center text-xs text-slate-600 pt-1">
                {{ __('Remembered your password?') }}
                <a href="{{ route('login') }}" class="font-bold text-brand-700 hover:text-brand-900 ml-1 underline">{{ __('Sign In') }}</a>
            </div>

        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();
        });
    </script>
</body>
</html>
