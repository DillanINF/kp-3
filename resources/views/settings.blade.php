@extends('layouts.app')

@section('title', 'Settings')
@section('page_title', 'Settings')
@section('page_description')Pengaturan umum aplikasi.@endsection

@section('content')
    @php($tab = in_array(request('tab'), ['profil', 'password'], true) ? request('tab') : 'profil')
    <div class="grid grid-cols-1 gap-4 xl:grid-cols-12">
        <div class="xl:col-span-3">
            <div class="rounded-xl border border-slate-200 bg-white p-2">
                <a href="{{ route('settings', ['tab' => 'profil']) }}" class="block rounded-lg px-3 py-2 text-sm font-semibold transition-colors {{ $tab === 'profil' ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-100' }}">Profil</a>
                <a href="{{ route('settings', ['tab' => 'password']) }}" class="mt-1 block rounded-lg px-3 py-2 text-sm font-semibold transition-colors {{ $tab === 'password' ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-100' }}">Ganti Password</a>
            </div>
        </div>

        <div class="xl:col-span-9">
            <div class="rounded-xl border border-slate-200 bg-white p-4">
                @if(session('success'))
                    <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm text-emerald-800">
                        {{ session('success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-4 rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-sm text-rose-800">
                        Periksa kembali input yang kamu masukkan.
                    </div>
                @endif

                @if($tab === 'password')
                    <div class="text-sm font-semibold text-slate-900">Ganti Password</div>
                    <div class="mt-1 text-xs text-slate-500">Gunakan minimal 8 karakter dan jangan gunakan password yang mudah ditebak.</div>
                    <form class="mt-4 space-y-3" method="POST" action="{{ route('settings.password.update', ['tab' => 'password']) }}">
                        @csrf
                        <div>
                            <label class="text-sm font-medium text-slate-700">Password lama</label>
                            <input name="current_password" type="password" class="mt-1 w-full rounded-md border bg-white px-3 py-2 text-sm text-slate-700 {{ $errors->has('current_password') ? 'border-rose-300' : 'border-slate-200' }}" />
                            @error('current_password')
                                <div class="mt-1 text-xs text-rose-600">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label class="text-sm font-medium text-slate-700">Password baru</label>
                            <input name="password" type="password" class="mt-1 w-full rounded-md border bg-white px-3 py-2 text-sm text-slate-700 {{ $errors->has('password') ? 'border-rose-300' : 'border-slate-200' }}" />
                            @error('password')
                                <div class="mt-1 text-xs text-rose-600">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label class="text-sm font-medium text-slate-700">Konfirmasi password baru</label>
                            <input name="password_confirmation" type="password" class="mt-1 w-full rounded-md border bg-white px-3 py-2 text-sm text-slate-700 {{ $errors->has('password') ? 'border-rose-300' : 'border-slate-200' }}" />
                        </div>
                        <div class="pt-2">
                            <button type="submit" class="inline-flex items-center justify-center rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">Simpan</button>
                        </div>
                    </form>
                @else
                    <div class="text-sm font-semibold text-slate-900">Profil</div>
                    <div class="mt-1 text-xs text-slate-500">Perbarui informasi akun yang digunakan untuk masuk.</div>
                    <form class="mt-4 space-y-3" method="POST" action="{{ route('settings.profile.update', ['tab' => 'profil']) }}">
                        @csrf
                        <div>
                            <label class="text-sm font-medium text-slate-700">Nama</label>
                            <input name="name" type="text" value="{{ old('name', auth()->user()?->name) }}" class="mt-1 w-full rounded-md border bg-white px-3 py-2 text-sm text-slate-700 {{ $errors->has('name') ? 'border-rose-300' : 'border-slate-200' }}" />
                            @error('name')
                                <div class="mt-1 text-xs text-rose-600">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label class="text-sm font-medium text-slate-700">Email</label>
                            <input name="email" type="email" value="{{ old('email', auth()->user()?->email) }}" class="mt-1 w-full rounded-md border bg-white px-3 py-2 text-sm text-slate-700 {{ $errors->has('email') ? 'border-rose-300' : 'border-slate-200' }}" />
                            @error('email')
                                <div class="mt-1 text-xs text-rose-600">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="pt-2">
                            <button type="submit" class="inline-flex items-center justify-center rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">Simpan</button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
@endsection
