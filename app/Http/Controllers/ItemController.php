<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Support\PhotoName;

class ItemController extends Controller
{
    // Pilihan tempat penyimpanan untuk barang temuan
    public const PENYIMPANAN = ['Saya bawa sendiri', 'Pos satpam', 'Bagian kemahasiswaan'];

    public function create(string $jenis)
    {
        return view('items.create', [
            'jenis'       => $jenis,
            'categories'  => Category::orderBy('id')->get(),
            // "Lainnya" selalu di urutan terakhir
            'locations'   => Location::orderByRaw("nama = 'Lainnya'")->orderBy('nama')->get(),
            'penyimpanan' => self::PENYIMPANAN,
        ]);
    }

    public function store(Request $request, string $jenis)
    {
        $temuan = $jenis === 'temuan';

        $data = $request->validate([
            'judul'              => ['required', 'string', 'max:150'],
            'category_id'        => ['required', 'exists:categories,id'],
            'location_id'        => ['required', 'exists:locations,id'],
            'detail_lokasi'      => ['nullable', 'string', 'max:200'],
            'tanggal_kejadian'   => ['required', 'date', 'before_or_equal:today'],
            'deskripsi'          => ['nullable', 'string', 'max:1000'],
            'ciri_rahasia'       => [$temuan ? 'required' : 'nullable', 'string', 'max:1000'],
            'lokasi_penyimpanan' => [$temuan ? 'required' : 'nullable', Rule::in(self::PENYIMPANAN)],
            'photos'             => ['nullable', 'array', 'max:5'],
            'photos.*'           => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            // Wajib dicentang hanya kalau ada foto yang diunggah
            'privacy_ack'        => $request->hasFile('photos') ? ['accepted'] : ['nullable'],
        ], [
            'judul.required'              => 'Nama barang wajib diisi.',
            'category_id.required'        => 'Pilih salah satu kategori.',
            'location_id.required'        => 'Pilih lokasi.',
            'tanggal_kejadian.required'   => 'Tanggal wajib diisi.',
            'tanggal_kejadian.before_or_equal' => 'Tanggal tidak boleh di masa depan.',
            'ciri_rahasia.required'       => 'Isi ciri rahasia untuk memverifikasi pemilik asli.',
            'lokasi_penyimpanan.required' => 'Pilih di mana barang disimpan.',
            'photos.max'                  => 'Maksimal 5 foto.',
            'photos.*.image'              => 'File harus berupa gambar.',
            'photos.*.mimes'              => 'Format foto harus JPG, PNG, atau WEBP.',
            'photos.*.max'                => 'Ukuran tiap foto maksimal 5 MB.',
            'privacy_ack.accepted' => 'Centang pernyataan privasi sebelum memposting foto.',
        ]);

        $item = DB::transaction(function () use ($request, $data, $jenis, $temuan) {
            $item = Item::create([
                'user_id'            => $request->user()->id,
                'jenis'              => $jenis,
                'judul'              => $data['judul'],
                'category_id'        => $data['category_id'],
                'location_id'        => $data['location_id'],
                'detail_lokasi'      => $data['detail_lokasi'] ?? null,
                'tanggal_kejadian'   => $data['tanggal_kejadian'],
                'deskripsi'          => $data['deskripsi'] ?? null,
                'ciri_rahasia'       => $data['ciri_rahasia'] ?? null,
                'lokasi_penyimpanan' => $temuan ? $data['lokasi_penyimpanan'] : null,
                'status'             => 'aktif',
                'expires_at'         => now()->addDays(60),
            ]);

            // store() memberi nama file acak, hanya path yang disimpan ke database
            foreach ($request->file('photos', []) as $i => $file) {
                $nama = PhotoName::make($jenis, $data['judul'], $item->id, $i + 1, $file);

                $item->images()->create([
                    'path_foto' => $file->storeAs('items/' . now()->format('Y/m'), $nama, 'public'),
                    'urutan'    => $i + 1,
                ]);
            }

            return $item;
        });

        return redirect()->route('home')->with(
            'success',
            $temuan
                ? 'Barang temuan berhasil diposting. Terima kasih sudah membantu!'
                : 'Laporan kehilangan berhasil diposting. Semoga cepat ketemu!'
        );
    }

    public function show(Request $request, Item $item)
    {
        $user    = $request->user();
        $pemilik = $user && $user->id === $item->user_id;
        $admin   = $user && $user->isAdmin();

        // Postingan yang dihapus hanya boleh dilihat pemilik dan admin
        if ($item->status === 'dihapus' && ! $pemilik && ! $admin) {
            abort(404);
        }

        // Tamu yang klik "login" akan kembali ke halaman ini setelah masuk
        if (! $user) {
            session()->put('url.intended', url()->current());
        }

        $item->load(['category', 'location', 'images', 'user']);

        // Sudah pernah mengajukan klaim yang masih berjalan?
        $sudahKlaim = $user && ! $pemilik
            ? $item->claims()->where('claimant_id', $user->id)
            ->whereIn('status', ['menunggu', 'disetujui'])->latest()->first()
            : null;

        // Info untuk pemilik postingan
        $klaimMenunggu = $pemilik
            ? $item->claims()->where('status', 'menunggu')->count()
            : 0;

        // Barang berlawanan jenis dengan kategori sama = kandidat kecocokan
        $berkaitan = Item::with(['category', 'location', 'images'])
            ->whereIn('status', ['aktif', 'verifikasi'])
            ->where('jenis', $item->jenis === 'temuan' ? 'hilang' : 'temuan')
            ->where('category_id', $item->category_id)
            ->where('id', '!=', $item->id)
            ->latest()
            ->limit(4)
            ->get();

        $daftarKlaim = $pemilik
            ? $item->claims()->with('claimant')->latest()->get()
            : collect();

        // Klaim yang sudah disetujui/selesai (untuk menentukan tampilan)
        $klaimDisetujui = $item->claims()->whereIn('status', ['disetujui', 'selesai'])
            ->with('claimant')->latest()->first();

        return view('items.show', [
            'item'          => $item,
            'pemilik'       => $pemilik,
            'admin'         => $admin,
            'sudahKlaim'    => $sudahKlaim,
            'klaimMenunggu' => $klaimMenunggu,
            'berkaitan'     => $berkaitan,
            'daftarKlaim' => $daftarKlaim,
            'klaimDisetujui' => $klaimDisetujui,
        ]);
    }
}
