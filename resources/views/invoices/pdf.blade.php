<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice->invoice_no ?? '' }}</title>

    <style>
        * {
            font-family: DejaVu Sans, sans-serif;
        }

        body {
            font-size: 12px;
            color: #1F2937;
            margin: 0;
            padding: 24px;
            background: #fff;
        }

        .container {
            max-width: 800px;
            margin: auto;
        }

        .header {
            border-bottom: 2px solid #E5E7EB;
            padding-bottom: 14px;
            margin-bottom: 20px;
        }

        .flex {
            width: 100%;
        }

        .company {
            font-size: 18px;
            font-weight: bold;
        }

        .muted {
            color: #6B7280;
            font-size: 11px;
        }

        .invoice-title {
            font-size: 28px;
            font-weight: 800;
            text-align: right;
            letter-spacing: 2px;
        }

        .box {
            border: 1px solid #E5E7EB;
            border-radius: 8px;
            padding: 12px;
            margin-top: 15px;
        }

        .meta td {
            padding: 4px 0;
        }

        .meta-label {
            width: 120px;
            color: #6B7280;
        }

        .meta-value {
            font-weight: 600;
        }

        .section-title {
            margin: 20px 0 8px;
            font-weight: bold;
            font-size: 13px;
        }

        table.data {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        table.data th {
            background: #F3F4F6;
            padding: 10px;
            text-align: left;
            font-size: 11px;
            text-transform: uppercase;
        }

        table.data td {
            padding: 10px;
            border-bottom: 1px solid #E5E7EB;
        }

        .right {
            text-align: right;
        }

        .center {
            text-align: center;
        }

        .total {
            font-weight: bold;
            background: #F9FAFB;
        }

        .footer {
            margin-top: 20px;
            font-size: 11px;
            color: #6B7280;
        }

        .sign {
            margin-top: 50px;
            width: 100%;
        }

        .sign td {
            width: 50%;
            text-align: center;
        }

        .sign-line {
            margin-top: 60px;
            border-top: 1px solid #9CA3AF;
            width: 200px;
            margin-left: auto;
            margin-right: auto;
        }
    </style>
</head>

<body>
    <div class="container">

        <!-- HEADER -->
        <div class="header">
            <table class="flex">
                <tr>
                    <td style="width: 80px;">
                        <img src="{{ url('LOGO.png') }}" style="width:70px;">
                    </td>
                    <td>
                        <div class="company">PT CAM JAYA ABADI</div>
                        <div class="muted">Jl. Raya Cibinong No. 123</div>
                        <div class="muted">Bogor, Jawa Barat</div>
                        <div class="muted">Telp: 0812-3456-7890</div>
                    </td>
                    <td class="invoice-title">
                        INVOICE
                    </td>
                </tr>
            </table>
        </div>

        <!-- INFO -->
        <div class="box">
            <table class="meta">
                <tr>
                    <td class="meta-label">No Invoice</td>
                    <td class="meta-value">: {{ $invoice->invoice_no ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="meta-label">No PO</td>
                    <td class="meta-value">: {{ $poNo ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="meta-label">Tanggal</td>
                    <td class="meta-value">:
                        {{ $invoice->date ? $invoice->date->format('d/m/Y') : '-' }}
                    </td>
                </tr>
                <tr>
                    <td class="meta-label">Customer</td>
                    <td class="meta-value">: {{ $invoice->customer?->name ?? '-' }}</td>
                </tr>
            </table>
        </div>

        <!-- TABLE -->
        <div class="section-title">Rincian Produk</div>

        <table class="data">
            <thead>
                <tr>
                    <th style="width:30px;">No</th>
                    <th>Produk</th>
                    <th class="right">Qty</th>
                    <th class="right">Harga</th>
                    <th class="right">Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($details as $d)
                    @php
                        $qty = (int) ($d->qty ?? 0);
                        $price = (int) ($d->price ?? 0);
                        $total = $qty * $price;
                    @endphp
                    <tr>
                        <td class="center">{{ $loop->iteration }}</td>
                        <td>{{ $d->item?->name ?? '-' }}</td>
                        <td class="right">{{ $qty }}</td>
                        <td class="right">Rp {{ number_format($price,0,',','.') }}</td>
                        <td class="right">Rp {{ number_format($total,0,',','.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="center">Tidak ada data</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr class="total">
                    <td colspan="4" class="right">Total</td>
                    <td class="right">
                        Rp {{ number_format($grandTotal ?? 0,0,',','.') }}
                    </td>
                </tr>
            </tfoot>
        </table>

        <!-- FOOTER -->
        <div class="footer">
            Dicetak: {{ now()->format('d/m/Y H:i') }}
        </div>

        <!-- SIGN -->
        <table class="sign">
            <tr>
                <td>
                    Penerima
                    <div class="sign-line"></div>
                </td>
                <td>
                    Hormat Kami
                    <div class="sign-line"></div>
                    {{ $printedBy?->name ?? 'Admin' }}
                </td>
            </tr>
        </table>

    </div>
</body>

</html>