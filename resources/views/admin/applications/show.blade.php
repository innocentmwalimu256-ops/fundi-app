@extends('layouts.admin')

@section('title', __('Application #') . sprintf('%03d', $application->id))
@section('page_title', __('Technician Application Review #') . sprintf('%03d', $application->id))

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="{ rejectModal: false }">

    <a href="{{ route('admin.applications.index') }}" class="inline-flex items-center text-xs font-bold text-slate-500 hover:text-slate-800">
        <i data-lucide="arrow-left" class="w-4 h-4 mr-1"></i> {{ __('Back to Queue') }}
    </a>

    <!-- Verification Card Header -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-6">
        
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-6">
            <div class="flex items-center space-x-4">
                <div class="w-16 h-16 rounded-2xl bg-slate-900 text-teal-300 font-black text-xl flex items-center justify-center shadow-md">
                    {{ $application->user->initials }}
                </div>
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">{{ __('Application #') }}{{ sprintf('%03d', $application->id) }}</span>
                    <h1 class="text-xl font-black text-slate-900">{{ $application->user->full_name }}</h1>
                    <p class="text-xs font-bold text-teal-700 mt-0.5">{{ __($application->professional_title) }}</p>
                </div>
            </div>

            <!-- Current Status & Reviewer -->
            <div class="text-right">
                <span class="px-3 py-1 rounded-full text-xs font-bold uppercase {{ $application->status === 'approved' ? 'bg-teal-100 text-teal-800' : ($application->status === 'rejected' ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800') }}">
                    {{ __(ucfirst($application->status)) }}
                </span>
                @if($application->reviewed_by)
                <p class="text-[10px] text-slate-400 mt-1">{{ __('Reviewed by') }} {{ $application->reviewer->full_name }} {{ __('on') }} {{ $application->reviewed_at?->format('d M Y') }}</p>
                @endif
            </div>
        </div>

        <!-- Application Metadata -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100">
                <span class="text-slate-400 block text-[10px] uppercase font-bold">{{ __('Category') }}</span>
                <span class="font-bold text-slate-900 mt-0.5 block">{{ __($application->service->name ?? 'General') }}</span>
            </div>
            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100">
                <span class="text-slate-400 block text-[10px] uppercase font-bold">{{ __('Experience') }}</span>
                <span class="font-bold text-slate-900 mt-0.5 block">{{ $application->years_experience }} {{ __('yrs exp') }}</span>
            </div>
            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100">
                <span class="text-slate-400 block text-[10px] uppercase font-bold">{{ __('Location') }}</span>
                <span class="font-bold text-slate-900 mt-0.5 block">{{ $application->location }}</span>
            </div>
            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100">
                <span class="text-slate-400 block text-[10px] uppercase font-bold">{{ __('Coverage Area') }}</span>
                <span class="font-bold text-slate-900 mt-0.5 block">{{ $application->service_area ?? 'Regional' }}</span>
            </div>
        </div>

        <!-- Bio -->
        <div>
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">{{ __('Applicant Statement & Bio') }}</h3>
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 text-xs text-slate-700 leading-relaxed">
                {{ $application->bio }}
            </div>
        </div>

        <!-- Skills -->
        @if(!empty($application->skills))
        <div>
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">{{ __('Submitted Skills') }}</h3>
            <div class="flex flex-wrap gap-2">
                @foreach($application->skills as $skill)
                <span class="px-2.5 py-1 rounded-xl bg-slate-100 text-slate-700 text-xs font-semibold">
                    {{ $skill }}
                </span>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Uploaded Documents for Verification -->
        <div class="space-y-3 pt-2">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-900">{{ __('Submitted Verification Documents') }}</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                @if($application->id_document_path)
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                    <div class="flex items-center space-x-2.5">
                        <i data-lucide="file-badge" class="w-6 h-6 text-teal-600"></i>
                        <div>
                            <p class="text-xs font-bold text-slate-900">{{ __('National ID / Passport') }}</p>
                            <span class="text-[10px] text-slate-400">{{ __('PDF / Image Document') }}</span>
                        </div>
                    </div>
                    <a href="{{ asset('storage/' . $application->id_document_path) }}" target="_blank" class="px-3 py-1.5 bg-white hover:bg-slate-100 text-slate-800 border border-slate-200 rounded-xl text-xs font-bold shadow-sm transition">
                        {{ __('View Document') }}
                    </a>
                </div>
                @endif

                @if($application->certificate_document_path)
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                    <div class="flex items-center space-x-2.5">
                        <i data-lucide="award" class="w-6 h-6 text-teal-600"></i>
                        <div>
                            <p class="text-xs font-bold text-slate-900">{{ __('Vocational / Academic Cert') }}</p>
                            <span class="text-[10px] text-slate-400">{{ __('VETA / Certified Qualification') }}</span>
                        </div>
                    </div>
                    <a href="{{ asset('storage/' . $application->certificate_document_path) }}" target="_blank" class="px-3 py-1.5 bg-white hover:bg-slate-100 text-slate-800 border border-slate-200 rounded-xl text-xs font-bold shadow-sm transition">
                        {{ __('View Document') }}
                    </a>
                </div>
                @endif
            </div>
        </div>

        <!-- Approval / Rejection Decision Actions -->
        <div class="pt-6 border-t border-slate-100 flex items-center space-x-3">
            <form method="POST" action="{{ route('admin.applications.approve', $application->id) }}" class="flex-1">
                @csrf
                <button type="submit" class="w-full py-3.5 px-4 rounded-2xl bg-teal-600 hover:bg-teal-700 text-white font-black text-xs sm:text-sm shadow-lg shadow-teal-600/20 transition flex items-center justify-center space-x-2">
                    <i data-lucide="check-circle-2" class="w-5 h-5"></i>
                    <span>{{ __('APPROVE & GRANT VERIFIED TECHNICIAN BADGE') }}</span>
                </button>
            </form>

            <button type="button" @click="rejectModal = true" class="py-3.5 px-6 rounded-2xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs border border-rose-200 transition">
                {{ __('REJECT APPLICATION') }}
            </button>
        </div>

    </div>

    <!-- Rejection Modal -->
    <div x-show="rejectModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
        <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl border border-slate-100 space-y-4" @click.outside="rejectModal = false">
            <h3 class="text-base font-bold text-slate-900">{{ __('Reject Technician Application') }}</h3>
            <p class="text-xs text-slate-500">{{ __('Specify why the applicant\'s credentials were not verified so they can fix and resubmit.') }}</p>
            
            <form method="POST" action="{{ route('admin.applications.reject', $application->id) }}" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">{{ __('Rejection Reason') }}</label>
                    <textarea name="rejection_reason" rows="4" required placeholder="e.g. Uploaded certificate is illegible; please upload clear scan of your NIDA card." class="w-full p-3 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-rose-500 bg-slate-50"></textarea>
                </div>
                <div class="flex justify-end space-x-2 pt-2">
                    <button type="button" @click="rejectModal = false" class="px-4 py-2 text-xs font-bold text-slate-600 rounded-xl hover:bg-slate-100">{{ __('Cancel') }}</button>
                    <button type="submit" class="px-5 py-2 text-xs font-bold bg-rose-600 text-white rounded-xl shadow hover:bg-rose-700">{{ __('Reject Application') }}</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection