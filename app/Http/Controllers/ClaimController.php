<?php

namespace App\Http\Controllers;

use App\Models\Claim;
use App\Models\ClaimImage;
use App\Models\Item;
use App\Models\User;
use App\Support\PhotoName;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Support\Notify;

class ClaimController extends Controller
{
    public function create(Request $request, Item $item)
    {
        if ($tolak = $this->cekBolehKlaim($request, $item)) {
            return $tolak;
        }

        return view('claims.create', [
            'item' => $item->load(['category', 'location', 'images']),
        ]);
    }

    public function store(Request $request, Item $item)
    {
        if ($tolak = $this->cekBolehKlaim($request, $item)) {
            return $tolak;
        }

        $temuan = $item->jenis === 'temuan';

        $data = $request->validate([
            'bukti_deskripsi'        => ['required', 'string', 'min:20', 'max:1000'],
            'lokasi_barang_sekarang' => [$temuan ? 'nullable' : 'required', 'string', 'max:150'],
            'photos'                 => ['nullable', 'array', 'max:5'],
            'photos.*'               => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'privacy_ack'            => $request->hasFile('photos') ? ['accepted'] : ['nullable'],
        ], [
            'bukti_deskripsi.required'        => 'Jelaskan ciri-ciri barang sebagai bukti.',
            'bukti_deskripsi.min'             => 'Penjelasan terlalu singkat (minimal 20 karakter).',
            'lokasi_barang_sekarang.required' => 'Tulis barang itu sekarang ada di mana.',
            'photos.max'                      => 'Maksimal 5 foto.',
            'photos.*.image'                  => 'File harus berupa gambar.',
            'photos.*.mimes'                  => 'Format foto harus JPG, PNG, atau WEBP.',
            'photos.*.max'                    => 'Ukuran tiap foto maksimal 5 MB.',
            'privacy_ack.accepted'            => 'Centang pernyataan privasi sebelum mengirim foto.',
        ]);

        $claim = DB::transaction(function () use ($request, $item, $data, $temuan) {
            $claim = Claim::create([
                'item_id'                => $item->id,
                'claimant_id'            => $request->user()->id,
                'jenis_klaim'            => $temuan ? 'klaim_pemilik' : 'lapor_temuan',
                'bukti_deskripsi'        => $data['bukti_deskripsi'],
                'lokasi_barang_sekarang' => $temuan ? null : ($data['lokasi_barang_sekarang'] ?? null),
                'status'                 => 'menunggu',
            ]);

            // Foto klaim disimpan di disk PRIVAT (storage/app/private), bukan public
            foreach ($request->file('photos', []) as $i => $file) {
                $nama = PhotoName::make('klaim', $item->judul, $claim->id, $i + 1, $file);

                $claim->images()->create([
                    'path_foto' => $file->storeAs('claims/' . now()->format('Y/m'), $nama, 'local'),
                    'urutan'    => $i + 1,
                ]);
            }

            if ($item->status === 'aktif') {
                $item->update(['status' => 'verifikasi']);
            }

            return $claim;
        });

        Notify::to(
            $item->user,
            $temuan ? 'Klaim baru dari pemilik' : 'Laporan baru dari penemu',
            $request->user()->name . ($temuan
                ? ' (pemilik) mengklaim "' . $item->judul . '". Periksa buktinya.'
                : ' (penemu) melaporkan menemukan "' . $item->judul . '". Periksa buktinya.'),
            route('claims.show', $claim),
            'inbox',
            'amber'
        );

        return redirect()->route('claims.show', $claim)->with(
            'success',
            $temuan
                ? 'Klaim terkirim. Tunggu penemu memeriksa buktimu.'
                : 'Laporanmu terkirim. Tunggu pemilik barang memeriksanya.'
        );
    }

    public function show(Request $request, Claim $claim)
    {
        $user = $request->user();
        $claim->load(['item.category', 'item.location', 'item.images', 'item.user', 'claimant', 'images', 'handoverImages.uploader']);

        $this->izinkanLihat($user, $claim);

        $pengklaim = $user->id === $claim->claimant_id;

        return view('claims.show', [
            'claim'       => $claim,
            'pengklaim'   => $pengklaim,
            'lawan'       => $pengklaim ? $claim->item->user : $claim->claimant,
            'bolehReview' => ! $pengklaim && ($user->id === $claim->item->user_id || $user->isAdmin()),
        ]);
    }

    // Menyajikan foto klaim yang privat setelah memeriksa hak akses
    public function image(Request $request, ClaimImage $image)
    {
        $claim = $image->claim()->with('item')->firstOrFail();
        $this->izinkanLihat($request->user(), $claim);

        abort_unless(Storage::disk('local')->exists($image->path_foto), 404);

        return response()->file(
            Storage::disk('local')->path($image->path_foto),
            ['Cache-Control' => 'no-store, private']
        );
    }

