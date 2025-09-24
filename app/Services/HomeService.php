<?php

namespace App\Services;

use App\Models\Master\Pool;
use Illuminate\Support\Str;
use App\Helpers\StringHelper;
use App\Models\Master\Type;
use App\Models\Master\Vehicle;
use Illuminate\Support\Facades\DB;

class HomeService
{
    protected $pool;
    protected $vehicle;
    protected $type;

    public function __construct(Pool $pool, Vehicle $vehicle, Type $type)
    {
        $this->pool = $pool;
        $this->vehicle = $vehicle;
        $this->type = $type;
    }

    public function getPoolDT($rows, $offset, $searchKey, $start_date, $end_date, $status_id)
    {
        if ($status_id == 'type') {
            $query = $this->type->whereNull('deleted_at')
                ->withCount([
                    'vehiclesInt as total_int' => function ($query) use ($start_date, $end_date) {
                        $query->leftJoin('order', 'vehicle.code', '=', 'order.vehicle_id')
                            ->whereNull('order.deleted_at')
                            ->whereBetween(DB::raw('DATE(dmlt_order.created_at)'), [$start_date, $end_date]);
                    }, 'vehiclesExt as total_ext' => function ($query) use ($start_date, $end_date) {
                        $query->leftJoin('order', 'vehicle.code', '=', 'order.vehicle_id')
                            ->whereNull('order.deleted_at')
                            ->whereBetween(DB::raw('DATE(dmlt_order.created_at)'), [$start_date, $end_date]);
                    }
                ]);

            if ($rows) {
                return $query->skip($offset)
                    ->take($rows)
                    ->get(['name', 'code']);
            }

            return $query->count();
        } else {
            $query = $status_id === 'vehicle' ? $this->vehicle : $this->pool;

            $query = $query->whereNull('deleted_at')
                ->withCount([
                    'ordersInt as total_int' => function ($query) use ($start_date, $end_date) {
                        $query->whereNull('deleted_at')
                            ->whereBetween(DB::raw('DATE(created_at)'), [$start_date, $end_date]);
                    }, 'ordersExt as total_ext' => function ($query) use ($start_date, $end_date) {
                        $query->whereNull('deleted_at')
                            ->whereBetween(DB::raw('DATE(created_at)'), [$start_date, $end_date]);
                    },
                ]);

            if ($rows) {
                return $query->skip($offset)
                    ->take($rows)
                    ->get(['name', 'code']);
            }

            return $query->count();
        }
    }
}
