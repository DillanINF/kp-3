<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>LAPORAN KEUNTUNGAN & KERUGIAN</title>
    <style>
        * { 
            font-family: DejaVu Sans, sans-serif; 
            box-sizing: border-box;
        }
        body { 
            font-size: 12px; 
            color: #111827; 
            margin: 0;
            padding: 15px;
            width: 100%;
            overflow-x: hidden;
        }
        .container {
            max-width: 100%;
            overflow-x: auto;
        }
        .muted { color: #6B7280; }
        .title { 
            font-size: 24px; 
            font-weight: 700; 
            margin: 0 10px 4px 0; 
        }
        .subtitle { margin: 0 0 12px 0; }
        .meta { margin: 0 0 4px 0; }
        .summary { 
            width: 100%; 
            max-width: 100%;
            border-collapse: collapse; 
            margin-top: 12px;
            table-layout: fixed;
        }
        .summary td { 
            border: 1px solid #E5E7EB; 
            padding: 8px 10px; 
            vertical-align: top; 
            word-wrap: break-word;
        }
        .summary .label { 
            font-size: 10px; 
            text-transform: uppercase; 
            letter-spacing: 0.04em; 
            color: #6B7280; 
            font-weight: 700; 
        }
        .summary .value { 
            font-size: 14px; 
            font-weight: 700; 
            margin-top: 4px; 
        }
        .section-title { 
            font-size: 13px; 
            font-weight: 700; 
            margin: 18px 0 8px 0; 
        }
        table.data { 
            width: 100%; 
            max-width: 100%;
            border-collapse: collapse; 
            table-layout: fixed;
        }
        table.data th { 
            background: #F3F4F6; 
            font-size: 10px; 
            text-transform: uppercase; 
            letter-spacing: 0.04em; 
            color: #374151; 
            padding: 8px; 
            border: 1px solid #E5E7EB; 
            word-wrap: break-word;
        }
        table.data td { 
            padding: 8px; 
            border: 1px solid #E5E7EB; 
            word-wrap: break-word;
        }
        .right { text-align: right; }
        .center { text-align: center; }
        .footer { 
            margin-top: 16px; 
            font-size: 10px; 
            color: #6B7280; 
        }
        .print-meta { 
            margin-top: 14px; 
            color: #6B7280; 
            font-size: 11px; 
            max-width: 100%;
            word-wrap: break-word;
        }
        table.sign { 
            width: 100%; 
            max-width: 100%;
            margin-top: 18px; 
            border-collapse: collapse;
            table-layout: fixed;
        }
        table.sign td { 
            width: 50%; 
            vertical-align: top; 
            word-wrap: break-word;
        }
        .sign-label { 
            color: #111827; 
            font-weight: 700; 
            margin-bottom: 54px; 
        }
        .sign-label1 { 
            color: #111827; 
            font-weight: 700; 
            margin-bottom: 54px; 
            text-align: left; 
            margin-left: 170px; 
        }
        .sign-line { 
            border-top: 1px solid #9CA3AF; 
            width: 180px; 
            max-width: 100%;
        }
        
        /* Kolom dengan lebar tetap untuk tabel data */
        .col-date { width: 78px; min-width: 78px; max-width: 78px; }
        .col-type { width: 70px; min-width: 70px; max-width: 70px; }
        .col-customer { width: 120px; min-width: 120px; max-width: 120px; }
        .col-item { width: 150px; min-width: 150px; max-width: 150px; }
        .col-qty { width: 42px; min-width: 42px; max-width: 42px; }
        .col-buy-price { width: 90px; min-width: 90px; max-width: 90px; }
        .col-sell-price { width: 90px; min-width: 90px; max-width: 90px; }
        .col-profit { width: 110px; min-width: 110px; max-width: 110px; }
        
        /* Untuk cetakan PDF */
        @media print {
            body {
                padding: 10px;
                margin: 0;
                width: 100%;
                max-width: 100%;
            }
            .container {
                width: 100%;
                max-width: 100%;
            }
            table {
                page-break-inside: auto;
            }
            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div>
            <div class="title center">LAPORAN KEUNTUNGAN & KERUGIAN</div>
            <div class="subtitle muted"></div>

            @php
                $monthParam = trim((string) ($month ?? ''));
            @endphp
            <div class="meta"><span class="muted">Periode:</span>
                @if($monthParam !== '')
                    {{ \Carbon\Carbon::createFromFormat('Y-m', $monthParam)->translatedFormat('F Y') }}
                @else
                    {{ ($fromDate ?? now()->startOfMonth())->format('d/m/Y') }} - {{ ($toDate ?? now())->format('d/m/Y') }}
                @endif
            </div>
        </div>

        <table class="summary">
            <tr>
                <td style="width: 33.33%;">
                    <div class="label">Pendapatan</div>
                    <div class="value">Rp {{ number_format($salesRevenue ?? 0, 0, ',', '.') }}</div>
                </td>
                <td style="width: 33.33%;">
                    <div class="label">Modal</div>
                    <div class="value">Rp {{ number_format($salesCogs ?? 0, 0, ',', '.') }}</div>
                </td>
                <td style="width: 33.33%;">
                    <div class="label">Profit</div>
                    <div class="value">Rp {{ number_format($salesProfit ?? 0, 0, ',', '.') }}</div>
                </td>
            </tr>
            <tr>
                <td style="width: 33.33%;">
                    <div class="label">Kerugian Rusak</div>
                    <div class="value">Rp {{ number_format($lossDamaged ?? 0, 0, ',', '.') }}</div>
                </td>
                <td style="width: 33.33%;">
                    <div class="label">Kerugian Expired</div>
                    <div class="value">Rp {{ number_format($lossExpired ?? 0, 0, ',', '.') }}</div>
                </td>
                <td style="width: 33.33%;">
                    <div class="label">Profit Bersih</div>
                    <div class="value">Rp {{ number_format($net ?? 0, 0, ',', '.') }}</div>
                </td>
            </tr>
        </table>

        <div class="section-title">Rincian Transaksi</div>

        <table class="data">
            <thead>
                <tr>
                    <th class="col-date">Tanggal</th>
                    <th class="col-type">Tipe</th>
                    <th class="col-customer">Customer</th>
                    <th class="col-item">Barang</th>
                    <th class="col-qty right">Qty</th>
                    <th class="col-buy-price right">Harga Beli</th>
                    <th class="col-sell-price right">Harga Jual</th>
                    <th class="col-profit right">Profit</th>
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
                        <td class="col-date">{{ !empty($r->date) ? \Carbon\Carbon::parse($r->date)->format('d/m/Y') : '-' }}</td>
                        <td class="col-type center">{{ strtoupper($r->type ?? '-') }}</td>
                        <td class="col-customer">{{ $r->customer?->name ?? '-' }}</td>
                        <td class="col-item">{{ $r->item?->name ?? '-' }}</td>
                        <td class="col-qty right">{{ $qty }}</td>
                        <td class="col-buy-price right">Rp {{ number_format($buy, 0, ',', '.') }}</td>
                        <td class="col-sell-price right">Rp {{ number_format($sell, 0, ',', '.') }}</td>
                        <td class="col-profit right">Rp {{ number_format($profitLoss, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="center muted">Belum ada data.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="print-meta">Dicetak: {{ ($printedAt ?? now())->format('d/m/Y H:i') }} @if(!empty($printedBy?->name)) oleh {{ $printedBy->name }}@endif</div>

        <table class="sign">
            <tr>
                <td style="width: 50%;">
                    <div class="sign-label">Diperiksa</div>
                    <div class="sign-line"></div>
                </td>
                <td style="width: 50%;" class="right">
                    <div class="sign-label1">Dibuat</div>
                    <div class="sign-label1">@if(!empty($printedBy?->name)){{ $printedBy->name }}@endif</div>
                </td>
            </tr>
        </table>

        <div class="footer">Sumber: barang keluar (penjualan + rusak/expired).</div>
    </div>
</body>
</html>