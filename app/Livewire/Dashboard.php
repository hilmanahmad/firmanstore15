<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Log;
use App\Models\Item;
use App\Models\Customer;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\ItemHistory; // Tambahkan import ItemHistory
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Dashboard extends Component
{
    public $startDate;
    public $endDate;
    public $filterType = '7days';

    public function mount()
    {
        $this->setDateRange();
    }

    public function render()
    {
        $data = $this->getDashboardData();

        return view('livewire.dashboard', $data + ['title' => 'Dashboard'])
            ->extends('layout.template')
            ->section('container');
    }

    public function setDateRange($type = '7days')
    {
        $this->filterType = $type;

        switch ($type) {
            case 'today':
                $this->startDate = Carbon::today()->toDateString();
                $this->endDate = Carbon::today()->toDateString();
                break;
            case '7days':
                $this->startDate = Carbon::today()->subDays(6)->toDateString();
                $this->endDate = Carbon::today()->toDateString();
                break;
            case '30days':
                $this->startDate = Carbon::today()->subDays(29)->toDateString();
                $this->endDate = Carbon::today()->toDateString();
                break;
            case 'thismonth':
                $this->startDate = Carbon::now()->startOfMonth()->toDateString();
                $this->endDate = Carbon::now()->endOfMonth()->toDateString();
                break;
            default:
                $this->startDate = Carbon::today()->subDays(29)->toDateString();
                $this->endDate = Carbon::today()->toDateString();
        }

        // Log untuk debug
        \Log::info('Filter periode changed:', [
            'type' => $type,
            'start_date' => $this->startDate,
            'end_date' => $this->endDate
        ]);

        // Emit event untuk refresh chart
        $this->dispatch('dashboardDataUpdated');
    }

    public function getDashboardData()
    {
        // Total counts
        $totalItems = Item::count();
        $totalCustomers = Customer::count();
        $totalCategories = Category::count();
        $totalTransactions = Transaction::count();

        // Sales data for selected period
        $salesData = Transaction::with('detail')->selectRaw('selling_price * qty as qty')
            ->whereBetween(DB::raw('DATE(created_at)'), [$this->startDate, $this->endDate])
            ->get();

        $totalSales = $salesData->sum('qty');
        $salesData = Transaction::with('detail')
            ->whereBetween(DB::raw('DATE(created_at)'), [$this->startDate, $this->endDate])
            ->get();
        $totalProfit = $this->calculateTotalProfit($salesData);

        // Low stock items menggunakan ItemHistory dengan field qty
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
            $dailySales = Transaction::selectRaw('DATE(created_at) as date, SUM(selling_price) as total')
                ->whereBetween(DB::raw('DATE(created_at)'), [$this->startDate, $this->endDate])
                ->groupBy(DB::raw('DATE(created_at)'))
                ->orderBy('date')
                ->get();

            // Format data untuk chart
            $chartData = [
                'labels' => $dailySales->pluck('date')->map(function ($date) {
                    return Carbon::parse($date)->format('d M');
                })->toArray(),
                'data' => $dailySales->pluck('total')->map(function ($total) {
                    return (float) ($total ?? 0);
                })->toArray()
            ];
            return $chartData;
        } catch (\Exception $e) {
            Log::error('Error generating chart data:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Return fallback data
            return [
                'labels' => ['Error'],
                'data' => [0]
            ];
        }
    }

    public function changeData()
    {
        \Log::info('Refresh data triggeresd');

        // Force refresh component data
        $this->mount();

        // Emit event untuk refresh chart
        $this->dispatch('dashboardDataUpdated');
    }
}
