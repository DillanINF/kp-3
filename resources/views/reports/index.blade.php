@extends('layouts.app')

@section('title', 'Laporan')
@section('page_title', 'Laporan')
@section('page_description')Buat dan lihat ringkasan laporan.@endsection

@section('content')
    <div class="grid grid-cols-1 gap-4 xl:grid-cols-3">
        <div class="rounded-xl border border-slate-200 bg-white p-4 xl:col-span-3">
            <div class="text-sm font-semibold text-slate-900">Laporan Keuntungan & Kerugian</div>
            <div class="mt-1 text-sm text-slate-600">Sumber: barang keluar (penjualan + rusak/expired).</div>

            <form method="GET" class="mt-4 grid grid-cols-1 gap-3 md:grid-cols-3">
                <div class="space-y-1">
                    <label class="text-sm font-medium text-slate-700">Dari</label>
                    <input name="from" type="date" value="{{ ($fromDate ?? now()->startOfMonth())->toDateString() }}" class="h-11 w-full rounded-md border border-slate-200 bg-white px-3 text-sm text-slate-700" />
                </div>
                <div class="space-y-1">
                    <label class="text-sm font-medium text-slate-700">Sampai</label>
                    <input name="to" type="date" value="{{ ($toDate ?? now())->toDateString() }}" class="h-11 w-full rounded-md border border-slate-200 bg-white px-3 text-sm text-slate-700" />
                </div>
                <div class="flex items-end">
                    <button type="submit" class="inline-flex h-11 w-full items-center justify-center rounded-md bg-slate-900 px-4 text-sm font-semibold text-white hover:bg-slate-800">Terapkan</button>
                </div>
            </form>

            <div class="mt-5 grid grid-cols-1 gap-3 md:grid-cols-4">
                <div class="rounded-lg border border-slate-200 bg-white p-4">
                    <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Omzet Penjualan</div>
                    <div class="mt-1 text-lg font-semibold text-slate-900">Rp {{ number_format($salesRevenue ?? 0, 0, ',', '.') }}</div>
                </div>
                <div class="rounded-lg border border-slate-200 bg-white p-4">
                    <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">HPP Penjualan</div>
                    <div class="mt-1 text-lg font-semibold text-slate-900">Rp {{ number_format($salesCogs ?? 0, 0, ',', '.') }}</div>
                </div>
                <div class="rounded-lg border border-slate-200 bg-white p-4">
                    <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Keuntungan (Sale)</div>
                    <div class="mt-1 text-lg font-semibold text-slate-900">Rp {{ number_format($salesProfit ?? 0, 0, ',', '.') }}</div>
                </div>
                <div class="rounded-lg border border-slate-200 bg-white p-4">
                    <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Kerugian (Rusak/Expired)</div>
                    <div class="mt-1 text-lg font-semibold text-slate-900">Rp {{ number_format($lossDamagedExpired ?? 0, 0, ',', '.') }}</div>
                </div>
            </div>

            <div class="mt-3 rounded-lg border border-slate-200 bg-white p-4">
                <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Net</div>
                <div class="mt-1 text-lg font-semibold text-slate-900">Rp {{ number_format($net ?? 0, 0, ',', '.') }}</div>
            </div>

            <div class="mt-6 overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-600">
                        <tr>
                            <th class="px-4 py-3">Tanggal</th>
                            <th class="px-4 py-3">Tipe</th>
                            <th class="px-4 py-3">Customer</th>
                            <th class="px-4 py-3">Barang</th>
                            <th class="px-4 py-3 text-right">Qty</th>
                            <th class="px-4 py-3 text-right">Harga Beli</th>
                            <th class="px-4 py-3 text-right">Harga Jual</th>
                            <th class="px-4 py-3 text-right">Profit/Loss</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse(($rows ?? []) as $r)
                            @php
                                $qty = (int) $r->qty;
                                $buy = (int) $r->buy_price;
                                $sell = (int) $r->sell_price;
                                $profitLoss = 0;
                                if ($r->type === 'sale') {
                                    $profitLoss = ($sell - $buy) * $qty;
                                } else {
                                    $profitLoss = 0 - ($buy * $qty);
                                }
                            @endphp
                            <tr>
                                <td class="px-4 py-3 text-slate-600">{{ $r->created_at?->timezone(config('app.timezone'))->format('d/m/Y H:i:s') }}</td>
                                <td class="px-4 py-3 text-slate-700">{{ strtoupper($r->type ?? '-') }}</td>
                                <td class="px-4 py-3 text-slate-700">{{ $r->customer?->name ?? '-' }}</td>
                                <td class="px-4 py-3 text-slate-700">{{ $r->item?->name ?? '-' }}</td>
                                <td class="px-4 py-3 text-right text-slate-700">{{ $qty }}</td>
                                <td class="px-4 py-3 text-right text-slate-700">Rp {{ number_format($buy, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-right text-slate-700">Rp {{ number_format($sell, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-right text-slate-900">Rp {{ number_format($profitLoss, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-6 text-center text-sm text-slate-500">Belum ada data.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
