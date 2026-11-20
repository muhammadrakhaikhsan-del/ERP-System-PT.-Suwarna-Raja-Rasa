<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use App\Models\BarangMasuk;
use App\Models\BarangKeluar;
use App\Models\StockOpname;

class LaporanBarangController extends Controller
{
    public function index(Request $request)
    {
        // Set default filter date (Bulan Ini)
        $start_date = $request->input('start_date', date('Y-m-01'));
        $end_date = $request->input('end_date', date('Y-m-t'));

        // Ambil semua barang
        $barang = Barang::all();
        $laporan = [];

        foreach ($barang as $b) {
            // Hitung Total Masuk dalam periode
            $total_masuk = BarangMasuk::where('id_barang', $b->id_barang)
                ->whereBetween('tgl', [$start_date, $end_date])
                ->sum('qty_barang');

            // Hitung Total Keluar dalam periode
            $total_keluar = BarangKeluar::where('id_barang', $b->id_barang)
                ->whereBetween('tgl', [$start_date, $end_date])
                ->sum('qty_barang');

            // Ambil Data Stock Opname terakhir dalam periode (jika ada)
            $so = StockOpname::where('id_barang', $b->id_barang)
                ->whereBetween('tgl', [$start_date, $end_date])
                ->orderBy('tgl', 'desc')
                ->first();

            $stok_fisik = $so ? $so->stok_fisik : '-';
            $selisih = $so ? $so->selisih : '-';

            $laporan[] = [
                'id_barang' => $b->id_barang,
                'nama_barang' => $b->nama_barang,
                'stok_akhir_sistem' => $b->qty_barang, // Stok live saat ini
                'total_masuk' => $total_masuk,
                'total_keluar' => $total_keluar,
                'stok_fisik_so' => $stok_fisik,
                'selisih' => $selisih,
            ];
        }

        return view('laporan_barang', compact('laporan', 'start_date', 'end_date'));
    }
}
