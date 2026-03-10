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
        $typeId = $request->input('type_id');

        // Get the data for the current page

        // dd($rows, $offset, $searchKey);
        $query = $this->transaction->getByArrayDT($rows, $offset, $searchKey, $customerId, $itemId, $typeId);
        $data = [];
        foreach ($query as $key => $q) {
            $totalQty = 0;
            $totalAmount = 0;
            $totalProfit = 0;
            $items = [];

            foreach ($q->transaction as $trans) {
                $items[] = $trans->item->name . ' (' . $trans->type->name . ')';
                $totalQty += $trans->qty;
                $totalAmount += $trans->selling_price * $trans->qty;

                // Hitung profit per transaksi
                foreach ($trans->detail as $detail) {
                    $profit = ($trans->selling_price - $detail->itemHistory->purchase_price) * $detail->qty;
                    $totalProfit += $profit;
                }
            }
            $data[$key]['no_trans'] = $q->no_trans;
            $data[$key]['customer_id'] = $q->customer_id;
            $data[$key]['customer'] = $q->customer->name;
            $data[$key]['item'] = implode(', ', $items);
            $data[$key]['amount'] = StringHelper::formatRupiah($totalAmount);
            $data[$key]['profit'] = StringHelper::formatRupiah($totalProfit);
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
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required',
            'items.*.type_id' => 'required',
            'items.*.qty' => 'required|numeric|min:1',
            'items.*.selling_price' => 'required',
        ], [
            'customer_id.required' => 'Pelanggan harus dipilih',
            'items.required' => 'Minimal tambahkan 1 item',
            'items.*.item_id.required' => 'Barang harus dipilih',
            'items.*.type_id.required' => 'Satuan harus dipilih',
            'items.*.qty.required' => 'Jumlah harus diisi',
            'items.*.qty.min' => 'Jumlah minimal 1',
            'items.*.selling_price.required' => 'Harga jual harus diisi',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ]);
        }

        try {
            DB::beginTransaction();

            // Check if edit mode (update)
            if ($request->has('no_trans') && !empty($request->no_trans)) {
                $this->transaction->update($request);
                $message = 'Data berhasil diupdate';
            } else {
                $this->transaction->store($request);
                $message = 'Data berhasil disimpan';
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => $message
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

    public function getDetail($id)
    {
        try {
            $transaction = $this->transaction->getById($id);

            if (!$transaction) {
                return response()->json([
                    'status' => false,
                    'message' => 'Transaksi tidak ditemukan'
                ]);
            }

            foreach ($transaction as $key => $q) {
                $profit = 0;
                foreach ($q->detail as $detail) {
                    // Hitung profit per detail
                    $profit += ($q->selling_price - $detail->itemHistory->purchase_price) * $detail->qty;
                    // Simpan atau tampilkan profit sesuai kebutuhan
                }
                $data[$key]['no_trans'] = $q->no_trans;
                $data[$key]['customer_id'] = $q->customer_id;
                $data[$key]['customer'] = $q->customer->name;
                $data[$key]['item_id'] = $q->item_id;
                $data[$key]['item'] = $q->item->name;
                $data[$key]['type_id'] = $q->type_id;
                $data[$key]['type'] = $q->type->name;
                $data[$key]['qty'] = $q->qty;
                $data[$key]['selling_price'] = StringHelper::formatRupiah($q->selling_price);
                $data[$key]['profit'] = StringHelper::formatRupiah($profit);
                $data[$key]['created_at'] = Carbon::parse($q->created_at)->format('d M Y');
            }

            return response()->json([
                'status' => true,
                'data' => $data
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ]);
        }
    }

    /**
     * Delete all transactions with the same no_trans (transaction group)
     */
    public function deleteGroup($noTrans)
    {
        try {
            DB::beginTransaction();

            $this->transaction->destroyGroup($noTrans);

            DB::commit();

            echo json_encode(['status' => true, 'message' => 'Transaksi berhasil dihapus']);
        } catch (\Throwable $th) {
            DB::rollBack();
            echo json_encode(['status' => false, 'message' => $th->getMessage()]);
        }
    }
}
