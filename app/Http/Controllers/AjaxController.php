<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Master\Pool;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Helpers\TariffHelper;
use App\Models\Master\Outlet;
use App\Models\Master\Vehicle;
use App\Models\Master\Customer;
use App\Models\Master\Employee;
use Illuminate\Support\Facades\DB;
use App\Models\Order\OrderHandling;
use Illuminate\Support\Facades\Hash;
use App\Models\VendorManagement\Vendor;
use App\Models\Configuration\RouteTariff;
use App\Models\VendorManagement\VendorTariff;
use App\Models\VendorManagement\TermOfPayment;
use App\Models\VendorManagement\VendorTermOfPayment;

class AjaxController extends Controller
{
    public function optionAjax(Request $request)
    {
        // Extract validated variables
        $table = $request->table;
        $order = $request->order;
        $whereName = $request->whereName ?? null;
        $whereValue = $request->whereValue ?? null;

        // Build the query
        $query = DB::table("$table");

        // Add the where clause if both whereName and whereValue are provided
        if ($whereName && $whereValue) {
            $query->where($whereName, $whereValue);
        }

        $query->where('deleted_at', null);
        // Order the query based on the provided order column
        $query->orderBy($order);

        // Execute and get the results
        $result = $query->get();

        return response()->json($result);
    }

    public function optionAjaxx(Request $request)
    {
        // Extract validated variables
        $table = $request->table;
        $order = $request->order;
        $whereName = $request->whereName ?? null;
        $whereValue = $request->whereValue ?? null;
        $whereName2 = $request->whereName2 ?? null;
        $whereValue2 = $request->whereValue2 ?? null;

        // Build the query
        $query = DB::table("$table");

        // Add the where clause if both whereName and whereValue are provided
        if ($whereName && $whereValue) {
            $query->where($whereName, $whereValue);
        }

        if ($whereName2 && $whereValue2) {
            $query->where($whereName2, $whereValue2);
        }

        // Order the query based on the provided order column
        $query->orderBy($order);

        // Execute and get the results
        $result = $query->get();

        return response()->json($result);
    }

    public function optionRegencie(Request $request)
    {
        // Extract validated variables
        $whereValue = $request->whereValue ?? null;
        $whereId = $request->whereId ?? null;
        // Build the query
        $query = DB::table('regencies');
        $query->select('regencies.*', 'provinces.name as province_name');
        $query->join('provinces', 'regencies.province_id', '=', 'provinces.id');

        // Order the query based on the provided order column
        if ($whereValue) {
            $query->orWhere('regencies.name', 'like', '%' . $whereValue . '%');
            $query->orWhere('provinces.name', 'like', '%' . $whereValue . '%');
        };
        $query->orderBy('regencies.name');

        // Execute and get the results
        $result = $query->get();

        return response()->json($result);
    }

    public function optionDistrict(Request $request)
    {
        // Extract validated variables
        $whereValue = $request->whereValue ?? null;
        $whereId = $request->whereId ?? null;
        // Build the query
        $query = DB::table('districts');
        $query->select('districts.*', 'regencies.name as regency_name', 'provinces.name as province_name');
        $query->join('regencies', 'districts.regency_id', '=', 'regencies.id');
        $query->join('provinces', 'regencies.province_id', '=', 'provinces.id');
        // Order the query based on the provided order column
        if ($whereValue) {
            $query->where('districts.name', 'like', '%' . $whereValue . '%');
            $query->orWhere('regencies.name', 'like', '%' . $whereValue . '%');
            $query->orWhere('provinces.name', 'like', '%' . $whereValue . '%');
        } else if ($whereId) {
            $query->where('districts.id', $whereId);
        }
        $query->orderBy('districts.name');

        // Execute and get the results
        $result = $query->get();
        return response()->json($result);
    }

