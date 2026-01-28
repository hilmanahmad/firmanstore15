<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;

class BuybackMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cek apakah menu sudah ada
        $exists = Menu::where('url', 'buyback-calculator')->first();

        if (!$exists) {
            // Cari sort terakhir
            $lastSort = Menu::where('is_header', 'true')->max('sort') ?? 0;

            Menu::create([
                'id' => (string) \Illuminate\Support\Str::uuid(),
                'name' => 'Cek Buyback',
                'is_header' => 'true',
                'parent' => null,
                'url' => 'buyback-calculator',
                'icon' => 'ri-money-dollar-circle-line',
                'have_sub_menu' => 'false',
                'sort' => $lastSort + 1,
            ]);

            $this->command->info('Menu Cek Buyback berhasil ditambahkan!');
        } else {
            $this->command->info('Menu Cek Buyback sudah ada.');
        }
    }
}
