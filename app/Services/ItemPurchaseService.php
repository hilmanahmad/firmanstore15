<?php

namespace App\Services;

use App\Models\ItemPurchase;
use Illuminate\Support\Str;

class ItemPurchaseService
{
    protected $itemPurchase;

    public function __construct(ItemPurchase $itemPurchase)
    {
        $this->itemPurchase = $itemPurchase;
    }

    public function getByArrayDT($rows, $offset, $searchKey, $itemId = null, $typeId = null, $status = null, $userId = null)
    {
        $query = $this->itemPurchase->query();

        // Filter by user_id if provided (for non-SUPERADMIN)
        if ($userId) {
            $query = $query->where('user_id', $userId);
        }

        if ($itemId) {
            $query = $query->where('item_id', $itemId);
        }

        if ($typeId) {
            $query = $query->where('type_id', $typeId);
        }

        if ($status) {
            $query = $query->where('status', $status);
        }

        if ($rows) {
            return $query->whereHas('item', function ($q) use ($searchKey) {
                $q->where('name', 'LIKE', '%' . $searchKey . '%');
            })->skip($offset)->take($rows)->orderBy('created_at', 'DESC')->get();
        } else {
            return $query->count();
        }
    }

    public function getById($id)
    {
        return $this->itemPurchase->where('id', $id)->first();
    }

    public function store($request)
    {
        $id = Str::uuid();
        $data = [
            'user_id' => $request->user_id ?? auth()->id(),
            'item_id' => $request->item_id,
            'type_id' => $request->type_id,
            'purchase_price' => str_replace(".", "", $request->purchase_price),
            'qty' => $request->qty,
            'tracking_number' => $request->tracking_number,
            'status' => 'pending', // Default pending
        ];

        if ($request->id) {
            $this->itemPurchase->where('id', $request->id)->update($data);
        } else {
            $data['id'] = $id;
            $this->itemPurchase->create($data);
        }

        return $data;
    }

    public function destroy($id)
    {
        $this->itemPurchase->where('id', $id)->delete();
    }

    public function getAll()
    {
        return $this->itemPurchase->orderBy('created_at', 'DESC')->get();
    }
}
