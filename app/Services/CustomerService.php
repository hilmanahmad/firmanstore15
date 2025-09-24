<?php

namespace App\Services;

use Illuminate\Support\Str;
use App\Models\Customer;

class CustomerService
{
    protected $customer;

    public function __construct(Customer $customer)
    {
        $this->customer = $customer;
    }

    public function getByArrayDT($rows, $offset, $searchKey)
    {
        $query = $this->customer;

        if ($rows) {
            return $query->where('name', 'LIKE', '%' . $searchKey . '%')->skip($offset)->take($rows)->orderBy('created_at', 'DESC')->get();
        } else {
            return $query->count();
        }
    }


    public function getById($id)
    {
        return $this->customer->where('id', $id)->first();
    }

    public function store($request)
    {
        $id = Str::uuid();
        $data = [
            'name' => strtoupper($request->name)
        ];
        if ($request->id) {
            $this->customer->where('id', $request->id)->update($data);
        } else {
            $data['id'] = $id;
            $this->customer->create($data);
        }
    }

    public function destroy($id)
    {
        $this->customer->where('id', $id)->delete();
    }
}
