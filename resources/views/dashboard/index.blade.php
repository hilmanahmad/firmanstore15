@extends('layout.template')

@section('container')
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
                                <h4><b>{{ number_format($totalItems) }}</b></h4>
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
                                <h4><b>{{ number_format($totalCustomers) }}</b></h4>
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
                                <h4><b>{{ number_format($totalTransactions) }}</b></h4>
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
                                <h4><b>{{ $totalCategories }}</b></h4>
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
                                <h3 class="text-success"><b>Rp {{ number_format($totalSales, 0, ',', '.') }}</b></h3>
                            </div>
                            <small class="text-muted">{{ Carbon\Carbon::parse($startDate)->format('d M') }} -
                                {{ Carbon\Carbon::parse($endDate)->format('d M Y') }}</small>
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
                                <h3 class="text-primary"><b>Rp {{ number_format($totalProfit, 0, ',', '.') }}</b></h3>
                            </div>
                            <small class="text-muted">{{ Carbon\Carbon::parse($startDate)->format('d M') }} -
                                {{ Carbon\Carbon::parse($endDate)->format('d M Y') }}</small>
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
                                    onclick="changeFilter('today')">Hari Ini</button>
                                <button type="button"
                                    class="btn btn-outline-primary {{ $filterType == '7days' ? 'active' : '' }}"
                                    onclick="changeFilter('7days')">7 Hari</button>
                                <button type="button"
                                    class="btn btn-outline-primary {{ $filterType == '30days' ? 'active' : '' }}"
                                    onclick="changeFilter('30days')">30 Hari</button>
                                <button type="button"
                                    class="btn btn-outline-primary {{ $filterType == 'thismonth' ? 'active' : '' }}"
                                    onclick="changeFilter('thismonth')">Bulan Ini</button>
                            </div>
                        </div>
                        <div class="col-md-4 text-right">
                            <a href="{{ request()->fullUrl() }}" class="btn btn-info">
                                <i class="ri-refresh-line"></i> Refresh Data
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Line Chart Section -->
            <div class="row">
                <div class="col-md-12">
                    <div class="iq-card">
                        <div class="iq-card-header d-flex justify-content-between">
                            <div class="iq-header-title">
                                <h4 class="card-title">📈 Grafik Penjualan Harian (Line Chart)</h4>
                            </div>
                            <div>
                                <button onclick="initSalesChart()" class="btn btn-sm btn-primary">🔄 Refresh Chart</button>
                            </div>
                        </div>
                        <div class="iq-card-body">
                            <!-- Chart Status -->
                            <div id="chart-status" class="alert alert-info">
                                <small>Status: Memuat chart...</small>
                            </div>

                            <!-- Chart Canvas -->
                            <div style="position: relative; height: 400px; width: 100%;">
                                <canvas id="salesLineChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts and Tables Row -->
            <div class="row">
                <div class="col-md-8">
                    <!-- Line Chart sudah ada di atas -->
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
                            @forelse($topSellingItems as $index => $item)
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
                            @forelse($lowStockItems as $item)
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
                                <p class="text-muted text-center">Semua stok aman</p>
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
                            @forelse($recentTransactions as $transaction)
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div>
                                        <h6 class="mb-0">{{ $transaction->customer->name ?? 'N/A' }}</h6>
                                        <small class="text-muted">
                                            {{ $transaction->created_at->format('d M Y H:i') }}
                                        </small>
                                    </div>
                                    <div class="text-right">
                                        <strong class="text-success">Rp
                                            {{ number_format($transaction->total, 0, ',', '.') }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $transaction->detail->sum('qty') ?? 0 }} item</small>
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

    <!-- Chart.js Library -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        let salesChart = null;

        // Chart data from Laravel
        const chartData = @json($chartData);

        function updateStatus(message) {
            const statusEl = document.getElementById('chart-status');
            if (statusEl) {
                statusEl.innerHTML = '<small>' + message + '</small>';
                statusEl.className = 'alert alert-info';
            }
            console.log('Chart Status:', message);
        }

        function initSalesChart() {
            updateStatus('🔄 Membuat line chart...');

            const ctx = document.getElementById('salesLineChart');
            if (!ctx) {
                updateStatus('❌ Canvas element tidak ditemukan!');
                return;
            }

            if (typeof Chart === 'undefined') {
                updateStatus('❌ Chart.js tidak dimuat!');
                return;
            }

            // Destroy existing chart
            if (salesChart) {
                salesChart.destroy();
            }

            try {
                salesChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: chartData.labels,
                        datasets: [{
                            label: 'Penjualan Harian (Rp)',
                            data: chartData.data,
                            borderColor: '#3b82f6',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: '#3b82f6',
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 2,
                            pointRadius: 6,
                            pointHoverRadius: 8,
                            pointHoverBackgroundColor: '#1d4ed8',
                            pointHoverBorderColor: '#ffffff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            intersect: false,
                            mode: 'index'
                        },
                        plugins: {
                            title: {
                                display: true,
                                text: 'Trend Penjualan Harian',
                                font: {
                                    size: 18,
                                    weight: 'bold'
                                },
                                color: '#1f2937'
                            },
                            legend: {
                                display: true,
                                position: 'top'
                            },
                            tooltip: {
                                backgroundColor: 'rgba(17, 24, 39, 0.9)',
                                titleColor: '#ffffff',
                                bodyColor: '#ffffff',
                                borderColor: '#3b82f6',
                                borderWidth: 1,
                                cornerRadius: 8,
                                displayColors: false,
                                callbacks: {
                                    title: function(context) {
                                        return 'Tanggal: ' + context[0].label;
                                    },
                                    label: function(context) {
                                        const value = context.parsed.y;
                                        return 'Penjualan: Rp ' + new Intl.NumberFormat('id-ID').format(value);
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                title: {
                                    display: true,
                                    text: 'Tanggal',
                                    color: '#374151'
                                },
                                grid: {
                                    display: true,
                                    color: 'rgba(156, 163, 175, 0.3)'
                                }
                            },
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Penjualan (Rupiah)',
                                    color: '#374151'
                                },
                                grid: {
                                    display: true,
                                    color: 'rgba(156, 163, 175, 0.3)'
                                },
                                ticks: {
                                    callback: function(value) {
                                        if (value >= 1000000) {
                                            return 'Rp ' + (value / 1000000).toFixed(1) + 'M';
                                        } else if (value >= 1000) {
                                            return 'Rp ' + (value / 1000).toFixed(0) + 'K';
                                        }
                                        return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                                    }
                                }
                            }
                        },
                        animation: {
                            duration: 1500,
                            easing: 'easeInOutQuart'
                        }
                    }
                });

                updateStatus('✅ Line chart berhasil dibuat! (' + chartData.labels.length + ' data points)');
            } catch (error) {
                updateStatus('❌ Error: ' + error.message);
                console.error('Chart creation error:', error);
            }
        }

        // Filter change handler
        function changeFilter(filterType) {
            updateStatus('🔄 Memuat data ' + filterType + '...');

            // Update active button
            document.querySelectorAll('.btn-group .btn').forEach(btn => {
                btn.classList.remove('active');
            });
            event.target.classList.add('active');

            // Fetch new data via AJAX
            fetch(`/dashboard/update-chart?filter=${filterType}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update global chartData
                        window.chartData = data.chartData;

                        // Recreate chart with new data
                        if (salesChart) {
                            salesChart.destroy();
                        }

                        // Update chart data
                        const ctx = document.getElementById('salesLineChart');
                        salesChart = new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: data.chartData.labels,
                                datasets: [{
                                    label: 'Penjualan Harian (Rp)',
                                    data: data.chartData.data,
                                    borderColor: '#3b82f6',
                                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                    borderWidth: 3,
                                    fill: true,
                                    tension: 0.4,
                                    pointBackgroundColor: '#3b82f6',
                                    pointBorderColor: '#ffffff',
                                    pointBorderWidth: 2,
                                    pointRadius: 6,
                                    pointHoverRadius: 8,
                                    pointHoverBackgroundColor: '#1d4ed8',
                                    pointHoverBorderColor: '#ffffff'
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                interaction: {
                                    intersect: false,
                                    mode: 'index'
                                },
                                plugins: {
                                    title: {
                                        display: true,
                                        text: 'Trend Penjualan ' + data.period.toUpperCase(),
                                        font: {
                                            size: 18,
                                            weight: 'bold'
                                        },
                                        color: '#1f2937'
                                    },
                                    legend: {
                                        display: true,
                                        position: 'top'
                                    },
                                    tooltip: {
                                        backgroundColor: 'rgba(17, 24, 39, 0.9)',
                                        titleColor: '#ffffff',
                                        bodyColor: '#ffffff',
                                        borderColor: '#3b82f6',
                                        borderWidth: 1,
                                        cornerRadius: 8,
                                        displayColors: false,
                                        callbacks: {
                                            title: function(context) {
                                                return 'Tanggal: ' + context[0].label;
                                            },
                                            label: function(context) {
                                                const value = context.parsed.y;
                                                return 'Penjualan: Rp ' + new Intl.NumberFormat('id-ID')
                                                    .format(value);
                                            }
                                        }
                                    }
                                },
                                scales: {
                                    x: {
                                        title: {
                                            display: true,
                                            text: 'Tanggal',
                                            color: '#374151'
                                        },
                                        grid: {
                                            display: true,
                                            color: 'rgba(156, 163, 175, 0.3)'
                                        }
                                    },
                                    y: {
                                        beginAtZero: true,
                                        title: {
                                            display: true,
                                            text: 'Penjualan (Rupiah)',
                                            color: '#374151'
                                        },
                                        grid: {
                                            display: true,
                                            color: 'rgba(156, 163, 175, 0.3)'
                                        },
                                        ticks: {
                                            callback: function(value) {
                                                if (value >= 1000000) return 'Rp ' + (value / 1000000)
                                                    .toFixed(1) + 'M';
                                                else if (value >= 1000) return 'Rp ' + (value / 1000)
                                                    .toFixed(0) + 'K';
                                                return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                                            }
                                        }
                                    }
                                },
                                animation: {
                                    duration: 1000,
                                    easing: 'easeInOutQuart'
                                }
                            }
                        });

                        updateStatus(
                            `✅ Chart ${data.period} berhasil diperbarui! (${data.dateRange.start} - ${data.dateRange.end})`
                            );
                    } else {
                        updateStatus('❌ Gagal memperbarui data');
                    }
                })
                .catch(error => {
                    updateStatus('❌ Error: ' + error.message);
                    console.error('Fetch error:', error);
                });
        }

        // Initialize chart when page loads
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded, Chart.js available:', typeof Chart !== 'undefined');
            console.log('Chart data:', chartData);

            setTimeout(() => {
                initSalesChart();
            }, 500);
        });
    </script>
@endsection
