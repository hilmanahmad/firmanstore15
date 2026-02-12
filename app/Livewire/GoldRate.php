<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Http;

class GoldRate extends Component
{
    public $buyingRate = 0;
    public $sellingRate = 0;
    public $spread = 0;
    public $lastUpdate = '-';
    public $countdown = 60;
    public $isLoading = true;
    public $error = null;
    public $rateHistory = [];
    public $promoThreshold = 0; // Harga beli untuk promo
    public $isPromoActive = false;

    // Search dan pagination untuk history
    public $search = '';
    public $perPage = 10;

    const TARGET_SECOND = 3;
    const MAX_RETRIES = 3;

    public function mount()
    {
        $this->fetchRate();
    }

    public function fetchRate($retryCount = 0)
    {
        $this->isLoading = true;
        $this->error = null;

        try {
            $response = Http::timeout(3)->post('https://api.treasury.id/api/v1/antigrvty/gold/rate');

            if ($response->successful()) {
                $data = $response->json();

                if (isset($data['meta']['status']) && $data['meta']['status'] === 'success') {
                    $this->buyingRate = $data['data']['buying_rate'];
                    $this->sellingRate = $data['data']['selling_rate'];
                    $this->spread = $this->sellingRate - $this->buyingRate;
                    $this->lastUpdate = now()->format('H:i:s');

                    // Cek promo
                    $this->checkPromo();

                    // Add to history
                    $this->addToHistory();

                    // Dispatch event untuk flash cards
                    $this->dispatch('rateUpdated');

                    $this->isLoading = false;
                    return;
                }
            }

            // Retry jika gagal
            if ($retryCount < self::MAX_RETRIES) {
                $this->fetchRate($retryCount + 1);
            } else {
                $this->error = 'Gagal mengambil data setelah ' . self::MAX_RETRIES . 'x retry';
                $this->isLoading = false;
            }
        } catch (\Exception $e) {
            if ($retryCount < self::MAX_RETRIES) {
                $this->fetchRate($retryCount + 1);
            } else {
                $this->error = $e->getMessage();
                $this->isLoading = false;
            }
        }
    }

    protected function addToHistory()
    {
        $estCuan20JT = round((20000000 / $this->buyingRate) * $this->sellingRate) - 19330000;
        $estCuan30JT = round((30000000 / $this->buyingRate) * $this->sellingRate) - 28995000;
        $estCuan40JT = round((40000000 / $this->buyingRate) * $this->sellingRate) - 38660000;
        $estCuan50JT = round((50000000 / $this->buyingRate) * $this->sellingRate) - 48325000;

        // Tambah ke awal array (terbaru di atas)
        array_unshift($this->rateHistory, [
            'no' => count($this->rateHistory) + 1,
            'time' => now()->format('d/m/Y H:i:s'),
            'buying_rate' => $this->buyingRate,
            'selling_rate' => $this->sellingRate,
            'est_cuan_20' => $estCuan20JT,
            'est_cuan_30' => $estCuan30JT,
            'est_cuan_40' => $estCuan40JT,
            'est_cuan_50' => $estCuan50JT,
        ]);

        // Update nomor urut
        foreach ($this->rateHistory as $key => $item) {
            $this->rateHistory[$key]['no'] = count($this->rateHistory) - $key;
        }
    }

    public function getFilteredHistoryProperty()
    {
        $filtered = collect($this->rateHistory);

        if ($this->search) {
            $filtered = $filtered->filter(function ($item) {
                return str_contains(strtolower($item['time']), strtolower($this->search)) ||
                    str_contains((string) $item['buying_rate'], $this->search) ||
                    str_contains((string) $item['selling_rate'], $this->search);
            });
        }

        return $filtered;
    }

    public function updatePromoThreshold()
    {
        $this->validate([
            'promoThreshold' => 'nullable|numeric|min:0',
        ]);

        $this->checkPromo();
    }

    protected function checkPromo()
    {
        $wasPromoActive = $this->isPromoActive;

        if ($this->promoThreshold > 0 && $this->buyingRate >= $this->promoThreshold) {
            $this->isPromoActive = true;

            // Dispatch event untuk notifikasi browser
            if (!$wasPromoActive) {
                $this->dispatch('promoActivated', [
                    'buyingRate' => $this->buyingRate,
                    'threshold' => $this->promoThreshold
                ]);
            }
        } else {
            $this->isPromoActive = false;
        }
    }

    public function formatCurrency($value)
    {
        return 'Rp ' . number_format($value, 0, ',', '.');
    }

    public function render()
    {
        return view('livewire.gold-rate', [
            'filteredHistory' => $this->filteredHistory,
        ]);
    }
}
