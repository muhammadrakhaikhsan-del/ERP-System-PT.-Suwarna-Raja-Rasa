<?php
namespace App\Http\Controllers;

use App\Models\Outlet;
use Illuminate\Http\Request;

class OutletController extends Controller
{
    public function index()
    {
        $data = Outlet::paginate(10);
        return view('outlet', compact('data'));
    }

    public function store(Request $request)
    {
        Outlet::create($request->all());
        return redirect('/outlet')->with('success', 'Data berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $item = Outlet::findOrFail($id);
        $item->update($request->all());
        return redirect('/outlet')->with('success', 'Data berhasil diubah');
    }

    public function destroy($id)
    {
        Outlet::findOrFail($id)->delete();
        return redirect('/outlet')->with('success', 'Data berhasil dihapus');
    }
}
