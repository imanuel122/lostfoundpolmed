<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        $lokasi = [
            'Gedung Direktorat',
            'Perpustakaan',
            'Kantin',
            'Masjid / Musholla',
            'Parkiran',
            'Lapangan / Area Olahraga',
            'Jurusan Teknik Sipil',
            'Jurusan Teknik Mesin',
            'Jurusan Teknik Elektro',
            'Jurusan Akuntansi',
            'Jurusan Administrasi Niaga',
            'Jurusan Teknik Komputer & Informatika',
            'Laboratorium',
            'Toilet',
            'Lainnya',
        ];

        foreach ($lokasi as $nama) {
            Location::firstOrCreate(['nama' => $nama]);
        }
    }
}
