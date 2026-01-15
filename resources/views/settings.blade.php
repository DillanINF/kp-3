@extends('layouts.app')

@section('title', 'Settings')
@section('page_title', 'Settings')
@section('page_description')Pengaturan umum aplikasi.@endsection

@section('content')
    @php($tab = in_array(request('tab'), ['profil', 'password', 'manager-list', 'manager-create'], true) ? request('tab') : 'profil')
    <div class="grid grid-cols-1 gap-4 xl:grid-cols-12">
        <div class="xl:col-span-3">
            <div class="rounded-xl border border-slate-200 bg-white p-2">
                <a href="{{ route('settings', ['tab' => 'profil']) }}" class="block rounded-lg px-3 py-2 text-sm font-semibold transition-colors {{ $tab === 'profil' ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-100' }}">Profil</a>
                <a href="{{ route('settings', ['tab' => 'password']) }}" class="mt-1 block rounded-lg px-3 py-2 text-sm font-semibold transition-colors {{ $tab === 'password' ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-100' }}">Ganti Password</a>
                @if(auth()->user()?->role === 'admin')
                    <a href="{{ route('settings', ['tab' => 'manager-list']) }}" class="mt-1 block rounded-lg px-3 py-2 text-sm font-semibold transition-colors {{ $tab === 'manager-list' ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-100' }}">Daftar Akun Manager</a>
                    <a href="{{ route('settings', ['tab' => 'manager-create']) }}" class="mt-1 block rounded-lg px-3 py-2 text-sm font-semibold transition-colors {{ $tab === 'manager-create' ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-100' }}">Buat Akun Manager</a>
                @endif
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

                @if($tab === 'manager-list' && auth()->user()?->role === 'admin')
                    <div class="text-sm font-semibold text-slate-900">Daftar Akun Manager</div>
                    <div class="mt-1 text-xs text-slate-500">Kelola akun manager yang bisa mengakses fitur aplikasi sesuai role manager.</div>
                    <div class="mt-4 rounded-lg border border-slate-200">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-slate-200 text-sm">
                                <thead class="bg-slate-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Nama</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Email</th>
                                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200 bg-white">
                                    @forelse(($managers ?? collect()) as $manager)
                                        <tr>
                                            <td class="whitespace-nowrap px-4 py-3 text-slate-700">{{ $manager->name }}</td>
                                            <td class="whitespace-nowrap px-4 py-3 text-slate-700">{{ $manager->email }}</td>
                                            <td class="whitespace-nowrap px-4 py-3 text-right">
                                                <form method="POST" action="{{ route('settings.manager.destroy', $manager) }}" onsubmit="return confirm('Hapus akun manager ini?');" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="inline-flex items-center justify-center rounded-md border border-rose-200 bg-rose-50 px-3 py-1.5 text-xs font-semibold text-rose-700 hover:bg-rose-100">Hapus</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="px-4 py-4 text-center text-sm text-slate-500">Belum ada akun manager terdaftar.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                @elseif($tab === 'manager-create' && auth()->user()?->role === 'admin')
                    <div class="text-sm font-semibold text-slate-900">Buat Akun Manager</div>
                    <div class="mt-1 text-xs text-slate-500">Buat akun manager baru untuk mengakses fitur aplikasi sesuai role manager.</div>
                    <form class="mt-4 space-y-3" method="POST" action="{{ route('settings.manager.store') }}">
                        @csrf
                        <div>
                            <label class="text-sm font-medium text-slate-700">Nama</label>
                            <input name="name" type="text" value="{{ old('name') }}" class="mt-1 w-full rounded-md border bg-white px-3 py-2 text-sm text-slate-700 {{ $errors->has('name') ? 'border-rose-300' : 'border-slate-200' }}" />
                            @error('name')
                                <div class="mt-1 text-xs text-rose-600">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label class="text-sm font-medium text-slate-700">Email</label>
                            <input name="email" type="email" value="{{ old('email') }}" class="mt-1 w-full rounded-md border bg-white px-3 py-2 text-sm text-slate-700 {{ $errors->has('email') ? 'border-rose-300' : 'border-slate-200' }}" />
                            @error('email')
                                <div class="mt-1 text-xs text-rose-600">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label class="text-sm font-medium text-slate-700">Password</label>
                            <input name="password" type="password" class="mt-1 w-full rounded-md border bg-white px-3 py-2 text-sm text-slate-700 {{ $errors->has('password') ? 'border-rose-300' : 'border-slate-200' }}" />
                            @error('password')
                                <div class="mt-1 text-xs text-rose-600">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label class="text-sm font-medium text-slate-700">Konfirmasi password</label>
                            <input name="password_confirmation" type="password" class="mt-1 w-full rounded-md border bg-white px-3 py-2 text-sm text-slate-700 {{ $errors->has('password') ? 'border-rose-300' : 'border-slate-200' }}" />
                        </div>
                        <div class="pt-2">
                            <button type="submit" class="inline-flex items-center justify-center rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">Buat Akun</button>
                        </div>
                    </form>
                @elseif($tab === 'password')
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
