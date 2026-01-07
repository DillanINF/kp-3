<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laporan Keuntungan & Kerugian</title>
    <style>
        * { font-family: DejaVu Sans, sans-serif; }
        body { font-size: 12px; color: #111827; }
        .muted { color: #6B7280; }
        .title { font-size: 18px; font-weight: 700; margin: 0 0 4px 0; }
        .subtitle { margin: 0 0 12px 0; }
        .meta { margin: 0 0 4px 0; }
        .summary { width: 100%; border-collapse: collapse; margin-top: 12px; }
        .summary td { border: 1px solid #E5E7EB; padding: 8px 10px; vertical-align: top; }
        .summary .label { font-size: 10px; text-transform: uppercase; letter-spacing: 0.04em; color: #6B7280; font-weight: 700; }
        .summary .value { font-size: 14px; font-weight: 700; margin-top: 4px; }
        .section-title { font-size: 13px; font-weight: 700; margin: 18px 0 8px 0; }
        table.data { width: 100%; border-collapse: collapse; }
        table.data th { background: #F3F4F6; font-size: 10px; text-transform: uppercase; letter-spacing: 0.04em; color: #374151; padding: 8px; border: 1px solid #E5E7EB; }
        table.data td { padding: 8px; border: 1px solid #E5E7EB; }
        .right { text-align: right; }
        .center { text-align: center; }
        .footer { margin-top: 16px; font-size: 10px; color: #6B7280; }
    </style>
</head>
<body>
    <div>
        <div class="title">Laporan Keuntungan & Kerugian</div>
        <div class="subtitle muted">{{ config('app.name') }}</div>

        <div class="meta"><span class="muted">Periode:</span> {{ ($fromDate ?? now()->startOfMonth())->format('d/m/Y') }} - {{ ($toDate ?? now())->format('d/m/Y') }}</div>
        <div class="meta"><span class="muted">Dicetak:</span> {{ ($printedAt ?? now())->format('d/m/Y H:i') }} @if(!empty($printedBy?->name))<span class="muted">oleh</span> {{ $printedBy->name }}@endif</div>
    </div>

    <table class="summary">
        <tr>
            <td>
                <div class="label">Omzet Penjualan</div>
                <div class="value">Rp {{ number_format($salesRevenue ?? 0, 0, ',', '.') }}</div>
            </td>
            <td>
                <div class="label">HPP Penjualan</div>
                <div class="value">Rp {{ number_format($salesCogs ?? 0, 0, ',', '.') }}</div>
            </td>
            <td>
                <div class="label">Keuntungan (Sale)</div>
                <div class="value">Rp {{ number_format($salesProfit ?? 0, 0, ',', '.') }}</div>
            </td>
        </tr>
        <tr>
            <td>
                <div class="label">Kerugian Rusak</div>
                <div class="value">Rp {{ number_format($lossDamaged ?? 0, 0, ',', '.') }}</div>
            </td>
            <td>
                <div class="label">Kerugian Expired</div>
                <div class="value">Rp {{ number_format($lossExpired ?? 0, 0, ',', '.') }}</div>
            </td>
            <td>
                <div class="label">Net</div>
                <div class="value">Rp {{ number_format($net ?? 0, 0, ',', '.') }}</div>
            </td>
        </tr>
    </table>

    <div class="section-title">Rincian Transaksi</div>

    <table class="data">
        <thead>
            <tr>
                <th style="width: 78px;">Tanggal</th>
                <th style="width: 70px;">Tipe</th>
                <th>Customer</th>
                <th>Barang</th>
                <th class="right" style="width: 42px;">Qty</th>
                <th class="right" style="width: 90px;">Harga Beli</th>
                <th class="right" style="width: 90px;">Harga Jual</th>
                <th class="right" style="width: 110px;">Profit/Loss</th>
            </tr>
        </thead>
        <tbody>
            @forelse(($rows ?? []) as $r)
                @php
                    $qty = (int) $r->qty;
                    $buy = (int) $r->buy_price;
                    $sell = (int) $r->sell_price;
                    $profitLoss = 0;
                    if ($r->type === 'sale') {
                        $profitLoss = ($sell - $buy) * $qty;
                    } else {
                        $profitLoss = 0 - ($buy * $qty);
                    }
                @endphp
                <tr>
                    <td>{{ !empty($r->date) ? \Carbon\Carbon::parse($r->date)->format('d/m/Y') : '-' }}</td>
                    <td class="center">{{ strtoupper($r->type ?? '-') }}</td>
                    <td>{{ $r->customer?->name ?? '-' }}</td>
                    <td>{{ $r->item?->name ?? '-' }}</td>
                    <td class="right">{{ $qty }}</td>
                    <td class="right">Rp {{ number_format($buy, 0, ',', '.') }}</td>
                    <td class="right">Rp {{ number_format($sell, 0, ',', '.') }}</td>
                    <td class="right">Rp {{ number_format($profitLoss, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="center muted">Belum ada data.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">Sumber: barang keluar (penjualan + rusak/expired).</div>
</body>
</html>
