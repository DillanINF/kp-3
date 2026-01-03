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
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($history as $row)
                            <tr>
                                <td class="px-4 py-3 text-slate-600">{{ $row->created_at?->timezone(config('app.timezone'))->format('d/m/Y H:i:s') }}</td>
                                <td class="px-4 py-3 text-slate-700">{{ $row->supplier?->name }}</td>
                                <td class="px-4 py-3 text-slate-700">{{ $row->item?->name }}</td>
                                <td class="px-4 py-3 text-slate-700">{{ $row->qty }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-6 text-center text-sm text-slate-500">Belum ada data barang masuk.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
@endsection
