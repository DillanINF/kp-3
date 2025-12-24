@extends('layouts.app')

@section('title', 'Settings')
@section('page_title', 'Settings')
@section('page_description')Pengaturan umum aplikasi.@endsection

@section('content')
    <div class="grid grid-cols-1 gap-4 xl:grid-cols-2">
        <div class="rounded-xl border border-slate-200 bg-white p-4">
            <div class="text-sm font-semibold text-slate-900">Profil Perusahaan</div>
            <form class="mt-4 space-y-3">
                <div>
                    <label class="text-sm font-medium text-slate-700">Nama Perusahaan</label>
                    <input type="text" value="{{ config('app.name', 'Company Manager') }}" class="mt-1 w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700" />
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-700">Alamat</label>
                    <input type="text" value="Jl. Contoh No. 1" class="mt-1 w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700" />
                </div>
                <button type="button" class="rounded-md bg-slate-900 px-3 py-2 text-sm font-semibold text-white hover:bg-slate-800">Simpan</button>
            </form>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-4">
            <div class="text-sm font-semibold text-slate-900">Preferensi</div>
            <div class="mt-4 space-y-3 text-sm text-slate-700">
                <div class="flex items-center justify-between rounded-lg border border-slate-200 p-3">
                    <div>
                        <div class="font-medium">Notifikasi email</div>
                        <div class="text-xs text-slate-500">Kirim notifikasi saat invoice jatuh tempo.</div>
                    </div>
                    <input type="checkbox" class="h-4 w-4" />
                </div>
                <div class="flex items-center justify-between rounded-lg border border-slate-200 p-3">
                    <div>
                        <div class="font-medium">Mode gelap</div>
                        <div class="text-xs text-slate-500">Pengaturan tema tampilan.</div>
                    </div>
                    <input type="checkbox" class="h-4 w-4" />
                </div>
            </div>
        </div>
    </div>
@endsection
