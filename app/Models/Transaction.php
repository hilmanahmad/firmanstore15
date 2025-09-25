<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'transactions';
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'customer_id',
        'item_id',
        'type_id',
        'qty',
        'selling_price',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id', 'id');
    }

    public function type()
    {
        return $this->belongsTo(Type::class, 'type_id', 'id')->withDefault(
            ['name' => '']
        );
    }

    public function detail()
    {
        return $this->hasMany(TransactionDetail::class, 'transaction_id', 'id');
    }
}
