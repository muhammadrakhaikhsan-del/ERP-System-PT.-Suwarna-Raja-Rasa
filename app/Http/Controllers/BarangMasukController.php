<?php
namespace App\Http\Controllers;

use App\Models\BarangMasuk;
use App\Models\Tenant;
use App\Models\Barang;
use Illuminate\Http\Request;

class BarangMasukController extends Controller
{
    public function index()
    {
        $data    = BarangMasuk::with('barang.tenant')->orderByDesc('id_bm')->paginate(10);
        $tenants = Tenant::orderBy('nama_tenant')->get();
        $barang  = Barang::with('tenant')->orderBy('nama_barang')->get();
        return view('barang_masuk', compact('data', 'tenants', 'barang'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_barang'  => 'required|exists:barang,id_barang',
            'qty_barang' => 'required|integer|min:1',
            'tgl'        => 'required|date',
        ]);

        BarangMasuk::create([
            'id_barang'  => $request->id_barang,
            'qty_barang' => $request->qty_barang,
            'tgl'        => $request->tgl,
        ]);

        // Tambah stok di tabel barang
        $barang = Barang::findOrFail($request->id_barang);
        $barang->increment('qty_barang', $request->qty_barang);

        return redirect('/barang_masuk')->with('success', 'Barang masuk berhasil ditambahkan. Stok bertambah ' . $request->qty_barang . ' unit.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'id_barang'  => 'required|exists:barang,id_barang',
            'qty_barang' => 'required|integer|min:1',
            'tgl'        => 'required|date',
        ]);

        $old    = BarangMasuk::findOrFail($id);
        $barang = Barang::findOrFail($request->id_barang);

        // Kembalikan stok lama dulu
        $barangLama = Barang::findOrFail($old->id_barang);
        $barangLama->decrement('qty_barang', $old->qty_barang);

        // Update data
        $old->update([
            'id_barang'  => $request->id_barang,
            'qty_barang' => $request->qty_barang,
            'tgl'        => $request->tgl,
        ]);

        // Tambah stok baru
        $barang->increment('qty_barang', $request->qty_barang);

        return redirect('/barang_masuk')->with('success', 'Data berhasil diubah.');
    }

    public function destroy($id)
    {
        $bm = BarangMasuk::findOrFail($id);
        // Kembalikan stok
        $barang = Barang::find($bm->id_barang);
        if ($barang) {
            $barang->decrement('qty_barang', $bm->qty_barang);
        }
        $bm->delete();
        return redirect('/barang_masuk')->with('success', 'Data berhasil dihapus. Stok dikembalikan.');
    }
}
