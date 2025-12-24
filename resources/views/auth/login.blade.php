<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Login - {{ config('app.name', 'Company Manager') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-slate-50 text-slate-900">
        <div class="mx-auto flex min-h-screen max-w-7xl items-center justify-center px-4 py-10">
            <div class="w-full max-w-md overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 bg-white px-6 py-5">
                    <div class="text-lg font-semibold text-slate-900">Masuk</div>
                    <div class="mt-1 text-sm text-slate-500">Gunakan akun yang sudah dibuat oleh admin.</div>
                </div>

                <form method="POST" action="{{ route('login.store') }}" class="space-y-4 px-6 py-6">
                    @csrf

                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-slate-700">Email</label>
                        <input name="email" value="{{ old('email') }}" type="email" autocomplete="email" class="h-11 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-700 outline-none focus:border-slate-400 focus:ring-4 focus:ring-slate-100" required />
                        @error('email')
                            <div class="text-xs text-red-600">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-slate-700">Password</label>
                        <input name="password" type="password" autocomplete="current-password" class="h-11 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-700 outline-none focus:border-slate-400 focus:ring-4 focus:ring-slate-100" required />
                        @error('password')
                            <div class="text-xs text-red-600">{{ $message }}</div>
                        @enderror
                    </div>

                    <label class="inline-flex items-center gap-2 text-sm font-semibold text-slate-700">
                        <input name="remember" type="checkbox" value="1" class="h-4 w-4 rounded border-slate-300" />
                        Remember me
                    </label>

                    <button type="submit" class="inline-flex h-11 w-full items-center justify-center rounded-xl bg-slate-900 px-4 text-sm font-semibold text-white hover:bg-slate-800">
                        Login
                    </button>
                </form>
            </div>
        </div>
    </body>
</html>
