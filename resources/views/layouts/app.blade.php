<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>@yield('title', config('app.name', 'Company Manager'))</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>

    <body class="min-h-screen bg-slate-50 text-slate-900">
        <input id="sidebar-toggle" type="checkbox" class="peer hidden" />

        <div class="min-h-screen md:grid md:grid-cols-[280px_1fr]">
            <aside class="fixed inset-y-0 left-0 z-40 w-[280px] -translate-x-full border-r border-slate-200 bg-white transition-transform peer-checked:translate-x-0 md:static md:translate-x-0">
                @include('partials.sidebar')
            </aside>

            <div class="min-w-0">
                <header class="sticky top-0 z-30 border-b border-slate-200 bg-white/80 backdrop-blur">
                    <div class="flex items-center justify-between px-4 py-3 md:px-6">
                        <div class="flex items-center gap-3">
                            <label for="sidebar-toggle" class="inline-flex h-9 w-9 cursor-pointer items-center justify-center rounded-md border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 md:hidden">
                                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5">
                                    <path d="M4 6H20" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                    <path d="M4 12H20" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                    <path d="M4 18H20" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                            </label>

                            <div class="min-w-0">
                                <div class="text-sm font-semibold text-slate-900">{{ config('app.name', 'Company Manager') }}</div>
                                <div class="text-xs text-slate-500">@yield('subtitle', 'Manajemen perusahaan')</div>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            @auth
                                <div class="hidden items-center gap-2 rounded-md border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 md:flex">
                                    <span class="truncate max-w-[180px]">{{ auth()->user()->name }}</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-1 text-xs font-semibold text-slate-700">{{ auth()->user()->role }}</span>
                                </div>
                            @endauth
                        </div>
                    </div>
                </header>

                <main class="px-4 py-6 md:px-6">
                    <div class="mx-auto w-full max-w-7xl">
                        <div class="mb-6">
                            <h1 class="text-xl font-semibold text-slate-900">@yield('page_title', 'Dashboard')</h1>
                            @hasSection('page_description')
                                <p class="mt-1 text-sm text-slate-600">@yield('page_description')</p>
                            @endif
                        </div>

                        @yield('content')
                    </div>
                </main>
            </div>
        </div>

        <label for="sidebar-toggle" class="fixed inset-0 z-30 hidden bg-slate-900/30 peer-checked:block md:hidden"></label>
    </body>
</html>
