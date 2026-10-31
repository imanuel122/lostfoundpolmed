@extends('layouts.app')

@section('title', 'Notifikasi - LostFound Polmed')

@section('content')
    <section class="max-w-3xl mx-auto px-4 pt-28 pb-6 min-h-[70vh]">

        <div class="flex flex-wrap items-end justify-between gap-4 animate-fade-up">
            <div>
                <h1 class="text-3xl font-extrabold tracking-tight text-slate-900">Notifikasi</h1>
                <p class="mt-1 text-slate-500">Kabar terbaru tentang postingan dan klaimmu.</p>
            </div>

            @if (auth()->user()->unreadNotifications()->count())
                <form method="POST" action="{{ route('notifications.readAll') }}">
                    @csrf
                    <button
                        class="inline-flex items-center gap-2 rounded-xl bg-white ring-1 ring-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50 hover:-translate-y-0.5 active:scale-95 transition">
                        <x-icon name="check-check" class="w-4 h-4" /> Tandai semua dibaca
                    </button>
                </form>
            @endif
        </div>

        <div class="mt-5 inline-flex bg-white p-1 rounded-full shadow-sm ring-1 ring-slate-200">
            <a href="{{ route('notifications.index') }}"
                class="px-4 py-2 rounded-full text-sm font-semibold transition {{ $filter === 'semua' ? 'bg-slate-900 text-white shadow' : 'text-slate-500 hover:text-slate-900' }}">Semua</a>
            <a href="{{ route('notifications.index', ['filter' => 'belum']) }}"
                class="px-4 py-2 rounded-full text-sm font-semibold transition {{ $filter === 'belum' ? 'bg-emerald-600 text-white shadow' : 'text-slate-500 hover:text-emerald-600' }}">Belum
                dibaca</a>
        </div>

        <div class="mt-5">
            @if ($notifs->count())
                <div class="bg-white rounded-3xl ring-1 ring-slate-200 p-2 space-y-1">
                    @foreach ($notifs as $n)
                        @include('partials.notif-row', ['n' => $n])
                    @endforeach
                </div>
                <div class="mt-6">{{ $notifs->links() }}</div>
            @else
                @include('partials.dashboard-empty', [
                    'icon' => 'bell',
                    'title' => $filter === 'belum' ? 'Semua sudah dibaca' : 'Belum ada notifikasi',
                    'text' => 'Kabar tentang klaim dan serah terima akan muncul di sini.',
                ])
            @endif
        </div>
    </section>
@endsection
