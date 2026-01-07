@extends('layouts.app')

@section('title', 'PO Belum Terkirim')
@section('page_title', 'PO Belum Terkirim')
@section('page_description')Daftar purchase order yang belum dilakukan pengiriman.@endsection

@section('content')
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
            <button type="button" class="inline-flex items-center gap-2 rounded-md border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Export</button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-600">
                    <tr>
                        <th class="px-4 py-3">Tanggal</th>
                        <th class="px-4 py-3">No Invoice</th>
                        <th class="px-4 py-3">No PO</th>
                        <th class="px-4 py-3">Customer</th>
                        <th class="px-4 py-3">Barang</th>
                        <th class="px-4 py-3">Qty</th>
                        <th class="px-4 py-3">Harga</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                        <th class="px-4 py-3 text-center">Kirim</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse(($rows ?? []) as $row)
                        <tr>
                            <td class="px-4 py-3 text-slate-600">{{ optional($row->created_at)->timezone(config('app.timezone'))->format('Y-m-d H:i:s') }}</td>
                            <td class="px-4 py-3 font-medium text-slate-900">{{ $row->invoice_no ?? $row->invoice?->invoice_no ?? '-' }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $row->po_no ?? $row->invoice?->po_no ?? '-' }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $row->customer?->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $row->item?->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $row->qty }}</td>
                            <td class="px-4 py-3 text-slate-700">Rp {{ number_format((int) $row->price, 0, ',', '.') }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex rounded-full bg-amber-50 px-2 py-1 text-xs font-medium text-amber-700">{{ strtoupper($row->status ?? 'pending') }}</span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="inline-flex items-center justify-center gap-2">
                                    <button type="button" data-pending-edit data-id="{{ $row->id }}" data-qty="{{ $row->qty }}" class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-700 hover:bg-slate-50" aria-label="Edit Qty">
                                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4">
                                            <path d="M12 20H21" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                                            <path d="M16.5 3.5C17.3284 2.67157 18.6716 2.67157 19.5 3.5C20.3284 4.32843 20.3284 5.67157 19.5 6.5L8 18L3 19L4 14L16.5 3.5Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round" />
                                        </svg>
                                    </button>

                                    <form action="{{ route('invoices.po_pending.destroy', ['pending' => $row->id]) }}" method="POST" class="inline-flex" onsubmit="return confirm('Hapus data pending ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-rose-200 bg-rose-50 text-rose-700 hover:bg-rose-100" aria-label="Hapus">
                                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4">
                                                <path d="M3 6H21" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                                                <path d="M8 6V4H16V6" stroke="currentColor" stroke-width="2" stroke-linejoin="round" />
                                                <path d="M19 6L18 20H6L5 6" stroke="currentColor" stroke-width="2" stroke-linejoin="round" />
                                                <path d="M10 11V17" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                                                <path d="M14 11V17" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <form action="{{ route('invoices.po_pending.fulfill', ['pending' => $row->id]) }}" method="POST" class="inline-flex">
                                    @csrf
                                    <button type="submit" class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-600 text-white hover:bg-indigo-700" aria-label="Kirim Otomatis" onclick="return confirm('Kirim otomatis sisa PO ini?')">
                                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4">
                                            <path d="M22 2L11 13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            <path d="M22 2L15 22L11 13L2 9L22 2Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round" />
                                        </svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-4 py-6 text-center text-sm text-slate-500">Belum ada PO yang belum terkirim.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
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

            document.addEventListener('click', (e) => {
                const editBtn = e.target.closest('[data-pending-edit]');
                if (editBtn) {
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
@endsection
