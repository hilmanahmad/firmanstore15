<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use App\Services\CategoryService;
use App\Services\CustomerService;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    protected $category;
    public function __construct(CategoryService $category)
    {
        $this->category = $category;
    }

    public function index()
    {
        return view('category.index', [
            'title' => 'Kategori',
            'active' => 'category'
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

        // dd($rows, $offset, $searchKey);
        $query = $this->category->getByArrayDT($rows, $offset, $searchKey);
        $data = [];
        foreach ($query as $key => $q) {
            $data[$key]['id'] = $q->id;
            $data[$key]['name'] = $q->name;
        }

        // Get the total count of the data
        $count = $this->category->getByArrayDT(0, 0, $searchKey);

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
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:customers,name'
        ]);

        if (!$request->id && $validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first('name') // Menampilkan pesan error pertama untuk field 'name'
            ]);
        }
        try {
            DB::beginTransaction();

            $this->category->store($request);

            DB::commit();

            echo json_encode(['status' => true, 'message' => 'Data berhasil disimpan']);
        } catch (\Throwable $th) {
            echo json_encode(['status' => false, 'message' => $th->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Customer $customer)
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

            $this->category->destroy($id);

            DB::commit();

            echo json_encode(['status' => true, 'message' => 'Data berhasil dihapus']);
        } catch (\Throwable $th) {
            echo json_encode(['status' => false, 'message' => $th->getMessage()]);
        }
    }
}
