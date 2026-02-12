<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RoleMenuAccess extends Model
{
    use HasFactory;

    protected $table = 'role_menu_access';

    protected $fillable = [
        'role_code',
        'menu_id',
        'can_view',
        'can_create',
        'can_edit',
        'can_delete',
    ];

    protected $casts = [
        'can_view' => 'boolean',
        'can_create' => 'boolean',
        'can_edit' => 'boolean',
        'can_delete' => 'boolean',
    ];

    /**
     * Get the role that owns the access.
     */
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_code', 'code');
    }

    /**
     * Get the menu that this access belongs to.
     */
    public function menu()
    {
        return $this->belongsTo(Menu::class, 'menu_id', 'id');
    }
}
