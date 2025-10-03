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
                            <h4><b>{{ $total_categories }}</b></h4>
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
                        <div>
                            <button onclick="renderChart()" class="btn btn-sm btn-primary">🔄 Refresh Chart</button>
                            <button onclick="testChart()" class="btn btn-sm btn-info">🧪 Test Chart</button>
                        </div>
                    </div>
                    <div class="iq-card-body" wire:ignore>
                        <!-- Debug info -->
                        <div id="chartDebug" style="font-size: 12px; color: #666; margin-bottom: 10px;">
                            Status: Loading...
                        </div>

                        <!-- Canvas dengan wrapper -->
                        <div style="position: relative; height: 400px; width: 100%;">
                            <canvas id="salesChart"></canvas>
                        </div>
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
<!-- Tambahkan di layout atau head -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

<!-- Di body, setelah canvas -->
<script>
    let salesChart = null;

    function updateDebug(message) {
        const debugEl = document.getElementById('chartDebug');
        if (debugEl) {
            debugEl.innerHTML = message;
        }
        console.log('Chart Debug:', message);
    }

    async function renderChart() {
        updateDebug('🔄 Memulai render chart...');

        try {
            // Get chart data from Laravel
            const chartData = @json($chart_data ?? ['labels' => ['No Data'], 'data' => [0]]);
            updateDebug('📊 Data chart diterima: ' + JSON.stringify(chartData));

            const ctx = document.getElementById('salesChart');
            if (!ctx) {
                updateDebug('❌ Canvas element tidak ditemukan!');
                return;
            }

            if (typeof Chart === 'undefined') {
                updateDebug('❌ Chart.js tidak tersedia!');
                return;
            }

            // Destroy existing chart
            if (salesChart) {
                salesChart.destroy();
                updateDebug('🗑️ Chart sebelumnya dihapus');
            }

            // Create line chart with enhanced styling
            salesChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: chartData.labels || ['No Data'],
                    datasets: [{
                        label: 'Penjualan Harian (Rp)',
                        data: chartData.data || [0],
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
                        pointHoverBorderColor: '#ffffff',
                        pointHoverBorderWidth: 3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    plugins: {
                        title: {
                            display: true,
                            text: 'Trend Penjualan Harian',
                            font: {
                                size: 18,
                                weight: 'bold',
                                family: 'Inter, sans-serif'
                            },
                            color: '#1f2937',
                            padding: {
                                top: 10,
                                bottom: 20
                            }
                        },
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                font: {
                                    size: 14,
                                    family: 'Inter, sans-serif'
                                },
                                color: '#374151',
                                usePointStyle: true,
                                pointStyle: 'circle'
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(17, 24, 39, 0.95)',
                            titleColor: '#ffffff',
                            bodyColor: '#ffffff',
                            borderColor: '#3b82f6',
                            borderWidth: 2,
                            cornerRadius: 8,
                            displayColors: false,
                            titleFont: {
                                size: 14,
                                weight: 'bold'
                            },
                            bodyFont: {
                                size: 13
                            },
                            callbacks: {
                                title: function(context) {
                                    return '📅 Tanggal: ' + context[0].label;
                                },
                                label: function(context) {
                                    const value = context.parsed.y ?? 0;
                                    return '💰 Penjualan: Rp ' + new Intl.NumberFormat('id-ID').format(
                                        value);
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Tanggal',
                                font: {
                                    size: 14,
                                    weight: '600',
                                    family: 'Inter, sans-serif'
                                },
                                color: '#374151'
                            },
                            grid: {
                                display: true,
                                color: 'rgba(156, 163, 175, 0.3)',
                                borderDash: [2, 4]
                            },
                            ticks: {
                                font: {
                                    size: 12,
                                    family: 'Inter, sans-serif'
                                },
                                color: '#6b7280'
                            }
                        },
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Penjualan (Rupiah)',
                                font: {
                                    size: 14,
                                    weight: '600',
                                    family: 'Inter, sans-serif'
                                },
                                color: '#374151'
                            },
                            grid: {
                                display: true,
                                color: 'rgba(156, 163, 175, 0.3)',
                                borderDash: [2, 4]
                            },
                            ticks: {
                                font: {
                                    size: 12,
                                    family: 'Inter, sans-serif'
                                },
                                color: '#6b7280',
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
                    elements: {
                        line: {
                            borderCapStyle: 'round',
                            borderJoinStyle: 'round'
                        }
                    },
                    animation: {
                        duration: 1500,
                        easing: 'easeInOutQuart'
                    }
                }
            });

            updateDebug('✅ Line chart berhasil dibuat! (' + (chartData.labels?.length || 0) + ' data points)');

        } catch (error) {
            updateDebug('❌ Error saat membuat chart: ' + error.message);
            console.error('Chart creation error:', error);
        }
    }

    // Initialize chart on different events
    document.addEventListener('DOMContentLoaded', function() {
        updateDebug('🚀 DOM ready - akan render chart...');
        setTimeout(renderChart, 300);
    });

    document.addEventListener('livewire:navigated', function() {
        updateDebug('📍 Livewire navigated');
        setTimeout(renderChart, 400);
    });

    document.addEventListener('livewire:updated', function() {
        updateDebug('🔄 Livewire updated - refresh chart');
        setTimeout(renderChart, 500);
    });

    // Listen for Livewire events
    if (typeof Livewire !== 'undefined') {
        Livewire.on('dashboardDataUpdated', () => {
            updateDebug('📊 Dashboard data updated');
            setTimeout(renderChart, 300);
        });
    }

    // Test chart function with sample data
    function testChart() {
        updateDebug('🧪 Membuat test chart dengan data sample...');

        const ctx = document.getElementById('salesChart');
        if (!ctx) {
            updateDebug('❌ Canvas element tidak ditemukan!');
            return;
        }

        if (typeof Chart === 'undefined') {
            updateDebug('❌ Chart.js tidak tersedia!');
            return;
        }

        // Destroy existing chart
        if (salesChart) {
            salesChart.destroy();
        }

        // Sample data for testing
        const sampleData = {
            labels: ['1 Okt', '2 Okt', '3 Okt', '4 Okt', '5 Okt', '6 Okt', '7 Okt'],
            data: [150000, 230000, 180000, 320000, 290000, 410000, 350000]
        };

        salesChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: sampleData.labels,
                datasets: [{
                    label: 'Sample Penjualan (Rp)',
                    data: sampleData.data,
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#10b981',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 6,
                    pointHoverRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                plugins: {
                    title: {
                        display: true,
                        text: '📊 Test Line Chart - Sample Data',
                        font: {
                            size: 16,
                            weight: 'bold'
                        },
                        color: '#059669'
                    },
                    legend: {
                        display: true,
                        position: 'top'
                    },
                    tooltip: {
                        backgroundColor: 'rgba(17, 24, 39, 0.9)',
                        titleColor: '#ffffff',
                        bodyColor: '#ffffff',
                        borderColor: '#10b981',
                        borderWidth: 1,
                        cornerRadius: 8,
                        displayColors: false,
                        callbacks: {
                            title: function(context) {
                                return '📅 ' + context[0].label;
                            },
                            label: function(context) {
                                const value = context.parsed.y;
                                return '💰 Rp ' + new Intl.NumberFormat('id-ID').format(value);
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
                            color: 'rgba(156, 163, 175, 0.3)'
                        },
                        ticks: {
                            callback: function(value) {
                                if (value >= 1000000) return 'Rp ' + (value / 1000000).toFixed(1) + 'M';
                                else if (value >= 1000) return 'Rp ' + (value / 1000).toFixed(0) + 'K';
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

        updateDebug('✅ Test chart berhasil dibuat dengan sample data!');
    }

    // Fallback initialization
    setTimeout(() => {
        if (!salesChart && typeof Chart !== 'undefined') {
            updateDebug('⏰ Fallback chart initialization');
            renderChart();
        }
    }, 2000);
</script>
