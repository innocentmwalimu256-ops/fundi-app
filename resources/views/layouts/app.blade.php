<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-[#F7F8F7]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sign In') — FUNDI | Find. Connect. Fix.</title>
    
    <!-- Preconnect & DNS-Prefetch for Maximum Speed -->
    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link rel="dns-prefetch" href="//cdn.tailwindcss.com">
    <link rel="dns-prefetch" href="//unpkg.com">
    <link rel="dns-prefetch" href="//cdn.jsdelivr.net">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@500;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Chrome / Edge Native Speculation Rules (Instant 0ms Prerendering & Page Swapping) -->
    <script type="speculationrules">
    {
      "prerender": [
        {
          "source": "document",
          "where": {
            "and": [
              { "href_matches": "/*" },
              { "not": { "href_matches": "*/logout*" } },
              { "not": { "href_matches": "*#*" } }
            ]
          },
          "eagerness": "moderate"
        }
      ],
      "prefetch": [
        {
          "source": "document",
          "where": {
            "and": [
              { "href_matches": "/*" },
              { "not": { "href_matches": "*/logout*" } }
            ]
          },
          "eagerness": "immediate"
        }
      ]
    }
    </script>
    
    <!-- Tailwind CSS CDN with Specification Tokens -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'monospace'],
                    },
                    colors: {
                        navy: {
                            DEFAULT: '#0B1220',
                            950: '#070B14',
                            900: '#0B1220',
                            800: '#131C31',
                            700: '#1D2A47',
                            600: '#2C3E66',
                        },
                        teal: {
                            DEFAULT: '#0F9F95',
                            50: '#F0FDFB',
                            100: '#CCFBEF',
                            200: '#99F6E0',
                            300: '#5EEAD4',
                            400: '#2DD4BF',
                            500: '#0F9F95',
                            600: '#087F78',
                            700: '#066560',
                            800: '#054F4B',
                            900: '#043A37',
                        },
                        neutral: {
                            bg: '#F7F8F7',
                            surface: '#FFFFFF',
                            text: '#111827',
                            secondary: '#667085',
                            muted: '#98A2B3',
                            border: '#E5E7EB',
                        },
                        status: {
                            success: '#12B76A',
                            warning: '#F79009',
                            error: '#F04438',
                            info: '#2E90FA',
                        }
                    },
                    boxShadow: {
                        'subtle': '0 1px 3px 0 rgba(11, 18, 32, 0.05), 0 1px 2px 0 rgba(11, 18, 32, 0.03)',
                        'card': '0 4px 6px -1px rgba(11, 18, 32, 0.04), 0 2px 4px -2px rgba(11, 18, 32, 0.03)',
                        'elevated': '0 12px 24px -4px rgba(11, 18, 32, 0.08), 0 4px 8px -2px rgba(11, 18, 32, 0.03)',
                        'modal': '0 20px 25px -5px rgba(11, 18, 32, 0.12), 0 8px 10px -6px rgba(11, 18, 32, 0.06)',
                    },
                    borderRadius: {
                        'card': '18px',
                        'btn': '12px',
                        'badge': '8px',
                    }
                }
            }
        }
    </script>
    
    <!-- Hotwire Turbo Drive: Seamless SPA Navigation (No Full Page Reloads) -->
    <script src="https://cdn.jsdelivr.net/npm/@hotwired/turbo@7.3.0/dist/turbo.es5-umd.js"></script>
    
    <!-- Lucide Icons & Alpine.js -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; -webkit-tap-highlight-color: transparent; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
        .custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #F7F8F7; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #E5E7EB; border-radius: 9999px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #98A2B3; }
        .turbo-progress-bar { height: 3px; background: linear-gradient(90deg, #0F9F95, #14B8A6, #2DD4BF); }
    </style>
    @stack('styles')
</head>
<body class="h-full bg-[#F7F8F7] text-[#111827] flex flex-col antialiased selection:bg-teal-500 selection:text-white @auth pb-20 md:pb-0 @endauth">

    @auth
    <!-- Global Top Navigation Bar (Only for Logged-In Users) -->
    <header class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-[#E5E7EB] transition-all">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16 sm:h-[72px]">
                
                <!-- Logo & Brand -->
                <div class="flex items-center space-x-3">
                    <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : (auth()->user()->isTechnician() ? route('technician.dashboard') : route('client.dashboard')) }}" class="flex items-center space-x-2.5 group">
                        <div class="w-10 h-10 rounded-xl bg-navy-900 text-teal-400 flex items-center justify-center font-black shadow-sm group-hover:bg-navy-800 transition">
                            <i data-lucide="wrench" class="w-5 h-5"></i>
                        </div>
                        <div class="flex flex-col">
                            <div class="flex items-center space-x-1.5">
                                <span class="text-xl font-extrabold tracking-tight text-navy-900">FUNDI</span>
                                <span class="text-[10px] uppercase font-bold tracking-wider px-2 py-0.5 rounded-md bg-[#F0FDFB] text-teal-700 border border-teal-200">
                                    {{ auth()->user()->role }}
                                </span>
                            </div>
                            <span class="text-[10px] text-[#667085] font-medium tracking-tight -mt-0.5 hidden sm:block">Find. Connect. Fix.</span>
                        </div>
                    </a>
                </div>

                <!-- Desktop Navigation for Clients -->
                @if(auth()->user()->isClient())
                <nav class="hidden md:flex items-center space-x-1 lg:space-x-2">
                    <a href="{{ route('client.dashboard') }}" class="px-3.5 py-2 text-sm font-semibold rounded-xl transition {{ request()->routeIs('client.dashboard') ? 'bg-[#F0FDFB] text-teal-700' : 'text-[#667085] hover:text-[#111827] hover:bg-slate-100' }}">
                        {{ __('Dashboard') }}
                    </a>
                    <a href="{{ route('client.services.index') }}" class="px-3.5 py-2 text-sm font-semibold rounded-xl transition {{ request()->routeIs('client.services.*') ? 'bg-[#F0FDFB] text-teal-700' : 'text-[#667085] hover:text-[#111827] hover:bg-slate-100' }}">
                        {{ __('Services') }}
                    </a>
                    <a href="{{ route('client.technicians.index') }}" class="px-3.5 py-2 text-sm font-semibold rounded-xl transition {{ request()->routeIs('client.technicians.*') ? 'bg-[#F0FDFB] text-teal-700' : 'text-[#667085] hover:text-[#111827] hover:bg-slate-100' }}">
                        {{ __('Find Technicians') }}
                    </a>
                    <a href="{{ route('client.requests.index') }}" class="px-3.5 py-2 text-sm font-semibold rounded-xl transition {{ request()->routeIs('client.requests.*') ? 'bg-[#F0FDFB] text-teal-700' : 'text-[#667085] hover:text-[#111827] hover:bg-slate-100' }}">
                        {{ __('My Requests') }}
                    </a>
                    <a href="{{ route('client.favorites.index') }}" class="px-3.5 py-2 text-sm font-semibold rounded-xl transition {{ request()->routeIs('client.favorites.*') ? 'bg-[#F0FDFB] text-teal-700' : 'text-[#667085] hover:text-[#111827] hover:bg-slate-100' }}">
                        {{ __('Saved') }}
                    </a>
                </nav>
                @endif

                <!-- Desktop Navigation for Technicians -->
                @if(auth()->user()->isTechnician())
                <nav class="hidden md:flex items-center space-x-1 lg:space-x-2">
                    <a href="{{ route('technician.dashboard') }}" class="px-3.5 py-2 text-sm font-semibold rounded-xl transition {{ request()->routeIs('technician.dashboard') ? 'bg-[#F0FDFB] text-teal-700' : 'text-[#667085] hover:text-[#111827] hover:bg-slate-100' }}">
                        {{ __('Dashboard') }}
                    </a>
                    <a href="{{ route('technician.requests.index') }}" class="px-3.5 py-2 text-sm font-semibold rounded-xl transition {{ request()->routeIs('technician.requests.*') ? 'bg-[#F0FDFB] text-teal-700' : 'text-[#667085] hover:text-[#111827] hover:bg-slate-100' }}">
                        {{ __('My Requests') }}
                    </a>
                    <a href="{{ route('technician.subscription') }}" class="px-3.5 py-2 text-sm font-semibold rounded-xl transition {{ request()->routeIs('technician.subscription*') ? 'bg-[#F0FDFB] text-teal-700' : 'text-[#667085] hover:text-[#111827] hover:bg-slate-100' }}">
                        {{ __('Subscriptions') }}
                    </a>
                    <a href="{{ route('technician.portfolios.index') }}" class="px-3.5 py-2 text-sm font-semibold rounded-xl transition {{ request()->routeIs('technician.portfolios.*') ? 'bg-[#F0FDFB] text-teal-700' : 'text-[#667085] hover:text-[#111827] hover:bg-slate-100' }}">
                        {{ __('Portfolio') }}
                    </a>
                    <a href="{{ route('technician.reviews.index') }}" class="px-3.5 py-2 text-sm font-semibold rounded-xl transition {{ request()->routeIs('technician.reviews.*') ? 'bg-[#F0FDFB] text-teal-700' : 'text-[#667085] hover:text-[#111827] hover:bg-slate-100' }}">
                        {{ __('Rate & Review') }}
                    </a>
                </nav>
                @endif

                <!-- Right Actions: Language Switcher, Notifications, Messages, Profile -->
                <div class="flex items-center space-x-2 sm:space-x-3">

                    <!-- Bilingual Switcher (SW | EN) -->
                    <div class="relative" x-data="{ langOpen: false }">
                        <button @click="langOpen = !langOpen" class="flex items-center space-x-1.5 px-3 py-1.5 rounded-xl border border-[#E5E7EB] bg-white hover:bg-slate-50 text-xs font-bold text-navy-900 transition shadow-xs cursor-pointer">
                            <i data-lucide="globe" class="w-3.5 h-3.5 text-teal-600"></i>
                            <span>{{ app()->getLocale() === 'en' ? 'EN' : 'SW' }}</span>
                        </button>
                        <div x-show="langOpen" @click.away="langOpen = false" x-cloak class="absolute right-0 mt-2 w-36 bg-white rounded-xl shadow-elevated border border-[#E5E7EB] py-1.5 z-50 text-xs font-semibold">
                            <a href="{{ route('language.switch', 'sw') }}" class="flex items-center justify-between px-3.5 py-2 hover:bg-[#F0FDFB] hover:text-teal-700 transition {{ app()->getLocale() === 'sw' ? 'text-teal-700 font-bold bg-[#F0FDFB]' : 'text-[#111827]' }}">
                                <span>🇹🇿 Kiswahili</span>
                                @if(app()->getLocale() === 'sw') <i data-lucide="check" class="w-3.5 h-3.5 text-teal-600"></i> @endif
                            </a>
                            <a href="{{ route('language.switch', 'en') }}" class="flex items-center justify-between px-3.5 py-2 hover:bg-[#F0FDFB] hover:text-teal-700 transition {{ app()->getLocale() === 'en' ? 'text-teal-700 font-bold bg-[#F0FDFB]' : 'text-[#111827]' }}">
                                <span>🇬🇧 English</span>
                                @if(app()->getLocale() === 'en') <i data-lucide="check" class="w-3.5 h-3.5 text-teal-600"></i> @endif
                            </a>
                        </div>
                    </div>

                    <!-- Notification Bell -->
                    <a href="{{ route('notifications.index') }}" class="relative p-2 text-[#667085] hover:text-navy-900 hover:bg-slate-100 rounded-xl transition" title="{{ __('Notifications') }}">
                        <i data-lucide="bell" class="w-5 h-5"></i>
                        @php $unreadCount = auth()->user()->unreadNotificationsCount(); @endphp
                        @if($unreadCount > 0)
                            <span class="absolute top-1.5 right-1.5 flex h-4 w-4 items-center justify-center rounded-full bg-[#F04438] text-[9px] font-bold text-white shadow-xs">
                                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                            </span>
                        @endif
                    </a>

                    <!-- Messages Link -->
                    <a href="{{ route('messages.index') }}" class="relative p-2 text-[#667085] hover:text-navy-900 hover:bg-slate-100 rounded-xl transition" title="{{ __('Messages') }}">
                        <i data-lucide="message-square" class="w-5 h-5"></i>
                    </a>

                    <!-- User Profile Dropdown -->
                    <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                        <button @click="open = !open" class="flex items-center space-x-2 p-1 rounded-xl hover:bg-slate-100 transition focus:outline-none cursor-pointer">
                            <div class="w-9 h-9 rounded-xl bg-navy-900 text-teal-300 flex items-center justify-center font-bold text-xs uppercase shadow-xs">
                                {{ auth()->user()->initials }}
                            </div>
                            <span class="hidden md:inline-block text-xs font-bold text-navy-900 max-w-[120px] truncate">
                                {{ auth()->user()->first_name }}
                            </span>
                            <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-[#98A2B3]"></i>
                        </button>

                        <div x-show="open" x-cloak class="absolute right-0 mt-2 w-56 bg-white rounded-2xl shadow-elevated border border-[#E5E7EB] py-2 z-50 text-xs">
                            <div class="px-4 py-2.5 border-b border-[#E5E7EB]">
                                <p class="font-bold text-navy-900 truncate">{{ auth()->user()->full_name }}</p>
                                <p class="text-[11px] text-[#667085] font-mono truncate">{{ auth()->user()->phone ?? auth()->user()->email }}</p>
                            </div>

                            @if(auth()->user()->isAdmin())
                                <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-2 text-navy-900 hover:bg-[#F0FDFB] hover:text-teal-700 font-semibold transition">
                                    <i data-lucide="shield" class="w-4 h-4 mr-2.5 text-teal-600"></i> {{ __('Admin Center') }}
                                </a>
                            @elseif(auth()->user()->isTechnician())
                                <a href="{{ route('technician.subscription') }}" class="flex items-center px-4 py-2 text-navy-900 hover:bg-[#F0FDFB] hover:text-teal-700 font-semibold transition">
                                    <i data-lucide="credit-card" class="w-4 h-4 mr-2.5 text-teal-600"></i> {{ __('My Subscription') }}
                                </a>
                                <a href="{{ route('technician.availability') }}" class="flex items-center px-4 py-2 text-navy-900 hover:bg-[#F0FDFB] hover:text-teal-700 font-semibold transition">
                                    <i data-lucide="clock" class="w-4 h-4 mr-2.5 text-teal-600"></i> {{ __('Set Availability') }}
                                </a>
                            @elseif(auth()->user()->isClient())
                                @php
                                    $hasApp = \App\Models\TechnicianApplication::where('user_id', auth()->id())->first();
                                @endphp
                                @if($hasApp)
                                    <a href="{{ route('client.technician-application.status') }}" class="flex items-center px-4 py-2 text-amber-800 bg-amber-50/70 hover:bg-amber-100 font-semibold transition">
                                        <i data-lucide="clock" class="w-4 h-4 mr-2.5 text-amber-600"></i> {{ __('Technician Application Status') }}
                                    </a>
                                @else
                                    <a href="{{ route('client.become-technician') }}" class="flex items-center px-4 py-2 text-teal-700 bg-teal-50/70 hover:bg-teal-100 font-bold transition">
                                        <i data-lucide="award" class="w-4 h-4 mr-2.5 text-teal-600"></i> {{ __('Join as Technician') }}
                                    </a>
                                @endif
                            @endif

                            <form method="POST" action="{{ route('logout') }}" data-turbo="false" class="border-t border-[#E5E7EB] mt-1 pt-1">
                                @csrf
                                <button type="submit" class="w-full flex items-center px-4 py-2 text-[#F04438] hover:bg-rose-50 font-bold transition cursor-pointer">
                                    <i data-lucide="log-out" class="w-4 h-4 mr-2.5"></i> {{ __('Logout') }}
                                </button>
                            </form>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </header>
    @endauth

    <!-- Flash Alerts Container (Only for Logged In Pages) -->
    @auth
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full">
        @if(session('success'))
            <div class="flex items-center justify-between p-4 mb-3 rounded-2xl bg-[#F0FDFB] border border-teal-200 text-teal-900 text-xs font-medium shadow-subtle" role="alert">
                <div class="flex items-center space-x-2.5">
                    <i data-lucide="check-circle" class="w-5 h-5 text-teal-600 flex-shrink-0"></i>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" @click="$el.parentElement.remove()" class="text-teal-600 hover:text-teal-900 p-1">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div class="flex items-center justify-between p-4 mb-3 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 text-xs font-medium shadow-subtle" role="alert">
                <div class="flex items-center space-x-2.5">
                    <i data-lucide="alert-triangle" class="w-5 h-5 text-[#F04438] flex-shrink-0"></i>
                    <span>{{ session('error') }}</span>
                </div>
                <button type="button" @click="$el.parentElement.remove()" class="text-rose-600 hover:text-rose-900 p-1">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        @endif
    </div>
    @endauth

    <!-- Main Content Area -->
    <main class="flex-grow flex flex-col">
        @yield('content')
    </main>

    @auth
    <!-- Mobile Bottom Navigation (Visible on Small Screens) -->
    <nav class="md:hidden fixed bottom-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-md border-t border-[#E5E7EB] px-4 py-1.5 flex justify-around items-center">
        @if(auth()->user()->isClient())
            <a href="{{ route('client.dashboard') }}" class="flex flex-col items-center py-1 px-3 text-[10px] font-bold transition {{ request()->routeIs('client.dashboard') ? 'text-teal-600' : 'text-[#667085]' }}">
                <i data-lucide="home" class="w-5 h-5 mb-0.5"></i>
                <span>{{ __('Dashboard') }}</span>
            </a>
            <a href="{{ route('client.technicians.index') }}" class="flex flex-col items-center py-1 px-3 text-[10px] font-bold transition {{ request()->routeIs('client.technicians.*') ? 'text-teal-600' : 'text-[#667085]' }}">
                <i data-lucide="users" class="w-5 h-5 mb-0.5"></i>
                <span>{{ __('Find Technicians') }}</span>
            </a>
            <a href="{{ route('client.requests.index') }}" class="flex flex-col items-center py-1 px-3 text-[10px] font-bold transition {{ request()->routeIs('client.requests.*') ? 'text-teal-600' : 'text-[#667085]' }}">
                <i data-lucide="inbox" class="w-5 h-5 mb-0.5"></i>
                <span>{{ __('My Requests') }}</span>
            </a>
            <a href="{{ route('messages.index') }}" class="flex flex-col items-center py-1 px-3 text-[10px] font-bold transition {{ request()->routeIs('messages.*') ? 'text-teal-600' : 'text-[#667085]' }}">
                <i data-lucide="message-square" class="w-5 h-5 mb-0.5"></i>
                <span>{{ __('Messages') }}</span>
            </a>
            <a href="{{ route('client.favorites.index') }}" class="flex flex-col items-center py-1 px-3 text-[10px] font-bold transition {{ request()->routeIs('client.favorites.*') ? 'text-teal-600' : 'text-[#667085]' }}">
                <i data-lucide="bookmark" class="w-5 h-5 mb-0.5"></i>
                <span>{{ __('Saved') }}</span>
            </a>
        @elseif(auth()->user()->isTechnician())
            <a href="{{ route('technician.dashboard') }}" class="flex flex-col items-center py-1 px-3 text-[10px] font-bold transition {{ request()->routeIs('technician.dashboard') ? 'text-teal-600' : 'text-[#667085]' }}">
                <i data-lucide="home" class="w-5 h-5 mb-0.5"></i>
                <span>{{ __('Dashboard') }}</span>
            </a>
            <a href="{{ route('technician.requests.index') }}" class="flex flex-col items-center py-1 px-3 text-[10px] font-bold transition {{ request()->routeIs('technician.requests.*') ? 'text-teal-600' : 'text-[#667085]' }}">
                <i data-lucide="briefcase" class="w-5 h-5 mb-0.5"></i>
                <span>{{ __('My Requests') }}</span>
            </a>
            <a href="{{ route('technician.subscription') }}" class="flex flex-col items-center py-1 px-3 text-[10px] font-bold transition {{ request()->routeIs('technician.subscription*') ? 'text-teal-600' : 'text-[#667085]' }}">
                <i data-lucide="credit-card" class="w-5 h-5 mb-0.5"></i>
                <span>{{ __('Subscriptions') }}</span>
            </a>
            <a href="{{ route('messages.index') }}" class="flex flex-col items-center py-1 px-3 text-[10px] font-bold transition {{ request()->routeIs('messages.*') ? 'text-teal-600' : 'text-[#667085]' }}">
                <i data-lucide="message-square" class="w-5 h-5 mb-0.5"></i>
                <span>{{ __('Messages') }}</span>
            </a>
        @endif
    </nav>

    <!-- Footer for Logged In Users -->
    <footer class="bg-navy-950 text-[#98A2B3] text-xs mt-16 border-t border-navy-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex flex-col sm:flex-row items-center justify-between text-[11px] text-[#667085]">
                <div class="flex items-center space-x-2">
                    <span class="text-white font-bold">FUNDI Platform</span>
                    <span>•</span>
                    <span>Tanzania</span>
                </div>
                <p class="mt-2 sm:mt-0">&copy; {{ date('Y') }} All rights reserved.</p>
            </div>
        </div>
    </footer>
    @endauth

    <!-- Top Fast Loading Indicator -->
    <div id="page-progress" class="fixed top-0 left-0 h-[2.5px] bg-teal-500 z-50 transition-all duration-200 pointer-events-none opacity-0 w-0"></div>

    <!-- Instant Page Prefetcher (Instant Loading on Hover/Touch) -->
    <script src="https://cdn.jsdelivr.net/npm/instant.page@5.2.0/instantpage.js" type="module"></script>

    <!-- Initialize Lucide Icons & Turbo SPA Support -->
    <script>
        function initAppIcons() {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        }

        document.addEventListener('DOMContentLoaded', initAppIcons);
        document.addEventListener('turbo:load', initAppIcons);
        document.addEventListener('turbo:render', initAppIcons);

        // Instant Back/Forward Cache Restore
        window.addEventListener('pageshow', (event) => {
            initAppIcons();
        });
    </script>
    @stack('scripts')
</body>
</html>
