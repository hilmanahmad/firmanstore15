<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Helpers\StringHelper;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Services\TransactionService;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    protected $transaction;
    public function __construct(TransactionService $transaction)
    {
        $this->transaction = $transaction;
    }

    public function index()
    {
        return view('transaction.index', [
            'title' => 'Transaksi',
            'active' => 'transaction'
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
        $customerId = $request->input('customer_id');
        $itemId = $request->input('item_id');

        // Get the data for the current page

        // dd($rows, $offset, $searchKey);
        $query = $this->transaction->getByArrayDT($rows, $offset, $searchKey, $customerId, $itemId);
        $data = [];
        foreach ($query as $key => $q) {
            $profit = 0;
            foreach ($q->detail as $detail) {
                // Hitung profit per detail
                $profit += ($q->selling_price - $detail->itemHistory->purchase_price) * $detail->qty;
                // Simpan atau tampilkan profit sesuai kebutuhan
            }
            $data[$key]['id'] = $q->id;
            $data[$key]['customer_id'] = $q->customer_id;
            $data[$key]['customer'] = $q->customer->name;
            $data[$key]['item_id'] = $q->item_id;
            $data[$key]['item'] = $q->item->name;
            $data[$key]['qty'] = $q->qty;
            $data[$key]['selling_price'] = StringHelper::formatRupiah($q->selling_price);
            $data[$key]['profit'] = StringHelper::formatRupiah($profit);
            $data[$key]['created_at'] = Carbon::parse($q->created_at)->format('d M Y');
        }

        // Get the total count of the data
        $count = $this->transaction->getByArrayDT(0, 0, $searchKey);

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
            'customer_id' => 'required',
            'item_id' => 'required',
        ]);

        if (!$request->id && $validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first('item_id') // Menampilkan pesan error pertama untuk field 'name'
            ]);
        }
        try {
            DB::beginTransaction();

            $this->transaction->store($request);

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

            $this->transaction->destroy($id);

            DB::commit();

            echo json_encode(['status' => true, 'message' => 'Data berhasil dihapus']);
        } catch (\Throwable $th) {
            echo json_encode(['status' => false, 'message' => $th->getMessage()]);
        }
    }
}
