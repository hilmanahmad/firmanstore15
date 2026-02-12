<?php

namespace App\Services;

use App\Models\Role;
use Illuminate\Support\Str;

class RoleService
{
    protected $role;

    public function __construct(Role $role)
    {
        $this->role = $role;
    }

    public function getAll()
    {
        return $this->role->where('is_active', true)->orderBy('name', 'ASC')->get();
    }

    public function getByArrayDT($rows, $offset, $searchKey)
    {
        $query = $this->role;

        if ($rows) {
            return $query->where('name', 'LIKE', '%' . $searchKey . '%')
                ->orWhere('code', 'LIKE', '%' . $searchKey . '%')
                ->skip($offset)
                ->take($rows)
                ->orderBy('name', 'ASC')
                ->get();
        } else {
            return $query->count();
        }
    }

    public function getById($id)
    {
        return $this->role->where('id', $id)->first();
    }

    public function getByCode($code)
    {
        return $this->role->where('code', $code)->first();
    }

    public function store($request)
    {
        $data = [
            'code' => strtoupper($request->code),
            'name' => ucwords($request->name),
            'description' => $request->description,
            'is_active' => isset($request->is_active) ? true : false,
        ];

        if ($request->id) {
            $this->role->where('id', $request->id)->update($data);
            return $this->role->where('id', $request->id)->first();
        } else {
            return $this->role->create($data);
        }
    }

    public function destroy($id)
    {
        return $this->role->where('id', $id)->delete();
    }
}
