<?php

namespace App\Http\Controllers;

use App\Helpers\StringHelper;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Services\ItemService;
use App\Services\CategoryService;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    protected $item;
    public function __construct(ItemService $item)
    {
        $this->item = $item;
    }

    public function index()
    {
        return view('item.index', [
            'title' => 'Item',
            'active' => 'item'
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
        $query = $this->item->getByArrayDT($rows, $offset, $searchKey);
        $data = [];
        $total_profit = 0;
        foreach ($query as $key => $q) {
            $profit = 0;
            foreach ($q->itemHistory as $itemHistory) {
                foreach ($itemHistory->detail as $detail) {
                    // Hitung profit per detail
                    $profit += ($detail->selling_price - $itemHistory->purchase_price) * $detail->qty;
                    // Simpan atau tampilkan profit sesuai kebutuhan
                }
            }
            $total_profit += $profit;
            $data[$key]['id'] = $q->id;
            $data[$key]['name'] = $q->name;
            $data[$key]['category_id'] = $q->category_id;
            $data[$key]['category'] = $q->category->name;
            $data[$key]['qty_dus'] = isset($q->itemHistory) ? $q->itemHistory->where('type_id', 'd69a5c93-ec7b-4a08-bcb4-514402b29e5b')->sum(function ($item) {
                return $item->qty - $item->qty_sold;
            }) : 0;
            $data[$key]['qty_pcs'] = isset($q->itemHistory) ? $q->itemHistory->where('type_id', '384c6870-3e9c-4e99-be56-6617256c69c6')->sum(function ($item) {
                return $item->qty - $item->qty_sold;
            }) : 0;
            $data[$key]['profit'] = StringHelper::formatRupiah($profit);
        }

        // Get the total count of the data
        $count = $this->item->getByArrayDT(0, 0, $searchKey);

        $footer = [
            [
                "profit"   => StringHelper::formatRupiah($total_profit),
            ]
        ];

        $result = [
            "total" => $count,
            "rows" => $data,
            "footer" => $footer
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
            'name' => 'required|unique:items,name'
        ]);

        if (!$request->id && $validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first('name') // Menampilkan pesan error pertama untuk field 'name'
            ]);
        }
        try {
            DB::beginTransaction();

            $this->item->store($request);

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

            $this->item->destroy($id);

            DB::commit();

            echo json_encode(['status' => true, 'message' => 'Data berhasil dihapus']);
        } catch (\Throwable $th) {
            echo json_encode(['status' => false, 'message' => $th->getMessage()]);
        }
    }
}
