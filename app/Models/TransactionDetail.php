<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'item_history_id',
        'qty',
        'selling_price',
        'subtotal'
    ];

    // Relationship ke Transaction
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    // Relationship ke ItemHistory
    public function itemHistory()
    {
        return $this->belongsTo(ItemHistory::class);
    }

    // Relationship ke Item melalui ItemHistory
    public function item()
    {
        return $this->hasOneThrough(
            Item::class,
            ItemHistory::class,
            'id', // Foreign key on ItemHistory table
            'id', // Foreign key on Item table
            'item_history_id', // Local key on TransactionDetail table
            'item_id' // Local key on ItemHistory table
        );
    }
}
