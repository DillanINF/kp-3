<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
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
            ->with(['supplierItems' => fn ($q) => $q->select(['id', 'supplier_id', 'name', 'unit', 'price'])->orderBy('name')])
            ->orderBy('name')
            ->get();

        $supplierProducts = $suppliers
            ->mapWithKeys(function ($supplier) {
                return [
                    (string) $supplier->id => ($supplier->supplierItems ?? collect())
                        ->map(fn ($it) => [
                            'name' => $it->name,
                            'unit' => $it->unit,
                            'price' => (int) ($it->price ?? 0),
                        ])
                        ->values()
                        ->all(),
                ];
            })
            ->all();
        $selectedSupplierId = $request->query('supplier_id');

        $requestsQuery = SupplierRequest::query()
            ->with(['supplier', 'items'])
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
                        'product_name' => $it->product_name,
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
            'items.*.product_name' => ['required', 'string', 'max:255'],
            'items.*.unit' => ['required', 'string', 'max:30'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
            'items.*.price' => ['nullable', 'integer', 'min:0'],
        ]);

        DB::transaction(function () use ($validated, $request) {
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
                $qty = (int) ($it['qty'] ?? 0);
                $price = (int) ($it['price'] ?? 0);

                SupplierRequestItem::query()->create([
                    'supplier_request_id' => $req->id,
                    'product_name' => (string) $it['product_name'],
                    'unit' => (string) $it['unit'],
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
            'items.*.product_name' => ['required', 'string', 'max:255'],
            'items.*.unit' => ['required', 'string', 'max:30'],
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

            $supplierRequest->supplier_id = $validated['supplier_id'];
            $supplierRequest->notes = $validated['notes'] ?? null;
            $supplierRequest->total_qty = $totalQty;
            $supplierRequest->total_amount = $totalAmount;
            $supplierRequest->save();

            $supplierRequest->items()->delete();

            foreach ($items as $it) {
                $qty = (int) ($it['qty'] ?? 0);
                $price = (int) ($it['price'] ?? 0);

                SupplierRequestItem::query()->create([
                    'supplier_request_id' => $supplierRequest->id,
                    'product_name' => (string) $it['product_name'],
                    'unit' => (string) $it['unit'],
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
        $supplierRequest->loadMissing(['supplier', 'items']);

        $email = $supplierRequest->supplier?->email;
        if ($email) {
            Mail::to($email)->send(new SupplierRequestSent($supplierRequest));
        }

        return redirect()->route('masters.items_supplier', ['supplier_id' => $supplierRequest->supplier_id]);
    }

    public function accept(SupplierRequest $supplierRequest)
    {
        if ($supplierRequest->status !== 'accepted') {
            $supplierRequest->status = 'accepted';
            $supplierRequest->save();
        }

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
