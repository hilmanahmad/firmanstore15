<?php

namespace App\Services;

use DB;
use App\Models\ItemHistory;
use App\Models\Transaction;
use Illuminate\Support\Str;
use App\Models\TransactionDetail;

class TransactionService
{
    protected $transaction;

    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    public function getByArrayDT($rows, $offset, $searchKey, $customerId = null, $itemId = null)
    {
        $query = $this->transaction->query();

        if ($customerId) {
            $query = $query->where('customer_id', $customerId);
        }

        if ($itemId) {
            $query = $query->where('item_id', $itemId);
        }


        if ($rows) {
            return $query->whereHas('customer', function ($q) use ($searchKey) {
                $q->where('name', 'LIKE', '%' . $searchKey . '%');
            })->whereHas('item', function ($q) use ($searchKey) {
                $q->where('name', 'LIKE', '%' . $searchKey . '%');
            })->skip($offset)->take($rows)->orderBy('created_at', 'DESC')->get();
        } else {
            return $query->count();
        }
    }


    public function getById($id)
    {
        return $this->transaction->where('id', $id)->first();
    }

    public function store($request)
    {
        $itemQuery = ItemHistory::where([
            'item_id' => $request->item_id,
            'deleted_at' => null
        ]);

        // Check total available stock
        $tran = Transaction::where('id', $request->id)->first();
        if ($request->id) {
            if ($itemQuery->sum(DB::raw('qty - qty_sold')) + $tran->qty < $request->qty) {
                throw new \Exception("Stok tidak mencukupi");
            }
        } else {
            if ($itemQuery->sum(DB::raw('qty - qty_sold')) < $request->qty) {
                throw new \Exception("Stok tidak mencukupi");
            }
        }

        $id = Str::uuid();
        $data = [
            'customer_id' => $request->customer_id,
            'item_id' => $request->item_id,
            'qty' => $request->qty,
            'selling_price' => str_replace('.', '', $request->selling_price),
        ];

        if ($request->id) {
            $this->transaction->where('id', $request->id)->update($data);
            $tranDetail = TransactionDetail::where('transaction_id', $request->id)->get();

            foreach ($tranDetail as $td) {
                $itemHist = ItemHistory::where('id', $td->item_history_id)->first();
                $itemHist->update([
                    'qty_sold' => $itemHist->qty_sold - $td->qty
                ]);
            }
            TransactionDetail::where('transaction_id', $request->id)->delete();
        } else {
            $data['id'] = $id;
            $this->transaction->create($data);
        }

        // Process stock allocation with looping
        $remainingQty = $request->qty;
        $items = ItemHistory::where([
            'item_id' => $request->item_id,
            'deleted_at' => null
        ])->whereRaw('qty > qty_sold')->orderBy('created_at', 'ASC')->get();

        foreach ($items as $item) {
            if ($remainingQty <= 0) {
                break;
            }

            $availableStock = $item->qty - $item->qty_sold;

            if ($availableStock > 0) {
                $qtyToAllocate = min($remainingQty, $availableStock);

                // Update qty_sold
                $item->update([
                    'qty_sold' => $item->qty_sold + $qtyToAllocate
                ]);

                TransactionDetail::create([
                    'id' => Str::uuid(),
                    'transaction_id' => isset($tran->id) ? $tran->id : $id,
                    'item_history_id' => $item->id,
                    'qty' => $qtyToAllocate,
                    'selling_price' => str_replace('.', '', $request->selling_price),

                ]);

                $remainingQty -= $qtyToAllocate;
            }
        }

        if ($remainingQty > 0) {
            throw new \Exception("Gagal mengalokasikan stok, sisa yang belum teralokasi: " . $remainingQty);
        }
    }

    public function destroy($id)
    {
        $this->transaction->where('id', $id)->delete();
    }
}
