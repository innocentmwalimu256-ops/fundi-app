@extends('layouts.app')

@section('title', __('Technician Application Status') . ' — FUNDI')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

    <div class="bg-white rounded-3xl p-6 sm:p-10 border border-slate-200 shadow-xs text-center space-y-6">
        
        @if(!$application)
            <div class="w-16 h-16 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto">
                <i data-lucide="file-question" class="w-8 h-8"></i>
            </div>
            <h1 class="text-xl font-bold text-slate-900">{{ __('No Technician Application Found') }}</h1>
            <p class="text-xs text-slate-500 max-w-md mx-auto">{{ __('You have not submitted an application to become a verified fundi yet.') }}</p>
            <div>
                <a href="{{ route('client.become-technician') }}" class="px-5 py-3 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-xl shadow-xs transition inline-flex items-center space-x-2">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    <span>{{ __('Become a Verified Technician') }}</span>
                </a>
            </div>
        @elseif($application->status === 'pending')
            <div class="w-20 h-20 rounded-3xl bg-amber-50 text-amber-600 border border-amber-200 flex items-center justify-center mx-auto shadow-inner">
                <i data-lucide="clock" class="w-10 h-10 animate-pulse"></i>
            </div>
            <div>
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">
                    {{ __('APPLICATION UNDER REVIEW (PENDING)') }}
                </span>
                <h1 class="text-2xl font-black text-slate-900 mt-3">{{ __('Your Application is Under Review by Admin') }}</h1>
                <p class="text-xs text-slate-500 mt-1 max-w-md mx-auto">
                    {{ __('FUNDI team is verifying your National ID and vocational credentials. You will receive an update shortly.') }}
                </p>
            </div>

            <div class="bg-slate-50 rounded-2xl p-4 text-left text-xs space-y-2 border border-slate-200 max-w-md mx-auto">
                <div class="flex justify-between">
                    <span class="text-slate-500">{{ __('Submission Date') }}:</span>
                    <span class="font-bold text-slate-800">{{ $application->created_at->format('d M Y, H:i') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">{{ __('Primary Trade') }}:</span>
                    <span class="font-bold text-slate-800">{{ __($application->service->name ?? 'Technician') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">{{ __('Professional Title') }}:</span>
                    <span class="font-bold text-slate-800">{{ __($application->professional_title) }}</span>
                </div>
            </div>
        @elseif($application->status === 'approved')
            <div class="w-20 h-20 rounded-3xl bg-emerald-50 text-emerald-600 border border-emerald-200 flex items-center justify-center mx-auto shadow-inner">
                <i data-lucide="check-circle-2" class="w-10 h-10"></i>
            </div>
            <div>
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                    {{ __('APPROVED / VERIFIED') }}
                </span>
                <h1 class="text-2xl font-black text-slate-900 mt-3">{{ __('Congratulations! Your Application has been Approved') }}</h1>
                <p class="text-xs text-slate-500 mt-1 max-w-md mx-auto">
                    {{ __('Your technician profile is now verified and active. You can start receiving job requests and clients.') }}
                </p>
            </div>

            <div>
                <a href="{{ route('technician.dashboard') }}" class="px-6 py-3.5 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-xl shadow-xs transition inline-flex items-center space-x-2">
                    <span>{{ __('Go to Technician Dashboard') }}</span>
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </a>
            </div>
        @elseif($application->status === 'rejected')
            <div class="w-20 h-20 rounded-3xl bg-rose-50 text-rose-600 border border-rose-200 flex items-center justify-center mx-auto shadow-inner">
                <i data-lucide="x-circle" class="w-10 h-10"></i>
            </div>
            <div>
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800 border border-rose-200">
                    {{ __('APPLICATION DECLINED') }}
                </span>
                <h1 class="text-2xl font-black text-slate-900 mt-3">{{ __('Documents Require Revision') }}</h1>
                <p class="text-xs text-slate-500 mt-1 max-w-md mx-auto">
                    {{ __('Verification could not be completed with the submitted documents.') }}
                </p>
            </div>

            <div class="bg-rose-50 rounded-2xl p-4 text-left text-xs space-y-1 border border-rose-200 max-w-md mx-auto">
                <span class="font-bold text-rose-900">{{ __('Reason from Admin') }}:</span>
                <p class="text-rose-800 leading-relaxed">{{ $application->rejection_reason ?? __('Please re-upload clearer photos of your valid NIDA ID or technical certificates.') }}</p>
            </div>

            <div class="pt-2">
                <a href="{{ route('client.become-technician') }}" class="px-5 py-3 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-xl shadow-xs transition inline-flex items-center space-x-2">
                    <i data-lucide="rotate-cw" class="w-4 h-4"></i>
                    <span>{{ __('Update & Re-Submit Application') }}</span>
                </a>
            </div>
        @endif

    </div>

</div>
@endsection
