<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TransactionDetail extends Model
{
    use HasFactory;
    protected $table = 'transaction_details';
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'transaction_id',
        'item_history_id',
        'selling_price',
        'qty',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id', 'id');
    }

    public function itemHistory()
    {
        return $this->belongsTo(ItemHistory::class, 'item_history_id', 'id');
    }
}
