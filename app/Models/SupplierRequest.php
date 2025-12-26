<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupplierRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_no',
        'supplier_id',
        'request_date',
        'status',
        'notes',
        'total_qty',
        'total_amount',
        'created_by',
    ];

    protected $casts = [
        'request_date' => 'date',
        'total_qty' => 'integer',
        'total_amount' => 'integer',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(SupplierRequestItem::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
