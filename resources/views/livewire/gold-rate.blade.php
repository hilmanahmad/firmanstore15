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
                                <div class="custom-control custom-switch mr-3" title="Notifikasi Suara">
                                    <input type="checkbox" class="custom-control-input" id="soundToggle">
                                    <label class="custom-control-label" for="soundToggle">
                                        <i class="fa fa-bell" id="soundIcon"></i>
                                    </label>
                                </div>
                                <span class="badge badge-info mr-2">
                                    <i class="fa fa-hourglass-half mr-1"></i>Next: <span id="countdown">-</span>
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

    @script
        <script>
            const TARGET_SECOND = 3;
            const ALERT_SECONDS = 10; // Bunyi dring saat 10 detik sebelum refresh
            let lastFetchMinute = -1;
            let hasPlayedAlert = false;

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
                        soundIcon.className = 'fa fa-bell text-success';
                    } else {
                        soundIcon.className = 'fa fa-bell-slash text-muted';
                    }
                }
            }

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
                    // Use Web Speech API for Text-to-Speech
                    if ('speechSynthesis' in window) {
                        const utterance = new SpeechSynthesisUtterance('yuk konfirm');
                        utterance.lang = 'id-ID'; // Bahasa Indonesia
                        utterance.rate = 1.0; // Kecepatan normal
                        utterance.pitch = 1.2; // Sedikit lebih tinggi
                        utterance.volume = 2.0; // Volume penuh

                        window.speechSynthesis.speak(utterance);
                        console.log('Playing voice: yuk konfirm');
                    } else {
                        // Fallback ke bell sound jika speech synthesis tidak didukung
                        console.log('Speech synthesis not supported, using fallback bell sound');
                        const audioContext = new(window.AudioContext || window.webkitAudioContext)();
                        const oscillator = audioContext.createOscillator();
                        const gainNode = audioContext.createGain();

                        oscillator.connect(gainNode);
                        gainNode.connect(audioContext.destination);

                        oscillator.frequency.setValueAtTime(830, audioContext.currentTime);
                        oscillator.type = 'sine';

                        gainNode.gain.setValueAtTime(0.5, audioContext.currentTime);
                        gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.5);

                        oscillator.start(audioContext.currentTime);
                        oscillator.stop(audioContext.currentTime + 0.5);
                    }
                } catch (e) {
                    console.log('Audio not supported:', e);
                }
            } // Initialize TradingView Widget
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

                    // Play dring sound when countdown reaches 10 seconds (if enabled)
                    if (secondsUntilNext === ALERT_SECONDS && !hasPlayedAlert) {
                        playDringSound(); // Will check isSoundEnabled() inside
                        hasPlayedAlert = true;
                        countdownEl.classList.add('text-warning');
                    }

                    // Reset alert flag when countdown resets
                    if (secondsUntilNext > ALERT_SECONDS) {
                        hasPlayedAlert = false;
                        countdownEl.classList.remove('text-warning');
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
