@extends('layouts.app')

@section('title', 'PO Belum Terkirim')
@section('page_title', 'PO Belum Terkirim')
@section('page_description')Daftar purchase order yang belum dilakukan pengiriman.@endsection

@section('content')
    @php
        $isAdmin = auth()->check() && (auth()->user()?->role === 'admin');
    @endphp

    @if(session('success'))
        <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            {{ session('success') }}
        </div>
    @endif
    @if(session('warning'))
        <div class="mb-4 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
            {{ session('warning') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
            {{ session('error') }}
        </div>
    @endif

    <div class="rounded-xl border border-slate-200 bg-white">
        <div class="flex items-center justify-between border-b border-slate-200 p-4">
            <div class="text-sm font-semibold text-slate-900">PO Belum Terkirim</div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-600">
                        <tr>
                            <th class="px-4 py-3 text-center w-12">No</th>
                            <th class="px-4 py-3">Tanggal</th>
                            <th class="px-4 py-3">No Invoice</th>
                            <th class="px-4 py-3">No PO</th>
                            <th class="px-4 py-3">Customer</th>
                            <th class="px-4 py-3">Barang</th>
                            <th class="px-4 py-3">Qty</th>
                            <th class="px-4 py-3">Harga</th>
                            <th class="px-4 py-3">Status</th>
                            @if($isAdmin)
                                <th class="px-4 py-3 text-center">Kirim</th>
                                <th class="px-4 py-3 text-center">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse(($rows ?? []) as $row)
                            <tr class="group hover:bg-slate-50 transition-colors">
                                <td class="px-4 py-3 text-center text-slate-500">{{ $loop->iteration + (($rows->currentPage() - 1) * $rows->perPage()) }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ optional($row->created_at)->timezone(config('app.timezone'))->format('Y-m-d H:i:s') }}</td>
                                <td class="px-4 py-3 font-medium text-slate-900">
                                    <div class="flex flex-col">
                                        <span>{{ $row->invoice_no ?? $row->invoice?->invoice_no ?? '-' }}</span>
                                        @if($row->invoice)
                                            <span class="text-[10px] text-slate-400 font-normal">ID: {{ $row->invoice->id }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-slate-700">{{ $row->po_no ?? $row->invoice?->po_no ?? '-' }}</td>
                                <td class="px-4 py-3 text-slate-700">{{ $row->customer?->name ?? '-' }}</td>
                                <td class="px-4 py-3 text-slate-700">{{ $row->item?->name ?? '-' }}</td>
                                <td class="px-4 py-3 text-slate-700">{{ $row->qty }}</td>
                                <td class="px-4 py-3 text-slate-700">Rp {{ number_format((int) $row->price, 0, ',', '.') }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex rounded-full bg-amber-50 px-2 py-1 text-xs font-medium text-amber-700">{{ strtoupper($row->status ?? 'pending') }}</span>
                                </td>
                                @if($isAdmin)
                                    <td class="px-4 py-3 text-center">
                                        <form action="{{ route('invoices.po_pending.fulfill', ['pending' => $row->id]) }}" method="POST" class="inline-flex">
                                            @csrf
                                            <button type="submit" class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-600 text-white shadow-sm hover:bg-indigo-700 hover:scale-105 transition-all" title="Kirim Otomatis" onclick="return confirm('Kirim otomatis sisa PO ini?')">
                                                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4">
                                                    <path d="M22 2L11 13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                    <path d="M22 2L15 22L11 13L2 9L22 2Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round" />
                                                </svg>
                                            </button>
                                        </form>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <button type="button" 
                                            data-dropdown-trigger
                                            data-row-id="{{ $row->id }}"
                                            data-item-id="{{ $row->item_id }}"
                                            data-qty="{{ $row->qty }}"
                                            class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-500 hover:bg-slate-50 hover:text-slate-900 transition-colors"
                                            aria-label="Aksi">
                                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5">
                                                <path d="M12 13C12.5523 13 13 12.5523 13 12C13 11.4477 12.5523 11 12 11C11.4477 11 11 11.4477 11 12C11 12.5523 11.4477 13 12 13Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M12 6C12.5523 6 13 5.55228 13 5C13 4.44772 12.5523 4 12 4C11.4477 4 11 4.44772 11 5C11 5.55228 11.4477 6 12 6Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M12 20C12.5523 20 13 19.5523 13 19C13 18.4477 12.5523 18 12 18C11.4477 18 11 18.4477 11 19C11 19.5523 11.4477 20 12 20Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </button>

                                        <!-- Dropdown Menu Portal (fixed positioning) -->
                                        <div id="dropdown-menu-{{ $row->id }}" data-dropdown-menu class="hidden fixed z-[9999] w-48 rounded-xl bg-white shadow-2xl ring-1 ring-slate-200 focus:outline-none">
                                            <div class="py-1" role="menu" aria-orientation="vertical">
                                                @php
                                                    $supplierItem = \App\Models\SupplierItem::where('item_id', $row->item_id)->first();
                                                    $supplierId = $supplierItem ? $supplierItem->supplier_id : '';
                                                @endphp
                                                <a href="{{ route('masters.items_supplier') }}?supplier_id={{ $supplierId }}&add_item_id={{ $row->item_id }}&add_qty={{ $row->qty }}" 
                                                    class="flex items-center px-4 py-2 text-sm text-slate-700 hover:bg-indigo-50 hover:text-indigo-700" role="menuitem">
                                                    <svg class="mr-3 h-4 w-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                                    </svg>
                                                    Tambah Stok
                                                </a>
                                                
                                                <button type="button" 
                                                    data-pending-edit 
                                                    data-id="{{ $row->id }}" 
                                                    data-qty="{{ $row->qty }}"
                                                    class="flex w-full items-center px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 text-left" role="menuitem">
                                                    <svg class="mr-3 h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                                    </svg>
                                                    Edit Qty
                                                </button>
                                                
                                                <form action="{{ route('invoices.po_pending.destroy', ['pending' => $row->id]) }}" method="POST" class="block w-full" onsubmit="return confirm('Hapus data pending ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="flex w-full items-center px-4 py-2 text-sm text-rose-600 hover:bg-rose-50 text-left" role="menuitem">
                                                        <svg class="mr-3 h-4 w-4 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                        </svg>
                                                        Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                @endif
                            </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $isAdmin ? 11 : 9 }}" class="px-4 py-6 text-center text-sm text-slate-500">Belum ada PO yang belum terkirim.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($rows->hasPages())
            <div class="border-t border-slate-200 px-4 py-3">
                {{ $rows->links() }}
            </div>
        @endif
    </div>

    @if($isAdmin)
        <div id="modal-preview-pdf" data-modal class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 p-4">
            <div class="w-full max-w-5xl h-[90vh] overflow-hidden rounded-2xl bg-white shadow-xl flex flex-col">
                <div class="bg-slate-900 px-6 py-4 flex items-center justify-between">
                    <div class="text-lg font-semibold text-white">Preview PDF Invoice</div>
                    <button type="button" data-close-modal="modal-preview-pdf" class="text-slate-400 hover:text-white transition-colors">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="flex-1 bg-slate-100 p-4">
                    <iframe id="pdf-preview-frame" src="" class="w-full h-full rounded-lg border border-slate-200 bg-white" frameborder="0"></iframe>
                </div>

                <div class="flex items-center justify-end gap-2 px-6 py-4 border-t border-slate-200">
                    <button type="button" data-close-modal="modal-preview-pdf" class="inline-flex h-10 items-center justify-center rounded-xl border border-slate-200 bg-white px-6 text-sm font-semibold text-slate-700 hover:bg-slate-50">Tutup</button>
                </div>
            </div>
        </div>

        <div id="modal-edit-po-pending" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 p-4" data-pending-modal>
            <div class="w-full max-w-md overflow-hidden rounded-2xl bg-white shadow-xl">
                <div class="bg-slate-900 px-6 py-5">
                    <div class="text-lg font-semibold text-white">Edit Qty Pending</div>
                    <div class="mt-1 text-sm text-slate-200">Ubah qty saja.</div>
                </div>

                <form data-pending-edit-form method="POST" class="space-y-4 px-6 py-6">
                    @csrf
                    @method('PUT')

                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-slate-700">Qty</label>
                        <input name="qty" data-pending-qty type="number" min="1" step="1" class="h-11 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-700 outline-none" required />
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2">
                        <button type="button" data-pending-close class="inline-flex h-10 items-center justify-center rounded-xl border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-700 hover:bg-slate-50">Batal</button>
                        <button type="submit" class="inline-flex h-10 items-center justify-center rounded-xl bg-slate-900 px-4 text-sm font-semibold text-white hover:bg-slate-800">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

        <script>
            (function () {
                const modal = document.querySelector('[data-pending-modal]');
                const form = document.querySelector('[data-pending-edit-form]');
                const qtyInput = document.querySelector('[data-pending-qty]');

                const open = () => {
                    if (!modal) return;
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                };

                const close = () => {
                    if (!modal) return;
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                };

                // Dropdown toggle handler with fixed positioning
                function positionDropdownMenu(trigger, menu) {
                    const rect = trigger.getBoundingClientRect();
                    const menuHeight = menu.offsetHeight || 150;
                    const menuWidth = menu.offsetWidth || 192;
                    
                    // Calculate position (below the button, aligned to right)
                    let top = rect.bottom + window.scrollY + 4;
                    let left = rect.right + window.scrollX - menuWidth;
                    
                    // Check if menu goes below viewport
                    const viewportHeight = window.innerHeight;
                    if (top + menuHeight > viewportHeight + window.scrollY) {
                        // Show above the button instead
                        top = rect.top + window.scrollY - menuHeight - 4;
                    }
                    
                    menu.style.top = `${top}px`;
                    menu.style.left = `${left}px`;
                }

                document.addEventListener('click', (e) => {
                    const trigger = e.target.closest('[data-dropdown-trigger]');
                    if (trigger) {
                        const rowId = trigger.getAttribute('data-row-id');
                        const menu = document.getElementById(`dropdown-menu-${rowId}`);
                        if (menu) {
                            // Close all other dropdowns first
                            document.querySelectorAll('[data-dropdown-menu]').forEach(m => {
                                if (m !== menu) m.classList.add('hidden');
                            });
                            
                            if (menu.classList.contains('hidden')) {
                                positionDropdownMenu(trigger, menu);
                                menu.classList.remove('hidden');
                            } else {
                                menu.classList.add('hidden');
                            }
                        }
                        return;
                    }

                    // Close dropdown when clicking outside
                    const clickedMenu = e.target.closest('[data-dropdown-menu]');
                    if (!clickedMenu && !e.target.closest('[data-dropdown-trigger]')) {
                        document.querySelectorAll('[data-dropdown-menu]').forEach(m => m.classList.add('hidden'));
                    }
                });

                // Auto-preview PDF after successful fulfill
                @if(session('show_pdf_preview'))
                (function() {
                    const previewUrl = @json(session('show_pdf_preview'));
                    if (previewUrl) {
                        setTimeout(() => {
                            const iframe = document.getElementById('pdf-preview-frame');
                            const modalPreview = document.getElementById('modal-preview-pdf');
                            if (iframe && modalPreview) {
                                iframe.src = previewUrl;
                                modalPreview.classList.remove('hidden');
                                modalPreview.classList.add('flex');
                            }
                        }, 300);
                    }
                })();
                @endif

                document.addEventListener('click', (e) => {
                    const previewBtn = e.target.closest('[data-open-modal="modal-preview-pdf"]');
                    if (previewBtn) {
                        const pdfUrl = previewBtn.getAttribute('data-pdf-url');
                        const iframe = document.getElementById('pdf-preview-frame');
                        const modalPreview = document.getElementById('modal-preview-pdf');
                        if (iframe && pdfUrl) {
                            iframe.src = pdfUrl;
                        }
                        if (modalPreview) {
                            modalPreview.classList.remove('hidden');
                            modalPreview.classList.add('flex');
                        }
                        return;
                    }

                    const closePreviewBtn = e.target.closest('[data-close-modal="modal-preview-pdf"]');
                    if (closePreviewBtn) {
                        const iframe = document.getElementById('pdf-preview-frame');
                        const modalPreview = document.getElementById('modal-preview-pdf');
                        if (iframe) {
                            iframe.src = '';
                        }
                        if (modalPreview) {
                            modalPreview.classList.add('hidden');
                            modalPreview.classList.remove('flex');
                        }
                        return;
                    }

                    const editBtn = e.target.closest('[data-pending-edit]');
                    if (editBtn) {
                        // Close dropdown first
                        document.querySelectorAll('[data-dropdown-menu]').forEach(m => m.classList.add('hidden'));
                        const id = editBtn.getAttribute('data-id');
                        const qty = editBtn.getAttribute('data-qty');
                        if (form && id) {
                            form.setAttribute('action', `{{ route('invoices.po_pending.update', ['pending' => '__ID__']) }}`.replace('__ID__', String(id)));
                        }
                        if (qtyInput) qtyInput.value = String(qty ?? '1');
                        open();
                        return;
                    }

                    const closeBtn = e.target.closest('[data-pending-close]');
                    if (closeBtn) {
                        close();
                        return;
                    }

                    if (modal && e.target === modal) {
                        close();
                    }
                });
            })();
        </script>
    @endif
@endsection
