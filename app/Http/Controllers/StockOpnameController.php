<?php
namespace App\Http\Controllers;

use App\Models\StockOpname;
use App\Models\Barang;
use Illuminate\Http\Request;

class StockOpnameController extends Controller
{
    public function index()
    {
        $data    = StockOpname::with('barang.tenant')->orderByDesc('id_so')->paginate(10);
        $barang  = Barang::with('tenant')->orderBy('nama_barang')->get();
        $tenants = \App\Models\Tenant::orderBy('nama_tenant')->get();
        return view('stock_opname', compact('data', 'barang', 'tenants'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_barang'  => 'required|exists:barang,id_barang',
            'stok_fisik' => 'required|integer|min:0',
            'tgl'        => 'required|date',
        ]);

        $barang      = Barang::findOrFail($request->id_barang);
        $stok_sistem = $barang->qty_barang;
        $selisih     = $request->stok_fisik - $stok_sistem;

        StockOpname::create([
            'id_barang'   => $request->id_barang,
            'stok_sistem' => $stok_sistem,
            'stok_fisik'  => $request->stok_fisik,
            'selisih'     => $selisih,
            'tgl'         => $request->tgl,
        ]);

        return redirect('/stock_opname')->with('success', 'Data stock opname berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'id_barang'  => 'required|exists:barang,id_barang',
            'stok_fisik' => 'required|integer|min:0',
            'tgl'        => 'required|date',
        ]);

        $item        = StockOpname::findOrFail($id);
        $barang      = Barang::findOrFail($request->id_barang);
        $stok_sistem = $barang->qty_barang;
        $selisih     = $request->stok_fisik - $stok_sistem;

        $item->update([
            'id_barang'   => $request->id_barang,
            'stok_sistem' => $stok_sistem,
            'stok_fisik'  => $request->stok_fisik,
            'selisih'     => $selisih,
            'tgl'         => $request->tgl,
        ]);

        return redirect('/stock_opname')->with('success', 'Data berhasil diubah');
    }

    public function destroy($id)
    {
        StockOpname::findOrFail($id)->delete();
        return redirect('/stock_opname')->with('success', 'Data berhasil dihapus');
    }
}
