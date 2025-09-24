<?php

namespace App\Services\Administrator;

use App\Models\Master\Menu;
use Illuminate\Support\Str;

class MenuService
{
    protected $menu;

    public function __construct(Menu $menu)
    {
        $this->menu = $menu;
    }

    public function getByArrayDT($rows, $offset, $searchKey)
    {
        $query = $this->menu;

        if ($rows) {
            return $query->where('menu_name', 'LIKE', '%' . $searchKey . '%')->orWhere('url', 'LIKE', '%' . $searchKey . '%')->skip($offset)->take($rows)->orderBy('menu_name', 'ASC')->get();
        } else {
            return $query->count();
        }
    }


    public function getById($id)
    {
        return $this->menu->where('id', $id)->first();
    }

    public function store($request)
    {
        $id = Str::uuid();
        $data = [
            'menu_name' => ucwords($request->menu_name),
            'is_header' => isset($request->is_header) ? 'true' : 'false',
            'parent' => isset($request->parent) ? $request->parent : 'false',
            'url' => $request->url,
            'icon' => $request->icon,
            'have_sub_menu' => isset($request->have_sub_menu) ? 'true' : 'false',
            'sort' => $request->sort
        ];
        if ($request->id) {
            $this->menu->where('id', $request->id)->update($data);
        } else {
            $data['id'] = $id;
            $this->menu->create($data);
        }
    }

    public function destroy($id)
    {
        $this->menu->where('id', $id)->delete();
    }
}
