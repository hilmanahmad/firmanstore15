@extends('layout.template2')
<div id="content-page" class="content-page">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="iq-card">
                    <div class="iq-card-header d-flex justify-content-between">
                        <div class="iq-header-title">
                            <h4 class="card-title">
                                <i class="fa fa-coins text-warning"></i> {{ $title }}
                            </h4>
                        </div>
                        <div class="iq-card-header-toolbar d-flex align-items-center">
                            <span class="badge badge-info mr-2">
                                <i class="fa fa-hourglass-half mr-1"></i>Next: <span id="countdown">-</span>
                            </span>
                            <span class="badge badge-secondary" id="last-update">-</span>
                            <button class="btn btn-sm btn-outline-primary ml-2" onclick="fetchGoldRate()"
                                title="Refresh">
                                <i class="fa fa-refresh" id="refresh-icon"></i>
                            </button>
                        </div>
                    </div>
                    <div class="iq-card-body">
                        <div class="row">
                            <!-- Buying Rate Card -->
                            <div class="col-md-6 mb-4">
                                <div class="card bg-gradient-success text-white h-100">
                                    <div class="card-body text-center">
                                        <div class="mb-3">
                                            <i class="fa fa-arrow-circle-down fa-3x"></i>
                                        </div>
                                        <h5 class="card-title text-white">Harga Beli</h5>
                                        <h2 class="display-4 font-weight-bold" id="buying-rate">
                                            <span class="spinner-border spinner-border-sm" role="status"></span>
                                        </h2>
                                        <p class="mb-0">per gram</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Selling Rate Card -->
                            <div class="col-md-6 mb-4">
                                <div class="card bg-gradient-danger text-white h-100">
                                    <div class="card-body text-center">
                                        <div class="mb-3">
                                            <i class="fa fa-arrow-circle-up fa-3x"></i>
                                        </div>
                                        <h5 class="card-title text-white">Harga Jual</h5>
                                        <h2 class="display-4 font-weight-bold" id="selling-rate">
                                            <span class="spinner-border spinner-border-sm" role="status"></span>
                                        </h2>
                                        <p class="mb-0">per gram</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- 
                        <!-- Spread Info -->
                        <div class="row">
                            <div class="col-12">
                                <div class="alert alert-info d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="fa fa-info-circle mr-2"></i>
                                        <strong>Spread:</strong> <span id="spread-amount">-</span>
                                    </div>
                                    <div>
                                        <small class="text-muted">
                                            <i class="fa fa-clock mr-1"></i> Auto refresh setiap 1 menit
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div> --}}

                        <!-- Rate History -->
                        <div class="row mt-3">
                            <div class="col-12">
                                <h5><i class="fa fa-history mr-2"></i>Riwayat Rate (Sesi Ini)</h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped table-sm" id="rate-history">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th class="text-center" width="50">#</th>
                                                <th class="text-center">Waktu</th>
                                                <th class="text-right">Harga Beli</th>
                                                <th class="text-right">Harga Jual</th>
                                                <th class="text-right">Est. cuan 20 JT</th>
                                                <th class="text-right">Est. cuan 30 JT</th>
                                            </tr>
                                        </thead>
                                        <tbody id="rate-history-body">
                                            <tr>
                                                <td colspan="6" class="text-center text-muted">
                                                    Memuat data...
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
    .bg-gradient-success {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    }

    .bg-gradient-danger {
        background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);
    }

    .display-4 {
        font-size: 2.5rem;
    }

    @media (max-width: 768px) {
        .display-4 {
            font-size: 1.8rem;
        }
    }

    .card {
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease;
    }

    .card:hover {
        transform: translateY(-5px);
    }

    .spin {
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        from {
            transform: rotate(0deg);
        }

        to {
            transform: rotate(360deg);
        }
    }
</style>

