@extends('layouts.app')

@section('title', 'Pengguna - Panel Admin')

@section('content')
    @include('admin._nav', ['active' => 'users', 'judul' => 'Kelola Pengguna'])

    <section x-data class="max-w-6xl mx-auto px-4 pt-6 pb-6">

        <form method="GET" class="grid grid-cols-2 md:grid-cols-4 gap-3 bg-white p-4 rounded-2xl ring-1 ring-slate-200">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama, email, atau NIM..."
                class="col-span-2 rounded-xl border border-slate-200 px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-emerald-400">
            <select name="role"
                class="rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-emerald-400 bg-white">
                <option value="">Semua peran</option>
                <option value="mahasiswa" @selected(request('role') === 'mahasiswa')>Mahasiswa</option>
                <option value="admin" @selected(request('role') === 'admin')>Admin</option>
            </select>
            <button
                class="rounded-xl bg-slate-900 text-white text-sm font-semibold px-4 py-2.5 hover:bg-emerald-600 active:scale-95 transition">Terapkan</button>
        </form>

        <div class="mt-5 bg-white rounded-3xl ring-1 ring-slate-200 divide-y divide-slate-100 overflow-hidden">
            @foreach ($users as $u)
                <div class="flex flex-col md:flex-row md:items-center gap-3 p-4">
                    <div class="flex items-center gap-3 min-w-0 flex-1">
                        <span
                            class="w-11 h-11 rounded-full bg-gradient-to-br from-emerald-500 to-cyan-500 text-white grid place-items-center font-bold shrink-0">
                            {{ strtoupper(mb_substr($u->name, 0, 1)) }}
                        </span>
                        <div class="min-w-0">
                            <p class="font-bold text-slate-900 truncate">
                                {{ $u->name }}
                                @if ($u->id === auth()->id())
                                    <span class="text-xs font-medium text-slate-400">(kamu)</span>
                                @endif
                            </p>
                            <p class="text-xs text-slate-400 truncate">
                                {{ $u->email }}{{ $u->nim ? ' · NIM ' . $u->nim : '' }}</p>
                            <p class="text-xs text-slate-400">{{ $u->items_count }} postingan · {{ $u->claims_count }} klaim
                                · bergabung {{ $u->created_at->diffForHumans() }}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 shrink-0">
                        <span
                            class="px-3 py-1 rounded-full text-xs font-semibold {{ $u->isAdmin() ? 'bg-indigo-100 text-indigo-700' : 'bg-slate-100 text-slate-600' }}">
                            {{ $u->isAdmin() ? 'Admin' : 'Mahasiswa' }}
                        </span>

                        @if ($u->id !== auth()->id())
                            <button type="button"
                                @click="$dispatch('open-confirm', {{ Js::from([
                                    'action' => route('admin.users.role', $u),
                                    'title' => $u->isAdmin() ? 'Cabut peran admin?' : 'Jadikan admin?',
                                    'text' => $u->isAdmin()
                                        ? $u->name . ' tidak lagi bisa membuka Panel Admin.'
                                        : $u->name . ' akan bisa menghapus postingan, mengelola pengguna, dan memproses semua klaim.',
                                    'button' => $u->isAdmin() ? 'Ya, cabut' : 'Ya, jadikan admin',
                                    'tone' => $u->isAdmin() ? 'rose' : 'emerald',
                                ]) }})"
                                class="rounded-xl bg-slate-50 ring-1 ring-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-100 transition">
                                {{ $u->isAdmin() ? 'Cabut admin' : 'Jadikan admin' }}
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">{{ $users->links() }}</div>
    </section>
@endsection
