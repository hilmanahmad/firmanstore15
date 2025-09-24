<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Menu extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'menus';
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'name',
        'is_header',
        'parent',
        'url',
        'icon',
        'have_sub_menu',
        'sort'
    ];
}
