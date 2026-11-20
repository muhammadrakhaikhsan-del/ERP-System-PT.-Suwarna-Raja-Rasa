<?php
namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Tenant;
use Illuminate\Http\Request;

class BarangController extends Controller
{
    public function index()
    {
        $data    = Barang::with('tenant')->orderBy('nama_barang')->paginate(10);
        $tenants = Tenant::orderBy('nama_tenant')->get();
        return view('barang', compact('data', 'tenants'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_tenant'    => 'required|exists:tenant,id_tenant',
            'nama_barang'  => 'required|string|max:255',
            'harga_barang' => 'required|numeric|min:0',
        ]);

        Barang::create([
            'id_tenant'    => $request->id_tenant,
            'nama_barang'  => $request->nama_barang,
            'qty_barang'   => 0, // stok awal 0, diisi via barang masuk
            'harga_barang' => $request->harga_barang,
        ]);

        return redirect('/barang')->with('success', 'Barang berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'id_tenant'    => 'required|exists:tenant,id_tenant',
            'nama_barang'  => 'required|string|max:255',
            'harga_barang' => 'required|numeric|min:0',
        ]);

        $item = Barang::findOrFail($id);
        $item->update([
            'id_tenant'    => $request->id_tenant,
            'nama_barang'  => $request->nama_barang,
            'harga_barang' => $request->harga_barang,
        ]);

        return redirect('/barang')->with('success', 'Data berhasil diubah');
    }

    public function destroy($id)
    {
        Barang::findOrFail($id)->delete();
        return redirect('/barang')->with('success', 'Data berhasil dihapus');
    }

    /**
     * API: Return JSON list of barang by tenant id
     */
    public function getByTenant($id_tenant)
    {
        $barang = Barang::where('id_tenant', $id_tenant)
            ->orderBy('nama_barang')
            ->get(['id_barang', 'nama_barang', 'qty_barang', 'harga_barang']);
        return response()->json($barang);
    }
}
