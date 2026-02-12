<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\ItemHistory;
use Illuminate\Support\Str;
use App\Models\ItemPurchase;
use Illuminate\Http\Request;
use App\Helpers\StringHelper;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Services\ItemPurchaseService;
use Illuminate\Support\Facades\Validator;

class ItemPurchaseController extends Controller
{
    protected $itemPurchase;

    public function __construct(ItemPurchaseService $itemPurchase)
    {
        $this->itemPurchase = $itemPurchase;
    }

    public function index()
    {
        $user = auth()->user();
        $isSuperAdmin = $user && $user->role_code === 'SUPERADMIN';

        return view('itemPurchase.index', [
            'title' => 'Pembelian Barang',
            'active' => 'item-purchase',
            'isSuperAdmin' => $isSuperAdmin,
            'currentUserId' => $user ? $user->id : null,
            'currentUserName' => $user ? $user->name : null,
        ]);
    }

    public function datatable(Request $request)
    {
        $row = $request->input('rows');
        $page = $request->input('page');

        $rows = $row >= 10 ? $row : 20;
        $offset = ($page - 1) * $rows;
        $searchKey = $request->input('searchKey');
        $itemId = $request->input('item_id');
        $typeId = $request->input('type_id');
        $status = $request->input('status');

        // Filter by user if not SUPERADMIN
        $user = auth()->user();
        $userId = null;
        if ($user && $user->role_code !== 'SUPERADMIN') {
            $userId = $user->id;
        }

        $query = $this->itemPurchase->getByArrayDT($rows, $offset, $searchKey, $itemId, $typeId, $status, $userId);
        $data = [];

        foreach ($query as $key => $q) {
            $data[$key]['id'] = $q->id;
            $data[$key]['user_id'] = $q->user_id;
            $data[$key]['user_name'] = $q->user->name ?? '-';
            $data[$key]['item_id'] = $q->item_id;
            $data[$key]['item'] = $q->item->name ?? '-';
            $data[$key]['type_id'] = $q->type_id;
            $data[$key]['type'] = $q->type->name ?? '-';
            $data[$key]['purchase_price'] = StringHelper::formatRupiah($q->purchase_price);
            $data[$key]['qty'] = $q->qty;
            $data[$key]['total'] = StringHelper::formatRupiah($q->purchase_price * $q->qty);
            $data[$key]['tracking_number'] = $q->tracking_number ?? '-';
            $data[$key]['status_badge'] = $q->status === 'received'
                ? '<span class="badge badge-success">Diterima</span>'
                : '<span class="badge badge-warning">Pending</span>';
            $data[$key]['status'] = $q->status;
            $data[$key]['created_at'] = Carbon::parse($q->created_at)->format('d M Y');
        }

        $count = $this->itemPurchase->getByArrayDT(0, 0, $searchKey, $itemId, $typeId, $status, $userId);

        return response()->json([
            "total" => $count,
            "rows" => $data
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'item_id' => 'required',
            'type_id' => 'required',
            'purchase_price' => 'required',
            'qty' => 'required|numeric|min:1',
            'tracking_number' => 'required',
        ], [
            'item_id.required' => 'Barang harus dipilih',
            'type_id.required' => 'Satuan harus dipilih',
            'purchase_price.required' => 'Harga beli harus diisi',
            'qty.required' => 'Jumlah harus diisi',
            'tracking_number.required' => 'Nomor resi harus diisi',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ]);
        }

        try {
            DB::beginTransaction();
            $this->itemPurchase->store($request);
            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Data pembelian berhasil disimpan. Scan resi untuk konfirmasi barang masuk.'
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ]);
        }
    }

    public function confirmReceived(Request $request)
    {
        try {
            $trackingNumber = $request->input('tracking_number');

            if (empty($trackingNumber)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Nomor resi tidak boleh kosong'
                ]);
            }

            // Cari pembelian berdasarkan tracking number
            $purchase = ItemPurchase::where('tracking_number', $trackingNumber)
                ->where('status', 'pending')
                ->first();

            if (!$purchase) {
                // Cek apakah sudah pernah diterima
                $existingReceived = ItemPurchase::where('tracking_number', $trackingNumber)
                    ->where('status', 'received')
                    ->first();

                if ($existingReceived) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Resi "' . $trackingNumber . '" sudah pernah dikonfirmasi sebelumnya'
                    ]);
                }

                return response()->json([
                    'status' => false,
                    'message' => 'Resi "' . $trackingNumber . '" tidak ditemukan atau tidak ada pembelian pending'
                ]);
            }

            DB::beginTransaction();

            // Update status pembelian
            $purchase->update([
                'status' => 'received',
                'received_at' => now()
            ]);

            // Masukkan ke Item History (Stok)
            ItemHistory::create([
                'id' => Str::uuid(),
                'item_id' => $purchase->item_id,
                'type_id' => $purchase->type_id,
                'purchase_price' => $purchase->purchase_price,
                'qty' => $purchase->qty,
                'qty_sold' => 0,
            ]);

            DB::commit();

            $itemName = $purchase->item->name ?? 'Barang';
            $typeName = $purchase->type->name ?? '';

            return response()->json([
                'status' => true,
                'message' => "Barang berhasil dikonfirmasi!<br><br>
                    <strong>Item:</strong> {$itemName} ({$typeName})<br>
                    <strong>Qty:</strong> {$purchase->qty}<br>
                    <strong>Resi:</strong> {$trackingNumber}<br><br>
                    Stok sudah ditambahkan ke sistem."
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ]);
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $this->itemPurchase->destroy($id);
            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Data pembelian berhasil dihapus'
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
