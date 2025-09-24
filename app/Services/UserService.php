<?php

namespace App\Services;

use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserService
{
    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function getByArrayDT($rows, $offset, $searchKey)
    {
        $query = $this->user;

        if ($rows) {
            return $query->where('name', 'LIKE', '%' . $searchKey . '%')->orWhere('username', 'LIKE', '%' . $searchKey . '%')->skip($offset)->take($rows)->orderBy('created_at', 'DESC')->get();
        } else {
            return $query->count();
        }
    }


    public function getById($id)
    {
        return $this->user->where('id', $id)->first();
    }

    public function store($request)
    {
        $id = Str::uuid();
        $data = [
            'name' => $request->name,
            'username' => $request->username,
            'role_code' => $request->role_code,
            'group_code' => $request->sbu_code,
        ];
        if ($request->uuid) {
            $this->user->where('uuid', $request->uuid)->update($data);
        } else {
            $data['password'] = Hash::make('123456');
            $data['uuid'] = $id;
            $this->user->create($data);
        }
    }

    public function destroy($id)
    {
        $this->user->where('uuid', $id)->delete();
    }
}
