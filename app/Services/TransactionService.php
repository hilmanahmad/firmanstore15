<?php

namespace App\Services;

use App\Models\ItemHistory;
use App\Models\Transaction;
use Illuminate\Support\Str;
use App\Helpers\StringHelper;
use App\Models\TransactionDetail;
use Illuminate\Support\Facades\DB;

class TransactionService
{
    protected $transaction;
    protected $transactionDetail;

    public function __construct(Transaction $transaction, TransactionDetail $transactionDetail)
    {
        $this->transaction = $transaction;
        $this->transactionDetail = $transactionDetail;
    }

    public function getByArrayDT($rows, $offset, $searchKey, $customerId = null, $itemId = null, $typeId = null)
    {
        $query = $this->transaction->query();

        if ($customerId) {
            $query = $query->where('customer_id', $customerId);
        }

        if ($itemId) {
            $query = $query->where('item_id', $itemId);
        }

        if ($typeId) {
            $query = $query->where('type_id', $typeId);
        }

        if ($rows) {
            return $query->whereHas('customer', function ($q) use ($searchKey) {
                $q->where('name', 'LIKE', '%' . $searchKey . '%');
            })->orWhereHas('item', function ($q) use ($searchKey) {
                $q->where('name', 'LIKE', '%' . $searchKey . '%');
            })
                ->select('no_trans', 'customer_id', DB::raw('MIN(created_at) as created_at', 'profit'))
                ->groupBy('no_trans', 'customer_id')
                ->orderBy('created_at', 'DESC')
                ->skip($offset)
                ->take($rows)
                ->get();
        } else {
            return $query->select('no_trans')
                ->groupBy('no_trans')
                ->get()
                ->count();
        }
    }


    public function getById($id)
    {
        return $this->transaction->where('no_trans', $id)->get();
    }

    public function store($request)
    {
        // Generate atau gunakan no_trans existing

        $code_month = StringHelper::code_month('transactions', 'no_trans', 1, 4);
        dd($code_month);
        foreach ($request->items as $itemArray) {
            $itemQuery = ItemHistory::where([
                'item_id' => $itemArray['item_id'],
                'deleted_at' => null
            ]);

            // Check total available stock
            if ($itemQuery->sum(DB::raw('qty - qty_sold')) < $itemArray['qty']) {
                throw new \Exception("Stok tidak mencukupi untuk item yang dipilih");
            }

            $id = Str::uuid();
            $data = [
                'id' => $id,
                'no_trans' => $code_month,
                'customer_id' => $request->customer_id,
                'item_id' => $itemArray['item_id'],
                'type_id' => $itemArray['type_id'],
                'qty' => $itemArray['qty'],
                'selling_price' => str_replace('.', '', $itemArray['selling_price']),
            ];

            $this->transaction->create($data);

            // Process stock allocation with looping (FIFO)
            $remainingQty = $itemArray['qty'];
            $items = ItemHistory::where([
                'item_id' => $itemArray['item_id'],
                'type_id' => $itemArray['type_id'],
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
                        'transaction_id' => $id,
                        'item_history_id' => $item->id,
                        'qty' => $qtyToAllocate,
                        'selling_price' => str_replace('.', '', $itemArray['selling_price']),
                    ]);

                    $remainingQty -= $qtyToAllocate;
                }
            }

            if ($remainingQty > 0) {
                throw new \Exception("Gagal mengalokasikan stok, sisa yang belum teralokasi: " . $remainingQty);
            }
        }
    }

    public function destroy($id)
    {
        $tranDetail = TransactionDetail::where('transaction_id', $id)->get();

        foreach ($tranDetail as $td) {
            $itemHist = ItemHistory::where('id', $td->item_history_id)->first();
            $itemHist->update([
                'qty_sold' => $itemHist->qty_sold - $td->qty
            ]);
        }
        $this->transaction->where('id', $id)->delete();
        $this->transactionDetail->where('transaction_id', $id)->delete();
    }
    public function update($request)
    {
        $noTrans = $request->no_trans;

        // Hapus transaksi lama dengan no_trans yang sama
        $oldTransactions = Transaction::where('no_trans', $noTrans)->get();

        foreach ($oldTransactions as $oldTrans) {
            // Kembalikan stok
            $tranDetails = TransactionDetail::where('transaction_id', $oldTrans->id)->get();
            foreach ($tranDetails as $td) {
                $itemHist = ItemHistory::where('id', $td->item_history_id)->first();
                if ($itemHist) {
                    $itemHist->update([
                        'qty_sold' => $itemHist->qty_sold - $td->qty
                    ]);
                }
            }

            // Hapus detail dan transaksi
            TransactionDetail::where('transaction_id', $oldTrans->id)->delete();
            $oldTrans->delete();
        }

        // Simpan transaksi baru dengan no_trans yang sama
        $request->merge(['no_trans' => $noTrans]);
        return $this->store($request);
    }
}
