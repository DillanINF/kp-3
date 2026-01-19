@extends('layouts.app')

@section('title', 'Data Invoice')
@section('page_title', 'Data Invoice')
@section('page_description')

@section('content')
    <div class="space-y-6">
        @php
            $isAdmin = auth()->check() && (auth()->user()?->role === 'admin');
        @endphp

        @if(session('success'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800">
                {{ session('success') }}
            </div>
        @endif
        @if(session('warning'))
            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
                {{ session('warning') }}
            </div>
        @endif
        @if(session('error'))
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
                            <th class="px-4 py-3">Tanggal</th>
                            <th class="px-4 py-3">No Invoice</th>
                            <th class="px-4 py-3">Customer</th>
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
                                <td class="px-4 py-3 text-slate-700">{{ optional($invoice->date)->format('d/m/Y') }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center justify-center rounded-full bg-indigo-100 px-3 py-1 text-xs font-semibold text-indigo-700">{{ $invoice->invoice_no }}</span>
                                </td>
                                <td class="px-4 py-3 text-slate-700">{{ $invoice->customer?->name ?? '-' }}</td>
                                <td class="px-4 py-3 text-slate-500">{{ $invoice->po_no ?? '-' }}</td>
                                <td class="px-4 py-3 text-slate-500">{{ $invoice->grand_total > 0 ? 'Rp ' . number_format($invoice->grand_total, 0, ',', '.') : '-' }}</td>
                                <td class="px-4 py-3 text-slate-500">{{ $invoice->qty_total > 0 ? $invoice->qty_total : '-' }}</td>
                                @if($isAdmin)
                                    <td class="px-4 py-3 text-center">
                                        <div class="inline-flex items-center justify-center gap-2">
                                            <a href="{{ route('invoices.pdf', $invoice) }}" class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-indigo-200 bg-indigo-50 text-indigo-700 hover:bg-indigo-100" aria-label="PDF" title="Download PDF">
                                                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4">
                                                    <path d="M7 3H14L18 7V21H7V3Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                                                    <path d="M14 3V7H18" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                                                    <path d="M9 12H15" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                                    <path d="M9 16H13" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                                </svg>
                                            </a>

                                            <form action="{{ route('invoices.destroy', $invoice) }}" method="POST" onsubmit="return {{ $isEmpty ? "confirm('Hapus invoice ini?')" : 'false' }}" class="inline-flex">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" data-action="delete-invoice" class="inline-flex h-9 w-9 items-center justify-center rounded-xl border {{ $isEmpty ? 'border-rose-200 bg-rose-50 text-rose-700 hover:bg-rose-100' : 'border-slate-200 bg-slate-50 text-slate-400 cursor-not-allowed' }}" aria-label="Hapus" {{ $isEmpty ? '' : 'disabled' }}>
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
                        @empty
                            <tr>
                                <td colspan="{{ $isAdmin ? 7 : 6 }}" class="px-4 py-6 text-center text-sm text-slate-500">Belum ada data invoice.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
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
        })();
    </script>

    @if($isAdmin)
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
                        <select name="customer_id" class="h-11 w-full rounded-xl border border-indigo-200 bg-white px-3 text-sm text-slate-700 outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100">
                            <option value="">-- Pilih Customer --</option>
                            @foreach($customers ?? [] as $customer)
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
