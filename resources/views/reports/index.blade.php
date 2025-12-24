@extends('layouts.app')

@section('title', 'Laporan')
@section('page_title', 'Laporan')
@section('page_description')Buat dan lihat ringkasan laporan.@endsection

@section('content')
    <div class="grid grid-cols-1 gap-4 xl:grid-cols-3">
        <div class="rounded-xl border border-slate-200 bg-white p-4 xl:col-span-2">
            <div class="text-sm font-semibold text-slate-900">Laporan</div>
            <div class="mt-1 text-sm text-slate-600">Pilih tipe laporan dan rentang tanggal.</div>

            <form class="mt-4 grid grid-cols-1 gap-3 md:grid-cols-3">
                <div class="space-y-1">
                    <label class="text-sm font-medium text-slate-700">Tipe</label>
                    <select class="h-11 w-full rounded-md border border-slate-200 bg-white px-3 text-sm text-slate-700">
                        <option>Invoice</option>
                        <option>Barang Masuk</option>
                        <option>Barang Keluar</option>
                    </select>
                </div>
                <div class="space-y-1">
                    <label class="text-sm font-medium text-slate-700">Dari</label>
                    <input type="date" class="h-11 w-full rounded-md border border-slate-200 bg-white px-3 text-sm text-slate-700" />
                </div>
                <div class="space-y-1">
                    <label class="text-sm font-medium text-slate-700">Sampai</label>
                    <input type="date" class="h-11 w-full rounded-md border border-slate-200 bg-white px-3 text-sm text-slate-700" />
                </div>

                <div class="md:col-span-3">
                    <button type="button" class="inline-flex h-11 items-center justify-center rounded-md bg-slate-900 px-4 text-sm font-semibold text-white hover:bg-slate-800">Generate</button>
                </div>
            </form>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-4">
            <div class="text-sm font-semibold text-slate-900">Export</div>
            <div class="mt-2 space-y-2">
                <button type="button" class="w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Download PDF</button>
                <button type="button" class="w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Download Excel</button>
            </div>
        </div>
    </div>
@endsection
