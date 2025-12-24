@extends('layouts.app')

@section('title', 'Data Invoice')
@section('page_title', 'Data Invoice')
@section('page_description')

@section('content')
    <div class="space-y-6">
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
                                <span data-invoice-total-count>1</span>
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
                        <button type="button" data-open-modal="modal-atur-invoice" class="inline-flex items-center gap-2 rounded-xl bg-amber-500 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-amber-600">
                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4">
                                <path d="M12 8V12L14.5 13.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2"/>
                            </svg>
                            Atur No Invoice
                        </button>
                        <button type="button" data-open-modal="modal-tambah-invoice" class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700">
                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4">
                                <path d="M12 5V19" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                <path d="M5 12H19" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                            Tambah No Invoice
                        </button>
                    </div>

                    <div class="relative">
                        <input data-invoice-search type="text" placeholder="Cari no invoice..." class="h-10 w-full rounded-xl border border-slate-200 bg-white pl-10 pr-3 text-sm text-slate-700 shadow-sm outline-none placeholder:text-slate-400 focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100 md:w-[260px]" />
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

        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
            <span class="font-semibold">Peringatan:</span>
            Setelah tanda terima ditandatangani, ubah status invoice pada kolom <span class="font-semibold">Status</span> menjadi <span class="font-semibold">Accept</span>. Sistem akan menyinkronkan data ke Jatuh Tempo agar penagihan berjalan tepat waktu.
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

                <span data-invoice-count-badge class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">1 data</span>
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
                            <th class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200" data-invoice-rows data-input-po-url="{{ route('invoices.input_po') }}">
                        <tr class="cursor-pointer transition-colors hover:bg-indigo-50 active:bg-indigo-100" data-invoice-no="1">
                            <td class="px-4 py-3 text-slate-700">23/12/2025</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-indigo-100 text-xs font-semibold text-indigo-700">1</span>
                            </td>
                            <td class="px-4 py-3 text-slate-700">Dillan Inf</td>
                            <td class="px-4 py-3 text-slate-500">-</td>
                            <td class="px-4 py-3 text-slate-500">-</td>
                            <td class="px-4 py-3 text-slate-500">-</td>
                            <td class="px-4 py-3">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <div class="flex items-center justify-center gap-2">
                                        <button type="button" data-action="edit-invoice-row" class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-600 hover:bg-slate-50" aria-label="Edit">
                                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4">
                                                <path d="M12 20H21" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                                <path d="M16.5 3.5C17.3284 2.67157 18.6716 2.67157 19.5 3.5C20.3284 4.32843 20.3284 5.67157 19.5 6.5L8 18L3 19L4 14L16.5 3.5Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                                            </svg>
                                        </button>

                                        <button type="button" data-action="delete-invoice-row" class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-rose-200 bg-rose-50 text-rose-700 hover:bg-rose-100" aria-label="Delete">
                                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4">
                                                <path d="M3 6H21" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                                <path d="M8 6V4H16V6" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                                                <path d="M19 6L18 20H6L5 6" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                                                <path d="M10 11V17" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                                <path d="M14 11V17" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="modal-tambah-invoice" data-modal class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 p-4">
        <div class="w-full max-w-lg overflow-hidden rounded-2xl bg-white shadow-xl">
            <div class="bg-indigo-600 px-6 py-5">
                <div class="text-lg font-semibold text-white">Pilih Customer untuk Tambah No Invoice</div>
                <div class="mt-1 text-sm text-indigo-100">Customer wajib dipilih sebelum melanjutkan</div>
            </div>

            <div class="space-y-4 px-6 py-6">
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-slate-700">Customer</label>
                    <select data-tambah-invoice-customer class="h-11 w-full rounded-xl border border-indigo-200 bg-white px-3 text-sm text-slate-700 outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100">
                        <option value="">-- Pilih Customer --</option>
                        @foreach($customers ?? [] as $customer)
                            <option value="{{ $customer->id }}" data-name="{{ $customer->name }}">{{ $customer->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" data-close-modal="modal-tambah-invoice" class="inline-flex h-10 items-center justify-center rounded-xl border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-700 hover:bg-slate-50">Batal</button>
                    <button id="btn-tambah-invoice-lanjut" type="button" class="inline-flex h-10 items-center justify-center rounded-xl bg-indigo-600 px-4 text-sm font-semibold text-white hover:bg-indigo-700">Lanjut</button>
                </div>
            </div>
        </div>
    </div>

    <div id="modal-atur-invoice" data-modal class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 p-4">
        <div class="w-full max-w-lg overflow-hidden rounded-2xl bg-white shadow-xl">
            <div class="bg-amber-500 px-6 py-5">
                <div class="text-lg font-semibold text-white">Pilih Customer untuk Atur No Invoice</div>
                <div class="mt-1 text-sm text-amber-100">Customer wajib dipilih sebelum melanjutkan</div>
            </div>

            <div class="space-y-4 px-6 py-6">
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-slate-700">Customer</label>
                    <select data-atur-invoice-customer class="h-11 w-full rounded-xl border border-amber-200 bg-white px-3 text-sm text-slate-700 outline-none focus:border-amber-400 focus:ring-4 focus:ring-amber-100">
                        <option value="">-- Pilih Customer --</option>
                        @foreach($customers ?? [] as $customer)
                            <option value="{{ $customer->id }}" data-name="{{ $customer->name }}">{{ $customer->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-semibold text-slate-700">No Invoice Berikutnya</label>
                    <div class="relative">
                        <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">#</span>
                        <input data-atur-invoice-next type="number" value="1000" class="h-11 w-full rounded-xl border border-slate-200 bg-white pl-8 pr-3 text-sm text-slate-700 outline-none focus:border-amber-400 focus:ring-4 focus:ring-amber-100" />
                    </div>
                    <div class="text-xs text-slate-500">Contoh: 1000, 2000, 5000</div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" data-close-modal="modal-atur-invoice" class="inline-flex h-10 items-center justify-center rounded-xl border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-700 hover:bg-slate-50">Batal</button>
                    <button id="btn-atur-invoice-lanjut" type="button" class="inline-flex h-10 items-center justify-center rounded-xl bg-amber-500 px-4 text-sm font-semibold text-white hover:bg-amber-600">Lanjut</button>
                </div>
            </div>
        </div>
    </div>
@endsection
