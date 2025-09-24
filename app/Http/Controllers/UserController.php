<?php

namespace App\Http\Controllers\Administrator;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Services\UserService;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    protected $userSvc;
    public function __construct(UserService $userSvc)
    {
        $this->userSvc = $userSvc;
    }

    public function index()
    {
        return view('administrator.user.index', [
            'title' => 'User',
            'active' => 'user'
        ]);
    }

    public function datatable(Request $request)
    {
        $row = $request->input('rows');
        $page = $request->input('page');

        // dd($request);
        $rows = $row >= 10 ? $row : 20;
        $offset = ($page - 1) * $rows;
        $searchKey = $request->input('searchKey');

        // Get the data for the current page

        // dd($rows, $offset, $searchKey);
        $query = $this->userSvc->getByArrayDT($rows, $offset, $searchKey);
        foreach ($query as $key => $q) {
            foreach ($query as $key => $q) {
                $data[$key]['id'] = $q->id;
                $data[$key]['uuid'] = $q->uuid;
                $data[$key]['name'] = $q->name;
                $data[$key]['username'] = $q->username;
                $data[$key]['role'] = $q->role->name;
                $data[$key]['role_code'] = $q->role_code;
                $data[$key]['group_code'] = $q->group_code;
            }
        }

        // Get the total count of the data
        $count = $this->userSvc->getByArrayDT(0, 0, $searchKey);

        $result = [
            "total" => $count,
            "rows" => $data
        ];

        return response()->json($result);
    }



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $this->userSvc->store($request);

            DB::commit();

            echo json_encode(['status' => true, 'message' => 'Data berhasil disimpan']);
        } catch (\Throwable $th) {
            echo json_encode(['status' => false, 'message' => $th->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $this->userSvc->destroy($id);

            DB::commit();

            echo json_encode(['status' => true, 'message' => 'Data berhasil dihapus']);
        } catch (\Throwable $th) {
            echo json_encode(['status' => false, 'message' => $th->getMessage()]);
        }
    }
}
