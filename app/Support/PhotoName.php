<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class PhotoName
{
    /**
     * Contoh hasil: temuan-dompet-hitam-15-01-k3x9ab.jpg
     *
     * @param string $prefix  temuan / hilang / klaim / serah-terima
     * @param string $judul   nama barang (akan dijadikan slug)
     * @param int    $id      ID postingan (atau ID klaim)
     * @param int    $urutan  foto ke berapa, mulai dari 1
     */
    public static function make(string $prefix, string $judul, int $id, int $urutan, UploadedFile $file): string
    {
        $slug = Str::slug(Str::limit($judul, 40, '')) ?: 'barang';
        $kode = Str::lower(Str::random(6));

        // Ekstensi dari isi file, bukan dari nama file buatan pengguna
        $ext = strtolower($file->extension() ?: 'jpg');
        if ($ext === 'jpeg') {
            $ext = 'jpg';
        }

        return sprintf('%s-%s-%d-%02d-%s.%s', $prefix, $slug, $id, $urutan, $kode, $ext);
    }
}
