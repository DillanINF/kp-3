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
    public function index(Request $request)
    {
        $customers = Customer::query()->orderBy('name')->get();
        $pengirims = \App\Models\Pengirim::query()->orderBy('name')->get();
        $invoices = Invoice::query()
            ->with(['customer', 'pengirim', 'approval'])
            ->withCount('poPendingItems')
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->paginate(5)
            ->withQueryString();

        return view('invoices.index', [
            'customers' => $customers,
            'pengirims' => $pengirims,
            'invoices' => $invoices,
        ]);
    }

    public function accept(Invoice $invoice)
    {
        try {
            DB::transaction(function () use ($invoice) {
                $invoice->refresh();

                $pendingCount = PoPendingItem::query()
                    ->where('invoice_id', $invoice->id)
                    ->count();

                if ($pendingCount > 0) {
                    throw new \Exception('Masih ada PO belum terkirim. Selesaikan dulu sebelum Accept.');
                }

                $approval = InvoiceApproval::query()->firstOrNew([
                    'invoice_id' => $invoice->id,
                ]);
                $approval->status = 'accept';
                $approval->accepted_at = now();
                $approval->save();
            });

            return redirect()->route('invoices.index')->with('success', 'Invoice berhasil di-Accept.');
        } catch (\Exception $e) {
            return redirect()->route('invoices.index')->with('error', $e->getMessage());
        }
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
                'pengirim_id' => null, // Will be set in input_po step
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
        $pengirims = \App\Models\Pengirim::query()->orderBy('name')->get();

        return view('invoices.input_po', [
            'invoice' => $invoice,
            'items' => $items,
            'pengirims' => $pengirims,
        ]);
    }

    public function inputPoByNo(string $invoiceNo)
    {
        $invoice = Invoice::query()->where('invoice_no', $invoiceNo)->firstOrFail();
        $invoice->load(['customer', 'details.item']);
        $items = Item::query()->where('item_type', 'regular')->orderBy('name')->get();
        $pengirims = \App\Models\Pengirim::query()->orderBy('name')->get();

        return view('invoices.input_po', [
            'invoice' => $invoice,
            'items' => $items,
            'pengirims' => $pengirims,
        ]);
    }

    public function storePo(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'pengirim_id' => ['required', 'integer', 'exists:pengirims,id'],
            'delivery_date' => ['required', 'date'],
            'address' => ['required', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.item_id' => ['required', 'integer', 'distinct', 'exists:items,id'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
            'items.*.price' => ['required', 'integer', 'min:0'],
        ]);

        try {
            $result = DB::transaction(function () use ($invoice, $validated) {
                $invoice->refresh();
                $invoice->load('customer');

                $invoice->pengirim_id = $validated['pengirim_id'];
                $invoice->delivery_date = $validated['delivery_date'];
                $invoice->address = $validated['address'];

                $poNo = (string) ($invoice->po_no ?: ('PO-' . (string) ($invoice->invoice_no ?? '')));

                $hasPending = false;
                $totalPendingQty = 0;

                if ($invoice->status !== 'draft') {
                    throw new \Exception('Invoice sudah diproses dan tidak bisa diubah.');
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
                        throw new \Exception('Barang yang dipilih tidak valid untuk invoice.');
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
                $msg = '⚠️ PERHATIAN: Stok tidak mencukupi! ';
                if ($pendingQty > 0) {
                    $msg .= 'Sebanyak ' . $pendingQty . ' unit otomatis masuk ke daftar "PO Belum Terkirim".';
                } else {
                    $msg .= 'Sisa pesanan otomatis masuk ke menu PO Belum Terkirim.';
                }

                return redirect()->route('invoices.index')->with('warning', $msg);
            }

            return redirect()->route('invoices.index')->with('success', 'PO berhasil disimpan.');
        } catch (\Exception $e) {
            return redirect()->route('invoices.index')->with('error', $e->getMessage());
        }
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
            'pengirim_name' => $invoice->pengirim?->name ?? 'Admin',
            'details' => $details,
            'pending' => $pending,
            'deliveredTotal' => (int) $detailsTotal,
            'detailsTotal' => (int) $detailsTotal,
            'pendingTotal' => (int) $pendingTotal,
            'grandTotal' => (int) $grandTotal,
        ])->setPaper('a4', 'portrait');

        $filename = 'invoice-' . (string) ($invoice->invoice_no ?? $invoice->id) . '.pdf';

        if ($request->has('preview')) {
            return $pdf->stream($filename);
        }

        return $pdf->download($filename);
    }

    public function poPending(Request $request)
    {
        $rows = PoPendingItem::query()
            ->with(['customer', 'item', 'invoice'])
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(5)
            ->withQueryString();

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
        
        // Get invoice for preview if successful
        $invoice = null;
        if (!empty($pending->invoice_id)) {
            $invoice = Invoice::query()->find($pending->invoice_id);
        }
        
        if ($message) {
            $redirect = redirect()->route('invoices.po_pending')->with($type, $message);
            
            // Add PDF preview URL if successful and has invoice
            if ($type === 'success' && $invoice) {
                $previewUrl = route('invoices.pdf', $invoice) . '?preview=1';
                $redirect->with('show_pdf_preview', $previewUrl);
            }
            
            return $redirect;
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
        try {
            DB::transaction(function () use ($invoice) {
                $invoice->refresh();

                // 1. Kembalikan stok dan hapus ItemOut (Data Laporan)
                $details = InvoiceDetail::query()->where('invoice_id', $invoice->id)->get();
                foreach ($details as $detail) {
                    $item = Item::find($detail->item_id);
                    if ($item) {
                        $item->increment('stock', $detail->qty);
                    }
                    
                    // Hapus data keluar terkait invoice ini (untuk sinkronisasi laporan/dashboard)
                    ItemOut::query()
                        ->where('customer_id', $invoice->customer_id)
                        ->where('item_id', $detail->item_id)
                        ->where('qty', $detail->qty)
                        ->where('date', $invoice->date)
                        ->where('type', 'sale')
                        ->delete();
                }

                // 2. Hapus data di PO Belum Terkirim jika ada
                PoPendingItem::query()->where('invoice_id', $invoice->id)->delete();

                // 3. Hapus Approval status
                InvoiceApproval::query()->where('invoice_id', $invoice->id)->delete();

                // 4. Hapus Detail dan Invoice
                InvoiceDetail::query()->where('invoice_id', $invoice->id)->delete();
                $invoice->delete();
            });

            return redirect()->route('invoices.index')->with('success', 'Invoice berhasil dihapus dan stok telah dikembalikan.');
        } catch (\Exception $e) {
            return redirect()->route('invoices.index')->with('error', $e->getMessage());
        }
    }
}
