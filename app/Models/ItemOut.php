<?php

namespace App\Models;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemOut extends Model
{
    use HasFactory;

    protected $table = 'item_outs';

    protected $fillable = [
        'customer_id',
        'item_id',
        'type',
        'buy_price',
        'sell_price',
        'qty',
        'date',
    ];

    protected $casts = [
        'qty' => 'integer',
        'buy_price' => 'integer',
        'sell_price' => 'integer',
        'date' => 'date',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
