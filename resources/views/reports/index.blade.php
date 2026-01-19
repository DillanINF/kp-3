@extends('layouts.app')

@section('title', 'Laporan')
@section('page_title', 'Laporan')
@section('page_description')Buat dan lihat ringkasan laporan.@endsection

@section('content')
    <div class="grid grid-cols-1 gap-4 xl:grid-cols-3">
        <div class="rounded-xl border border-slate-200 bg-white p-4 xl:col-span-3">
            <div class="text-sm font-semibold text-slate-900">Laporan Keuntungan & Kerugian</div>
            <div class="mt-1 text-sm text-slate-600">Sumber: barang keluar (penjualan + rusak/expired).</div>

            <form method="GET" class="mt-4 grid grid-cols-1 gap-3 md:grid-cols-4">
                <div class="space-y-1 md:col-span-2">
                    <label class="text-sm font-medium text-slate-700">Tahun</label>
                    <input name="year" type="number" min="2000" max="2100" value="{{ (int) ($year ?? now()->year) }}" class="h-11 w-full rounded-md border border-slate-200 bg-white px-3 text-sm text-slate-700" />
                </div>
                <div class="flex items-end md:col-span-1">
                    <button type="submit" class="inline-flex h-11 w-full items-center justify-center rounded-md bg-slate-900 px-4 text-sm font-semibold text-white hover:bg-slate-800">Terapkan</button>
                </div>
                <div class="flex items-end md:col-span-1">
                    <a href="{{ route('reports.index') }}" class="inline-flex h-11 w-full items-center justify-center rounded-md border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-700 hover:bg-slate-50">Reset</a>
                </div>
            </form>

            <div class="mt-6 overflow-hidden rounded-xl border border-slate-200">
                <div class="bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-900">Ringkasan per Bulan</div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-white text-xs font-semibold uppercase tracking-wide text-slate-600">
                            <tr>
                                <th class="px-4 py-3">Bulan/Tahun</th>
                                <th class="px-4 py-3 text-center">PDF</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @forelse(($monthly ?? []) as $m)
                                <tr>
                                    <td class="px-4 py-3 font-medium text-slate-700">{{ $m['label'] ?? ($m['month'] ?? '-') }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <a href="{{ route('reports.pdf', ['month' => $m['month'] ?? '']) }}" class="inline-flex h-9 items-center justify-center rounded-md border border-slate-200 bg-white px-3 text-sm font-semibold text-slate-700 hover:bg-slate-50">Cetak</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="px-4 py-6 text-center text-sm text-slate-500">Belum ada data.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
