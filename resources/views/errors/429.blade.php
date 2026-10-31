@include('errors.layout', [
    'code' => 429,
    'icon' => 'clock',
    'tone' => 'amber',
    'title' => 'Terlalu banyak percobaan',
    'text' => 'Kamu terlalu sering mencoba. Tunggu satu menit, lalu coba lagi.',
])
