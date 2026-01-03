@extends('layouts.app')

@section('title', 'Input Purchase Order')
@section('page_title', 'Input Purchase Order')
@section('page_description')Isi data PO untuk invoice yang dipilih dari daftar invoice.@endsection

@section('content')
    <form action="{{ route('invoices.input_po.store', $invoice) }}" method="POST" class="space-y-6">
        @csrf
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div class="min-w-0">
                    <div class="text-lg font-semibold text-slate-900">Form Input PO</div>
                    <div class="mt-1 text-sm text-slate-500">Isi data PO untuk invoice yang dipilih.</div>
                </div>

                <div class="flex flex-wrap items-center justify-end gap-2">
                    <a href="{{ route('invoices.index') }}" class="inline-flex h-10 items-center justify-center rounded-xl border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-700 hover:bg-slate-50">Kembali ke Data Invoice</a>
                    <button type="submit" class="inline-flex h-10 items-center justify-center rounded-xl bg-indigo-600 px-4 text-sm font-semibold text-white hover:bg-indigo-700">Simpan</button>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="grid grid-cols-1 gap-4 p-5 lg:grid-cols-2">
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-slate-700">Customer</label>
                    <input type="text" value="{{ $invoice->customer?->name }}" disabled class="h-11 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 text-sm text-slate-700 outline-none" />
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-semibold text-slate-700">No PO</label>
                    <input name="po_no" value="{{ old('po_no', $invoice->po_no) }}" type="text" placeholder="po.32545" class="h-11 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-700 outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100" />
                </div>

                <div class="space-y-2 lg:col-span-2">
                    <label class="text-sm font-semibold text-slate-700">Alamat</label>
                    <input name="address" value="{{ old('address', $invoice->address) }}" type="text" placeholder="JLN. DAYAT" class="h-11 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-700 outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100" />
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex flex-col gap-3 border-b border-slate-200 p-5 md:flex-row md:items-center md:justify-between">
                <div class="min-w-0">
                    <div class="text-sm font-semibold text-slate-900">Produk Items</div>
                    <div class="mt-0.5 text-xs text-slate-500">Tambah produk, qty, dan harga untuk menghitung total.</div>
                </div>

                <button type="button" data-po-add-item class="inline-flex h-10 items-center justify-center rounded-xl bg-indigo-600 px-4 text-sm font-semibold text-white hover:bg-indigo-700">Tambah Produk</button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-600">
                        <tr>
                            <th class="px-4 py-3">Produk</th>
                            <th class="px-4 py-3">Quantity</th>
                            <th class="px-4 py-3">Harga</th>
                            <th class="px-4 py-3">Total</th>
                            <th class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200" data-po-items>
                        @php
                            $existingDetails = old('items') ?? ($invoice->details?->count() ? $invoice->details->map(fn ($d) => [
                                'item_id' => $d->item_id,
                                'qty' => $d->qty,
                                'price' => $d->price,
                            ])->toArray() : [[
                                'item_id' => null,
                                'qty' => 1,
                                'price' => 0,
                            ]]);
                        @endphp

                        @foreach($existingDetails as $i => $row)
                            <tr data-po-item-row>
                                <td class="px-4 py-3">
                                    <select data-po-item-id name="items[{{ $i }}][item_id]" class="h-10 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-700 outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100">
                                        <option value="">-- Pilih Produk --</option>
                                        @foreach($items ?? [] as $item)
                                            <option value="{{ $item->id }}" data-unit="{{ $item->unit }}" {{ (string) ($row['item_id'] ?? '') === (string) $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="grid grid-cols-[1fr_84px] gap-2">
                                        <input data-po-item-qty name="items[{{ $i }}][qty]" value="{{ $row['qty'] ?? 1 }}" type="number" min="1" step="1" class="h-10 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-700 outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100" />
                                        <input data-po-item-unit type="text" value="pcs" disabled class="h-10 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 text-sm text-slate-700 outline-none" />
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <input data-po-item-price name="items[{{ $i }}][price]" value="{{ $row['price'] ?? 0 }}" type="number" min="0" step="1" class="h-10 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-700 outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100" />
                                </td>
                                <td class="px-4 py-3">
                                    <input data-po-item-total type="text" disabled value="0" class="h-10 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 text-sm text-slate-700 outline-none" />
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <button type="button" data-po-remove-item class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-rose-200 bg-rose-50 text-rose-700 hover:bg-rose-100" aria-label="Delete">×</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="flex items-center justify-end gap-2 border-t border-slate-200 p-5">
                <div class="inline-flex items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                    <div class="text-sm font-semibold text-slate-700">Grand Total</div>
                    <input data-po-grand-total type="text" disabled value="0" class="h-10 w-[200px] rounded-xl border border-slate-200 bg-white px-3 text-right text-sm font-semibold text-slate-900 outline-none" />
                </div>
            </div>
        </div>
    </form>
@endsection
