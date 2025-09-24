<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Support\Str;

class CategoryService
{
    protected $category;

    public function __construct(Category $category)
    {
        $this->category = $category;
    }

    public function getByArrayDT($rows, $offset, $searchKey)
    {
        $query = $this->category;

        if ($rows) {
            return $query->where('name', 'LIKE', '%' . $searchKey . '%')->skip($offset)->take($rows)->orderBy('created_at', 'DESC')->get();
        } else {
            return $query->count();
        }
    }


    public function getById($id)
    {
        return $this->category->where('id', $id)->first();
    }

    public function store($request)
    {
        $id = Str::uuid();
        $data = [
            'name' => strtoupper($request->name)
        ];
        if ($request->id) {
            $this->category->where('id', $request->id)->update($data);
        } else {
            $data['id'] = $id;
            $this->category->create($data);
        }
    }

    public function destroy($id)
    {
        $this->category->where('id', $id)->delete();
    }
}
