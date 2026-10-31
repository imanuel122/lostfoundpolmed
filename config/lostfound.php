<?php

return [
    // Kosong = semua email boleh mendaftar.
    // Nanti kalau mau dibatasi, isi lewat .env (ALLOWED_EMAIL_DOMAIN).
    'allowed_email_domain' => env('ALLOWED_EMAIL_DOMAIN'),
];
