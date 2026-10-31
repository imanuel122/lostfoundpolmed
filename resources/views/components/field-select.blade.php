@props(['name', 'label', 'icon' => 'map-pin', 'options' => [], 'placeholder' => 'Pilih...', 'hint' => null])

@php
    $error = $errors->first($name);
    $value = old($name);
@endphp

<div>
    <label for="{{ $name }}" class="block text-[13px] font-semibold text-slate-700">{{ $label }}</label>

    <div class="relative mt-1.5 group">
        <x-icon :name="$icon"
            class="absolute left-4 top-1/2 -translate-y-1/2 w-[18px] h-[18px] pointer-events-none transition-colors
                       {{ $error ? 'text-rose-400' : 'text-slate-400 group-focus-within:text-emerald-600' }}" />

        <select id="{{ $name }}" name="{{ $name }}"
            class="appearance-none w-full rounded-xl pl-11 pr-10 py-3 text-sm outline-none transition ring-1
                       {{ $error
                           ? 'bg-rose-50/60 ring-rose-300 focus:ring-2 focus:ring-rose-400'
                           : 'bg-slate-50 ring-slate-200 hover:ring-slate-300 focus:bg-white focus:ring-2 focus:ring-emerald-500' }}
                       {{ $value ? 'text-slate-800' : 'text-slate-400' }}">
            <option value="">{{ $placeholder }}</option>
            @foreach ($options as $val => $text)
                <option value="{{ $val }}" class="text-slate-800" @selected((string) $value === (string) $val)>
                    {{ $text }}</option>
            @endforeach
        </select>

        <x-icon name="chevron-down"
            class="absolute right-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" />
    </div>

    @if ($error)
        <p class="mt-1.5 flex items-center gap-1.5 text-xs font-medium text-rose-600">
            <x-icon name="alert-circle" class="w-4 h-4 shrink-0" /> {{ $error }}
        </p>
    @elseif($hint)
        <p class="mt-1.5 text-xs text-slate-400">{{ $hint }}</p>
    @endif
</div>
