<?php

namespace App\Services;

use App\Models\Item;
use Illuminate\Support\Str;

class ItemService
{
    protected $item;

    public function __construct(Item $item)
    {
        $this->item = $item;
    }

    public function getByArrayDT($rows, $offset, $searchKey)
    {
        $query = $this->item;

        if ($rows) {
            return $query->with(['itemHistory'])->where('name', 'LIKE', '%' . $searchKey . '%')->orWhereHas('category', function ($q) use ($searchKey) {
                $q->where('name', 'LIKE', '%' . $searchKey . '%');
            })->skip($offset)->take($rows)->orderBy('created_at', 'DESC')->get();
        } else {
            return $query->count();
        }
    }


    public function getById($id)
    {
        return $this->item->where('id', $id)->first();
    }

    public function store($request)
    {
        $id = Str::uuid();
        $data = [
            'category_id' => $request->category_id,
            'name' => strtoupper($request->name)
        ];
        if ($request->id) {
            $this->item->where('id', $request->id)->update($data);
        } else {
            $data['id'] = $id;
            $this->item->create($data);
        }
    }

    public function destroy($id)
    {
        $this->item->where('id', $id)->delete();
    }
}
