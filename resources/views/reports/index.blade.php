@extends('layouts.app')

@section('title', 'Laporan')
@section('page_title', 'Laporan')
@section('page_description')Buat dan lihat ringkasan laporan.@endsection

@section('content')
    @php
        $t = $totals ?? [];
        $yearValue = (int) ($year ?? now()->year);
        $net = (int) ($t['net'] ?? 0);
    @endphp

    <div class="space-y-6">
        <!-- Header & Filter Section -->
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
            <div>
                <h2 class="text-xl font-bold text-slate-900 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-indigo-600"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    Laporan Keuntungan & Kerugian
                </h2>
                <p class="mt-1 text-sm text-slate-500 font-medium">Berdasarkan data barang keluar (penjualan + rusak/expired).</p>
            </div>

            <form method="GET" class="flex items-center gap-2">
                <div class="relative group">
                    <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none text-slate-400 group-focus-within:text-indigo-500 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    </div>
                    <select name="year" class="h-11 pl-10 pr-4 rounded-xl border border-slate-200 bg-slate-50 text-sm font-bold text-slate-700 outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/5 transition-all appearance-none cursor-pointer min-w-[120px]" onchange="this.form.submit()">
                        @foreach(($years ?? []) as $y)
                            <option value="{{ $y }}" {{ (int) $y === $yearValue ? 'selected' : '' }}>Tahun {{ $y }}</option>
                        @endforeach
                    </select>
                </div>
                <a href="{{ route('reports.index') }}" class="inline-flex h-11 items-center justify-center rounded-xl border border-slate-200 bg-white px-4 text-sm font-bold text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-all">Reset</a>
            </form>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6">
            <!-- Pendapatan -->
            <div class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-5 transition-all hover:shadow-lg hover:shadow-indigo-500/5 hover:-translate-y-1">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-slate-400">Pendapatan</p>
                        <h3 class="mt-2 text-lg font-extrabold text-slate-900" data-report-card="salesRevenue" data-report-value="{{ (int) ($t['salesRevenue'] ?? 0) }}">Rp {{ number_format((int) ($t['salesRevenue'] ?? 0), 0, ',', '.') }}</h3>
                    </div>
                    <div class="rounded-xl bg-indigo-50 p-2 text-indigo-600 group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 7 13.5 15.5 8.5 10.5 2 17"></polyline><polyline points="16 7 22 7 22 13"></polyline></svg>
                    </div>
                </div>
            </div>

            <!-- Modal -->
            <div class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-5 transition-all hover:shadow-lg hover:shadow-slate-500/5 hover:-translate-y-1">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-slate-400">Modal</p>
                        <h3 class="mt-2 text-lg font-extrabold text-slate-900" data-report-card="salesCogs" data-report-value="{{ (int) ($t['salesCogs'] ?? 0) }}">Rp {{ number_format((int) ($t['salesCogs'] ?? 0), 0, ',', '.') }}</h3>
                    </div>
                    <div class="rounded-xl bg-slate-50 p-2 text-slate-600 group-hover:bg-slate-600 group-hover:text-white transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 7h-9"/><path d="M14 17H5"/><circle cx="17" cy="17" r="3"/><circle cx="7" cy="7" r="3"/></svg>
                    </div>
                </div>
            </div>

            <!-- Profit -->
            <div class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-5 transition-all hover:shadow-lg hover:shadow-blue-500/5 hover:-translate-y-1">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-slate-400">Profit</p>
                        <h3 class="mt-2 text-lg font-extrabold text-slate-900" data-report-card="salesProfit" data-report-value="{{ (int) ($t['salesProfit'] ?? 0) }}">Rp {{ number_format((int) ($t['salesProfit'] ?? 0), 0, ',', '.') }}</h3>
                    </div>
                    <div class="rounded-xl bg-blue-50 p-2 text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" x2="12" y1="8" y2="12"/><line x1="12" x2="12.01" y1="16" y2="16"/></svg>
                    </div>
                </div>
            </div>

            <!-- Rusak -->
            <div class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-5 transition-all hover:shadow-lg hover:shadow-rose-500/5 hover:-translate-y-1">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-slate-400">Rusak</p>
                        <h3 class="mt-2 text-lg font-extrabold text-slate-900" data-report-card="lossDamaged" data-report-value="{{ (int) ($t['lossDamaged'] ?? 0) }}">Rp {{ number_format((int) ($t['lossDamaged'] ?? 0), 0, ',', '.') }}</h3>
                    </div>
                    <div class="rounded-xl bg-rose-50 p-2 text-rose-600 group-hover:bg-rose-600 group-hover:text-white transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><path d="M12 9v4"/><path d="M12 17h.01"/></svg>
                    </div>
                </div>
            </div>

            <!-- Expired -->
            <div class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-5 transition-all hover:shadow-lg hover:shadow-orange-500/5 hover:-translate-y-1">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-slate-400">Expired</p>
                        <h3 class="mt-2 text-lg font-extrabold text-slate-900" data-report-card="lossExpired" data-report-value="{{ (int) ($t['lossExpired'] ?? 0) }}">Rp {{ number_format((int) ($t['lossExpired'] ?? 0), 0, ',', '.') }}</h3>
                    </div>
                    <div class="rounded-xl bg-orange-50 p-2 text-orange-600 group-hover:bg-orange-600 group-hover:text-white transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    </div>
                </div>
            </div>

            <!-- Bersih -->
            @php $isPositive = $net >= 0; @endphp
            <div class="group relative overflow-hidden rounded-2xl border-2 {{ $isPositive ? 'border-emerald-500 bg-emerald-50/30' : 'border-rose-500 bg-rose-50/30' }} p-5 shadow-lg {{ $isPositive ? 'shadow-emerald-500/10' : 'shadow-rose-500/10' }} transition-all hover:-translate-y-1">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest {{ $isPositive ? 'text-emerald-600' : 'text-rose-600' }}">Profit Bersih</p>
                        <h3 class="mt-2 text-lg font-extrabold {{ $isPositive ? 'text-emerald-700' : 'text-rose-700' }}" data-report-card="net" data-report-net data-report-value="{{ $net }}">Rp {{ number_format($net, 0, ',', '.') }}</h3>
                    </div>
                    <div class="rounded-xl {{ $isPositive ? 'bg-emerald-500 text-white' : 'bg-rose-500 text-white' }} p-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table Monthly -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="flex items-center justify-between border-b border-slate-100 bg-slate-50/50 px-6 py-4">
                <h3 class="font-bold text-slate-800 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-slate-500"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="3" y1="10" x2="21" y2="10"/><line x1="7" y1="2" x2="7" y2="6"/><line x1="17" y1="2" x2="17" y2="6"/></svg>
                    Ringkasan Bulanan
                </h3>
                <span class="px-3 py-1 rounded-full bg-indigo-50 text-xs font-bold text-indigo-600">Tahun {{ $yearValue }}</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50/50 text-[10px] font-bold uppercase tracking-widest text-slate-500">
                        <tr>
                            <th class="px-6 py-4">Bulan</th>
                            <th class="px-6 py-4 text-right">Pendapatan</th>
                            <th class="px-6 py-4 text-right">Modal</th>
                            <th class="px-6 py-4 text-right">Profit</th>
                            <th class="px-6 py-4 text-right">Rusak</th>
                            <th class="px-6 py-4 text-right">Expired</th>
                            <th class="px-6 py-4 text-right">Bersih</th>
                            <th class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse(($monthly ?? []) as $m)
                            @php $rowNet = (int) ($m['net'] ?? 0); @endphp
                            <tr class="hover:bg-slate-50 transition-colors cursor-pointer group" 
                                data-report-month-row 
                                data-month="{{ $m['month'] ?? '' }}" 
                                data-label="{{ $m['label'] ?? ($m['month'] ?? '-') }}" 
                                data-sales-revenue="{{ (int) ($m['salesRevenue'] ?? 0) }}" 
                                data-sales-cogs="{{ (int) ($m['salesCogs'] ?? 0) }}" 
                                data-sales-profit="{{ (int) ($m['salesProfit'] ?? 0) }}" 
                                data-loss-damaged="{{ (int) ($m['lossDamaged'] ?? 0) }}" 
                                data-loss-expired="{{ (int) ($m['lossExpired'] ?? 0) }}" 
                                data-net="{{ (int) ($m['net'] ?? 0) }}">
                                <td class="px-6 py-4 font-bold text-slate-700 group-hover:text-indigo-600 transition-colors">{{ $m['label'] ?? ($m['month'] ?? '-') }}</td>
                                <td class="px-6 py-4 text-right font-medium text-slate-600 italic">Rp {{ number_format((int) ($m['salesRevenue'] ?? 0), 0, ',', '.') }}</td>
                                <td class="px-6 py-4 text-right font-medium text-slate-600 italic">Rp {{ number_format((int) ($m['salesCogs'] ?? 0), 0, ',', '.') }}</td>
                                <td class="px-6 py-4 text-right font-bold text-slate-900">Rp {{ number_format((int) ($m['salesProfit'] ?? 0), 0, ',', '.') }}</td>
                                <td class="px-6 py-4 text-right font-medium text-rose-500">Rp {{ number_format((int) ($m['lossDamaged'] ?? 0), 0, ',', '.') }}</td>
                                <td class="px-6 py-4 text-right font-medium text-orange-500">Rp {{ number_format((int) ($m['lossExpired'] ?? 0), 0, ',', '.') }}</td>
                                <td class="px-6 py-4 text-right font-extrabold {{ $rowNet < 0 ? 'text-rose-700' : 'text-emerald-700' }}">Rp {{ number_format($rowNet, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 text-center">
                                    <a href="{{ route('reports.pdf', ['month' => $m['month'] ?? '']) }}" class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-900 hover:text-white hover:border-slate-900 transition-all shadow-sm" title="Download PDF">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/></svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center gap-2">
                                        <div class="h-12 w-12 rounded-2xl bg-slate-50 flex items-center justify-center text-slate-300">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/><line x1="16" x2="8" y1="13" y2="13"/><line x1="16" x2="8" y1="17" y2="17"/><line x1="10" x2="8" y1="9" y2="9"/></svg>
                                        </div>
                                        <p class="text-sm font-medium text-slate-400">Belum ada data laporan untuk tahun ini.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        (function () {
            const cards = Array.from(document.querySelectorAll('[data-report-card]'));
            const netEl = document.querySelector('[data-report-net]');
            const netCard = netEl?.closest('.group');
            const rows = Array.from(document.querySelectorAll('[data-report-month-row]'));
            if (!cards.length || !rows.length) return;

            const yearlyValues = {};
            for (const el of cards) {
                yearlyValues[el.getAttribute('data-report-card')] = Number(el.getAttribute('data-report-value') || 0);
            }

            const fmt = new Intl.NumberFormat('id-ID');
            function setNetStyle(val) {
                if (!netEl || !netCard) return;
                netEl.classList.remove('text-rose-700', 'text-emerald-700');
                netCard.classList.remove('border-rose-500', 'bg-rose-50/30', 'shadow-rose-500/10', 'border-emerald-500', 'bg-emerald-50/30', 'shadow-emerald-500/10');
                
                const label = netCard.querySelector('p');
                const iconBg = netCard.querySelector('.rounded-xl:last-child');
                label.classList.remove('text-rose-600', 'text-emerald-600');
                iconBg.classList.remove('bg-rose-500', 'bg-emerald-500');

                if (val < 0) {
                    netEl.classList.add('text-rose-700');
                    netCard.classList.add('border-rose-500', 'bg-rose-50/30', 'shadow-rose-500/10');
                    label.classList.add('text-rose-600');
                    iconBg.classList.add('bg-rose-500');
                } else {
                    netEl.classList.add('text-emerald-700');
                    netCard.classList.add('border-emerald-500', 'bg-emerald-50/30', 'shadow-emerald-500/10');
                    label.classList.add('text-emerald-600');
                    iconBg.classList.add('bg-emerald-500');
                }
            }

            function setCards(values) {
                for (const el of cards) {
                    const key = el.getAttribute('data-report-card');
                    const val = Number(values[key] ?? 0);
                    el.setAttribute('data-report-value', String(val));
                    el.textContent = 'Rp ' + fmt.format(val);
                }
                setNetStyle(Number(values.net ?? 0));
            }

            let selected = null;

            function clearSelection() {
                for (const r of rows) {
                    r.classList.remove('bg-indigo-50/50', 'ring-1', 'ring-inset', 'ring-indigo-100');
                }
                selected = null;
            }

            for (const r of rows) {
                r.addEventListener('click', function () {
                    const month = r.getAttribute('data-month') || '';
                    if (selected === month) {
                        clearSelection();
                        setCards(yearlyValues);
                        return;
                    }

                    clearSelection();
                    r.classList.add('bg-indigo-50/50', 'ring-1', 'ring-inset', 'ring-indigo-100');
                    selected = month;

                    setCards({
                        salesRevenue: Number(r.getAttribute('data-sales-revenue') || 0),
                        salesCogs: Number(r.getAttribute('data-sales-cogs') || 0),
                        salesProfit: Number(r.getAttribute('data-sales-profit') || 0),
                        lossDamaged: Number(r.getAttribute('data-loss-damaged') || 0),
                        lossExpired: Number(r.getAttribute('data-loss-expired') || 0),
                        net: Number(r.getAttribute('data-net') || 0),
                    });
                });
            }
        })();
    </script>
@endsection
