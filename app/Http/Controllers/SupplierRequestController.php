<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ItemIn;
use App\Models\Supplier;
use App\Models\SupplierItem;
use App\Models\SupplierRequest;
use App\Models\SupplierRequestItem;
use App\Mail\SupplierRequestSent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class SupplierRequestController extends Controller
{
    public function index(Request $request)
    {
        $suppliers = Supplier::query()
            ->with(['supplierItems' => fn ($q) => $q->with('item')->orderBy('id')])
            ->orderBy('name')
            ->get();

        $supplierProducts = $suppliers
            ->mapWithKeys(function ($supplier) {
                return [
                    (string) $supplier->id => ($supplier->supplierItems ?? collect())
                        ->map(fn ($it) => [
                            'id' => (int) $it->item_id,
                            'name' => $it->item?->name,
                            'unit' => $it->item?->unit,
                            'price' => (int) ($it->buy_price ?? 0),
                        ])
                        ->values()
                        ->all(),
                ];
            })
            ->all();
        $selectedSupplierId = $request->query('supplier_id');

        $requestsQuery = SupplierRequest::query()
            ->with(['supplier', 'items' => fn ($q) => $q->with('item')])
            ->orderByDesc('request_date')
            ->orderByDesc('id');

        if ($selectedSupplierId) {
            $requestsQuery->where('supplier_id', $selectedSupplierId);
        }

        $requests = $requestsQuery->get();

        $requests->each(function ($req) {
            $req->items_json = json_encode(
                ($req->items ?? collect())
                    ->map(fn ($it) => [
                        'item_id' => (int) $it->item_id,
                        'unit' => $it->unit,
                        'qty' => (int) $it->qty,
                        'price' => (int) ($it->price ?? 0),
                    ])
                    ->values()
                    ->all(),
                JSON_UNESCAPED_UNICODE
            );

            $units = ($req->items ?? collect())
                ->pluck('unit')
                ->filter(fn ($u) => (string) $u !== '')
                ->unique()
                ->values();
            $req->units_summary = $units->count() ? $units->implode(', ') : '-';
        });

        return view('masters.items_supplier', [
            'suppliers' => $suppliers,
            'supplierProducts' => $supplierProducts,
            'requests' => $requests,
            'selectedSupplierId' => $selectedSupplierId,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => ['required', 'integer', 'exists:suppliers,id'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.item_id' => ['required', 'integer', 'exists:items,id'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
            'items.*.price' => ['nullable', 'integer', 'min:0'],
        ]);

        DB::transaction(function () use ($validated) {
            $items = $validated['items'];

            $totalQty = 0;
            $totalAmount = 0;

            foreach ($items as $it) {
                $qty = (int) ($it['qty'] ?? 0);
                $price = (int) ($it['price'] ?? 0);
                $subtotal = $qty * $price;
                $totalQty += $qty;
                $totalAmount += $subtotal;
            }

            foreach ($items as $it) {
                $exists = SupplierItem::query()
                    ->where('supplier_id', $validated['supplier_id'])
                    ->where('item_id', $it['item_id'])
                    ->exists();
                if (!$exists) {
                    abort(422, 'Barang yang dipilih tidak terdaftar pada supplier.');
                }
            }

            $req = SupplierRequest::query()->create([
                'request_no' => $this->generateRequestNo(),
                'supplier_id' => $validated['supplier_id'],
                'request_date' => now()->toDateString(),
                'status' => 'pending',
                'notes' => $validated['notes'] ?? null,
                'total_qty' => $totalQty,
                'total_amount' => $totalAmount,
                'created_by' => auth()->id(),
            ]);

            foreach ($items as $it) {
                $item = Item::query()->findOrFail($it['item_id']);
                $qty = (int) ($it['qty'] ?? 0);
                $price = (int) ($it['price'] ?? 0);

                SupplierRequestItem::query()->create([
                    'supplier_request_id' => $req->id,
                    'item_id' => (int) $it['item_id'],
                    'unit' => (string) ($item->unit ?? 'pcs'),
                    'qty' => $qty,
                    'price' => $price,
                    'subtotal' => $qty * $price,
                ]);
            }
        });

        return redirect()->route('masters.items_supplier', ['supplier_id' => $validated['supplier_id']]);
    }

    public function update(Request $request, SupplierRequest $supplierRequest)
    {
        $validated = $request->validate([
            'supplier_id' => ['required', 'integer', 'exists:suppliers,id'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.item_id' => ['required', 'integer', 'exists:items,id'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
            'items.*.price' => ['nullable', 'integer', 'min:0'],
        ]);

        DB::transaction(function () use ($validated, $supplierRequest) {
            $items = $validated['items'];

            $totalQty = 0;
            $totalAmount = 0;

            foreach ($items as $it) {
                $qty = (int) ($it['qty'] ?? 0);
                $price = (int) ($it['price'] ?? 0);
                $subtotal = $qty * $price;
                $totalQty += $qty;
                $totalAmount += $subtotal;
            }

            foreach ($items as $it) {
                $exists = SupplierItem::query()
                    ->where('supplier_id', $validated['supplier_id'])
                    ->where('item_id', $it['item_id'])
                    ->exists();
                if (!$exists) {
                    abort(422, 'Barang yang dipilih tidak terdaftar pada supplier.');
                }
            }

            $supplierRequest->supplier_id = $validated['supplier_id'];
            $supplierRequest->notes = $validated['notes'] ?? null;
            $supplierRequest->total_qty = $totalQty;
            $supplierRequest->total_amount = $totalAmount;
            $supplierRequest->save();

            $supplierRequest->items()->delete();

            foreach ($items as $it) {
                $item = Item::query()->findOrFail($it['item_id']);
                $qty = (int) ($it['qty'] ?? 0);
                $price = (int) ($it['price'] ?? 0);

                SupplierRequestItem::query()->create([
                    'supplier_request_id' => $supplierRequest->id,
                    'item_id' => (int) $it['item_id'],
                    'unit' => (string) ($item->unit ?? 'pcs'),
                    'qty' => $qty,
                    'price' => $price,
                    'subtotal' => $qty * $price,
                ]);
            }
        });

        return redirect()->route('masters.items_supplier', ['supplier_id' => $validated['supplier_id']]);
    }

    public function send(SupplierRequest $supplierRequest)
    {
        if ($supplierRequest->status === 'accepted') {
            return redirect()->route('masters.items_supplier', ['supplier_id' => $supplierRequest->supplier_id]);
        }

        $supplierRequest->loadMissing(['supplier', 'items' => fn ($q) => $q->with('item')]);

        $forcedTo = config('mail.force_to');
        $email = $forcedTo ?: $supplierRequest->supplier?->email;
        if ($email) {
            Mail::to($email)->send(new SupplierRequestSent($supplierRequest));
        }

        return redirect()->route('masters.items_supplier', ['supplier_id' => $supplierRequest->supplier_id]);
    }

    public function accept(SupplierRequest $supplierRequest)
    {
        if ($supplierRequest->status === 'accepted') {
            return redirect()->route('masters.items_supplier', ['supplier_id' => $supplierRequest->supplier_id]);
        }

        DB::transaction(function () use ($supplierRequest) {
            $supplierRequest->loadMissing(['items']);

            $supplierRequest->status = 'accepted';
            $supplierRequest->save();

            foreach ($supplierRequest->items as $it) {
                $qty = (int) ($it->qty ?? 0);
                if ($qty <= 0) continue;

                ItemIn::query()->create([
                    'supplier_id' => $supplierRequest->supplier_id,
                    'item_id' => (int) $it->item_id,
                    'qty' => $qty,
                    'date' => now()->toDateString(),
                ]);

                Item::query()->whereKey((int) $it->item_id)->increment('stock', $qty);
            }
        });

        return redirect()->route('masters.items_supplier', ['supplier_id' => $supplierRequest->supplier_id]);
    }

    public function destroy(SupplierRequest $supplierRequest)
    {
        $supplierId = $supplierRequest->supplier_id;
        $supplierRequest->delete();

        return redirect()->route('masters.items_supplier', ['supplier_id' => $supplierId]);
    }

    private function generateRequestNo(): string
    {
        $datePart = now()->format('Ymd');
        do {
            $candidate = 'REQ-' . $datePart . '-' . random_int(1000, 9999);
        } while (SupplierRequest::query()->where('request_no', $candidate)->exists());

        return $candidate;
    }
}
