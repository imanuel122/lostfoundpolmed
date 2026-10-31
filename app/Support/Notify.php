<?php

namespace App\Support;

use App\Models\User;
use App\Notifications\AppNotification;

class Notify
{
    /**
     * @param string $warna emerald | amber | rose | blue | slate
     */
    public static function to(?User $user, string $judul, string $pesan, string $url, string $ikon = 'bell', string $warna = 'slate'): void
    {
        if (! $user) {
            return;
        }

        // Simpan path saja (bukan domain), supaya tetap benar kalau domain berubah
        $url = parse_url($url, PHP_URL_PATH) ?: '/';

        $user->notify(new AppNotification(compact('judul', 'pesan', 'url', 'ikon', 'warna')));
    }
}
