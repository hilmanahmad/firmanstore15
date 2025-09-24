<?php

namespace App\Http\Controllers\Administrator;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Master\Menu;
use App\Services\Administrator\MenuService;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    protected $menuSvc;
    public function __construct(MenuService $menuSvc)
    {
        $this->menuSvc = $menuSvc;
    }

    public function index()
    {
        return view('administrator.menu.index', [
            'title' => 'Menu',
            'active' => 'menu'
        ]);
    }

    public function datatable(Request $request)
    {
        $row = $request->input('rows');
        $page = $request->input('page');

        // dd($request);
        $rows = $row >= 10 ? $row : 20;
        $offset = ($page - 1) * $rows;
        $searchKey = $request->input('searchKey');

        // Get the data for the current page
        $query = $this->menuSvc->getByArrayDT($rows, $offset, $searchKey);
        $data = [];
        foreach ($query as $key => $q) {
            $data[$key]['id'] = $q->id;
            $data[$key]['menu_name'] = $q->menu_name;
            $data[$key]['url'] = $q->url;
            $data[$key]['icon'] = $q->icon;
            $data[$key]['is_header'] = $q->is_header;
            $data[$key]['have_sub_menu'] = $q->have_sub_menu;
            $data[$key]['parent'] = $q->parent;
            $data[$key]['sort'] = $q->sort;
            $data[$key]['url'] = $q->url;
        }

        // Get the total count of the data
        $count = $this->menuSvc->getByArrayDT(0, 0, $searchKey);

        $result = [
            "total" => $count,
            "rows" => $data
        ];

        return response()->json($result);
    }



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $this->menuSvc->store($request);

            DB::commit();

            echo json_encode(['status' => true, 'message' => 'Data berhasil disimpan']);
        } catch (\Throwable $th) {
            echo json_encode(['status' => false, 'message' => $th->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Menu $role)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Menu $role)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Menu $role)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $this->menuSvc->destroy($id);

            DB::commit();

            echo json_encode(['status' => true, 'message' => 'Data berhasil dihapus']);
        } catch (\Throwable $th) {
            echo json_encode(['status' => false, 'message' => $th->getMessage()]);
        }
    }
}
