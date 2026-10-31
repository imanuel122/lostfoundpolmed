<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Claim;
use App\Models\Item;
use App\Models\Location;
use App\Models\User;
use App\Support\Notify;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class AdminController extends Controller
{
    public function index()
    {
        $total   = Item::where('status', '!=', 'dihapus')->count();
        $kembali = Item::where('status', 'dikembalikan')->count();

        $stat = [
            'total'    => $total,
            'aktif'    => Item::whereIn('status', ['aktif', 'verifikasi'])->count(),
            'kembali'  => $kembali,
            'menunggu' => Claim::where('status', 'menunggu')->count(),
            'users'    => User::count(),
            'tingkat'  => $total > 0 ? round($kembali / $total * 100) : 0,
        ];

        // Klaim yang macet: bukti diunggah >3 hari belum dikonfirmasi,
        // atau disetujui >7 hari tapi belum ada bukti serah terima
        $macet = Claim::with(['item', 'claimant'])
            ->where('status', 'disetujui')
            ->where(function ($q) {
                $q->where(fn($w) => $w->whereNotNull('handed_over_at')->where('handed_over_at', '<=', now()->subDays(3)))
                    ->orWhere(fn($w) => $w->whereNull('handed_over_at')->where('reviewed_at', '<=', now()->subDays(7)));
            })
            ->latest()->get();

        return view('admin.index', [
            'stat'        => $stat,
            'macet'       => $macet,
            'terbaru'     => Item::with(['category', 'user'])->latest()->limit(6)->get(),
            'topLokasi'   => Location::withCount('items')->orderByDesc('items_count')->limit(5)->get(),
            'topKategori' => Category::withCount('items')->orderByDesc('items_count')->limit(5)->get(),
        ]);
    }

    /* ---------- POSTINGAN ---------- */

    public function items(Request $request)
    {
        $items = Item::with(['category', 'location', 'user'])
            ->withCount('claims')
            ->when($request->filled('q'), fn($q) => $q->where('judul', 'like', '%' . $request->q . '%'))
            ->when($request->filled('jenis'), fn($q) => $q->where('jenis', $request->jenis))
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(15)->withQueryString();

        return view('admin.items', compact('items'));
    }

    public function destroyItem(Request $request, Item $item)
    {
        $data = $request->validate([
            'catatan' => ['required', 'string', 'min:5', 'max:300'],
        ], [
            'catatan.required' => 'Tulis alasan penghapusan.',
            'catatan.min'      => 'Alasan terlalu singkat (minimal 5 karakter).',
        ]);

        if ($item->status === 'dihapus') {
            return back()->with('success', 'Postingan ini sudah dihapus.');
        }

        $item->update(['status' => 'dihapus']);

        // Klaim yang masih menunggu ikut ditolak
        $item->claims()->where('status', 'menunggu')->update([
            'status'        => 'ditolak',
            'catatan_admin' => 'Postingan dihapus oleh admin.',
            'reviewed_by'   => $request->user()->id,
            'reviewed_at'   => now(),
        ]);

        Notify::to(
            $item->user,
            'Postingan dihapus admin',
            '"' . $item->judul . '" dihapus. Alasan: ' . $data['catatan'],
            route('dashboard'),
            'trash-2',
            'rose'
        );

        return back()->with('success', 'Postingan dihapus. Pembuatnya sudah diberi tahu.');
    }

    public function restoreItem(Item $item)
    {
        if ($item->status !== 'dihapus') {
            return back()->with('success', 'Postingan ini tidak dalam keadaan dihapus.');
        }

        $status = $item->completed_at
            ? 'dikembalikan'
            : ($item->claims()->whereIn('status', ['menunggu', 'disetujui'])->exists() ? 'verifikasi' : 'aktif');

        $item->update(['status' => $status]);

        Notify::to(
            $item->user,
            'Postingan dipulihkan',
            '"' . $item->judul . '" ditampilkan kembali.',
            route('items.show', $item),
            'rotate-ccw',
            'emerald'
        );

        return back()->with('success', 'Postingan dipulihkan.');
    }

    /* ---------- KLAIM ---------- */

    public function claims(Request $request)
    {
        $claims = Claim::with(['item', 'claimant'])
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
            ->when($request->filled('q'), fn($q) => $q->whereHas('item', fn($w) => $w->where('judul', 'like', '%' . $request->q . '%')))
            ->latest()
            ->paginate(15)->withQueryString();

        return view('admin.claims', compact('claims'));
    }

    /* ---------- PENGGUNA ---------- */

    public function users(Request $request)
    {
        $users = User::withCount(['items', 'claims'])
            ->when($request->filled('q'), function ($q) use ($request) {
                $q->where(fn($w) => $w
                    ->where('name', 'like', '%' . $request->q . '%')
                    ->orWhere('email', 'like', '%' . $request->q . '%')
                    ->orWhere('nim', 'like', '%' . $request->q . '%'));
            })
            ->when($request->filled('role'), fn($q) => $q->where('role', $request->role))
            ->latest()
            ->paginate(20)->withQueryString();

        return view('admin.users', compact('users'));
    }

    public function toggleRole(Request $request, User $user)
    {
        if ($user->id === $request->user()->id) {
            return back()->with('success', 'Kamu tidak bisa mengubah perannya sendiri.');
        }

        $jadiAdmin = ! $user->isAdmin();
        $user->update(['role' => $jadiAdmin ? 'admin' : 'mahasiswa']);

        Notify::to(
            $user,
            $jadiAdmin ? 'Kamu sekarang admin' : 'Peran admin dicabut',
            $jadiAdmin ? 'Kamu bisa membuka Panel Admin dari menu akun.' : 'Akunmu kembali menjadi pengguna biasa.',
            route('dashboard'),
            'shield-check',
            $jadiAdmin ? 'emerald' : 'slate'
        );

        return back()->with('success', $user->name . ($jadiAdmin ? ' sekarang menjadi admin.' : ' tidak lagi menjadi admin.'));
    }

    /* ---------- KEDALUWARSA ---------- */

    public function expire()
    {
        Artisan::call('items:expire');

        return back()->with('success', trim(Artisan::output()));
    }
}
