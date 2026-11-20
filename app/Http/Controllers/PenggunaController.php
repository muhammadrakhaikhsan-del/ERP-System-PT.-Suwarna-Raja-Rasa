<?php

namespace App\Http\Controllers;

use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PenggunaController extends Controller
{
    public function index()
    {
        $data = Pengguna::paginate(10);
        return view('pengguna', compact('data'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|unique:pengguna',
            'password' => 'required|min:6',
            'role'     => 'required|in:crew,purchasing,accounting,manager',
            'nama'     => 'required|string|max:100'
        ]);

        Pengguna::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
            'nama'     => $request->nama
        ]);

        return redirect('/pengguna')->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function resetPassword(Request $request, $id)
    {
        $request->validate([
            'new_password' => 'required|min:6'
        ]);

        $pengguna = Pengguna::findOrFail($id);
        $pengguna->update([
            'password' => Hash::make($request->new_password)
        ]);

        return redirect('/pengguna')->with('success', 'Password berhasil direset.');
    }

    public function destroy($id)
    {
        if(session('user')['id'] == $id) {
            return redirect('/pengguna')->with('error', 'Tidak bisa menghapus akun yang sedang Anda gunakan.');
        }

        $pengguna = Pengguna::findOrFail($id);
        $pengguna->delete();
        
        return redirect('/pengguna')->with('success', 'Pengguna berhasil dihapus.');
    }
}
