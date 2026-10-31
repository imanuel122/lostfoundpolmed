<?php

namespace App\Console\Commands;

use App\Models\Item;
use App\Support\Notify;
use Illuminate\Console\Command;

class ExpireItems extends Command
{
    protected $signature = 'items:expire';
    protected $description = 'Ubah postingan yang melewati batas waktu menjadi kedaluwarsa';

    public function handle(): int
    {
        // Hanya yang masih "aktif". Yang sedang diverifikasi tidak ikut kedaluwarsa.
        $items = Item::with('user')
            ->where('status', 'aktif')
            ->whereDate('expires_at', '<', today())
            ->get();

        foreach ($items as $item) {
            $item->update(['status' => 'kedaluwarsa']);

            Notify::to(
                $item->user,
                'Postingan kedaluwarsa',
                '"' . $item->judul . '" melewati batas 60 hari tanpa klaim.',
                route('items.show', $item),
                'clock',
                'slate'
            );
        }

        $this->info($items->count() . ' postingan diubah menjadi kedaluwarsa.');

        return self::SUCCESS;
    }
}
