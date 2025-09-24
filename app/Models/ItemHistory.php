<?php

namespace App\Models;

use App\Models\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ItemHistory extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'item_histories';
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'item_id',
        'purchase_price',
        'qty',
        'qty_sold',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id', 'id');
    }

    public function detail()
    {
        return $this->hasMany(TransactionDetail::class, 'item_history_id', 'id');
    }
}
