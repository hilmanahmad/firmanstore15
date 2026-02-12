<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'roles';

    protected $fillable = [
        'code',
        'name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the users for the role.
     */
    public function users()
    {
        return $this->hasMany(User::class, 'role_code', 'code');
    }

    /**
     * Get the menu access for the role.
     */
    public function menuAccess()
    {
        return $this->hasMany(RoleMenuAccess::class, 'role_code', 'code');
    }

    /**
     * Get accessible menus for this role
     */
    public function accessibleMenus()
    {
        return $this->belongsToMany(Menu::class, 'role_menu_access', 'role_code', 'menu_id', 'code', 'id')
            ->withPivot(['can_view', 'can_create', 'can_edit', 'can_delete']);
    }

    /**
     * Check if role has access to a specific menu
     */
    public function hasMenuAccess($menuId, $permission = 'can_view')
    {
        $access = $this->menuAccess()->where('menu_id', $menuId)->first();

        if (!$access) {
            return false;
        }

        return $access->{$permission} ?? false;
    }
}
