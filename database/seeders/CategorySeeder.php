<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $kategori = [
            ['nama' => 'Dompet',              'ikon' => 'wallet'],
            ['nama' => 'HP / Gadget',         'ikon' => 'smartphone'],
            ['nama' => 'Kunci',               'ikon' => 'key-round'],
            ['nama' => 'KTM / Kartu',         'ikon' => 'credit-card'],
            ['nama' => 'Tas',                 'ikon' => 'backpack'],
            ['nama' => 'Laptop / Elektronik', 'ikon' => 'laptop'],
            ['nama' => 'Botol / Tumbler',     'ikon' => 'cup-soda'],
            ['nama' => 'Alat Tulis / Buku',   'ikon' => 'book-open'],
            ['nama' => 'Pakaian / Jaket',     'ikon' => 'shirt'],
            ['nama' => 'Lainnya',             'ikon' => 'package'],
        ];

        foreach ($kategori as $k) {
            // updateOrCreate: baris lama (yang masih emoji) ikut diperbarui
            Category::updateOrCreate(['nama' => $k['nama']], $k);
        }
    }
}
