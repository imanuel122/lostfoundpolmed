<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminMasterController extends Controller
{
    // Nama ikon yang tersedia di components/icon.blade.php untuk kategori
    public const ICONS = [
        'wallet',
        'smartphone',
        'key-round',
        'credit-card',
        'backpack',
        'laptop',
        'cup-soda',
        'book-open',
        'shirt',
        'tag',
        'package'
    ];

    private function cfg(string $type): array
    {
        return match ($type) {
            'kategori' => ['model' => Category::class, 'table' => 'categories', 'judul' => 'Kategori', 'ikon' => true,  'max' => 50],
            'lokasi'   => ['model' => Location::class, 'table' => 'locations',  'judul' => 'Lokasi',   'ikon' => false, 'max' => 100],
        };
    }

    public function index(string $type)
    {
        $c = $this->cfg($type);

        return view('admin.master', [
            'type'     => $type,
            'judul'    => $c['judul'],
            'pakaiIkon' => $c['ikon'],
            'ikonList' => self::ICONS,
            'rows'     => $c['model']::withCount('items')->orderBy('nama')->get(),
        ]);
    }

    public function store(Request $request, string $type)
    {
        $c = $this->cfg($type);

        $data = $request->validate([
            'nama' => ['required', 'string', 'max:' . $c['max'], 'unique:' . $c['table'] . ',nama'],
            'ikon' => [$c['ikon'] ? 'required' : 'nullable', Rule::in(self::ICONS)],
        ], [
            'nama.required' => 'Nama wajib diisi.',
            'nama.unique'   => 'Nama ini sudah ada.',
            'ikon.required' => 'Pilih ikon.',
        ]);

        $c['model']::create($c['ikon'] ? $data : ['nama' => $data['nama']]);

        return back()->with('success', $c['judul'] . ' ditambahkan.');
    }

    public function update(Request $request, string $type, int $id)
    {
        $c   = $this->cfg($type);
        $row = $c['model']::findOrFail($id);

        $data = $request->validate([
            'nama' => ['required', 'string', 'max:' . $c['max'], 'unique:' . $c['table'] . ',nama,' . $row->id],
            'ikon' => [$c['ikon'] ? 'required' : 'nullable', Rule::in(self::ICONS)],
        ], [
            'nama.required' => 'Nama wajib diisi.',
            'nama.unique'   => 'Nama ini sudah ada.',
        ]);

        $row->update($c['ikon'] ? $data : ['nama' => $data['nama']]);

        return back()->with('success', $c['judul'] . ' diperbarui.');
    }

    public function destroy(string $type, int $id)
    {
        $c   = $this->cfg($type);
        $row = $c['model']::findOrFail($id);

        if ($row->nama === 'Lainnya') {
            return back()->with('success', '"Lainnya" dipakai sebagai cadangan dan tidak bisa dihapus.');
        }

        if ($row->items()->exists()) {
            return back()->with('success', 'Tidak bisa dihapus karena masih dipakai postingan.');
        }

        $row->delete();

        return back()->with('success', $c['judul'] . ' dihapus.');
    }
}
