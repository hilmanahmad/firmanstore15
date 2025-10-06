<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Log;
use App\Models\Item;
use App\Models\Customer;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\ItemHistory;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Dashboard extends Component
{
    public $startDate;
    public $endDate;
    public $filterType = '7days';
    public $chart_data = []; // ← TAMBAHKAN INI! Property tidak ada

    public function mount()
    {
        // Perbaikan: Berikan parameter default untuk setDateRange()
        $this->setDateRange($this->filterType);
    }

    public function render()
    {
        $data = $this->getDashboardData();

        // Update chart_data property dengan data fresh
        $this->chart_data = $this->getChartData();

        // Pastikan chart_data di-pass ke view
        $data['chart_data'] = $this->chart_data;

        \Log::info('Render called - Chart data updated:', [
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'chart_labels_count' => count($this->chart_data['labels'] ?? []),
            'chart_datasets_count' => count($this->chart_data['datasets'] ?? [])
        ]);

        return view('livewire.dashboard', $data + ['title' => 'Dashboard'])
            ->extends('layout.template')
            ->section('container');
    }

    // Perbaikan: Berikan default value untuk parameter $type
    public function setDateRange($type = '7days')
    {
        $this->filterType = $type;

        switch ($type) {
            case 'today':
                $this->startDate = Carbon::today()->toDateString();
                $this->endDate = Carbon::today()->toDateString();
                $filterName = 'Hari Ini';
                break;
            case '7days':
                $this->startDate = Carbon::today()->subDays(6)->toDateString();
                $this->endDate = Carbon::today()->toDateString();
                $filterName = '7 Hari Terakhir';
                break;
            case '30days':
                $this->startDate = Carbon::today()->subDays(29)->toDateString();
                $this->endDate = Carbon::today()->toDateString();
                $filterName = '30 Hari Terakhir';
                break;
            case 'thismonth':
                $this->startDate = Carbon::now()->startOfMonth()->toDateString();
                $this->endDate = Carbon::now()->endOfMonth()->toDateString();
                $filterName = 'Bulan Ini';
                break;
            default:
                $this->startDate = Carbon::today()->subDays(6)->toDateString();
                $this->endDate = Carbon::today()->toDateString();
                $filterName = '7 Hari Terakhir';
        }

        \Log::info('Filter periode changed:', [
            'type' => $type,
            'filter_name' => $filterName,
            'start_date' => $this->startDate,
            'end_date' => $this->endDate
        ]);

        // HAPUS refreshChartData() dari sini - akan membuat circular call
        // $this->refreshChartData(); ← HAPUS INI!

        // Emit events saja
        $this->dispatch('filterChanged', $filterName);
        $this->dispatch('chartDataUpdated');
    }

    public function getDashboardData()
    {
        // Total counts
        $totalItems = Item::count();
        $totalCustomers = Customer::count();
        $totalCategories = Category::count();
        $totalTransactions = Transaction::count();

        // Sales data for selected period
        $salesData = Transaction::with('detail')
            ->selectRaw('SUM(selling_price * qty) as total_sales')
            ->whereBetween(DB::raw('DATE(created_at)'), [$this->startDate, $this->endDate])
            ->first();

        $totalSales = $salesData->total_sales ?? 0;

        // Profit calculation
        $salesDataForProfit = Transaction::with('detail')
            ->whereBetween(DB::raw('DATE(created_at)'), [$this->startDate, $this->endDate])
            ->get();

        $totalProfit = $this->calculateTotalProfit($salesDataForProfit);

        // Low stock items
        $lowStockItems = $this->getLowStockItems();

        // Top selling items
        $topSellingItems = $this->getTopSellingItems();

        // Recent transactions
        $recentTransactions = Transaction::with(['customer', 'detail.item'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Chart data
        $chartData = $this->getChartData();

        return [
            'total_items' => $totalItems,
            'total_customers' => $totalCustomers,
            'total_categories' => $totalCategories,
            'total_transactions' => $totalTransactions,
            'total_sales' => $totalSales,
            'total_profit' => $totalProfit,
            'low_stock_items' => $lowStockItems,
            'top_selling_items' => $topSellingItems,
            'recent_transactions' => $recentTransactions,
            'chart_data' => $chartData,
            'start_date' => $this->startDate,
            'end_date' => $this->endDate
        ];
    }

    private function getLowStockItems()
    {
        return Item::orderBy('created_at', 'DESC')->get();
    }

    private function calculateTotalProfit($transactions)
    {
        $totalProfit = 0;
        foreach ($transactions as $transaction) {
            foreach ($transaction->detail as $detail) {
                // Pastikan itemHistory relation ada
                if ($detail->itemHistory) {
                    $profit = ($detail->selling_price - $detail->itemHistory->purchase_price) * $detail->qty;
                    $totalProfit += $profit;
                }
            }
        }
        return $totalProfit;
    }

    private function getTopSellingItems()
    {
        return DB::table('transaction_details')
            ->join('item_histories', 'transaction_details.item_history_id', '=', 'item_histories.id')
            ->join('items', 'item_histories.item_id', '=', 'items.id')
            ->select('items.name as item_name', DB::raw('SUM(transaction_details.qty) as total_sold'))
            ->groupBy('items.name')
            ->orderBy('total_sold', 'desc')
            ->limit(5)
            ->get();
    }

    private function getChartData()
    {
        try {
            // Ambil data penjualan harian dari transaction dengan total yang benar
            $dailySales = Transaction::selectRaw('DATE(created_at) as date, SUM(selling_price * qty) as total, type_id')
                ->whereBetween(DB::raw('DATE(created_at)'), [$this->startDate, $this->endDate])
                ->groupBy(DB::raw('DATE(created_at), type_id'))
                ->orderBy('date')
                ->get();

            // Buat range tanggal lengkap untuk memastikan semua tanggal ada
            $startDate = Carbon::today()->subDays(29)->toDateString();
            $endDate = Carbon::today()->toDateString();
            $dateRange = [];
            $current = Carbon::parse($startDate);

            while ($current <= Carbon::parse($endDate)) {
                $dateRange[] = $current->format('Y-m-d');
                $current->addDay();
            }

            // Inisialisasi arrays dengan ukuran yang sama
            $labels = [];
            $data1 = []; // type_id: d69a5c93-ec7b-4a08-bcb4-514402b29e5b (Dus)
            $data2 = []; // type_id: 384c6870-3e9c-4e99-be56-6617256c69c6 (Pcs)

            // Loop setiap tanggal dalam range
            foreach ($dateRange as $date) {
                // Format label tanggal
                $labels[] = Carbon::parse($date)->format('d M');

                // Cari data untuk type_id pertama pada tanggal ini
                $type1Data = $dailySales
                    ->where('date', $date)
                    ->where('type_id', 'd69a5c93-ec7b-4a08-bcb4-514402b29e5b')
                    ->first();

                $data1[] = $type1Data ? (float) $type1Data->total : 0;

                // Cari data untuk type_id kedua pada tanggal ini  
                $type2Data = $dailySales
                    ->where('date', $date)
                    ->where('type_id', '384c6870-3e9c-4e99-be56-6617256c69c6')
                    ->first();

                $data2[] = $type2Data ? (float) $type2Data->total : 0;
            }

            // Format data untuk chart dengan struktur datasets
            $chartData = [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => 'Penjualan Dus',
                        'data' => $data1,
                        'borderColor' => '#3b82f6',
                        'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                        'borderWidth' => 3,
                        'fill' => true,
                        'tension' => 0.4,
                        'pointBackgroundColor' => '#3b82f6',
                        'pointBorderColor' => '#ffffff',
                        'pointBorderWidth' => 2,
                        'pointRadius' => 6,
                        'pointHoverRadius' => 8
                    ],
                    [
                        'label' => 'Penjualan Pcs',
                        'data' => $data2,
                        'borderColor' => '#00a023',
                        'backgroundColor' => 'rgba(0, 160, 35, 0.1)',
                        'borderWidth' => 2,
                        'fill' => false,
                        'tension' => 0.2,
                        'pointBackgroundColor' => '#00a023',
                        'pointBorderColor' => '#ffffff',
                        'pointBorderWidth' => 2,
                        'pointRadius' => 4,
                        'pointHoverRadius' => 6,
                        'borderDash' => [5, 5]
                    ]
                ]
            ];

            // Log untuk debugging
            \Log::info('Chart Data Debug:', [
                'date_range' => $dateRange,
                'labels_count' => count($labels),
                'data1_count' => count($data1),
                'data2_count' => count($data2),
                'labels' => $labels,
                'data1' => $data1,
                'data2' => $data2,
                'raw_sales_data' => $dailySales->toArray()
            ]);

            // Verifikasi jumlah array sama
            if (count($labels) === count($data1) && count($labels) === count($data2)) {
                \Log::info('✅ Array counts match:', [
                    'labels' => count($labels),
                    'data1' => count($data1),
                    'data2' => count($data2)
                ]);
            } else {
                \Log::warning('❌ Array counts do not match:', [
                    'labels' => count($labels),
                    'data1' => count($data1),
                    'data2' => count($data2)
                ]);
            }

            return $chartData;
        } catch (\Exception $e) {
            Log::error('Error generating chart data:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Return fallback data dengan struktur yang benar
            return [
                'labels' => ['Error'],
                'datasets' => [
                    [
                        'label' => 'Penjualan Dus',
                        'data' => [0],
                        'borderColor' => '#3b82f6',
                        'backgroundColor' => 'rgba(59, 130, 246, 0.1)'
                    ],
                    [
                        'label' => 'Penjualan Pcs',
                        'data' => [0],
                        'borderColor' => '#00a023',
                        'backgroundColor' => 'rgba(0, 160, 35, 0.1)'
                    ]
                ]
            ];
        }
    }

    public function changeData()
    {
        \Log::info('Manual data refresh triggered');

        // Force refresh component - JANGAN panggil mount()!
        // $this->mount(); ← HAPUS INI, akan reset filterType

        // Langsung refresh chart data
        $this->refreshChartData();

        // Emit events
        $this->dispatch('chartDataUpdated');
        $this->dispatch('dashboardDataUpdated');
    }

    // Method baru untuk refresh chart
    public function refreshChart()
    {
        \Log::info('Chart refresh triggered manually');

        // Refresh chart data
        $this->refreshChartData();

        // Emit event
        $this->dispatch('chartDataUpdated');
    }

    public function refreshChartData()
    {
        \Log::info('Refreshing chart data...', [
            'current_dates' => [$this->startDate, $this->endDate]
        ]);

        // Update property chart_data
        $this->chart_data = $this->getChartData();

        \Log::info('Chart data refreshed:', [
            'labels_count' => count($this->chart_data['labels'] ?? []),
            'datasets_count' => count($this->chart_data['datasets'] ?? []),
            'first_dataset_data_count' => count($this->chart_data['datasets'][0]['data'] ?? [])
        ]);

        return $this->chart_data;
    }
}
