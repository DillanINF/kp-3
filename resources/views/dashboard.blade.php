@extends('layouts.app')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')
@section('page_description')Ringkasan kinerja dan aktivitas terbaru.@endsection

@section('content')
    <div data-dashboard class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
        <a href="{{ route('invoices.index') }}" class="rounded-xl border border-slate-200 bg-white p-4 hover:bg-slate-50">
            <div class="text-sm text-slate-500">Invoice</div>
            <div class="mt-2 text-2xl font-semibold text-slate-900">{{ $invoicesCount ?? 0 }}</div>
            <div class="mt-1 text-xs text-slate-500">Total data invoice</div>
        </a>
        <a href="{{ route('masters.items_supplier') }}" class="rounded-xl border border-slate-200 bg-white p-4 hover:bg-slate-50">
            <div class="text-sm text-slate-500">Pengeluaran</div>
            <div class="mt-2 text-2xl font-semibold text-slate-900">Rp {{ number_format($expenseTotal ?? 0, 0, ',', '.') }}</div>
            <div class="mt-1 text-xs text-slate-500">Total pemesanan supplier (accepted)</div>
        </a>
        <a href="{{ route('invoices.index') }}" class="rounded-xl border border-slate-200 bg-white p-4 hover:bg-slate-50">
            <div class="text-sm text-slate-500">Pendapatan</div>
            <div class="mt-2 text-2xl font-semibold text-slate-900">Rp {{ number_format($revenueTotal ?? 0, 0, ',', '.') }}</div>
            <div class="mt-1 text-xs text-slate-500">Total invoice posted</div>
        </a>
        <a href="{{ route('masters.items_out') }}" class="rounded-xl border border-slate-200 bg-white p-4 hover:bg-slate-50">
            <div class="text-sm text-slate-500">Keuntungan</div>
            <div class="mt-2 text-2xl font-semibold text-slate-900">Rp {{ number_format($profitTotal ?? 0, 0, ',', '.') }}</div>
            <div class="mt-1 text-xs text-slate-500">(Harga jual - harga beli) x qty terjual</div>
        </a>
        <a href="{{ route('masters.items') }}" class="rounded-xl border border-slate-200 bg-white p-4 hover:bg-slate-50">
            <div class="text-sm text-slate-500">Data barang</div>
            <div class="mt-2 text-2xl font-semibold text-slate-900">{{ $itemsCount ?? 0 }}</div>
            <div class="mt-1 text-xs text-slate-500">Jumlah jenis barang</div>
        </a>
        <a href="{{ route('masters.items_out') }}" class="rounded-xl border border-slate-200 bg-white p-4 hover:bg-slate-50">
            <div class="text-sm text-slate-500">Barang keluar</div>
            <div class="mt-2 text-2xl font-semibold text-slate-900">{{ $goodsOutTotalQty ?? 0 }}</div>
            <div class="mt-1 text-xs text-slate-500">Total qty keluar</div>
        </a>
        <a href="{{ route('masters.items_in') }}" class="rounded-xl border border-slate-200 bg-white p-4 hover:bg-slate-50">
            <div class="text-sm text-slate-500">Barang masuk</div>
            <div class="mt-2 text-2xl font-semibold text-slate-900">{{ $goodsInTotalQty ?? 0 }}</div>
            <div class="mt-1 text-xs text-slate-500">Total qty masuk</div>
        </a>
        <a href="{{ route('masters.customers') }}" class="rounded-xl border border-slate-200 bg-white p-4 hover:bg-slate-50">
            <div class="text-sm text-slate-500">Data customer</div>
            <div class="mt-2 text-2xl font-semibold text-slate-900">{{ $customersCount ?? 0 }}</div>
            <div class="mt-1 text-xs text-slate-500">Customer terdaftar</div>
        </a>
        <a href="{{ route('masters.suppliers') }}" class="rounded-xl border border-slate-200 bg-white p-4 hover:bg-slate-50">
            <div class="text-sm text-slate-500">Data supplier</div>
            <div class="mt-2 text-2xl font-semibold text-slate-900">{{ $suppliersCount ?? 0 }}</div>
            <div class="mt-1 text-xs text-slate-500">Supplier terdaftar</div>
        </a>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-4 xl:grid-cols-3">
        <div class="rounded-xl border border-slate-200 bg-white p-4 xl:col-span-2">
            <div class="flex items-center justify-between">
                <h2 class="text-sm font-semibold text-slate-900">Aktivitas terbaru</h2>
                <a href="{{ route('invoices.index') }}" class="text-sm font-medium text-slate-700 hover:text-slate-900">Lihat invoice</a>
            </div>

            <div class="mt-4 overflow-hidden rounded-lg border border-slate-200">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-600">
                        <tr>
                            <th class="px-4 py-3">Tanggal</th>
                            <th class="px-4 py-3">Aktivitas</th>
                            <th class="px-4 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white">
                        @forelse(($activities ?? []) as $a)
                            @php
                                $tone = (string) ($a['tone'] ?? 'neutral');
                                $badge = 'bg-slate-100 text-slate-700';
                                if ($tone === 'success') $badge = 'bg-emerald-50 text-emerald-700';
                                if ($tone === 'warning') $badge = 'bg-amber-50 text-amber-700';
                            @endphp
                            <tr>
                                <td class="px-4 py-3 text-slate-600">{{ ($a['at'] ?? null) ? $a['at']->timezone(config('app.timezone'))->format('d/m/Y H:i:s') : '-' }}</td>
                                <td class="px-4 py-3">
                                    @if(!empty($a['url']))
                                        <a href="{{ $a['url'] }}" class="text-slate-700 hover:text-slate-900 hover:underline">{{ $a['label'] ?? '-' }}</a>
                                    @else
                                        {{ $a['label'] ?? '-' }}
                                    @endif
                                </td>
                                <td class="px-4 py-3"><span class="inline-flex rounded-full px-2 py-1 text-xs font-medium {{ $badge }}">{{ $a['status'] ?? '-' }}</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-6 text-center text-sm text-slate-500">Belum ada aktivitas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-4">
            <h2 class="text-sm font-semibold text-slate-900">Quick actions</h2>
            <div class="mt-4 space-y-2">
                <a href="{{ route('invoices.index') }}" class="block rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Buka Data Invoice</a>
                <a href="{{ route('invoices.po_pending') }}" class="block rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Cek PO Belum Terkirim</a>
                <a href="{{ route('masters.items_in') }}" class="block rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Input Barang Masuk</a>
                <a href="{{ route('masters.items_out') }}" class="block rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Input Barang Keluar</a>
            </div>
        </div>
    </div>
@endsection
