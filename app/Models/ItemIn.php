<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemIn extends Model
{
    use HasFactory;

    protected $table = 'item_ins';

    protected $fillable = [
        'supplier_id',
        'item_id',
        'qty',
        'date',
    ];

    protected $casts = [
        'qty' => 'integer',
        'date' => 'date',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