    public function approve(Request $request, Claim $claim)
    {
        $claim->load('item');
        $this->izinkanReview($request->user(), $claim);

        if ($claim->status !== 'menunggu') {
            return back()->with('success', 'Klaim ini sudah diproses sebelumnya.');
        }

        $sudahAda = Claim::where('item_id', $claim->item_id)
            ->where('status', 'disetujui')->exists();

        if ($sudahAda) {
            return back()->with('success', 'Sudah ada klaim lain yang disetujui untuk barang ini.');
        }

        $claim->update([
            'status'      => 'disetujui',
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        $temuan = $claim->item->jenis === 'temuan';
        $nama   = $claim->item->user->name;

        Notify::to(
            $claim->claimant,
            $temuan ? 'Klaim disetujui' : 'Laporan penemuanmu disetujui',
            $temuan
                ? 'Klaimmu untuk "' . $claim->item->judul . '" disetujui. Hubungi ' . $nama . ' (penemu) untuk mengambil barangmu.'
                : 'Laporanmu untuk "' . $claim->item->judul . '" disetujui. Hubungi ' . $nama . ' (pemilik) untuk mengembalikan barangnya.',
            route('claims.show', $claim),
            'circle-check',
            'emerald'
        );

        return back()->with('success', 'Klaim disetujui. Kamu dan ' . $claim->claimant->name . ' sekarang bisa saling menghubungi lewat WhatsApp.');
    }

    public function reject(Request $request, Claim $claim)
    {
        $claim->load('item');
        $this->izinkanReview($request->user(), $claim);

        $data = $request->validate([
            'catatan_admin' => ['required', 'string', 'min:5', 'max:300'],
        ], [
            'catatan_admin.required' => 'Tulis alasan penolakan.',
            'catatan_admin.min'      => 'Alasan terlalu singkat (minimal 5 karakter).',
        ]);

        if ($claim->status !== 'menunggu') {
            return back()->with('success', 'Klaim ini sudah diproses sebelumnya.');
        }

        DB::transaction(function () use ($request, $claim, $data) {
            $claim->update([
                'status'        => 'ditolak',
                'catatan_admin' => $data['catatan_admin'],
                'reviewed_by'   => $request->user()->id,
                'reviewed_at'   => now(),
            ]);

            // Kalau tidak ada klaim lain yang berjalan, barang kembali "aktif"
            $item = $claim->item;
            $masihAda = $item->claims()->whereIn('status', ['menunggu', 'disetujui'])->exists();

            if ($item->status === 'verifikasi' && ! $masihAda) {
                $item->update(['status' => 'aktif']);
            }
        });

        $temuan = $claim->item->jenis === 'temuan';

        Notify::to(
            $claim->claimant,
            $temuan ? 'Klaim ditolak' : 'Laporan penemuanmu ditolak',
            ($temuan ? 'Klaimmu' : 'Laporanmu') . ' untuk "' . $claim->item->judul . '" ditolak oleh ' . $claim->item->user->name
                . ' (' . ($temuan ? 'penemu' : 'pemilik') . '). Alasan: ' . $data['catatan_admin'],
            route('claims.show', $claim),
            'circle-x',
            'rose'
        );

        return back()->with('success', 'Klaim ditolak.');
    }

    /* ---------- helper ---------- */

    private function cekBolehKlaim(Request $request, Item $item)
    {
        $user = $request->user();

        if ($item->user_id === $user->id) {
            return redirect()->route('items.show', $item)
                ->with('success', 'Ini postinganmu sendiri, jadi tidak bisa mengajukan klaim.');
        }

        if (! in_array($item->status, ['aktif', 'verifikasi'])) {
            return redirect()->route('items.show', $item)
                ->with('success', 'Postingan ini sudah tidak menerima klaim.');
        }

        $ada = $item->claims()->where('claimant_id', $user->id)
            ->whereIn('status', ['menunggu', 'disetujui'])->latest()->first();

        if ($ada) {
            return redirect()->route('claims.show', $ada)
                ->with('success', 'Kamu sudah mengajukan klaim untuk barang ini.');
        }

        return null;
    }

    private function izinkanLihat(?User $user, Claim $claim): void
    {
        $boleh = $user && (
            $user->id === $claim->claimant_id
            || $user->id === $claim->item->user_id
            || $user->isAdmin()
        );

        abort_unless($boleh, 403);
    }

    // Hanya pembuat postingan atau admin, dan bukan pengklaimnya sendiri
    private function izinkanReview(?User $user, Claim $claim): void
    {
        $boleh = $user
            && $user->id !== $claim->claimant_id
            && ($user->id === $claim->item->user_id || $user->isAdmin());

        abort_unless($boleh, 403);
    }
}
