<?php

namespace App\Services;

use App\Models\ItemHistory;
use Illuminate\Support\Str;

class ItemHistoryService
{
    protected $itemHistory;

    public function __construct(ItemHistory $itemHistory)
    {
        $this->itemHistory = $itemHistory;
    }

    public function getByArrayDT($rows, $offset, $searchKey, $itemId = null, $typeId = null)
    {
        $query = $this->itemHistory->query();

        if ($itemId) {
            $query = $query->where('item_id', $itemId);
        }

        if ($typeId) {
            $query = $query->where('type_id', $typeId);
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
        return $this->itemHistory->where('id', $id)->first();
    }

    public function store($request)
    {
        $id = Str::uuid();
        $data = [
            'item_id' => $request->item_id,
            'type_id' => $request->type_id,
            'purchase_price' => str_replace(".", "", $request->purchase_price),
            'qty' => $request->qty,
        ];
        if ($request->id) {
            $this->itemHistory->where('id', $request->id)->update($data);
        } else {
            $data['id'] = $id;
            $data['qty_sold'] = 0;
            $this->itemHistory->create($data);
        }
    }

    public function destroy($id)
    {
        $this->itemHistory->where('id', $id)->delete();
    }
}
