@extends('layouts.app')

@section('title', 'Presentation Demo Center - FUNDI v4.0')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">

    <div class="text-center space-y-2">
        <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-teal-50 text-teal-800 text-xs font-bold border border-teal-200">
            <i data-lucide="presentation" class="w-3.5 h-3.5"></i>
            <span>Examiner & Presentation Demonstration Mode — FUNDI v4.0</span>
        </div>
        <h1 class="text-3xl font-black text-slate-900 tracking-tight">Presentation Scenarios Control Center</h1>
        <p class="text-xs sm:text-sm text-slate-500 max-w-2xl mx-auto">
            Select a verified demo persona below to instantly experience the subscription monetization model, contact access protection, and marketplace workflows.
        </p>
    </div>

    <!-- Core Demo Personas Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <!-- 1. Client Persona -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm flex flex-col justify-between space-y-4 hover:border-brand-500 transition">
            <div class="space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-700 flex items-center justify-center font-black">
                    <i data-lucide="user" class="w-6 h-6"></i>
                </div>
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full">Client Perspective</span>
                    <h3 class="text-base font-bold text-slate-900 mt-1">John Client</h3>
                    <p class="text-xs text-slate-500">client@fundi.test</p>
                </div>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Browse trade categories, send service requests with photos, communicate, agree on service price directly, and download digital job completion receipts.
                </p>
            </div>

            <form method="POST" action="{{ route('login.submit') }}">
                @csrf
                <input type="hidden" name="login" value="client@fundi.test">
                <input type="hidden" name="password" value="password123">
                <button type="submit" class="w-full py-3 rounded-2xl bg-brand-700 hover:bg-brand-800 text-white text-xs font-bold shadow transition flex items-center justify-center space-x-1.5">
                    <span>Enter as Client</span>
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </button>
            </form>
        </div>

        <!-- 2. Active Technician Persona (Professional Plan Active) -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm flex flex-col justify-between space-y-4 hover:border-teal-500 transition">
            <div class="space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-teal-50 text-teal-700 flex items-center justify-center font-black">
                    <i data-lucide="wrench" class="w-6 h-6"></i>
                </div>
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-teal-700 bg-teal-50 px-2 py-0.5 rounded-full">Active Plan (Professional)</span>
                    <h3 class="text-base font-bold text-slate-900 mt-1">John Mwakyusa</h3>
                    <p class="text-xs text-slate-500">tech@fundi.test (Electrical)</p>
                </div>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Active subscription unlocks client requests, direct WhatsApp communication, itemized quotes, and work portfolio showcases.
                </p>
            </div>

            <form method="POST" action="{{ route('login.submit') }}">
                @csrf
                <input type="hidden" name="login" value="tech@fundi.test">
                <input type="hidden" name="password" value="password123">
                <button type="submit" class="w-full py-3 rounded-2xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold shadow transition flex items-center justify-center space-x-1.5">
                    <span>Enter as Active Tech</span>
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </button>
            </form>
        </div>

        <!-- 3. Expired Technician Persona (Subscription Expired Scenario) -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm flex flex-col justify-between space-y-4 hover:border-rose-500 transition">
            <div class="space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-700 flex items-center justify-center font-black">
                    <i data-lucide="lock" class="w-6 h-6"></i>
                </div>
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-rose-700 bg-rose-50 px-2 py-0.5 rounded-full">Subscription Expired</span>
                    <h3 class="text-base font-bold text-slate-900 mt-1">Hassan Ali (Plumber)</h3>
                    <p class="text-xs text-slate-500">expiredtech@fundi.test</p>
                </div>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Demonstrates the server-side subscription barrier, expired screen, plan checkout, and instant reactivation workflow.
                </p>
            </div>

            <form method="POST" action="{{ route('login.submit') }}">
                @csrf
                <input type="hidden" name="login" value="expiredtech@fundi.test">
                <input type="hidden" name="password" value="password123">
                <button type="submit" class="w-full py-3 rounded-2xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold shadow transition flex items-center justify-center space-x-1.5">
                    <span>Enter as Expired Tech</span>
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </button>
            </form>
        </div>

    </div>

    <!-- Admin & Applicant Row -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <!-- Administrator -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm flex flex-col justify-between space-y-4 hover:border-purple-500 transition">
            <div class="space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-700 flex items-center justify-center font-black">
                    <i data-lucide="shield" class="w-6 h-6"></i>
                </div>
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-purple-700 bg-purple-50 px-2 py-0.5 rounded-full">Super Admin</span>
                    <h3 class="text-base font-bold text-slate-900 mt-1">FUNDI Administrator</h3>
                    <p class="text-xs text-slate-500">admin@fundi.test</p>
                </div>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Manage subscription plans, inspect monetization revenue, view transaction ledgers, verify applicants, and mediate disputes.
                </p>
            </div>

            <form method="POST" action="{{ route('login.submit') }}">
                @csrf
                <input type="hidden" name="login" value="admin@fundi.test">
                <input type="hidden" name="password" value="password123">
                <button type="submit" class="w-full py-3 rounded-2xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold shadow transition flex items-center justify-center space-x-1.5">
                    <span>Enter as Admin</span>
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </button>
            </form>
        </div>

        <!-- Pending Applicant -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm flex flex-col justify-between space-y-4 hover:border-amber-500 transition">
            <div class="space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-700 flex items-center justify-center font-black">
                    <i data-lucide="award" class="w-6 h-6"></i>
                </div>
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-amber-700 bg-amber-50 px-2 py-0.5 rounded-full">Pending Verification</span>
                    <h3 class="text-base font-bold text-slate-900 mt-1">Baraka Peter (Carpenter)</h3>
                    <p class="text-xs text-slate-500">pendingtech@fundi.test</p>
                </div>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Demonstrates the onboarding application state and document inspection by administrators.
                </p>
            </div>

            <form method="POST" action="{{ route('login.submit') }}">
                @csrf
                <input type="hidden" name="login" value="pendingtech@fundi.test">
                <input type="hidden" name="password" value="password123">
                <button type="submit" class="w-full py-3 rounded-2xl bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold shadow transition flex items-center justify-center space-x-1.5">
                    <span>Enter as Applicant</span>
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </button>
            </form>
        </div>

    </div>

</div>
@endsection
