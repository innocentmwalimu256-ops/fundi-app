@extends('layouts.app')

@section('title', 'Find. Connect. Fix.')

@section('content')
<div class="space-y-16 sm:space-y-24">

    <!-- 1. HERO SECTION (Section 12) -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6 sm:pt-12">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
            
            <!-- Left Side: Value Prop & Search Interface -->
            <div class="lg:col-span-7 space-y-6 sm:space-y-8">
                <div class="inline-flex items-center space-x-2 px-3 py-1.5 rounded-full bg-[#F0FDFB] border border-teal-200 text-teal-800 text-xs font-bold">
                    <span class="flex h-2 w-2 rounded-full bg-teal-500 animate-pulse"></span>
                    <span>Tanzania's #1 Verified Artisan Platform</span>
                </div>

                <div class="space-y-3">
                    <h1 class="text-3xl sm:text-5xl lg:text-[54px] font-black text-navy-900 tracking-tight leading-[1.15]">
                        Find the right fundi.<br/>
                        <span class="text-teal-600">Get the job done.</span>
                    </h1>
                    <p class="text-base sm:text-lg text-[#667085] leading-relaxed max-w-xl">
                        Find verified technicians for electrical, plumbing, AC repair, carpentry, painting and more in Dar es Salaam and across Tanzania.
                    </p>
                </div>

                <!-- Unified Search Interface (Section 49) -->
                <form action="{{ route('client.technicians.index') }}" method="GET" class="bg-white p-3 sm:p-4 rounded-2xl sm:rounded-3xl border border-[#E5E7EB] shadow-card space-y-3 sm:space-y-0 sm:flex sm:items-center sm:space-x-3">
                    <!-- Service Input -->
                    <div class="flex-1 flex items-center space-x-3 px-3 py-2 bg-[#F7F8F7] rounded-xl border border-transparent focus-within:border-teal-500 focus-within:bg-white transition">
                        <i data-lucide="search" class="w-5 h-5 text-[#98A2B3] flex-shrink-0"></i>
                        <input type="text" name="search" placeholder="What service do you need? (e.g. Electrician, AC)" class="w-full bg-transparent text-sm text-navy-900 placeholder-[#98A2B3] focus:outline-none font-medium">
                    </div>

                    <!-- Location Input -->
                    <div class="flex-1 flex items-center space-x-3 px-3 py-2 bg-[#F7F8F7] rounded-xl border border-transparent focus-within:border-teal-500 focus-within:bg-white transition">
                        <i data-lucide="map-pin" class="w-5 h-5 text-[#98A2B3] flex-shrink-0"></i>
                        <input type="text" name="location" placeholder="Enter your location (e.g. Mikocheni)" class="w-full bg-transparent text-sm text-navy-900 placeholder-[#98A2B3] focus:outline-none font-medium">
                    </div>

                    <!-- Submit CTA Button -->
                    <button type="submit" class="w-full sm:w-auto px-6 py-3.5 rounded-xl bg-teal-500 hover:bg-teal-600 text-white text-sm font-bold shadow-xs hover:shadow-subtle active:scale-[0.98] transition flex items-center justify-center space-x-2 flex-shrink-0">
                        <span>Find a Fundi</span>
                        <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </button>
                </form>

                <!-- Micro Social Proof -->
                <div class="flex items-center space-x-6 text-xs text-[#667085] pt-1">
                    <div class="flex items-center space-x-2">
                        <i data-lucide="shield-check" class="w-4 h-4 text-[#12B76A]"></i>
                        <span>100% NIDA & Cert Verified</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <i data-lucide="phone-call" class="w-4 h-4 text-teal-600"></i>
                        <span>Direct Call & WhatsApp</span>
                    </div>
                </div>
            </div>

            <!-- Right Side: Professional Artisan Visual Container -->
            <div class="lg:col-span-5 relative">
                <div class="relative rounded-3xl overflow-hidden border border-[#E5E7EB] shadow-card bg-white">
                    <img src="https://images.unsplash.com/photo-1621905251189-08b45d6a269e?auto=format&fit=crop&w=800&q=80" alt="Professional Technician at Work" class="w-full h-80 sm:h-[440px] object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t from-navy-950/80 via-transparent to-transparent flex flex-col justify-end p-6 text-white">
                        <div class="inline-flex items-center space-x-1.5 px-2.5 py-1 rounded-md bg-[#12B76A] text-white text-[11px] font-bold w-max shadow-xs mb-2">
                            <i data-lucide="check" class="w-3.5 h-3.5"></i>
                            <span>VERIFIED ARTISAN</span>
                        </div>
                        <h3 class="text-lg font-bold text-white">Professional & Equipped</h3>
                        <p class="text-xs text-slate-200 mt-0.5">Reliable local experts ready to fix your issues in minutes.</p>
                    </div>
                </div>

                <!-- Floating Badge -->
                <div class="hidden sm:flex absolute -bottom-5 -left-5 bg-white p-4 rounded-2xl border border-[#E5E7EB] shadow-elevated items-center space-x-3.5">
                    <div class="w-10 h-10 rounded-xl bg-[#F0FDFB] text-teal-600 flex items-center justify-center font-bold">
                        <i data-lucide="star" class="w-5 h-5 fill-amber-400 text-amber-400"></i>
                    </div>
                    <div>
                        <div class="flex items-center space-x-1">
                            <span class="text-sm font-black text-navy-900">4.9 / 5.0</span>
                            <span class="text-xs text-[#667085]">(500+ Reviews)</span>
                        </div>
                        <p class="text-[11px] text-[#667085] font-medium">Customer satisfaction rate</p>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- 2. TRUST INDICATORS (Section 13) -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6">
            <div class="p-5 rounded-2xl bg-white border border-[#E5E7EB] shadow-subtle space-y-2">
                <div class="w-10 h-10 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center">
                    <i data-lucide="badge-check" class="w-5 h-5"></i>
                </div>
                <h4 class="text-sm font-bold text-navy-900">Verified Technicians</h4>
                <p class="text-xs text-[#667085] leading-relaxed">Every artisan is reviewed with verified NIDA and qualifications.</p>
            </div>

            <div class="p-5 rounded-2xl bg-white border border-[#E5E7EB] shadow-subtle space-y-2">
                <div class="w-10 h-10 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center">
                    <i data-lucide="zap" class="w-5 h-5"></i>
                </div>
                <h4 class="text-sm font-bold text-navy-900">Fast Connections</h4>
                <p class="text-xs text-[#667085] leading-relaxed">Connect with nearby available artisans in minutes.</p>
            </div>

            <div class="p-5 rounded-2xl bg-white border border-[#E5E7EB] shadow-subtle space-y-2">
                <div class="w-10 h-10 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center">
                    <i data-lucide="file-text" class="w-5 h-5"></i>
                </div>
                <h4 class="text-sm font-bold text-navy-900">Transparent Process</h4>
                <p class="text-xs text-[#667085] leading-relaxed">Request, quote, schedule, and complete with clear tracking.</p>
            </div>

            <div class="p-5 rounded-2xl bg-white border border-[#E5E7EB] shadow-subtle space-y-2">
                <div class="w-10 h-10 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center">
                    <i data-lucide="message-circle" class="w-5 h-5"></i>
                </div>
                <h4 class="text-sm font-bold text-navy-900">Direct Communication</h4>
                <p class="text-xs text-[#667085] leading-relaxed">Unlock instant Phone & WhatsApp access directly with your fundi.</p>
            </div>
        </div>
    </section>

    <!-- 3. SERVICE CATEGORIES (Section 14) -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-8 space-y-2 sm:space-y-0">
            <div>
                <h2 class="text-2xl sm:text-3xl font-black text-navy-900 tracking-tight">What do you need help with?</h2>
                <p class="text-sm text-[#667085] mt-1">Explore verified specialized trade categories</p>
            </div>
            <a href="{{ route('client.services.index') }}" class="inline-flex items-center text-xs font-bold text-teal-600 hover:text-teal-700 transition">
                <span>View all services</span>
                <i data-lucide="arrow-right" class="w-4 h-4 ml-1"></i>
            </a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 sm:gap-6">
            @php
                $categories = [
                    ['name' => 'Electrical', 'icon' => 'zap', 'desc' => 'Wiring, fixtures & fault repair'],
                    ['name' => 'Plumbing', 'icon' => 'droplets', 'desc' => 'Pipes, leaks & pump install'],
                    ['name' => 'AC Repair', 'icon' => 'wind', 'desc' => 'Service, gas refill & cooling'],
                    ['name' => 'Carpentry', 'icon' => 'hammer', 'desc' => 'Doors, furniture & roofing'],
                    ['name' => 'Painting', 'icon' => 'paint-bucket', 'desc' => 'Interior & exterior finish'],
                    ['name' => 'Construction', 'icon' => 'hard-hat', 'desc' => 'Masonry, tiles & repairs'],
                    ['name' => 'Mechanics', 'icon' => 'wrench', 'desc' => 'Auto maintenance & diagnostics'],
                    ['name' => 'Appliance Repair', 'icon' => 'tv', 'desc' => 'Fridges, washing machines & TV'],
                ];
            @endphp

            @foreach($categories as $cat)
                <a href="{{ route('client.technicians.index', ['search' => $cat['name']]) }}" class="p-5 rounded-2xl bg-white border border-[#E5E7EB] hover:border-teal-500 hover:shadow-card transition group flex flex-col justify-between">
                    <div class="w-12 h-12 rounded-xl bg-[#F7F8F7] group-hover:bg-[#F0FDFB] text-navy-900 group-hover:text-teal-600 flex items-center justify-center transition mb-3">
                        <i data-lucide="{{ $cat['icon'] }}" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-navy-900 group-hover:text-teal-700 transition">{{ $cat['name'] }}</h4>
                        <p class="text-xs text-[#667085] mt-0.5 line-clamp-1">{{ $cat['desc'] }}</p>
                    </div>
                </a>
            @endforeach
        </div>
    </section>

    <!-- 4. FEATURED TECHNICIANS (Section 15) -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-8 space-y-2 sm:space-y-0">
            <div>
                <h2 class="text-2xl sm:text-3xl font-black text-navy-900 tracking-tight">Trusted fundis near you</h2>
                <p class="text-sm text-[#667085] mt-1">Verified artisans with active subscriptions ready for hire</p>
            </div>
            <a href="{{ route('client.technicians.index') }}" class="inline-flex items-center text-xs font-bold text-teal-600 hover:text-teal-700 transition">
                <span>Browse all technicians</span>
                <i data-lucide="arrow-right" class="w-4 h-4 ml-1"></i>
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($featuredTechnicians ?? [] as $tech)
                <div class="bg-white rounded-2xl border border-[#E5E7EB] p-5 shadow-subtle hover:shadow-card transition flex flex-col justify-between space-y-4">
                    <div class="flex items-start space-x-3.5">
                        <div class="w-14 h-14 rounded-xl bg-navy-900 text-teal-300 flex items-center justify-center font-bold text-base flex-shrink-0">
                            {{ $tech->initials }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center space-x-1.5">
                                <h4 class="text-sm font-bold text-navy-900 truncate">{{ $tech->full_name }}</h4>
                                <span class="px-1.5 py-0.5 rounded bg-[#F0FDFB] text-[#12B76A] text-[10px] font-bold flex items-center" title="Verified">
                                    <i data-lucide="check" class="w-3 h-3 mr-0.5"></i> VERIFIED
                                </span>
                            </div>
                            <p class="text-xs text-teal-700 font-semibold">{{ $tech->technicianProfile->specialty ?? 'General Artisan' }}</p>
                            <p class="text-xs text-[#667085] mt-0.5 flex items-center">
                                <i data-lucide="map-pin" class="w-3 h-3 mr-1 text-[#98A2B3]"></i>
                                {{ $tech->technicianProfile->location ?? 'Dar es Salaam' }}
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-3 border-t border-[#E5E7EB] text-xs">
                        <div class="flex items-center space-x-1 font-bold text-navy-900">
                            <i data-lucide="star" class="w-4 h-4 fill-amber-400 text-amber-400"></i>
                            <span>{{ number_format($tech->technicianProfile->rating ?? 4.9, 1) }}</span>
                            <span class="text-[#667085] font-normal">({{ $tech->technicianProfile->completed_jobs_count ?? 12 }} jobs)</span>
                        </div>
                        <a href="{{ route('client.technicians.show', $tech->id) }}" class="px-3.5 py-1.5 rounded-xl bg-[#F0FDFB] hover:bg-teal-500 hover:text-white text-teal-700 font-bold transition text-xs">
                            View Profile →
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-10 text-center bg-white rounded-2xl border border-[#E5E7EB] p-8">
                    <i data-lucide="users" class="w-10 h-10 text-[#98A2B3] mx-auto mb-2"></i>
                    <h4 class="text-sm font-bold text-navy-900">No active artisans listed right now</h4>
                    <p class="text-xs text-[#667085] mt-1">Check back shortly or view our services directory.</p>
                </div>
            @endforelse
        </div>
    </section>

    <!-- 5. HOW FUNDI WORKS (Section 16) -->
    <section id="how-it-works" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-12">
            <h2 class="text-2xl sm:text-3xl font-black text-navy-900 tracking-tight">How FUNDI Works</h2>
            <p class="text-sm text-[#667085] mt-1">Get your issues fixed in four simple, transparent steps</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="p-6 rounded-2xl bg-white border border-[#E5E7EB] shadow-subtle space-y-3 relative">
                <span class="text-2xl font-black text-teal-600 font-mono">01</span>
                <h4 class="text-base font-bold text-navy-900">Tell us what you need</h4>
                <p class="text-xs text-[#667085] leading-relaxed">Choose your service category, describe the issue, and upload photos.</p>
            </div>

            <div class="p-6 rounded-2xl bg-white border border-[#E5E7EB] shadow-subtle space-y-3 relative">
                <span class="text-2xl font-black text-teal-600 font-mono">02</span>
                <h4 class="text-base font-bold text-navy-900">Connect with a fundi</h4>
                <p class="text-xs text-[#667085] leading-relaxed">Pay a small TZS 2,000 connection fee to instantly unlock verified phone & WhatsApp access.</p>
            </div>

            <div class="p-6 rounded-2xl bg-white border border-[#E5E7EB] shadow-subtle space-y-3 relative">
                <span class="text-2xl font-black text-teal-600 font-mono">03</span>
                <h4 class="text-base font-bold text-navy-900">Receive quotation</h4>
                <p class="text-xs text-[#667085] leading-relaxed">Discuss the scope with your fundi and agree on labour and material prices.</p>
            </div>

            <div class="p-6 rounded-2xl bg-white border border-[#E5E7EB] shadow-subtle space-y-3 relative">
                <span class="text-2xl font-black text-teal-600 font-mono">04</span>
                <h4 class="text-base font-bold text-navy-900">Get the work done</h4>
                <p class="text-xs text-[#667085] leading-relaxed">Your fundi completes the job. Pay them directly with 0% platform commission deductions.</p>
            </div>
        </div>
    </section>

    <!-- 6. CONNECTION FEE CARD (Section 17) -->
    <section id="connection-fee" class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="rounded-3xl bg-navy-900 text-white p-8 sm:p-10 border border-navy-800 shadow-elevated relative overflow-hidden">
            <div class="max-w-xl space-y-4">
                <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-teal-500/20 text-teal-300 text-xs font-bold">
                    <i data-lucide="unlock" class="w-3.5 h-3.5"></i>
                    <span>Transparent Pricing Policy</span>
                </div>
                <h3 class="text-2xl sm:text-3xl font-black tracking-tight text-white">Connect directly with your fundi</h3>
                <p class="text-xs sm:text-sm text-[#98A2B3] leading-relaxed">
                    A small <strong class="text-white font-bold">TZS 2,000 connection fee</strong> unlocks direct, unrestricted communication with your chosen verified technician.
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2">
                    <div class="flex items-center space-x-2 text-xs font-semibold text-teal-300">
                        <i data-lucide="check-circle" class="w-4 h-4 text-[#12B76A]"></i>
                        <span>Phone number unlocked</span>
                    </div>
                    <div class="flex items-center space-x-2 text-xs font-semibold text-teal-300">
                        <i data-lucide="check-circle" class="w-4 h-4 text-[#12B76A]"></i>
                        <span>WhatsApp unlocked</span>
                    </div>
                    <div class="flex items-center space-x-2 text-xs font-semibold text-teal-300">
                        <i data-lucide="check-circle" class="w-4 h-4 text-[#12B76A]"></i>
                        <span>In-app messaging</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 7. TECHNICIAN SUBSCRIPTION PLANS (Section 18) -->
    <section id="pricing" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-12">
            <div class="inline-flex items-center space-x-2 px-3 py-1.5 rounded-full bg-[#F0FDFB] border border-teal-200 text-teal-800 text-xs font-bold mb-3">
                <span>For Artisans & Technicians</span>
            </div>
            <h2 class="text-2xl sm:text-3xl font-black text-navy-900 tracking-tight">Grow your business with FUNDI</h2>
            <p class="text-sm text-[#667085] mt-1">Choose a transparent monthly plan to receive client requests and showcase your portfolio</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8 max-w-5xl mx-auto items-stretch">
            
            <!-- Starter Plan -->
            <div class="p-6 sm:p-8 rounded-2xl bg-white border border-[#E5E7EB] shadow-subtle flex flex-col justify-between space-y-6">
                <div class="space-y-4">
                    <div>
                        <h4 class="text-base font-bold text-navy-900">Starter Plan</h4>
                        <p class="text-xs text-[#667085] mt-1">Ideal for individual artisans starting out</p>
                    </div>
                    <div>
                        <span class="text-3xl font-black text-navy-900 font-mono">TZS 10,000</span>
                        <span class="text-xs text-[#667085]">/ month</span>
                    </div>
                    <ul class="space-y-2.5 text-xs text-[#667085] pt-4 border-t border-[#E5E7EB]">
                        <li class="flex items-center space-x-2"><i data-lucide="check" class="w-4 h-4 text-[#12B76A]"></i><span>Listed in search directory</span></li>
                        <li class="flex items-center space-x-2"><i data-lucide="check" class="w-4 h-4 text-[#12B76A]"></i><span>Receive client requests</span></li>
                        <li class="flex items-center space-x-2"><i data-lucide="check" class="w-4 h-4 text-[#12B76A]"></i><span>3 Portfolio photos</span></li>
                        <li class="flex items-center space-x-2"><i data-lucide="check" class="w-4 h-4 text-[#12B76A]"></i><span>0% Commission on labour</span></li>
                    </ul>
                </div>
                <a href="{{ route('register') }}" class="w-full py-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-navy-900 font-bold text-xs text-center transition">
                    Get Started
                </a>
            </div>

            <!-- Professional Plan (Most Popular) -->
            <div class="p-6 sm:p-8 rounded-2xl bg-white border-2 border-teal-500 shadow-elevated flex flex-col justify-between space-y-6 relative">
                <div class="absolute -top-3.5 left-1/2 -translate-x-1/2 px-3 py-1 rounded-full bg-teal-500 text-white text-[10px] font-extrabold uppercase tracking-wider shadow-xs">
                    Most Popular
                </div>
                <div class="space-y-4">
                    <div>
                        <h4 class="text-base font-bold text-navy-900">Professional Plan</h4>
                        <p class="text-xs text-[#667085] mt-1">For active fundis wanting steady jobs</p>
                    </div>
                    <div>
                        <span class="text-3xl font-black text-teal-600 font-mono">TZS 20,000</span>
                        <span class="text-xs text-[#667085]">/ month</span>
                    </div>
                    <ul class="space-y-2.5 text-xs text-[#111827] pt-4 border-t border-[#E5E7EB]">
                        <li class="flex items-center space-x-2 font-medium"><i data-lucide="check" class="w-4 h-4 text-[#12B76A]"></i><span>Priority marketplace placement</span></li>
                        <li class="flex items-center space-x-2 font-medium"><i data-lucide="check" class="w-4 h-4 text-[#12B76A]"></i><span>Instant SMS/WhatsApp alerts</span></li>
                        <li class="flex items-center space-x-2 font-medium"><i data-lucide="check" class="w-4 h-4 text-[#12B76A]"></i><span>Unlimited portfolio gallery</span></li>
                        <li class="flex items-center space-x-2 font-medium"><i data-lucide="check" class="w-4 h-4 text-[#12B76A]"></i><span>Verified Badge on profile</span></li>
                    </ul>
                </div>
                <a href="{{ route('register') }}" class="w-full py-3 rounded-xl bg-teal-500 hover:bg-teal-600 text-white font-bold text-xs text-center shadow-xs transition">
                    Choose Professional
                </a>
            </div>

            <!-- Premium Plan -->
            <div class="p-6 sm:p-8 rounded-2xl bg-white border border-[#E5E7EB] shadow-subtle flex flex-col justify-between space-y-6">
                <div class="space-y-4">
                    <div>
                        <h4 class="text-base font-bold text-navy-900">Premium Plan</h4>
                        <p class="text-xs text-[#667085] mt-1">For established contractors & teams</p>
                    </div>
                    <div>
                        <span class="text-3xl font-black text-navy-900 font-mono">TZS 35,000</span>
                        <span class="text-xs text-[#667085]">/ month</span>
                    </div>
                    <ul class="space-y-2.5 text-xs text-[#667085] pt-4 border-t border-[#E5E7EB]">
                        <li class="flex items-center space-x-2"><i data-lucide="check" class="w-4 h-4 text-[#12B76A]"></i><span>Featured on homepage</span></li>
                        <li class="flex items-center space-x-2"><i data-lucide="check" class="w-4 h-4 text-[#12B76A]"></i><span>Top search ranking</span></li>
                        <li class="flex items-center space-x-2"><i data-lucide="check" class="w-4 h-4 text-[#12B76A]"></i><span>Contractor profile branding</span></li>
                        <li class="flex items-center space-x-2"><i data-lucide="check" class="w-4 h-4 text-[#12B76A]"></i><span>Dedicated admin support</span></li>
                    </ul>
                </div>
                <a href="{{ route('register') }}" class="w-full py-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-navy-900 font-bold text-xs text-center transition">
                    Choose Premium
                </a>
            </div>

        </div>
    </section>

</div>
@endsection
