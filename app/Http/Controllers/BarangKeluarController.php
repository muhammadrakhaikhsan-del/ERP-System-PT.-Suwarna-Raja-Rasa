<?php
namespace App\Http\Controllers;

use App\Models\BarangKeluar;
use App\Models\Barang;
use App\Models\Tenant;
use Illuminate\Http\Request;

class BarangKeluarController extends Controller
{
    public function index()
    {
        $data    = BarangKeluar::with('barang.tenant')->orderByDesc('id_bk')->paginate(10);
        $tenants = Tenant::orderBy('nama_tenant')->get();
        $barang  = Barang::with('tenant')->orderBy('nama_barang')->get();
        return view('barang_keluar', compact('data', 'tenants', 'barang'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_barang'  => 'required|exists:barang,id_barang',
            'qty_barang' => 'required|integer|min:1',
            'tgl'        => 'required|date',
        ]);

        $barang = Barang::findOrFail($request->id_barang);

        if ($request->qty_barang > $barang->qty_barang) {
            return redirect('/barang_keluar')
                ->with('error', 'Stok tidak cukup! Stok tersedia: ' . $barang->qty_barang . ' unit.');
        }

        BarangKeluar::create([
            'id_barang'  => $request->id_barang,
            'qty_barang' => $request->qty_barang,
            'tgl'        => $request->tgl,
        ]);

        $barang->decrement('qty_barang', $request->qty_barang);

        return redirect('/barang_keluar')->with('success', 'Barang keluar berhasil dicatat. Stok berkurang ' . $request->qty_barang . ' unit.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'id_barang'  => 'required|exists:barang,id_barang',
            'qty_barang' => 'required|integer|min:1',
            'tgl'        => 'required|date',
        ]);

        $old        = BarangKeluar::findOrFail($id);
        $barangLama = Barang::findOrFail($old->id_barang);

        // Kembalikan stok lama dulu
        $barangLama->increment('qty_barang', $old->qty_barang);

        $barangBaru = Barang::findOrFail($request->id_barang);

        if ($request->qty_barang > $barangBaru->qty_barang) {
            // Kembalikan stok lama lagi karena gagal
            $barangLama->decrement('qty_barang', $old->qty_barang);
            return redirect('/barang_keluar')
                ->with('error', 'Stok tidak cukup! Stok tersedia: ' . $barangBaru->qty_barang . ' unit.');
        }

        $old->update([
            'id_barang'  => $request->id_barang,
            'qty_barang' => $request->qty_barang,
            'tgl'        => $request->tgl,
        ]);

        $barangBaru->decrement('qty_barang', $request->qty_barang);

        return redirect('/barang_keluar')->with('success', 'Data berhasil diubah.');
    }

    public function destroy($id)
    {
        $bk = BarangKeluar::findOrFail($id);
        $barang = Barang::find($bk->id_barang);
        if ($barang) {
            $barang->increment('qty_barang', $bk->qty_barang);
        }
        $bk->delete();
        return redirect('/barang_keluar')->with('success', 'Data berhasil dihapus. Stok dikembalikan.');
    }
}