    public function optionAjaxWhere(Request $request)
    {
        // Extract validated variables
        $table = $request->table;
        $order = $request->order;
        $whereName = $request->whereName ?? null;
        $whereValue = $request->whereValue ?? null;
        $whereName2 = $request->whereName2 ?? null;
        $whereValue2 = $request->whereValue2 ?? null;
        $whereName3 = $request->whereName3 ?? null;
        $whereValue3 = $request->whereValue3 ?? null;

        // Build the query
        $query = DB::table("$table");

        // Add the where clause if both whereName and whereValue are provided
        if ($whereName && $whereValue) {
            $query->where($whereName, 'like', '%' . $whereValue . '%');
        }
        // Add the where clause if both whereName and whereValue are provided
        if ($whereName2 && $whereValue2) {
            $query->where($whereName2, 'like', '%' . $whereValue2 . '%');
        }
        if ($whereName3 && $whereValue3) {
            $query->where($whereName3, 'like', '%' . $whereValue3 . '%');
        }
        $query->where('deleted_at', null);
        // Order the query based on the provided order column
        $query->orderBy($order);

        // Execute and get the results
        $result = $query->get();

        return response()->json($result);
    }

    public function tariffTable(Request $request)
    {
        // Extract validated variables
        // Build the query
        $regencieFrom = Pool::where('code', $request->vehicle_pool_id)->first();
        $regencieTo = Outlet::where('code', $request->unloading_location_id)->first();
        $query = RouteTariff::where('type_id', $request->type_id)->where('regencie_from_id', $regencieFrom->regencie_id)->where('regencie_to_id', $regencieTo->regencie_id)->where('rit', $request->rit)->whereNull('deleted_at');

        // Execute and get the results
        $result = $query->first();
        return response()->json($result);
    }

    public function orderHandling(Request $request)
    {
        // Extract validated variables
        // Build the query  
        // Tanggal 5 hari yang lalu
        $result = [];
        $dateFiveDaysAgo = Carbon::now()->subDays(5);
        $dateInFiveDays = Carbon::now()->addDays(5);

        // Format tanggal
        $startDate = $dateFiveDaysAgo->format('Y-m-d');
        $endDate = $dateInFiveDays->format('Y-m-d');
        $data = [];
        $query = OrderHandling::select('tbl_order_handling.nopol', 'tbl_order_handling.kiriman_ke', 'tbl_order_handling.tgl_pesan', 't_m_kendaraan.SBU_CODE')
            ->distinct()
            ->join('t_m_kendaraan', function ($join) {
                $join->on('tbl_order_handling.nopol', '=', 't_m_kendaraan.NO_POL');
                if (auth()->user()->role_code == 'KK' || auth()->user()->role_code == 'KANONOP') {
                    $group = auth()->user()->group_code == 'RCK' ? 'CHAKRA' : auth()->user()->group_code;
                    $join->where('t_m_kendaraan.SBU_CODE', '=', $group);
                }
            })
            ->whereBetween('tbl_order_handling.tgl_pesan', [$startDate, $endDate])
            ->where('tbl_order_handling.stat_import', 'N')
            ->orderBy('tbl_order_handling.tgl_pesan', 'DESC')
            ->get();
        $outlet = [];
        foreach ($query as $key => $value) {
            $results = OrderHandling::query()
                ->select('i.kd_outlet', 'l.outlet_name', 'h.nama_kecamatan as nm_kec', 'h.nama_kabupaten as nm_kota')
                ->distinct('kd_outlet')
                ->fromSub(function ($query) use ($value) {
                    $query->select('kd_outlet')
                        ->from('tbl_order_handling')
                        ->where('nopol', $value->nopol)
                        ->where('tgl_pesan', $value->tgl_pesan)
                        ->where('kiriman_ke', $value->kiriman_ke)
                        ->distinct();
                }, 'i')
                ->leftJoin('t_outlet as l', 'i.kd_outlet', '=', 'l.outlet_code')
                ->leftJoin('t_wilayah as h', 'l.kode_distribusi', '=', 'h.kode_distribusi')
                ->whereNotNull('l.outlet_name')
                ->get();

            // $outlet = $results->pluck('outlet_name')->implode(',<br>');
            $result = $results->pluck('outlet_name')->map(function ($outlet, $key) use ($results) {
                $val = $results[$key];
                return $outlet . ', ' . $val->nm_kec . ', ' . $val->nm_kota;
            })->implode('<br>');

            $data[$key] = [
                'nopol' => $value->nopol,
                'kiriman_ke' => $value->kiriman_ke,
                'tgl_pesan' => $value->tgl_pesan,
                'outlet' => $result,
                'total_outlet' => count($results),
            ];
        }
        // Execute and get the results
        return response()->json($data);
    }

