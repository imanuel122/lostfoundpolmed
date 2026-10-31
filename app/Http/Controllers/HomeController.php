<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use App\Models\Location;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $jenis = $request->query('jenis'); // temuan / hilang / kembali / kosong = semua
        if (! in_array($jenis, ['temuan', 'hilang', 'kembali'], true)) {
            $jenis = null;
        }

        $query = Item::with(['category', 'location', 'images'])
            ->when(
                $jenis === 'kembali',
                // Tab "Sudah Kembali": yang paling baru selesai di atas
                fn($q) => $q->where('status', 'dikembalikan')->orderByDesc('completed_at')->orderByDesc('id'),
                // Tab lain: hanya yang masih berjalan
                fn($q) => $q->whereIn('status', ['aktif', 'verifikasi'])
                    ->when($jenis, fn($w) => $w->where('jenis', $jenis))
                    ->latest()
            )
            ->when($request->filled('q'), fn($q) => $q->where('judul', 'like', '%' . $request->q . '%'))
            ->when($request->filled('kategori'), fn($q) => $q->where('category_id', $request->kategori))
            ->when($request->filled('lokasi'), fn($q) => $q->where('location_id', $request->lokasi));

        $items = $query->paginate(12)->withQueryString();

        $berjalan = Item::whereIn('status', ['aktif', 'verifikasi']);

        $hitung = [
            'semua'   => (clone $berjalan)->count(),
            'temuan'  => (clone $berjalan)->where('jenis', 'temuan')->count(),
            'hilang'  => (clone $berjalan)->where('jenis', 'hilang')->count(),
            'kembali' => Item::where('status', 'dikembalikan')->count(),
        ];

        return view('home', [
            'items'        => $items,
            'jenis'        => $jenis,
            'hitung'       => $hitung,
            'totalKembali' => $hitung['kembali'],
            'categories'   => Category::orderBy('nama')->get(),
            'locations'    => Location::orderBy('nama')->get(),
        ]);
    }
}
