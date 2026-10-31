@include('errors.layout', [
    'code' => 403,
    'icon' => 'ban',
    'tone' => 'amber',
    'title' => 'Kamu tidak punya akses',
    'text' =>
        'Halaman ini hanya bisa dibuka oleh pihak yang bersangkutan. Pastikan kamu masuk dengan akun yang tepat.',
])
