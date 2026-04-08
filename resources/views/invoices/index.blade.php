@extends('layouts.app')

@section('title', 'Data Invoice')
@section('page_title', 'Data Invoice')
@section('page_description')

@section('content')
    <div class="space-y-6">
        @php
            $isAdmin = auth()->check() && (auth()->user()?->role === 'admin');
        @endphp

        @if(false && session('success'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800">
                {{ session('success') }}
            </div>
        @endif
        @if(false && session('warning'))
            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
                {{ session('warning') }}
            </div>
        @endif
        @if(false && session('error'))
            <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800">
                {{ session('error') }}
            </div>
        @endif

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div class="flex items-start gap-3">
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-600 text-white">
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5">
                            <path d="M8 3H16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            <path d="M9 3V7" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            <path d="M15 3V7" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            <path d="M6 7H18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            <path d="M7 7V20H17V7" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                            <path d="M9 11H15" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            <path d="M9 15H13" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    </span>

                    <div class="min-w-0">
                        <div class="text-lg font-semibold text-slate-900">Data Invoice</div>
                        <div class="mt-0.5 text-sm text-slate-500">Kelola invoice Purchase Order dengan mudah</div>
                        <div class="mt-2 flex flex-wrap items-center gap-3 text-xs text-slate-500">
                            <span class="inline-flex items-center gap-1">
                                <span class="font-medium text-slate-700">Total:</span>
                                <span>{{ ($invoices ?? collect())->count() }}</span>
                            </span>
                            <span class="inline-flex items-center gap-1">
                                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4">
                                    <path d="M7 3V6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                    <path d="M17 3V6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                    <path d="M4 9H20" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                    <path d="M6 6H18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                    <path d="M5 6V20H19V6" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                                </svg>
                                {{ now()->format('d M Y') }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="flex w-full flex-col gap-3 md:w-auto md:flex-row md:items-center md:justify-end">
                    <div class="flex flex-wrap items-center gap-2">
                        @if($isAdmin)
                            <button type="button" data-open-modal="modal-tambah-invoice" class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700">
                                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4">
                                    <path d="M12 5V19" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                    <path d="M5 12H19" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                                Tambah No Invoice
                            </button>
                        @endif
                    </div>

                    <div class="relative">
                        <input type="text" placeholder="Cari no invoice..." class="h-10 w-full rounded-xl border border-slate-200 bg-white pl-10 pr-3 text-sm text-slate-700 shadow-sm outline-none placeholder:text-slate-400 focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100 md:w-[260px]" />
                        <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4">
                                <path d="M21 21L16.65 16.65" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                <path d="M10.5 18C14.6421 18 18 14.6421 18 10.5C18 6.35786 14.6421 3 10.5 3C6.35786 3 3 6.35786 3 10.5C3 14.6421 6.35786 18 10.5 18Z" stroke="currentColor" stroke-width="2"/>
                            </svg>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex flex-col gap-3 border-b border-slate-200 p-4 md:flex-row md:items-center md:justify-between">
                <div class="flex items-start gap-3">
                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-50 text-indigo-700">
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5">
                            <path d="M7 7H17" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            <path d="M7 12H17" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            <path d="M7 17H13" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            <path d="M5 4H19V20H5V4Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                        </svg>
                    </span>

                    <div class="min-w-0">
                        <div class="text-sm font-semibold text-slate-900">Daftar Invoice</div>
                        <div class="mt-0.5 text-xs text-slate-500">Klik 2x pada baris untuk isi data invoice/PO dan masuk ke form input PO</div>
                    </div>
                </div>

                <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">{{ ($invoices ?? collect())->count() }} data</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-600">
                        <tr>
                            <th class="px-4 py-3 text-center w-12">No</th>
                            <th class="px-4 py-3">Tgl. Invoice</th>
                            <th class="px-4 py-3">Tgl. Kirim</th>
                            <th class="px-4 py-3">No Invoice</th>
                            <th class="px-4 py-3">Customer</th>
                            <th class="px-4 py-3">Alamat Kirim</th>
                            <th class="px-4 py-3">Pengirim</th>
                            <th class="px-4 py-3">No PO</th>
                            <th class="px-4 py-3">Total PO</th>
                            <th class="px-4 py-3">Qty</th>
                            @if($isAdmin)
                                <th class="px-4 py-3 text-center">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200" data-invoice-rows data-input-po-url="{{ route('invoices.input_po_by_no', ['invoiceNo' => '__NO__']) }}">
                        @forelse($invoices ?? [] as $invoice)
                            @php
                                $isDraft = ($invoice->status ?? '') === 'draft';
                                $pendingCount = (int) ($invoice->po_pending_items_count ?? 0);
                                $isEmpty = $isDraft
                                    && (int) ($invoice->grand_total ?? 0) === 0
                                    && (int) ($invoice->qty_total ?? 0) === 0
                                    && $pendingCount <= 0;
                            @endphp
                            <tr class="transition-colors hover:bg-indigo-50 {{ $isAdmin && ($invoice->status ?? '') === 'draft' ? 'cursor-pointer' : '' }}" data-po-editable="{{ $isAdmin && ($invoice->status ?? '') === 'draft' ? '1' : '0' }}" data-input-po-href="{{ route('invoices.input_po_by_no', ['invoiceNo' => $invoice->invoice_no]) }}" data-invoice-no-str="{{ $invoice->invoice_no }}" data-invoice-id="{{ $invoice->id }}" data-invoice-no="{{ (int) preg_replace('/\D+/', '', (string) $invoice->invoice_no) }}">
                                <td class="px-4 py-3 text-center text-slate-500">{{ $loop->iteration + (($invoices->currentPage() - 1) * $invoices->perPage()) }}</td>
                                <td class="px-4 py-3 text-slate-700">{{ optional($invoice->date)->format('d/m/Y') }}</td>
                                <td class="px-4 py-3 text-slate-700">
                                    @if($invoice->delivery_date)
                                        <span class="inline-flex items-center gap-1.5 rounded-md bg-indigo-50 px-2 py-1 text-xs font-medium text-indigo-700 ring-1 ring-inset ring-indigo-700/10">
                                            {{ $invoice->delivery_date->format('d/m/Y') }}
                                        </span>
                                    @else
                                        <span class="text-slate-400 italic text-xs">- belum diset -</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center justify-center rounded-full bg-indigo-100 px-3 py-1 text-xs font-semibold text-indigo-700">{{ $invoice->invoice_no }}</span>
                                </td>
                                <td class="px-4 py-3 text-slate-700">{{ $invoice->customer?->name ?? '-' }}</td>
                                <td class="px-4 py-3 text-slate-500">
                                    <div class="max-w-[200px] truncate" title="{{ $invoice->address }}">
                                        {{ $invoice->address ?? '-' }}
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-slate-700">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-slate-100 text-slate-800">
                                        {{ $invoice->pengirim?->name ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-slate-500">{{ $invoice->po_no ?? '-' }}</td>
                                <td class="px-4 py-3 text-slate-500">{{ $invoice->grand_total > 0 ? 'Rp ' . number_format($invoice->grand_total, 0, ',', '.') : '-' }}</td>
                                <td class="px-4 py-3 text-slate-500">{{ $invoice->qty_total > 0 ? $invoice->qty_total : '-' }}</td>
                                @if($isAdmin)
                                    <td class="px-4 py-3 text-center">
                                        <button type="button" 
                                            data-dropdown-trigger
                                            data-invoice-id="{{ $invoice->id }}"
                                            data-pdf-url="{{ route('invoices.pdf', $invoice) }}?preview=1"
                                            data-download-url="{{ route('invoices.pdf', $invoice) }}"
                                            data-delete-url="{{ route('invoices.destroy', $invoice) }}"
                                            class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-500 hover:bg-slate-50 hover:text-slate-900 transition-colors"
                                            aria-label="Aksi">
                                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5">
                                                <path d="M12 13C12.5523 13 13 12.5523 13 12C13 11.4477 12.5523 11 12 11C11.4477 11 11 11.4477 11 12C11 12.5523 11.4477 13 12 13Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M12 6C12.5523 6 13 5.55228 13 5C13 4.44772 12.5523 4 12 4C11.4477 4 11 4.44772 11 5C11 5.55228 11.4477 6 12 6Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M12 20C12.5523 20 13 19.5523 13 19C13 18.4477 12.5523 18 12 18C11.4477 18 11 18.4477 11 19C11 19.5523 11.4477 20 12 20Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </button>

                                        <!-- Dropdown Menu Portal (fixed positioning) -->
                                        <div id="dropdown-menu-invoice-{{ $invoice->id }}" data-dropdown-menu class="hidden fixed z-[9999] w-48 rounded-xl bg-white shadow-2xl ring-1 ring-slate-200 focus:outline-none">
                                            <div class="py-1" role="menu" aria-orientation="vertical">
                                                <button type="button" 
                                                    data-open-modal="modal-preview-pdf"
                                                    data-pdf-url="{{ route('invoices.pdf', $invoice) }}?preview=1"
                                                    class="flex w-full items-center px-4 py-2 text-sm text-slate-700 hover:bg-indigo-50 hover:text-indigo-700 text-left" role="menuitem">
                                                    <svg class="mr-3 h-4 w-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                    </svg>
                                                    Preview PDF
                                                </button>
                                                
                                                <a href="{{ route('invoices.pdf', $invoice) }}" 
                                                    class="flex items-center px-4 py-2 text-sm text-slate-700 hover:bg-slate-50" role="menuitem">
                                                    <svg class="mr-3 h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                    </svg>
                                                    Download PDF
                                                </a>
                                                
                                                <form action="{{ route('invoices.destroy', $invoice) }}" method="POST" class="block w-full" onsubmit="return confirm('Hapus invoice ini? (Data stok dan laporan akan disesuaikan)')">
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
                                <td colspan="{{ $isAdmin ? 11 : 10 }}" class="px-4 py-6 text-center text-sm text-slate-500">Belum ada data invoice.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($invoices->hasPages())
                <div class="border-t border-slate-200 px-4 py-3">
                    {{ $invoices->links() }}
                </div>
            @endif
        </div>
    </div>

    <script>
        (function () {
            window.addEventListener('pageshow', function (e) {
                if (e && e.persisted) {
                    window.location.reload();
                }
            });

            document.addEventListener('dblclick', function (e) {
                const isAdmin = {{ $isAdmin ? 'true' : 'false' }};
                if (!isAdmin) return;

                const actionArea = e.target.closest('button, a, form, [data-action], [data-open-modal], [data-close-modal]');
                if (actionArea) return;

                const row = e.target.closest('[data-invoice-rows] tr');
                if (!row) return;

                const editable = row.getAttribute('data-po-editable');
                if (String(editable) !== '1') {
                    return;
                }

                const href = row.getAttribute('data-input-po-href');
                if (href && String(href).trim().length > 0) {
                    window.location.href = String(href);
                }
            });

            // Dropdown toggle handler with fixed positioning
            function positionDropdownMenu(trigger, menu) {
                const rect = trigger.getBoundingClientRect();
                const menuHeight = menu.offsetHeight || 150;
                const menuWidth = menu.offsetWidth || 192;
                
                let top = rect.bottom + window.scrollY + 4;
                let left = rect.right + window.scrollX - menuWidth;
                
                const viewportHeight = window.innerHeight;
                if (top + menuHeight > viewportHeight + window.scrollY) {
                    top = rect.top + window.scrollY - menuHeight - 4;
                }
                
                menu.style.top = `${top}px`;
                menu.style.left = `${left}px`;
            }

            document.addEventListener('click', function (e) {
                // Handle dropdown toggle
                const trigger = e.target.closest('[data-dropdown-trigger]');
                if (trigger) {
                    const invoiceId = trigger.getAttribute('data-invoice-id');
                    const menu = document.getElementById(`dropdown-menu-invoice-${invoiceId}`);
                    if (menu) {
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

                // Handle preview modal
                const previewBtn = e.target.closest('[data-open-modal="modal-preview-pdf"]');
                if (previewBtn) {
                    // Close dropdown first
                    document.querySelectorAll('[data-dropdown-menu]').forEach(m => m.classList.add('hidden'));
                    
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
                }
            });
        })();
    </script>

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

        <div id="modal-tambah-invoice" data-modal class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 p-4">
            <div class="w-full max-w-lg overflow-hidden rounded-2xl bg-white shadow-xl">
                <div class="bg-indigo-600 px-6 py-5">
                    <div class="text-lg font-semibold text-white">Pilih Customer untuk Tambah No Invoice</div>
                    <div class="mt-1 text-sm text-indigo-100">Customer wajib dipilih sebelum melanjutkan</div>
                </div>

                <form action="{{ route('invoices.store') }}" method="POST" class="space-y-4 px-6 py-6">
                    @csrf
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-slate-700">Customer</label>
                        <select name="customer_id" class="h-11 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-700 outline-none focus:border-slate-400 focus:ring-4 focus:ring-slate-100" required>
                            <option value="">Pilih Customer</option>
                            @foreach ($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2">
                        <button type="button" data-close-modal="modal-tambah-invoice" class="inline-flex h-10 items-center justify-center rounded-xl border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-700 hover:bg-slate-50">Batal</button>
                        <button type="submit" class="inline-flex h-10 items-center justify-center rounded-xl bg-indigo-600 px-4 text-sm font-semibold text-white hover:bg-indigo-700">Lanjut</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
@endsection
