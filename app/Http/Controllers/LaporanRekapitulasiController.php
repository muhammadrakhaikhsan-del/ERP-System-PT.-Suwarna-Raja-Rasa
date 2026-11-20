<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KeuanganHarian;
use App\Models\BarangKeluar;
use App\Models\Barang;
use App\Models\StockOpname;

class LaporanRekapitulasiController extends Controller
{
    public function index(Request $request)
    {
        $start_date = $request->input('start_date', date('Y-m-01'));
        $end_date   = $request->input('end_date', date('Y-m-t'));

        // --- POINT 1: TOTAL PENDAPATAN dari keuangan_harian ---
        $pendapatan = KeuanganHarian::whereBetween('tgl', [$start_date, $end_date])
            ->sum('total_penjualan');

        // --- POINT 2: TOTAL KELUAR BARANG = qty_keluar x harga_barang ---
        $barang_keluar = BarangKeluar::with('barang')
            ->whereBetween('tgl', [$start_date, $end_date])
            ->get();

        $total_keluar_barang = 0;
        $total_item_keluar   = 0;
        foreach ($barang_keluar as $bk) {
            $harga = $bk->barang ? $bk->barang->harga_barang : 0;
            $total_keluar_barang += ($bk->qty_barang * $harga);
            $total_item_keluar   += $bk->qty_barang;
        }

        // SELISIH PENJUALAN = Pendapatan - Total Keluar Barang
        $selisih_penjualan = $pendapatan - $total_keluar_barang;

        // --- REKAP BARANG MASUK ---
        $total_item_masuk = \App\Models\BarangMasuk::whereBetween('tgl', [$start_date, $end_date])
            ->sum('qty_barang');

        // --- REKAP STOCK OPNAME (barang bermasalah) ---
        $barang        = Barang::all();
        $total_selisih_minus = 0;
        $total_selisih_plus  = 0;
        $barang_bermasalah   = [];

        foreach ($barang as $b) {
            $so = StockOpname::where('id_barang', $b->id_barang)
                ->whereBetween('tgl', [$start_date, $end_date])
                ->orderBy('tgl', 'desc')
                ->first();

            if ($so) {
                if ($so->selisih < 0) {
                    $total_selisih_minus += abs($so->selisih);
                    $barang_bermasalah[] = [
                        'nama'    => $b->nama_barang,
                        'selisih' => $so->selisih,
                        'tgl'     => $so->tgl,
                    ];
                } elseif ($so->selisih > 0) {
                    $total_selisih_plus += $so->selisih;
                    $barang_bermasalah[] = [
                        'nama'    => $b->nama_barang,
                        'selisih' => $so->selisih,
                        'tgl'     => $so->tgl,
                    ];
                }
            }
        }

        return view('laporan_rekapitulasi', compact(
            'start_date',
            'end_date',
            'pendapatan',
            'total_keluar_barang',
            'selisih_penjualan',
            'total_item_masuk',
            'total_item_keluar',
            'total_selisih_minus',
            'total_selisih_plus',
            'barang_bermasalah'
        ));
    }
}
