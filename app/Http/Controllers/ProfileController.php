<?php

namespace App\Http\Controllers;

use App\Support\Phone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        $u = $request->user();

        return view('profile.edit', [
            'user' => $u,
            'stat' => [
                'postingan' => $u->items()->where('status', '!=', 'dihapus')->count(),
                'klaim'     => $u->claims()->count(),
                'kembali'   => $u->items()->where('status', 'dikembalikan')->count(),
            ],
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $request->merge(['no_wa' => preg_replace('/[\s\-]/', '', (string) $request->no_wa)]);

        $data = $request->validate([
            'name'  => ['required', 'string', 'max:100'],
            'nim'   => ['nullable', 'string', 'max:20', 'unique:users,nim,' . $user->id],
            'email' => ['required', 'email', 'max:100', 'unique:users,email,' . $user->id],
            'no_wa' => ['required', 'regex:/^(\+62|62|0)8[0-9]{8,12}$/'],
        ], [
            'name.required'  => 'Nama lengkap wajib diisi.',
            'nim.unique'     => 'NIM ini sudah dipakai akun lain.',
            'email.required' => 'Email wajib diisi.',
            'email.email'    => 'Format email tidak valid.',
            'email.unique'   => 'Email ini sudah dipakai akun lain.',
            'no_wa.required' => 'Nomor WhatsApp wajib diisi.',
            'no_wa.regex'    => 'Nomor WhatsApp tidak valid. Contoh: 081234567890.',
        ]);

        $user->update([
            'name'  => $data['name'],
            'nim'   => $data['nim'] ?? null,
            'email' => strtolower($data['email']),
            'no_wa' => Phone::normalize($data['no_wa']),
        ]);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function password(Request $request)
    {
        $data = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'confirmed', 'different:current_password', Password::min(8)->letters()->numbers()],
        ], [
            'current_password.required'         => 'Masukkan password yang sekarang.',
            'current_password.current_password' => 'Password sekarang salah.',
            'password.required'                 => 'Password baru wajib diisi.',
            'password.confirmed'                => 'Konfirmasi password tidak sama.',
            'password.different'                => 'Password baru harus berbeda dari yang lama.',
            'password.min'                      => 'Password minimal 8 karakter.',
            'password.letters'                  => 'Password harus mengandung huruf.',
            'password.numbers'                  => 'Password harus mengandung angka.',
        ]);

        $request->user()->update(['password' => Hash::make($data['password'])]);

        return back()->with('success', 'Password berhasil diubah.');
    }
}
