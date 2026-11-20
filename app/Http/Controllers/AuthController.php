<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (session('user')) {
            return redirect('/dashboard');
        }
        return view('login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $throttleKey = strtolower($request->input('username')) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            throw ValidationException::withMessages([
                'login' => ['Terlalu banyak percobaan login. Silakan coba lagi dalam ' . $seconds . ' detik.'],
            ]);
        }

        $user = DB::table('pengguna')->where('username', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            RateLimiter::hit($throttleKey);
            return back()->withErrors(['login' => 'Username atau password salah.'])->withInput();
        }

        RateLimiter::clear($throttleKey);

        session(['user' => [
            'id'       => $user->id,
            'username' => $user->username,
            'nama'     => $user->nama,
            'role'     => $user->role,
        ]]);

        return redirect('/dashboard')->with('success', 'Selamat datang, ' . $user->nama . '!');
    }

    public function logout()
    {
        session()->forget('user');
        return redirect('/')->with('success', 'Anda berhasil logout.');
    }
}
