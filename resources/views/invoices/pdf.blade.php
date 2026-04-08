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

        /* ================= TABLE ================= */
        table.data {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
            table-layout: fixed;
        }

        table.data th,
        table.data td {
            padding: 10px;
            border-bottom: 1px solid #E5E7EB;
            vertical-align: middle;
        }

        table.data th {
            background: #F3F4F6;
            font-size: 11px;
            text-transform: uppercase;
        }

        table.data tr {
            height: 45px;
        }

        /* WIDTH KOLOM */
        .col-no {
            width: 5%;
            text-align: center;
            padding-right: 10px; /* 🔥 kasih jarak ke kanan */
        }

        .col-produk {
            width: 45%;
            text-align: left;
            padding-left: 25px; /* 🔥 DIGESER KE KANAN */
            white-space: nowrap;
        }

        .col-qty {
            width: 10%;
            text-align: center;
        }

        .col-harga {
            width: 20%;
            text-align: right;
        }

        .col-total {
            width: 20%;
            text-align: right;
        }

        .right { text-align: right; }
        .center { text-align: center; }

        .total {
            font-weight: bold;
            background: #F9FAFB;
        }

        .footer {
            margin-top: 20px;
            font-size: 11px;
            color: #6B7280;
        }

        /* ================= SIGN ================= */
        .sign {
            margin-top: 50px;
            width: 100%;
            table-layout: fixed;
        }

        .sign td {
            width: 50%;
            vertical-align: top;
        }

        .sign td:first-child {
            text-align: left;
        }

        .sign td:last-child {
            text-align: right;
        }

        .sign-line {
            margin-top: 60px;
            border-top: 1px solid #9CA3AF;
            width: 200px;
        }

        .sign td:first-child .sign-line {
            margin-left: 0;
        }

        .sign td:last-child .sign-line {
            margin-left: auto;
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
                        <img src="{{ public_path('LOGO.png') }}" style="width:70px;">
                    </td>
                    <td>
                        <div class="company">PT CAM JAYA ABADI</div>
                        <div class="muted">Jl. Wahana Bakti No.65 17510</div>
                        <div class="muted">Bekasi, Jawa Barat</div>
                        <div class="muted">Telp: 021 - 8837 1899</div>
                    </td>
                    <td class="invoice-title">
                        INVOICE
                    </td>
                </tr>
            </table>
        </div>

        <!-- INFO -->
        <div class="box">
            <table class="meta" style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td class="meta-label" style="width: 100px;">No Invoice</td>
                    <td class="meta-value" style="width: 180px;">: {{ $invoice->invoice_no ?? '-' }}</td>
                    <td class="meta-label" style="width: 80px; padding-left: 30px;">Customer</td>
                    <td class="meta-value">: {{ $invoice->customer?->name ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="meta-label">No PO</td>
                    <td class="meta-value">: {{ $poNo ?? '-' }}</td>
                    <td class="meta-label" style="padding-left: 30px; vertical-align: top;">Alamat Customer
                    <td class="meta-value" style="vertical-align: top;">: {{ $invoice->address ?? '-' }}</td>
                </tr>
               
                <tr>
                    <td class="meta-label">Tgl. Kirim</td>
                    <td class="meta-value">: {{ $invoice->delivery_date ? $invoice->delivery_date->format('d/m/Y') : '-' }}</td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td class="meta-label">Pengirim</td>
                    <td class="meta-value">: {{ $invoice->pengirim?->name ?? '-' }}</td>
                    <td class="meta-label" style="padding-left: 30px;">Detail Pengirim</td>
                    <td class="meta-value">: {{ $invoice->pengirim ? ($invoice->pengirim->vehicle_type . ' (' . $invoice->pengirim->license_plate . ')') : '-' }}</td>
                </tr>
                @if($invoice->pengirim && $invoice->pengirim->phone)
                <tr>
                    <td class="meta-label">Telp Pengirim</td>
                    <td class="meta-value">: {{ $invoice->pengirim->phone }}</td>
                    <td></td>
                    <td></td>
                </tr>
                @endif
            </table>
        </div>

        <!-- TABLE -->
        <div class="section-title">Rincian Produk</div>

        <table class="data">
            <thead>
                <tr>
                    <th class="col-no">No</th>
                    <th class="col-produk">Produk</th>
                    <th class="col-qty">Qty</th>
                    <th class="col-harga">Harga</th>
                    <th class="col-total">Total</th>
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
                        <td class="col-no">{{ $loop->iteration }}</td>
                        <td class="col-produk">{{ $d->item?->name ?? '-' }}</td>
                        <td class="col-qty">{{ $qty }}</td>
                        <td class="col-harga">Rp {{ number_format($price,0,',','.') }}</td>
                        <td class="col-total">Rp {{ number_format($total,0,',','.') }}</td>
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