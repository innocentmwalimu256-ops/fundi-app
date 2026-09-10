@extends('layouts.app')

@section('title', $technician->full_name . ' - ' . __('Verified Professional'))

@section('content')
@php $tp = $technician->technicianProfile; @endphp
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-8" x-data="{ reportModal: false }">

    <!-- Header & Breadcrumb -->
    <div>
        <a href="{{ route('client.technicians.index') }}" class="inline-flex items-center text-xs font-bold text-slate-500 hover:text-slate-800 mb-4 transition">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-1"></i> {{ __('Back to Technician Directory') }}
        </a>

        <!-- Main Profile Banner Card -->
        <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200 shadow-xs relative overflow-hidden">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                
                <div class="flex items-start sm:items-center space-x-5">
                    <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl bg-slate-900 text-teal-300 font-black text-2xl flex items-center justify-center flex-shrink-0 shadow-xs">
                        {{ $technician->initials }}
                    </div>

                    <div class="space-y-1.5">
                        <div class="flex flex-wrap items-center gap-2">
                            <h1 class="text-xl sm:text-2xl font-black text-slate-900">{{ $technician->full_name }}</h1>
                            @if($technician->isFeaturedTechnician())
                                <span class="inline-flex items-center space-x-1 px-2.5 py-0.5 rounded-full bg-amber-50 text-amber-900 border border-amber-300 text-xs font-black shadow-xs">
                                    <span>⭐ {{ __('Featured Technician') }}</span>
                                </span>
                            @endif
                            @if($tp && $tp->verification_status === 'approved')
                                <span class="inline-flex items-center space-x-1 px-2.5 py-0.5 rounded-full bg-teal-50 text-teal-800 border border-teal-200 text-xs font-bold">
                                    <i data-lucide="award" class="w-3.5 h-3.5 text-teal-600"></i>
                                    <span>{{ __('Verified Professional') }}</span>
                                </span>
                            @endif
                        </div>

                        <p class="text-sm font-bold text-teal-700">{{ __($tp->professional_title ?? 'Master Technician') }}</p>

                        <div class="flex flex-wrap items-center gap-4 text-xs text-slate-500 pt-0.5">
                            @if(($tp->average_rating ?? 0) > 0)
                            <span class="flex items-center font-bold text-amber-500">
                                ★ {{ number_format($tp->average_rating, 1) }}
                                @if(($tp->total_reviews ?? 0) > 0)
                                    <span class="text-slate-400 font-normal ml-1">({{ $tp->total_reviews }} {{ __('reviews') }})</span>
                                @endif
                            </span>
                            @else
                            <span class="flex items-center font-bold text-teal-700 bg-teal-50 px-2.5 py-0.5 rounded-lg border border-teal-100 text-xs">
                                {{ __('New') }}
                            </span>
                            @endif
                            <span>• <i data-lucide="map-pin" class="w-3.5 h-3.5 inline mr-0.5 text-slate-400"></i>{{ $tp->location ?? 'Dar es Salaam' }}</span>
                            <span>• <i data-lucide="briefcase" class="w-3.5 h-3.5 inline mr-0.5 text-slate-400"></i>{{ $tp->years_experience ?? 1 }} {{ __('Years Experience') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Action CTA & Favorite -->
                <div class="flex flex-wrap items-center gap-3 self-stretch sm:self-auto flex-shrink-0">
                    <form method="POST" action="{{ route('technicians.favorite', $technician->id) }}">
                        @csrf
                        <button type="submit" class="p-3 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 shadow-xs transition text-slate-500 hover:text-rose-600 {{ $isFavorite ? 'text-rose-600 bg-rose-50 border-rose-200' : '' }}" title="{{ $isFavorite ? __('Saved') : __('Save Technician') }}">
                            <i data-lucide="heart" class="w-5 h-5 {{ $isFavorite ? 'fill-rose-500 stroke-rose-500' : '' }}"></i>
                        </button>
                    </form>

                    <a href="{{ route('client.requests.create', ['technician_id' => $technician->id]) }}" class="flex-1 sm:flex-none px-6 py-3.5 bg-teal-600 hover:bg-teal-700 text-white font-black text-xs uppercase tracking-wider rounded-xl shadow-xs transition text-center flex items-center justify-center space-x-2">
                        <i data-lucide="send" class="w-4 h-4"></i>
                        <span>{{ __('Request Service') }}</span>
                    </a>
                </div>

            </div>
        </div>
    </div>

    <!-- Trust Score & Response Performance Card -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs">
            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">{{ __('Response Rate') }}</p>
            <h3 class="text-2xl font-black text-teal-700 mt-0.5">{{ $tp->response_rate ?? 95 }}%</h3>
            <span class="text-[11px] text-slate-500">{{ __('Fast client replies') }}</span>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs">
            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">{{ __('Avg. Response Time') }}</p>
            <h3 class="text-2xl font-black text-slate-900 mt-0.5">{{ $tp->avg_response_time ?? '15 min' }}</h3>
            <span class="text-[11px] text-slate-500">{{ __('Typical response speed') }}</span>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs">
            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">{{ __('Completion Rate') }}</p>
            <h3 class="text-2xl font-black text-emerald-600 mt-0.5">{{ $tp->completion_rate ?? 98 }}%</h3>
            <span class="text-[11px] text-slate-500">{{ $tp->completed_jobs_count ?? 0 }} {{ __('successful jobs completed') }}</span>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs">
            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">{{ __('Smart Match') }}</p>
            <h3 class="text-2xl font-black text-teal-700 mt-0.5">⚡ {{ $matchScore ?? 92 }}%</h3>
            <span class="text-[11px] text-teal-700 font-bold">{{ __('Top recommendation') }}</span>
        </div>
    </div>

    <!-- 2 Column Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left 2 Cols: Bio, Portfolio Showcase, Reviews -->
        <div class="lg:col-span-2 space-y-8">

            <!-- About & Technical Skills -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-6">
                <div class="space-y-2">
                    <h2 class="text-base font-bold uppercase tracking-wider text-slate-900">{{ __('About & Background') }}</h2>
                    <p class="text-xs sm:text-sm text-slate-700 leading-relaxed">
                        {{ __($tp->bio ?? 'No detailed biography provided.') }}
                    </p>
                </div>

                <!-- Skills Tags -->
                @if(!empty($tp->skills))
                <div class="space-y-2 pt-4 border-t border-slate-100">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">{{ __('Technical Skills & Expertise') }}</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach($tp->skills as $skill)
                        <span class="px-3 py-1.5 rounded-xl bg-slate-100 text-slate-800 text-xs font-semibold">
                            ✓ {{ __($skill) }}
                        </span>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <!-- Previous Work Portfolio Showcase -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-bold uppercase tracking-wider text-slate-900">{{ __('Previous Work & Portfolio') }}</h2>
                        <p class="text-xs text-slate-500">{{ __('Verified photos of past completed projects and installations') }}</p>
                    </div>
                    <span class="text-xs font-bold text-slate-400 font-mono">{{ $technician->portfolios->count() }} {{ __('Projects') }}</span>
                </div>

                @if($technician->portfolios->isNotEmpty())
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach($technician->portfolios as $port)
                    <div class="bg-white rounded-2xl overflow-hidden border border-slate-200/90 shadow-sm flex flex-col justify-between hover:shadow-md transition">
                        @if($port->image_path)
                        <a href="{{ asset('storage/' . $port->image_path) }}" target="_blank" class="block aspect-video bg-slate-100 overflow-hidden hover:opacity-95 transition relative group">
                            <img src="{{ asset('storage/' . $port->image_path) }}" alt="{{ $port->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            <span class="absolute inset-0 bg-slate-900/20 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white">
                                <i data-lucide="maximize-2" class="w-5 h-5 drop-shadow"></i>
                            </span>
                        </a>
                        @endif
                        <div class="p-4 space-y-1.5">
                            <div class="flex items-center justify-between">
                                <h4 class="text-xs font-black text-slate-900">{{ $port->title }}</h4>
                                <span class="text-[10px] text-slate-400 font-medium">{{ $port->project_date }}</span>
                            </div>
                            <p class="text-[11px] text-slate-600 leading-relaxed line-clamp-2">{{ $port->description }}</p>
                            @if($port->service)
                            <span class="inline-flex items-center space-x-1 mt-1.5 px-2.5 py-0.5 rounded-lg bg-teal-50 text-teal-700 text-[10px] font-bold border border-teal-100">
                                <i data-lucide="tag" class="w-2.5 h-2.5"></i>
                                <span>{{ __($port->service->name) }}</span>
                            </span>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="p-8 text-center bg-slate-50/80 rounded-2xl border border-slate-100 text-xs text-slate-400 space-y-2">
                    <i data-lucide="image" class="w-8 h-8 mx-auto text-slate-300"></i>
                    <p class="font-medium text-slate-600">{{ __('No portfolio photos uploaded yet.') }}</p>
                    <p class="text-[11px] text-slate-400">{{ __('This technician has not uploaded work photos yet.') }}</p>
                </div>
                @endif
            </div>

            <!-- Client Reviews & Sub-Ratings -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-bold uppercase tracking-wider text-slate-900">{{ __('Verified Client Reviews') }}</h2>
                        <p class="text-xs text-slate-500">{{ __('Feedback from clients with completed jobs') }}</p>
                    </div>
                    @if(($tp->total_reviews ?? 0) > 0)
                    <span class="font-bold text-amber-500 text-sm">
                        ★ {{ number_format($tp->average_rating, 1) }} / 5.0 ({{ $tp->total_reviews }} {{ __('reviews') }})
                    </span>
                    @else
                    <span class="font-bold text-teal-700 bg-teal-50 px-3 py-1 rounded-xl border border-teal-100 text-xs">
                        {{ __('New (No reviews yet)') }}
                    </span>
                    @endif
                </div>

                <!-- Sub-Criteria Breakdown -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 p-4 rounded-2xl bg-slate-50 border border-slate-100 text-xs text-center">
                    <div>
                        <span class="text-slate-400 block text-[10px] uppercase font-bold">{{ __('Quality') }}</span>
                        <span class="font-bold text-slate-900 mt-0.5 block">★ {{ $ratingStats['quality'] }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[10px] uppercase font-bold">{{ __('Punctuality') }}</span>
                        <span class="font-bold text-slate-900 mt-0.5 block">★ {{ $ratingStats['punctuality'] }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[10px] uppercase font-bold">{{ __('Professionalism') }}</span>
                        <span class="font-bold text-slate-900 mt-0.5 block">★ {{ $ratingStats['professionalism'] }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[10px] uppercase font-bold">{{ __('Communication') }}</span>
                        <span class="font-bold text-slate-900 mt-0.5 block">★ {{ $ratingStats['communication'] }}</span>
                    </div>
                </div>

                <!-- Reviews Stream -->
                <div class="space-y-4 divide-y divide-slate-100">
                    @forelse($reviews as $rev)
                    <div class="pt-4 first:pt-0 space-y-2">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2.5">
                                <div class="w-8 h-8 rounded-xl bg-slate-100 text-slate-800 font-bold text-xs flex items-center justify-center">
                                    {{ $rev->client->initials }}
                                </div>
                                <div>
                                    <h4 class="text-xs font-bold text-slate-900">{{ $rev->client->full_name }}</h4>
                                    <span class="text-[10px] text-teal-700 font-medium">✓ {{ __('Verified Completed Job') }}</span>
                                </div>
                            </div>
                            <div class="flex items-center text-amber-400 text-xs">
                                @for($i = 1; $i <= 5; $i++)
                                    <i data-lucide="star" class="w-3.5 h-3.5 {{ $i <= $rev->rating ? 'fill-amber-400 stroke-amber-400' : 'text-slate-200 stroke-slate-200' }}"></i>
                                @endfor
                            </div>
                        </div>

                        @if($rev->comment)
                        <p class="text-xs text-slate-700 leading-relaxed pl-10">
                            "{{ $rev->comment }}"
                        </p>
                        @endif
                    </div>
                    @empty
                    <div class="p-8 text-center text-slate-400 text-xs">
                        {{ __('No client reviews received yet. Reviews will automatically populate as clients confirm completed jobs.') }}
                    </div>
                    @endforelse
                </div>
            </div>

        </div>

        <!-- Right Col: Availability Calendar, Service Areas, Safety -->
        <div class="space-y-6">

            <!-- Weekly Availability Schedule -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-900">{{ __('Weekly Working Hours') }}</h3>
                    <span class="w-2 h-2 rounded-full {{ ($tp->availability_status ?? '') === 'available' ? 'bg-emerald-500' : 'bg-amber-500' }}"></span>
                </div>

                <div class="space-y-2 text-xs divide-y divide-slate-100">
                    @php $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday']; @endphp
                    @foreach($days as $day)
                    @php 
                        $avail = $technician->availabilities->where('day_of_week', $day)->first();
                        $isAvail = $avail ? $avail->is_available : ($day !== 'Sunday');
                    @endphp
                    <div class="flex items-center justify-between py-1.5 first:pt-0">
                        <span class="font-medium text-slate-700">{{ __($day) }}</span>
                        @if($isAvail)
                            <span class="text-slate-500 font-mono text-[11px]">
                                {{ $avail ? substr($avail->start_time, 0, 5) . ' – ' . substr($avail->end_time, 0, 5) : '08:00 – 18:00' }}
                            </span>
                        @else
                            <span class="text-slate-400 text-[11px] italic">{{ __('Unavailable') }}</span>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Service Coverage Districts -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm space-y-3">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-900">{{ __('Service Coverage Areas') }}</h3>
                <p class="text-xs text-slate-500">{{ __('Technician provides call-out services across:') }}</p>
                
                <div class="flex flex-wrap gap-1.5 pt-1">
                    @if($technician->serviceAreas->isNotEmpty())
                        @foreach($technician->serviceAreas as $area)
                        <span class="px-3 py-1 rounded-xl bg-teal-50 text-teal-800 border border-teal-200 text-xs font-bold">
                            📍 {{ $area->area_name }}
                        </span>
                        @endforeach
                    @else
                        <span class="px-3 py-1 rounded-xl bg-slate-100 text-slate-700 text-xs font-medium">
                            📍 {{ $tp->service_area ?? $tp->location ?? __('Dar es Salaam Citywide') }}
                        </span>
                    @endif
                </div>
            </div>

            <!-- Platform Safety & Reporting -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm space-y-3">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">{{ __('Platform Safety & Trust') }}</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    {{ __('All technicians undergo National ID verification and customer review checks. Notice anything inappropriate?') }}
                </p>
                <button type="button" @click="reportModal = true" class="text-xs font-bold text-rose-600 hover:text-rose-800 flex items-center">
                    <i data-lucide="flag" class="w-3.5 h-3.5 mr-1"></i>
                    <span>{{ __('Report this Technician') }}</span>
                </button>
            </div>

        </div>

    </div>

    <!-- Report Technician Modal -->
    <div x-show="reportModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
        <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl border border-slate-100 space-y-4" @click.outside="reportModal = false">
            <h3 class="text-base font-bold text-slate-900">{{ __('Report') }} {{ $technician->full_name }}</h3>
            <p class="text-xs text-slate-500">{{ __('Your report will be confidentially sent to platform administrators for investigation.') }}</p>

            <form method="POST" action="{{ route('technicians.report', $technician->id) }}" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">{{ __('Reason for Report') }}</label>
                    <select name="reason" required class="w-full py-2.5 px-3 rounded-xl border border-slate-200 text-xs bg-slate-50">
                        <option value="Fraud / suspicious activity">{{ __('Fraud / suspicious activity') }}</option>
                        <option value="Professional misconduct">{{ __('Professional misconduct') }}</option>
                        <option value="False qualification information">{{ __('False qualification information') }}</option>
                        <option value="Unprofessional behaviour">{{ __('Unprofessional behaviour') }}</option>
                        <option value="Other safety issue">{{ __('Other safety issue') }}</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">{{ __('Details & Evidence') }}</label>
                    <textarea name="details" rows="3" required placeholder="{{ __('Describe the issue specifically...') }}" class="w-full p-3 rounded-xl border border-slate-200 text-xs bg-slate-50"></textarea>
                </div>

                <div class="flex justify-end space-x-2 pt-2">
                    <button type="button" @click="reportModal = false" class="px-4 py-2 text-xs font-bold text-slate-600 rounded-xl hover:bg-slate-100">{{ __('Cancel') }}</button>
                    <button type="submit" class="px-5 py-2 text-xs font-bold bg-rose-600 text-white rounded-xl shadow hover:bg-rose-700">{{ __('Submit Report') }}</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
