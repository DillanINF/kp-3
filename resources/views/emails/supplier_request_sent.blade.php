<div>
    <div style="font-weight: 600; font-size: 16px;">Permintaan Barang</div>
    <div style="margin-top: 6px;">No: {{ $request->request_no }}</div>
    <div>Supplier: {{ $request->supplier?->name }}</div>
    <div>Waktu: {{ $request->created_at?->timezone(config('app.timezone'))->format('d/m/Y H:i:s') }}</div>

    @if($request->notes)
        <div style="margin-top: 10px;">Catatan: {{ $request->notes }}</div>
    @endif

    <div style="margin-top: 14px; font-weight: 600;">Detail Item</div>

    <table style="width: 100%; border-collapse: collapse; margin-top: 8px;">
        <thead>
            <tr>
                <th style="border: 1px solid #e2e8f0; padding: 8px; text-align: left;">Produk</th>
                <th style="border: 1px solid #e2e8f0; padding: 8px; text-align: left;">Satuan</th>
                <th style="border: 1px solid #e2e8f0; padding: 8px; text-align: left;">Qty</th>
                <th style="border: 1px solid #e2e8f0; padding: 8px; text-align: right;">Harga</th>
                <th style="border: 1px solid #e2e8f0; padding: 8px; text-align: right;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($request->items as $it)
                <tr>
                    <td style="border: 1px solid #e2e8f0; padding: 8px;">{{ $it->item?->name ?? '-' }}</td>
                    <td style="border: 1px solid #e2e8f0; padding: 8px;">{{ $it->unit }}</td>
                    <td style="border: 1px solid #e2e8f0; padding: 8px;">{{ $it->qty }}</td>
                    <td style="border: 1px solid #e2e8f0; padding: 8px; text-align: right;">Rp {{ number_format($it->price ?? 0, 0, ',', '.') }}</td>
                    <td style="border: 1px solid #e2e8f0; padding: 8px; text-align: right;">Rp {{ number_format($it->subtotal ?? 0, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 12px; font-weight: 600;">Total Qty: {{ $request->total_qty }}</div>
    <div style="font-weight: 600;">Total: Rp {{ number_format($request->total_amount ?? 0, 0, ',', '.') }}</div>
</div>
