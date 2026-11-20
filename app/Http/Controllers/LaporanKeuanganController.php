<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KeuanganHarian;

class LaporanKeuanganController extends Controller
{
    public function index(Request $request)
    {
        $start_date = $request->input('start_date', date('Y-m-01'));
        $end_date   = $request->input('end_date', date('Y-m-t'));

        // Ambil Data Pendapatan dari keuangan_harian
        $pendapatan = KeuanganHarian::with('tenant')
            ->whereBetween('tgl', [$start_date, $end_date])
            ->orderBy('tgl')
            ->get();

        // Rekap Harian (tanggal => pendapatan)
        $rekap_harian = [];
        $total_pendapatan_all = 0;

        foreach ($pendapatan as $p) {
            $date = $p->tgl;
            if (!isset($rekap_harian[$date])) {
                $rekap_harian[$date] = 0;
            }
            $rekap_harian[$date] += $p->total_penjualan;
            $total_pendapatan_all += $p->total_penjualan;
        }

        // Sort by date descending
        krsort($rekap_harian);

        return view('laporan_keuangan', compact(
            'rekap_harian',
            'start_date',
            'end_date',
            'total_pendapatan_all'
        ));
    }
}
