@extends('layouts.app')

@section('title', 'Data Pengirim')
@section('page_title', 'Data Pengirim')
@section('page_description')
    Manajemen nama pengirim untuk keperluan invoice.
@endsection

@section('content')
    <div class="rounded-xl border border-slate-200 bg-white">
        <div class="flex items-center justify-between border-b border-slate-200 p-4">
            <div class="text-sm font-semibold text-slate-900">Pengirim</div>

            @if(auth()->user()?->role === 'admin')
                <button type="button"
                    data-open-modal="modal-tambah-pengirim"
                    class="inline-flex items-center gap-2 rounded-md bg-slate-900 px-3 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                    Tambah
                </button>
            @endif
        </div>

        <div class="p-4">
            {{-- INFO --}}
            <div class="mb-4 rounded-lg border border-slate-200 bg-slate-50 p-4 text-sm text-slate-700">
                Pengirim yang sedang digunakan:
                <span class="font-semibold text-slate-900">
                    {{ session('nama_pengirim') ?? 'Belum diatur' }}
                </span>
            </div>
        </div>
    </div>

    {{-- ================= MODAL TAMBAH ================= --}}
    @if(auth()->user()?->role === 'admin')
        <div id="modal-tambah-pengirim" data-modal
            class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 p-4">

            <div class="w-full max-w-lg overflow-hidden rounded-2xl bg-white shadow-xl">

                {{-- HEADER --}}
                <div class="bg-slate-900 px-6 py-5">
                    <div class="text-lg font-semibold text-white">Tambah Pengirim</div>
                    <div class="mt-1 text-sm text-slate-200">
                        Masukkan nama pengirim untuk invoice.
                    </div>
                </div>

                {{-- FORM --}}
                <form action="{{ route('masters.pengirim.store') }}" method="POST"
                    class="space-y-4 px-6 py-6">
                    @csrf

                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-slate-700">Nama Pengirim</label>
                        <input name="nama_pengirim"
                            value="{{ old('nama_pengirim') }}"
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