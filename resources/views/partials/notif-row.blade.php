@php
    /** @var \Illuminate\Notifications\DatabaseNotification $n */
    $warna =
        [
            'emerald' => 'bg-emerald-100 text-emerald-600',
            'amber' => 'bg-amber-100 text-amber-600',
            'rose' => 'bg-rose-100 text-rose-600',
            'blue' => 'bg-blue-100 text-blue-600',
            'slate' => 'bg-slate-100 text-slate-600',
        ][$n->data['warna'] ?? 'slate'] ?? 'bg-slate-100 text-slate-600';
@endphp

<a href="{{ route('notifications.open', $n->id) }}"
    class="flex gap-3 px-3 py-3 rounded-xl hover:bg-slate-50 transition {{ $n->read_at ? '' : 'bg-emerald-50/60' }}">
    <span class="w-10 h-10 rounded-xl grid place-items-center shrink-0 {{ $warna }}">
        <x-icon :name="$n->data['ikon'] ?? 'bell'" class="w-5 h-5" />
    </span>
    <span class="min-w-0 flex-1">
        <span class="flex items-start justify-between gap-2">
            <span
                class="text-sm font-bold text-slate-900 {{ $n->read_at ? 'font-semibold' : '' }}">{{ $n->data['judul'] ?? 'Notifikasi' }}</span>
            @unless ($n->read_at)
                <span class="mt-1.5 w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span>
            @endunless
        </span>
        <span class="block text-sm text-slate-500 leading-snug line-clamp-2">{{ $n->data['pesan'] ?? '' }}</span>
        <span class="block mt-1 text-xs text-slate-400">{{ $n->created_at->diffForHumans() }}</span>
    </span>
</a>