    public function dataOrderHandling(Request $request)
    {
        $data = [];
        $results = DB::connection('mysql2')->select("SELECT i.id, i.kd_outlet, l.outlet_name, i.kd_brg, p.itemProduct_name nama_barang, i.qty, h.nama_kecamatan as nm_kec, h.nama_kabupaten as nm_kota FROM ( SELECT id, kd_outlet, kd_brg, qty FROM tbl_order_handling WHERE nopol = '$request->nopol' AND tgl_pesan = '$request->tgl_pesan' AND kiriman_ke = $request->kiriman_ke ) i LEFT JOIN t_outlet l ON i.kd_outlet = l.outlet_code LEFT JOIN t_wilayah h ON l.kode_distribusi = h.kode_distribusi LEFT JOIN t_item_product p ON p.itemProduct_code = i.kd_brg WHERE l.outlet_name IS NOT NULL");
        foreach ($results as $key => $value) {
            $data[$key] = [
                'id' => $value->id,
                'kd_outlet' => $value->kd_outlet,
                'outlet_name' => $value->outlet_name . ', ' . $value->nm_kec . ', ' . $value->nm_kota,
                'kd_brg' => $value->kd_brg,
                'nama_barang' => $value->nama_barang,
                'qty' => $value->qty,
            ];
        }

        // Execute and get the results
        return response()->json($data);
    }

    public function generateToken(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // Generate a new token
        $token = Str::random(60);

        // Save token to user
        $user->api_token = hash('sha256', $token);
        $user->save();

        return response()->json(['access_key' => $token]);
    }

    public function tariffVendor(Request $request)
    {
        // Extract validated variables

        // Build the query
        $result = VendorTariff::with(['vendor' => function ($query) {
            $query->where('status', 1);
        }])->where('loading_district_id', 'like', '%' . $request->from_id . '%')->where('unloading_district_id', 'like', '%' . $request->distance_id . '%')->where('type_id', 'like', '%' . $request->type_id . '%')->get();

        return response()->json($result);
    }

    public function termOfPayment(Request $request)
    {
        // Extract validated variables
        $vendor = VendorTariff::where('id', $request->id)->first();
        // Build the query
        $result = VendorTermOfPayment::where('vendor_id', $vendor->vendor_id)->orderBy('term_of_payment_id', 'ASC')->get();

        return response()->json($result);
    }

    public function updateChartData(Request $request)
    {
        $pools = Pool::whereNull('deleted_at')->get();

        // Rit 1
        $pools->loadCount(['orders as order_count' => function ($query) use ($request) {
            $query->where('rit', 1)->whereBetween(DB::raw('DATE(created_at)'), [$request->start_date, $request->end_date])->whereNull('deleted_at');
        }]);

        // Rit 0.5
        $pools->loadCount(['orders as order_count_half' => function ($query) use ($request) {
            $query->where('rit', 0.5)->whereBetween(DB::raw('DATE(created_at)'), [$request->start_date, $request->end_date])->whereNull('deleted_at');
        }]);

        $data['labels'] = $pools->pluck('code')->toArray();
        $data['data'] = $pools->pluck('order_count')->toArray();
        $data['labels_2'] = $pools->pluck('code')->toArray();
        $data['data_2'] = $pools->pluck('order_count_half')->toArray();
        return response()->json($data);
    }

    public function generateTariff(Request $request)
    {
        $result = TariffHelper::generateTariff($request->id);

        return response()->json($result);
    }
}
