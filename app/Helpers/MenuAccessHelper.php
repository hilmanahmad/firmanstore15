<?php

namespace App\Helpers;

use App\Models\RoleMenuAccess;

class MenuAccessHelper
{
    /**
     * Check if current user can view a menu
     */
    public static function canView($menuId)
    {
        return self::hasAccess($menuId, 'can_view');
    }

    /**
     * Check if current user can create in a menu
     */
    public static function canCreate($menuId)
    {
        return self::hasAccess($menuId, 'can_create');
    }

    /**
     * Check if current user can edit in a menu
     */
    public static function canEdit($menuId)
    {
        return self::hasAccess($menuId, 'can_edit');
    }

    /**
     * Check if current user can delete in a menu
     */
    public static function canDelete($menuId)
    {
        return self::hasAccess($menuId, 'can_delete');
    }

    /**
     * Check if current user has specific permission
     */
    public static function hasAccess($menuId, $permission = 'can_view')
    {
        $user = auth()->user();

        if (!$user) {
            return false;
        }

        // Super Admin always has access
        if ($user->role_code === 'SUPERADMIN') {
            return true;
        }

        $access = RoleMenuAccess::where('role_code', $user->role_code)
            ->where('menu_id', $menuId)
            ->first();

        if (!$access) {
            return false;
        }

        return $access->{$permission} ?? false;
    }

    /**
     * Check if user has any of the specified roles
     */
    public static function hasRole($roles)
    {
        $user = auth()->user();

        if (!$user) {
            return false;
        }

        if (is_string($roles)) {
            $roles = [$roles];
        }

        return in_array($user->role_code, $roles);
    }

    /**
     * Check if current user is admin
     */
    public static function isAdmin()
    {
        return self::hasRole(['ADMIN', 'SUPERADMIN']);
    }

    /**
     * Check if current user is super admin
     */
    public static function isSuperAdmin()
    {
        return self::hasRole(['SUPERADMIN']);
    }
}
