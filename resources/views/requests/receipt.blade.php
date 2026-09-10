<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt #{{ $request->reference_no }} — FUNDI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; }
            .receipt-box { box-shadow: none !important; border: 1px solid #e2e8f0 !important; }
        }
    </style>
</head>
@php
    $backUrl = route('client.requests.show', $request->id);
    if (auth()->check()) {
        if (auth()->user()->role === 'technician') {
            $backUrl = route('technician.requests.show', $request->id);
        } elseif (auth()->user()->role === 'admin') {
            $backUrl = route('admin.requests.show', $request->id);
        }
    }
@endphp
<body class="bg-slate-100 min-h-screen py-10 px-4 sm:px-6">

    <div class="max-w-2xl mx-auto space-y-4">
        
        <!-- Print & Back Navigation (Hidden during print) -->
        <div class="flex items-center justify-between no-print">
            <a href="{{ $backUrl }}" onclick="if (window.history.length > 1) { window.history.back(); return false; }" class="text-xs font-bold text-slate-700 hover:text-slate-950 flex items-center space-x-1.5 transition px-3.5 py-2 rounded-xl bg-white border border-slate-200 shadow-xs hover:bg-slate-50">
                <span class="text-sm">&larr;</span>
                <span>{{ __('Back to Request') }}</span>
            </a>
            <button onclick="window.print()" class="px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl shadow-sm transition flex items-center space-x-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                <span>{{ __('Print / Save Receipt') }}</span>
            </button>
        </div>

        <!-- Receipt Document Card -->
        <div class="receipt-box bg-white rounded-3xl p-8 sm:p-12 shadow-xl border border-slate-200 space-y-8 relative overflow-hidden">
            
            <!-- Header -->
            <div class="flex items-start justify-between border-b border-slate-100 pb-6">
                <div class="space-y-1">
                    <div class="inline-flex items-center space-x-2">
                        <div class="w-8 h-8 rounded-xl bg-slate-950 text-teal-400 font-black flex items-center justify-center text-sm">
                            F
                        </div>
                        <span class="text-xl font-black tracking-tight text-slate-950">FUNDI</span>
                    </div>
                    <p class="text-xs text-slate-400 font-medium">Find. Connect. Fix. • Verified Service Marketplace</p>
                    <p class="text-[11px] text-slate-400">Dar es Salaam, Tanzania</p>
                </div>

                <div class="text-right space-y-1">
                    <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase bg-emerald-100 text-emerald-800 border border-emerald-200">
                        Job Completed ✓
                    </span>
                    <p class="text-xs font-mono font-bold text-slate-900 mt-1">{{ $request->reference_no }}</p>
                    <p class="text-[11px] text-slate-400">{{ now()->format('d M Y, H:i') }}</p>
                </div>
            </div>

            <!-- Job Summary & Participants -->
            <div class="grid grid-cols-2 gap-6 text-xs">
                <div class="space-y-1">
                    <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Client Information</p>
                    <p class="font-bold text-slate-900 text-sm">{{ $request->client->full_name }}</p>
                    <p class="text-slate-500">{{ $request->location }}</p>
                </div>

                <div class="space-y-1 text-right sm:text-left">
                    <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Verified Technician</p>
                    <p class="font-bold text-slate-900 text-sm">{{ $request->technician->full_name }}</p>
                    <p class="text-slate-500">{{ $request->technician->technicianProfile->professional_title ?? 'Master Technician' }}</p>
                    <p class="text-teal-700 font-medium">Trade: {{ $request->service->name }}</p>
                </div>
            </div>

            <!-- Itemized Cost Agreement Breakdown -->
            @php $q = $request->latestQuotation; @endphp
            <div class="space-y-3">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-900">Agreed Service Breakdown</p>
                
                <div class="bg-slate-50 rounded-2xl p-5 border border-slate-200 space-y-3 text-xs">
                    <div class="flex justify-between text-slate-700">
                        <span>Labour / Technical Work</span>
                        <span class="font-bold text-slate-900">TZS {{ number_format($q ? $q->labour_cost : 0, 0) }}</span>
                    </div>
                    <div class="flex justify-between text-slate-700">
                        <span>Materials & Replacement Parts</span>
                        <span class="font-bold text-slate-900">TZS {{ number_format($q ? $q->materials_cost : 0, 0) }}</span>
                    </div>
                    <div class="flex justify-between text-slate-700">
                        <span>Transport & Logistics</span>
                        <span class="font-bold text-slate-900">TZS {{ number_format($q ? $q->transport_cost : 0, 0) }}</span>
                    </div>
                    @if($q && $q->discount > 0)
                    <div class="flex justify-between text-emerald-700 font-bold">
                        <span>Discount Applied</span>
                        <span>- TZS {{ number_format($q->discount, 0) }}</span>
                    </div>
                    @endif
                    <div class="pt-3 border-t border-slate-200 flex justify-between items-center text-sm font-black text-slate-950">
                        <span>TOTAL AGREED AMOUNT</span>
                        <span class="text-base text-slate-950">{{ $q ? $q->formatted_total : 'TZS 0' }}</span>
                    </div>
                </div>
            </div>

            <!-- Service & Settlement Notice -->
            <div class="p-4 rounded-2xl bg-teal-50/60 border border-teal-100 text-[11px] text-teal-950 leading-relaxed space-y-1">
                <p class="font-bold">✓ Service Completed & Verified by Client</p>
                <p class="text-teal-800">
                    Payment for this service was settled directly between Client and Technician via cash / mobile money as agreed. FUNDI verifies technician credentials, quotes, and job completion records.
                </p>
            </div>

            <!-- Footer Signature -->
            <div class="pt-6 border-t border-slate-100 flex items-center justify-between text-[10px] text-slate-400">
                <span>Official FUNDI Verification Document</span>
                <span>Security Hash: {{ strtoupper(substr(md5($request->reference_no . $request->id), 0, 12)) }}</span>
            </div>

        </div>

    </div>

</body>
</html>
