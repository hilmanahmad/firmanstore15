<?php

namespace App\Models;

use App\Models\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ItemPurchase extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'item_purchases';
    protected $keyType = 'string';
    protected $with = ['type'];
    protected $fillable = [
        'id',
        'user_id',
        'item_id',
        'type_id',
        'purchase_price',
        'qty',
        'tracking_number',
        'status', // pending, received
        'received_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
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
}
