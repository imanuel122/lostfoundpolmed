@props(['name', 'label', 'placeholder' => '', 'hint' => null, 'optional' => false, 'rows' => 4, 'max' => 1000])

@php $error = $errors->first($name); @endphp

<div x-data="{ n: {{ mb_strlen((string) old($name, '')) }} }">
    <label for="{{ $name }}" class="flex items-center justify-between text-[13px] font-semibold text-slate-700">
        <span>{{ $label }}</span>
        @if ($optional)
            <span class="text-[11px] font-medium text-slate-400">Opsional</span>
        @endif
    </label>

    <textarea id="{{ $name }}" name="{{ $name }}" rows="{{ $rows }}" maxlength="{{ $max }}"
        placeholder="{{ $placeholder }}" @input="n = $event.target.value.length"
        class="mt-1.5 w-full rounded-xl px-4 py-3 text-sm outline-none transition resize-none placeholder:text-slate-400 ring-1
                     {{ $error
                         ? 'bg-rose-50/60 ring-rose-300 focus:ring-2 focus:ring-rose-400'
                         : 'bg-slate-50 ring-slate-200 hover:ring-slate-300 focus:bg-white focus:ring-2 focus:ring-emerald-500' }}">{{ old($name) }}</textarea>

    <div class="mt-1.5 flex items-start justify-between gap-3 text-xs">
        @if ($error)
            <p class="flex items-center gap-1.5 font-medium text-rose-600">
                <x-icon name="alert-circle" class="w-4 h-4 shrink-0" /> {{ $error }}
            </p>
        @else
            <p class="text-slate-400">{{ $hint }}</p>
        @endif
        <span class="shrink-0 text-slate-400"><span x-text="n"></span>/{{ $max }}</span>
    </div>
</div>
