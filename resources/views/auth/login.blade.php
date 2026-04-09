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
    <body class="min-h-screen bg-[#f3f4f6] text-slate-900 font-['Instrument_Sans']">
        <div class="flex min-h-screen items-center justify-center px-4 py-12">
            <div class="flex w-full max-w-[1000px] overflow-hidden rounded-[2rem] bg-white shadow-2xl border border-slate-100">
                <!-- Left Side: Illustration -->
                <div class="relative hidden w-1/2 bg-slate-50 lg:flex items-center justify-center p-12">
                    <div class="w-full max-w-[450px]">
                        <img src="https://images.unsplash.com/photo-1586281380349-632531db7ed4?auto=format&fit=crop&q=80&w=1000" 
                             alt="Working Illustration" 
                             class="w-full h-auto rounded-2xl shadow-lg transition-transform duration-700 hover:scale-105" />
                        <div class="mt-10 text-center">
                            <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Kelola Pekerjaan Anda</h2>
                            <p class="mt-2 text-sm text-slate-500 font-medium">Sistem manajemen terintegrasi untuk efisiensi bisnis Anda.</p>
                        </div>
                    </div>
                </div>

                <!-- Right Side: Login Form -->
                <div class="w-full p-8 sm:p-14 lg:w-1/2">
                    <div class="mb-12">
                       
                        <h1 class="text-3xl font-bold text-slate-900 tracking-tight">Sign In</h1>
                        <p class="mt-2 text-sm text-slate-500 font-medium italic">Silakan masuk menggunakan kredensial Anda.</p>
                    </div>

                    <form method="POST" action="{{ route('login.store') }}" class="space-y-6">
                        @csrf

                        <div class="space-y-2">
                            <label class="text-[11px] font-bold uppercase tracking-[0.15em] text-slate-400 ml-1">Username / Email</label>
                            <input name="email" value="{{ old('email') }}" type="email" autocomplete="email" placeholder="name@company.com" 
                                class="h-12 w-full rounded-xl border border-slate-200 bg-slate-50 px-4 text-sm font-medium outline-none transition-all focus:border-indigo-600 focus:bg-white placeholder:text-slate-300" required />
                            @error('email')
                                <p class="text-[11px] font-medium text-red-600 ml-1 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="text-[11px] font-bold uppercase tracking-[0.15em] text-slate-400 ml-1">Password</label>
                            <input name="password" type="password" autocomplete="current-password" placeholder="••••••••" 
                                class="h-12 w-full rounded-xl border border-slate-200 bg-slate-50 px-4 text-sm font-medium outline-none transition-all focus:border-indigo-600 focus:bg-white placeholder:text-slate-300" required />
                            @error('password')
                                <p class="text-[11px] font-medium text-red-600 ml-1 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit" class="group h-12 w-full rounded-xl bg-indigo-600 text-sm font-bold text-white shadow-xl shadow-indigo-200 transition-all hover:bg-indigo-700 hover:shadow-indigo-300 active:scale-[0.98]">
                            <span class="flex items-center justify-center gap-2">
                                Sign In
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="transition-transform group-hover:translate-x-1"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                            </span>
                        </button>
                       
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>
