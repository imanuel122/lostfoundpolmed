<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'login'    => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'login.required'    => 'Email atau NIM wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        // Boleh masuk dengan email ATAU NIM
        $field = filter_var($data['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'nim';

        if (! Auth::attempt([$field => $data['login'], 'password' => $data['password']], $request->boolean('remember'))) {
            return back()
                ->withErrors(['login' => 'Email/NIM atau password salah.'])
                ->onlyInput('login');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('home'))
            ->with('success', 'Selamat datang kembali, ' . Auth::user()->name . '!');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        // Rapikan nomor WA sebelum divalidasi (hapus spasi dan strip)
        $request->merge(['no_wa' => preg_replace('/[\s\-]/', '', (string) $request->no_wa)]);

        $emailRules = ['required', 'email', 'max:100', 'unique:users,email'];

        if ($domain = config('lostfound.allowed_email_domain')) {
            $emailRules[] = function ($attribute, $value, $fail) use ($domain) {
                if (! str_ends_with(strtolower($value), '@' . strtolower($domain))) {
                    $fail("Gunakan email kampus (@{$domain}).");
                }
            };
        }

        $data = $request->validate([
            'name'     => ['required', 'string', 'max:100'],
            'nim'      => ['nullable', 'string', 'max:20', 'unique:users,nim'],
            'email'    => $emailRules,
            'no_wa'    => ['required', 'regex:/^(\+62|62|0)8[0-9]{8,12}$/'],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ], [
            'name.required'      => 'Nama lengkap wajib diisi.',
            'nim.unique'         => 'NIM ini sudah terdaftar.',
            'email.required'     => 'Email wajib diisi.',
            'email.email'        => 'Format email tidak valid.',
            'email.unique'       => 'Email ini sudah terdaftar.',
            'no_wa.required'     => 'Nomor WhatsApp wajib diisi.',
            'no_wa.regex'        => 'Nomor WhatsApp tidak valid. Contoh: 081234567890.',
            'password.required'  => 'Password wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak sama.',
            'password.min'       => 'Password minimal 8 karakter.',
            'password.letters'   => 'Password harus mengandung huruf.',
            'password.numbers'   => 'Password harus mengandung angka.',
        ]);

        // Samakan format: 0812... atau +62812... menjadi 62812...
        $wa = preg_replace('/\D/', '', $data['no_wa']);
        if (str_starts_with($wa, '0')) {
            $wa = '62' . substr($wa, 1);
        }

        $user = User::create([
            'name'     => $data['name'],
            'nim'      => $data['nim'] ?? null,
            'email'    => strtolower($data['email']),
            'no_wa'    => $wa,
            'password' => Hash::make($data['password']),
            'role'     => 'mahasiswa',
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('home')
            ->with('success', 'Akun berhasil dibuat. Selamat datang di LostFound Polmed!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Kamu sudah keluar.');
    }
}
