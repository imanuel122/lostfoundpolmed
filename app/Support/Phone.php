<?php

namespace App\Support;

class Phone
{
    // 0812... / +62812... menjadi 62812...
    public static function normalize(?string $raw): string
    {
        $digits = preg_replace('/\D/', '', (string) $raw);

        return str_starts_with($digits, '0') ? '62' . substr($digits, 1) : $digits;
    }
}
