@extends('layouts.app')

@section('title', 'Data Supplier')
@section('page_title', 'Data Supplier')
@section('page_description')Manajemen data supplier untuk transaksi barang masuk.@endsection

@section('content')
    <div class="rounded-xl border border-slate-200 bg-white">
        <div class="flex items-center justify-between border-b border-slate-200 p-4">
            <div class="text-sm font-semibold text-slate-900">Supplier</div>
            @if(auth()->user()?->role === 'admin')
                <button type="button" data-open-modal="modal-tambah-supplier" class="inline-flex items-center gap-2 rounded-md bg-slate-900 px-3 py-2 text-sm font-semibold text-white hover:bg-slate-800">Tambah</button>
            @endif
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-600">
                    <tr>
                        <th class="w-10 px-4 py-3"></th>
                        <th class="px-4 py-3">Nama</th>
                        <th class="px-4 py-3">Kontak</th>
                        <th class="px-4 py-3">Telepon</th>
                        <th class="px-4 py-3">Alamat</th>
                        @if(auth()->user()?->role === 'admin')
                            <th class="px-4 py-3 text-center">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($suppliers as $supplier)
                        <tr>
                            <td class="px-4 py-3">
                                <button type="button" data-toggle-supplier-items="{{ $supplier->id }}" class="inline-flex h-8 w-8 items-center justify-center rounded-md border border-slate-200 bg-white text-slate-600 hover:bg-slate-50" aria-label="Toggle">
                                    <span data-supplier-chevron="{{ $supplier->id }}">&gt;</span>
                                </button>
                            </td>
                            <td class="px-4 py-3 font-medium text-slate-900">{{ $supplier->name }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $supplier->contact_name ?? '-' }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $supplier->phone ?? '-' }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $supplier->address ?? '-' }}</td>
                            @if(auth()->user()?->role === 'admin')
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-center gap-2">
                                        <a
                                            href="{{ route('masters.items_supplier', ['supplier_id' => $supplier->id]) }}"
                                            class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-600 hover:bg-slate-50"
                                            aria-label="Kelola bahan baku"
                                            title="Kelola bahan baku"
                                        >
                                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4">
                                                <path d="M4 6H20" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                                                <path d="M4 12H20" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                                                <path d="M4 18H14" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                                            </svg>
                                        </a>

                                        <button
                                            type="button"
                                            data-action="edit-supplier"
                                            data-supplier-id="{{ $supplier->id }}"
                                            data-supplier-name="{{ $supplier->name }}"
                                            data-supplier-contact="{{ $supplier->contact_name }}"
                                            data-supplier-phone="{{ $supplier->phone }}"
                                            data-supplier-address="{{ $supplier->address }}"
                                            data-supplier-active="{{ $supplier->is_active ? 1 : 0 }}"
                                            class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-600 hover:bg-slate-50"
                                            aria-label="Edit"
                                        >
                                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4">
                                                <path d="M12 20H21" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                                <path d="M16.5 3.5C17.3284 2.67157 18.6716 2.67157 19.5 3.5C20.3284 4.32843 20.3284 5.67157 19.5 6.5L8 18L3 19L4 14L16.5 3.5Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                                            </svg>
                                        </button>

                                        <form action="{{ route('masters.suppliers.destroy', $supplier) }}" method="POST" onsubmit="return confirm('Hapus supplier ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-rose-200 bg-rose-50 text-rose-700 hover:bg-rose-100" aria-label="Hapus">
                                                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4">
                                                    <path d="M3 6H21" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                                    <path d="M8 6V4H16V6" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                                                    <path d="M19 6L18 20H6L5 6" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                                                    <path d="M10 11V17" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                                    <path d="M14 11V17" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            @endif
                        </tr>

                        <tr data-supplier-items-row="{{ $supplier->id }}" class="hidden bg-slate-50">
                            <td></td>
                            <td colspan="{{ auth()->user()?->role === 'admin' ? 5 : 4 }}" class="px-4 py-4">
                                <div class="rounded-lg border border-slate-200 bg-white p-4">
                                    <div class="mb-2 text-sm font-semibold text-slate-900">Barang Supplier</div>
                                    @if(($supplier->supplierItems?->count() ?? 0) > 0)
                                        <div class="grid gap-2">
                                            @foreach($supplier->supplierItems as $item)
                                                <div class="flex items-center justify-between rounded-md border border-slate-200 bg-slate-50 px-3 py-2">
                                                    <div class="font-medium text-slate-900">{{ $item->name }}</div>
                                                    <div class="text-xs text-slate-500">{{ $item->sku }}</div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="text-sm text-slate-500">Belum ada barang supplier.</div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ auth()->user()?->role === 'admin' ? 6 : 5 }}" class="px-4 py-6 text-center text-sm text-slate-500">Belum ada data supplier.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if(auth()->user()?->role === 'admin')
        <div id="modal-tambah-supplier" data-modal class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 p-4">
            <div class="w-full max-w-lg overflow-hidden rounded-2xl bg-white shadow-xl">
                <div class="bg-slate-900 px-6 py-5">
                    <div class="text-lg font-semibold text-white">Tambah Supplier</div>
                    <div class="mt-1 text-sm text-slate-200">Isi data supplier.</div>
                </div>

                <form action="{{ route('masters.suppliers.store') }}" method="POST" class="space-y-4 px-6 py-6">
                    @csrf

                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-slate-700">Nama</label>
                        <input name="name" value="{{ old('name') }}" type="text" class="h-11 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-700 outline-none focus:border-slate-400 focus:ring-4 focus:ring-slate-100" required />
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-slate-700">Kontak</label>
                        <input name="contact_name" value="{{ old('contact_name') }}" type="text" class="h-11 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-700 outline-none focus:border-slate-400 focus:ring-4 focus:ring-slate-100" />
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-slate-700">Telepon</label>
                        <input name="phone" value="{{ old('phone') }}" type="text" class="h-11 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-700 outline-none focus:border-slate-400 focus:ring-4 focus:ring-slate-100" />
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-slate-700">Alamat</label>
                        <input name="address" value="{{ old('address') }}" type="text" class="h-11 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-700 outline-none focus:border-slate-400 focus:ring-4 focus:ring-slate-100" />
                    </div>

                    <div class="flex items-center justify-between pt-2">
                        <label class="inline-flex items-center gap-2 text-sm font-semibold text-slate-700">
                            <input name="is_active" type="checkbox" value="1" class="h-4 w-4 rounded border-slate-300" checked />
                            Aktif
                        </label>

                        <div class="flex items-center gap-2">
                            <button type="button" data-close-modal="modal-tambah-supplier" class="inline-flex h-10 items-center justify-center rounded-xl border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-700 hover:bg-slate-50">Batal</button>
                            <button type="submit" class="inline-flex h-10 items-center justify-center rounded-xl bg-slate-900 px-4 text-sm font-semibold text-white hover:bg-slate-800">Simpan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div id="modal-edit-supplier" data-modal class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 p-4">
            <div class="w-full max-w-lg overflow-hidden rounded-2xl bg-white shadow-xl">
                <div class="bg-slate-900 px-6 py-5">
                    <div class="text-lg font-semibold text-white">Edit Supplier</div>
                    <div class="mt-1 text-sm text-slate-200">Perbarui data supplier.</div>
                </div>

                <form data-edit-supplier-form data-action-template="{{ route('masters.suppliers.update', ['supplier' => '__ID__']) }}" action="" method="POST" class="space-y-4 px-6 py-6">
                    @csrf
                    @method('PUT')

                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-slate-700">Nama</label>
                        <input data-edit-supplier-name name="name" type="text" class="h-11 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-700 outline-none focus:border-slate-400 focus:ring-4 focus:ring-slate-100" required />
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-slate-700">Kontak</label>
                        <input data-edit-supplier-contact name="contact_name" type="text" class="h-11 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-700 outline-none focus:border-slate-400 focus:ring-4 focus:ring-slate-100" />
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-slate-700">Telepon</label>
                        <input data-edit-supplier-phone name="phone" type="text" class="h-11 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-700 outline-none focus:border-slate-400 focus:ring-4 focus:ring-slate-100" />
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-slate-700">Alamat</label>
                        <input data-edit-supplier-address name="address" type="text" class="h-11 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-700 outline-none focus:border-slate-400 focus:ring-4 focus:ring-slate-100" />
                    </div>

                    <div class="flex items-center justify-between pt-2">
                        <label class="inline-flex items-center gap-2 text-sm font-semibold text-slate-700">
                            <input data-edit-supplier-active name="is_active" type="checkbox" value="1" class="h-4 w-4 rounded border-slate-300" />
                            Aktif
                        </label>

                        <div class="flex items-center gap-2">
                            <button type="button" data-close-modal="modal-edit-supplier" class="inline-flex h-10 items-center justify-center rounded-xl border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-700 hover:bg-slate-50">Batal</button>
                            <button type="submit" class="inline-flex h-10 items-center justify-center rounded-xl bg-slate-900 px-4 text-sm font-semibold text-white hover:bg-slate-800">Simpan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <script>
        document.addEventListener('click', (e) => {
            const toggleBtn = e.target.closest('[data-toggle-supplier-items]');
            if (!toggleBtn) return;

            const supplierId = toggleBtn.getAttribute('data-toggle-supplier-items');
            const row = document.querySelector(`[data-supplier-items-row="${supplierId}"]`);
            const chevron = document.querySelector(`[data-supplier-chevron="${supplierId}"]`);
            if (!row) return;

            const isHidden = row.classList.contains('hidden');
            if (isHidden) {
                row.classList.remove('hidden');
                if (chevron) chevron.textContent = 'v';
            } else {
                row.classList.add('hidden');
                if (chevron) chevron.textContent = '>';
            }
        });
    </script>
@endsection
