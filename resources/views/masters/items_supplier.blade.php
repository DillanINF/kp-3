@extends('layouts.app')

@section('title', 'Permintaan Barang Supplier')
@section('page_title', 'Permintaan Barang Supplier')
@section('page_description')Permintaan barang ke supplier untuk proses produksi.@endsection

@section('content')
    <div class="rounded-xl border border-slate-200 bg-white">
        <div class="flex flex-col gap-3 border-b border-slate-200 p-4 md:flex-row md:items-center md:justify-between">
            <div class="flex flex-col gap-3 md:flex-row md:items-center">
                <div class="text-sm font-semibold text-slate-900">Daftar Permintaan</div>
                <form action="{{ route('masters.items_supplier') }}" method="GET">
                    <select name="supplier_id" onchange="this.form.submit()" class="h-9 rounded-md border border-slate-200 bg-white px-3 text-sm text-slate-700">
                        <option value="">Semua Supplier</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" @selected((string) $supplier->id === (string) ($selectedSupplierId ?? ''))>{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                </form>
            </div>

            @if(auth()->user()?->role === 'admin')
                <button type="button" data-open-modal="modal-tambah-permintaan" class="inline-flex h-10 items-center justify-center rounded-md bg-slate-900 px-4 text-sm font-semibold text-white hover:bg-slate-800">Buat Permintaan</button>
            @endif
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-600">
                    <tr>
                        <th class="px-4 py-3">No</th>
                        <th class="px-4 py-3">Waktu</th>
                        <th class="px-4 py-3">Supplier</th>
                        <th class="px-4 py-3">Qty</th>
                        <th class="px-4 py-3">Satuan</th>
                        <th class="px-4 py-3">Harga/QTY</th>
                        <th class="px-4 py-3">Total</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Send</th>
                        @if(auth()->user()?->role === 'admin')
                            <th class="px-4 py-3 text-center">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($requests as $req)
                        <tr>
                            <td class="px-4 py-3 font-medium text-slate-900">{{ $loop->iteration }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $req->created_at?->format('d/m/Y H:i:s') }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $req->supplier?->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $req->total_qty }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $req->units_summary ?? '-' }}</td>
                            @php
                                $totalQty = (int) ($req->total_qty ?? 0);
                                $totalAmount = (int) ($req->total_amount ?? 0);
                                $unitPrice = $totalQty > 0 ? (int) round($totalAmount / $totalQty) : 0;
                            @endphp
                            <td class="px-4 py-3 text-slate-700">Rp {{ number_format($unitPrice, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-slate-700">Rp {{ number_format($req->total_amount ?? 0, 0, ',', '.') }}</td>
                            <td class="px-4 py-3">
                                @if(auth()->user()?->role === 'admin' && $req->status !== 'accepted')
                                    <form action="{{ route('masters.items_supplier.accept', ['supplierRequest' => $req->id]) }}" method="POST" onsubmit="return confirm('Terima barang dari supplier? Stok akan bertambah dan tercatat di Barang Masuk.')">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-800 hover:bg-amber-200">TERIMA BARANG</button>
                                    </form>
                                @elseif(($req->status ?? '') === 'accepted')
                                    <span class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-800">DITERIMA</span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">BELUM DITERIMA</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if(auth()->user()?->role === 'admin')
                                    <form action="{{ route('masters.items_supplier.send', ['supplierRequest' => $req->id]) }}" method="POST" onsubmit="return confirm('Kirim permintaan ini ke supplier?')">
                                        @csrf
                                        <button type="submit" @disabled($req->status === 'accepted') class="inline-flex h-9 items-center justify-center rounded-md border border-indigo-200 bg-indigo-50 px-3 text-xs font-semibold text-indigo-700 hover:bg-indigo-100 disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:bg-indigo-50">
                                            Send
                                        </button>
                                    </form>
                                @else
                                    <span class="text-sm text-slate-500">-</span>
                                @endif
                            </td>
                            @if(auth()->user()?->role === 'admin')
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-center gap-2">
                                        <button
                                            type="button"
                                            data-action="edit-request"
                                            data-request-id="{{ $req->id }}"
                                            data-request-supplier-id="{{ $req->supplier_id }}"
                                            data-request-notes="{{ $req->notes }}"
                                            data-request-items='{{ $req->items_json }}'
                                            class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-600 hover:bg-slate-50"
                                            aria-label="Edit"
                                        >
                                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4">
                                                <path d="M12 20H21" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                                <path d="M16.5 3.5C17.3284 2.67157 18.6716 2.67157 19.5 3.5C20.3284 4.32843 20.3284 5.67157 19.5 6.5L8 18L3 19L4 14L16.5 3.5Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                                            </svg>
                                        </button>
                                        <form action="{{ route('masters.items_supplier.destroy', ['supplierRequest' => $req->id]) }}" method="POST" onsubmit="return confirm('Hapus permintaan ini?')">
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
                            <td colspan="{{ auth()->user()?->role === 'admin' ? 9 : 8 }}" class="px-4 py-6 text-center text-sm text-slate-500">Belum ada permintaan barang supplier.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if(auth()->user()?->role === 'admin')
        <div id="modal-tambah-permintaan" data-modal class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 p-4">
            <div class="w-full max-w-4xl overflow-hidden rounded-2xl bg-white shadow-xl">
                <div class="bg-slate-900 px-6 py-5">
                    <div class="text-lg font-semibold text-white">Buat Permintaan Barang</div>
                    <div class="mt-1 text-sm text-slate-200">Input pesanan barang ke supplier beserta detail item.</div>
                </div>

                <form id="request-form" action="{{ route('masters.items_supplier.store') }}" method="POST" class="space-y-4 px-6 py-6">
                    @csrf

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-slate-700">Supplier</label>
                            <select name="supplier_id" data-request-supplier class="h-11 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-700 outline-none" required>
                                <option value="" disabled selected>Pilih Supplier</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-slate-700">Waktu</label>
                            <input type="text" value="{{ now()->format('d/m/Y H:i:s') }}" class="h-11 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 text-sm text-slate-700 outline-none" readonly />
                        </div>
                    </div>

                    <div class="rounded-xl border border-slate-200">
                        <div class="flex items-center justify-between border-b border-slate-200 bg-slate-50 px-4 py-3">
                            <div class="text-sm font-semibold text-slate-900">Detail Item</div>
                            <button type="button" data-add-request-item class="inline-flex h-9 items-center justify-center rounded-md bg-indigo-600 px-3 text-xs font-semibold text-white hover:bg-indigo-700">Tambah Item</button>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm">
                                <thead class="bg-white text-xs font-semibold uppercase tracking-wide text-slate-600">
                                    <tr>
                                        <th class="px-4 py-3">Nama Produk</th>
                                        <th class="px-4 py-3">Satuan</th>
                                        <th class="px-4 py-3">Qty</th>
                                        <th class="px-4 py-3">Harga</th>
                                        <th class="px-4 py-3 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200" data-request-items></tbody>
                            </table>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2">
                        <button type="button" data-close-modal="modal-tambah-permintaan" class="inline-flex h-10 items-center justify-center rounded-xl border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-700 hover:bg-slate-50">Batal</button>
                        <button type="submit" class="inline-flex h-10 items-center justify-center rounded-xl bg-slate-900 px-4 text-sm font-semibold text-white hover:bg-slate-800">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

        <div id="modal-edit-permintaan" data-modal class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 p-4">
            <div class="w-full max-w-4xl overflow-hidden rounded-2xl bg-white shadow-xl">
                <div class="bg-slate-900 px-6 py-5">
                    <div class="text-lg font-semibold text-white">Edit Permintaan Barang</div>
                    <div class="mt-1 text-sm text-slate-200">Perbarui data permintaan dan detail item.</div>
                </div>

                <form id="edit-request-form" data-action-template="{{ route('masters.items_supplier.update', ['supplierRequest' => '__ID__']) }}" action="" method="POST" class="space-y-4 px-6 py-6">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-slate-700">Supplier</label>
                            <select name="supplier_id" data-edit-request-supplier class="h-11 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-700 outline-none" required>
                                <option value="" disabled selected>Pilih Supplier</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-slate-700">Waktu</label>
                            <input type="text" value="{{ now()->format('d/m/Y H:i:s') }}" class="h-11 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 text-sm text-slate-700 outline-none" readonly />
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-slate-700">Catatan (opsional)</label>
                        <textarea name="notes" rows="2" data-edit-request-notes class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 outline-none"></textarea>
                    </div>

                    <div class="rounded-xl border border-slate-200">
                        <div class="flex items-center justify-between border-b border-slate-200 bg-slate-50 px-4 py-3">
                            <div class="text-sm font-semibold text-slate-900">Detail Item</div>
                            <button type="button" data-edit-add-request-item class="inline-flex h-9 items-center justify-center rounded-md bg-indigo-600 px-3 text-xs font-semibold text-white hover:bg-indigo-700">Tambah Item</button>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm">
                                <thead class="bg-white text-xs font-semibold uppercase tracking-wide text-slate-600">
                                    <tr>
                                        <th class="px-4 py-3">Nama Produk</th>
                                        <th class="px-4 py-3">Satuan</th>
                                        <th class="px-4 py-3">Qty</th>
                                        <th class="px-4 py-3">Harga</th>
                                        <th class="px-4 py-3 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200" data-edit-request-items></tbody>
                            </table>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2">
                        <button type="button" data-close-modal="modal-edit-permintaan" class="inline-flex h-10 items-center justify-center rounded-xl border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-700 hover:bg-slate-50">Batal</button>
                        <button type="submit" class="inline-flex h-10 items-center justify-center rounded-xl bg-slate-900 px-4 text-sm font-semibold text-white hover:bg-slate-800">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

        <script>
            const SUPPLIER_PRODUCTS = @json($supplierProducts ?? []);

            const openInlineModal = (id) => {
                const modal = document.getElementById(id);
                if (!modal) return;
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                document.documentElement.classList.add('overflow-hidden');
            };

            const getSelectedSupplierId = () => {
                const form = document.getElementById('request-form');
                const sel = form?.querySelector('[data-request-supplier]');
                const v = sel?.value;
                return v && String(v).trim().length > 0 ? String(v) : null;
            };

            const getProductsForSelectedSupplier = () => {
                const supplierId = getSelectedSupplierId();
                if (!supplierId) return [];
                return Array.isArray(SUPPLIER_PRODUCTS?.[supplierId]) ? SUPPLIER_PRODUCTS[supplierId] : [];
            };

            const createRequestRow = (index) => {
                const products = getProductsForSelectedSupplier();
                const tr = document.createElement('tr');
                tr.setAttribute('data-request-item-row', '1');
                tr.innerHTML = `
                    <td class="px-4 py-3">
                        <select name="items[${index}][item_id]" data-request-product class="h-10 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-700 outline-none" required>
                            <option value="" disabled selected>Pilih Produk</option>
                            ${products
                                .map(
                                    (p) =>
                                        `<option value="${String(p.id)}" data-unit="${String(p.unit ?? '').replaceAll('"', '&quot;')}" data-price="${String(p.price)}">${String(p.name)}</option>`,
                                )
                                .join('')}
                        </select>
                    </td>
                    <td class="px-4 py-3">
                        <input name="items[${index}][unit]" data-request-unit type="text" value="" class="h-10 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 text-sm text-slate-700 outline-none" required readonly />
                    </td>
                    <td class="px-4 py-3">
                        <input name="items[${index}][qty]" type="number" min="1" step="1" value="1" class="h-10 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-700 outline-none" required />
                    </td>
                    <td class="px-4 py-3">
                        <input name="items[${index}][price]" data-request-price type="number" min="0" step="1" value="0" class="h-10 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-700 outline-none" />
                    </td>
                    <td class="px-4 py-3 text-center">
                        <button type="button" data-remove-request-item class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-rose-200 bg-rose-50 text-rose-700 hover:bg-rose-100" aria-label="Delete">×</button>
                    </td>
                `;
                return tr;
            };

            const resetRequestForm = () => {
                const form = document.getElementById('request-form');
                if (!form) return;
                const itemsEl = form.querySelector('[data-request-items]');
                if (!itemsEl) return;
                itemsEl.innerHTML = '';
                itemsEl.appendChild(createRequestRow(0));
            };

            const createEditRequestRow = (index) => {
                const products = getProductsForSelectedSupplierEdit();
                const tr = document.createElement('tr');
                tr.setAttribute('data-request-item-row', '1');
                tr.innerHTML = `
                    <td class="px-4 py-3">
                        <select name="items[${index}][item_id]" data-request-product class="h-10 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-700 outline-none" required>
                            <option value="" disabled selected>Pilih Produk</option>
                            ${products
                                .map(
                                    (p) =>
                                        `<option value="${String(p.id)}" data-unit="${String(p.unit ?? '').replaceAll('"', '&quot;')}" data-price="${String(p.price)}">${String(p.name)}</option>`,
                                )
                                .join('')}
                        </select>
                    </td>
                    <td class="px-4 py-3">
                        <input name="items[${index}][unit]" data-request-unit type="text" value="" class="h-10 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 text-sm text-slate-700 outline-none" required readonly />
                    </td>
                    <td class="px-4 py-3">
                        <input name="items[${index}][qty]" type="number" min="1" step="1" value="1" class="h-10 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-700 outline-none" required />
                    </td>
                    <td class="px-4 py-3">
                        <input name="items[${index}][price]" data-request-price type="number" min="0" step="1" value="0" class="h-10 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-700 outline-none" />
                    </td>
                    <td class="px-4 py-3 text-center">
                        <button type="button" data-edit-remove-request-item class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-rose-200 bg-rose-50 text-rose-700 hover:bg-rose-100" aria-label="Delete">×</button>
                    </td>
                `;
                return tr;
            };

            const getSelectedSupplierIdEdit = () => {
                const form = document.getElementById('edit-request-form');
                const sel = form?.querySelector('[data-edit-request-supplier]');
                const v = sel?.value;
                return v && String(v).trim().length > 0 ? String(v) : null;
            };

            const getProductsForSelectedSupplierEdit = () => {
                const supplierId = getSelectedSupplierIdEdit();
                if (!supplierId) return [];
                return Array.isArray(SUPPLIER_PRODUCTS?.[supplierId]) ? SUPPLIER_PRODUCTS[supplierId] : [];
            };

            const applySelectedProductToRowEdit = (row) => {
                applySelectedProductToRow(row);
            };

            const resetEditFormWithItems = (items) => {
                const form = document.getElementById('edit-request-form');
                const itemsEl = form?.querySelector('[data-edit-request-items]');
                if (!itemsEl) return;
                itemsEl.innerHTML = '';

                const list = Array.isArray(items) && items.length ? items : [{ item_id: '', unit: '', qty: 1, price: 0 }];
                list.forEach((it, idx) => {
                    const row = createEditRequestRow(idx);
                    itemsEl.appendChild(row);

                    const sel = row.querySelector('[data-request-product]');
                    if (sel && it?.item_id) {
                        sel.value = String(it.item_id);
                    }
                    applySelectedProductToRowEdit(row);

                    const qtyEl = row.querySelector(`[name="items[${idx}][qty]"]`);
                    if (qtyEl) qtyEl.value = String(it?.qty ?? 1);

                    const priceEl = row.querySelector('[data-request-price]');
                    if (priceEl) priceEl.value = String(it?.price ?? 0);
                });
            };

            const applySelectedProductToRow = (row) => {
                if (!row) return;
                const productSel = row.querySelector('[data-request-product]');
                const unitEl = row.querySelector('[data-request-unit]');
                const priceEl = row.querySelector('[data-request-price]');
                const opt = productSel?.selectedOptions?.[0];
                if (!opt) return;

                const unit = opt.getAttribute('data-unit') || '';
                const price = opt.getAttribute('data-price') || '0';
                if (unitEl) unitEl.value = unit;
                if (priceEl) priceEl.value = price;
            };

            document.addEventListener('click', (e) => {
                const openNew = e.target.closest('[data-open-modal="modal-tambah-permintaan"]');
                if (openNew) {
                    resetRequestForm();
                    return;
                }

                const editBtn = e.target.closest('[data-action="edit-request"]');
                if (editBtn) {
                    const id = editBtn.getAttribute('data-request-id');
                    const supplierId = editBtn.getAttribute('data-request-supplier-id') || '';
                    const notes = editBtn.getAttribute('data-request-notes') || '';
                    const itemsRaw = editBtn.getAttribute('data-request-items') || '[]';
                    let items = [];
                    try {
                        items = JSON.parse(itemsRaw);
                    } catch (err) {
                        items = [];
                    }

                    const form = document.getElementById('edit-request-form');
                    if (form) {
                        const template = form.getAttribute('data-action-template') || '';
                        if (template.includes('__ID__') && id) {
                            form.setAttribute('action', template.replace('__ID__', id));
                        }
                    }

                    const supplierEl = document.querySelector('#edit-request-form [data-edit-request-supplier]');
                    const notesEl = document.querySelector('#edit-request-form [data-edit-request-notes]');
                    if (supplierEl) supplierEl.value = supplierId;
                    if (notesEl) notesEl.value = notes;

                    resetEditFormWithItems(items);
                    openInlineModal('modal-edit-permintaan');
                    return;
                }

                const addBtn = e.target.closest('[data-add-request-item]');
                if (addBtn) {
                    const form = document.getElementById('request-form');
                    const itemsEl = form?.querySelector('[data-request-items]');
                    if (!itemsEl) return;
                    const index = itemsEl.querySelectorAll('[data-request-item-row]').length;
                    itemsEl.appendChild(createRequestRow(index));
                    return;
                }

                const removeBtn = e.target.closest('[data-remove-request-item]');
                if (removeBtn) {
                    const row = removeBtn.closest('tr');
                    const form = document.getElementById('request-form');
                    const itemsEl = form?.querySelector('[data-request-items]');
                    if (!row || !itemsEl) return;
                    row.remove();
                    if (itemsEl.querySelectorAll('[data-request-item-row]').length === 0) {
                        itemsEl.appendChild(createRequestRow(0));
                    }
                }

                const editAddBtn = e.target.closest('[data-edit-add-request-item]');
                if (editAddBtn) {
                    const form = document.getElementById('edit-request-form');
                    const itemsEl = form?.querySelector('[data-edit-request-items]');
                    if (!itemsEl) return;
                    const index = itemsEl.querySelectorAll('[data-request-item-row]').length;
                    itemsEl.appendChild(createEditRequestRow(index));
                    return;
                }

                const editRemoveBtn = e.target.closest('[data-edit-remove-request-item]');
                if (editRemoveBtn) {
                    const row = editRemoveBtn.closest('tr');
                    const form = document.getElementById('edit-request-form');
                    const itemsEl = form?.querySelector('[data-edit-request-items]');
                    if (!row || !itemsEl) return;
                    row.remove();
                    if (itemsEl.querySelectorAll('[data-request-item-row]').length === 0) {
                        itemsEl.appendChild(createEditRequestRow(0));
                    }
                }
            });

            document.addEventListener('change', (e) => {
                const supplierSel = e.target.closest('#request-form [data-request-supplier]');
                if (supplierSel) {
                    resetRequestForm();
                    return;
                }

                const editSupplierSel = e.target.closest('#edit-request-form [data-edit-request-supplier]');
                if (editSupplierSel) {
                    resetEditFormWithItems([]);
                    return;
                }

                const productSel = e.target.closest('#request-form [data-request-product]');
                if (productSel) {
                    const row = productSel.closest('tr');
                    applySelectedProductToRow(row);
                }

                const editProductSel = e.target.closest('#edit-request-form [data-request-product]');
                if (editProductSel) {
                    const row = editProductSel.closest('tr');
                    applySelectedProductToRowEdit(row);
                }
            });
        </script>
    @endif
@endsection
