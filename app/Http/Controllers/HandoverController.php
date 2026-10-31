<?php

namespace App\Http\Controllers;

use App\Models\Claim;
use App\Models\HandoverImage;
use App\Models\User;
use App\Support\PhotoName;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Support\Notify;

class HandoverController extends Controller
{
    // Pemosting mengunggah foto bukti serah terima
    public function store(Request $request, Claim $claim)
    {
        $claim->load('item');
        $this->izinkanPemosting($request->user(), $claim);

        if ($claim->status !== 'disetujui' || $claim->handed_over_at) {
            return back()->with('success', 'Bukti serah terima untuk klaim ini tidak bisa dikirim lagi.');
        }

        $request->validate([
            'photos'      => ['required', 'array', 'min:1', 'max:3'],
            'photos.*'    => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'keterangan'  => ['nullable', 'string', 'max:150'],
            'privacy_ack' => ['accepted'],
        ], [
            'photos.required'      => 'Unggah minimal 1 foto bukti serah terima.',
            'photos.min'           => 'Unggah minimal 1 foto bukti serah terima.',
            'photos.max'           => 'Maksimal 3 foto.',
            'photos.*.image'       => 'File harus berupa gambar.',
            'photos.*.mimes'       => 'Format foto harus JPG, PNG, atau WEBP.',
            'photos.*.max'         => 'Ukuran tiap foto maksimal 5 MB.',
            'privacy_ack.accepted' => 'Centang pernyataan privasi sebelum mengirim foto.',
        ]);

        DB::transaction(function () use ($request, $claim) {
            $mulai = $claim->handoverImages()->count(); // lanjut penomoran kalau unggah ulang

            foreach ($request->file('photos') as $i => $file) {
                $urutan = $mulai + $i + 1;
                $nama   = PhotoName::make('serah-terima', $claim->item->judul, $claim->id, $urutan, $file);

                HandoverImage::create([
                    'item_id'     => $claim->item_id,
                    'claim_id'    => $claim->id,
                    'uploaded_by' => $request->user()->id,
                    'path_foto'   => $file->storeAs('handovers/' . now()->format('Y/m'), $nama, 'local'),
                    'keterangan'  => $request->input('keterangan'),
                ]);
            }

            $claim->update(['handed_over_at' => now(), 'dispute_note' => null]);
        });

        $temuan = $claim->item->jenis === 'temuan';
        $nama   = $claim->item->user->name;

        Notify::to(
            $claim->claimant,
            'Bukti serah terima diunggah',
            $temuan
                ? $nama . ' (penemu) mengunggah bukti bahwa "' . $claim->item->judul . '" sudah diserahkan kepadamu. Mohon konfirmasi bahwa kamu sudah menerimanya.'
                : $nama . ' (pemilik) mengunggah bukti bahwa "' . $claim->item->judul . '" sudah diterima darimu. Mohon konfirmasi bahwa kamu sudah menyerahkannya.',
            route('claims.show', $claim),
            'package-check',
            'amber'
        );

        return back()->with('success', 'Bukti serah terima terkirim. Menunggu konfirmasi dari pengklaim.');
    }

