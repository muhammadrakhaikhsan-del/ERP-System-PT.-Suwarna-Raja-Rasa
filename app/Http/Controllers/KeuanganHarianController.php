<?php
namespace App\Http\Controllers;

use App\Models\KeuanganHarian;
use App\Models\Tenant;
use Illuminate\Http\Request;

class KeuanganHarianController extends Controller
{
    public function index()
    {
        $data    = KeuanganHarian::with('tenant')->orderByDesc('id_kh')->paginate(10);
        $tenants = Tenant::orderBy('nama_tenant')->get();
        return view('keuangan_harian', compact('data', 'tenants'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_tenant'       => 'required|exists:tenant,id_tenant',
            'tgl'             => 'required|date',
            'total_penjualan' => 'required|numeric|min:0',
        ]);

        KeuanganHarian::create([
            'id_tenant'       => $request->id_tenant,
            'tgl'             => $request->tgl,
            'total_penjualan' => $request->total_penjualan,
        ]);

        return redirect('/keuangan_harian')->with('success', 'Data berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'id_tenant'       => 'required|exists:tenant,id_tenant',
            'tgl'             => 'required|date',
            'total_penjualan' => 'required|numeric|min:0',
        ]);

        $item = KeuanganHarian::findOrFail($id);
        $item->update([
            'id_tenant'       => $request->id_tenant,
            'tgl'             => $request->tgl,
            'total_penjualan' => $request->total_penjualan,
        ]);

        return redirect('/keuangan_harian')->with('success', 'Data berhasil diubah');
    }

    public function destroy($id)
    {
        KeuanganHarian::findOrFail($id)->delete();
        return redirect('/keuangan_harian')->with('success', 'Data berhasil dihapus');
    }
}
