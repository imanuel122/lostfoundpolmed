<?php

namespace App\Http\Controllers;

use App\Models\Claim;
use App\Models\Item;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Postingan saya (kecuali yang dihapus)
        $items = Item::where('user_id', $user->id)
            ->where('status', '!=', 'dihapus')
            ->with(['category', 'images'])
            ->withCount(['claims as klaim_menunggu_count' => fn ($q) => $q->where('status', 'menunggu')])
            ->latest()
            ->get();

        // Klaim yang masuk ke postingan saya (yang menunggu di urutan atas)
        $masuk = Claim::whereHas('item', fn ($q) => $q->where('user_id', $user->id))
            ->with(['item.category', 'item.images', 'claimant'])
            ->orderByRaw("status = 'menunggu' desc")
            ->latest()
            ->get();

        // Klaim yang saya ajukan ke postingan orang lain
        $keluar = Claim::where('claimant_id', $user->id)
            ->with(['item.category', 'item.images'])
            ->latest()
            ->get();

        $stat = [
            'aktif'        => $items->whereIn('status', ['aktif', 'verifikasi'])->count(),
            'perlu'        => $masuk->where('status', 'menunggu')->count(),
            'dikembalikan' => $items->where('status', 'dikembalikan')->count(),
            'klaimSaya'    => $keluar->count(),
        ];

        return view('dashboard', compact('items', 'masuk', 'keluar', 'stat'));
    }
}