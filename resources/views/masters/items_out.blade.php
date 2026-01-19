@extends('layouts.app')

@section('title', 'Data Barang Keluar')
@section('page_title', 'Data Barang Keluar')
@section('page_description')Pencatatan barang keluar (tujuan: customer).@endsection

@section('content')
    <div class="space-y-4">
        @if(session('error'))
            <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800">
                {{ session('error') }}
            </div>
        @endif
        @if($errors->any())
            <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800">
                {{ $errors->first() }}
            </div>
        @endif

    <div class="rounded-xl border border-slate-200 bg-white">
            <div class="flex items-center justify-between border-b border-slate-200 p-4">
                <div>
                    <div class="text-sm font-semibold text-slate-900">Riwayat Barang Keluar</div>
                    <div class="mt-1 text-sm text-slate-600">Tipe: penjualan / rusak / expired / lainnya.</div>
                </div>
                <div class="flex items-center gap-2">
                    @php
                        $months = [
                            1 => 'Jan',
                            2 => 'Feb',
                            3 => 'Mar',
                            4 => 'Apr',
                            5 => 'Mei',
                            6 => 'Jun',
                            7 => 'Jul',
                            8 => 'Agu',
                            9 => 'Sep',
                            10 => 'Okt',
                            11 => 'Nov',
                            12 => 'Des',
                        ];
                    @endphp
                    <form method="GET" action="{{ route('masters.items_out') }}" class="flex items-center gap-2">
                        <select name="month" class="h-10 rounded-md border border-slate-200 bg-white px-3 text-sm text-slate-700 outline-none" onchange="this.form.submit()">
                            @foreach($months as $m => $label)
                                <option value="{{ $m }}" {{ (int) ($selectedMonth ?? now()->month) === (int) $m ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        <select name="year" class="h-10 rounded-md border border-slate-200 bg-white px-3 text-sm text-slate-700 outline-none" onchange="this.form.submit()">
                            @foreach(($years ?? []) as $y)
                                <option value="{{ $y }}" {{ (int) ($selectedYear ?? now()->year) === (int) $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                    </form>
                    @if(auth()->user()?->role === 'admin')
                        <button type="button" data-open-modal="modal-tambah-item-out" class="inline-flex h-10 items-center justify-center rounded-md bg-slate-900 px-4 text-sm font-semibold text-white hover:bg-slate-800">Tambah</button>
                    @endif
                </div>
            </div>

            @php
                $lossHistory = ($history ?? collect())->whereIn('type', ['damaged', 'expired', 'other']);
            @endphp

            <div class="grid grid-cols-1 gap-4 p-4 xl:grid-cols-2">
                <div class="overflow-hidden rounded-xl border border-slate-200">
                    <div class="border-b border-slate-200 bg-slate-50 px-4 py-3">
                        <div class="text-sm font-semibold text-slate-900">Penjualan</div>
                    </div>
                    <div class="space-y-3 p-4">
                        @forelse(($salesInvoices ?? []) as $inv)
                        <details class="group overflow-hidden rounded-lg border border-slate-200 bg-white">
                            <summary class="flex cursor-pointer list-none items-center justify-between gap-3 p-3">
                                <div class="grid min-w-0 flex-1 grid-cols-2 gap-3 text-sm">
                                    <div class="truncate text-slate-600">{{ $inv->created_at?->timezone(config('app.timezone'))->format('d/m/Y H:i') ?? ($inv->date?->format('d/m/Y') ?? '-') }}</div>
                                    <div class="truncate text-right font-semibold text-slate-900">{{ $inv->invoice_no ?? '-' }}</div>
                                </div>
                                <span class="inline-flex h-8 w-8 flex-none items-center justify-center rounded-md border border-slate-200 text-slate-600 group-open:bg-slate-50">
                                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform group-open:rotate-180">
                                        <path d="M6 9L12 15L18 9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </span>
                            </summary>
                            <div class="border-t border-slate-200">
                                <div class="flex flex-col gap-2 p-3 sm:flex-row sm:items-center sm:justify-between">
                                    <div class="text-sm">
                                        <div class="font-semibold text-slate-900">PO: {{ $inv->po_no ?? '-' }}</div>
                                        <div class="text-slate-600">Customer: {{ $inv->customer?->name ?? '-' }}</div>
                                    </div>
                                </div>
                                <div class="overflow-x-auto">
                                    <table class="w-full text-left text-sm">
                                        <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-600">
                                            <tr>
                                                <th class="px-4 py-3">Barang</th>
                                                <th class="px-4 py-3 text-right">Qty</th>
                                                <th class="px-4 py-3 text-right">Harga</th>
                                                <th class="px-4 py-3 text-right">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-200">
                                            @forelse(($inv->details ?? []) as $d)
                                                <tr>
                                                    <td class="px-4 py-3 text-slate-700">{{ $d->item?->name ?? '-' }}</td>
                                                    <td class="px-4 py-3 text-right text-slate-700">{{ (int) ($d->qty ?? 0) }}</td>
                                                    <td class="px-4 py-3 text-right text-slate-700">Rp {{ number_format((int) ($d->price ?? 0), 0, ',', '.') }}</td>
                                                    <td class="px-4 py-3 text-right font-semibold text-slate-900">Rp {{ number_format((int) ($d->total ?? 0), 0, ',', '.') }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="px-4 py-6 text-center text-sm text-slate-500">Belum ada item.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </details>
                        @empty
                            <div class="px-4 py-6 text-center text-sm text-slate-500">Belum ada data penjualan.</div>
                        @endforelse
                    </div>
                </div>

                <div class="overflow-hidden rounded-xl border border-slate-200">
                    <div class="border-b border-slate-200 bg-slate-50 px-4 py-3">
                        <div class="text-sm font-semibold text-slate-900">Kerugian (Rusak/Expired/Lainnya)</div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-white text-xs font-semibold uppercase tracking-wide text-slate-600">
                                <tr>
                                    <th class="px-4 py-3">Tanggal</th>
                                    <th class="px-4 py-3">Penyebab</th>
                                    <th class="px-4 py-3">Barang</th>
                                    <th class="px-4 py-3">Qty</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200">
                                @forelse($lossHistory as $row)
                                    @php
                                        $cause = strtoupper((string) ($row->type ?? '-'));
                                        if (($row->type ?? '') === 'damaged') {
                                            $cause = 'RUSAK';
                                        }
                                        if (($row->type ?? '') === 'expired') {
                                            $cause = 'EXPIRED';
                                        }
                                        if (($row->type ?? '') === 'other') {
                                            $cause = 'LAINNYA';
                                        }
                                    @endphp
                                    <tr>
                                        <td class="px-4 py-3 text-slate-600">{{ $row->created_at?->timezone(config('app.timezone'))->format('d/m/Y H:i:s') }}</td>
                                        <td class="px-4 py-3 text-slate-700">{{ $cause }}</td>
                                        <td class="px-4 py-3 text-slate-700">{{ $row->item?->name ?? '-' }}</td>
                                        <td class="px-4 py-3 text-slate-700">{{ $row->qty }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-6 text-center text-sm text-slate-500">Belum ada data kerugian.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
    </div>
    </div>

    @if(auth()->user()?->role === 'admin')
        <div id="modal-tambah-item-out" data-modal class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 p-4">
            <div class="w-full max-w-lg overflow-hidden rounded-2xl bg-white shadow-xl">
                <div class="bg-slate-900 px-6 py-5">
                    <div class="text-lg font-semibold text-white">Tambah Kerugian</div>
                    <div class="mt-1 text-sm text-slate-200">Catat kerugian (rusak/expired/lainnya).</div>
                </div>

                <form action="{{ route('masters.items_out.store') }}" method="POST" class="space-y-4 px-6 py-6" data-item-out-create-form>
                    @csrf

                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-slate-700">Tipe</label>
                        <select name="type" class="h-11 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-700 outline-none" data-item-out-type required>
                            <option value="damaged" selected>Rusak</option>
                            <option value="expired">Expired</option>
                            <option value="other">Lainnya</option>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-slate-700">Barang</label>
                        <select name="item_id" class="h-11 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-700 outline-none" required>
                            <option value="" disabled selected>Pilih Barang</option>
                            @foreach(($items ?? []) as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-slate-700">Qty</label>
                        <input name="qty" type="number" min="1" step="1" value="1" class="h-11 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-700 outline-none" required />
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-slate-700">Tanggal</label>
                        <input name="date" type="date" value="{{ now()->toDateString() }}" class="h-11 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-700 outline-none" required />
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2">
                        <button type="button" data-close-modal="modal-tambah-item-out" class="inline-flex h-10 items-center justify-center rounded-xl border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-700 hover:bg-slate-50">Batal</button>
                        <button type="submit" class="inline-flex h-10 items-center justify-center rounded-xl bg-slate-900 px-4 text-sm font-semibold text-white hover:bg-slate-800">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

        <script>
            (function () {
                const typeEl = document.querySelector('[data-item-out-type]');
                if (!typeEl) return;
            })();
        </script>
    @endif
@endsection
