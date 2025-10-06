<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Penyewa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Tampilkan form login
    public function showLogin()
    {
        return view('auth.login');
    }

    // Proses login
    public function login(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ], [
        'email.required' => 'Email wajib diisi.',
        'email.email' => 'Format email tidak valid.',
        'password.required' => 'Password wajib diisi.',
    ]);

    $credentials = $request->only('email', 'password');

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();

        $role = Auth::user()->role;

        // Redirect berdasarkan role
        if ($role === 'admin') {
            return redirect()->route('dashboard.admin');
        } elseif ($role === 'penyewa') {
            return redirect()->route('dashboard.penyewa');
        }

        return redirect()->route('kontrakan.index');
    }

    return back()->withErrors([
        'email' => 'Email atau password salah.',
    ])->onlyInput('email'); // agar email tetap muncul
}


    // Tampilkan form register
    public function showRegister()
    {
        return view('auth.register');
    }

    // Proses register
public function register(Request $request)
{
    $request->validate([
        'name'     => 'required|string|max:255',
        'email'    => 'required|string|email|unique:users,email',
        'password' => 'required|string|min:6|confirmed',
    ], [
        'name.required' => 'Nama lengkap wajib diisi.',
        'email.required' => 'Email wajib diisi.',
        'email.email' => 'Format email tidak valid.',
        'email.unique' => 'Email sudah digunakan.',
        'password.required' => 'Password wajib diisi.',
        'password.min' => 'Password minimal 6 karakter.',
        'password.confirmed' => 'Konfirmasi password tidak sesuai.',
    ]);

    // Buat user baru
    $users = User::create([
        'name'     => $request->name,
        'email'    => $request->email,
        'password' => Hash::make($request->password),
        'role'     => 'penyewa',
    ]);

    // Buat data di tabel penyewa
    Penyewa::create([
        'id_penyewa'   => $users->id,
        'nama_lengkap' => $users->name,
        // bisa tambahkan default no_telepon, NIK, alamat kosong
    ]);

    // Login otomatis
    Auth::login($users);

    return redirect()->route('dashboard.penyewa');
}


    // Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
