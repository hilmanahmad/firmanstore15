<div>
    <div id="content-page" class="content-page">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="iq-card">
                        <div class="iq-card-header d-flex justify-content-between">
                            <div class="iq-header-title">
                                <h4 class="card-title">
                                    <i class="fa fa-calculator text-warning"></i> Kalkulator Buyback Emas
                                </h4>
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


                            <!-- Form Input -->
                            <div class="row">
                                <!-- Tabel Produk -->
                                <div class="col-md-8">
                                    <div class="card mb-4">
                                        <div
                                            class="card-header bg-primary text-white py-2 d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0"><i class="fa fa-cube mr-2"></i>Daftar Produk</h6>
                                            <button type="button" class="btn btn-sm btn-light" wire:click="addProduct">
                                                <i class="fa fa-plus"></i> Tambah Produk
                                            </button>
                                        </div>
                                        <div class="card-body p-0">
                                            <div class="table-responsive">
                                                <table class="table table-bordered mb-0">
                                                    <thead class="thead-light">
                                                        <tr>
                                                            <th width="40" class="text-center">#</th>
                                                            <th>Nama Produk</th>
                                                            <th width="90" class="text-center">Gram</th>
                                                            <th width="140" class="text-center">BB/gram</th>
                                                            <th width="140" class="text-right">Total</th>
                                                            <th width="40" class="text-center"></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($products as $index => $product)
                                                            <tr>
                                                                <td class="text-center align-middle">{{ $index + 1 }}
                                                                </td>
                                                                <td>
                                                                    <input type="text"
                                                                        class="form-control form-control-sm"
                                                                        wire:model="products.{{ $index }}.nama"
                                                                        placeholder="Nama emas...">
                                                                </td>
                                                                <td>
                                                                    <input type="text" step="0.01"
                                                                        class="form-control form-control-sm text-center"
                                                                        wire:model.live="products.{{ $index }}.gram"
                                                                        placeholder="0.5" min="0.01">
                                                                </td>
                                                                <td>
                                                                    @php $bbValue = $product['bb'] ?? 0; @endphp
                                                                    <input type="text"
                                                                        class="form-control form-control-sm text-right currency-input"
                                                                        value="{{ $bbValue > 0 ? number_format($bbValue, 0, ',', '.') : '' }}"
                                                                        x-data="{ value: '{{ $bbValue > 0 ? number_format($bbValue, 0, ',', '.') : '' }}' }"
                                                                        x-on:input="value = formatRupiah($el.value); $el.value = value"
                                                                        x-on:change="$wire.set('products.{{ $index }}.bb', parseRupiah(value))"
                                                                        placeholder="1.482.380">
                                                                </td>
                                                                <td
                                                                    class="text-right align-middle font-weight-bold bg-light">
                                                                    {{ $this->formatCurrency($product['total'] ?? 0) }}
                                                                </td>
                                                                <td class="text-center align-middle">
                                                                    @if (count($products) > 1)
                                                                        <button type="button"
                                                                            class="btn btn-sm btn-outline-danger"
                                                                            wire:click="removeProduct({{ $index }})">
                                                                            <i class="fa fa-times"></i>
                                                                        </button>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                    <tfoot class="bg-warning">
                                                        <tr class="font-weight-bold">
                                                            <td colspan="4" class="text-right">Total Buyback:</td>
                                                            <td class="text-right">
                                                                {{ $this->formatCurrency(collect($products)->sum(fn($p) => $p['total'] ?? 0)) }}
                                                            </td>
                                                            <td></td>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Payment & Hasil -->
                                <div class="col-md-4">
                                    <div class="card mb-4">
                                        <div class="card-header bg-success text-white py-2">
                                            <h6 class="mb-0"><i class="fa fa-money mr-2"></i>Payment</h6>
                                        </div>
                                        <div class="card-body p-0">
                                            <table class="table table-bordered mb-0">
                                                <tbody>
                                                    <tr>
                                                        <td width="40%"><strong>Payment</strong></td>
                                                        <td>
                                                            <input type="text"
                                                                class="form-control form-control-sm text-right currency-input"
                                                                value="{{ $payment > 0 ? number_format($payment, 0, ',', '.') : '' }}"
                                                                x-data="{ value: '{{ $payment > 0 ? number_format($payment, 0, ',', '.') : '' }}' }"
                                                                x-on:input="value = formatRupiah($el.value); $el.value = value"
                                                                x-on:change="$wire.set('payment', parseRupiah(value))"
                                                                placeholder="1.500.100">
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Kembalian</strong></td>
                                                        <td>
                                                            <input type="text"
                                                                class="form-control form-control-sm text-right currency-input"
                                                                value="{{ $kembalian > 0 ? number_format($kembalian, 0, ',', '.') : '' }}"
                                                                x-data="{ value: '{{ $kembalian > 0 ? number_format($kembalian, 0, ',', '.') : '' }}' }"
                                                                x-on:input="value = formatRupiah($el.value); $el.value = value"
                                                                x-on:change="$wire.set('kembalian', parseRupiah(value))"
                                                                placeholder="47.000">
                                                        </td>
                                                    </tr>
                                                    <tr class="bg-light">
                                                        <td><strong>Sisa (Modal)</strong></td>
                                                        <td class="font-weight-bold">
                                                            {{ $this->formatCurrency($payment - $kembalian) }}
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <!-- Tombol Hitung -->
                                    <div class="d-flex justify-content-between mb-4">
                                        <button type="button" class="btn btn-success btn-block mr-2"
                                            wire:click="hitungBuyback">
                                            <i class="fa fa-calculator mr-1"></i> Hitung Buyback
                                        </button>
                                        <button type="button" class="btn btn-secondary" wire:click="resetForm">
                                            <i class="fa fa-refresh"></i>
                                        </button>
                                    </div>

                                    <!-- Hasil -->
                                    @if ($showResult)
                                        <div
                                            class="card {{ $totalKeuntungan >= 0 ? 'border-success' : 'border-danger' }}">
                                            <div
                                                class="card-header {{ $totalKeuntungan >= 0 ? 'bg-success' : 'bg-danger' }} text-white py-2">
                                                <h6 class="mb-0">
                                                    <i
                                                        class="fa {{ $totalKeuntungan >= 0 ? 'fa-check-circle' : 'fa-times-circle' }} mr-2"></i>
                                                    Hasil Perhitungan
                                                </h6>
                                            </div>
                                            <div class="card-body">
                                                <table class="table table-sm table-borderless mb-0">
                                                    <tr>
                                                        <td>Total Buyback</td>
                                                        <td class="text-right font-weight-bold">
                                                            {{ $this->formatCurrency($totalBuyback) }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Modal Asli</td>
                                                        <td class="text-right">{{ $this->formatCurrency($modalAsli) }}
                                                        </td>
                                                    </tr>
                                                    <tr class="border-top">
                                                        <td class="font-weight-bold">
                                                            {{ $totalKeuntungan >= 0 ? 'Total Untung' : 'Total Rugi' }}
                                                        </td>
                                                        <td
                                                            class="text-right h4 mb-0 {{ $totalKeuntungan >= 0 ? 'text-success' : 'text-danger' }}">
                                                            {{ $totalKeuntungan >= 0 ? '+' : '-' }}{{ $this->formatCurrency(abs($totalKeuntungan)) }}
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Riwayat Perhitungan -->
                            @if (count($riwayat) > 0)
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h5><i class="fa fa-history mr-2"></i>Riwayat Perhitungan (Sesi Ini)</h5>
                                            <button class="btn btn-sm btn-outline-danger" wire:click="clearRiwayat">
                                                <i class="fa fa-trash mr-1"></i> Hapus Riwayat
                                            </button>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped table-sm">
                                                <thead class="thead-dark">
                                                    <tr>
                                                        <th class="text-center" width="60">Waktu</th>
                                                        <th>Produk</th>
                                                        <th class="text-center">Total Gram</th>
                                                        <th class="text-right">Modal Asli</th>
                                                        <th class="text-right">Total Buyback</th>
                                                        <th class="text-right">Keuntungan</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($riwayat as $item)
                                                        <tr>
                                                            <td class="text-center">{{ $item['waktu'] }}</td>
                                                            <td>
                                                                @foreach ($item['products'] as $p)
                                                                    <small class="d-block">{{ $p['nama'] ?: 'Emas' }}
                                                                        ({{ $p['gram'] }}gr)
                                                                    </small>
                                                                @endforeach
                                                            </td>
                                                            <td class="text-center">{{ $item['total_gram'] }} gr</td>
                                                            <td class="text-right">
                                                                {{ $this->formatCurrency($item['modal_asli']) }}</td>
                                                            <td class="text-right">
                                                                {{ $this->formatCurrency($item['total_buyback']) }}
                                                            </td>
                                                            <td
                                                                class="text-right font-weight-bold {{ $item['keuntungan'] >= 0 ? 'text-success' : 'text-danger' }}">
                                                                {{ $item['keuntungan'] >= 0 ? '+' : '' }}{{ $this->formatCurrency($item['keuntungan']) }}
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Info Rumus -->
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="alert alert-light border">
                                        <h6><i class="fa fa-info-circle mr-2"></i>Rumus Perhitungan:</h6>
                                        <ul class="mb-0">
                                            <li><strong>Total per Produk</strong> = Harga Jual (BB) × Gramasi</li>
                                            <li><strong>Total Buyback</strong> = Jumlah semua Total Produk</li>
                                            <li><strong>Modal Asli (Sisa)</strong> = Payment - Kembalian</li>
                                            <li><strong>Total Keuntungan</strong> = Total Buyback - Modal Asli</li>
                                        </ul>
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
        .bg-gradient-warning {
            background: linear-gradient(135deg, #f39c12 0%, #f1c40f 100%);
        }

        .bg-gradient-info {
            background: linear-gradient(135deg, #3498db 0%, #2ecc71 100%);
        }

        .card {
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
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

        .form-control-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
    </style>

    <script>
        // Format angka ke format Rupiah (dengan titik sebagai pemisah ribuan)
        function formatRupiah(angka) {
            // Hapus semua karakter selain angka
            let number_string = angka.toString().replace(/[^0-9]/g, '');

            if (number_string === '') return '';

            // Format dengan titik sebagai pemisah ribuan
            let formatted = '';
            let len = number_string.length;
            for (let i = 0; i < len; i++) {
                if (i > 0 && (len - i) % 3 === 0) {
                    formatted += '.';
                }
                formatted += number_string[i];
            }

            return formatted;
        }

        // Parse format rupiah kembali ke angka
        function parseRupiah(formatted) {
            if (!formatted) return 0;
            return parseInt(formatted.toString().replace(/\./g, '')) || 0;
        }

        // Re-format inputs saat Livewire update
        document.addEventListener('livewire:navigated', () => {
            document.querySelectorAll('.currency-input').forEach(el => {
                if (el.value) {
                    el.value = formatRupiah(el.value);
                }
            });
        });
    </script>
</div>
