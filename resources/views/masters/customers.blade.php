@extends('layouts.app')

@section('title', 'Data Customer')
@section('page_title', 'Data Customer')
@section('page_description')Manajemen data customer untuk transaksi barang keluar dan invoice.@endsection

@section('content')
    <div class="rounded-xl border border-slate-200 bg-white">
        <div class="flex items-center justify-between border-b border-slate-200 p-4">
            <div class="text-sm font-semibold text-slate-900">Customer</div>
            @if(auth()->user()?->role === 'admin')
                <button type="button" data-open-modal="modal-tambah-customer" class="inline-flex items-center gap-2 rounded-md bg-slate-900 px-3 py-2 text-sm font-semibold text-white hover:bg-slate-800">Tambah</button>
            @endif
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-600">
                    <tr>
                        <th class="px-4 py-3">Nama</th>
                        <th class="px-4 py-3">Email</th>
                        <th class="px-4 py-3">Telepon</th>
                        <th class="px-4 py-3">Alamat</th>
                        <th class="px-4 py-3">Status</th>
                        @if(auth()->user()?->role === 'admin')
                            <th class="px-4 py-3 text-center">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($customers as $customer)
                        <tr>
                            <td class="px-4 py-3 font-medium text-slate-900">{{ $customer->name }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $customer->email ?? '-' }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $customer->phone ?? '-' }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $customer->address ?? '-' }}</td>
                            <td class="px-4 py-3">
                                @if($customer->is_active)
                                    <span class="inline-flex rounded-full bg-emerald-50 px-2 py-1 text-xs font-medium text-emerald-700">Aktif</span>
                                @else
                                    <span class="inline-flex rounded-full bg-slate-100 px-2 py-1 text-xs font-medium text-slate-700">Nonaktif</span>
                                @endif
                            </td>
                            @if(auth()->user()?->role === 'admin')
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-center gap-2">
                                        <button
                                            type="button"
                                            data-action="edit-customer"
                                            data-customer-id="{{ $customer->id }}"
                                            data-customer-name="{{ $customer->name }}"
                                            data-customer-email="{{ $customer->email }}"
                                            data-customer-phone="{{ $customer->phone }}"
                                            data-customer-address="{{ $customer->address }}"
                                            data-customer-active="{{ $customer->is_active ? 1 : 0 }}"
                                            class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-600 hover:bg-slate-50"
                                            aria-label="Edit"
                                        >
                                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4">
                                                <path d="M12 20H21" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                                <path d="M16.5 3.5C17.3284 2.67157 18.6716 2.67157 19.5 3.5C20.3284 4.32843 20.3284 5.67157 19.5 6.5L8 18L3 19L4 14L16.5 3.5Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                                            </svg>
                                        </button>

                                        <form action="{{ route('masters.customers.destroy', $customer) }}" method="POST" onsubmit="return confirm('Hapus customer ini?')">
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
                    @empty
                        <tr>
                            <td colspan="{{ auth()->user()?->role === 'admin' ? 6 : 5 }}" class="px-4 py-6 text-center text-sm text-slate-500">Belum ada data customer.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if(auth()->user()?->role === 'admin')
        <div id="modal-tambah-customer" data-modal class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 p-4">
            <div class="w-full max-w-lg overflow-hidden rounded-2xl bg-white shadow-xl">
                <div class="bg-slate-900 px-6 py-5">
                    <div class="text-lg font-semibold text-white">Tambah Customer</div>
                    <div class="mt-1 text-sm text-slate-200">Isi data customer untuk transaksi invoice.</div>
                </div>

            <form action="{{ route('masters.customers.store') }}" method="POST" class="space-y-4 px-6 py-6">
                @csrf

                <div class="space-y-2">
                    <label class="text-sm font-semibold text-slate-700">Nama</label>
                    <input name="name" value="{{ old('name') }}" type="text" class="h-11 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-700 outline-none focus:border-slate-400 focus:ring-4 focus:ring-slate-100" required />
                    @error('name')
                        <div class="text-xs text-red-600">{{ $message }}</div>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-semibold text-slate-700">Email</label>
                    <input name="email" value="{{ old('email') }}" type="email" class="h-11 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-700 outline-none focus:border-slate-400 focus:ring-4 focus:ring-slate-100" />
                    @error('email')
                        <div class="text-xs text-red-600">{{ $message }}</div>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-semibold text-slate-700">Telepon</label>
                    <input name="phone" value="{{ old('phone') }}" type="text" class="h-11 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-700 outline-none focus:border-slate-400 focus:ring-4 focus:ring-slate-100" />
                    @error('phone')
                        <div class="text-xs text-red-600">{{ $message }}</div>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-semibold text-slate-700">Alamat</label>
                    <input name="address" value="{{ old('address') }}" type="text" class="h-11 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-700 outline-none focus:border-slate-400 focus:ring-4 focus:ring-slate-100" />
                    @error('address')
                        <div class="text-xs text-red-600">{{ $message }}</div>
                    @enderror
                </div>

                <div class="flex items-center justify-between pt-2">
                    <label class="inline-flex items-center gap-2 text-sm font-semibold text-slate-700">
                        <input name="is_active" type="checkbox" value="1" class="h-4 w-4 rounded border-slate-300" checked />
                        Aktif
                    </label>

                    <div class="flex items-center gap-2">
                        <button type="button" data-close-modal="modal-tambah-customer" class="inline-flex h-10 items-center justify-center rounded-xl border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-700 hover:bg-slate-50">Batal</button>
                        <button type="submit" class="inline-flex h-10 items-center justify-center rounded-xl bg-slate-900 px-4 text-sm font-semibold text-white hover:bg-slate-800">Simpan</button>
                    </div>
                </div>
                </form>
            </div>
        </div>

        <div id="modal-edit-customer" data-modal class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 p-4">
            <div class="w-full max-w-lg overflow-hidden rounded-2xl bg-white shadow-xl">
                <div class="bg-slate-900 px-6 py-5">
                    <div class="text-lg font-semibold text-white">Edit Customer</div>
                    <div class="mt-1 text-sm text-slate-200">Perbarui data customer.</div>
                </div>

            <form data-edit-customer-form data-action-template="{{ route('masters.customers.update', ['customer' => '__ID__']) }}" action="" method="POST" class="space-y-4 px-6 py-6">
                @csrf
                @method('PUT')

                <div class="space-y-2">
                    <label class="text-sm font-semibold text-slate-700">Nama</label>
                    <input data-edit-customer-name name="name" type="text" class="h-11 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-700 outline-none focus:border-slate-400 focus:ring-4 focus:ring-slate-100" required />
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-semibold text-slate-700">Email</label>
                    <input data-edit-customer-email name="email" type="email" class="h-11 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-700 outline-none focus:border-slate-400 focus:ring-4 focus:ring-slate-100" />
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-semibold text-slate-700">Telepon</label>
                    <input data-edit-customer-phone name="phone" type="text" class="h-11 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-700 outline-none focus:border-slate-400 focus:ring-4 focus:ring-slate-100" />
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-semibold text-slate-700">Alamat</label>
                    <input data-edit-customer-address name="address" type="text" class="h-11 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-700 outline-none focus:border-slate-400 focus:ring-4 focus:ring-slate-100" />
                </div>

                <div class="flex items-center justify-between pt-2">
                    <label class="inline-flex items-center gap-2 text-sm font-semibold text-slate-700">
                        <input data-edit-customer-active name="is_active" type="checkbox" value="1" class="h-4 w-4 rounded border-slate-300" />
                        Aktif
                    </label>

                    <div class="flex items-center gap-2">
                        <button type="button" data-close-modal="modal-edit-customer" class="inline-flex h-10 items-center justify-center rounded-xl border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-700 hover:bg-slate-50">Batal</button>
                        <button type="submit" class="inline-flex h-10 items-center justify-center rounded-xl bg-slate-900 px-4 text-sm font-semibold text-white hover:bg-slate-800">Simpan</button>
                    </div>
                </div>
                </form>
            </div>
        </div>
    @endif
@endsection
