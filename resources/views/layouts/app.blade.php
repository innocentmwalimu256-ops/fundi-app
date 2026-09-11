<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-[#F7F8F7]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, viewport-fit=cover">
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
        :root {
            --sab: env(safe-area-inset-bottom, 0px);
            --sat: env(safe-area-inset-top, 0px);
        }
        body { 
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; 
            -webkit-tap-highlight-color: transparent; 
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
        .custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #F7F8F7; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #E5E7EB; border-radius: 9999px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #98A2B3; }
        .turbo-progress-bar { height: 3px; background: linear-gradient(90deg, #0F9F95, #14B8A6, #2DD4BF); }
        
        /* Touch & Native Mobile Enhancements */
        .btn-tap {
            transition: transform 0.12s cubic-bezier(0.4, 0, 0.2, 1), filter 0.12s ease;
            user-select: none;
        }
        .btn-tap:active {
            transform: scale(0.97);
        }
        .safe-bottom-nav {
            padding-bottom: max(0.5rem, calc(0.4rem + env(safe-area-inset-bottom, 0px)));
        }
        .scrollbar-none {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        .scrollbar-none::-webkit-scrollbar {
            display: none;
        }
        .touch-scroll {
            -webkit-overflow-scrolling: touch;
            scroll-behavior: smooth;
        }
        @media (max-width: 639px) {
            input, select, textarea {
                font-size: 16px !important; /* Prevents auto-zoom on iOS Safari */
            }
        }
    </style>
    @stack('styles')
</head>
<body class="h-full bg-[#F7F8F7] text-[#111827] flex flex-col antialiased selection:bg-teal-500 selection:text-white" x-data="{ mobileSidebarOpen: false }">

    @auth
    <!-- Logged-in Master Workspace Layout (Left Sidebar + Header + Content) -->
    <div class="min-h-screen flex bg-[#F8FAFC]">
        
        <!-- 1. LEFT SIDEBAR (Desktop / Tablet lg:) -->
        <aside class="hidden lg:flex flex-col w-64 xl:w-72 bg-white border-r border-slate-200/90 sticky top-0 h-screen z-30 flex-shrink-0 justify-between py-5 px-4 custom-scrollbar overflow-y-auto select-none shadow-[1px_0_4px_0_rgba(0,0,0,0.02)]">
            
            <div class="space-y-6">
                <!-- Brand Header -->
                <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : (auth()->user()->isTechnician() ? route('technician.dashboard') : route('client.dashboard')) }}" class="flex items-center space-x-3 px-2 group">
                    <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-slate-900 to-slate-800 text-teal-400 flex items-center justify-center font-black shadow-md shadow-slate-900/10 group-hover:scale-105 transition-transform flex-shrink-0 border border-slate-700/40">
                        <i data-lucide="wrench" class="w-5 h-5"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center space-x-1.5">
                            <span class="text-xl font-black tracking-tight text-slate-900">FUNDI</span>
                            <span class="px-1.5 py-0.2 rounded bg-teal-50 text-teal-700 text-[9px] font-black uppercase tracking-wider border border-teal-200/80">PRO</span>
                        </div>
                        <p class="text-[11px] text-teal-600 font-bold uppercase tracking-wider truncate">{{ __('Find. Connect. Fix.') }}</p>
                    </div>
                </a>

                <!-- User Mini-Profile Card -->
                <div class="p-3 rounded-2xl bg-slate-50/90 border border-slate-200/80 flex items-center space-x-3 hover:border-slate-300 transition shadow-xs">
                    <div class="w-10 h-10 rounded-xl bg-slate-900 text-teal-300 flex items-center justify-center font-bold text-xs uppercase shadow-xs flex-shrink-0">
                        {{ auth()->user()->initials }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <h4 class="text-xs font-bold text-slate-900 truncate">{{ auth()->user()->full_name }}</h4>
                        <div class="flex items-center space-x-2 mt-0.5">
                            <span class="px-1.5 py-0.2 rounded bg-teal-50 text-teal-700 text-[9px] font-bold border border-teal-200 uppercase tracking-wider">
                                {{ auth()->user()->role }}
                            </span>
                            <span class="text-[10px] text-emerald-600 font-bold flex items-center">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1 animate-pulse"></span> {{ __('Online') }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Navigation Groups -->
                <nav class="space-y-6 text-xs font-semibold">
                    
                    @if(auth()->user()->isClient())
                    <!-- Client Workspace Links -->
                    <div class="space-y-1">
                        <span class="px-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-2">{{ __('WORKSPACE') }}</span>
                        
                        <a href="{{ route('client.dashboard') }}" class="group relative flex items-center px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('client.dashboard') ? 'bg-teal-50/80 text-teal-800 font-bold border border-teal-200 shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                            @if(request()->routeIs('client.dashboard'))
                                <span class="absolute left-0 top-2 bottom-2 w-1 bg-teal-600 rounded-r-full"></span>
                            @endif
                            <i data-lucide="layout-dashboard" class="w-4 h-4 mr-3 {{ request()->routeIs('client.dashboard') ? 'text-teal-600' : 'text-slate-400 group-hover:text-slate-600' }}"></i>
                            <span>{{ __('Dashboard') }}</span>
                        </a>

                        <a href="{{ route('client.services.index') }}" class="group relative flex items-center px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('client.services.*') ? 'bg-teal-50/80 text-teal-800 font-bold border border-teal-200 shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                            @if(request()->routeIs('client.services.*'))
                                <span class="absolute left-0 top-2 bottom-2 w-1 bg-teal-600 rounded-r-full"></span>
                            @endif
                            <i data-lucide="layers" class="w-4 h-4 mr-3 {{ request()->routeIs('client.services.*') ? 'text-teal-600' : 'text-slate-400 group-hover:text-slate-600' }}"></i>
                            <span>{{ __('Services') }}</span>
                        </a>

                        <a href="{{ route('client.technicians.index') }}" class="group relative flex items-center px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('client.technicians.*') ? 'bg-teal-50/80 text-teal-800 font-bold border border-teal-200 shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                            @if(request()->routeIs('client.technicians.*'))
                                <span class="absolute left-0 top-2 bottom-2 w-1 bg-teal-600 rounded-r-full"></span>
                            @endif
                            <i data-lucide="search" class="w-4 h-4 mr-3 {{ request()->routeIs('client.technicians.*') ? 'text-teal-600' : 'text-slate-400 group-hover:text-slate-600' }}"></i>
                            <span>{{ __('Find Technicians') }}</span>
                        </a>

                        <a href="{{ route('client.requests.index') }}" class="group relative flex items-center justify-between px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('client.requests.*') ? 'bg-teal-50/80 text-teal-800 font-bold border border-teal-200 shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                            <div class="flex items-center">
                                @if(request()->routeIs('client.requests.*'))
                                    <span class="absolute left-0 top-2 bottom-2 w-1 bg-teal-600 rounded-r-full"></span>
                                @endif
                                <i data-lucide="inbox" class="w-4 h-4 mr-3 {{ request()->routeIs('client.requests.*') ? 'text-teal-600' : 'text-slate-400 group-hover:text-slate-600' }}"></i>
                                <span>{{ __('My Requests') }}</span>
                            </div>
                            @php
                                $activeReqCount = \App\Models\ServiceRequest::where('client_id', auth()->id())->whereNotIn('status', ['completed', 'client_confirmed', 'reviewed', 'declined', 'cancelled'])->count();
                            @endphp
                            @if($activeReqCount > 0)
                                <span class="px-1.5 py-0.2 rounded-full bg-teal-100 text-teal-800 text-[10px] font-bold">
                                    {{ $activeReqCount }}
                                </span>
                            @endif
                        </a>

                        <a href="{{ route('client.favorites.index') }}" class="group relative flex items-center px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('client.favorites.*') ? 'bg-teal-50/80 text-teal-800 font-bold border border-teal-200 shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                            @if(request()->routeIs('client.favorites.*'))
                                <span class="absolute left-0 top-2 bottom-2 w-1 bg-teal-600 rounded-r-full"></span>
                            @endif
                            <i data-lucide="bookmark" class="w-4 h-4 mr-3 {{ request()->routeIs('client.favorites.*') ? 'text-teal-600' : 'text-slate-400 group-hover:text-slate-600' }}"></i>
                            <span>{{ __('Saved') }}</span>
                        </a>
                    </div>

                    <!-- Client Communication Group -->
                    <div class="space-y-1">
                        <span class="px-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-2">{{ __('COMMUNICATION') }}</span>
                        
                        <a href="{{ route('messages.index') }}" class="group relative flex items-center justify-between px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('messages.*') ? 'bg-teal-50/80 text-teal-800 font-bold border border-teal-200 shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                            <div class="flex items-center">
                                @if(request()->routeIs('messages.*'))
                                    <span class="absolute left-0 top-2 bottom-2 w-1 bg-teal-600 rounded-r-full"></span>
                                @endif
                                <i data-lucide="message-square" class="w-4 h-4 mr-3 {{ request()->routeIs('messages.*') ? 'text-teal-600' : 'text-slate-400 group-hover:text-slate-600' }}"></i>
                                <span>{{ __('Messages') }}</span>
                            </div>
                        </a>

                        <a href="{{ route('notifications.index') }}" class="group relative flex items-center justify-between px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('notifications.*') ? 'bg-teal-50/80 text-teal-800 font-bold border border-teal-200 shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                            <div class="flex items-center">
                                @if(request()->routeIs('notifications.*'))
                                    <span class="absolute left-0 top-2 bottom-2 w-1 bg-teal-600 rounded-r-full"></span>
                                @endif
                                <i data-lucide="bell" class="w-4 h-4 mr-3 {{ request()->routeIs('notifications.*') ? 'text-teal-600' : 'text-slate-400 group-hover:text-slate-600' }}"></i>
                                <span>{{ __('Notifications') }}</span>
                            </div>
                            @php $unreadCount = auth()->user()->unreadNotificationsCount(); @endphp
                            @if($unreadCount > 0)
                                <span class="px-1.5 py-0.2 rounded-full bg-rose-500 text-[9px] font-bold text-white">
                                    {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                                </span>
                            @endif
                        </a>
                    </div>

                    <!-- Client Career / Verification -->
                    <div class="space-y-1">
                        <span class="px-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-2">{{ __('OPPORTUNITIES') }}</span>
                        @php
                            $hasApp = \App\Models\TechnicianApplication::where('user_id', auth()->id())->first();
                        @endphp
                        @if($hasApp)
                            <a href="{{ route('client.technician-application.status') }}" class="flex items-center px-3.5 py-2.5 rounded-xl text-amber-900 bg-amber-50/90 hover:bg-amber-100 font-bold transition border border-amber-200/80">
                                <i data-lucide="clock" class="w-4 h-4 mr-3 text-amber-600"></i>
                                <span>{{ __('Application Status') }}</span>
                            </a>
                        @else
                            <a href="{{ route('client.become-technician') }}" class="flex items-center px-3.5 py-2.5 rounded-xl text-teal-800 bg-teal-50/90 hover:bg-teal-100 font-bold transition border border-teal-200/80">
                                <i data-lucide="award" class="w-4 h-4 mr-3 text-teal-600"></i>
                                <span>{{ __('Join as Technician') }}</span>
                            </a>
                        @endif
                    </div>
                    @endif

                    @if(auth()->user()->isTechnician())
                    <!-- Technician Workspace Links -->
                    <div class="space-y-1">
                        <span class="px-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-2">{{ __('WORKSPACE') }}</span>
                        
                        <a href="{{ route('technician.dashboard') }}" class="group relative flex items-center px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('technician.dashboard') ? 'bg-teal-50/80 text-teal-800 font-bold border border-teal-200 shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                            @if(request()->routeIs('technician.dashboard'))
                                <span class="absolute left-0 top-2 bottom-2 w-1 bg-teal-600 rounded-r-full"></span>
                            @endif
                            <i data-lucide="layout-dashboard" class="w-4 h-4 mr-3 {{ request()->routeIs('technician.dashboard') ? 'text-teal-600' : 'text-slate-400 group-hover:text-slate-600' }}"></i>
                            <span>{{ __('Dashboard') }}</span>
                        </a>

                        <a href="{{ route('technician.requests.index') }}" class="group relative flex items-center justify-between px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('technician.requests.*') ? 'bg-teal-50/80 text-teal-800 font-bold border border-teal-200 shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                            <div class="flex items-center">
                                @if(request()->routeIs('technician.requests.*'))
                                    <span class="absolute left-0 top-2 bottom-2 w-1 bg-teal-600 rounded-r-full"></span>
                                @endif
                                <i data-lucide="inbox" class="w-4 h-4 mr-3 {{ request()->routeIs('technician.requests.*') ? 'text-teal-600' : 'text-slate-400 group-hover:text-slate-600' }}"></i>
                                <span>{{ __('Job Requests') }}</span>
                            </div>
                            @php
                                $techPending = \App\Models\ServiceRequest::where('technician_id', auth()->id())->whereIn('status', ['requested', 'pending'])->count();
                            @endphp
                            @if($techPending > 0)
                                <span class="px-1.5 py-0.2 rounded-full bg-amber-100 text-amber-800 text-[10px] font-bold">
                                    {{ $techPending }}
                                </span>
                            @endif
                        </a>

                        <a href="{{ route('technician.subscription') }}" class="group relative flex items-center px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('technician.subscription*') ? 'bg-teal-50/80 text-teal-800 font-bold border border-teal-200 shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                            @if(request()->routeIs('technician.subscription*'))
                                <span class="absolute left-0 top-2 bottom-2 w-1 bg-teal-600 rounded-r-full"></span>
                            @endif
                            <i data-lucide="credit-card" class="w-4 h-4 mr-3 {{ request()->routeIs('technician.subscription*') ? 'text-teal-600' : 'text-slate-400 group-hover:text-slate-600' }}"></i>
                            <span>{{ __('Subscription Plan') }}</span>
                        </a>

                        <a href="{{ route('technician.portfolios.index') }}" class="group relative flex items-center px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('technician.portfolios.*') ? 'bg-teal-50/80 text-teal-800 font-bold border border-teal-200 shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                            @if(request()->routeIs('technician.portfolios.*'))
                                <span class="absolute left-0 top-2 bottom-2 w-1 bg-teal-600 rounded-r-full"></span>
                            @endif
                            <i data-lucide="image" class="w-4 h-4 mr-3 {{ request()->routeIs('technician.portfolios.*') ? 'text-teal-600' : 'text-slate-400 group-hover:text-slate-600' }}"></i>
                            <span>{{ __('Portfolio Works') }}</span>
                        </a>

                        <a href="{{ route('technician.reviews.index') }}" class="group relative flex items-center px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('technician.reviews.*') ? 'bg-teal-50/80 text-teal-800 font-bold border border-teal-200 shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                            @if(request()->routeIs('technician.reviews.*'))
                                <span class="absolute left-0 top-2 bottom-2 w-1 bg-teal-600 rounded-r-full"></span>
                            @endif
                            <i data-lucide="star" class="w-4 h-4 mr-3 {{ request()->routeIs('technician.reviews.*') ? 'text-teal-600' : 'text-slate-400 group-hover:text-slate-600' }}"></i>
                            <span>{{ __('Client Reviews') }}</span>
                        </a>
                    </div>

                    <!-- Technician Communication & Schedule -->
                    <div class="space-y-1">
                        <span class="px-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-2">{{ __('COMMUNICATION & SCHEDULE') }}</span>
                        
                        <a href="{{ route('messages.index') }}" class="group relative flex items-center px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('messages.*') ? 'bg-teal-50/80 text-teal-800 font-bold border border-teal-200 shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                            @if(request()->routeIs('messages.*'))
                                <span class="absolute left-0 top-2 bottom-2 w-1 bg-teal-600 rounded-r-full"></span>
                            @endif
                            <i data-lucide="message-square" class="w-4 h-4 mr-3 {{ request()->routeIs('messages.*') ? 'text-teal-600' : 'text-slate-400 group-hover:text-slate-600' }}"></i>
                            <span>{{ __('Messages') }}</span>
                        </a>

                        <a href="{{ route('technician.availability') }}" class="group relative flex items-center px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('technician.availability') ? 'bg-teal-50/80 text-teal-800 font-bold border border-teal-200 shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                            @if(request()->routeIs('technician.availability'))
                                <span class="absolute left-0 top-2 bottom-2 w-1 bg-teal-600 rounded-r-full"></span>
                            @endif
                            <i data-lucide="clock" class="w-4 h-4 mr-3 {{ request()->routeIs('technician.availability') ? 'text-teal-600' : 'text-slate-400 group-hover:text-slate-600' }}"></i>
                            <span>{{ __('Set Availability') }}</span>
                        </a>
                    </div>
                    @endif

                    @if(auth()->user()->isAdmin())
                    <!-- Admin Links -->
                    <div class="space-y-1">
                        <span class="px-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-2">{{ __('ADMINISTRATION') }}</span>
                        
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.dashboard') ? 'bg-teal-50/80 text-teal-800 font-bold border border-teal-200 shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                            <i data-lucide="layout-dashboard" class="w-4 h-4 mr-3 text-teal-600"></i>
                            <span>{{ __('Admin Overview') }}</span>
                        </a>

                        <a href="{{ route('admin.users') }}" class="flex items-center px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.users*') ? 'bg-teal-50/80 text-teal-800 font-bold border border-teal-200 shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                            <i data-lucide="users" class="w-4 h-4 mr-3 text-teal-600"></i>
                            <span>{{ __('Users') }}</span>
                        </a>

                        <a href="{{ route('admin.applications') }}" class="flex items-center px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.applications*') ? 'bg-teal-50/80 text-teal-800 font-bold border border-teal-200 shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                            <i data-lucide="check-square" class="w-4 h-4 mr-3 text-teal-600"></i>
                            <span>{{ __('Verification') }}</span>
                        </a>

                        <a href="{{ route('admin.complaints') }}" class="flex items-center px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.complaints*') ? 'bg-teal-50/80 text-teal-800 font-bold border border-teal-200 shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                            <i data-lucide="alert-circle" class="w-4 h-4 mr-3 text-teal-600"></i>
                            <span>{{ __('Issues & Disputes') }}</span>
                        </a>

                        <a href="{{ route('admin.logs') }}" class="flex items-center px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.logs*') ? 'bg-teal-50/80 text-teal-800 font-bold border border-teal-200 shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                            <i data-lucide="file-text" class="w-4 h-4 mr-3 text-teal-600"></i>
                            <span>{{ __('Audit Logs') }}</span>
                        </a>
                    </div>
                    @endif

                </nav>
            </div>

            <!-- Left Sidebar Footer: Language & Logout -->
            <div class="pt-4 border-t border-slate-200/80 space-y-2">
                <!-- Language Switcher Inline -->
                <div class="flex items-center justify-between px-3 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs font-bold text-slate-800">
                    <div class="flex items-center space-x-2">
                        <i data-lucide="globe" class="w-3.5 h-3.5 text-teal-600"></i>
                        <span>{{ app()->getLocale() === 'en' ? 'English' : 'Kiswahili' }}</span>
                    </div>
                    <a href="{{ route('language.switch', app()->getLocale() === 'en' ? 'sw' : 'en') }}" class="text-[10px] text-teal-600 hover:text-teal-700 font-bold uppercase underline">
                        {{ app()->getLocale() === 'en' ? 'Badili (SW)' : 'Switch (EN)' }}
                    </a>
                </div>

                <!-- Logout Form -->
                <form method="POST" action="{{ route('logout') }}" data-turbo="false">
                    @csrf
                    <button type="submit" class="w-full flex items-center px-3.5 py-2.5 text-xs font-bold text-rose-600 hover:bg-rose-50 rounded-xl transition cursor-pointer">
                        <i data-lucide="log-out" class="w-4 h-4 mr-3"></i>
                        <span>{{ __('Logout') }}</span>
                    </button>
                </form>
            </div>

        </aside>

        <!-- 2. MOBILE SLIDE-OVER DRAWER OVERLAY -->
        <div x-show="mobileSidebarOpen" x-cloak class="fixed inset-0 z-50 lg:hidden flex">
            <!-- Backdrop -->
            <div x-show="mobileSidebarOpen" @click="mobileSidebarOpen = false" class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm transition-opacity"></div>
            
            <!-- Drawer Menu -->
            <div class="relative w-72 max-w-[85vw] bg-white h-full flex flex-col justify-between py-6 px-4 z-10 shadow-2xl custom-scrollbar overflow-y-auto">
                <div class="space-y-6">
                    <div class="flex items-center justify-between px-2">
                        <div class="flex items-center space-x-3">
                            <div class="w-9 h-9 rounded-xl bg-slate-900 text-teal-400 flex items-center justify-center font-black">
                                <i data-lucide="wrench" class="w-4 h-4"></i>
                            </div>
                            <span class="text-lg font-black text-slate-900">FUNDI</span>
                        </div>
                        <button @click="mobileSidebarOpen = false" class="p-1.5 rounded-xl hover:bg-slate-100 text-slate-400 hover:text-slate-900">
                            <i data-lucide="x" class="w-5 h-5"></i>
                        </button>
                    </div>

                    <!-- User Mini-Profile -->
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 flex items-center space-x-3">
                        <div class="w-9 h-9 rounded-lg bg-slate-900 text-teal-300 flex items-center justify-center font-bold text-xs uppercase">
                            {{ auth()->user()->initials }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <h4 class="text-xs font-bold text-slate-900 truncate">{{ auth()->user()->full_name }}</h4>
                            <span class="text-[10px] text-teal-700 font-bold uppercase">{{ auth()->user()->role }}</span>
                        </div>
                    </div>

                    <!-- Drawer Links -->
                    <nav class="space-y-1 text-xs font-semibold">
                        @if(auth()->user()->isClient())
                            <a href="{{ route('client.dashboard') }}" class="flex items-center px-3 py-2.5 rounded-xl {{ request()->routeIs('client.dashboard') ? 'bg-teal-50 text-teal-800 font-bold' : 'text-slate-600' }}">
                                <i data-lucide="layout-dashboard" class="w-4 h-4 mr-3"></i> {{ __('Dashboard') }}
                            </a>
                            <a href="{{ route('client.services.index') }}" class="flex items-center px-3 py-2.5 rounded-xl {{ request()->routeIs('client.services.*') ? 'bg-teal-50 text-teal-800 font-bold' : 'text-slate-600' }}">
                                <i data-lucide="layers" class="w-4 h-4 mr-3"></i> {{ __('Services') }}
                            </a>
                            <a href="{{ route('client.technicians.index') }}" class="flex items-center px-3 py-2.5 rounded-xl {{ request()->routeIs('client.technicians.*') ? 'bg-teal-50 text-teal-800 font-bold' : 'text-slate-600' }}">
                                <i data-lucide="search" class="w-4 h-4 mr-3"></i> {{ __('Find Technicians') }}
                            </a>
                            <a href="{{ route('client.requests.index') }}" class="flex items-center px-3 py-2.5 rounded-xl {{ request()->routeIs('client.requests.*') ? 'bg-teal-50 text-teal-800 font-bold' : 'text-slate-600' }}">
                                <i data-lucide="inbox" class="w-4 h-4 mr-3"></i> {{ __('My Requests') }}
                            </a>
                            <a href="{{ route('client.favorites.index') }}" class="flex items-center px-3 py-2.5 rounded-xl {{ request()->routeIs('client.favorites.*') ? 'bg-teal-50 text-teal-800 font-bold' : 'text-slate-600' }}">
                                <i data-lucide="bookmark" class="w-4 h-4 mr-3"></i> {{ __('Saved') }}
                            </a>
                        @elseif(auth()->user()->isTechnician())
                            <a href="{{ route('technician.dashboard') }}" class="flex items-center px-3 py-2.5 rounded-xl {{ request()->routeIs('technician.dashboard') ? 'bg-teal-50 text-teal-800 font-bold' : 'text-slate-600' }}">
                                <i data-lucide="layout-dashboard" class="w-4 h-4 mr-3"></i> {{ __('Dashboard') }}
                            </a>
                            <a href="{{ route('technician.requests.index') }}" class="flex items-center px-3 py-2.5 rounded-xl {{ request()->routeIs('technician.requests.*') ? 'bg-teal-50 text-teal-800 font-bold' : 'text-slate-600' }}">
                                <i data-lucide="inbox" class="w-4 h-4 mr-3"></i> {{ __('Job Requests') }}
                            </a>
                            <a href="{{ route('technician.subscription') }}" class="flex items-center px-3 py-2.5 rounded-xl {{ request()->routeIs('technician.subscription*') ? 'bg-teal-50 text-teal-800 font-bold' : 'text-slate-600' }}">
                                <i data-lucide="credit-card" class="w-4 h-4 mr-3"></i> {{ __('Subscription Plan') }}
                            </a>
                            <a href="{{ route('technician.portfolios.index') }}" class="flex items-center px-3 py-2.5 rounded-xl {{ request()->routeIs('technician.portfolios.*') ? 'bg-teal-50 text-teal-800 font-bold' : 'text-slate-600' }}">
                                <i data-lucide="image" class="w-4 h-4 mr-3"></i> {{ __('Portfolio') }}
                            </a>
                        @endif
                        <a href="{{ route('messages.index') }}" class="flex items-center px-3 py-2.5 rounded-xl {{ request()->routeIs('messages.*') ? 'bg-teal-50 text-teal-800 font-bold' : 'text-slate-600' }}">
                            <i data-lucide="message-square" class="w-4 h-4 mr-3"></i> {{ __('Messages') }}
                        </a>
                    </nav>
                </div>

                <form method="POST" action="{{ route('logout') }}" data-turbo="false" class="pt-4 border-t border-slate-200">
                    @csrf
                    <button type="submit" class="w-full flex items-center px-3 py-2 text-xs font-bold text-rose-600 hover:bg-rose-50 rounded-xl">
                        <i data-lucide="log-out" class="w-4 h-4 mr-3"></i> {{ __('Logout') }}
                    </button>
                </form>
            </div>
        </div>

        <!-- 3. MAIN WORKSPACE CONTAINER (Header + Content + Footer) -->
        <div class="flex-1 flex flex-col min-w-0">
            
            <!-- Top Workspace Glassmorphic Header -->
            <header class="sticky top-0 z-20 bg-white/95 backdrop-blur-md border-b border-slate-200/90 transition-all shadow-xs">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between items-center h-16">
                        
                        <!-- Left: Hamburger (Mobile) & Search / Breadcrumbs -->
                        <div class="flex items-center space-x-3 flex-1 max-w-md">
                            <button @click="mobileSidebarOpen = true" class="lg:hidden p-2 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition cursor-pointer">
                                <i data-lucide="menu" class="w-5 h-5"></i>
                            </button>
                            
                            <!-- Search Bar / Quick Action (Desktop) -->
                            <form method="GET" action="{{ route('client.technicians.index') }}" class="hidden sm:flex items-center w-full">
                                <div class="relative w-full">
                                    <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                                    <input type="text" name="search" placeholder="{{ __('Search services, technicians, locations...') }}" class="w-full pl-9 pr-12 py-1.5 bg-slate-100/80 hover:bg-slate-100 focus:bg-white rounded-xl border border-slate-200/80 focus:border-teal-500 text-xs text-slate-900 placeholder-slate-400 focus:outline-none transition font-medium">
                                    <span class="absolute right-2.5 top-1/2 -translate-y-1/2 px-1.5 py-0.5 rounded bg-slate-200 text-slate-500 text-[10px] font-mono font-bold">/</span>
                                </div>
                            </form>
                        </div>

                        <!-- Right: Quick Post CTA, Language, Notifications & Profile -->
                        <div class="flex items-center space-x-2 sm:space-x-3">
                            @if(auth()->user()->isClient())
                                <a href="{{ route('client.requests.create') }}" class="btn-tap hidden sm:flex items-center space-x-1.5 px-3.5 py-1.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs shadow-xs transition">
                                    <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                                    <span>{{ __('Post Request') }}</span>
                                </a>
                            @endif

                            <!-- Language Toggle -->
                            <a href="{{ route('language.switch', app()->getLocale() === 'en' ? 'sw' : 'en') }}" class="px-2.5 py-1.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-xs font-bold text-slate-800 transition shadow-xs flex items-center space-x-1">
                                <i data-lucide="globe" class="w-3.5 h-3.5 text-teal-600"></i>
                                <span>{{ app()->getLocale() === 'en' ? 'EN' : 'SW' }}</span>
                            </a>

                            <!-- Notifications -->
                            <a href="{{ route('notifications.index') }}" class="relative p-2 text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-xl transition">
                                <i data-lucide="bell" class="w-4 h-4"></i>
                                @php $unreadCount = auth()->user()->unreadNotificationsCount(); @endphp
                                @if($unreadCount > 0)
                                    <span class="absolute top-1 right-1 flex h-4 w-4 items-center justify-center rounded-full bg-rose-500 text-[9px] font-bold text-white shadow-xs">
                                        {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                                    </span>
                                @endif
                            </a>

                            <!-- Messages -->
                            <a href="{{ route('messages.index') }}" class="p-2 text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-xl transition">
                                <i data-lucide="message-square" class="w-4 h-4"></i>
                            </a>

                            <!-- User Profile Chip -->
                            <div class="flex items-center space-x-2 pl-2 border-l border-slate-200">
                                <div class="w-8 h-8 rounded-xl bg-slate-900 text-teal-300 flex items-center justify-center font-bold text-xs uppercase shadow-xs">
                                    {{ auth()->user()->initials }}
                                </div>
                                <div class="hidden xl:flex flex-col text-left">
                                    <span class="text-xs font-bold text-slate-900 max-w-[100px] truncate leading-tight">
                                        {{ auth()->user()->first_name }}
                                    </span>
                                    <span class="text-[10px] text-slate-400 capitalize leading-tight">
                                        {{ auth()->user()->role }}
                                    </span>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </header>

            <!-- Flash Alerts Container -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full">
                @if(session('success'))
                    <div class="flex items-center justify-between p-4 mb-3 rounded-2xl bg-teal-50 border border-teal-200 text-teal-900 text-xs font-medium shadow-xs" role="alert">
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
                    <div class="flex items-center justify-between p-4 mb-3 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 text-xs font-medium shadow-xs" role="alert">
                        <div class="flex items-center space-x-2.5">
                            <i data-lucide="alert-triangle" class="w-5 h-5 text-rose-600 flex-shrink-0"></i>
                            <span>{{ session('error') }}</span>
                        </div>
                        <button type="button" @click="$el.parentElement.remove()" class="text-rose-600 hover:text-rose-900 p-1">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </button>
                    </div>
                @endif
            </div>

            <!-- Page Main Content -->
            <main class="flex-grow flex flex-col p-4 sm:p-6 lg:p-8">
                @yield('content')
            </main>

        </div>

    </div>
    @endauth

    @guest
        @if(!request()->routeIs('login') && !request()->routeIs('register') && !request()->routeIs('password.*'))
        <header class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-[#E5E7EB] transition-all">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16 sm:h-[72px]">
                    <!-- Brand -->
                    <a href="{{ url('/') }}" class="flex items-center space-x-2.5 group">
                        <div class="w-10 h-10 rounded-xl bg-navy-900 text-teal-400 flex items-center justify-center font-black shadow-sm group-hover:bg-navy-800 transition">
                            <i data-lucide="wrench" class="w-5 h-5"></i>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-xl font-extrabold tracking-tight text-navy-900">FUNDI</span>
                            <span class="text-[10px] text-[#667085] font-medium tracking-tight -mt-0.5 hidden sm:block">Find. Connect. Fix.</span>
                        </div>
                    </a>

                    <!-- Desktop Nav Links -->
                    <nav class="hidden md:flex items-center space-x-2">
                        <a href="{{ route('client.services.index') }}" class="px-3.5 py-2 text-sm font-semibold rounded-xl text-[#667085] hover:text-[#111827] hover:bg-slate-100 transition">
                            {{ __('Services') }}
                        </a>
                        <a href="{{ route('client.technicians.index') }}" class="px-3.5 py-2 text-sm font-semibold rounded-xl text-[#667085] hover:text-[#111827] hover:bg-slate-100 transition">
                            {{ __('Find Technicians') }}
                        </a>
                    </nav>

                    <!-- Right CTAs -->
                    <div class="flex items-center space-x-2 sm:space-x-3">
                        <!-- Language Toggle -->
                        <a href="{{ route('language.switch', app()->getLocale() === 'en' ? 'sw' : 'en') }}" class="px-2.5 py-1.5 rounded-xl border border-[#E5E7EB] bg-white hover:bg-slate-50 text-xs font-bold text-navy-900 transition shadow-xs flex items-center space-x-1">
                            <i data-lucide="globe" class="w-3.5 h-3.5 text-teal-600"></i>
                            <span>{{ app()->getLocale() === 'en' ? 'EN' : 'SW' }}</span>
                        </a>

                        <a href="{{ route('login') }}" class="px-3.5 py-2 text-xs font-bold text-navy-900 hover:bg-slate-100 rounded-xl transition">
                            {{ __('Sign In') }}
                        </a>
                        <a href="{{ route('register') }}" class="px-4 py-2 rounded-xl bg-teal-500 hover:bg-teal-600 text-white text-xs font-bold shadow-xs hover:shadow-subtle active:scale-[0.98] transition">
                            {{ __('Join Now') }}
                        </a>
                    </div>
                </div>
            </div>
        </header>
        @endif

        <main class="flex-grow flex flex-col">
            @yield('content')
        </main>
    @endguest



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
