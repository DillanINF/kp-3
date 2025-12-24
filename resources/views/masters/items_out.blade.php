@extends('layouts.app')

@section('title', 'Data Barang Keluar')
@section('page_title', 'Data Barang Keluar')
@section('page_description')Pencatatan barang keluar (tujuan: customer).@endsection

@section('content')
    <div class="grid grid-cols-1 gap-4 xl:grid-cols-3">
        <div class="rounded-xl border border-slate-200 bg-white p-4 xl:col-span-1">
            <div class="text-sm font-semibold text-slate-900">Form Barang Keluar</div>
            <form action="{{ route('masters.items_out.store') }}" method="POST" class="mt-4 space-y-3">
                @csrf
                <div>
                    <label class="text-sm font-medium text-slate-700">Customer</label>
                    <select name="customer_id" class="mt-1 w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700" required>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-700">Barang</label>
                    <select name="item_id" class="mt-1 w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700" required>
                        @foreach($items as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-700">Qty</label>
                    <input name="qty" type="number" value="1" min="1" step="1" class="mt-1 w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700" required />
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-700">Tanggal</label>
                    <input name="date" type="date" value="{{ now()->toDateString() }}" class="mt-1 w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700" required />
                </div>
                @if(auth()->user()?->role === 'admin')
                    <button type="submit" class="w-full rounded-md bg-slate-900 px-3 py-2 text-sm font-semibold text-white hover:bg-slate-800">Simpan</button>
                @endif
            </form>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white xl:col-span-2">
            <div class="border-b border-slate-200 p-4">
                <div class="text-sm font-semibold text-slate-900">Riwayat Barang Keluar</div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-600">
                        <tr>
                            <th class="px-4 py-3">Tanggal</th>
                            <th class="px-4 py-3">Customer</th>
                            <th class="px-4 py-3">Barang</th>
                            <th class="px-4 py-3">Qty</th>
                            @if(auth()->user()?->role === 'admin')
                                <th class="px-4 py-3 text-center">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($history as $row)
                            <tr>
                                <td class="px-4 py-3 text-slate-600">{{ $row->date?->format('Y-m-d') }}</td>
                                <td class="px-4 py-3 text-slate-700">{{ $row->customer?->name }}</td>
                                <td class="px-4 py-3 text-slate-700">{{ $row->item?->name }}</td>
                                <td class="px-4 py-3 text-slate-700">{{ $row->qty }}</td>
                                @if(auth()->user()?->role === 'admin')
                                    <td class="px-4 py-3">
                                        <div class="flex items-center justify-center gap-2">
                                            <button
                                                type="button"
                                                data-action="edit-item-out"
                                                data-item-out-id="{{ $row->id }}"
                                                data-item-out-customer-id="{{ $row->customer_id }}"
                                                data-item-out-item-id="{{ $row->item_id }}"
                                                data-item-out-qty="{{ $row->qty }}"
                                                data-item-out-date="{{ $row->date?->format('Y-m-d') }}"
                                                class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-600 hover:bg-slate-50"
                                                aria-label="Edit"
                                            >
                                                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4">
                                                    <path d="M12 20H21" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                                    <path d="M16.5 3.5C17.3284 2.67157 18.6716 2.67157 19.5 3.5C20.3284 4.32843 20.3284 5.67157 19.5 6.5L8 18L3 19L4 14L16.5 3.5Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                                                </svg>
                                            </button>

                                            <form action="{{ route('masters.items_out.destroy', $row) }}" method="POST" onsubmit="return confirm('Hapus riwayat barang keluar ini?')">
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
                                <td colspan="{{ auth()->user()?->role === 'admin' ? 5 : 4 }}" class="px-4 py-6 text-center text-sm text-slate-500">Belum ada data barang keluar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if(auth()->user()?->role === 'admin')
        <div id="modal-edit-item-out" data-modal class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 p-4">
            <div class="w-full max-w-lg overflow-hidden rounded-2xl bg-white shadow-xl">
                <div class="bg-slate-900 px-6 py-5">
                    <div class="text-lg font-semibold text-white">Edit Barang Keluar</div>
                    <div class="mt-1 text-sm text-slate-200">Perbarui data barang keluar.</div>
                </div>

                <form data-edit-item-out-form data-action-template="{{ route('masters.items_out.update', ['itemOut' => '__ID__']) }}" action="" method="POST" class="space-y-4 px-6 py-6">
                    @csrf
                    @method('PUT')

                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-slate-700">Customer</label>
                        <select data-edit-item-out-customer name="customer_id" class="h-11 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-700 outline-none" required>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-slate-700">Barang</label>
                        <select data-edit-item-out-item name="item_id" class="h-11 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-700 outline-none" required>
                            @foreach($items as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-slate-700">Qty</label>
                        <input data-edit-item-out-qty name="qty" type="number" min="1" step="1" class="h-11 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-700 outline-none" required />
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-slate-700">Tanggal</label>
                        <input data-edit-item-out-date name="date" type="date" class="h-11 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-700 outline-none" required />
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2">
                        <button type="button" data-close-modal="modal-edit-item-out" class="inline-flex h-10 items-center justify-center rounded-xl border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-700 hover:bg-slate-50">Batal</button>
                        <button type="submit" class="inline-flex h-10 items-center justify-center rounded-xl bg-slate-900 px-4 text-sm font-semibold text-white hover:bg-slate-800">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
@endsection
