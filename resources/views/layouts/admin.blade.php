<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-[#F8FAFC]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('Admin Panel')) — FUNDI {{ __('Control Center') }}</title>
    
    <!-- Preconnect & DNS-Prefetch for Maximum Speed -->
    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link rel="dns-prefetch" href="//cdn.tailwindcss.com">
    <link rel="dns-prefetch" href="//unpkg.com">
    <link rel="dns-prefetch" href="//cdn.jsdelivr.net">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
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
    
    <!-- Tailwind CSS CDN -->
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
                            DEFAULT: '#0F172A',
                            950: '#020617',
                            900: '#0F172A',
                            800: '#1E293B',
                            700: '#334155',
                            600: '#475569',
                        },
                        teal: {
                            DEFAULT: '#0D9488',
                            50: '#F0FDFA',
                            100: '#CCFBF1',
                            200: '#99F6E4',
                            300: '#5EEAD4',
                            400: '#2DD4BF',
                            500: '#14B8A6',
                            600: '#0D9488',
                            700: '#0F766E',
                            800: '#115E59',
                            900: '#134E4A',
                        },
                        surface: {
                            bg: '#F8FAFC',
                            card: '#FFFFFF',
                            border: '#E2E8F0',
                        }
                    },
                    boxShadow: {
                        'card': '0 1px 3px 0 rgba(0, 0, 0, 0.04), 0 1px 2px -1px rgba(0, 0, 0, 0.03)',
                        'elevated': '0 10px 15px -3px rgba(0, 0, 0, 0.06), 0 4px 6px -4px rgba(0, 0, 0, 0.03)',
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
        .custom-scrollbar::-webkit-scrollbar { width: 5px; height: 5px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #0F172A; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #334155; border-radius: 9999px; }
        .turbo-progress-bar { height: 3px; background: linear-gradient(90deg, #0F9F95, #14B8A6, #2DD4BF); }
    </style>
    @stack('styles')
</head>
<body class="h-full flex bg-[#F8FAFC] text-slate-800 antialiased" x-data="{ sidebarOpen: false }">

    <!-- Mobile Sidebar Backdrop -->
    <div x-show="sidebarOpen" @click="sidebarOpen = false" x-cloak class="fixed inset-0 z-40 bg-slate-950/70 backdrop-blur-xs lg:hidden transition-opacity"></div>

    <!-- Admin Left Sidebar -->
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-50 w-64 bg-[#0F172A] text-slate-300 border-r border-slate-800 flex flex-col transition-transform duration-200 ease-in-out lg:translate-x-0">
        
        <!-- Sidebar Brand Header -->
        <div class="h-16 flex items-center justify-between px-6 border-b border-slate-800 bg-[#0F172A]">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3">
                <div class="w-9 h-9 rounded-xl bg-teal-500 text-slate-950 font-black flex items-center justify-center shadow-sm">
                    <i data-lucide="shield" class="w-5 h-5"></i>
                </div>
                <div>
                    <span class="text-base font-extrabold tracking-tight text-white block">FUNDI</span>
                    <span class="text-[10px] uppercase tracking-wider text-teal-400 font-bold block -mt-0.5">{{ __('Control Center') }}</span>
                </div>
            </a>
            <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-white p-1">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <!-- Navigation Menu -->
        <div class="flex-1 overflow-y-auto px-4 py-5 space-y-5 custom-scrollbar text-xs font-medium">
            
            <!-- Group 1: Core -->
            <div class="space-y-1">
                <p class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ __('Overview') }}</p>
                <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('admin.dashboard') ? 'bg-slate-800 text-white font-bold' : 'hover:bg-slate-800/60 hover:text-white text-slate-400' }}">
                    <i data-lucide="layout-dashboard" class="w-4 h-4 {{ request()->routeIs('admin.dashboard') ? 'text-teal-400' : 'text-slate-400' }}"></i>
                    <span>{{ __('Dashboard KPIs') }}</span>
                </a>
                <a href="{{ route('admin.subscriptions.revenue') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('admin.subscriptions.revenue') ? 'bg-slate-800 text-white font-bold' : 'hover:bg-slate-800/60 hover:text-white text-slate-400' }}">
                    <i data-lucide="trending-up" class="w-4 h-4 {{ request()->routeIs('admin.subscriptions.revenue') ? 'text-teal-400' : 'text-slate-400' }}"></i>
                    <span>{{ __('Revenue Analytics') }}</span>
                </a>
            </div>

            <!-- Group 2: Finance -->
            <div class="space-y-1">
                <p class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ __('Finance & Payments') }}</p>
                <a href="{{ route('admin.subscriptions.payments') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('admin.subscriptions.payments*') ? 'bg-slate-800 text-white font-bold' : 'hover:bg-slate-800/60 hover:text-white text-slate-400' }}">
                    <i data-lucide="wallet" class="w-4 h-4 {{ request()->routeIs('admin.subscriptions.payments*') ? 'text-teal-400' : 'text-slate-400' }}"></i>
                    <span>{{ __('Payment Ledger') }}</span>
                </a>
                <a href="{{ route('admin.subscriptions.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('admin.subscriptions.index') ? 'bg-slate-800 text-white font-bold' : 'hover:bg-slate-800/60 hover:text-white text-slate-400' }}">
                    <i data-lucide="credit-card" class="w-4 h-4 {{ request()->routeIs('admin.subscriptions.index') ? 'text-teal-400' : 'text-slate-400' }}"></i>
                    <span>{{ __('Subscriptions') }}</span>
                </a>
            </div>

            <!-- Group 3: Operations -->
            <div class="space-y-1">
                <p class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ __('Marketplace') }}</p>
                <a href="{{ route('admin.requests.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('admin.requests.*') ? 'bg-slate-800 text-white font-bold' : 'hover:bg-slate-800/60 hover:text-white text-slate-400' }}">
                    <i data-lucide="inbox" class="w-4 h-4 {{ request()->routeIs('admin.requests.*') ? 'text-teal-400' : 'text-slate-400' }}"></i>
                    <span>{{ __('Service Requests') }}</span>
                </a>
                <a href="{{ route('admin.applications.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('admin.applications.*') ? 'bg-slate-800 text-white font-bold' : 'hover:bg-slate-800/60 hover:text-white text-slate-400' }}">
                    <i data-lucide="badge-check" class="w-4 h-4 {{ request()->routeIs('admin.applications.*') ? 'text-teal-400' : 'text-slate-400' }}"></i>
                    <span>{{ __('Verification Desk') }}</span>
                </a>
                <a href="{{ route('admin.users.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('admin.users.*') ? 'bg-slate-800 text-white font-bold' : 'hover:bg-slate-800/60 hover:text-white text-slate-400' }}">
                    <i data-lucide="users" class="w-4 h-4 {{ request()->routeIs('admin.users.*') ? 'text-teal-400' : 'text-slate-400' }}"></i>
                    <span>{{ __('User Directory') }}</span>
                </a>
                <a href="{{ route('admin.services.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('admin.services.*') ? 'bg-slate-800 text-white font-bold' : 'hover:bg-slate-800/60 hover:text-white text-slate-400' }}">
                    <i data-lucide="grid" class="w-4 h-4 {{ request()->routeIs('admin.services.*') ? 'text-teal-400' : 'text-slate-400' }}"></i>
                    <span>{{ __('Service Catalog') }}</span>
                </a>
            </div>

            <!-- Group 4: Administration -->
            <div class="space-y-1">
                <p class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ __('Administration') }}</p>
                <a href="{{ route('admin.settings.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('admin.settings.*') ? 'bg-slate-800 text-white font-bold' : 'hover:bg-slate-800/60 hover:text-white text-slate-400' }}">
                    <i data-lucide="sliders" class="w-4 h-4 {{ request()->routeIs('admin.settings.*') ? 'text-teal-400' : 'text-slate-400' }}"></i>
                    <span>{{ __('Gateway & API Settings') }}</span>
                </a>
                <a href="{{ route('admin.profile.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('admin.profile.*') ? 'bg-slate-800 text-white font-bold' : 'hover:bg-slate-800/60 hover:text-white text-slate-400' }}">
                    <i data-lucide="user" class="w-4 h-4 {{ request()->routeIs('admin.profile.*') ? 'text-teal-400' : 'text-slate-400' }}"></i>
                    <span>{{ __('Admin Profile') }}</span>
                </a>
                <a href="{{ route('admin.audit-logs.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('admin.audit-logs.*') ? 'bg-slate-800 text-white font-bold' : 'hover:bg-slate-800/60 hover:text-white text-slate-400' }}">
                    <i data-lucide="activity" class="w-4 h-4 {{ request()->routeIs('admin.audit-logs.*') ? 'text-teal-400' : 'text-slate-400' }}"></i>
                    <span>{{ __('Audit Logs') }}</span>
                </a>
            </div>

        </div>

        <!-- User Logout Footer -->
        <div class="p-4 border-t border-slate-800 bg-[#0F172A]">
            <form method="POST" action="{{ route('logout') }}" data-turbo="false">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center space-x-2 px-3 py-2.5 rounded-xl bg-slate-800/80 hover:bg-rose-900/30 text-rose-400 font-bold text-xs transition">
                    <i data-lucide="log-out" class="w-4 h-4"></i>
                    <span>{{ __('Log Out') }}</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="lg:pl-64 flex flex-col flex-1 min-w-0">
        
        <!-- Top Admin Header -->
        <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-4 sm:px-6 lg:px-8 sticky top-0 z-30">
            <div class="flex items-center space-x-3">
                <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-xl text-slate-500 hover:bg-slate-100">
                    <i data-lucide="menu" class="w-5 h-5"></i>
                </button>
                <h1 class="text-sm sm:text-base font-extrabold text-slate-900">@yield('page_title', __('Admin Dashboard'))</h1>
            </div>

            <div class="flex items-center space-x-2.5">
                <a href="{{ route('client.dashboard') }}" class="hidden sm:inline-flex items-center text-xs font-bold text-teal-700 bg-teal-50 border border-teal-200 hover:bg-teal-100 px-3 py-1.5 rounded-xl transition">
                    <i data-lucide="external-link" class="w-3.5 h-3.5 mr-1.5"></i> {{ __('Client View') }}
                </a>

                <!-- Language Switcher (SW | EN) -->
                <div class="relative" x-data="{ langOpen: false }">
                    <button @click="langOpen = !langOpen" class="flex items-center space-x-1 px-2.5 py-1.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-xs font-bold text-slate-700 transition">
                        <span>{{ app()->getLocale() === 'en' ? 'EN' : 'SW' }}</span>
                        <i data-lucide="globe" class="w-3.5 h-3.5 text-slate-400"></i>
                    </button>
                    <div x-show="langOpen" @click.away="langOpen = false" x-cloak class="absolute right-0 mt-2 w-32 bg-white rounded-xl shadow-elevated border border-slate-200 py-1.5 z-50 text-xs font-semibold">
                        <a href="{{ route('language.switch', 'sw') }}" class="flex items-center justify-between px-3.5 py-2 hover:bg-teal-50 hover:text-teal-700 transition {{ app()->getLocale() === 'sw' ? 'text-teal-700 font-bold bg-teal-50' : 'text-slate-800' }}">
                            <span>Kiswahili</span>
                        </a>
                        <a href="{{ route('language.switch', 'en') }}" class="flex items-center justify-between px-3.5 py-2 hover:bg-teal-50 hover:text-teal-700 transition {{ app()->getLocale() === 'en' ? 'text-teal-700 font-bold bg-teal-50' : 'text-slate-800' }}">
                            <span>English</span>
                        </a>
                    </div>
                </div>

                <!-- Admin Profile Pill -->
                <a href="{{ route('admin.profile.index') }}" class="flex items-center space-x-2 text-xs font-bold text-slate-800 bg-slate-100 hover:bg-slate-200 px-3 py-1.5 rounded-xl transition">
                    <div class="w-5 h-5 rounded-full bg-slate-900 text-teal-300 flex items-center justify-center text-[10px] font-bold">
                        {{ substr(auth()->user()->first_name ?? 'A', 0, 1) }}
                    </div>
                    <span class="hidden md:inline">{{ auth()->user()->first_name }}</span>
                </a>
            </div>
        </header>

        <!-- Flash alerts -->
        <div class="px-4 sm:px-6 lg:px-8 mt-4">
            @if(session('success'))
                <div class="flex items-center justify-between p-3.5 rounded-xl bg-teal-50 border border-teal-200 text-teal-900 text-xs font-medium shadow-card mb-3">
                    <div class="flex items-center space-x-2">
                        <i data-lucide="check-circle" class="w-4 h-4 text-teal-600"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button type="button" @click="$el.parentElement.remove()" class="text-teal-600 hover:text-teal-900"><i data-lucide="x" class="w-4 h-4"></i></button>
                </div>
            @endif

            @if(session('error'))
                <div class="flex items-center justify-between p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-900 text-xs font-medium shadow-card mb-3">
                    <div class="flex items-center space-x-2">
                        <i data-lucide="alert-triangle" class="w-4 h-4 text-rose-600"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                    <button type="button" @click="$el.parentElement.remove()" class="text-rose-600 hover:text-rose-900"><i data-lucide="x" class="w-4 h-4"></i></button>
                </div>
            @endif
        </div>

        <!-- Page Content -->
        <main class="flex-1 p-4 sm:p-6 lg:p-8">
            @yield('content')
        </main>
    </div>

    <!-- Top Fast Loading Indicator -->
    <div id="page-progress" class="fixed top-0 left-0 h-[2.5px] bg-teal-500 z-50 transition-all duration-200 pointer-events-none opacity-0 w-0"></div>

    <!-- Instant Page Prefetcher (Instant Loading on Hover/Touch) -->
    <script src="https://cdn.jsdelivr.net/npm/instant.page@5.2.0/instantpage.js" type="module"></script>

    <!-- Initialize Lucide Icons & Ultra-Speed Turbo SPA Support -->
    <script>
        function initAdminIcons() {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        }

        if (typeof Turbo !== 'undefined') {
            Turbo.setProgressBarDelay(20);
        }

        // Ultra-Speed Turbo Instant Preloader for Admin (0ms Perceived Transition)
        const ultraPreloadedAdminUrls = new Set();
        function ultraPreloadAdmin(url) {
            if (!url || ultraPreloadedAdminUrls.has(url) || url.includes('#') || url.includes('logout')) return;
            ultraPreloadedAdminUrls.add(url);
            const link = document.createElement('link');
            link.rel = 'prefetch';
            link.href = url;
            link.as = 'document';
            document.head.appendChild(link);
        }

        document.addEventListener('mouseover', (e) => {
            const a = e.target.closest('a');
            if (a && a.href && a.origin === location.origin && a.getAttribute('data-turbo') !== 'false') {
                ultraPreloadAdmin(a.href);
            }
        }, { passive: true });

        document.addEventListener('touchstart', (e) => {
            const a = e.target.closest('a');
            if (a && a.href && a.origin === location.origin && a.getAttribute('data-turbo') !== 'false') {
                ultraPreloadAdmin(a.href);
            }
        }, { passive: true });

        document.addEventListener('DOMContentLoaded', initAdminIcons);
        document.addEventListener('turbo:load', initAdminIcons);
        document.addEventListener('turbo:render', initAdminIcons);

        // Instant Back/Forward Cache Restore
        window.addEventListener('pageshow', (event) => {
            initAdminIcons();
        });
    </script>
    @stack('scripts')
</body>
</html>
