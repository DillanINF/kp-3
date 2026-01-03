<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceDetail;
use App\Models\Item;
use App\Models\ItemOut;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    public function index()
    {
        $customers = Customer::query()->orderBy('name')->get();
        $invoices = Invoice::query()->with('customer')->orderByDesc('date')->orderByDesc('id')->get();

        return view('invoices.index', [
            'customers' => $customers,
            'invoices' => $invoices,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => ['required', 'integer', 'exists:customers,id'],
        ]);

        $invoice = DB::transaction(function () use ($validated) {
            $nextId = (int) (Invoice::query()->max('id') ?? 0) + 1;
            $invoiceNo = 'INV-' . str_pad((string) $nextId, 4, '0', STR_PAD_LEFT);

            return Invoice::query()->create([
                'invoice_no' => $invoiceNo,
                'customer_id' => $validated['customer_id'],
                'date' => now()->toDateString(),
                'status' => 'draft',
                'grand_total' => 0,
                'qty_total' => 0,
            ]);
        });

        return redirect()->route('invoices.input_po', $invoice);
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

    public function storePo(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'po_no' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.item_id' => ['required', 'integer', 'exists:items,id'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
            'items.*.price' => ['required', 'integer', 'min:0'],
        ]);

        DB::transaction(function () use ($invoice, $validated) {
            $invoice->refresh();
            $invoice->load('customer');

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
                $total = $qty * $price;

                $item = Item::query()->lockForUpdate()->findOrFail($itemId);
                if ($item->item_type !== 'regular') {
                    abort(422, 'Barang yang dipilih tidak valid untuk invoice.');
                }
                if ($item->stock < $qty) {
                    abort(422, 'Stok tidak mencukupi untuk ' . $item->name . '.');
                }

                InvoiceDetail::query()->create([
                    'invoice_id' => $invoice->id,
                    'item_id' => $itemId,
                    'qty' => $qty,
                    'price' => $price,
                    'total' => $total,
                ]);

                $item->decrement('stock', $qty);

                ItemOut::query()->create([
                    'customer_id' => $invoice->customer_id,
                    'item_id' => $itemId,
                    'qty' => $qty,
                    'date' => $invoice->date,
                ]);

                $grandTotal += $total;
                $qtyTotal += $qty;
            }

            $invoice->po_no = $validated['po_no'] ?? null;
            $invoice->address = $validated['address'] ?? null;
            $invoice->grand_total = $grandTotal;
            $invoice->qty_total = $qtyTotal;
            $invoice->status = 'posted';
            $invoice->save();
        });

        return redirect()->route('invoices.index');
    }
}
