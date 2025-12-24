@extends('layouts.app')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')
@section('page_description')Ringkasan kinerja dan aktivitas terbaru.@endsection

@section('content')
    <div data-dashboard class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-xl border border-slate-200 bg-white p-4">
            <div class="text-sm text-slate-500">Invoice bulan ini</div>
            <div class="mt-2 text-2xl font-semibold text-slate-900" data-kpi="invoice-this-month">0</div>
            <div class="mt-1 text-xs text-slate-500">Dari data invoice</div>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4">
            <div class="text-sm text-slate-500">Jumlah customer</div>
            <div class="mt-2 text-2xl font-semibold text-slate-900" data-kpi="customers">{{ $customersCount ?? 0 }}</div>
            <div class="mt-1 text-xs text-slate-500">Terdaftar</div>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4">
            <div class="text-sm text-slate-500">Jumlah supplier</div>
            <div class="mt-2 text-2xl font-semibold text-slate-900" data-kpi="suppliers">0</div>
            <div class="mt-1 text-xs text-slate-500">Terdaftar</div>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4">
            <div class="text-sm text-slate-500">Jumlah jenis barang</div>
            <div class="mt-2 text-2xl font-semibold text-slate-900" data-kpi="items">0</div>
            <div class="mt-1 text-xs text-slate-500">Master barang</div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-4">
            <div class="text-sm text-slate-500">PO belum terkirim</div>
            <div class="mt-2 text-2xl font-semibold text-slate-900" data-kpi="po-pending">0</div>
            <div class="mt-1 text-xs text-slate-500">Perlu tindak lanjut</div>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4">
            <div class="text-sm text-slate-500">Barang keluar (1 bulan terakhir)</div>
            <div class="mt-2 text-2xl font-semibold text-slate-900" data-kpi="goods-out">0</div>
            <div class="mt-1 text-xs text-slate-500">Ke customer</div>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4">
            <div class="text-sm text-slate-500">Barang masuk (1 bulan terakhir)</div>
            <div class="mt-2 text-2xl font-semibold text-slate-900" data-kpi="goods-in">0</div>
            <div class="mt-1 text-xs text-slate-500">Dari supplier</div>
        </div>
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
                        <tr>
                            <td class="px-4 py-3 text-slate-600">2025-12-23</td>
                            <td class="px-4 py-3">Invoice INV-001 dibuat</td>
                            <td class="px-4 py-3"><span class="inline-flex rounded-full bg-emerald-50 px-2 py-1 text-xs font-medium text-emerald-700">Selesai</span></td>
                        </tr>
                        <tr>
                            <td class="px-4 py-3 text-slate-600">2025-12-22</td>
                            <td class="px-4 py-3">Barang masuk dari Supplier A</td>
                            <td class="px-4 py-3"><span class="inline-flex rounded-full bg-slate-100 px-2 py-1 text-xs font-medium text-slate-700">Tercatat</span></td>
                        </tr>
                        <tr>
                            <td class="px-4 py-3 text-slate-600">2025-12-21</td>
                            <td class="px-4 py-3">PO PO-005 masih menunggu pengiriman</td>
                            <td class="px-4 py-3"><span class="inline-flex rounded-full bg-amber-50 px-2 py-1 text-xs font-medium text-amber-700">Pending</span></td>
                        </tr>
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
