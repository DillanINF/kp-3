<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\InvoiceApproval;
use App\Models\PoPendingItem;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_no',
        'po_no',
        'customer_id',
        'pengirim_id',
        'date',
        'status',
        'grand_total',
        'qty_total',
    ];

    protected $casts = [
        'date' => 'date',
        'grand_total' => 'integer',
        'qty_total' => 'integer',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function pengirim(): BelongsTo
    {
        return $this->belongsTo(Pengirim::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(InvoiceDetail::class);
    }

    public function approval(): HasOne
    {
        return $this->hasOne(InvoiceApproval::class);
    }

    public function poPendingItems(): HasMany
    {
        return $this->hasMany(PoPendingItem::class);
    }
}
