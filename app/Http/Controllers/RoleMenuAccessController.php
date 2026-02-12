<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Role;
use Illuminate\Http\Request;
use App\Services\RoleService;
use App\Models\RoleMenuAccess;
use Illuminate\Support\Facades\DB;
use App\Services\RoleMenuAccessService;

class RoleMenuAccessController extends Controller
{
    protected $roleMenuAccessSvc;
    protected $roleSvc;

    public function __construct(RoleMenuAccessService $roleMenuAccessSvc, RoleService $roleSvc)
    {
        $this->roleMenuAccessSvc = $roleMenuAccessSvc;
        $this->roleSvc = $roleSvc;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = $this->roleSvc->getAll();
        $menus = Menu::orderBy('sort', 'ASC')->get();

        return view('role-menu-access.index', [
            'title' => 'Menu Access',
            'active' => 'role-menu-access',
            'roles' => $roles,
            'menus' => $menus
        ]);
    }

    /**
     * Get menu access by role
     */
    public function getByRole(Request $request)
    {
        $roleCode = $request->input('role_code');

        if (!$roleCode) {
            return response()->json([
                'status' => false,
                'message' => 'Role code is required'
            ]);
        }

        $menusWithAccess = $this->roleMenuAccessSvc->getMenusWithAccess($roleCode);

        return response()->json([
            'status' => true,
            'data' => $menusWithAccess
        ]);
    }

    /**
     * Store or update menu access for a role
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'role_code' => 'required',
                'menu_access' => 'required|array',
            ]);

            DB::beginTransaction();

            $this->roleMenuAccessSvc->syncMenuAccess(
                $request->role_code,
                $request->menu_access
            );

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Menu access berhasil disimpan'
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ]);
        }
    }

    /**
     * Update single menu access
     */
    public function updateSingle(Request $request)
    {
        try {
            $request->validate([
                'role_code' => 'required',
                'menu_id' => 'required',
            ]);

            DB::beginTransaction();

            $this->roleMenuAccessSvc->updateSingleAccess(
                $request->role_code,
                $request->menu_id,
                [
                    'can_view' => $request->can_view ?? false,
                    'can_create' => $request->can_create ?? false,
                    'can_edit' => $request->can_edit ?? false,
                    'can_delete' => $request->can_delete ?? false,
                ]
            );

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Menu access berhasil diupdate'
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ]);
        }
    }

    /**
     * Copy menu access from one role to another
     */
    public function copyAccess(Request $request)
    {
        try {
            $request->validate([
                'source_role_code' => 'required',
                'target_role_code' => 'required',
            ]);

            DB::beginTransaction();

            // Get source access
            $sourceAccess = $this->roleMenuAccessSvc->getByRoleCode($request->source_role_code);

            // Convert to array format
            $menuAccess = $sourceAccess->map(function ($access) {
                return [
                    'menu_id' => $access->menu_id,
                    'can_view' => $access->can_view,
                    'can_create' => $access->can_create,
                    'can_edit' => $access->can_edit,
                    'can_delete' => $access->can_delete,
                ];
            })->toArray();

            // Sync to target role
            $this->roleMenuAccessSvc->syncMenuAccess($request->target_role_code, $menuAccess);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Menu access berhasil dicopy'
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ]);
        }
    }
}
