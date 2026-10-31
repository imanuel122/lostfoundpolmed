@include('errors.layout', [
    'code' => 500,
    'icon' => 'triangle-alert',
    'tone' => 'rose',
    'title' => 'Terjadi kesalahan di server',
    'text' => 'Ini bukan salahmu. Coba lagi beberapa saat. Kalau tetap begini, hubungi admin.',
])
