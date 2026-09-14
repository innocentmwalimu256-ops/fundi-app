@extends('layouts.app')

@section('title', __('Download FUNDI Mobile App (APK)') . ' — FUNDI')

@section('content')
<div class="min-h-screen bg-slate-50 py-8 sm:py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-5xl mx-auto space-y-10">

        <!-- Top Navigation Bar / Breadcrumb -->
        <div class="flex items-center justify-between">
            <a href="{{ url('/') }}" class="inline-flex items-center space-x-2 text-xs font-bold text-slate-600 hover:text-teal-700 bg-white px-3.5 py-2 rounded-xl border border-slate-200 shadow-xs transition">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                <span>{{ __('Back to Sign In') }}</span>
            </a>

            <!-- Language Switcher -->
            <a href="{{ route('language.switch', app()->getLocale() === 'en' ? 'sw' : 'en') }}" class="px-3 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-xs font-bold text-slate-800 transition shadow-xs flex items-center space-x-1.5">
                <i data-lucide="globe" class="w-3.5 h-3.5 text-teal-600"></i>
                <span>{{ app()->getLocale() === 'en' ? 'Kiswahili' : 'English' }}</span>
            </a>
        </div>

        <!-- 1. HERO SECTION -->
        <div class="bg-white rounded-3xl border border-slate-200/90 shadow-xl overflow-hidden">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center p-6 sm:p-10 lg:p-12">
                
                <!-- Left Details & Download Button -->
                <div class="lg:col-span-7 space-y-6">
                    <div class="inline-flex items-center space-x-2 px-3 py-1.5 rounded-full bg-teal-50 border border-teal-200 text-teal-800 text-xs font-bold">
                        <span class="flex h-2 w-2 rounded-full bg-teal-500 animate-pulse"></span>
                        <span>{{ __('Official Android APK Release') }} ({{ $apkVersion }})</span>
                    </div>

                    <div class="space-y-3">
                        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black text-slate-900 tracking-tight leading-tight">
                            {{ __('Get the FUNDI Mobile App') }}<br/>
                            <span class="text-teal-600">{{ __('Fast. Lightweight. Direct.') }}</span>
                        </h1>
                        <p class="text-sm sm:text-base text-slate-600 leading-relaxed">
                            {{ __('Download the official FUNDI Android app (APK) directly to your smartphone. Connect with verified fundis, get instant job alerts, and chat or call in one tap.') }}
                        </p>
                    </div>

                    <!-- Direct APK Download CTA -->
                    <div class="space-y-3 pt-2">
                        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                            <a href="{{ route('app.download.apk') }}" class="flex-1 px-6 py-4 rounded-2xl bg-gradient-to-r from-teal-600 to-teal-700 hover:from-teal-700 hover:to-teal-800 text-white font-black text-xs uppercase tracking-wider shadow-lg shadow-teal-700/25 active:scale-[0.98] transition flex items-center justify-center space-x-3 text-center">
                                <i data-lucide="download" class="w-5 h-5"></i>
                                <span>{{ __('Download Android APK') }}</span>
                                <span class="px-2 py-0.5 rounded-md bg-teal-800/60 text-[11px] font-mono lowercase tracking-normal">{{ $apkSize }}</span>
                            </a>

                            <a href="{{ route('login') }}" class="px-5 py-4 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs uppercase tracking-wider transition flex items-center justify-center space-x-2">
                                <i data-lucide="globe" class="w-4 h-4 text-slate-500"></i>
                                <span>{{ __('Use Web Version') }}</span>
                            </a>
                        </div>

                        <div class="flex flex-wrap items-center gap-4 text-xs text-slate-500 pt-1">
                            <span class="flex items-center space-x-1.5">
                                <i data-lucide="shield-check" class="w-4 h-4 text-emerald-600"></i>
                                <span>{{ __('100% Safe & Virus Free') }}</span>
                            </span>
                            <span class="flex items-center space-x-1.5">
                                <i data-lucide="smartphone" class="w-4 h-4 text-teal-600"></i>
                                <span>{{ __('Android 7.0 & Newer') }}</span>
                            </span>
                            <span class="flex items-center space-x-1.5">
                                <i data-lucide="zap" class="w-4 h-4 text-amber-500"></i>
                                <span>{{ __('No Google Play Needed') }}</span>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Right Visual: Mobile Mockup & Scan QR -->
                <div class="lg:col-span-5 flex flex-col items-center justify-center space-y-4">
                    <div class="relative w-full max-w-xs bg-gradient-to-b from-slate-900 to-slate-800 rounded-3xl p-6 text-white text-center shadow-2xl border border-slate-700 space-y-4">
                        <div class="w-14 h-14 rounded-2xl bg-teal-500/20 border border-teal-500/30 text-teal-400 flex items-center justify-center mx-auto shadow-inner">
                            <i data-lucide="smartphone" class="w-7 h-7"></i>
                        </div>
                        
                        <div>
                            <h3 class="text-base font-black tracking-tight text-white">FUNDI Mobile</h3>
                            <p class="text-[11px] text-slate-400 mt-0.5">{{ __('Scan with camera to download on phone') }}</p>
                        </div>

                        <!-- QR Code -->
                        <div class="p-3 bg-white rounded-2xl shadow-md inline-block">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=160x160&data={{ urlencode(route('app.download')) }}&margin=4" 
                                 alt="QR Code to Download FUNDI App" 
                                 class="w-36 h-36 mx-auto rounded-lg">
                        </div>

                        <p class="text-[10px] text-teal-300 font-mono">
                            {{ route('app.download') }}
                        </p>
                    </div>
                </div>

            </div>
        </div>

        <!-- 2. EASY 3-STEP INSTALLATION GUIDE -->
        <div class="bg-white rounded-3xl border border-slate-200/90 p-6 sm:p-10 shadow-sm space-y-6">
            <div class="text-center max-w-xl mx-auto space-y-2">
                <div class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-bold">
                    <i data-lucide="help-circle" class="w-3.5 h-3.5 text-teal-600"></i>
                    <span>{{ __('Installation Guide') }}</span>
                </div>
                <h2 class="text-xl sm:text-2xl font-black text-slate-900">{{ __('How to Install the APK on Android') }}</h2>
                <p class="text-xs sm:text-sm text-slate-500">{{ __('Follow these 3 simple steps to get the app running on your phone in under a minute.') }}</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-2">
                <!-- Step 1 -->
                <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-3 relative">
                    <div class="w-9 h-9 rounded-xl bg-teal-600 text-white font-black text-sm flex items-center justify-center font-mono shadow-xs">
                        1
                    </div>
                    <h3 class="text-sm font-bold text-slate-900">{{ __('Download the APK') }}</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        {{ __('Tap the "Download Android APK" button above to save the file (FUNDI-App-v1.0.apk) to your device downloads folder.') }}
                    </p>
                </div>

                <!-- Step 2 -->
                <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-3 relative">
                    <div class="w-9 h-9 rounded-xl bg-teal-600 text-white font-black text-sm flex items-center justify-center font-mono shadow-xs">
                        2
                    </div>
                    <h3 class="text-sm font-bold text-slate-900">{{ __('Allow Install & Open') }}</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        {{ __('Tap on the downloaded file. If your browser asks, tap "Settings" and turn on "Allow from this source" to proceed.') }}
                    </p>
                </div>

                <!-- Step 3 -->
                <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-3 relative">
                    <div class="w-9 h-9 rounded-xl bg-teal-600 text-white font-black text-sm flex items-center justify-center font-mono shadow-xs">
                        3
                    </div>
                    <h3 class="text-sm font-bold text-slate-900">{{ __('Launch & Sign In') }}</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        {{ __('Tap "Install" then "Open". Sign in with your registered phone number or email to start using FUNDI instantly.') }}
                    </p>
                </div>
            </div>
        </div>

        <!-- 3. PROGRESSIVE WEB APP (PWA) / IPHONE (iOS) GUIDE -->
        <div class="bg-gradient-to-br from-slate-900 to-slate-800 rounded-3xl p-6 sm:p-10 text-white shadow-xl space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-700/80 pb-6">
                <div>
                    <div class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-full bg-teal-500/20 text-teal-300 text-xs font-bold mb-2">
                        <i data-lucide="apple" class="w-3.5 h-3.5"></i>
                        <span>{{ __('For iPhone / iOS & Any Web Browser') }}</span>
                    </div>
                    <h2 class="text-xl sm:text-2xl font-black text-white">{{ __('Install as Web App (No Download Required)') }}</h2>
                    <p class="text-xs sm:text-sm text-slate-400 mt-1">{{ __('You can add FUNDI directly to your iPhone or Android home screen for an instant app experience.') }}</p>
                </div>

                <a href="{{ route('login') }}" class="px-5 py-3 rounded-xl bg-teal-500 hover:bg-teal-400 text-slate-950 font-bold text-xs uppercase tracking-wider transition self-start sm:self-auto flex items-center space-x-2">
                    <i data-lucide="external-link" class="w-4 h-4"></i>
                    <span>{{ __('Open Web App Now') }}</span>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="p-5 rounded-2xl bg-slate-800/80 border border-slate-700 space-y-2">
                    <div class="flex items-center space-x-2 text-teal-400 text-xs font-bold">
                        <i data-lucide="smartphone" class="w-4 h-4"></i>
                        <span>{{ __('iPhone / iPad (Safari Browser)') }}</span>
                    </div>
                    <p class="text-xs text-slate-300 leading-relaxed">
                        {{ __('1. Open Safari and visit the FUNDI website. 2. Tap the "Share" button at the bottom of the screen. 3. Scroll down and tap "Add to Home Screen".') }}
                    </p>
                </div>

                <div class="p-5 rounded-2xl bg-slate-800/80 border border-slate-700 space-y-2">
                    <div class="flex items-center space-x-2 text-teal-400 text-xs font-bold">
                        <i data-lucide="chrome" class="w-4 h-4"></i>
                        <span>{{ __('Android / PC (Chrome Browser)') }}</span>
                    </div>
                    <p class="text-xs text-slate-300 leading-relaxed">
                        {{ __('1. Open Chrome browser. 2. Tap the 3 dots menu at the top right. 3. Tap "Install App" or "Add to Home screen".') }}
                    </p>
                </div>
            </div>
        </div>

        <!-- 4. APP HIGHLIGHTS & FEATURES -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 sm:gap-6">
            <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-xs space-y-2 text-center">
                <div class="w-10 h-10 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center mx-auto">
                    <i data-lucide="bell" class="w-5 h-5"></i>
                </div>
                <h4 class="text-xs font-bold text-slate-900">{{ __('Live Notifications') }}</h4>
                <p class="text-[11px] text-slate-500">{{ __('Instant push alerts for new jobs and quotations.') }}</p>
            </div>

            <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-xs space-y-2 text-center">
                <div class="w-10 h-10 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center mx-auto">
                    <i data-lucide="phone-call" class="w-5 h-5"></i>
                </div>
                <h4 class="text-xs font-bold text-slate-900">{{ __('1-Tap Call & Chat') }}</h4>
                <p class="text-[11px] text-slate-500">{{ __('Direct WhatsApp and phone call links.') }}</p>
            </div>

            <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-xs space-y-2 text-center">
                <div class="w-10 h-10 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center mx-auto">
                    <i data-lucide="zap" class="w-5 h-5"></i>
                </div>
                <h4 class="text-xs font-bold text-slate-900">{{ __('Ultra Fast & Light') }}</h4>
                <p class="text-[11px] text-slate-500">{{ __('Consumes minimal battery and mobile data.') }}</p>
            </div>

            <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-xs space-y-2 text-center">
                <div class="w-10 h-10 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center mx-auto">
                    <i data-lucide="shield-check" class="w-5 h-5"></i>
                </div>
                <h4 class="text-xs font-bold text-slate-900">{{ __('Verified Security') }}</h4>
                <p class="text-[11px] text-slate-500">{{ __('End-to-end protected payments and profiles.') }}</p>
            </div>
        </div>

        <!-- Clean Footer -->
        <div class="text-center text-xs text-slate-400 pt-6">
            <p>&copy; {{ date('Y') }} FUNDI Platform. {{ __('All rights reserved.') }}</p>
        </div>

    </div>
</div>
@endsection