<div id="content-page" class="content-page">
    <div class="container-fluid">
        <!-- Stats Cards -->
        <div class="row">
            <div class="col-sm-6 col-md-6 col-lg-3">
                <div class="iq-card iq-card-block iq-card-stretch iq-card-height">
                    <div class="iq-card-body iq-box-relative">
                        <div class="iq-box-absolute icon iq-icon-box rounded-circle iq-bg-primary">
                            <i class="ri-product-hunt-line"></i>
                        </div>
                        <p class="text-secondary">Total Produk</p>
                        <div class="d-flex align-items-center justify-content-between">
                            <h4><b>{{ number_format($total_items) }}</b></h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-6 col-lg-3">
                <div class="iq-card iq-card-block iq-card-stretch iq-card-height">
                    <div class="iq-card-body iq-box-relative">
                        <div class="iq-box-absolute icon iq-icon-box rounded-circle iq-bg-success">
                            <i class="ri-user-line"></i>
                        </div>
                        <p class="text-secondary">Total Pelanggan</p>
                        <div class="d-flex align-items-center justify-content-between">
                            <h4><b>{{ number_format($total_customers) }}</b></h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-6 col-lg-3">
                <div class="iq-card iq-card-block iq-card-stretch iq-card-height">
                    <div class="iq-card-body iq-box-relative">
                        <div class="iq-box-absolute icon iq-icon-box rounded-circle iq-bg-warning">
                            <i class="ri-shopping-cart-line"></i>
                        </div>
                        <p class="text-secondary">Total Transaksi</p>
                        <div class="d-flex align-items-center justify-content-between">
                            <h4><b>{{ number_format($total_transactions) }}</b></h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-6 col-lg-3">
                <div class="iq-card iq-card-block iq-card-stretch iq-card-height">
                    <div class="iq-card-body iq-box-relative">
                        <div class="iq-box-absolute icon iq-icon-box rounded-circle iq-bg-info">
                            <i class="ri-folder-line"></i>
                        </div>
                        <p class="text-secondary">Total Kategori</p>
                        <div class="d-flex align-items-center justify-content-between">
                            <h4><b>{{ number_format($total_categories) }}</b></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sales Summary Cards -->
        <div class="row">
            <div class="col-sm-6">
                <div class="iq-card iq-card-block iq-card-stretch iq-card-height">
                    <div class="iq-card-body iq-box-relative text-center">
                        <div class="iq-box-absolute icon iq-icon-box rounded-circle iq-bg-success">
                            <i class="ri-money-dollar-circle-line text-warning"></i>
                        </div>
                        <h6 class="text-secondary">Total Penjualan</h6>
                        <div class="d-flex align-items-center justify-content-center">
                            <h3 class="text-success"><b>Rp {{ number_format($total_sales, 0, ',', '.') }}</b></h3>
                        </div>
                        <small class="text-muted">{{ Carbon\Carbon::parse($start_date)->format('d M') }} -
                            {{ Carbon\Carbon::parse($end_date)->format('d M Y') }}</small>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="iq-card iq-card-block iq-card-stretch iq-card-height">
                    <div class="iq-card-body iq-box-relative text-center">
                        <div class="iq-box-absolute icon iq-icon-box rounded-circle iq-bg-primary">
                            <i class="ri-money-dollar-circle-line text-primary"></i>
                        </div>
                        <h6 class="text-secondary">Total Keuntungan</h6>
                        <div class="d-flex align-items-center justify-content-center">
                            <h3 class="text-primary"><b>Rp {{ number_format($total_profit, 0, ',', '.') }}</b></h3>
                        </div>
                        <small class="text-muted">{{ Carbon\Carbon::parse($start_date)->format('d M') }} -
                            {{ Carbon\Carbon::parse($end_date)->format('d M Y') }}</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="iq-card">
            <div class="iq-card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h5 class="mb-3">Filter Periode</h5>
                        <div class="btn-group" role="group">
                            <button type="button"
                                class="btn btn-outline-primary {{ $filterType == 'today' ? 'active' : '' }}"
                                wire:click="setDateRange('today')">Hari Ini</button>
                            <button type="button"
                                class="btn btn-outline-primary {{ $filterType == '7days' ? 'active' : '' }}"
                                wire:click="setDateRange('7days')">7 Hari</button>
                            <button type="button"
                                class="btn btn-outline-primary {{ $filterType == '30days' ? 'active' : '' }}"
                                wire:click="setDateRange('30days')">30 Hari</button>
                            <button type="button"
                                class="btn btn-outline-primary {{ $filterType == 'thismonth' ? 'active' : '' }}"
                                wire:click="setDateRange('thismonth')">Bulan Ini</button>
                        </div>
                    </div>
                    <div class="col-md-4 text-right">
                        <button class="btn btn-info" wire:click="changeData">
                            <i class="ri-refresh-line"></i> Refresh Data
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts and Tables Row -->
        <div class="row">
            <div class="col-md-8">
                <div class="iq-card">
                    <div class="iq-card-header d-flex justify-content-between">
                        <div class="iq-header-title">
                            <h4 class="card-title">Grafik Penjualan Harian</h4>
                        </div>
                    </div>
                    <div class="iq-card-body" wire:ignore>
                        <canvas id="salesChart" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>

            <!-- Top Selling Items -->
            <div class="col-md-4">
                <div class="iq-card">
                    <div class="iq-card-header d-flex justify-content-between">
                        <div class="iq-header-title">
                            <h4 class="card-title">Produk Terlaris</h4>
                        </div>
                    </div>
                    <div class="iq-card-body">
                        @forelse($top_selling_items as $index => $item)
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="d-flex align-items-center">
                                    <span class="badge badge-primary mr-2">{{ $index + 1 }}</span>
                                    <div>
                                        <h6 class="mb-0">{{ $item->item_name }}</h6>
                                        <small class="text-muted">{{ $item->total_sold }} terjual</small>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted text-center">Belum ada data penjualan</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Low Stock and Recent Transactions -->
        <div class="row">
            <!-- Low Stock Items -->
            <div class="col-md-6">
                <div class="iq-card">
                    <div class="iq-card-header d-flex justify-content-between">
                        <div class="iq-header-title">
                            <h4 class="card-title">Stok Menipis</h4>
                        </div>
                    </div>
                    <div class="iq-card-body">
                        @forelse($low_stock_items as $item)
                            @php
                                $qty_dus = isset($item->itemHistory)
                                    ? $item->itemHistory
                                        ->where('type_id', 'd69a5c93-ec7b-4a08-bcb4-514402b29e5b')
                                        ->sum(function ($item) {
                                            return $item->qty - $item->qty_sold;
                                        })
                                    : 0;
                                $qty_pcs = isset($item->itemHistory)
                                    ? $item->itemHistory
                                        ->where('type_id', '384c6870-3e9c-4e99-be56-6617256c69c6')
                                        ->sum(function ($item) {
                                            return $item->qty - $item->qty_sold;
                                        })
                                    : 0;
                            @endphp
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div>
                                    <h6 class="mb-0">{{ $item->name }}</h6>
                                    <small class="text-muted">{{ $item->category->name ?? 'Tanpa Kategori' }}</small>
                                </div>
                                <div>
                                    <span class="badge badge-{{ $qty_dus <= 5 ? 'danger' : 'warning' }}">
                                        {{ $qty_dus }} Dus
                                    </span>
                                    <span class="badge badge-{{ $qty_pcs <= 5 ? 'danger' : 'warning' }}">
                                        {{ $qty_pcs }} Pcs
                                    </span>
                                </div>
                            </div>
                        @empty
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Recent Transactions -->
            <div class="col-md-6">
                <div class="iq-card">
                    <div class="iq-card-header d-flex justify-content-between">
                        <div class="iq-header-title">
                            <h4 class="card-title">Transaksi Terbaru</h4>
                        </div>
                    </div>
                    <div class="iq-card-body">
                        @forelse($recent_transactions as $transaction)
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div>
                                    <h6 class="mb-0">{{ $transaction->customer->name }}</h6>
                                    <small class="text-muted">
                                        {{ $transaction->created_at->format('d M Y H:i') }}
                                    </small>
                                </div>
                                <div class="text-right">
                                    <strong class="text-success">Rp
                                        {{ number_format($transaction->selling_price * $transaction->qty, 0, ',', '.') }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $transaction->qty }}
                                        {{ $transaction->type->name }}</small>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted text-center">Belum ada transaksi</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <!-- Script yang sama seperti di atas -->
    <script>
        let salesChart = null;

        function initSalesChart() {
            const ctx = document.getElementById('salesChart');
            if (!ctx) {
                console.log('Canvas element not found');
                return;
            }

            // Destroy existing chart if exists
            if (salesChart) {
                salesChart.destroy();
            }

            const chartData = @json($chart_data ?? ['labels' => [], 'data' => []]);
            console.log('Chart data:', chartData); // Debug log

            // Check if data exists
            if (!chartData.labels || chartData.labels.length === 0) {
                ctx.getContext('2d').clearRect(0, 0, ctx.width, ctx.height);
                const context = ctx.getContext('2d');
                context.font = '16px Arial';
                context.fillStyle = '#666';
                context.textAlign = 'center';
                context.fillText('Tidak ada data untuk ditampilkan', ctx.width / 2, ctx.height / 2);
                return;
            }

            salesChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: chartData.labels,
                    datasets: [{
                        label: 'Penjualan Harian (Rp)',
                        data: chartData.data,
                        borderColor: '#667eea',
                        backgroundColor: 'rgba(102, 126, 234, 0.1)',
                        borderWidth: 3,
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#667eea',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 6,
                        pointHoverRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Trend Penjualan Harian',
                            font: {
                                size: 16,
                                weight: 'bold'
                            },
                            color: '#333'
                        },
                        legend: {
                            display: true,
                            position: 'top'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                                }
                            },
                            grid: {
                                color: 'rgba(0, 0, 0, 0.1)'
                            }
                        },
                        x: {
                            grid: {
                                color: 'rgba(0, 0, 0, 0.1)'
                            }
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    },
                    elements: {
                        point: {
                            hoverRadius: 8
                        }
                    }
                }
            });
        }

        // Initialize chart when page loads
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded, initializing chart...');
            setTimeout(() => {
                initSalesChart();
            }, 100);
        });

        // Initialize chart for Livewire navigation
        document.addEventListener('livewire:navigated', function() {
            console.log('Livewire navigated, initializing chart...');
            setTimeout(() => {
                initSalesChart();
            }, 100);
        });

        // Listen for Livewire updates
        document.addEventListener('livewire:updated', function() {
            console.log('Livewire updated, reinitializing chart...');
            setTimeout(() => {
                initSalesChart();
            }, 200);
        });

        // Alternative event listener for older Livewire versions
        if (typeof Livewire !== 'undefined') {
            Livewire.on('dashboardDataUpdated', () => {
                console.log('Dashboard data updated, reinitializing chart...');
                setTimeout(() => {
                    initSalesChart();
                }, 200);
            });
        }
    </script>
@endpush
