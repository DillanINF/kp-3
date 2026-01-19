<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceDetail;
use App\Models\InvoiceApproval;
use App\Models\Item;
use App\Models\ItemOut;
use App\Models\PoPendingItem;
use App\Models\SupplierItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    public function index()
    {
        $customers = Customer::query()->orderBy('name')->get();
        $invoices = Invoice::query()
            ->with('customer')
            ->with('approval')
            ->withCount('poPendingItems')
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->get();

        return view('invoices.index', [
            'customers' => $customers,
            'invoices' => $invoices,
        ]);
    }

    public function accept(Invoice $invoice)
    {
        DB::transaction(function () use ($invoice) {
            $invoice->refresh();

            $pendingCount = PoPendingItem::query()
                ->where('invoice_id', $invoice->id)
                ->count();

            if ($pendingCount > 0) {
                abort(422, 'Masih ada PO belum terkirim. Selesaikan dulu sebelum Accept.');
            }

            $approval = InvoiceApproval::query()->firstOrNew([
                'invoice_id' => $invoice->id,
            ]);
            $approval->status = 'accept';
            $approval->accepted_at = now();
            $approval->save();
        });

        return redirect()->route('invoices.index')->with('success', 'Invoice berhasil di-Accept.');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => ['required', 'integer', 'exists:customers,id'],
        ]);

        $invoice = DB::transaction(function () use ($validated) {
            $existing = Invoice::query()
                ->lockForUpdate()
                ->pluck('invoice_no')
                ->filter()
                ->map(fn ($no) => (string) $no)
                ->all();

            $used = [];
            foreach ($existing as $no) {
                if (preg_match('/^INV-(\d+)$/', $no, $m)) {
                    $n = (int) $m[1];
                    if ($n > 0) $used[$n] = true;
                }
            }

            $next = 1;
            while (isset($used[$next])) {
                $next++;
            }

            $invoiceNo = 'INV-' . str_pad((string) $next, 4, '0', STR_PAD_LEFT);

            return Invoice::query()->create([
                'invoice_no' => $invoiceNo,
                'customer_id' => $validated['customer_id'],
                'date' => now()->toDateString(),
                'status' => 'draft',
                'grand_total' => 0,
                'qty_total' => 0,
            ]);
        });

        return redirect()->route('invoices.input_po_by_no', ['invoiceNo' => $invoice->invoice_no]);
    }

    public function inputPo(Invoice $invoice)
    {
        $invoice->load(['customer', 'details.item']);
        $items = Item::query()->where('item_type', 'regular')->orderBy('name')->get();

        return view('invoices.input_po', [
            'invoice' => $invoice,
            'items' => $items,
        ]);
    }

    public function inputPoByNo(string $invoiceNo)
    {
        $invoice = Invoice::query()->where('invoice_no', $invoiceNo)->firstOrFail();
        $invoice->load(['customer', 'details.item']);
        $items = Item::query()->where('item_type', 'regular')->orderBy('name')->get();

        return view('invoices.input_po', [
            'invoice' => $invoice,
            'items' => $items,
        ]);
    }

    public function storePo(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.item_id' => ['required', 'integer', 'distinct', 'exists:items,id'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
            'items.*.price' => ['required', 'integer', 'min:0'],
        ]);

        $result = DB::transaction(function () use ($invoice, $validated) {
            $invoice->refresh();
            $invoice->load('customer');

            $poNo = (string) ($invoice->po_no ?: ('PO-' . (string) ($invoice->invoice_no ?? '')));

            $hasPending = false;
            $totalPendingQty = 0;

            if ($invoice->status !== 'draft') {
                abort(422, 'Invoice sudah diproses dan tidak bisa diubah.');
            }

            InvoiceDetail::query()->where('invoice_id', $invoice->id)->delete();

            $grandTotal = 0;
            $qtyTotal = 0;

            foreach ($validated['items'] as $row) {
                $itemId = (int) $row['item_id'];
                $qty = (int) $row['qty'];
                $price = (int) $row['price'];

                $item = Item::query()->lockForUpdate()->findOrFail($itemId);
                if ($item->item_type !== 'regular') {
                    abort(422, 'Barang yang dipilih tidak valid untuk invoice.');
                }

                $buyPrice = (int) (SupplierItem::query()
                    ->where('item_id', $item->id)
                    ->orderBy('buy_price')
                    ->value('buy_price') ?? 0);

                $sellPrice = (int) ($item->price ?? 0);

                $deliverQty = min((int) ($item->stock ?? 0), $qty);
                $pendingQty = max(0, $qty - $deliverQty);

                if ($pendingQty > 0) {
                    $hasPending = true;
                    $totalPendingQty += $pendingQty;
                    PoPendingItem::query()->create([
                        'invoice_id' => $invoice->id,
                        'invoice_no' => $invoice->invoice_no,
                        'po_no' => $poNo,
                        'customer_id' => $invoice->customer_id,
                        'item_id' => $itemId,
                        'qty' => $pendingQty,
                        'price' => $price,
                        'status' => 'pending',
                    ]);
                }

                if ($deliverQty <= 0) {
                    continue;
                }

                $total = $deliverQty * $price;

                InvoiceDetail::query()->create([
                    'invoice_id' => $invoice->id,
                    'item_id' => $itemId,
                    'qty' => $deliverQty,
                    'price' => $price,
                    'total' => $total,
                ]);

                $item->decrement('stock', $deliverQty);

                ItemOut::query()->create([
                    'type' => 'sale',
                    'customer_id' => $invoice->customer_id,
                    'item_id' => $itemId,
                    'qty' => $deliverQty,
                    'date' => $invoice->date,
                    'buy_price' => $buyPrice,
                    'sell_price' => $sellPrice,
                ]);

                $grandTotal += $total;
                $qtyTotal += $deliverQty;
            }

            $invoice->po_no = $poNo;
            $invoice->grand_total = $grandTotal;
            $invoice->qty_total = $qtyTotal;
            $invoice->status = $qtyTotal > 0 ? 'posted' : 'draft';
            $invoice->save();

            return [
                'has_pending' => $hasPending,
                'total_pending_qty' => $totalPendingQty,
            ];
        });

        if (!empty($result['has_pending'])) {
            $pendingQty = (int) ($result['total_pending_qty'] ?? 0);
            $msg = 'Qty yang kamu input melebihi stok. ';
            if ($pendingQty > 0) {
                $msg .= 'Sisa pesanan ' . $pendingQty . ' pcs otomatis masuk ke menu PO Belum Terkirim.';
            } else {
                $msg .= 'Sisa pesanan otomatis masuk ke menu PO Belum Terkirim.';
            }

            return redirect()->route('invoices.index')->with('warning', $msg);
        }

        return redirect()->route('invoices.index')->with('success', 'PO berhasil disimpan.');
    }

    public function pdf(Request $request, Invoice $invoice)
    {
        $invoice->load([
            'customer',
            'details.item',
            'poPendingItems.item',
        ]);

        $printedAt = now();

        $poNo = (string) ($invoice->po_no ?: ('PO-' . (string) ($invoice->invoice_no ?? '')));

        $details = $invoice->details ?? collect();
        $pending = ($invoice->poPendingItems ?? collect())
            ->filter(fn ($r) => (string) ($r->status ?? '') === 'pending')
            ->values();

        $detailsTotal = $details->sum(fn ($d) => (int) ($d->total ?? ((int) ($d->qty ?? 0) * (int) ($d->price ?? 0))));
        $pendingTotal = $pending->sum(fn ($d) => (int) ($d->qty ?? 0) * (int) ($d->price ?? 0));
        $grandTotal = (int) $detailsTotal + (int) $pendingTotal;

        $pdf = Pdf::loadView('invoices.pdf', [
            'invoice' => $invoice,
            'poNo' => $poNo,
            'printedAt' => $printedAt,
            'printedBy' => $request->user(),
            'details' => $details,
            'pending' => $pending,
            'deliveredTotal' => (int) $detailsTotal,
            'detailsTotal' => (int) $detailsTotal,
            'pendingTotal' => (int) $pendingTotal,
            'grandTotal' => (int) $grandTotal,
        ])->setPaper('a4', 'portrait');

        $filename = 'invoice-' . (string) ($invoice->invoice_no ?? $invoice->id) . '.pdf';

        return $pdf->download($filename);
    }

    public function poPending()
    {
        $rows = PoPendingItem::query()
            ->with(['customer', 'item', 'invoice'])
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get();

        return view('invoices.po_pending', [
            'rows' => $rows,
        ]);
    }

    public function fulfillPoPending(PoPendingItem $pending)
    {
        $result = DB::transaction(function () use ($pending) {
            $pending->refresh();
            $pendingQty = (int) ($pending->qty ?? 0);
            if ($pendingQty <= 0) {
                $pending->delete();
                return [
                    'type' => 'success',
                    'message' => 'Data pending sudah kosong dan dihapus.',
                ];
            }

            $item = Item::query()->lockForUpdate()->findOrFail($pending->item_id);
            $available = (int) ($item->stock ?? 0);
            if ($available <= 0) {
                return [
                    'type' => 'error',
                    'message' => 'Stok belum tersedia untuk ' . $item->name . '.',
                ];
            }

            $deliverQty = min($available, $pendingQty);
            if ($deliverQty <= 0) {
                return [
                    'type' => 'error',
                    'message' => 'Stok belum tersedia untuk ' . $item->name . '.',
                ];
            }

            $invoice = null;
            if (!empty($pending->invoice_id)) {
                $invoice = Invoice::query()->lockForUpdate()->find($pending->invoice_id);
            }

            $item->decrement('stock', $deliverQty);

            ItemOut::query()->create([
                'type' => 'sale',
                'customer_id' => $pending->customer_id,
                'item_id' => $pending->item_id,
                'qty' => $deliverQty,
                'date' => now()->toDateString(),
                'buy_price' => (int) (SupplierItem::query()
                    ->where('item_id', $pending->item_id)
                    ->orderBy('buy_price')
                    ->value('buy_price') ?? 0),
                'sell_price' => (int) ($item->price ?? 0),
            ]);

            if ($invoice) {
                $price = (int) ($pending->price ?? 0);
                $addedTotal = $deliverQty * $price;

                $detail = InvoiceDetail::query()
                    ->lockForUpdate()
                    ->where('invoice_id', $invoice->id)
                    ->where('item_id', $pending->item_id)
                    ->first();

                if ($detail) {
                    $detail->qty = (int) $detail->qty + $deliverQty;
                    $detail->total = (int) $detail->total + $addedTotal;
                    $detail->price = $price;
                    $detail->save();
                } else {
                    InvoiceDetail::query()->create([
                        'invoice_id' => $invoice->id,
                        'item_id' => $pending->item_id,
                        'qty' => $deliverQty,
                        'price' => $price,
                        'total' => $addedTotal,
                    ]);
                }

                $invoice->qty_total = (int) ($invoice->qty_total ?? 0) + $deliverQty;
                $invoice->grand_total = (int) ($invoice->grand_total ?? 0) + $addedTotal;
                $invoice->save();
            }

            $remaining = $pendingQty - $deliverQty;
            if ($remaining <= 0) {
                $pending->delete();
                return [
                    'type' => 'success',
                    'message' => 'Sisa PO berhasil dikirim untuk ' . $item->name . '. Data pending dihapus.',
                ];
            }

            $pending->qty = $remaining;
            $pending->status = 'pending';
            $pending->save();

            return [
                'type' => 'warning',
                'message' => 'Pengiriman parsial untuk ' . $item->name . '. Sisa pending: ' . $remaining . '.',
            ];
        });

        $type = $result['type'] ?? 'success';
        $message = $result['message'] ?? null;
        if ($message) {
            return redirect()->route('invoices.po_pending')->with($type, $message);
        }

        return redirect()->route('invoices.po_pending');
    }

    public function updatePoPending(Request $request, PoPendingItem $pending)
    {
        $validated = $request->validate([
            'qty' => ['required', 'integer', 'min:1'],
        ]);

        DB::transaction(function () use ($pending, $validated) {
            $pending->refresh();
            $pending->qty = (int) $validated['qty'];
            $pending->status = 'pending';
            $pending->save();
        });

        return redirect()->route('invoices.po_pending')->with('success', 'Qty pending berhasil diperbarui.');
    }

    public function destroyPoPending(PoPendingItem $pending)
    {
        DB::transaction(function () use ($pending) {
            $pending->refresh();
            $pending->delete();
        });

        return redirect()->route('invoices.po_pending')->with('success', 'Data pending berhasil dihapus.');
    }

    public function destroy(Invoice $invoice)
    {
        DB::transaction(function () use ($invoice) {
            $invoice->refresh();

            $pendingCount = PoPendingItem::query()
                ->where('invoice_id', $invoice->id)
                ->count();
            if ($pendingCount > 0) {
                abort(422, 'Invoice tidak bisa dihapus karena masih ada data di PO Belum Terkirim.');
            }

            $approvalStatus = InvoiceApproval::query()
                ->where('invoice_id', $invoice->id)
                ->value('status');
            if (($approvalStatus ?? 'pending') === 'accept') {
                abort(422, 'Invoice yang sudah Accept tidak bisa dihapus.');
            }

            if (($invoice->status ?? '') !== 'draft') {
                abort(422, 'Invoice yang sudah diproses tidak bisa dihapus.');
            }

            InvoiceDetail::query()->where('invoice_id', $invoice->id)->delete();
            $invoice->delete();
        });

        return redirect()->route('invoices.index');
    }
}
