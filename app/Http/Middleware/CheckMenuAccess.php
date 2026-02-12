<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Menu;
use App\Models\RoleMenuAccess;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckMenuAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $permission = 'can_view'): Response
    {
        $user = auth()->user();

        if (!$user) {
            return redirect('/')->with('error', 'Silahkan login terlebih dahulu');
        }

        // Super Admin bypass - always has access
        if ($user->role_code === 'SUPERADMIN') {
            return $next($request);
        }

        // Get current URL path
        $currentPath = $request->path();

        // Find menu by URL
        $menu = $this->findMenuByUrl($currentPath);

        if (!$menu) {
            // If menu not found in database, allow access (for routes not managed by menu)
            return $next($request);
        }

        // Check if user has access to this menu
        $hasAccess = RoleMenuAccess::where('role_code', $user->role_code)
            ->where('menu_id', $menu->id)
            ->where($permission, 1)
            ->exists();

        // Also check parent menu access if this is a submenu
        if (!$hasAccess && $menu->parent && $menu->parent !== 'false') {
            $hasAccess = RoleMenuAccess::where('role_code', $user->role_code)
                ->where('menu_id', $menu->parent)
                ->where($permission, 1)
                ->exists();
        }

        if (!$hasAccess) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Anda tidak memiliki akses ke halaman ini'
                ], 403);
            }

            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses ke halaman ini');
        }

        return $next($request);
    }

    /**
     * Find menu by URL path
     */
    private function findMenuByUrl($path)
    {
        // Remove leading slash
        $path = ltrim($path, '/');

        // Try exact match first
        $menu = Menu::where('url', $path)->first();

        if ($menu) {
            return $menu;
        }

        // Try matching base path (for resource routes like 'user/1/edit')
        $pathParts = explode('/', $path);
        $basePath = $pathParts[0] ?? '';

        if ($basePath) {
            $menu = Menu::where('url', $basePath)->first();
            if ($menu) {
                return $menu;
            }
        }

        // Try with 'like' for partial matches
        $menu = Menu::where('url', 'LIKE', $basePath . '%')->first();

        return $menu;
    }
}
