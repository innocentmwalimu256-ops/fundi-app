<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Official Payment Receipt #{{ $request->reference_no }} — FUNDI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@500;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; padding: 0 !important; }
            .receipt-box { box-shadow: none !important; border: 1px solid #cbd5e1 !important; border-radius: 0 !important; }
        }
        .stamp-paid {
            border: 3px dashed #059669;
            color: #059669;
            transform: rotate(-12deg);
            text-shadow: 0 0 1px rgba(5, 150, 105, 0.2);
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
    $q = $request->latestQuotation;
    $totalAmount = $q ? $q->total_cost : 0;
    $receiptNo = 'REC-' . strtoupper(substr(md5($request->reference_no . $request->id), 0, 8));
    $settlementDate = $request->updated_at ? $request->updated_at->format('d M Y, H:i') : now()->format('d M Y, H:i');
@endphp
<body class="bg-slate-100 min-h-screen py-10 px-4 sm:px-6">

    <div class="max-w-2xl mx-auto space-y-4">
        
        <!-- Navigation & Actions (Hidden in Print) -->
        <div class="flex items-center justify-between no-print">
            <a href="{{ $backUrl }}" onclick="if (window.history.length > 1) { window.history.back(); return false; }" class="text-xs font-bold text-slate-700 hover:text-slate-950 flex items-center space-x-1.5 transition px-3.5 py-2 rounded-xl bg-white border border-slate-200 shadow-xs hover:bg-slate-50">
                <span class="text-sm">&larr;</span>
                <span>{{ __('Back to Request') }}</span>
            </a>
            <div class="flex items-center space-x-2">
                <button onclick="window.print()" class="px-5 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs rounded-xl shadow-sm transition flex items-center space-x-2 cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                    <span>{{ __('Print / Download PDF') }}</span>
                </button>
            </div>
        </div>

        <!-- Official Receipt Box -->
        <div class="receipt-box bg-white rounded-3xl p-8 sm:p-12 shadow-xl border border-slate-200 space-y-8 relative overflow-hidden">
            
            <!-- Watermark / Stamp -->
            <div class="absolute right-8 top-36 sm:right-16 sm:top-32 pointer-events-none opacity-85 z-10 select-none">
                <div class="stamp-paid px-6 py-2 rounded-2xl font-black text-center uppercase tracking-widest leading-tight">
                    <span class="text-xl sm:text-2xl block">PAID IN FULL</span>
                    <span class="text-[10px] font-bold block tracking-wider">IMELIPWA YOTE • VERIFIED</span>
                </div>
            </div>

            <!-- Header -->
            <div class="flex items-start justify-between border-b border-slate-200 pb-6">
                <div class="space-y-1">
                    <div class="inline-flex items-center space-x-2">
                        <div class="w-9 h-9 rounded-xl bg-slate-950 text-teal-400 font-black flex items-center justify-center text-base">
                            F
                        </div>
                        <div>
                            <span class="text-xl font-black tracking-tight text-slate-950">FUNDI</span>
                            <span class="text-[10px] font-bold bg-teal-50 text-teal-800 border border-teal-200 px-1.5 py-0.5 rounded ml-1.5 uppercase">Official Receipt</span>
                        </div>
                    </div>
                    <p class="text-xs text-slate-500 font-medium">Digital Service Marketplace • Verified Operations</p>
                    <p class="text-[11px] text-slate-400">United Republic of Tanzania</p>
                </div>

                <div class="text-right space-y-1">
                    <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase bg-emerald-100 text-emerald-800 border border-emerald-300 inline-flex items-center space-x-1">
                        <span>MALIPO YAMEKAMILIKA</span>
                    </span>
                    <p class="text-xs font-mono font-bold text-slate-900 mt-1">Receipt #: <span class="text-emerald-700 font-black">{{ $receiptNo }}</span></p>
                    <p class="text-[11px] font-mono text-slate-500">Order: {{ $request->reference_no }}</p>
                    <p class="text-[11px] text-slate-400">{{ $settlementDate }}</p>
                </div>
            </div>

            <!-- Participants Details -->
            <div class="grid grid-cols-2 gap-6 text-xs bg-slate-50/70 p-5 rounded-2xl border border-slate-100">
                <div class="space-y-1">
                    <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Billed To (Client)</p>
                    <p class="font-black text-slate-900 text-sm">{{ $request->client->full_name }}</p>
                    <p class="text-slate-600 font-mono text-[11px]">{{ $request->client->phone }}</p>
                    <p class="text-slate-500">{{ $request->location }}</p>
                </div>

                <div class="space-y-1 text-right sm:text-left">
                    <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Service Provider (Technician)</p>
                    <p class="font-black text-slate-900 text-sm">{{ $request->technician->full_name }}</p>
                    <p class="text-slate-600 font-mono text-[11px]">{{ $request->technician->phone }}</p>
                    <p class="text-teal-700 font-bold">Category: {{ $request->service->name }}</p>
                </div>
            </div>

            <!-- Itemized Financial Breakdown -->
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-900">Payment Breakdown / Mchanganuo wa Malipo</p>
                    <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">Payment Confirmed</span>
                </div>
                
                <div class="bg-white rounded-2xl border border-slate-200 divide-y divide-slate-100 text-xs overflow-hidden">
                    <div class="flex justify-between p-3.5 text-slate-700 hover:bg-slate-50">
                        <div>
                            <span class="font-bold text-slate-900 block">Labour / Technical Work (Gharama ya Ufundi)</span>
                            <span class="text-[11px] text-slate-400">Direct technical diagnostic & repair service</span>
                        </div>
                        <span class="font-bold text-slate-900 font-mono text-sm self-center">TZS {{ number_format($q ? $q->labour_cost : 0, 0) }}</span>
                    </div>

                    <div class="flex justify-between p-3.5 text-slate-700 hover:bg-slate-50">
                        <div>
                            <span class="font-bold text-slate-900 block">Materials & Replacement Parts (Vifaa vya Kazi)</span>
                            <span class="text-[11px] text-slate-400">Hardware, spare parts and supplies installed</span>
                        </div>
                        <span class="font-bold text-slate-900 font-mono text-sm self-center">TZS {{ number_format($q ? $q->materials_cost : 0, 0) }}</span>
                    </div>

                    <div class="flex justify-between p-3.5 text-slate-700 hover:bg-slate-50">
                        <div>
                            <span class="font-bold text-slate-900 block">Transport & Logistics (Usafiri wa Fundi)</span>
                            <span class="text-[11px] text-slate-400">On-site travel to {{ $request->location }}</span>
                        </div>
                        <span class="font-bold text-slate-900 font-mono text-sm self-center">TZS {{ number_format($q ? $q->transport_cost : 0, 0) }}</span>
                    </div>

                    @if($q && $q->discount > 0)
                    <div class="flex justify-between p-3.5 text-emerald-700 bg-emerald-50/40">
                        <span class="font-bold">Special Discount (Punguzo)</span>
                        <span class="font-bold font-mono">- TZS {{ number_format($q->discount, 0) }}</span>
                    </div>
                    @endif

                    <!-- Totals Table -->
                    <div class="p-4 bg-slate-50 space-y-2 border-t border-slate-200">
                        <div class="flex justify-between text-slate-600 text-xs">
                            <span>Total Invoiced Amount (Jumla ya Gharama):</span>
                            <span class="font-bold font-mono text-slate-900">TZS {{ number_format($totalAmount, 0) }}</span>
                        </div>
                        <div class="flex justify-between text-emerald-800 text-xs font-bold">
                            <span>Amount Paid by Client (Kiasi Kilicholipwa):</span>
                            <span class="font-mono text-emerald-700 font-black">TZS {{ number_format($totalAmount, 0) }}</span>
                        </div>
                        <div class="flex justify-between text-slate-900 text-sm font-black pt-2 border-t border-slate-200">
                            <span>BALANCE DUE / DENI LILILOBAKI:</span>
                            <span class="font-mono text-emerald-600">TZS 0.00 (PAID)</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Settlement & Audit Details -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 space-y-1">
                    <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Payment Status & Method</p>
                    <p class="font-bold text-emerald-700 flex items-center space-x-1">
                        <span>Direct Settlement • Paid in Full</span>
                    </p>
                    <p class="text-[11px] text-slate-500">Method: Cash / Mobile Money (Direct P2P)</p>
                </div>

                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 space-y-1">
                    <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Service Completion Verification</p>
                    <p class="font-bold text-slate-900">Confirmed & Signed by Client</p>
                    <p class="text-[11px] text-slate-500">Platform Commission: 0% Platform Free</p>
                </div>
            </div>

            <!-- Legal and Verification Notice -->
            <div class="p-4 rounded-2xl bg-emerald-50/70 border border-emerald-200 text-[11px] text-emerald-950 leading-relaxed space-y-1">
                <p class="font-black text-emerald-900 flex items-center space-x-1">
                    <span>Uthibitisho Rasmi wa Malipo na Ukamilifu wa Kazi</span>
                </p>
                <p class="text-emerald-800">
                    Risiti hii inathibitisha kuwa kazi ya ufundi imekaguliwa, kukubaliwa, na malipo ya makadirio ya jumla ya <strong>TZS {{ number_format($totalAmount, 0) }}</strong> yamekamilika kikamilifu kati ya Mteja na Fundi bila salio lolote linalodaiwa.
                </p>
            </div>

            <!-- Footer Signature & Cryptographic Verification -->
            <div class="pt-6 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between text-[10px] text-slate-400 gap-2 font-mono">
                <div>
                    <span>Official FUNDI Verification Document • </span>
                    <span class="text-slate-600 font-bold">Ref: {{ $request->reference_no }}</span>
                </div>
                <div>
                    <span>Security Hash: {{ strtoupper(substr(md5($request->reference_no . $request->id . 'PAID'), 0, 16)) }}</span>
                </div>
            </div>

        </div>

    </div>

</body>
</html>
