@props([
    'name',
    'label',
    'icon' => 'user',
    'type' => 'text',
    'placeholder' => '',
    'hint' => null,
    'optional' => false,
    'autocomplete' => 'off',
    'value' => null,
])

@php
    $isPass = $type === 'password';
    $error = $errors->first($name);
@endphp

<div x-data="{ show: false }">
    <label for="{{ $name }}" class="flex items-center justify-between text-[13px] font-semibold text-slate-700">
        <span>{{ $label }}</span>
        @if ($optional)
            <span class="text-[11px] font-medium text-slate-400">Opsional</span>
        @endif
    </label>

    <div class="relative mt-1.5 group">
        <x-icon :name="$icon"
            class="absolute left-4 top-1/2 -translate-y-1/2 w-[18px] h-[18px] pointer-events-none transition-colors
                       {{ $error ? 'text-rose-400' : 'text-slate-400 group-focus-within:text-emerald-600' }}" />

        <input id="{{ $name }}" name="{{ $name }}"
            @if ($isPass) type="password" :type="show ? 'text' : 'password'" @else type="{{ $type }}" @endif
            @unless ($isPass) value="{{ old($name, $value) }}" @endunless
            placeholder="{{ $placeholder }}" autocomplete="{{ $autocomplete }}"
            {{ $attributes->merge([
                'class' =>
                    'w-full rounded-xl pl-11 ' .
                    ($isPass ? 'pr-12' : 'pr-4') .
                    ' py-3 text-sm outline-none transition placeholder:text-slate-400 ring-1 ' .
                    ($error
                        ? 'bg-rose-50/60 ring-rose-300 focus:ring-2 focus:ring-rose-400'
                        : 'bg-slate-50 ring-slate-200 hover:ring-slate-300 focus:bg-white focus:ring-2 focus:ring-emerald-500'),
            ]) }}>

        @if ($isPass)
            <button type="button" @click="show = !show" tabindex="-1" aria-label="Tampilkan password"
                class="absolute right-2 top-1/2 -translate-y-1/2 w-9 h-9 grid place-items-center rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-200/60 transition">
                <x-icon name="eye" x-show="!show" class="w-[18px] h-[18px]" />
                <x-icon name="eye-off" x-show="show" x-cloak class="w-[18px] h-[18px]" />
            </button>
        @endif
    </div>

    @if ($error)
        <p class="mt-1.5 flex items-center gap-1.5 text-xs font-medium text-rose-600">
            <x-icon name="alert-circle" class="w-4 h-4 shrink-0" /> {{ $error }}
        </p>
    @elseif($hint)
        <p class="mt-1.5 text-xs text-slate-400">{{ $hint }}</p>
    @endif
</div>
