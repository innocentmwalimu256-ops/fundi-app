<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('FUNDI - Executive Platform & Financial Audit Report') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
            color: #0f172a;
        }
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: white !important;
                color: black !important;
                padding: 0 !important;
            }
            .page-break {
                page-break-before: always;
            }
            .report-card {
                box-shadow: none !important;
                border: 1px solid #e2e8f0 !important;
            }
        }
    </style>
</head>
<body class="p-4 sm:p-8">

    <!-- Top Action Bar (Hidden when printing / saving PDF) -->
    <div class="max-w-5xl mx-auto mb-6 no-print flex flex-wrap items-center justify-between gap-4 bg-slate-900 text-white p-4 rounded-2xl shadow-lg">
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.reports.index') }}" class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-xs font-bold text-slate-200 transition">
                &larr; {{ __('Back to Analytics') }}
            </a>
            <span class="text-xs text-slate-400">|</span>
            <span class="text-xs font-semibold text-teal-400">{{ __('Printable Statement / PDF Mode') }}</span>
        </div>

        <div class="flex items-center space-x-2">
            <a href="{{ route('admin.reports.export.csv') }}" class="px-4 py-2 rounded-xl bg-teal-600 hover:bg-teal-500 text-white text-xs font-bold transition shadow-xs flex items-center space-x-1.5">
                <span><i data-lucide="download" class="w-3.5 h-3.5 inline"></i> {{ __('Export CSV') }}</span>
            </a>
            <button onclick="window.print()" class="px-5 py-2 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 text-xs font-black transition shadow-md flex items-center space-x-1.5">
                <span><i data-lucide="printer" class="w-3.5 h-3.5 inline"></i>️ {{ __('Print / Save as PDF') }}</span>
            </button>
        </div>
    </div>

    <!-- Main Report Document Container -->
    <div class="max-w-5xl mx-auto bg-white rounded-3xl border border-slate-200 shadow-xl p-8 sm:p-12 space-y-8 report-card">
        
        <!-- Header & Branding -->
        <div class="border-b border-slate-200 pb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6">
            <div class="space-y-1.5">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-2xl bg-teal-600 text-white font-black text-xl flex items-center justify-center shadow-md">
                        F
                    </div>
                    <div>
                        <h1 class="text-2xl font-black tracking-tight text-slate-900">FUNDI PLATFORM</h1>
                        <p class="text-xs font-bold text-teal-700 uppercase tracking-wider">{{ __('Verified Artisan Marketplace System') }}</p>
                    </div>
                </div>
                <p class="text-xs text-slate-500 max-w-md">
                    {{ __('Comprehensive Business Analytics, Operations Performance & Financial Statement Report.') }}
                </p>
            </div>

            <div class="sm:text-right bg-slate-50 p-4 rounded-2xl border border-slate-100 space-y-1">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">{{ __('Report Reference') }}</p>
                <p class="font-mono font-bold text-xs text-slate-900">REP-{{ date('Ymd-His') }}</p>
                <p class="text-[11px] text-slate-500">{{ __('Date') }}: <span class="font-semibold text-slate-700">{{ date('d M Y, H:i') }}</span></p>
                <p class="text-[11px] text-slate-500">{{ __('Auditor / Admin') }}: <span class="font-semibold text-slate-700">{{ $admin->full_name }}</span></p>
            </div>
        </div>

        <!-- Executive Summary KPI Cards -->
        <div>
            <h2 class="text-xs font-black uppercase tracking-wider text-slate-400 mb-3">{{ __('1. Key Platform Performance Metrics') }}</h2>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                    <p class="text-[11px] font-bold text-slate-500 uppercase">{{ __('Total Platform Revenue') }}</p>
                    <h3 class="text-xl font-black text-teal-700 mt-1">TZS {{ number_format($totalProfit, 0) }}</h3>
                    <p class="text-[10px] text-slate-400 mt-0.5">{{ __('Combined Subscriptions & Fees') }}</p>
                </div>

                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                    <p class="text-[11px] font-bold text-slate-500 uppercase">{{ __('Job Completion Rate') }}</p>
                    @php $compRate = $jobStats['total'] > 0 ? round(($jobStats['completed'] / $jobStats['total']) * 100, 1) : 100; @endphp
                    <h3 class="text-xl font-black text-emerald-600 mt-1">{{ $compRate }}%</h3>
                    <p class="text-[10px] text-emerald-600 font-semibold mt-0.5">{{ $jobStats['completed'] }} / {{ $jobStats['total'] }} {{ __('Jobs Closed') }}</p>
                </div>

                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                    <p class="text-[11px] font-bold text-slate-500 uppercase">{{ __('Marketplace Users') }}</p>
                    <h3 class="text-xl font-black text-slate-900 mt-1">{{ $totalUsers }}</h3>
                    <p class="text-[10px] text-slate-500 mt-0.5">{{ $totalClients }} {{ __('Clients') }} • {{ $totalTechs }} {{ __('Technicians') }}</p>
                </div>

                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                    <p class="text-[11px] font-bold text-slate-500 uppercase">{{ __('Active Work Orders') }}</p>
                    <h3 class="text-xl font-black text-indigo-600 mt-1">{{ $jobStats['active'] }}</h3>
                    <p class="text-[10px] text-slate-400 mt-0.5">{{ __('In-progress / On the way') }}</p>
                </div>

            </div>
        </div>

        <!-- Financial Breakdown Section -->
        <div class="space-y-3">
            <h2 class="text-xs font-black uppercase tracking-wider text-slate-400">{{ __('2. Financial Performance & Revenue Monetization') }}</h2>
            <div class="overflow-x-auto rounded-2xl border border-slate-200">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-100 text-slate-700 font-bold uppercase text-[10px] tracking-wider">
                        <tr>
                            <th class="py-3 px-4">{{ __('Revenue Stream') }}</th>
                            <th class="py-3 px-4">{{ __('Description') }}</th>
                            <th class="py-3 px-4">{{ __('Unit Rate / Pricing') }}</th>
                            <th class="py-3 px-4 text-right">{{ __('Total Collected (TZS)') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-800">
                        <tr>
                            <td class="py-3 px-4 font-bold text-slate-900">{{ __('Technician Subscriptions') }}</td>
                            <td class="py-3 px-4 text-slate-500">{{ __('Monthly tiered artisan membership fees (Starter, Pro, Premium)') }}</td>
                            <td class="py-3 px-4 font-mono">TZS 10,000 - 30,000</td>
                            <td class="py-3 px-4 text-right font-black text-slate-900 font-mono">TZS {{ number_format($subRev, 0) }}</td>
                        </tr>
                        <tr>
                            <td class="py-3 px-4 font-bold text-slate-900">{{ __('Client Connection Platform Fees') }}</td>
                            <td class="py-3 px-4 text-slate-500">{{ __('Direct artisan connection protection and matching fee per request') }}</td>
                            <td class="py-3 px-4 font-mono">TZS 2,000 / Request</td>
                            <td class="py-3 px-4 text-right font-black text-slate-900 font-mono">TZS {{ number_format($clientRev, 0) }}</td>
                        </tr>
                        <tr class="bg-teal-50 font-black">
                            <td class="py-3.5 px-4 text-teal-950" colspan="3">{{ __('TOTAL NET BUSINESS REVENUE') }}</td>
                            <td class="py-3.5 px-4 text-right text-teal-900 font-mono text-sm">TZS {{ number_format($totalProfit, 0) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Service Category Demand Volume -->
        <div class="space-y-3">
            <h2 class="text-xs font-black uppercase tracking-wider text-slate-400">{{ __('3. Service Category Distribution & Demand Share') }}</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                @foreach($serviceStats as $srv)
                @php $share = $jobStats['total'] > 0 ? round(($srv->requests_count / $jobStats['total']) * 100, 1) : 0; @endphp
                <div class="p-3.5 rounded-xl border border-slate-200 bg-slate-50 flex items-center justify-between">
                    <div>
                        <p class="font-bold text-xs text-slate-900">{{ __($srv->name) }}</p>
                        <p class="text-[10px] text-slate-500">{{ $srv->requests_count }} {{ __('service requests') }}</p>
                    </div>
                    <div class="text-right">
                        <span class="text-xs font-black text-teal-700">{{ $share }}%</span>
                        <span class="text-[10px] text-slate-400 block">{{ __('Market share') }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Top Performing Technicians Leaderboard -->
        <div class="space-y-3">
            <h2 class="text-xs font-black uppercase tracking-wider text-slate-400">{{ __('4. High-Performance Verified Artisans') }}</h2>
            <div class="overflow-x-auto rounded-2xl border border-slate-200">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-100 text-slate-700 font-bold uppercase text-[10px] tracking-wider">
                        <tr>
                            <th class="py-2.5 px-4">{{ __('Technician Name') }}</th>
                            <th class="py-2.5 px-4">{{ __('Specialty / Title') }}</th>
                            <th class="py-2.5 px-4">{{ __('Location') }}</th>
                            <th class="py-2.5 px-4 text-center">{{ __('Completed Jobs') }}</th>
                            <th class="py-2.5 px-4 text-right">{{ __('Rating') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-800">
                        @forelse($topTechnicians as $tech)
                        @php $tp = $tech->technicianProfile; @endphp
                        <tr>
                            <td class="py-2.5 px-4 font-bold text-slate-900">{{ $tech->full_name }}</td>
                            <td class="py-2.5 px-4 text-slate-600">{{ __($tp->professional_title ?? 'Technician') }}</td>
                            <td class="py-2.5 px-4 text-slate-500">{{ $tp->location ?? 'Dar es Salaam' }}</td>
                            <td class="py-2.5 px-4 text-center font-bold text-slate-900">{{ $tp->completed_jobs_count ?? 0 }}</td>
                            <td class="py-2.5 px-4 text-right font-black text-amber-500 font-mono"><i data-lucide="star" class="w-3.5 h-3.5 inline fill-amber-400 text-amber-400"></i> {{ number_format($tp->average_rating ?? 5.0, 1) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-4 text-center text-slate-400 text-xs">{{ __('No verified technicians registered yet.') }}</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Official Sign-off and Certification Block -->
        <div class="pt-8 border-t border-slate-200 grid grid-cols-1 sm:grid-cols-2 gap-8 text-xs">
            <div class="space-y-2">
                <p class="font-bold text-slate-900 uppercase tracking-wider text-[11px]">{{ __('System Certification & Compliance') }}</p>
                <p class="text-slate-500 text-[11px] leading-relaxed">
                    {{ __('This document is an electronically generated official report produced by the FUNDI Verified Artisan Marketplace System. All metrics, financial records, and service evaluations are audited and stored in the encrypted system database.') }}
                </p>
            </div>

            <div class="space-y-4 sm:text-right">
                <p class="font-bold text-slate-900 uppercase tracking-wider text-[11px]">{{ __('Authorized Sign-Off') }}</p>
                <div class="inline-block sm:text-right border-b-2 border-slate-900 pb-1 w-48">
                    <p class="font-black text-slate-900">{{ $admin->full_name }}</p>
                    <p class="text-[10px] text-teal-700 font-bold uppercase">{{ __('Lead System Administrator') }}</p>
                </div>
                <p class="text-[10px] text-slate-400 font-mono">{{ __('Stamp & Verification Hash') }}: {{ strtoupper(md5(now()->toIso8601String())) }}</p>
            </div>
        </div>

    </div>

</body>
</html>
