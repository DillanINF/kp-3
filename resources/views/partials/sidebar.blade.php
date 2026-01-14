<div class="flex h-full flex-col">
    <div class="flex items-center justify-between border-b border-slate-200 px-4 py-4">
        <div class="flex w-full items-center justify-center">
            <img src="{{ asset('image.png') }}" alt="{{ config('app.name', 'Company Manager') }}" class="h-10 w-auto max-w-full" />
        </div>
    </div>

    <nav class="flex-1 overflow-y-auto px-3 py-4">
        <div class="space-y-1">
            <a href="{{ route('dashboard') }}" class="sidebar-link group flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium text-slate-700 transition-colors hover:bg-slate-100 hover:text-slate-900 {{ request()->routeIs('dashboard') ? 'bg-slate-100 text-slate-900' : '' }}">
                <span class="sidebar-icon flex h-8 w-8 items-center justify-center rounded-md bg-slate-100 text-slate-700 transition-colors group-hover:bg-slate-300 {{ request()->routeIs('dashboard') ? 'bg-slate-300' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4">
                        <path d="M4 13H10V21H4V13Z" stroke="currentColor" stroke-width="2" />
                        <path d="M14 3H20V21H14V3Z" stroke="currentColor" stroke-width="2" />
                    </svg>
                </span>
                Dashboard
            </a>

            <details class="group rounded-md" @if(request()->routeIs('invoices.*')) open @endif>
                <summary class="sidebar-link flex cursor-pointer list-none items-center justify-between rounded-md px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100 hover:text-slate-900">
                    <span class="flex items-center gap-3">
                        <span class="sidebar-icon flex h-8 w-8 items-center justify-center rounded-md bg-slate-100 text-slate-700 transition-colors group-hover:bg-slate-300">
                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4">
                                <path d="M7 3H17V7H7V3Z" stroke="currentColor" stroke-width="2" />
                                <path d="M5 9H19V21H5V9Z" stroke="currentColor" stroke-width="2" />
                                <path d="M8 13H16" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                                <path d="M8 17H13" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                            </svg>
                        </span>
                        Data Invoice
                    </span>

                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-500 transition-transform group-open:rotate-180">
                        <path d="M6 9L12 15L18 9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </summary>

                <div class="mt-1 space-y-1 pl-11">
                    <a href="{{ route('invoices.index') }}" class="sidebar-link block rounded-md px-3 py-2 text-sm text-slate-600 transition-colors hover:bg-slate-100 hover:text-slate-900 {{ request()->routeIs('invoices.index') ? 'bg-slate-100 text-slate-900' : '' }}">Data Invoice</a>
                    <a href="{{ route('invoices.po_pending') }}" class="sidebar-link block rounded-md px-3 py-2 text-sm text-slate-600 transition-colors hover:bg-slate-100 hover:text-slate-900 {{ request()->routeIs('invoices.po_pending') ? 'bg-slate-100 text-slate-900' : '' }}">PO Belum Terkirim</a>
                </div>
            </details>

            @if(auth()->check() && in_array(auth()->user()?->role, ['admin', 'manager'], true))
                <a href="{{ route('reports.index') }}" class="sidebar-link group flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium text-slate-700 transition-colors hover:bg-slate-100 hover:text-slate-900 {{ request()->routeIs('reports.*') ? 'bg-slate-100 text-slate-900' : '' }}">
                    <span class="sidebar-icon flex h-8 w-8 items-center justify-center rounded-md bg-slate-100 text-slate-700 transition-colors group-hover:bg-slate-300 {{ request()->routeIs('reports.*') ? 'bg-slate-300' : '' }}">
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4">
                            <path d="M7 3H17V21H7V3Z" stroke="currentColor" stroke-width="2" />
                            <path d="M10 7H14" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                            <path d="M10 11H14" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                            <path d="M10 15H14" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                        </svg>
                    </span>
                    Laporan
                </a>
            @endif

            <details class="group rounded-md" @if(request()->routeIs('masters.*')) open @endif>
                <summary class="sidebar-link flex cursor-pointer list-none items-center justify-between rounded-md px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100 hover:text-slate-900">
                    <span class="flex items-center gap-3">
                        <span class="sidebar-icon flex h-8 w-8 items-center justify-center rounded-md bg-slate-100 text-slate-700 transition-colors group-hover:bg-slate-300">
                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4">
                                <path d="M4 6H20" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                                <path d="M4 12H20" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                                <path d="M4 18H20" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                            </svg>
                        </span>
                        Data Master
                    </span>

                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-500 transition-transform group-open:rotate-180">
                        <path d="M6 9L12 15L18 9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </summary>

                <div class="mt-1 space-y-1 pl-11">
                    <a href="{{ route('masters.customers') }}" class="sidebar-link block rounded-md px-3 py-2 text-sm text-slate-600 transition-colors hover:bg-slate-100 hover:text-slate-900 {{ request()->routeIs('masters.customers') ? 'bg-slate-100 text-slate-900' : '' }}">Data Customer</a>
                    <a href="{{ route('masters.suppliers') }}" class="sidebar-link block rounded-md px-3 py-2 text-sm text-slate-600 transition-colors hover:bg-slate-100 hover:text-slate-900 {{ request()->routeIs('masters.suppliers') ? 'bg-slate-100 text-slate-900' : '' }}">Data Supplier</a>
                    <a href="{{ route('masters.items_supplier') }}" class="sidebar-link block rounded-md px-3 py-2 text-sm text-slate-600 transition-colors hover:bg-slate-100 hover:text-slate-900 {{ request()->routeIs('masters.items_supplier') ? 'bg-slate-100 text-slate-900' : '' }}">Permintaan Barang Supplier</a>
                    <a href="{{ route('masters.items') }}" class="sidebar-link block rounded-md px-3 py-2 text-sm text-slate-600 transition-colors hover:bg-slate-100 hover:text-slate-900 {{ request()->routeIs('masters.items') ? 'bg-slate-100 text-slate-900' : '' }}">Data Barang</a>
                    <a href="{{ route('masters.items_in') }}" class="sidebar-link block rounded-md px-3 py-2 text-sm text-slate-600 transition-colors hover:bg-slate-100 hover:text-slate-900 {{ request()->routeIs('masters.items_in') ? 'bg-slate-100 text-slate-900' : '' }}">Data Barang Masuk</a>
                    <a href="{{ route('masters.items_out') }}" class="sidebar-link block rounded-md px-3 py-2 text-sm text-slate-600 transition-colors hover:bg-slate-100 hover:text-slate-900 {{ request()->routeIs('masters.items_out') ? 'bg-slate-100 text-slate-900' : '' }}">Data Barang Keluar</a>
                </div>
            </details>

            @if(auth()->check() && in_array(auth()->user()?->role, ['admin', 'manager'], true))
                <a href="{{ route('settings') }}" class="sidebar-link group flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium text-slate-700 transition-colors hover:bg-slate-100 hover:text-slate-900 {{ request()->routeIs('settings') ? 'bg-slate-100 text-slate-900' : '' }}">
                    <span class="sidebar-icon flex h-8 w-8 items-center justify-center rounded-md bg-slate-100 text-slate-700 transition-colors group-hover:bg-slate-300 {{ request()->routeIs('settings') ? 'bg-slate-300' : '' }}">
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4">
                            <path d="M12 15.5C13.933 15.5 15.5 13.933 15.5 12C15.5 10.067 13.933 8.5 12 8.5C10.067 8.5 8.5 10.067 8.5 12C8.5 13.933 10.067 15.5 12 15.5Z" stroke="currentColor" stroke-width="2" />
                            <path d="M19.4 15C19.3 15.3 19.5 15.7 19.8 15.9L20 16.1C20.5 16.6 20.5 17.4 20 17.9L17.9 20C17.4 20.5 16.6 20.5 16.1 20L15.9 19.8C15.7 19.5 15.3 19.4 15 19.5C14.6 19.6 14.2 19.8 13.8 20V20.3C13.8 21 13.3 21.5 12.6 21.5H11.4C10.7 21.5 10.2 21 10.2 20.3V20C9.8 19.8 9.4 19.6 9 19.5C8.7 19.4 8.3 19.5 8.1 19.8L7.9 20C7.4 20.5 6.6 20.5 6.1 20L4 17.9C3.5 17.4 3.5 16.6 4 16.1L4.2 15.9C4.5 15.7 4.6 15.3 4.5 15C4.4 14.6 4.2 14.2 4 13.8H3.7C3 13.8 2.5 13.3 2.5 12.6V11.4C2.5 10.7 3 10.2 3.7 10.2H4C4.2 9.8 4.4 9.4 4.5 9C4.6 8.7 4.5 8.3 4.2 8.1L4 7.9C3.5 7.4 3.5 6.6 4 6.1L6.1 4C6.6 3.5 7.4 3.5 7.9 4L8.1 4.2C8.3 4.5 8.7 4.6 9 4.5C9.4 4.4 9.8 4.2 10.2 4V3.7C10.2 3 10.7 2.5 11.4 2.5H12.6C13.3 2.5 13.8 3 13.8 3.7V4C14.2 4.2 14.6 4.4 15 4.5C15.3 4.6 15.7 4.5 15.9 4.2L16.1 4C16.6 3.5 17.4 3.5 17.9 4L20 6.1C20.5 6.6 20.5 7.4 20 7.9L19.8 8.1C19.5 8.3 19.4 8.7 19.5 9C19.6 9.4 19.8 9.8 20 10.2H20.3C21 10.2 21.5 10.7 21.5 11.4V12.6C21.5 13.3 21 13.8 20.3 13.8H20C19.8 14.2 19.6 14.6 19.5 15Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </span>
                    Settings
                </a>
            @endif
        </div>
    </nav>

    <div class="border-t border-slate-200 p-3">
        @auth
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="sidebar-link group flex h-12 w-full items-center gap-3 rounded-xl px-3 text-left text-sm font-semibold text-slate-700 hover:bg-slate-100 hover:text-slate-900">
                    <span class="sidebar-icon flex h-9 w-9 items-center justify-center rounded-xl bg-slate-100 text-slate-700 transition-colors group-hover:bg-slate-300">
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4">
                            <path d="M10 17L15 12L10 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M15 12H3" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                            <path d="M21 3V21" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                        </svg>
                    </span>
                    Logout
                </button>
            </form>
        @endauth
    </div>
</div>
