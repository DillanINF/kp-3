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
                        <th class="px-4 py-3">Nama</th>
                        <th class="px-4 py-3">Satuan</th>
                        <th class="px-4 py-3">Stok</th>
                        <th class="px-4 py-3">Harga</th>
                        @if(auth()->user()?->role === 'admin')
                            <th class="px-4 py-3 text-center">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($regularItems as $item)
                        <tr>
                            <td class="px-4 py-3 text-slate-700">{{ $item->name }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $item->unit }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $item->stock }}</td>
                            <td class="px-4 py-3 text-slate-700">Rp {{ number_format($item->price ?? 0, 0, ',', '.') }}</td>
                            @if(auth()->user()?->role === 'admin')
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-center gap-2">
                                        <button
                                            type="button"
                                            data-action="edit-item"
                                            data-item-id="{{ $item->id }}"
                                            data-item-type="regular"
                                            data-item-name="{{ $item->name }}"
                                            data-item-unit="{{ $item->unit }}"
                                            data-item-price="{{ $item->price }}"
                                            data-item-active="{{ $item->is_active ? 1 : 0 }}"
                                            class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-600 hover:bg-slate-50"
                                            aria-label="Edit"
                                        >
                                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4">
                                                <path d="M12 20H21" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                                <path d="M16.5 3.5C17.3284 2.67157 18.6716 2.67157 19.5 3.5C20.3284 4.32843 20.3284 5.67157 19.5 6.5L8 18L3 19L4 14L16.5 3.5Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                                            </svg>
                                        </button>

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
                            <td colspan="{{ auth()->user()?->role === 'admin' ? 5 : 4 }}" class="px-4 py-6 text-center text-sm text-slate-500">Belum ada data barang biasa.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if(auth()->user()?->role === 'admin')
        <div id="modal-edit-item" data-modal class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 p-4">
            <div class="w-full max-w-lg overflow-hidden rounded-2xl bg-white shadow-xl">
                <div class="bg-slate-900 px-6 py-5">
                    <div class="text-lg font-semibold text-white">Edit Barang</div>
                    <div class="mt-1 text-sm text-slate-200">Perbarui data barang.</div>
                </div>

                <form data-edit-item-form data-action-template="{{ route('masters.items.update', ['item' => '__ID__']) }}" action="" method="POST" class="space-y-4 px-6 py-6">
                    @csrf
                    @method('PUT')

                    <input data-edit-item-type name="item_type" type="hidden" value="regular" />

                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-slate-700">Nama</label>
                        <input data-edit-item-name name="name" type="text" class="h-11 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-700 outline-none focus:border-slate-400 focus:ring-4 focus:ring-slate-100" required />
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-slate-700">Satuan</label>
                        <input data-edit-item-unit name="unit" type="text" class="h-11 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-700 outline-none focus:border-slate-400 focus:ring-4 focus:ring-slate-100" required />
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-slate-700">Harga</label>
                        <input data-edit-item-price name="price" type="number" min="0" step="1" class="h-11 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-700 outline-none focus:border-slate-400 focus:ring-4 focus:ring-slate-100" />
                    </div>

                    <div class="flex items-center justify-between pt-2">
                        <label class="inline-flex items-center gap-2 text-sm font-semibold text-slate-700">
                            <input data-edit-item-active name="is_active" type="checkbox" value="1" class="h-4 w-4 rounded border-slate-300" />
                            Aktif
                        </label>

                        <div class="flex items-center gap-2">
                            <button type="button" data-close-modal="modal-edit-item" class="inline-flex h-10 items-center justify-center rounded-xl border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-700 hover:bg-slate-50">Batal</button>
                            <button type="submit" class="inline-flex h-10 items-center justify-center rounded-xl bg-slate-900 px-4 text-sm font-semibold text-white hover:bg-slate-800">Simpan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif
@endsection
