<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_outlet'    => DB::table('outlet')->count(),
            'total_tenant'    => DB::table('tenant')->count(),
            'total_barang'    => DB::table('barang')->count(),
            'total_bm'        => DB::table('barang_masuk')->count(),
            'total_bk'        => DB::table('barang_keluar')->count(),
            'total_so'        => DB::table('stock_opname')->count(),
            'total_keuangan'  => DB::table('keuangan_harian')->sum('total_penjualan'),
            'barang_low_stock'=> DB::table('barang')->where('qty_barang', '<=', 5)->count(),
        ];

        $recent_bm = DB::table('barang_masuk')
            ->join('barang', 'barang_masuk.id_barang', '=', 'barang.id_barang')
            ->select('barang_masuk.*', 'barang.nama_barang')
            ->orderByDesc('barang_masuk.id_bm')
            ->limit(5)->get();

        $recent_bk = DB::table('barang_keluar')
            ->join('barang', 'barang_keluar.id_barang', '=', 'barang.id_barang')
            ->select('barang_keluar.*', 'barang.nama_barang')
            ->orderByDesc('barang_keluar.id_bk')
            ->limit(5)->get();

        // === Chart Data: Last 7 Days ===
        $chartLabels   = [];
        $chartBm       = [];
        $chartBk       = [];
        $chartKeuangan = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $chartLabels[]   = Carbon::parse($date)->format('d M');
            $chartBm[]       = (int) DB::table('barang_masuk')->whereDate('tgl', $date)->sum('qty_barang');
            $chartBk[]       = (int) DB::table('barang_keluar')->whereDate('tgl', $date)->sum('qty_barang');
            $chartKeuangan[] = (float) DB::table('keuangan_harian')->whereDate('tgl', $date)->sum('total_penjualan');
        }

        // === Low Stock Alerts (stok <= 5) ===
        $lowStockItems = DB::table('barang')
            ->join('tenant', 'barang.id_tenant', '=', 'tenant.id_tenant')
            ->select('barang.nama_barang', 'barang.qty_barang', 'tenant.nama_tenant')
            ->where('barang.qty_barang', '<=', 5)
            ->orderBy('barang.qty_barang')
            ->get();

        return view('dashboard', compact(
            'stats', 'recent_bm', 'recent_bk',
            'chartLabels', 'chartBm', 'chartBk', 'chartKeuangan',
            'lowStockItems'
        ));
    }
}
