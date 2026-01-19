@extends('layouts.app')

@section('title', 'Data Barang Masuk')
@section('page_title', 'Data Barang Masuk')
@section('page_description')Pencatatan barang masuk (sumber: supplier).@endsection

@section('content')
    <div class="rounded-xl border border-slate-200 bg-white">
            <div class="border-b border-slate-200 p-4">
                <div class="text-sm font-semibold text-slate-900">Riwayat Barang Masuk</div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-600">
                        <tr>
                            <th class="px-4 py-3">Tanggal</th>
                            <th class="px-4 py-3">Supplier</th>
                            <th class="px-4 py-3">Barang</th>
                            <th class="px-4 py-3">Qty</th>
                            <th class="px-4 py-3 text-right">Total Harga</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($history as $row)
                            @php
                                $supplierId = (int) ($row->supplier_id ?? 0);
                                $itemId = (int) ($row->item_id ?? 0);
                                $price = (int) (($supplierPriceMap[$supplierId][$itemId] ?? 0));
                                $subtotal = (int) ($price * (int) ($row->qty ?? 0));
                            @endphp
                            <tr>
                                <td class="px-4 py-3 text-slate-600">{{ $row->created_at?->timezone(config('app.timezone'))->format('d/m/Y H:i:s') }}</td>
                                <td class="px-4 py-3 text-slate-700">{{ $row->supplier?->name }}</td>
                                <td class="px-4 py-3 text-slate-700">{{ $row->item?->name }}</td>
                                <td class="px-4 py-3 text-slate-700">{{ $row->qty }}</td>
                                <td class="px-4 py-3 text-right font-semibold text-slate-900">Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-6 text-center text-sm text-slate-500">Belum ada data barang masuk.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($history->count() > 0)
                        <tfoot class="bg-slate-50">
                            <tr>
                                <td colspan="4" class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-600">Subtotal</td>
                                <td class="px-4 py-3 text-right text-sm font-semibold text-slate-900">Rp {{ number_format((int) ($historyGrandTotal ?? 0), 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
@endsection
