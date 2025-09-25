<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use App\Helpers\StringHelper;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Services\ItemHistoryService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class ItemHistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    protected $itemHistory;
    public function __construct(ItemHistoryService $itemHistory)
    {
        $this->itemHistory = $itemHistory;
    }

    public function index()
    {
        return view('itemHistory.index', [
            'title' => 'Barang Masuk',
            'active' => 'item-history'
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
        $itemId = $request->input('item_id');
        $typeId = $request->input('type_id');

        // Get the data for the current page

        // dd($rows, $offset, $searchKey);
        $query = $this->itemHistory->getByArrayDT($rows, $offset, $searchKey, $itemId, $typeId);
        $data = [];
        foreach ($query as $key => $q) {
            $profit = 0;
            foreach ($q->detail as $detail) {
                // Hitung profit per detail
                $profit += ($detail->selling_price - $q->purchase_price) * $detail->qty;
                // Simpan atau tampilkan profit sesuai kebutuhan
            }

            $data[$key]['id'] = $q->id;
            $data[$key]['item_id'] = $q->item_id;
            $data[$key]['item'] = $q->item->name;
            $data[$key]['type_id'] = $q->type_id;
            $data[$key]['type'] = $q->type->name;
            $data[$key]['purchase_price'] = StringHelper::formatRupiah($q->purchase_price);
            $data[$key]['qty'] = $q->qty;
            $data[$key]['qty_sold'] = $q->qty_sold;
            $data[$key]['profit'] = StringHelper::formatRupiah($profit);
            $data[$key]['created_at'] = Carbon::parse($q->created_at)->format('d M Y');
        }

        // Get the total count of the data
        $count = $this->itemHistory->getByArrayDT(0, 0, $searchKey);

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
            'item_id' => 'required'
        ]);

        if (!$request->id && $validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first('item_id') // Menampilkan pesan error pertama untuk field 'name'
            ]);
        }
        try {
            DB::beginTransaction();

            $this->itemHistory->store($request);

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

            $this->itemHistory->destroy($id);

            DB::commit();

            echo json_encode(['status' => true, 'message' => 'Data berhasil dihapus']);
        } catch (\Throwable $th) {
            echo json_encode(['status' => false, 'message' => $th->getMessage()]);
        }
    }
}
