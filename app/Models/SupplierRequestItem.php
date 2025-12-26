<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplierRequestItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_request_id',
        'product_name',
        'unit',
        'qty',
        'price',
        'subtotal',
    ];

    protected $casts = [
        'qty' => 'integer',
        'price' => 'integer',
        'subtotal' => 'integer',
    ];

    public function supplierRequest(): BelongsTo
    {
        return $this->belongsTo(SupplierRequest::class);
    }
}
