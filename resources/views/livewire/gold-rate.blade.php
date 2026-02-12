<div>
    <div id="content-page" class="content-page">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="iq-card">
                        <div class="iq-card-header d-flex justify-content-between">
                            <div class="iq-header-title">
                                <h4 class="card-title">
                                    <i class="fa fa-coins text-warning"></i> Harga Emas
                                </h4>
                            </div>
                            <div class="iq-card-header-toolbar d-flex align-items-center">
                                <button class="btn btn-sm btn-outline-warning mr-2" id="notifPermBtn"
                                    onclick="requestNotificationPermission()" title="Aktifkan Notifikasi Browser">
                                    <i class="fa fa-bell-o" id="notifPermIcon"></i>
                                </button>
                                <div class="custom-control custom-switch mr-3" title="Notifikasi Suara">
                                    <input type="checkbox" class="custom-control-input" id="soundToggle">
                                    <label class="custom-control-label" for="soundToggle">
                                        <i class="fa fa-volume-up" id="soundIcon"></i>
                                    </label>
                                </div>
                                <span class="badge badge-info mr-2 countdown-badge" id="countdownBadge">
                                    <i class="fa fa-hourglass-half mr-1 hourglass-spin"></i>Next: <span
                                        id="countdown">-</span>
                                </span>
                                <span class="badge badge-secondary">
                                    <i class="fa fa-clock mr-1"></i>{{ $lastUpdate }}
                                </span>
                                <button class="btn btn-sm btn-outline-primary ml-2" wire:click="fetchRate"
                                    wire:loading.attr="disabled" title="Refresh">
                                    <i class="fa fa-refresh {{ $isLoading ? 'spin' : '' }}"
                                        wire:loading.class="spin"></i>
                                </button>
                            </div>
                        </div>
                        <div class="iq-card-body">
                            {{-- Error Alert --}}
                            @if ($error)
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="fa fa-exclamation-triangle mr-2"></i>{{ $error }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            {{-- Promo Alert --}}
                            @if ($isPromoActive)
                                <div class="alert alert-success alert-dismissible fade show animate__animated animate__heartBeat"
                                    role="alert">
                                    <i class="fa fa-bullhorn mr-2"></i>
                                    <strong>PROMO SEDANG AKTIF!</strong> Harga beli saat ini
                                    <strong>{{ $this->formatCurrency($buyingRate) }}</strong>
                                    telah mencapai atau melebihi target
                                    <strong>{{ $this->formatCurrency($promoThreshold) }}</strong>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            {{-- Promo Threshold Input --}}
                            <div class="row mb-3">
                                <div class="col-lg-6">
                                    <div class="d-flex align-items-center mb-2">
                                        <button class="btn btn-sm btn-outline-warning" type="button"
                                            id="togglePromoSetting" onclick="togglePromoInput()">
                                            <i class="fa fa-bell mr-1" id="promoSettingIcon"></i>
                                            Pengaturan Promo
                                            @if ($promoThreshold > 0)
                                                <span
                                                    class="badge badge-light ml-1">{{ $this->formatCurrency($promoThreshold) }}</span>
                                            @endif
                                        </button>
                                        @if ($isPromoActive)
                                            <span
                                                class="badge badge-success ml-2 animate__animated animate__pulse animate__infinite">
                                                <i class="fa fa-check-circle mr-1"></i> PROMO ON
                                            </span>
                                        @endif
                                    </div>
                                    <div class="card border-warning" id="promoSettingCard" style="display: none;">
                                        <div class="card-body py-3">
                                            <div class="form-group mb-0">
                                                <label class="small text-muted mb-2">
                                                    <i class="fa fa-info-circle mr-1"></i>
                                                    Masukkan harga beli minimum untuk notifikasi promo:
                                                </label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">Rp</span>
                                                    </div>
                                                    <input type="text" id="promoThresholdInput" class="form-control"
                                                        placeholder="1.400.000"
                                                        value="{{ $promoThreshold > 0 ? number_format($promoThreshold, 0, ',', '.') : '' }}">
                                                    <div class="input-group-append">
                                                        <button class="btn btn-warning" type="button" id="btnSetPromo">
                                                            <i class="fa fa-check"></i> Set
                                                        </button>
                                                        @if ($promoThreshold > 0)
                                                            <button class="btn btn-outline-danger" type="button"
                                                                id="btnClearPromo">
                                                                <i class="fa fa-times"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                </div>
                                                <small class="form-text text-muted">
                                                    Notifikasi akan muncul ketika harga beli ≥ nilai yang dimasukkan
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Buying Rate Card -->
                                <div class="col-md-6 mb-4">
                                    <div class="card bg-gradient-success text-white h-100">
                                        <div class="card-body text-center">
                                            <div class="mb-3">
                                                <i class="fa fa-arrow-circle-down fa-3x"></i>
                                            </div>
                                            <h5 class="card-title text-white">Harga Beli</h5>
                                            <h2 class="display-4 font-weight-bold">
                                                @if ($isLoading && $buyingRate == 0)
                                                    <span class="spinner-border spinner-border-sm"
                                                        role="status"></span>
                                                @else
                                                    {{ $this->formatCurrency($buyingRate) }}
                                                @endif
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
                                            <h2 class="display-4 font-weight-bold">
                                                @if ($isLoading && $sellingRate == 0)
                                                    <span class="spinner-border spinner-border-sm"
                                                        role="status"></span>
                                                @else
                                                    {{ $this->formatCurrency($sellingRate) }}
                                                @endif
                                            </h2>
                                            <p class="mb-0">per gram</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Spread Info -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="alert alert-info d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="fa fa-info-circle mr-2"></i>
                                            <strong>Spread:</strong> {{ $this->formatCurrency($spread) }}
                                        </div>
                                        <div>
                                            <small class="text-muted">
                                                <i class="fa fa-clock mr-1"></i> Auto refresh setiap 1 menit di detik
                                                ke-3
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Rate History -->
                            <div class="row mt-3 mb-4">
                                <div class="col-12">
                                    <h5><i class="fa fa-history mr-2"></i>Riwayat Rate (Sesi Ini)</h5>

                                    <!-- Search & Per Page -->
                                    <div class="row mb-3">
                                        <div class="col-md-3">
                                            <select wire:model.live="perPage" class="form-control form-control-sm">
                                                <option value="10">10 per halaman</option>
                                                <option value="25">25 per halaman</option>
                                                <option value="50">50 per halaman</option>
                                                <option value="100">100 per halaman</option>
                                            </select>
                                        </div>
                                        <div class="col-md-9">
                                            <input type="text" wire:model.live.debounce.300ms="search"
                                                class="form-control form-control-sm" placeholder="Cari...">
                                        </div>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped table-sm" style="width:100%">
                                            <thead class="thead-dark">
                                                <tr>
                                                    <th class="text-center" width="50">#</th>
                                                    <th class="text-center">Waktu</th>
                                                    <th class="text-right">Harga Beli</th>
                                                    <th class="text-right">Harga Jual</th>
                                                    <th class="text-right">Est. cuan 20 JT</th>
                                                    <th class="text-right">Est. cuan 30 JT</th>
                                                    <th class="text-right">Est. cuan 40 JT</th>
                                                    <th class="text-right">Est. cuan 50 JT</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($filteredHistory->take($perPage) as $item)
                                                    <tr>
                                                        <td class="text-center">{{ $item['no'] }}</td>
                                                        <td class="text-center">{{ $item['time'] }}</td>
                                                        <td class="text-right font-weight-bold">
                                                            {{ $this->formatCurrency($item['buying_rate']) }}</td>
                                                        <td class="text-right text-danger font-weight-bold">
                                                            {{ $this->formatCurrency($item['selling_rate']) }}</td>
                                                        <td
                                                            class="text-right {{ $item['est_cuan_20'] >= 0 ? '' : 'text-danger' }}">
                                                            {{ $this->formatCurrency($item['est_cuan_20']) }}
                                                        </td>
                                                        <td
                                                            class="text-right {{ $item['est_cuan_30'] >= 0 ? '' : 'text-danger' }}">
                                                            {{ $this->formatCurrency($item['est_cuan_30']) }}
                                                        </td>
                                                        <td
                                                            class="text-right {{ $item['est_cuan_40'] >= 0 ? '' : 'text-danger' }}">
                                                            {{ $this->formatCurrency($item['est_cuan_40']) }}
                                                        </td>
                                                        <td
                                                            class="text-right {{ $item['est_cuan_50'] >= 0 ? '' : 'text-danger' }}">
                                                            {{ $this->formatCurrency($item['est_cuan_50']) }}
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="6" class="text-center text-muted">
                                                            @if ($isLoading)
                                                                <span class="spinner-border spinner-border-sm mr-2"
                                                                    role="status"></span> Memuat data...
                                                            @else
                                                                Belum ada data
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="text-muted small">
                                        Menampilkan {{ min($perPage, $filteredHistory->count()) }} dari
                                        {{ $filteredHistory->count() }} data
                                    </div>
                                </div>
                            </div>

                            <!-- Charts Widget -->
                            <div class="row" wire:ignore>
                                <div class="col-md-6 mb-4">
                                    <div class="card">
                                        <div class="card-header  text-white py-2">
                                            <h6 class="mb-0"><i class="fa fa-chart-line mr-2"></i>TradingView -
                                                XAU/USD</h6>
                                        </div>
                                        <div class="card-body p-0">
                                            <div class="tradingview-widget-container">
                                                <div id="tradingview_xauusd"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <div class="card">
                                        <div class="card-header  text-white py-2">
                                            <h6 class="mb-0"><i class="fa fa-chart-bar mr-2"></i>Investing.com -
                                                Gold</h6>
                                        </div>
                                        <div class="card-body p-0" style="overflow: hidden;">
                                            <iframe class="chart-iframe"
                                                src="https://sslcharts.investing.com/index.php?force_lang=54&pair_ID=2138&timescale=900&candles=80&style=candles"
                                                width="100%" height="480" frameborder="0"
                                                style="margin-top: -50px; margin-bottom: -20px;"
                                                loading="lazy"></iframe>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <div class="card">
                                        <div class="card-header  text-white py-2">
                                            <h6 class="mb-0"><i class="fa fa-chart-bar mr-2"></i>Kalender Ekonomi
                                            </h6>
                                        </div>
                                        <div class="card-body p-0" style="overflow: hidden;">
                                            <iframe class="chart-iframe"
                                                src="https://sslecal2.investing.com?columns=exc_flags,exc_currency,exc_importance,exc_actual,exc_forecast,exc_previous&category=_employment,_economicActivity,_inflation,_centralBanks,_confidenceIndex&importance=3&features=datepicker,timezone,timeselector,filters&countries=5,37,48,35,17,36,26,12,72&calType=week&timeZone=27&lang=54"
                                                height="467" width="100%" loading="lazy"></iframe>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <div class="card">
                                        <div class="card-header  text-white py-2">
                                            <h6 class="mb-0"><i class="fa fa-chart-bar mr-2"></i>Analisis Teknis
                                                XAU/USD
                                            </h6>
                                        </div>
                                        <div class="tradingview-widget-container">
                                            <div class="tradingview-widget-container__widget"></div>
                                            <script type="text/javascript" src="https://s3.tradingview.com/external-embedding/embed-widget-technical-analysis.js"
                                                async>
                                                {
                                                    "interval": "1m",
                                                    "width": "100%",
                                                    "isTransparent": true,
                                                    "height": 450,
                                                    "symbol": "OANDA:XAUUSD",
                                                    "showIntervalTabs": true,
                                                    "displayMode": "single",
                                                    "locale": "id",
                                                    "colorTheme": "light"
                                                }
                                            </script>
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

            /* Countdown animations */
            .countdown-badge {
                transition: all 0.3s ease;
            }

            .countdown-badge.alert-mode {
                background: linear-gradient(45deg, #f39c12, #e74c3c) !important;
                animation: pulse-alert 0.5s ease-in-out infinite;
                transform: scale(1.1);
            }

            @keyframes pulse-alert {

                0%,
                100% {
                    opacity: 1;
                    transform: scale(1.1);
                }

                50% {
                    opacity: 0.8;
                    transform: scale(1.15);
                }
            }

            .hourglass-spin {
                display: inline-block;
                animation: hourglass-rotate 2s ease-in-out infinite;
            }

            @keyframes hourglass-rotate {
                0% {
                    transform: rotate(0deg);
                }

                50% {
                    transform: rotate(180deg);
                }

                100% {
                    transform: rotate(180deg);
                }
            }

            /* Flash animation for cards when update */
            .flash-update {
                animation: flash-border 1s ease-out;
            }

            @keyframes flash-border {
                0% {
                    box-shadow: 0 0 20px 5px rgba(255, 193, 7, 0.8);
                }

                100% {
                    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
                }
            }

            /* Notification permission button */
            #notifPermBtn.granted {
                background-color: #28a745 !important;
                border-color: #28a745 !important;
                color: white !important;
            }

            /* Title flash for background notification */
            .title-flash {
                animation: title-blink 1s ease-in-out infinite;
            }
        </style>

        @script
            <script>
                const TARGET_SECOND = 3;
                const ALERT_SECONDS = 10; // Bunyi dring saat 10 detik sebelum refresh
                let lastFetchMinute = -1;
                let hasPlayedAlert = false;
                let originalTitle = document.title;
                let titleFlashInterval = null;

                // Request notification permission
                window.requestNotificationPermission = function() {
                    if ('Notification' in window) {
                        Notification.requestPermission().then(permission => {
                            updateNotifPermButton();
                            if (permission === 'granted') {
                                // Test notification
                                new Notification('🔔 Notifikasi Aktif!', {
                                    body: 'Anda akan menerima notifikasi 10 detik sebelum update harga emas.',
                                    icon: '{{ asset('assets/images/logo.png') }}',
                                    tag: 'gold-rate-test'
                                });
                            }
                        });
                    } else {
                        alert('Browser tidak mendukung notifikasi');
                    }
                }

                // Update notification permission button
                function updateNotifPermButton() {
                    const btn = document.getElementById('notifPermBtn');
                    const icon = document.getElementById('notifPermIcon');
                    if (btn && icon && 'Notification' in window) {
                        if (Notification.permission === 'granted') {
                            btn.classList.add('granted');
                            btn.title = 'Notifikasi Browser Aktif';
                            icon.className = 'fa fa-bell';
                        } else if (Notification.permission === 'denied') {
                            btn.classList.remove('granted');
                            btn.title = 'Notifikasi Browser Diblokir';
                            icon.className = 'fa fa-bell-slash';
                        }
                    }
                }

                // Show browser notification
                function showBrowserNotification() {
                    if ('Notification' in window && Notification.permission === 'granted') {
                        const notification = new Notification('⏰ Yuk Konfirm!', {
                            body: 'Update harga emas dalam 10 detik!',
                            icon: '{{ asset('assets/images/logo.png') }}',
                            tag: 'gold-rate-alert',
                            requireInteraction: false,
                            silent: false
                        });

                        // Auto close after 5 seconds
                        setTimeout(() => notification.close(), 5000);

                        // Focus window when clicked
                        notification.onclick = function() {
                            window.focus();
                            notification.close();
                        };
                    }
                }

                // Flash document title for background tabs
                function startTitleFlash() {
                    let isOriginal = true;
                    titleFlashInterval = setInterval(() => {
                        document.title = isOriginal ? '🔔 YUK KONFIRM!' : originalTitle;
                        isOriginal = !isOriginal;
                    }, 500);

                    // Stop after 10 seconds
                    setTimeout(stopTitleFlash, 10000);
                }

                function stopTitleFlash() {
                    if (titleFlashInterval) {
                        clearInterval(titleFlashInterval);
                        titleFlashInterval = null;
                        document.title = originalTitle;
                    }
                }

                // Add visual flash to countdown badge
                function triggerAlertMode(enable) {
                    const badge = document.getElementById('countdownBadge');
                    if (badge) {
                        if (enable) {
                            badge.classList.add('alert-mode');
                        } else {
                            badge.classList.remove('alert-mode');
                        }
                    }
                }

                // Flash cards on update
                function flashCards() {
                    document.querySelectorAll('.card.bg-gradient-success, .card.bg-gradient-danger').forEach(card => {
                        card.classList.add('flash-update');
                        setTimeout(() => card.classList.remove('flash-update'), 1000);
                    });
                }

                // Listen for Livewire events
                $wire.on('rateUpdated', () => {
                    flashCards();
                });

                // Listen for promo activation
                $wire.on('promoActivated', (event) => {
                    const data = event[0] || event;
                    const buyingRate = data.buyingRate;
                    const threshold = data.threshold;

                    // Show browser notification
                    if ('Notification' in window && Notification.permission === 'granted') {
                        const notification = new Notification('🎉 PROMO AKTIF!', {
                            body: `Harga beli Rp ${buyingRate.toLocaleString('id-ID')} telah mencapai target!`,
                            icon: '{{ asset('assets/images/logo.png') }}',
                            tag: 'gold-promo-alert',
                            requireInteraction: true,
                            silent: false
                        });

                        notification.onclick = function() {
                            window.focus();
                            notification.close();
                        };
                    }

                    // Play sound
                    if (isSoundEnabled()) {
                        playPromoSound();
                    }

                    // Flash title
                    startPromoTitleFlash();
                });

                // Play promo sound
                function playPromoSound() {
                    try {
                        const audio = new Audio();
                        audio.src =
                            'data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBSuBzvLZiTYIGGS86+mjVhQKTKXh67hYFAgzjddqoZEMFEum5u2mUxELOoPK6sV2FgozmNDkw3MdBiV/wvDdjkAMDlmu4+yrWBEGLInZ8c1yIwUledPw2YM6CAo9jcnqxXITBi+F1PLSfC4GJnrC8OCNPwwOV7Hk7KtYEQQuhtbxzXQjBSR1z/DdhzkHCj+JyurHcxQHL4HM7t15KwYncrnl7qVSEgQvhtTyz3sjBCJzu+zkqVEOCy+ByvHWfy0FI3fB8N+OQwwPWrDp7a1bEgYufMfsw3IdBSp5vfDakz8MCz6Kx+rGchUFMYbU8sx4KgUods3v24Y3Bwo+jMjrx3MVBSuAy+/XgSsGKHi98N+LQAoJVK7m7a5cEwUneb7q05pBDAk+i8jrx3MVBCiBzO7WeS0FJXa88NuBOQgKQJDJ68d0FAQrhszv1YEvBih3vPDfi0EKDFex5u2vXhQHLoPO89CHNwYle7/p0ptEDgpBjcnqxnMUBCuBye/WgCwGJ3i88OGMPgsLV67n7q9eFAgti8zw2YQ5CAo+jMfqxHIVBSyBzu7TeywGJ3e58N+KQQsKVK/m7bJgEgcuf8nv1Ik6BwpAkMjpxHIVBCx+yO/VgywGKHi98N+KQQsKVK/m7bJgEgcuf8nv1Ik6BwpAkMjpxHIVBCx+yO/VgywGKHi98N+KQQsKVK/m7bJgEgcuf8nv1Ik6BwpAkMjpxHIVBCx+yO/VgywGKHi98N+KQQsKVK/m7bJgEgcuf8nv1Ik6BwpAkMjpxHIVBCx+yO/VgywGKHi98N+KQQsKVK/m7bJgEgcuf8nv1Ik6BwpAkMjpxHIVBCx+yO/VgywGKHi98N+KQQsKVK/m7bJgEg==';
                        audio.volume = 0.8;
                        audio.play();

                        // Play multiple beeps
                        setTimeout(() => audio.play(), 300);
                        setTimeout(() => audio.play(), 600);

                        // Speech
                        setTimeout(() => {
                            if ('speechSynthesis' in window) {
                                window.speechSynthesis.cancel();
                                const utterance = new SpeechSynthesisUtterance('Promo aktif!');
                                utterance.lang = 'id-ID';
                                utterance.rate = 1.0;
                                utterance.pitch = 1.3;
                                utterance.volume = 1.0;
                                window.speechSynthesis.speak(utterance);
                            }
                        }, 800);
                    } catch (e) {
                        console.log('Promo audio error:', e);
                    }
                }

                // Flash title for promo
                function startPromoTitleFlash() {
                    let isOriginal = true;
                    let flashCount = 0;
                    const promoFlashInterval = setInterval(() => {
                        document.title = isOriginal ? '🎉 PROMO AKTIF!' : originalTitle;
                        isOriginal = !isOriginal;
                        flashCount++;

                        if (flashCount >= 20) { // 10 seconds
                            clearInterval(promoFlashInterval);
                            document.title = originalTitle;
                        }
                    }, 500);
                }

                // Function to check if sound is enabled
                function isSoundEnabled() {
                    return localStorage.getItem('goldRateSoundEnabled') !== 'false';
                }

                // Function to update sound icon
                function updateSoundIcon() {
                    const soundIcon = document.getElementById('soundIcon');
                    const soundToggle = document.getElementById('soundToggle');
                    if (soundIcon && soundToggle) {
                        if (soundToggle.checked) {
                            soundIcon.className = 'fa fa-volume-up text-success';
                        } else {
                            soundIcon.className = 'fa fa-volume-off text-muted';
                        }
                    }
                }

                // Initialize on load
                updateNotifPermButton();

                // Initialize sound toggle
                const soundToggle = document.getElementById('soundToggle');
                if (soundToggle) {
                    // Set initial state from localStorage
                    soundToggle.checked = isSoundEnabled();
                    updateSoundIcon();

                    // Listen for toggle changes
                    soundToggle.addEventListener('change', function() {
                        localStorage.setItem('goldRateSoundEnabled', this.checked ? 'true' : 'false');
                        updateSoundIcon();
                        console.log('Sound enabled:', this.checked);

                        // Play test sound when enabling
                        if (this.checked) {
                            playDringSound();
                        }
                    });
                }

                // Create voice notification "yuk konfirm"
                function playDringSound() {
                    // Double check if sound is enabled before playing
                    if (!isSoundEnabled()) {
                        console.log('Sound is disabled, not playing');
                        return;
                    }

                    try {
                        // Method 1: Try HTML5 Audio with base64 encoded beep sound (most reliable)
                        const audio = new Audio();
                        // Simple notification beep sound (data URI)
                        audio.src =
                            'data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBSuBzvLZiTYIGGS86+mjVhQKTKXh67hYFAgzjddqoZEMFEum5u2mUxELOoPK6sV2FgozmNDkw3MdBiV/wvDdjkAMDlmu4+yrWBEGLInZ8c1yIwUledPw2YM6CAo9jcnqxXITBi+F1PLSfC4GJnrC8OCNPwwOV7Hk7KtYEQQuhtbxzXQjBSR1z/DdhzkHCj+JyurHcxQHL4HM7t15KwYncrnl7qVSEgQvhtTyz3sjBCJzu+zkqVEOCy+ByvHWfy0FI3fB8N+OQwwPWrDp7a1bEgYufMfsw3IdBSp5vfDakz8MCz6Kx+rGchUFMYbU8sx4KgUods3v24Y3Bwo+jMjrx3MVBSuAy+/XgSsGKHi98N+LQAoJVK7m7a5cEwUneb7q05pBDAk+i8jrx3MVBCiBzO7WeS0FJXa88NuBOQgKQJDJ68d0FAQrhszv1YEvBih3vPDfi0EKDFex5u2vXhQHLoPO89CHNwYle7/p0ptEDgpBjcnqxnMUBCuBye/WgCwGJ3i88OGMPgsLV67n7q9eFAgti8zw2YQ5CAo+jMfqxHIVBSyBzu7TeywGJ3e58N+KQQsKVK/m7bJgEgcuf8nv1Ik6BwpAkMjpxHIVBCx+yO/VgywGKHi98N+KQQsKVK/m7bJgEgcuf8nv1Ik6BwpAkMjpxHIVBCx+yO/VgywGKHi98N+KQQsKVK/m7bJgEgcuf8nv1Ik6BwpAkMjpxHIVBCx+yO/VgywGKHi98N+KQQsKVK/m7bJgEgcuf8nv1Ik6BwpAkMjpxHIVBCx+yO/VgywGKHi98N+KQQsKVK/m7bJgEgcuf8nv1Ik6BwpAkMjpxHIVBCx+yO/VgywGKHi98N+KQQsKVK/m7bJgEg==';
                        audio.volume = 0.7;

                        const playPromise = audio.play();
                        if (playPromise !== undefined) {
                            playPromise.then(() => {
                                console.log('Audio beep played successfully');
                            }).catch(e => {
                                console.log('Audio beep failed:', e);
                            });
                        }

                        // Also try speech synthesis after beep
                        setTimeout(() => {
                            if ('speechSynthesis' in window) {
                                // Cancel any ongoing speech
                                window.speechSynthesis.cancel();

                                const utterance = new SpeechSynthesisUtterance('yuk konfirm');
                                utterance.lang = 'id-ID';
                                utterance.rate = 1.0;
                                utterance.pitch = 1.2;
                                utterance.volume = 1.0;

                                utterance.onstart = () => console.log('Speech started');
                                utterance.onerror = (e) => console.log('Speech error:', e);
                                utterance.onend = () => console.log('Speech ended');

                                window.speechSynthesis.speak(utterance);
                            }
                        }, 300);

                    } catch (e) {
                        console.log('Audio error:', e);
                    }
                }

                // Toggle Promo Setting Card
                window.togglePromoInput = function() {
                    const card = document.getElementById('promoSettingCard');
                    const icon = document.getElementById('promoSettingIcon');
                    if (card) {
                        if (card.style.display === 'none') {
                            card.style.display = 'block';
                            icon.className = 'fa fa-bell-slash mr-1';
                        } else {
                            card.style.display = 'none';
                            icon.className = 'fa fa-bell mr-1';
                        }
                    }
                }

                // Format Rupiah for Promo Threshold Input
                function formatRupiah(angka) {
                    if (!angka) return '';
                    const number = angka.toString().replace(/[^\d]/g, '');
                    if (!number) return '';
                    return number.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                }

                // Parse rupiah to number
                function parseRupiah(rupiah) {
                    if (!rupiah) return 0;
                    return parseInt(rupiah.toString().replace(/\./g, '')) || 0;
                }

                // Initialize promo input
                function initPromoInput() {
                    const promoInput = document.getElementById('promoThresholdInput');
                    const btnSet = document.getElementById('btnSetPromo');
                    const btnClear = document.getElementById('btnClearPromo');

                    if (promoInput) {
                        // Format on input
                        promoInput.addEventListener('input', function(e) {
                            const cursorPos = e.target.selectionStart;
                            const oldLength = e.target.value.length;
                            const formatted = formatRupiah(e.target.value);
                            e.target.value = formatted;

                            // Adjust cursor position
                            const newLength = formatted.length;
                            const diff = newLength - oldLength;
                            e.target.setSelectionRange(cursorPos + diff, cursorPos + diff);
                        });

                        // Submit on Enter key
                        promoInput.addEventListener('keypress', function(e) {
                            if (e.key === 'Enter') {
                                e.preventDefault();
                                setPromoThreshold();
                            }
                        });
                    }

                    if (btnSet) {
                        btnSet.addEventListener('click', function() {
                            setPromoThreshold();
                        });
                    }

                    if (btnClear) {
                        btnClear.addEventListener('click', function() {
                            const promoInput = document.getElementById('promoThresholdInput');
                            if (promoInput) promoInput.value = '';
                            @this.set('promoThreshold', 0);
                            @this.updatePromoThreshold();
                        });
                    }
                }

                function setPromoThreshold() {
                    const promoInput = document.getElementById('promoThresholdInput');
                    if (promoInput) {
                        const numericValue = parseRupiah(promoInput.value);
                        @this.set('promoThreshold', numericValue);
                        @this.updatePromoThreshold();
                    }
                }

                // Initialize on load
                initPromoInput();

                // Initialize TradingView Widget
                if (typeof TradingView !== 'undefined') {
                    new TradingView.widget({
                        "width": "100%",
                        "height": 480,
                        "symbol": "OANDA:XAUUSD",
                        "interval": "1",
                        "timezone": "Asia/Jakarta",
                        "theme": "light",
                        "style": "1",
                        "locale": "id",
                        "toolbar_bg": "#f1f3f6",
                        "enable_publishing": false,
                        "hide_legend": false,
                        "save_image": false,
                        "container_id": "tradingview_xauusd"
                    });
                }

                // Check every 500ms if it's time to fetch (at second 3 of every minute)
                setInterval(() => {
                    const now = new Date();
                    const currentSecond = now.getSeconds();
                    const currentMinute = now.getMinutes();

                    // Update countdown display
                    let secondsUntilNext;
                    if (currentSecond < TARGET_SECOND) {
                        secondsUntilNext = TARGET_SECOND - currentSecond;
                    } else {
                        secondsUntilNext = (60 - currentSecond) + TARGET_SECOND;
                    }

                    const countdownEl = document.getElementById('countdown');
                    if (countdownEl) {
                        countdownEl.textContent = secondsUntilNext + ' detik';

                        // Trigger all notifications when countdown reaches 10 seconds
                        if (secondsUntilNext === ALERT_SECONDS && !hasPlayedAlert) {
                            // 1. Play sound (if enabled)
                            playDringSound();

                            // 2. Show browser notification (works in background)
                            showBrowserNotification();

                            // 3. Flash document title (for background tabs)
                            startTitleFlash();

                            // 4. Visual alert mode
                            triggerAlertMode(true);

                            hasPlayedAlert = true;
                            countdownEl.classList.add('text-warning');
                        }

                        // Change badge color based on countdown
                        if (secondsUntilNext <= ALERT_SECONDS && secondsUntilNext > 0) {
                            triggerAlertMode(true);
                        }

                        // Reset alert flag when countdown resets
                        if (secondsUntilNext > ALERT_SECONDS) {
                            hasPlayedAlert = false;
                            countdownEl.classList.remove('text-warning');
                            triggerAlertMode(false);
                            stopTitleFlash();
                        }
                    }

                    // Fetch at second 3
                    if (currentSecond === TARGET_SECOND && currentMinute !== lastFetchMinute) {
                        lastFetchMinute = currentMinute;
                        $wire.fetchRate();
                    }
                }, 500);
            </script>
        @endscript
    </div>
