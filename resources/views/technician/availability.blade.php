@extends('layouts.app')

@section('title', __('Availability Status') . ' — FUNDI')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

    <div class="bg-white rounded-3xl p-6 sm:p-10 border border-slate-200 shadow-sm space-y-6">
        
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">{{ __('Availability & Working Schedule') }}</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">{{ __('Control your live receiving status and working hours per day') }}</p>
        </div>

        <form method="POST" action="{{ route('technician.availability.update') }}" class="space-y-6">
            @csrf

            <!-- Live Status Picker -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">{{ __('Current Working Status') }}</label>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <label class="p-4 rounded-2xl border border-slate-200 bg-slate-50 hover:bg-white cursor-pointer flex items-center space-x-3 text-xs font-bold text-slate-800 has-[:checked]:border-emerald-600 has-[:checked]:bg-emerald-50 has-[:checked]:text-emerald-950 transition">
                        <input type="radio" name="availability_status" value="available" {{ ($profile->availability_status ?? 'available') === 'available' ? 'checked' : '' }} class="text-emerald-600 focus:ring-emerald-500">
                        <div>
                            <p class="font-black text-sm">🟢 {{ __('Available Now') }}</p>
                            <span class="text-[11px] font-normal text-slate-500">{{ __('Accepting new service requests') }}</span>
                        </div>
                    </label>

                    <label class="p-4 rounded-2xl border border-slate-200 bg-slate-50 hover:bg-white cursor-pointer flex items-center space-x-3 text-xs font-bold text-slate-800 has-[:checked]:border-amber-500 has-[:checked]:bg-amber-50 has-[:checked]:text-amber-950 transition">
                        <input type="radio" name="availability_status" value="busy" {{ ($profile->availability_status ?? '') === 'busy' ? 'checked' : '' }} class="text-amber-600 focus:ring-amber-500">
                        <div>
                            <p class="font-black text-sm">🟡 {{ __('Busy on Job') }}</p>
                            <span class="text-[11px] font-normal text-slate-500">{{ __('Currently executing active job') }}</span>
                        </div>
                    </label>

                    <label class="p-4 rounded-2xl border border-slate-200 bg-slate-50 hover:bg-white cursor-pointer flex items-center space-x-3 text-xs font-bold text-slate-800 has-[:checked]:border-slate-600 has-[:checked]:bg-slate-200 has-[:checked]:text-slate-950 transition">
                        <input type="radio" name="availability_status" value="offline" {{ ($profile->availability_status ?? '') === 'offline' ? 'checked' : '' }} class="text-slate-600 focus:ring-slate-500">
                        <div>
                            <p class="font-black text-sm">⚪ {{ __('Offline') }}</p>
                            <span class="text-[11px] font-normal text-slate-500">{{ __('Off-duty & hidden from search') }}</span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Weekly Schedule Hours Table -->
            <div class="pt-4 border-t border-slate-100 space-y-4">
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700">{{ __('Weekly Working Hours by Day') }}</h3>
                    <p class="text-xs text-slate-500 mt-0.5">{{ __('Clients will see your available operating slots when requesting appointments') }}</p>
                </div>

                <div class="space-y-3 bg-slate-50 p-4 rounded-2xl border border-slate-200">
                    @foreach($daysOfWeek as $day)
                    @php 
                        $avail = $availabilities->where('day_of_week', $day)->first();
                        $isAvail = $avail ? $avail->is_available : ($day !== 'Sunday');
                        $startTime = $avail ? substr($avail->start_time, 0, 5) : '08:00';
                        $endTime = $avail ? substr($avail->end_time, 0, 5) : '18:00';
                    @endphp
                    <div class="grid grid-cols-1 sm:grid-cols-4 items-center gap-3 py-2 border-b border-slate-200/60 last:border-b-0 text-xs">
                        <label class="flex items-center space-x-2 font-bold text-slate-900 cursor-pointer">
                            <input type="checkbox" name="days[]" value="{{ $day }}" {{ $isAvail ? 'checked' : '' }} class="w-4 h-4 rounded text-teal-700 border-slate-300 focus:ring-teal-500">
                            <span>{{ __($day) }}</span>
                        </label>

                        <div class="sm:col-span-3 flex items-center space-x-3">
                            <div class="flex items-center space-x-1.5 flex-1">
                                <span class="text-[11px] text-slate-400">{{ __('From:') }}</span>
                                <input type="time" name="start_time[{{ $day }}]" value="{{ $startTime }}" class="py-1.5 px-2.5 rounded-xl border border-slate-200 bg-white text-xs text-slate-800 font-mono">
                            </div>
                            <div class="flex items-center space-x-1.5 flex-1">
                                <span class="text-[11px] text-slate-400">{{ __('To:') }}</span>
                                <input type="time" name="end_time[{{ $day }}]" value="{{ $endTime }}" class="py-1.5 px-2.5 rounded-xl border border-slate-200 bg-white text-xs text-slate-800 font-mono">
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100">
                <button type="submit" class="px-6 py-3 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs shadow transition">
                    {{ __('Save Availability Schedule') }}
                </button>
            </div>

        </form>

    </div>

</div>
@endsection
