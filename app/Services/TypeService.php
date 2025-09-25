<?php

namespace App\Services;

use App\Models\Type;
use Illuminate\Support\Str;

class TypeService
{
    protected $type;

    public function __construct(Type $type)
    {
        $this->type = $type;
    }

    public function getByArrayDT($rows, $offset, $searchKey)
    {
        $query = $this->type;

        if ($rows) {
            return $query->where('name', 'LIKE', '%' . $searchKey . '%')->skip($offset)->take($rows)->orderBy('created_at', 'DESC')->get();
        } else {
            return $query->count();
        }
    }


    public function getById($id)
    {
        return $this->type->where('id', $id)->first();
    }

    public function store($request)
    {
        $id = Str::uuid();
        $data = [
            'name' => strtoupper($request->name)
        ];
        if ($request->id) {
            $this->type->where('id', $request->id)->update($data);
        } else {
            $data['id'] = $id;
            $this->type->create($data);
        }
    }

    public function destroy($id)
    {
        $this->type->where('id', $id)->delete();
    }
}