    // Pihak lawan mengonfirmasi, lalu semuanya selesai
    public function confirm(Request $request, Claim $claim)
    {
        $claim->load('item.user', 'claimant');
        $user = $request->user();

        $pengklaim  = $user->id === $claim->claimant_id;
        $pemosting  = $user->id === $claim->item->user_id;
        $otoSelesai = $pemosting && $claim->handed_over_at && $claim->handed_over_at->lte(now()->subDays(3));

        abort_unless($pengklaim || $user->isAdmin() || $otoSelesai, 403);

        if ($claim->status !== 'disetujui' || ! $claim->handed_over_at) {
            return back()->with('success', 'Klaim ini belum siap dikonfirmasi atau sudah selesai.');
        }

        $lain = $claim->item->claims()
            ->where('id', '!=', $claim->id)
            ->where('status', 'menunggu')
            ->with('claimant')->get();

        DB::transaction(function () use ($claim, $user, $lain) {
            $claim->update(['status' => 'selesai', 'confirmed_at' => now()]);

            $claim->item->update([
                'status'             => 'dikembalikan',
                'completed_at'       => now(),
                'completed_claim_id' => $claim->id,
            ]);

            foreach ($lain as $k) {
                $k->update([
                    'status'        => 'ditolak',
                    'catatan_admin' => 'Barang ini sudah diserahkan kepada pengklaim lain.',
                    'reviewed_by'   => $user->id,
                    'reviewed_at'   => now(),
                ]);
            }
        });

        $judul = $claim->item->judul;
        $url   = route('claims.show', $claim);

        // Beri tahu pihak yang BUKAN penekan tombol
        $temuan = $claim->item->jenis === 'temuan';

        if ($user->id !== $claim->item->user_id) {
            Notify::to(
                $claim->item->user,
                'Serah terima selesai',
                $claim->claimant->name . ($temuan
                    ? ' (pemilik) mengonfirmasi sudah menerima "'
                    : ' (penemu) mengonfirmasi sudah menyerahkan "')
                    . $judul . '". Terima kasih sudah membantu!',
                $url,
                'circle-check',
                'blue'
            );
        }
        if ($user->id !== $claim->claimant_id) {
            Notify::to(
                $claim->claimant,
                'Serah terima selesai',
                'Serah terima "' . $judul . '" dinyatakan selesai oleh '
                    . ($user->isAdmin() ? 'admin' : $claim->item->user->name) . '.',
                $url,
                'circle-check',
                'blue'
            );
        }
        foreach ($lain as $k) {
            Notify::to(
                $k->claimant,
                'Klaim ditolak',
                '"' . $judul . '" sudah diserahkan kepada pengklaim lain.',
                route('claims.show', $k),
                'circle-x',
                'rose'
            );
        }

        return back()->with('success', 'Serah terima selesai. Terima kasih sudah saling membantu!');
    }

    // Pengklaim menyatakan belum menerima
    public function dispute(Request $request, Claim $claim)
    {
        $claim->load('item');
        abort_unless($request->user()->id === $claim->claimant_id, 403);

        $data = $request->validate([
            'dispute_note' => ['required', 'string', 'min:5', 'max:300'],
        ], [
            'dispute_note.required' => 'Tulis alasannya.',
            'dispute_note.min'      => 'Alasan terlalu singkat (minimal 5 karakter).',
        ]);

        if ($claim->status === 'disetujui' && $claim->handed_over_at) {
            $claim->update(['handed_over_at' => null, 'dispute_note' => $data['dispute_note']]);
            $temuan = $claim->item->jenis === 'temuan';

            Notify::to(
                $claim->item->user,
                $temuan ? 'Pemilik belum menerima barang' : 'Penemu belum menyerahkan barang',
                $claim->claimant->name . ($temuan
                    ? ' (pemilik) menyatakan belum menerima "'
                    : ' (penemu) menyatakan belum menyerahkan "')
                    . $claim->item->judul . '": ' . $data['dispute_note'],
                route('claims.show', $claim),
                'triangle-alert',
                'rose'
            );
        }

        return back()->with('success', 'Laporanmu terkirim. Pembuat postingan diminta mengunggah ulang bukti serah terima.');
    }

    // Menyajikan foto bukti yang privat
    public function image(Request $request, HandoverImage $image)
    {
        $claim = $image->claim()->with('item')->firstOrFail();
        $this->izinkanLihat($request->user(), $claim);

        abort_unless(Storage::disk('local')->exists($image->path_foto), 404);

        return response()->file(
            Storage::disk('local')->path($image->path_foto),
            ['Cache-Control' => 'no-store, private']
        );
    }

    /* ---------- helper ---------- */

    private function izinkanPemosting(?User $user, Claim $claim): void
    {
        $boleh = $user
            && $user->id !== $claim->claimant_id
            && ($user->id === $claim->item->user_id || $user->isAdmin());

        abort_unless($boleh, 403);
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
}
