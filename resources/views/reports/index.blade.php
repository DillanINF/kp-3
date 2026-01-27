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

    <div class="space-y-4">
        <div class="rounded-xl border border-slate-200 bg-white p-4">
            <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
                <div>
                    <div class="text-sm font-semibold text-slate-900 text-center">LAPORAN KEUNTUNGAN & KERUGIAN</div>
                    <div class="mt-1 text-sm text-slate-600">Sumber: barang keluar (penjualan + rusak/expired).</div>
                </div>

                <form method="GET" class="flex flex-col gap-2 sm:flex-row sm:items-end">
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-700">Tahun</label>
                        <select name="year" class="h-10 w-full rounded-md border border-slate-200 bg-white px-3 text-sm text-slate-700 outline-none sm:w-32" onchange="this.form.submit()">
                            @foreach(($years ?? []) as $y)
                                <option value="{{ $y }}" {{ (int) $y === $yearValue ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>
                    <a href="{{ route('reports.index') }}" class="inline-flex h-10 items-center justify-center rounded-md border border-slate-200 bg-white px-3 text-sm font-semibold text-slate-700 hover:bg-slate-50">Reset</a>
                </form>
            </div>

            <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-6">
                <div class="rounded-xl border border-slate-200 bg-white p-4">
                    <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Pendapatan</div>
                    <div class="mt-1 text-lg font-semibold text-slate-900" data-report-card="salesRevenue" data-report-value="{{ (int) ($t['salesRevenue'] ?? 0) }}">Rp {{ number_format((int) ($t['salesRevenue'] ?? 0), 0, ',', '.') }}</div>
                </div>
                <div class="rounded-xl border border-slate-200 bg-white p-4">
                    <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Modal</div>
                    <div class="mt-1 text-lg font-semibold text-slate-900" data-report-card="salesCogs" data-report-value="{{ (int) ($t['salesCogs'] ?? 0) }}">Rp {{ number_format((int) ($t['salesCogs'] ?? 0), 0, ',', '.') }}</div>
                </div>
                <div class="rounded-xl border border-slate-200 bg-white p-4">
                    <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Profit</div>
                    <div class="mt-1 text-lg font-semibold text-slate-900" data-report-card="salesProfit" data-report-value="{{ (int) ($t['salesProfit'] ?? 0) }}">Rp {{ number_format((int) ($t['salesProfit'] ?? 0), 0, ',', '.') }}</div>
                </div>
                <div class="rounded-xl border border-slate-200 bg-white p-4">
                    <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Kerugian Rusak</div>
                    <div class="mt-1 text-lg font-semibold text-slate-900" data-report-card="lossDamaged" data-report-value="{{ (int) ($t['lossDamaged'] ?? 0) }}">Rp {{ number_format((int) ($t['lossDamaged'] ?? 0), 0, ',', '.') }}</div>
                </div>
                <div class="rounded-xl border border-slate-200 bg-white p-4">
                    <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Kerugian Expired</div>
                    <div class="mt-1 text-lg font-semibold text-slate-900" data-report-card="lossExpired" data-report-value="{{ (int) ($t['lossExpired'] ?? 0) }}">Rp {{ number_format((int) ($t['lossExpired'] ?? 0), 0, ',', '.') }}</div>
                </div>
                <div class="rounded-xl border border-slate-200 bg-white p-4">
                    <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Profit Bersih</div>
                    <div class="mt-1 text-lg font-semibold {{ $net < 0 ? 'text-rose-700' : 'text-emerald-700' }}" data-report-card="net" data-report-net data-report-value="{{ $net }}">Rp {{ number_format($net, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>

        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white">
            <div class="flex items-center justify-between bg-slate-50 px-4 py-3">
                <div class="text-sm font-semibold text-slate-900">Ringkasan per Bulan</div>
                <div class="text-xs font-medium text-slate-500">{{ $yearValue }}</div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-white text-xs font-semibold uppercase tracking-wide text-slate-600">
                        <tr>
                            <th class="px-4 py-3">Bulan</th>
                            <th class="px-4 py-3 text-right">Pendapatan</th>
                            <th class="px-4 py-3 text-right">Modal</th>
                            <th class="px-4 py-3 text-right">Profit</th>
                            <th class="px-4 py-3 text-right">Rusak</th>
                            <th class="px-4 py-3 text-right">Expired</th>
                            <th class="px-4 py-3 text-right">Bersih</th>
                            <th class="px-4 py-3 text-center">PDF</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse(($monthly ?? []) as $m)
                            @php
                                $rowNet = (int) ($m['net'] ?? 0);
                            @endphp
                            <tr class="hover:bg-slate-50" data-report-month-row data-month="{{ $m['month'] ?? '' }}" data-label="{{ $m['label'] ?? ($m['month'] ?? '-') }}" data-sales-revenue="{{ (int) ($m['salesRevenue'] ?? 0) }}" data-sales-cogs="{{ (int) ($m['salesCogs'] ?? 0) }}" data-sales-profit="{{ (int) ($m['salesProfit'] ?? 0) }}" data-loss-damaged="{{ (int) ($m['lossDamaged'] ?? 0) }}" data-loss-expired="{{ (int) ($m['lossExpired'] ?? 0) }}" data-net="{{ (int) ($m['net'] ?? 0) }}">
                                <td class="px-4 py-3 font-medium text-slate-700">{{ $m['label'] ?? ($m['month'] ?? '-') }}</td>
                                <td class="px-4 py-3 text-right text-slate-700">Rp {{ number_format((int) ($m['salesRevenue'] ?? 0), 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-right text-slate-700">Rp {{ number_format((int) ($m['salesCogs'] ?? 0), 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-right font-semibold text-slate-900">Rp {{ number_format((int) ($m['salesProfit'] ?? 0), 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-right text-slate-700">Rp {{ number_format((int) ($m['lossDamaged'] ?? 0), 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-right text-slate-700">Rp {{ number_format((int) ($m['lossExpired'] ?? 0), 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-right font-semibold {{ $rowNet < 0 ? 'text-rose-700' : 'text-emerald-700' }}">Rp {{ number_format($rowNet, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-center">
                                    <a href="{{ route('reports.pdf', ['month' => $m['month'] ?? '']) }}" class="inline-flex h-9 items-center justify-center rounded-md border border-slate-200 bg-white px-3 text-sm font-semibold text-slate-700 hover:bg-slate-50">PDF</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-10 text-center text-sm text-slate-500">Belum ada data.</td>
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
            const rows = Array.from(document.querySelectorAll('[data-report-month-row]'));
            if (!cards.length || !rows.length) return;

            const headerYearEl = document.querySelector('[data-report-header-year]');
            const yearlyValues = {};
            for (const el of cards) {
                yearlyValues[el.getAttribute('data-report-card')] = Number(el.getAttribute('data-report-value') || 0);
            }

            const fmt = new Intl.NumberFormat('id-ID');
            function setNetColor(val) {
                if (!netEl) return;
                netEl.classList.remove('text-rose-700', 'text-emerald-700');
                netEl.classList.add(val < 0 ? 'text-rose-700' : 'text-emerald-700');
            }

            function setCards(values) {
                for (const el of cards) {
                    const key = el.getAttribute('data-report-card');
                    const val = Number(values[key] ?? 0);
                    el.setAttribute('data-report-value', String(val));
                    el.textContent = 'Rp ' + fmt.format(val);
                }
                setNetColor(Number(values.net ?? 0));
            }

            let selected = null;

            function clearSelection() {
                for (const r of rows) {
                    r.classList.remove('bg-indigo-50');
                }
                selected = null;
            }

            for (const r of rows) {
                r.style.cursor = 'pointer';
                r.addEventListener('click', function () {
                    const month = r.getAttribute('data-month') || '';
                    if (selected === month) {
                        clearSelection();
                        setCards(yearlyValues);
                        return;
                    }

                    clearSelection();
                    r.classList.add('bg-indigo-50');
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
