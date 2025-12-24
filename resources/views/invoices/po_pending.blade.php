@extends('layouts.app')

@section('title', 'PO Belum Terkirim')
@section('page_title', 'PO Belum Terkirim')
@section('page_description')Daftar purchase order yang belum dilakukan pengiriman.@endsection

@section('content')
    <div class="rounded-xl border border-slate-200 bg-white">
        <div class="flex items-center justify-between border-b border-slate-200 p-4">
            <div class="text-sm font-semibold text-slate-900">PO Belum Terkirim</div>
            <button type="button" class="inline-flex items-center gap-2 rounded-md border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Export</button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-600">
                    <tr>
                        <th class="px-4 py-3">No PO</th>
                        <th class="px-4 py-3">Supplier</th>
                        <th class="px-4 py-3">Tanggal</th>
                        <th class="px-4 py-3">Estimasi</th>
                        <th class="px-4 py-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    <tr>
                        <td class="px-4 py-3 font-medium text-slate-900">PO-005</td>
                        <td class="px-4 py-3 text-slate-700">Supplier A</td>
                        <td class="px-4 py-3 text-slate-600">2025-12-21</td>
                        <td class="px-4 py-3 text-slate-700">2025-12-26</td>
                        <td class="px-4 py-3"><span class="inline-flex rounded-full bg-amber-50 px-2 py-1 text-xs font-medium text-amber-700">Pending</span></td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 font-medium text-slate-900">PO-006</td>
                        <td class="px-4 py-3 text-slate-700">Supplier B</td>
                        <td class="px-4 py-3 text-slate-600">2025-12-22</td>
                        <td class="px-4 py-3 text-slate-700">2025-12-27</td>
                        <td class="px-4 py-3"><span class="inline-flex rounded-full bg-amber-50 px-2 py-1 text-xs font-medium text-amber-700">Pending</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection
