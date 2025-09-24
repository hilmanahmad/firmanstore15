<?php

namespace App\Helpers;

use App\Models\Master\Status;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class StringHelper
{
    const API_URL = 'https://routes.googleapis.com/directions/v2:computeRoutes';

    public static function generateThreeLetterCode($inputText)
    {

        return strtoupper(substr($inputText, 0, 3));
    }

    public static function code_month($table, $column, $start, $end)
    {
        $sbu = isset(auth()->user()->sbu->branch_id) ? str_pad(auth()->user()->sbu->branch_id, 2, '0', STR_PAD_LEFT) : null;
        // Pastikan Anda memiliki akses ke DB facade
        $currentYearMonth = date('my');

        // Hitung jumlah entri pada tabel dengan kode bulan saat ini
        $lastId = DB::table($table)
            ->selectRaw('COUNT(*) as id')
            ->whereRaw("SUBSTRING($column, $start, $end) = ?", [$currentYearMonth . $sbu])
            ->value('id');

        // Increment ID untuk kode baru
        $newId = $lastId + 1;

        // Format kode
        $code = str_pad($newId, 4, '0', STR_PAD_LEFT);
        $finalCode = $currentYearMonth . $sbu . $code;

        return $finalCode;
    }

    public static function code_month_vendor($table, $column, $start, $end)
    {
        $sbu = isset(auth()->user()->sbu->branch_id) ? auth()->user()->sbu->pool->city_abbreviation : null;
        // Pastikan Anda memiliki akses ke DB facade
        $currentYearMonth = date('Ym');

        // Hitung jumlah entri pada tabel dengan kode bulan saat ini
        $lastId = DB::table($table)
            ->selectRaw('COUNT(*) as id')
            ->whereRaw("SUBSTRING($column, $start, $end) = ?", ['TO' . $currentYearMonth])
            ->value('id');

        // Increment ID untuk kode baru
        $newId = $lastId + 1;

        // Format kode
        $code = str_pad($newId, 6, '0', STR_PAD_LEFT);
        $finalCode = 'TO' . $currentYearMonth . $code;

        return $finalCode;
    }

    public static function code_month_voucher($table, $column, $start, $end)
    {
        $sbu = isset(auth()->user()->sbu->branch_id) ? auth()->user()->sbu->pool->city_abbreviation : null;
        // Pastikan Anda memiliki akses ke DB facade
        $currentYearMonth = date('Ym');

        // Hitung jumlah entri pada tabel dengan kode bulan saat ini
        $lastId = DB::table($table)
            ->selectRaw('COUNT(*) as id')
            ->whereRaw("SUBSTRING($column, $start, $end) = ?", [$sbu . '-' . $currentYearMonth])
            ->value('id');

        // Increment ID untuk kode baru
        $newId = $lastId + 1;

        // Format kode
        $code = str_pad($newId, 4, '0', STR_PAD_LEFT);
        $finalCode = $sbu . '-' . $currentYearMonth . $code;

        return $finalCode;
    }

    public static function formatRupiah($amount)
    {
        return number_format($amount, 0, ',', '.');
    }

    public static function formatHours($hour)
    {
        if (empty($hour)) {
            return null; // Atau bisa return pesan error jika diperlukan
        }

        try {
            // Mengonversi ke objek Carbon dengan format 24 jam (jam dan menit)
            $waktu = Carbon::createFromFormat('H:i:s', $hour);

            // Jika berhasil, kembalikan format jam dan menit dalam format yang diinginkan
            return $waktu->format('H:i'); // Contoh output: 14:30
        } catch (\Exception $e) {
            // Tangani jika terjadi error, misalnya format tidak sesuai
            return "Format jam tidak valid"; // Atau bisa throw exception
        }
    }

    public static function terbilang($x)
    {
        $angka = ['', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan', 'sepuluh', 'sebelas'];

        if ($x < 12) {
            return ' ' . $angka[$x];
        } elseif ($x < 20) {
            return self::terbilang($x - 10) . ' belas';
        } elseif ($x < 100) {
            return self::terbilang($x / 10) . ' puluh' . self::terbilang($x % 10);
        } elseif ($x < 200) {
            return ' seratus' . self::terbilang($x - 100);
        } elseif ($x < 1000) {
            return self::terbilang($x / 100) . ' ratus' . self::terbilang($x % 100);
        } elseif ($x < 2000) {
            return ' seribu' . self::terbilang($x - 1000);
        } elseif ($x < 1000000) {
            return self::terbilang($x / 1000) . ' ribu' . self::terbilang($x % 1000);
        } elseif ($x < 1000000000) {
            return self::terbilang($x / 1000000) . ' juta' . self::terbilang($x % 1000000);
        }
    }

    public static function format_date($date)
    {
        $now = Carbon::parse($date);
        return $now->format('d F Y');
    }

    public static function deleteFile($filePath)
    {
        // Hapus file jika ada
        if (Storage::disk('public')->exists($filePath)) {
            Storage::disk('public')->delete($filePath);
            return response()->json([
                'status' => true,
                'message' => 'File berhasil dihapus!',
            ]);
        }


        return response()->json([
            'status' => false,
            'message' => 'File tidak ditemukan.',
        ], 404);
    }

    public static function get_distance($originLatitude, $originLongitude, $destinationLatitude, $destinationLongitude)
    {
        // 2. Panggil computeRoutes endpoint
        $response = Http::withHeaders([
            // Minta agar API mengembalikan bagian tolls
            'X-Goog-FieldMask' => 'routes.tolls,routes.distance,routes.duration',
        ])->post(
            'https://routes.googleapis.com/v2:computeRoutes?key=' . env('GOOGLE_MAPS_KEY'),
            [
                'origin'      => ['latitude' => $originLatitude,  'longitude' => $originLongitude],
                'destination' => ['latitude' => $destinationLatitude,  'longitude' => $destinationLongitude],
                'travelMode'  => 'DRIVE',
                'routeModifiers' => [
                    'avoidTolls'    => false,
                    'emissionType'  => 'GASOLINE_VEHICLE',
                    // Sertakan pass yang relevan untuk Indonesia
                    'tollPasses'    => ['ID_EXPRESS_LANE_PASS'],
                ],
            ]
        );

        $data = $response->json();

        // 3. Ambil daftar tolls (jika ada)
        $tolls = data_get($data, 'routes.0.tolls', []);

        // 4. Tampilkan sebagai JSON (atau bisa di-passing ke view)
        return response()->json([
            'origin'      => [$originLatitude, $originLongitude],
            'destination' => [$destinationLatitude, $destinationLongitude],
            'tolls'       => $tolls,
        ]);
    }

    public static function getTollFare(
        float $originLat,
        float $originLng,
        float $destLat,
        float $destLng,
        string $emissionType = 'GASOLINE',
        array  $tollPasses     = ['ID_E_TOLL']
    ): float {
        $apiKey = env('GOOGLE_MAPS_KEY');

        $client = new Client([
            'headers' => [
                'Content-Type'     => 'application/json',
                'X-Goog-Api-Key'   => $apiKey,
                'X-Goog-FieldMask' => 'routes.travelAdvisory.tollInfo',
            ],
        ]);

        $payload = [
            'origin'            => [
                'location' => [
                    'latLng' => ['latitude' => $originLat, 'longitude' => $originLng],
                ],
            ],
            'destination'       => [
                'location' => [
                    'latLng' => ['latitude' => $destLat,    'longitude' => $destLng],
                ],
            ],
            'travelMode'        => 'DRIVE',
            'extraComputations' => ['TOLLS'],
            'routeModifiers'    => [
                'vehicleInfo' => ['emissionType' => $emissionType],
                'tollPasses'   => $tollPasses,
            ],
        ];

        $res  = $client->post(env('TOLLGURU_BASE_URL'), ['json' => $payload]);
        $body = json_decode((string)$res->getBody(), true);
        $price = $body['routes'][0]['travelAdvisory']['tollInfo']['estimatedPrice'][0] ?? null;
        try {
            if ($price) {
                return (float)$price['units'] + ($price['nanos'] / 1e9);
            }
        } catch (\Throwable $e) {
            Log::error('GoogleTollHelper Error: ' . $e->getMessage());
        }

        return 0.0;
    }

    public static function messageNotFound($data, $message)
    {
        if (!$data) {
            Log::error($message);
        }
    }

    public static function formatMonth($month, $year)
    {
        return Carbon::createFromFormat(
            '!Y-m-d',
            "{$year}-{$month}-01"
        )
            ->locale(app()->getLocale())
            ->translatedFormat('F Y');
    }

    public static function float_format($number)
    {
        return str_replace(',', '.', $number);
    }
}
