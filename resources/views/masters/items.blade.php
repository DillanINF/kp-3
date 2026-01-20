@extends('layouts.app')

@section('title', 'Data Barang')
@section('page_title', 'Data Barang')
@section('page_description')Master data barang yang digunakan untuk transaksi masuk/keluar.@endsection

@section('content')
    <div class="rounded-xl border border-slate-200 bg-white">
        <div class="flex items-center justify-between border-b border-slate-200 p-4">
            <div class="text-sm font-semibold text-slate-900">Barang Biasa</div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-600">
                    <tr>
                        <th class="px-4 py-3">No</th>
                        <th class="px-4 py-3">Nama</th>
                        <th class="px-4 py-3">Stok</th>
                        <th class="px-4 py-3">Harga Beli (Supplier/pcs)</th>
                        <th class="px-4 py-3">Harga Jual (Customer)</th>
                        @if(auth()->user()?->role === 'admin')
                            <th class="px-4 py-3 text-center">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($regularItems as $item)
                        <tr>
                            <td class="px-4 py-3 text-slate-700">{{ $loop->iteration }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $item->name }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $item->stock }}</td>
                            <td class="px-4 py-3 text-slate-700">Rp {{ number_format($item->supplier_buy_price_per_pcs ?? 0, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-slate-700">Rp {{ number_format($item->price ?? 0, 0, ',', '.') }}</td>
                            @if(auth()->user()?->role === 'admin')
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-center gap-2">
                                        <form action="{{ route('masters.items.destroy', $item) }}" method="POST" onsubmit="return confirm('Hapus barang ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-rose-200 bg-rose-50 text-rose-700 hover:bg-rose-100" aria-label="Hapus">×</button>
                                        </form>
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ auth()->user()?->role === 'admin' ? 6 : 5 }}" class="px-4 py-6 text-center text-sm text-slate-500">Belum ada data barang biasa.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
