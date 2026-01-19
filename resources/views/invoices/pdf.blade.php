<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invoice {{ $invoice->invoice_no ?? '' }}</title>
    <style>
        * { font-family: DejaVu Sans, sans-serif; }
        body { font-size: 12px; color: #111827; margin: 0; padding: 18px; }
        .muted { color: #6B7280; }
        .header { border: 1px solid #E5E7EB; border-radius: 10px; padding: 14px 14px 10px 14px; background: #F9FAFB; }
        .title { font-size: 34px; font-weight: 800; margin: 0; text-align: center; letter-spacing: 0.02em; }
        .meta-grid { width: 100%; margin-top: 10px; border-collapse: collapse; }
        .meta-grid td { padding: 3px 0; vertical-align: top; }
        .meta-label { width: 110px; color: #6B7280; }
        .meta-value { color: #111827; font-weight: 600; }
        .section-title { font-size: 12px; font-weight: 800; margin: 14px 0 8px 0; color: #111827; }
        table.data { width: 100%; border-collapse: collapse; border: 1px solid #E5E7EB; border-radius: 10px; overflow: hidden; }
        table.data thead th { background: #F3F4F6; font-size: 10px; text-transform: uppercase; letter-spacing: 0.06em; color: #374151; padding: 9px; border-bottom: 1px solid #E5E7EB; }
        table.data tbody td { padding: 9px; border-bottom: 1px solid #E5E7EB; vertical-align: top; }
        table.data tbody tr:last-child td { border-bottom: none; }
        .right { text-align: right; }
        .center { text-align: center; }
        tr.total-row td { background: #F3F4F6; font-weight: 800; }
        .print-meta { margin-top: 14px; color: #6B7280; font-size: 11px; }
        table.sign { width: 100%; margin-top: 18px; border-collapse: collapse; }
        table.sign td { width: 50%; vertical-align: top; }
        .sign-label { color: #111827; font-weight: 700; margin-bottom: 54px; }
        .sign-line { border-top: 1px solid #9CA3AF; width: 180px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">INVOICE</div>

        <table class="meta-grid">
            <tr>
                <td style="width: 50%; padding-right: 16px;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td class="meta-label">No Invoice</td>
                            <td class="meta-value">: {{ $invoice->invoice_no ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="meta-label">No PO</td>
                            <td class="meta-value">: {{ $poNo ?? ($invoice->po_no ?? '-') }}</td>
                        </tr>
                        <tr>
                            <td class="meta-label">Tanggal</td>
                            <td class="meta-value">: {{ ($invoice->date ?? null) ? $invoice->date->format('d/m/Y') : '-' }}</td>
                        </tr>
                    </table>
                </td>
                <td style="width: 50%;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td class="meta-label">Customer</td>
                            <td class="meta-value">: {{ $invoice->customer?->name ?? '-' }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    <div class="section-title">Rincian Produk (Terkirim)</div>
    <table class="data">
        <thead>
            <tr>
                <th style="width: 30px;" class="center">No</th>
                <th>Produk</th>
                <th style="width: 60px;" class="right">Qty</th>
                <th style="width: 110px;" class="right">Harga</th>
                <th style="width: 120px;" class="right">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse(($details ?? []) as $d)
                @php
                    $qty = (int) ($d->qty ?? 0);
                    $price = (int) ($d->price ?? 0);
                    $total = (int) ($d->total ?? ($qty * $price));
                @endphp
                <tr>
                    <td class="center">{{ $loop->iteration }}</td>
                    <td>{{ $d->item?->name ?? '-' }}</td>
                    <td class="right">{{ $qty }}</td>
                    <td class="right">Rp {{ number_format($price, 0, ',', '.') }}</td>
                    <td class="right">Rp {{ number_format($total, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="center muted">Belum ada rincian item.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="4" class="right">Sub Total / Total Keseluruhan</td>
                <td class="right">Rp {{ number_format(($grandTotal ?? null) !== null ? $grandTotal : ($detailsTotal ?? 0), 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    @if(($pending ?? collect())->count() > 0)
        <div class="section-title">Rincian Produk (Pending / PO Belum Terkirim)</div>
        <table class="data">
            <thead>
                <tr>
                    <th style="width: 30px;" class="center">No</th>
                    <th>Produk</th>
                    <th style="width: 60px;" class="right">Qty</th>
                    <th style="width: 110px;" class="right">Harga</th>
                    <th style="width: 120px;" class="right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach(($pending ?? []) as $p)
                    @php
                        $qty = (int) ($p->qty ?? 0);
                        $price = (int) ($p->price ?? 0);
                        $total = $qty * $price;
                    @endphp
                    <tr>
                        <td class="center">{{ $loop->iteration }}</td>
                        <td>{{ $p->item?->name ?? '-' }}</td>
                        <td class="right">{{ $qty }}</td>
                        <td class="right">Rp {{ number_format($price, 0, ',', '.') }}</td>
                        <td class="right">Rp {{ number_format($total, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="print-meta">Dicetak: {{ ($printedAt ?? now())->format('d/m/Y H:i') }} @if(!empty($printedBy?->name)) oleh {{ $printedBy->name }}@endif</div>

    <table class="sign">
        <tr>
            <td>
                <div class="sign-label">Penerima</div>
                <div class="sign-line"></div>
            </td>
            <td class="right">
                <div class="sign-label">Hormat Kami</div>
                <div class="sign-line" style="margin-left: auto;"></div>
            </td>
        </tr>
    </table>
</body>
</html>
