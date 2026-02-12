<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\RoleService;

class RoleController extends Controller
{
    protected $roleSvc;

    public function __construct(RoleService $roleSvc)
    {
        $this->roleSvc = $roleSvc;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('role.index', [
            'title' => 'Role',
            'active' => 'role'
        ]);
    }

    /**
     * Get datatable data
     */
    public function datatable(Request $request)
    {
        $row = $request->input('rows');
        $page = $request->input('page');

        $rows = $row >= 10 ? $row : 20;
        $offset = ($page - 1) * $rows;
        $searchKey = $request->input('searchKey');

        $query = $this->roleSvc->getByArrayDT($rows, $offset, $searchKey);
        $data = [];

        foreach ($query as $key => $q) {
            $data[$key]['id'] = $q->id;
            $data[$key]['code'] = $q->code;
            $data[$key]['name'] = $q->name;
            $data[$key]['description'] = $q->description;
            $data[$key]['is_active'] = $q->is_active ? 'Active' : 'Inactive';
        }

        $count = $this->roleSvc->getByArrayDT(0, 0, $searchKey);

        return response()->json([
            "total" => $count,
            "rows" => $data
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'code' => 'required|max:50',
                'name' => 'required|max:100',
            ]);

            DB::beginTransaction();
            $this->roleSvc->store($request);
            DB::commit();

            return response()->json(['status' => true, 'message' => 'Data berhasil disimpan']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => $th->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $role = $this->roleSvc->getById($id);
        return response()->json($role);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $request->merge(['id' => $id]);

            DB::beginTransaction();
            $this->roleSvc->store($request);
            DB::commit();

            return response()->json(['status' => true, 'message' => 'Data berhasil diupdate']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => $th->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $this->roleSvc->destroy($id);
            DB::commit();

            return response()->json(['status' => true, 'message' => 'Data berhasil dihapus']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => $th->getMessage()]);
        }
    }

    /**
     * Get all roles for dropdown
     */
    public function getAll()
    {
        $roles = $this->roleSvc->getAll();
        return response()->json($roles);
    }
}
