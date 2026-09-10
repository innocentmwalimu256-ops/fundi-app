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
    <div class="min-h-screen flex bg-[#F7F8F7]">
        
        <!-- 1. LEFT SIDEBAR (Desktop / Tablet lg:) -->
        <aside class="hidden lg:flex flex-col w-64 xl:w-72 bg-white border-r border-[#E5E7EB] sticky top-0 h-screen z-30 flex-shrink-0 justify-between py-6 px-4 custom-scrollbar overflow-y-auto">
            
            <div class="space-y-6">
                <!-- Brand Header -->
                <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : (auth()->user()->isTechnician() ? route('technician.dashboard') : route('client.dashboard')) }}" class="flex items-center space-x-3 px-2">
                    <div class="w-10 h-10 rounded-2xl bg-navy-900 text-teal-400 flex items-center justify-center font-black shadow-xs flex-shrink-0">
                        <i data-lucide="wrench" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <div class="flex items-center space-x-1.5">
                            <span class="text-xl font-black tracking-tight text-navy-900">FUNDI</span>
                        </div>
                        <p class="text-[11px] text-teal-600 font-bold uppercase tracking-wider">Find. Connect. Fix.</p>
                    </div>
                </a>

                <!-- User Mini-Profile Card (Matching Reference Design) -->
                <div class="p-3.5 rounded-2xl bg-[#F7F8F7] border border-[#E5E7EB] flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-navy-900 text-teal-300 flex items-center justify-center font-bold text-xs uppercase shadow-xs flex-shrink-0">
                        {{ auth()->user()->initials }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <h4 class="text-xs font-bold text-navy-900 truncate">{{ auth()->user()->full_name }}</h4>
                        <div class="flex items-center space-x-1.5 mt-0.5">
                            <span class="px-1.5 py-0.2 rounded bg-[#F0FDFB] text-teal-700 text-[10px] font-bold border border-teal-200 uppercase">
                                {{ auth()->user()->role }}
                            </span>
                            <span class="text-[10px] text-[#12B76A] font-bold flex items-center">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#12B76A] mr-1"></span> Active
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Navigation Groups -->
                <nav class="space-y-6 text-xs font-semibold">
                    
                    @if(auth()->user()->isClient())
                    <!-- Client Workspace Links -->
                    <div class="space-y-1">
                        <span class="px-3 text-[10px] font-bold text-[#98A2B3] uppercase tracking-wider block mb-2">{{ __('WORKSPACE') }}</span>
                        
                        <a href="{{ route('client.dashboard') }}" class="flex items-center px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('client.dashboard') ? 'bg-[#F0FDFB] text-teal-700 font-bold border border-teal-200 shadow-xs' : 'text-[#667085] hover:text-navy-900 hover:bg-[#F7F8F7]' }}">
                            <i data-lucide="layout-dashboard" class="w-4 h-4 mr-3 {{ request()->routeIs('client.dashboard') ? 'text-teal-600' : 'text-[#98A2B3]' }}"></i>
                            <span>{{ __('Dashboard') }}</span>
                        </a>

                        <a href="{{ route('client.services.index') }}" class="flex items-center px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('client.services.*') ? 'bg-[#F0FDFB] text-teal-700 font-bold border border-teal-200 shadow-xs' : 'text-[#667085] hover:text-navy-900 hover:bg-[#F7F8F7]' }}">
                            <i data-lucide="layers" class="w-4 h-4 mr-3 {{ request()->routeIs('client.services.*') ? 'text-teal-600' : 'text-[#98A2B3]' }}"></i>
                            <span>{{ __('Services') }}</span>
                        </a>

                        <a href="{{ route('client.technicians.index') }}" class="flex items-center px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('client.technicians.*') ? 'bg-[#F0FDFB] text-teal-700 font-bold border border-teal-200 shadow-xs' : 'text-[#667085] hover:text-navy-900 hover:bg-[#F7F8F7]' }}">
                            <i data-lucide="search" class="w-4 h-4 mr-3 {{ request()->routeIs('client.technicians.*') ? 'text-teal-600' : 'text-[#98A2B3]' }}"></i>
                            <span>{{ __('Find Technicians') }}</span>
                        </a>

                        <a href="{{ route('client.requests.index') }}" class="flex items-center px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('client.requests.*') ? 'bg-[#F0FDFB] text-teal-700 font-bold border border-teal-200 shadow-xs' : 'text-[#667085] hover:text-navy-900 hover:bg-[#F7F8F7]' }}">
                            <i data-lucide="inbox" class="w-4 h-4 mr-3 {{ request()->routeIs('client.requests.*') ? 'text-teal-600' : 'text-[#98A2B3]' }}"></i>
                            <span>{{ __('My Requests') }}</span>
                        </a>

                        <a href="{{ route('client.favorites.index') }}" class="flex items-center px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('client.favorites.*') ? 'bg-[#F0FDFB] text-teal-700 font-bold border border-teal-200 shadow-xs' : 'text-[#667085] hover:text-navy-900 hover:bg-[#F7F8F7]' }}">
                            <i data-lucide="bookmark" class="w-4 h-4 mr-3 {{ request()->routeIs('client.favorites.*') ? 'text-teal-600' : 'text-[#98A2B3]' }}"></i>
                            <span>{{ __('Saved') }}</span>
                        </a>
                    </div>

                    <!-- Client Communication Group -->
                    <div class="space-y-1">
                        <span class="px-3 text-[10px] font-bold text-[#98A2B3] uppercase tracking-wider block mb-2">{{ __('COMMUNICATION') }}</span>
                        
                        <a href="{{ route('messages.index') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('messages.*') ? 'bg-[#F0FDFB] text-teal-700 font-bold border border-teal-200 shadow-xs' : 'text-[#667085] hover:text-navy-900 hover:bg-[#F7F8F7]' }}">
                            <div class="flex items-center">
                                <i data-lucide="message-square" class="w-4 h-4 mr-3 {{ request()->routeIs('messages.*') ? 'text-teal-600' : 'text-[#98A2B3]' }}"></i>
                                <span>{{ __('Messages') }}</span>
                            </div>
                        </a>

                        <a href="{{ route('notifications.index') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('notifications.*') ? 'bg-[#F0FDFB] text-teal-700 font-bold border border-teal-200 shadow-xs' : 'text-[#667085] hover:text-navy-900 hover:bg-[#F7F8F7]' }}">
                            <div class="flex items-center">
                                <i data-lucide="bell" class="w-4 h-4 mr-3 {{ request()->routeIs('notifications.*') ? 'text-teal-600' : 'text-[#98A2B3]' }}"></i>
                                <span>{{ __('Notifications') }}</span>
                            </div>
                            @php $unreadCount = auth()->user()->unreadNotificationsCount(); @endphp
                            @if($unreadCount > 0)
                                <span class="px-1.5 py-0.5 rounded-full bg-[#F04438] text-[9px] font-bold text-white">
                                    {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                                </span>
                            @endif
                        </a>
                    </div>

                    <!-- Client Career / Verification -->
                    <div class="space-y-1">
                        <span class="px-3 text-[10px] font-bold text-[#98A2B3] uppercase tracking-wider block mb-2">{{ __('OPPORTUNITIES') }}</span>
                        @php
                            $hasApp = \App\Models\TechnicianApplication::where('user_id', auth()->id())->first();
                        @endphp
                        @if($hasApp)
                            <a href="{{ route('client.technician-application.status') }}" class="flex items-center px-3.5 py-2.5 rounded-xl text-amber-800 bg-amber-50/70 hover:bg-amber-100 font-semibold transition border border-amber-200">
                                <i data-lucide="clock" class="w-4 h-4 mr-3 text-amber-600"></i>
                                <span>{{ __('Application Status') }}</span>
                            </a>
                        @else
                            <a href="{{ route('client.become-technician') }}" class="flex items-center px-3.5 py-2.5 rounded-xl text-teal-700 bg-teal-50/70 hover:bg-teal-100 font-bold transition border border-teal-200">
                                <i data-lucide="award" class="w-4 h-4 mr-3 text-teal-600"></i>
                                <span>{{ __('Join as Technician') }}</span>
                            </a>
                        @endif
                    </div>
                    @endif

                    @if(auth()->user()->isTechnician())
                    <!-- Technician Workspace Links -->
                    <div class="space-y-1">
                        <span class="px-3 text-[10px] font-bold text-[#98A2B3] uppercase tracking-wider block mb-2">{{ __('WORKSPACE') }}</span>
                        
                        <a href="{{ route('technician.dashboard') }}" class="flex items-center px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('technician.dashboard') ? 'bg-[#F0FDFB] text-teal-700 font-bold border border-teal-200 shadow-xs' : 'text-[#667085] hover:text-navy-900 hover:bg-[#F7F8F7]' }}">
                            <i data-lucide="layout-dashboard" class="w-4 h-4 mr-3 {{ request()->routeIs('technician.dashboard') ? 'text-teal-600' : 'text-[#98A2B3]' }}"></i>
                            <span>{{ __('Dashboard') }}</span>
                        </a>

                        <a href="{{ route('technician.requests.index') }}" class="flex items-center px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('technician.requests.*') ? 'bg-[#F0FDFB] text-teal-700 font-bold border border-teal-200 shadow-xs' : 'text-[#667085] hover:text-navy-900 hover:bg-[#F7F8F7]' }}">
                            <i data-lucide="inbox" class="w-4 h-4 mr-3 {{ request()->routeIs('technician.requests.*') ? 'text-teal-600' : 'text-[#98A2B3]' }}"></i>
                            <span>{{ __('Job Requests') }}</span>
                        </a>

                        <a href="{{ route('technician.subscription') }}" class="flex items-center px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('technician.subscription*') ? 'bg-[#F0FDFB] text-teal-700 font-bold border border-teal-200 shadow-xs' : 'text-[#667085] hover:text-navy-900 hover:bg-[#F7F8F7]' }}">
                            <i data-lucide="credit-card" class="w-4 h-4 mr-3 {{ request()->routeIs('technician.subscription*') ? 'text-teal-600' : 'text-[#98A2B3]' }}"></i>
                            <span>{{ __('Subscription Plan') }}</span>
                        </a>

                        <a href="{{ route('technician.portfolios.index') }}" class="flex items-center px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('technician.portfolios.*') ? 'bg-[#F0FDFB] text-teal-700 font-bold border border-teal-200 shadow-xs' : 'text-[#667085] hover:text-navy-900 hover:bg-[#F7F8F7]' }}">
                            <i data-lucide="image" class="w-4 h-4 mr-3 {{ request()->routeIs('technician.portfolios.*') ? 'text-teal-600' : 'text-[#98A2B3]' }}"></i>
                            <span>{{ __('Portfolio Works') }}</span>
                        </a>

                        <a href="{{ route('technician.reviews.index') }}" class="flex items-center px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('technician.reviews.*') ? 'bg-[#F0FDFB] text-teal-700 font-bold border border-teal-200 shadow-xs' : 'text-[#667085] hover:text-navy-900 hover:bg-[#F7F8F7]' }}">
                            <i data-lucide="star" class="w-4 h-4 mr-3 {{ request()->routeIs('technician.reviews.*') ? 'text-teal-600' : 'text-[#98A2B3]' }}"></i>
                            <span>{{ __('Client Reviews') }}</span>
                        </a>
                    </div>

                    <!-- Technician Communication & Schedule -->
                    <div class="space-y-1">
                        <span class="px-3 text-[10px] font-bold text-[#98A2B3] uppercase tracking-wider block mb-2">{{ __('COMMUNICATION & AVAILABILITY') }}</span>
                        
                        <a href="{{ route('messages.index') }}" class="flex items-center px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('messages.*') ? 'bg-[#F0FDFB] text-teal-700 font-bold border border-teal-200 shadow-xs' : 'text-[#667085] hover:text-navy-900 hover:bg-[#F7F8F7]' }}">
                            <i data-lucide="message-square" class="w-4 h-4 mr-3 {{ request()->routeIs('messages.*') ? 'text-teal-600' : 'text-[#98A2B3]' }}"></i>
                            <span>{{ __('Messages') }}</span>
                        </a>

                        <a href="{{ route('technician.availability') }}" class="flex items-center px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('technician.availability') ? 'bg-[#F0FDFB] text-teal-700 font-bold border border-teal-200 shadow-xs' : 'text-[#667085] hover:text-navy-900 hover:bg-[#F7F8F7]' }}">
                            <i data-lucide="clock" class="w-4 h-4 mr-3 {{ request()->routeIs('technician.availability') ? 'text-teal-600' : 'text-[#98A2B3]' }}"></i>
                            <span>{{ __('Set Availability') }}</span>
                        </a>
                    </div>
                    @endif

                    @if(auth()->user()->isAdmin())
                    <!-- Admin Links -->
                    <div class="space-y-1">
                        <span class="px-3 text-[10px] font-bold text-[#98A2B3] uppercase tracking-wider block mb-2">{{ __('ADMINISTRATION') }}</span>
                        
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.dashboard') ? 'bg-[#F0FDFB] text-teal-700 font-bold border border-teal-200 shadow-xs' : 'text-[#667085] hover:text-navy-900 hover:bg-[#F7F8F7]' }}">
                            <i data-lucide="layout-dashboard" class="w-4 h-4 mr-3 {{ request()->routeIs('admin.dashboard') ? 'text-teal-600' : 'text-[#98A2B3]' }}"></i>
                            <span>{{ __('Admin Overview') }}</span>
                        </a>

                        <a href="{{ route('admin.users') }}" class="flex items-center px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.users*') ? 'bg-[#F0FDFB] text-teal-700 font-bold border border-teal-200 shadow-xs' : 'text-[#667085] hover:text-navy-900 hover:bg-[#F7F8F7]' }}">
                            <i data-lucide="users" class="w-4 h-4 mr-3 {{ request()->routeIs('admin.users*') ? 'text-teal-600' : 'text-[#98A2B3]' }}"></i>
                            <span>{{ __('Users') }}</span>
                        </a>

                        <a href="{{ route('admin.applications') }}" class="flex items-center px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.applications*') ? 'bg-[#F0FDFB] text-teal-700 font-bold border border-teal-200 shadow-xs' : 'text-[#667085] hover:text-navy-900 hover:bg-[#F7F8F7]' }}">
                            <i data-lucide="check-square" class="w-4 h-4 mr-3 {{ request()->routeIs('admin.applications*') ? 'text-teal-600' : 'text-[#98A2B3]' }}"></i>
                            <span>{{ __('Verification') }}</span>
                        </a>

                        <a href="{{ route('admin.complaints') }}" class="flex items-center px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.complaints*') ? 'bg-[#F0FDFB] text-teal-700 font-bold border border-teal-200 shadow-xs' : 'text-[#667085] hover:text-navy-900 hover:bg-[#F7F8F7]' }}">
                            <i data-lucide="alert-circle" class="w-4 h-4 mr-3 {{ request()->routeIs('admin.complaints*') ? 'text-teal-600' : 'text-[#98A2B3]' }}"></i>
                            <span>{{ __('Issues & Disputes') }}</span>
                        </a>

                        <a href="{{ route('admin.logs') }}" class="flex items-center px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.logs*') ? 'bg-[#F0FDFB] text-teal-700 font-bold border border-teal-200 shadow-xs' : 'text-[#667085] hover:text-navy-900 hover:bg-[#F7F8F7]' }}">
                            <i data-lucide="file-text" class="w-4 h-4 mr-3 {{ request()->routeIs('admin.logs*') ? 'text-teal-600' : 'text-[#98A2B3]' }}"></i>
                            <span>{{ __('Audit Logs') }}</span>
                        </a>
                    </div>
                    @endif

                </nav>
            </div>

            <!-- Left Sidebar Footer: Language & Logout -->
            <div class="pt-4 border-t border-[#E5E7EB] space-y-2">
                <!-- Language Switcher Inline -->
                <div class="flex items-center justify-between px-3 py-2 rounded-xl bg-[#F7F8F7] border border-[#E5E7EB] text-xs font-bold text-navy-900">
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
                    <button type="submit" class="w-full flex items-center px-3.5 py-2.5 text-xs font-bold text-[#F04438] hover:bg-rose-50 rounded-xl transition cursor-pointer">
                        <i data-lucide="log-out" class="w-4 h-4 mr-3"></i>
                        <span>{{ __('Logout') }}</span>
                    </button>
                </form>
            </div>

        </aside>

        <!-- 2. MOBILE SLIDE-OVER DRAWER OVERLAY -->
        <div x-show="mobileSidebarOpen" x-cloak class="fixed inset-0 z-50 lg:hidden flex">
            <!-- Backdrop -->
            <div x-show="mobileSidebarOpen" @click="mobileSidebarOpen = false" class="fixed inset-0 bg-navy-950/60 backdrop-blur-sm transition-opacity"></div>
            
            <!-- Drawer Menu -->
            <div class="relative w-72 max-w-[85vw] bg-white h-full flex flex-col justify-between py-6 px-4 z-10 shadow-2xl custom-scrollbar overflow-y-auto">
                <div class="space-y-6">
                    <div class="flex items-center justify-between px-2">
                        <div class="flex items-center space-x-3">
                            <div class="w-9 h-9 rounded-xl bg-navy-900 text-teal-400 flex items-center justify-center font-black">
                                <i data-lucide="wrench" class="w-4 h-4"></i>
                            </div>
                            <span class="text-lg font-black text-navy-900">FUNDI</span>
                        </div>
                        <button @click="mobileSidebarOpen = false" class="p-1.5 rounded-xl hover:bg-slate-100 text-slate-400 hover:text-navy-900">
                            <i data-lucide="x" class="w-5 h-5"></i>
                        </button>
                    </div>

                    <!-- User Mini-Profile -->
                    <div class="p-3 rounded-xl bg-[#F7F8F7] border border-[#E5E7EB] flex items-center space-x-3">
                        <div class="w-9 h-9 rounded-lg bg-navy-900 text-teal-300 flex items-center justify-center font-bold text-xs uppercase">
                            {{ auth()->user()->initials }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <h4 class="text-xs font-bold text-navy-900 truncate">{{ auth()->user()->full_name }}</h4>
                            <span class="text-[10px] text-teal-700 font-bold uppercase">{{ auth()->user()->role }}</span>
                        </div>
                    </div>

                    <!-- Drawer Links -->
                    <nav class="space-y-1 text-xs font-semibold">
                        @if(auth()->user()->isClient())
                            <a href="{{ route('client.dashboard') }}" class="flex items-center px-3 py-2 rounded-xl {{ request()->routeIs('client.dashboard') ? 'bg-[#F0FDFB] text-teal-700 font-bold' : 'text-[#667085]' }}">
                                <i data-lucide="layout-dashboard" class="w-4 h-4 mr-3"></i> {{ __('Dashboard') }}
                            </a>
                            <a href="{{ route('client.services.index') }}" class="flex items-center px-3 py-2 rounded-xl {{ request()->routeIs('client.services.*') ? 'bg-[#F0FDFB] text-teal-700 font-bold' : 'text-[#667085]' }}">
                                <i data-lucide="layers" class="w-4 h-4 mr-3"></i> {{ __('Services') }}
                            </a>
                            <a href="{{ route('client.technicians.index') }}" class="flex items-center px-3 py-2 rounded-xl {{ request()->routeIs('client.technicians.*') ? 'bg-[#F0FDFB] text-teal-700 font-bold' : 'text-[#667085]' }}">
                                <i data-lucide="search" class="w-4 h-4 mr-3"></i> {{ __('Find Technicians') }}
                            </a>
                            <a href="{{ route('client.requests.index') }}" class="flex items-center px-3 py-2 rounded-xl {{ request()->routeIs('client.requests.*') ? 'bg-[#F0FDFB] text-teal-700 font-bold' : 'text-[#667085]' }}">
                                <i data-lucide="inbox" class="w-4 h-4 mr-3"></i> {{ __('My Requests') }}
                            </a>
                            <a href="{{ route('client.favorites.index') }}" class="flex items-center px-3 py-2 rounded-xl {{ request()->routeIs('client.favorites.*') ? 'bg-[#F0FDFB] text-teal-700 font-bold' : 'text-[#667085]' }}">
                                <i data-lucide="bookmark" class="w-4 h-4 mr-3"></i> {{ __('Saved') }}
                            </a>
                        @elseif(auth()->user()->isTechnician())
                            <a href="{{ route('technician.dashboard') }}" class="flex items-center px-3 py-2 rounded-xl {{ request()->routeIs('technician.dashboard') ? 'bg-[#F0FDFB] text-teal-700 font-bold' : 'text-[#667085]' }}">
                                <i data-lucide="layout-dashboard" class="w-4 h-4 mr-3"></i> {{ __('Dashboard') }}
                            </a>
                            <a href="{{ route('technician.requests.index') }}" class="flex items-center px-3 py-2 rounded-xl {{ request()->routeIs('technician.requests.*') ? 'bg-[#F0FDFB] text-teal-700 font-bold' : 'text-[#667085]' }}">
                                <i data-lucide="inbox" class="w-4 h-4 mr-3"></i> {{ __('Job Requests') }}
                            </a>
                            <a href="{{ route('technician.subscription') }}" class="flex items-center px-3 py-2 rounded-xl {{ request()->routeIs('technician.subscription*') ? 'bg-[#F0FDFB] text-teal-700 font-bold' : 'text-[#667085]' }}">
                                <i data-lucide="credit-card" class="w-4 h-4 mr-3"></i> {{ __('Subscription') }}
                            </a>
                            <a href="{{ route('technician.portfolios.index') }}" class="flex items-center px-3 py-2 rounded-xl {{ request()->routeIs('technician.portfolios.*') ? 'bg-[#F0FDFB] text-teal-700 font-bold' : 'text-[#667085]' }}">
                                <i data-lucide="image" class="w-4 h-4 mr-3"></i> {{ __('Portfolio') }}
                            </a>
                        @endif
                        <a href="{{ route('messages.index') }}" class="flex items-center px-3 py-2 rounded-xl {{ request()->routeIs('messages.*') ? 'bg-[#F0FDFB] text-teal-700 font-bold' : 'text-[#667085]' }}">
                            <i data-lucide="message-square" class="w-4 h-4 mr-3"></i> {{ __('Messages') }}
                        </a>
                    </nav>
                </div>

                <form method="POST" action="{{ route('logout') }}" data-turbo="false" class="pt-4 border-t border-[#E5E7EB]">
                    @csrf
                    <button type="submit" class="w-full flex items-center px-3 py-2 text-xs font-bold text-[#F04438] hover:bg-rose-50 rounded-xl">
                        <i data-lucide="log-out" class="w-4 h-4 mr-3"></i> {{ __('Logout') }}
                    </button>
                </form>
            </div>
        </div>

        <!-- 3. MAIN WORKSPACE CONTAINER (Header + Content + Footer) -->
        <div class="flex-1 flex flex-col min-w-0">
            
            <!-- Top Workspace Header -->
            <header class="sticky top-0 z-20 bg-white/95 backdrop-blur-md border-b border-[#E5E7EB] transition-all">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between items-center h-16">
                        
                        <!-- Left: Hamburger (Mobile) & Workspace Breadcrumb -->
                        <div class="flex items-center space-x-3">
                            <button @click="mobileSidebarOpen = true" class="lg:hidden p-2 rounded-xl text-slate-600 hover:text-navy-900 hover:bg-slate-100 transition cursor-pointer">
                                <i data-lucide="menu" class="w-5 h-5"></i>
                            </button>
                            <div class="hidden sm:flex items-center space-x-2 text-xs text-[#667085]">
                                <span class="font-bold text-navy-900">FUNDI</span>
                                <span>/</span>
                                <span class="capitalize">{{ str_replace(['client.', 'technician.', 'admin.'], '', request()->route()->getName() ?? 'workspace') }}</span>
                            </div>
                        </div>

                        <!-- Right: Actions & User Dropdown -->
                        <div class="flex items-center space-x-2 sm:space-x-3">
                            <!-- Language Toggle -->
                            <a href="{{ route('language.switch', app()->getLocale() === 'en' ? 'sw' : 'en') }}" class="px-2.5 py-1.5 rounded-xl border border-[#E5E7EB] bg-white hover:bg-slate-50 text-xs font-bold text-navy-900 transition shadow-xs flex items-center space-x-1">
                                <i data-lucide="globe" class="w-3.5 h-3.5 text-teal-600"></i>
                                <span>{{ app()->getLocale() === 'en' ? 'EN' : 'SW' }}</span>
                            </a>

                            <!-- Notifications -->
                            <a href="{{ route('notifications.index') }}" class="relative p-2 text-[#667085] hover:text-navy-900 hover:bg-slate-100 rounded-xl transition">
                                <i data-lucide="bell" class="w-5 h-5"></i>
                                @php $unreadCount = auth()->user()->unreadNotificationsCount(); @endphp
                                @if($unreadCount > 0)
                                    <span class="absolute top-1.5 right-1.5 flex h-4 w-4 items-center justify-center rounded-full bg-[#F04438] text-[9px] font-bold text-white">
                                        {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                                    </span>
                                @endif
                            </a>

                            <!-- Messages -->
                            <a href="{{ route('messages.index') }}" class="p-2 text-[#667085] hover:text-navy-900 hover:bg-slate-100 rounded-xl transition">
                                <i data-lucide="message-square" class="w-5 h-5"></i>
                            </a>

                            <!-- User Profile Chip -->
                            <div class="flex items-center space-x-2 pl-2 border-l border-[#E5E7EB]">
                                <div class="w-8 h-8 rounded-xl bg-navy-900 text-teal-300 flex items-center justify-center font-bold text-xs uppercase shadow-xs">
                                    {{ auth()->user()->initials }}
                                </div>
                                <span class="hidden md:inline-block text-xs font-bold text-navy-900 max-w-[120px] truncate">
                                    {{ auth()->user()->first_name }}
                                </span>
                            </div>
                        </div>

                    </div>
                </div>
            </header>

            <!-- Flash Alerts Container -->
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

            <!-- Page Main Content -->
            <main class="flex-grow flex flex-col pb-20 lg:pb-8">
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

    @auth
    <!-- Mobile Bottom Navigation (Visible on Small Screens) -->
    <nav class="md:hidden fixed bottom-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-xl border-t border-[#E5E7EB]/80 shadow-[0_-4px_24px_rgba(11,18,32,0.06)] px-2 sm:px-4 py-1.5 safe-bottom-nav flex justify-around items-center">
        @if(auth()->user()->isClient())
            <a href="{{ route('client.dashboard') }}" class="flex flex-col items-center justify-center flex-1 min-w-0 py-1 text-[11px] font-bold transition btn-tap {{ request()->routeIs('client.dashboard') ? 'text-teal-600' : 'text-[#667085] hover:text-navy-900' }}">
                <div class="p-1 rounded-xl transition {{ request()->routeIs('client.dashboard') ? 'bg-teal-50 text-teal-600' : '' }}">
                    <i data-lucide="home" class="w-5 h-5"></i>
                </div>
                <span class="truncate">{{ __('Dashboard') }}</span>
            </a>
            <a href="{{ route('client.technicians.index') }}" class="flex flex-col items-center justify-center flex-1 min-w-0 py-1 text-[11px] font-bold transition btn-tap {{ request()->routeIs('client.technicians.*') ? 'text-teal-600' : 'text-[#667085] hover:text-navy-900' }}">
                <div class="p-1 rounded-xl transition {{ request()->routeIs('client.technicians.*') ? 'bg-teal-50 text-teal-600' : '' }}">
                    <i data-lucide="search" class="w-5 h-5"></i>
                </div>
                <span class="truncate">{{ __('Find Fundi') }}</span>
            </a>
            <a href="{{ route('client.requests.index') }}" class="flex flex-col items-center justify-center flex-1 min-w-0 py-1 text-[11px] font-bold transition btn-tap {{ request()->routeIs('client.requests.*') ? 'text-teal-600' : 'text-[#667085] hover:text-navy-900' }}">
                <div class="p-1 rounded-xl transition {{ request()->routeIs('client.requests.*') ? 'bg-teal-50 text-teal-600' : '' }}">
                    <i data-lucide="inbox" class="w-5 h-5"></i>
                </div>
                <span class="truncate">{{ __('Requests') }}</span>
            </a>
            <a href="{{ route('messages.index') }}" class="flex flex-col items-center justify-center flex-1 min-w-0 py-1 text-[11px] font-bold transition btn-tap relative {{ request()->routeIs('messages.*') ? 'text-teal-600' : 'text-[#667085] hover:text-navy-900' }}">
                <div class="p-1 rounded-xl transition relative {{ request()->routeIs('messages.*') ? 'bg-teal-50 text-teal-600' : '' }}">
                    <i data-lucide="message-square" class="w-5 h-5"></i>
                </div>
                <span class="truncate">{{ __('Messages') }}</span>
            </a>
            <a href="{{ route('client.favorites.index') }}" class="flex flex-col items-center justify-center flex-1 min-w-0 py-1 text-[11px] font-bold transition btn-tap {{ request()->routeIs('client.favorites.*') ? 'text-teal-600' : 'text-[#667085] hover:text-navy-900' }}">
                <div class="p-1 rounded-xl transition {{ request()->routeIs('client.favorites.*') ? 'bg-teal-50 text-teal-600' : '' }}">
                    <i data-lucide="bookmark" class="w-5 h-5"></i>
                </div>
                <span class="truncate">{{ __('Saved') }}</span>
            </a>
        @elseif(auth()->user()->isTechnician())
            <a href="{{ route('technician.dashboard') }}" class="flex flex-col items-center justify-center flex-1 min-w-0 py-1 text-[11px] font-bold transition btn-tap {{ request()->routeIs('technician.dashboard') ? 'text-teal-600' : 'text-[#667085] hover:text-navy-900' }}">
                <div class="p-1 rounded-xl transition {{ request()->routeIs('technician.dashboard') ? 'bg-teal-50 text-teal-600' : '' }}">
                    <i data-lucide="home" class="w-5 h-5"></i>
                </div>
                <span class="truncate">{{ __('Dashboard') }}</span>
            </a>
            <a href="{{ route('technician.requests.index') }}" class="flex flex-col items-center justify-center flex-1 min-w-0 py-1 text-[11px] font-bold transition btn-tap {{ request()->routeIs('technician.requests.*') ? 'text-teal-600' : 'text-[#667085] hover:text-navy-900' }}">
                <div class="p-1 rounded-xl transition {{ request()->routeIs('technician.requests.*') ? 'bg-teal-50 text-teal-600' : '' }}">
                    <i data-lucide="briefcase" class="w-5 h-5"></i>
                </div>
                <span class="truncate">{{ __('Jobs') }}</span>
            </a>
            <a href="{{ route('technician.subscription') }}" class="flex flex-col items-center justify-center flex-1 min-w-0 py-1 text-[11px] font-bold transition btn-tap {{ request()->routeIs('technician.subscription*') ? 'text-teal-600' : 'text-[#667085] hover:text-navy-900' }}">
                <div class="p-1 rounded-xl transition {{ request()->routeIs('technician.subscription*') ? 'bg-teal-50 text-teal-600' : '' }}">
                    <i data-lucide="credit-card" class="w-5 h-5"></i>
                </div>
                <span class="truncate">{{ __('Plan') }}</span>
            </a>
            <a href="{{ route('messages.index') }}" class="flex flex-col items-center justify-center flex-1 min-w-0 py-1 text-[11px] font-bold transition btn-tap {{ request()->routeIs('messages.*') ? 'text-teal-600' : 'text-[#667085] hover:text-navy-900' }}">
                <div class="p-1 rounded-xl transition {{ request()->routeIs('messages.*') ? 'bg-teal-50 text-teal-600' : '' }}">
                    <i data-lucide="message-square" class="w-5 h-5"></i>
                </div>
                <span class="truncate">{{ __('Chat') }}</span>
            </a>
            <a href="{{ route('technician.portfolios.index') }}" class="flex flex-col items-center justify-center flex-1 min-w-0 py-1 text-[11px] font-bold transition btn-tap {{ request()->routeIs('technician.portfolios.*') ? 'text-teal-600' : 'text-[#667085] hover:text-navy-900' }}">
                <div class="p-1 rounded-xl transition {{ request()->routeIs('technician.portfolios.*') ? 'bg-teal-50 text-teal-600' : '' }}">
                    <i data-lucide="image" class="w-5 h-5"></i>
                </div>
                <span class="truncate">{{ __('Works') }}</span>
            </a>
        @elseif(auth()->user()->isAdmin())
            <a href="{{ route('admin.dashboard') }}" class="flex flex-col items-center justify-center flex-1 min-w-0 py-1 text-[11px] font-bold transition btn-tap {{ request()->routeIs('admin.dashboard') ? 'text-teal-600' : 'text-[#667085] hover:text-navy-900' }}">
                <div class="p-1 rounded-xl transition {{ request()->routeIs('admin.dashboard') ? 'bg-teal-50 text-teal-600' : '' }}">
                    <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                </div>
                <span class="truncate">{{ __('Admin') }}</span>
            </a>
            <a href="{{ route('admin.users') }}" class="flex flex-col items-center justify-center flex-1 min-w-0 py-1 text-[11px] font-bold transition btn-tap {{ request()->routeIs('admin.users*') ? 'text-teal-600' : 'text-[#667085] hover:text-navy-900' }}">
                <div class="p-1 rounded-xl transition {{ request()->routeIs('admin.users*') ? 'bg-teal-50 text-teal-600' : '' }}">
                    <i data-lucide="users" class="w-5 h-5"></i>
                </div>
                <span class="truncate">{{ __('Users') }}</span>
            </a>
            <a href="{{ route('admin.applications') }}" class="flex flex-col items-center justify-center flex-1 min-w-0 py-1 text-[11px] font-bold transition btn-tap {{ request()->routeIs('admin.applications*') ? 'text-teal-600' : 'text-[#667085] hover:text-navy-900' }}">
                <div class="p-1 rounded-xl transition {{ request()->routeIs('admin.applications*') ? 'bg-teal-50 text-teal-600' : '' }}">
                    <i data-lucide="check-square" class="w-5 h-5"></i>
                </div>
                <span class="truncate">{{ __('Verify') }}</span>
            </a>
            <a href="{{ route('admin.complaints') }}" class="flex flex-col items-center justify-center flex-1 min-w-0 py-1 text-[11px] font-bold transition btn-tap {{ request()->routeIs('admin.complaints*') ? 'text-teal-600' : 'text-[#667085] hover:text-navy-900' }}">
                <div class="p-1 rounded-xl transition {{ request()->routeIs('admin.complaints*') ? 'bg-teal-50 text-teal-600' : '' }}">
                    <i data-lucide="alert-circle" class="w-5 h-5"></i>
                </div>
                <span class="truncate">{{ __('Issues') }}</span>
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
