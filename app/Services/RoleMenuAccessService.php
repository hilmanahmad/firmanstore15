<?php

namespace App\Services;

use App\Models\Menu;
use App\Models\RoleMenuAccess;

class RoleMenuAccessService
{
    protected $roleMenuAccess;

    public function __construct(RoleMenuAccess $roleMenuAccess)
    {
        $this->roleMenuAccess = $roleMenuAccess;
    }

    public function getByRoleCode($roleCode)
    {
        return $this->roleMenuAccess->where('role_code', $roleCode)->get();
    }

    public function getMenusWithAccess($roleCode)
    {
        $menus = Menu::orderBy('sort', 'ASC')->get();
        $accessList = $this->roleMenuAccess->where('role_code', $roleCode)->get()->keyBy('menu_id');

        return $menus->map(function ($menu) use ($accessList) {
            $access = $accessList->get($menu->id);
            return [
                'menu_id' => $menu->id,
                'menu_name' => $menu->name,
                'url' => $menu->url,
                'is_header' => $menu->is_header,
                'parent' => $menu->parent,
                'can_view' => $access ? $access->can_view : false,
                'can_create' => $access ? $access->can_create : false,
                'can_edit' => $access ? $access->can_edit : false,
                'can_delete' => $access ? $access->can_delete : false,
            ];
        });
    }

    public function getAccessibleMenuIds($roleCode)
    {
        return $this->roleMenuAccess
            ->where('role_code', $roleCode)
            ->where('can_view', 1)
            ->pluck('menu_id');
    }

    public function syncMenuAccess($roleCode, $menuAccess)
    {
        // Delete existing access for this role
        $this->roleMenuAccess->where('role_code', $roleCode)->delete();
        // dd($menuAccess);s
        // Insert new access
        foreach ($menuAccess as $access) {
            if (isset($access['menu_id'])) {
                $this->roleMenuAccess->create([
                    'role_code' => $roleCode,
                    'menu_id' => $access['menu_id'],
                    'can_view' => !empty($access['can_view']) && $access['can_view'] == 'true' ? 1 : 0,
                    'can_create' => !empty($access['can_create']) && $access['can_create'] == 'true' ? 1 : 0,
                    'can_edit' => !empty($access['can_edit']) && $access['can_edit'] == 'true' ? 1 : 0,
                    'can_delete' => !empty($access['can_delete']) && $access['can_delete'] == 'true' ? 1 : 0,
                ]);
            }
        }

        return true;
    }

    public function updateSingleAccess($roleCode, $menuId, $permissions)
    {
        return $this->roleMenuAccess->updateOrCreate(
            [
                'role_code' => $roleCode,
                'menu_id' => $menuId,
            ],
            [
                'can_view' => !empty($permissions['can_view']) && $permissions['can_view'] == 'true' ? 1 : 0,
                'can_create' => !empty($permissions['can_create']) && $permissions['can_create'] == 'true' ? 1 : 0,
                'can_edit' => !empty($permissions['can_edit']) && $permissions['can_edit'] == 'true' ? 1 : 0,
                'can_delete' => !empty($permissions['can_delete']) && $permissions['can_delete'] == 'true' ? 1 : 0,
            ]
        );
    }

    public function hasAccess($roleCode, $menuId, $permission = 'can_view')
    {
        $access = $this->roleMenuAccess
            ->where('role_code', $roleCode)
            ->where('menu_id', $menuId)
            ->first();

        if (!$access) {
            return false;
        }

        return $access->{$permission} ?? false;
    }

    public function deleteByRoleCode($roleCode)
    {
        return $this->roleMenuAccess->where('role_code', $roleCode)->delete();
    }
}