<script src="{{ asset('assets') }}/js/jquery-3.6.0.min.js"></script>
<script type="text/javascript">
    let rateHistory = [];
    let checkInterval = null;
    let lastFetchMinute = -1;
    const TARGET_SECOND = 3; // Update di detik ke-3

    $(function() {
        // Fetch pertama kali
        fetchGoldRate();

        // Cek setiap 500ms apakah sudah waktunya fetch (detik ke-3 setiap menit)
        checkInterval = setInterval(checkAndFetch, 500);
    });

    function checkAndFetch() {
        const now = new Date();
        const currentSecond = now.getSeconds();
        const currentMinute = now.getMinutes();

        // Jika detik ke-3 dan belum fetch di menit ini
        if (currentSecond === TARGET_SECOND && currentMinute !== lastFetchMinute) {
            lastFetchMinute = currentMinute;
            fetchGoldRate();
        }

        // Update countdown display
        updateCountdown(now);
    }

    function updateCountdown(now) {
        const currentSecond = now.getSeconds();
        let secondsUntilNext;

        if (currentSecond < TARGET_SECOND) {
            secondsUntilNext = TARGET_SECOND - currentSecond;
        } else {
            secondsUntilNext = (60 - currentSecond) + TARGET_SECOND;
        }

        $('#countdown').text(secondsUntilNext + ' detik');
    }

    function fetchGoldRate() {
        // Tampilkan loading
        $('#refresh-icon').addClass('spin');

        $.ajax({
            url: '{{ route('gold-rate.fetch') }}',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.meta && response.meta.status === 'success') {
                    updateDisplay(response.data);
                    addToHistory(response.data);
                } else {
                    showError('Gagal mengambil data');
                }
            },
            error: function(xhr, status, error) {
                showError('Error: ' + error);
            },
            complete: function() {
                $('#refresh-icon').removeClass('spin');
            }
        });
    }

    function updateDisplay(data) {
        const buyingRate = data.buying_rate;
        const sellingRate = data.selling_rate;
        const spread = sellingRate - buyingRate;
        const updatedAt = data.updated_at;

        // Format currency
        $('#buying-rate').text(formatCurrency(buyingRate));
        $('#selling-rate').text(formatCurrency(sellingRate));
        $('#spread-amount').text(formatCurrency(spread));

        // Update timestamp
        const now = new Date();
        $('#last-update').html('<i class="fa fa-clock mr-1"></i>' + formatTime(now));
    }

    function addToHistory(data) {
        const now = new Date();
        const spread = data.selling_rate - data.buying_rate;

        // Tambah ke awal array
        rateHistory.unshift({
            time: now,
            buying_rate: data.buying_rate,
            selling_rate: data.selling_rate,
            spread: spread
        });

        // Batasi maksimal 20 history
        if (rateHistory.length > 20) {
            rateHistory.pop();
        }

        renderHistory();
    }

    function renderHistory() {
        let html = '';

        if (rateHistory.length === 0) {
            html = '<tr><td colspan="5" class="text-center text-muted">Belum ada data</td></tr>';
        } else {
            rateHistory.forEach((item, index) => {
                const estCuan20JT = Math.round((20000000 / item.buying_rate) * item.selling_rate) - 19315000;
                const estCuan30JT = Math.round((30000000 / item.buying_rate) * item.selling_rate) - 28980000;
                html += `
                    <tr>
                        <td class="text-center">${index + 1}</td>
                        <td class="text-center">${formatDateTime(item.time)}</td>
                        <td class="text-right text-success font-weight-bold">${formatCurrency(item.buying_rate)}</td>
                        <td class="text-right text-danger font-weight-bold">${formatCurrency(item.selling_rate)}</td>
                        <td class="text-right">${formatCurrency(estCuan20JT)}</td>
                        <td class="text-right">${formatCurrency(estCuan30JT)}</td>
                    </tr>
                `;
            });
        }

        $('#rate-history-body').html(html);
    }

    function formatCurrency(value) {
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
    }

    function formatTime(date) {
        return date.toLocaleTimeString('id-ID', {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });
    }

    function formatDateTime(date) {
        return date.toLocaleDateString('id-ID', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        }) + ' ' + formatTime(date);
    }

    function showError(message) {
        $('#buying-rate').html('<span class="text-danger">Error</span>');
        $('#selling-rate').html('<span class="text-danger">Error</span>');
        Swal.fire('Error', message, 'error');
    }

    // Cleanup interval saat halaman ditutup
    $(window).on('beforeunload', function() {
        if (refreshInterval) {
            clearInterval(refreshInterval);
        }
    });
</script>
