<?php
namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\Outlet;
use Illuminate\Http\Request;

class TenantController extends Controller
{
    public function index()
    {
        $data = Tenant::with('outlet')->orderBy('id_tenant', 'desc')->paginate(10);
        $outlets = Outlet::orderBy('nama_outlet')->get();
        return view('tenant', compact('data', 'outlets'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_outlet' => 'required|exists:outlet,id_outlet',
            'nama_tenant' => 'required|string|max:100',
        ]);
        Tenant::create($request->all());
        return redirect('/tenant')->with('success', 'Data berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'id_outlet' => 'required|exists:outlet,id_outlet',
            'nama_tenant' => 'required|string|max:100',
        ]);
        $item = Tenant::findOrFail($id);
        $item->update($request->all());
        return redirect('/tenant')->with('success', 'Data berhasil diubah');
    }

    public function destroy($id)
    {
        Tenant::findOrFail($id)->delete();
        return redirect('/tenant')->with('success', 'Data berhasil dihapus');
    }
}
