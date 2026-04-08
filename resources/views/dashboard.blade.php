@extends('layouts.app')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')
@section('page_description')Ringkasan kinerja dan aktivitas terbaru.@endsection

@section('content')
    <div class="space-y-6">
        <!-- Section Finansial -->
        <div>
            <div class="flex items-center gap-2 mb-4">
                <div class="h-5 w-1 bg-indigo-600 rounded-full"></div>
                <h2 class="text-sm font-bold uppercase tracking-wider text-slate-600">Ringkasan Finansial</h2>
            </div>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <a href="{{ route('invoices.index') }}" class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-5 transition-all hover:shadow-lg hover:shadow-indigo-500/10 hover:-translate-y-1">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium text-slate-500">Pendapatan</p>
                            <h3 class="mt-2 text-2xl font-bold text-slate-900 leading-none">Rp {{ number_format($revenueTotal ?? 0, 0, ',', '.') }}</h3>
                            <p class="mt-2 text-xs text-slate-400">Total invoice posted</p>
                        </div>
                        <div class="rounded-xl bg-indigo-50 p-2.5 text-indigo-600 group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trending-up"><polyline points="22 7 13.5 15.5 8.5 10.5 2 17"></polyline><polyline points="16 7 22 7 22 13"></polyline></svg>
                        </div>
                    </div>
                </a>

                <a href="{{ route('masters.items_supplier') }}" class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-5 transition-all hover:shadow-lg hover:shadow-rose-500/10 hover:-translate-y-1">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium text-slate-500">Pengeluaran</p>
                            <h3 class="mt-2 text-2xl font-bold text-slate-900 leading-none">Rp {{ number_format($expenseTotal ?? 0, 0, ',', '.') }}</h3>
                            <p class="mt-2 text-xs text-slate-400">Total pemesanan supplier</p>
                        </div>
                        <div class="rounded-xl bg-rose-50 p-2.5 text-rose-600 group-hover:bg-rose-600 group-hover:text-white transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trending-down"><polyline points="22 17 13.5 8.5 8.5 13.5 2 7"></polyline><polyline points="16 17 22 17 22 11"></polyline></svg>
                        </div>
                    </div>
                </a>

                <a href="{{ route('masters.items_out') }}" class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-5 transition-all hover:shadow-lg hover:shadow-emerald-500/10 hover:-translate-y-1">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium text-slate-500">Keuntungan</p>
                            <h3 class="mt-2 text-2xl font-bold text-slate-900 leading-none">Rp {{ number_format($profitTotal ?? 0, 0, ',', '.') }}</h3>
                            <p class="mt-2 text-xs text-slate-400">Laba kotor penjualan</p>
                        </div>
                        <div class="rounded-xl bg-emerald-50 p-2.5 text-emerald-600 group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-dollar-sign"><line x1="12" x2="12" y1="2" y2="22"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                        </div>
                    </div>
                </a>

                <a href="{{ route('invoices.index') }}" class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-5 transition-all hover:shadow-lg hover:shadow-blue-500/10 hover:-translate-y-1">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium text-slate-500">Total Invoice</p>
                            <h3 class="mt-2 text-2xl font-bold text-slate-900 leading-none">{{ $invoicesCount ?? 0 }}</h3>
                            <p class="mt-2 text-xs text-slate-400">Jumlah data transaksi</p>
                        </div>
                        <div class="rounded-xl bg-blue-50 p-2.5 text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-text"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" x2="8" y1="13" y2="13"></line><line x1="16" x2="8" y1="17" y2="17"></line><line x1="10" x2="8" y1="9" y2="9"></line></svg>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Section Operasional -->
        <div>
            <div class="flex items-center gap-2 mb-4">
                <div class="h-5 w-1 bg-amber-500 rounded-full"></div>
                <h2 class="text-sm font-bold uppercase tracking-wider text-slate-600">Aktivitas Operasional</h2>
            </div>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <a href="{{ route('masters.items_in') }}" class="group rounded-2xl border border-slate-200 bg-white p-5 transition-all hover:shadow-lg hover:border-amber-200">
                    <div class="flex items-center gap-4">
                        <div class="rounded-xl bg-amber-50 p-3 text-amber-600 group-hover:scale-110 transition-transform">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-package-plus"><path d="M16 16h6"></path><path d="M19 13v6"></path><path d="M21 10V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l2-1.14"></path><path d="m7.5 4.27 9 5.15"></path><polyline points="3.29 7 12 12 20.71 7"></polyline><line x1="12" x2="12" y1="22" y2="12"></line></svg>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Barang Masuk</p>
                            <h3 class="text-xl font-bold text-slate-900 leading-none mt-1">{{ $goodsInTotalQty ?? 0 }}</h3>
                        </div>
                    </div>
                </a>

                <a href="{{ route('masters.items_out') }}" class="group rounded-2xl border border-slate-200 bg-white p-5 transition-all hover:shadow-lg hover:border-blue-200">
                    <div class="flex items-center gap-4">
                        <div class="rounded-xl bg-blue-50 p-3 text-blue-600 group-hover:scale-110 transition-transform">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shopping-cart"><circle cx="8" cy="21" r="1"></circle><circle cx="19" cy="21" r="1"></circle><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"></path></svg>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Penjualan</p>
                            <h3 class="text-xl font-bold text-slate-900 leading-none mt-1">{{ $goodsOutSalesQty ?? 0 }}</h3>
                        </div>
                    </div>
                </a>

                <a href="{{ route('masters.items_out') }}" class="group rounded-2xl border border-slate-200 bg-white p-5 transition-all hover:shadow-lg hover:border-rose-200">
                    <div class="flex items-center gap-4">
                        <div class="rounded-xl bg-rose-50 p-3 text-rose-600 group-hover:scale-110 transition-transform">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-alert-triangle"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"></path><path d="M12 9v4"></path><path d="M12 17h.01"></path></svg>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Kerugian</p>
                            <h3 class="text-xl font-bold text-slate-900 leading-none mt-1">{{ $goodsOutLossQty ?? 0 }}</h3>
                        </div>
                    </div>
                </a>

                <a href="{{ route('masters.items_out') }}" class="group rounded-2xl border border-slate-200 bg-white p-5 transition-all hover:shadow-lg hover:border-slate-300">
                    <div class="flex items-center gap-4">
                        <div class="rounded-xl bg-slate-100 p-3 text-slate-600 group-hover:scale-110 transition-transform">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-package-check"><path d="M16 16h6"></path><path d="m19 13 3 3-3 3"></path><path d="M21 10V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l2-1.14"></path><path d="m7.5 4.27 9 5.15"></path><polyline points="3.29 7 12 12 20.71 7"></polyline><line x1="12" x2="12" y1="22" y2="12"></line></svg>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Total Keluar</p>
                            <h3 class="text-xl font-bold text-slate-900 leading-none mt-1">{{ $goodsOutTotalQty ?? 0 }}</h3>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Section Master & Aktivitas -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- Aktivitas Terbaru -->
            <div class="lg:col-span-2 rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <div class="flex items-center justify-between border-b border-slate-100 bg-slate-50/50 p-4">
                    <h3 class="font-bold text-slate-800 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-history text-slate-500"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"></path><path d="M3 3v5h5"></path><path d="M12 7v5l4 2"></path></svg>
                        Aktivitas Terbaru
                    </h3>
                    <a href="{{ route('invoices.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 transition-colors">Lihat Semua</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-50 text-[10px] font-bold uppercase tracking-widest text-slate-500">
                            <tr>
                                <th class="px-5 py-3">Waktu</th>
                                <th class="px-5 py-3">Aktivitas</th>
                                <th class="px-5 py-3">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse(($activities ?? []) as $a)
                                @php
                                    $tone = (string) ($a['tone'] ?? 'neutral');
                                    $statusColor = 'bg-slate-100 text-slate-600';
                                    if ($tone === 'success') $statusColor = 'bg-emerald-100 text-emerald-700';
                                    if ($tone === 'warning') $statusColor = 'bg-amber-100 text-amber-700';
                                @endphp
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="px-5 py-4 text-xs text-slate-500 whitespace-nowrap font-medium">
                                        {{ ($a['at'] ?? null) ? $a['at']->timezone(config('app.timezone'))->format('H:i • d M') : '-' }}
                                    </td>
                                    <td class="px-5 py-4">
                                        @if(!empty($a['url']))
                                            <a href="{{ $a['url'] }}" class="font-semibold text-slate-700 hover:text-indigo-600 transition-colors">{{ $a['label'] ?? '-' }}</a>
                                        @else
                                            <span class="font-semibold text-slate-700">{{ $a['label'] ?? '-' }}</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4">
                                        <span class="inline-flex rounded-lg px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider {{ $statusColor }}">
                                            {{ $a['status'] ?? '-' }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-5 py-10 text-center text-slate-400 italic">Belum ada aktivitas tercatat.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Quick Actions & Master Stats -->
            <div class="space-y-6">
                <!-- Master Data Stats -->
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <h3 class="font-bold text-slate-800 mb-4 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-database text-slate-500"><path d="M3 5c0 1.66 4 3 9 3s9-1.34 9-3"></path><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path></svg>
                        Data Master
                    </h3>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50">
                            <div class="flex items-center gap-3">
                                <div class="h-8 w-8 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-box"><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"></path><path d="m7.5 4.27 9 5.15"></path><polyline points="3.29 7 12 12 20.71 7"></polyline><line x1="12" x2="12" y1="22" y2="12"></line></svg>
                                </div>
                                <span class="text-sm font-semibold text-slate-600">Produk</span>
                            </div>
                            <span class="text-lg font-bold text-slate-900">{{ $itemsCount ?? 0 }}</span>
                        </div>
                        <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50">
                            <div class="flex items-center gap-3">
                                <div class="h-8 w-8 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-users"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M22 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                                </div>
                                <span class="text-sm font-semibold text-slate-600">Customer</span>
                            </div>
                            <span class="text-lg font-bold text-slate-900">{{ $customersCount ?? 0 }}</span>
                        </div>
                        <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50">
                            <div class="flex items-center gap-3">
                                <div class="h-8 w-8 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-truck"><path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"></path><path d="M15 18H9"></path><path d="M19 18h2a1 1 0 0 0 1-1v-5h-7v7Z"></path><path d="M13 9h4"></path><circle cx="7" cy="18" r="2"></circle><circle cx="17" cy="18" r="2"></circle></svg>
                                </div>
                                <span class="text-sm font-semibold text-slate-600">Supplier</span>
                            </div>
                            <span class="text-lg font-bold text-slate-900">{{ $suppliersCount ?? 0 }}</span>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="rounded-2xl border border-slate-200 bg-indigo-600 p-5 shadow-lg shadow-indigo-200">
                    <h3 class="font-bold text-white mb-4 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-zap"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
                        Aksi Cepat
                    </h3>
                    <div class="grid grid-cols-1 gap-2">
                        <a href="{{ route('invoices.index') }}" class="flex items-center gap-3 rounded-xl bg-white/10 p-3 text-sm font-bold text-white hover:bg-white/20 transition-colors">
                            <div class="h-8 w-8 rounded-lg bg-white/20 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-plus"><path d="M5 12h14"></path><path d="M12 5v14"></path></svg>
                            </div>
                            Input Invoice Baru
                        </a>
                        <a href="{{ route('invoices.po_pending') }}" class="flex items-center gap-3 rounded-xl bg-white/10 p-3 text-sm font-bold text-white hover:bg-white/20 transition-colors">
                            <div class="h-8 w-8 rounded-lg bg-white/20 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-clock-3"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                            </div>
                            PO Belum Terkirim
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
