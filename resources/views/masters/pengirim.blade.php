@extends('layouts.app')

@section('title', 'Data Pengirim')
@section('page_title', 'Data Pengirim')
@section('page_description')
    Manajemen nama pengirim untuk keperluan invoice.
@endsection

@section('content')
    <div class="rounded-xl border border-slate-200 bg-white">
        <div class="flex items-center justify-between border-b border-slate-200 p-4">
            <div class="text-sm font-semibold text-slate-900">Daftar Pengirim</div>

            @if(auth()->user()?->role === 'admin')
                <button type="button"
                    data-open-modal="modal-tambah-pengirim"
                    class="inline-flex items-center gap-2 rounded-md bg-slate-900 px-3 py-2 text-sm font-semibold text-white hover:bg-slate-800 transition-colors">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Pengirim
                </button>
            @endif
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-4 py-3 text-center w-16">No</th>
                        <th class="px-4 py-3">Nama Pengirim</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($pengirims as $p)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-4 py-3 text-center text-slate-500">{{ $loop->iteration }}</td>
                            <td class="px-4 py-3 font-medium text-slate-900">{{ $p->name }}</td>
                            <td class="px-4 py-3 text-right">
                                <div class="inline-flex items-center gap-2">
                                    @if(auth()->user()?->role === 'admin')
                                        <button type="button"
                                            data-open-modal="modal-edit-pengirim-{{ $p->id }}"
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 hover:text-slate-900"
                                            title="Edit">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4L16.5 3.5z" />
                                            </svg>
                                        </button>

                                        <form action="{{ route('masters.pengirim.destroy', $p) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengirim ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-red-100 bg-red-50 text-red-600 hover:bg-red-100 hover:text-red-700"
                                                title="Hapus">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>

                                        {{-- MODAL EDIT --}}
                                        <div id="modal-edit-pengirim-{{ $p->id }}" data-modal
                                            class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 p-4">
                                            <div class="w-full max-w-md overflow-hidden rounded-2xl bg-white shadow-xl">
                                                <div class="bg-slate-900 px-6 py-5">
                                                    <div class="text-lg font-semibold text-white">Edit Pengirim</div>
                                                </div>
                                                <form action="{{ route('masters.pengirim.update', $p) }}" method="POST" class="space-y-4 px-6 py-6">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="space-y-2 text-left">
                                                        <label class="text-sm font-semibold text-slate-700">Nama Pengirim</label>
                                                        <input name="name" value="{{ $p->name }}" type="text" class="h-11 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-700 outline-none focus:border-slate-400 focus:ring-4 focus:ring-slate-100" required />
                                                    </div>
                                                    <div class="flex items-center justify-end gap-2 pt-2">
                                                        <button type="button" data-close-modal="modal-edit-pengirim-{{ $p->id }}" class="inline-flex h-10 items-center justify-center rounded-xl border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-700 hover:bg-slate-50">Batal</button>
                                                        <button type="submit" class="inline-flex h-10 items-center justify-center rounded-xl bg-slate-900 px-4 text-sm font-semibold text-white hover:bg-slate-800">Simpan Perubahan</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-4 py-8 text-center text-slate-500 italic">Belum ada data pengirim.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ================= MODAL TAMBAH ================= --}}
    @if(auth()->user()?->role === 'admin')
        <div id="modal-tambah-pengirim" data-modal
            class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 p-4">

            <div class="w-full max-w-md overflow-hidden rounded-2xl bg-white shadow-xl">

                {{-- HEADER --}}
                <div class="bg-slate-900 px-6 py-5">
                    <div class="text-lg font-semibold text-white">Tambah Pengirim</div>
                    <div class="mt-1 text-sm text-slate-200">
                        Masukkan nama pengirim baru.
                    </div>
                </div>

                {{-- FORM --}}
                <form action="{{ route('masters.pengirim.store') }}" method="POST"
                    class="space-y-4 px-6 py-6">
                    @csrf

                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-slate-700">Nama Pengirim</label>
                        <input name="name"
                            value="{{ old('name') }}"
                            type="text"
                            placeholder="Contoh: Mursidi"
                            class="h-11 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-700 outline-none focus:border-slate-400 focus:ring-4 focus:ring-slate-100"
                            required />
                    </div>

                    {{-- BUTTON --}}
                    <div class="flex items-center justify-end gap-2 pt-2">
                        <button type="button"
                            data-close-modal="modal-tambah-pengirim"
                            class="inline-flex h-10 items-center justify-center rounded-xl border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                            Batal
                        </button>

                        <button type="submit"
                            class="inline-flex h-10 items-center justify-center rounded-xl bg-slate-900 px-4 text-sm font-semibold text-white hover:bg-slate-800">
                            Simpan
                        </button>
                    </div>
                </form>

            </div>
        </div>
    @endif
@endsection