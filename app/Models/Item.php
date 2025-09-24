<?php

namespace App\Models;

use App\Models\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Item extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'items';
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'name',
        'category_id'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function itemHistory()
    {
        return $this->hasMany(ItemHistory::class, 'item_id', 'id');
    }
}
