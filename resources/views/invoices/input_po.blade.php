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
                    <input name="po_no" value="{{ $invoice->po_no ?: ('PO-' . (string) ($invoice->invoice_no ?? '')) }}" type="text" readonly class="h-11 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 text-sm text-slate-700 outline-none" />
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-semibold text-slate-700">Tanggal Kirim</label>
                    <input name="delivery_date" value="{{ $invoice->delivery_date ? $invoice->delivery_date->format('Y-m-d') : date('Y-m-d') }}" type="date" class="h-11 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-700 outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100" required />
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-semibold text-slate-700">Pengirim</label>
                    <select id="pengirim_select" name="pengirim_id" class="h-11 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-700 outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100" required>
                        <option value="">-- Pilih Pengirim --</option>
                        @foreach($pengirims as $p)
                            <option value="{{ $p->id }}" 
                                data-phone="{{ $p->phone }}" 
                                data-vehicle="{{ $p->vehicle_type }}" 
                                data-plate="{{ $p->license_plate }}"
                                {{ (string) $invoice->pengirim_id === (string) $p->id ? 'selected' : '' }}>
                                {{ $p->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-semibold text-slate-700">Detail Pengirim</label>
                    <div id="pengirim_detail" class="flex h-11 w-full items-center rounded-xl border border-slate-200 bg-slate-50 px-3 text-sm text-slate-600">
                        <span class="italic text-slate-400">Pilih pengirim untuk melihat detail</span>
                    </div>
                </div>

                <div class="col-span-full space-y-2">
                    <label class="text-sm font-semibold text-slate-700">Alamat Customer</label>
                    <textarea name="address" rows="3" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100" placeholder="Masukkan alamat lengkap pengiriman..." required>{{ $invoice->address ?: $invoice->customer?->address }}</textarea>
                </div>
            </div>

            <div class="flex flex-col gap-3 border-t border-slate-200 p-5 md:flex-row md:items-center md:justify-between">
                <div class="min-w-0">
                    <div class="text-sm font-semibold text-slate-900">Produk Items</div>
                    <div class="mt-0.5 text-xs text-slate-500">Tambah produk, qty, dan harga untuk menghitung total.</div>
                </div>

                <button type="button" data-po-add-item class="inline-flex h-10 items-center justify-center rounded-xl bg-red-600 px-4 text-sm font-semibold text-white hover:bg-indigo-700">Tambah Produk</button>
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
                                    <select data-po-item-id name="items[{{ $i }}][item_id]" class="h-10 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-700 outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100" onchange="updatePrice(this)">
                                        <option value="">-- Pilih Produk --</option>
                                        @foreach($items ?? [] as $item)
                                            <option 
                                                value="{{ $item->id }}" 
                                                data-unit="{{ $item->unit }}" 
                                                data-price="{{ $item->price }}"
                                                data-stock="{{ $item->stock }}"
                                                {{ (string) ($row['item_id'] ?? '') === (string) $item->id ? 'selected' : '' }}>
                                                {{ $item->name }} (Stok: {{ $item->stock }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <script>
                                        function updatePrice(select) {
                                            const row = select.closest('tr');
                                            if (!row) return;
                                            
                                            const selectedOption = select.options[select.selectedIndex];
                                            const priceInput = row.querySelector('[data-po-item-price]');
                                            
                                            if (selectedOption && selectedOption.dataset.price) {
                                                priceInput.value = selectedOption.dataset.price;
                                                // Trigger input event to update total
                                                priceInput.dispatchEvent(new Event('input', { bubbles: true }));
                                            }
                                        }
                                        
                                        // Initialize prices for pre-selected items on page load
                                        document.addEventListener('DOMContentLoaded', function() {
                                            document.querySelectorAll('[data-po-item-id]').forEach(select => {
                                                if (select.value) {
                                                    updatePrice(select);
                                                }
                                            });
                                        });
                                    </script>
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
        </div>
    </form>

    <script>
        (function () {
            const toIntOrZero = (value) => {
                const raw = String(value ?? '').trim();
                if (!raw.length) return 0;
                const n = Number.parseInt(raw, 10);
                return Number.isFinite(n) ? n : 0;
            };

            const recalcRowTotals = () => {
                const rows = document.querySelectorAll('[data-po-items] [data-po-item-row]');
                rows.forEach((row) => {
                    const qty = toIntOrZero(row.querySelector('[data-po-item-qty]')?.value);
                    const price = toIntOrZero(row.querySelector('[data-po-item-price]')?.value);
                    const total = qty * price;
                    const totalEl = row.querySelector('[data-po-item-total]');
                    if (totalEl) totalEl.value = String(total);
                });
            };

            document.addEventListener('input', (e) => {
                if (!e.target.closest('[data-po-items]')) return;
                if (e.target.closest('[data-po-item-qty]') || e.target.closest('[data-po-item-price]')) {
                    recalcRowTotals();
                }
            });

            document.addEventListener('change', (e) => {
                if (!e.target.closest('[data-po-items]')) return;
                if (e.target.closest('[data-po-item-id]')) {
                    // updatePrice() already dispatches input on price; this is just a safe fallback.
                    setTimeout(recalcRowTotals, 0);
                }
            });

            document.addEventListener('DOMContentLoaded', () => {
                recalcRowTotals();
                
                // Info Pengirim Script
                const pengirimSelect = document.getElementById('pengirim_select');
                const pengirimDetail = document.getElementById('pengirim_detail');
                
                const updatePengirimDetail = () => {
                    const opt = pengirimSelect.options[pengirimSelect.selectedIndex];
                    if (!opt || !opt.value) {
                        pengirimDetail.innerHTML = '<span class="italic text-slate-400">Pilih pengirim untuk melihat detail</span>';
                        return;
                    }
                    
                    const phone = opt.dataset.phone || '-';
                    const vehicle = opt.dataset.vehicle || '-';
                    const plate = opt.dataset.plate || '-';
                    
                    pengirimDetail.innerHTML = `
                        <div class="flex gap-4">
                            <span title="No. Telp">📞 ${phone}</span>
                            <span title="Kendaraan">🚚 ${vehicle}</span>
                            <span title="No. Polisi">🆔 ${plate}</span>
                        </div>
                    `;
                };
                
                if (pengirimSelect) {
                    pengirimSelect.addEventListener('change', updatePengirimDetail);
                    updatePengirimDetail(); // Init on load
                }
            });
        })();
    </script>
@endsection
