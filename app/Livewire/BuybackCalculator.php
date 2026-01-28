<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Http;

class BuybackCalculator extends Component
{
    // Daftar produk emas (bisa lebih dari 1)
    public $products = [];

    // Payment (sama untuk semua produk)
    public $payment = 0;
    public $kembalian = 0;

    // Hasil perhitungan
    public $modalAsli = 0;
    public $totalBuyback = 0;
    public $totalKeuntungan = 0;
    public $showResult = false;

    // Harga emas dari API
    public $buyingRate = 0;
    public $sellingRate = 0;
    public $lastUpdate = '-';
    public $isLoading = false;
    public $error = null;

    // Riwayat perhitungan
    public $riwayat = [];

    public function mount()
    {
        // Initialize dengan 1 produk kosong
        $this->products = [
            ['nama' => '', 'gram' => 0.5, 'bb' => 0, 'total' => 0]
        ];
        $this->fetchGoldRate();
    }

    public function fetchGoldRate()
    {
        $this->isLoading = true;
        $this->error = null;

        try {
            $response = Http::timeout(5)->post('https://api.treasury.id/api/v1/antigrvty/gold/rate');

            if ($response->successful()) {
                $data = $response->json();

                if (isset($data['meta']['status']) && $data['meta']['status'] === 'success') {
                    $this->buyingRate = $data['data']['buying_rate'];
                    $this->sellingRate = $data['data']['selling_rate'];
                    $this->lastUpdate = now()->format('H:i:s d/m/Y');
                    $this->isLoading = false;

                    // Recalculate if already has result
                    if ($this->showResult) {
                        $this->hitungBuyback();
                    }

                    return;
                }
            }

            $this->error = 'Gagal mengambil data harga emas';
            $this->isLoading = false;
        } catch (\Exception $e) {
            $this->error = 'Error: ' . $e->getMessage();
            $this->isLoading = false;
        }
    }

    public function addProduct()
    {
        $this->products[] = ['nama' => '', 'gram' => 0.5, 'bb' => 0, 'total' => 0];
    }

    // Parse rupiah format to number (remove dots)
    protected function parseRupiah($value)
    {
        if (is_numeric($value)) return floatval($value);
        return floatval(str_replace('.', '', $value));
    }

    // Auto calculate total when gram or bb changes
    public function updatedProducts($value, $key)
    {
        // $key format: "0.gram" or "1.bb"
        $parts = explode('.', $key);
        if (count($parts) === 2) {
            $index = (int) $parts[0];
            $field = $parts[1];

            if ($field === 'gram' || $field === 'bb') {
                $gram = floatval($this->products[$index]['gram'] ?? 0);
                $bb = $this->parseRupiah($this->products[$index]['bb'] ?? 0);
                $this->products[$index]['total'] = round($gram * $bb);
            }
        }
    }

    public function removeProduct($index)
    {
        if (count($this->products) > 1) {
            unset($this->products[$index]);
            $this->products = array_values($this->products); // Re-index array
        }
    }

    public function hitungBuyback()
    {
        // Validasi
        $hasValidProduct = false;
        foreach ($this->products as $product) {
            if ($product['gram'] > 0) {
                $hasValidProduct = true;
                break;
            }
        }

        if (!$hasValidProduct) {
            $this->error = 'Minimal 1 produk dengan gramasi lebih dari 0';
            return;
        }

        if ($this->payment <= 0) {
            $this->error = 'Payment harus lebih dari 0';
            return;
        }

        // Validasi buyback price
        $hasBuybackPrice = false;
        foreach ($this->products as $product) {
            $bb = $this->parseRupiah($product['bb'] ?? 0);
            if ($bb > 0) {
                $hasBuybackPrice = true;
                break;
            }
        }
        if (!$hasBuybackPrice) {
            $this->error = 'Minimal 1 produk harus punya harga buyback';
            return;
        }

        $this->error = null;

        // Hitung total buyback dari semua produk
        $this->totalBuyback = 0;
        foreach ($this->products as $index => $product) {
            $gram = floatval($product['gram'] ?? 0);
            $bb = $this->parseRupiah($product['bb'] ?? 0);
            $total = round($gram * $bb);
            $this->products[$index]['total'] = $total;
            $this->totalBuyback += $total;
        }

        // Modal Asli = Payment - Kembalian
        $this->modalAsli = $this->payment - $this->kembalian;

        // Total Keuntungan = Total Buyback - Modal Asli
        $this->totalKeuntungan = $this->totalBuyback - $this->modalAsli;

        $this->showResult = true;

        // Simpan ke riwayat
        $productNames = collect($this->products)->pluck('nama')->filter()->implode(', ') ?: '-';
        $totalGram = collect($this->products)->sum('gram');

        array_unshift($this->riwayat, [
            'waktu' => now()->format('H:i:s'),
            'products' => $this->products,
            'nama_barang' => $productNames,
            'total_gram' => $totalGram,
            'modal_asli' => $this->modalAsli,
            'total_buyback' => $this->totalBuyback,
            'keuntungan' => $this->totalKeuntungan,
            'selling_rate' => $this->sellingRate,
        ]);

        // Batasi riwayat maksimal 20 item
        $this->riwayat = array_slice($this->riwayat, 0, 20);
    }

    public function resetForm()
    {
        $this->products = [
            ['nama' => '', 'gram' => 0.5, 'bb' => 0, 'total' => 0]
        ];
        $this->payment = 0;
        $this->kembalian = 0;
        $this->modalAsli = 0;
        $this->totalBuyback = 0;
        $this->totalKeuntungan = 0;
        $this->showResult = false;
        $this->error = null;
    }

    public function clearRiwayat()
    {
        $this->riwayat = [];
    }

    public function formatCurrency($value)
    {
        return 'Rp ' . number_format($value, 0, ',', '.');
    }

    public function formatNumber($value)
    {
        return number_format($value, 2, ',', '.');
    }

    public function render()
    {
        return view('livewire.buyback-calculator')
            ->extends('layout.template2')
            ->section('container');
    }
}
