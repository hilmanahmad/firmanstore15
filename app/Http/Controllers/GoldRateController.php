<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class GoldRateController extends Controller
{
    public function index()
    {
        return view('goldRate.index', [
            'title' => 'Harga Emas',
            'active' => 'gold-rate'
        ]);
    }

    public function getRate()
    {
        try {
            $response = Http::post('https://api.treasury.id/api/v1/antigrvty/gold/rate');

            if ($response->successful()) {
                return response()->json($response->json());
            }

            return response()->json([
                'meta' => [
                    'code' => 500,
                    'status' => 'error',
                    'message' => 'Gagal mengambil data dari API'
                ],
                'data' => null
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'meta' => [
                    'code' => 500,
                    'status' => 'error',
                    'message' => $e->getMessage()
                ],
                'data' => null
            ], 500);
        }
    }
}
