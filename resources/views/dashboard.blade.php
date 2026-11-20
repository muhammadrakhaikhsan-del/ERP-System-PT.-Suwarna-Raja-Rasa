@extends('layouts.main')
@section('title', 'Dashboard')
@section('header_title', 'DASHBOARD')

@section('content')
{{-- Flash Messages --}}
@if(session('success'))
    <div class="{{ config('ui.alert_success') }}">
        {{ session('success') }}
    </div>
@endif

{{-- Low Stock Alert Banner --}}
@if($lowStockItems->count() > 0)
<div class="mb-6 glass-panel border border-red-500/40 p-4 rounded-xl flex items-start gap-4">
    <div class="flex-shrink-0 w-10 h-10 bg-red-900/40 border border-red-500/40 rounded-lg flex items-center justify-center">
        <i class="bi bi-exclamation-triangle-fill text-red-400 text-lg"></i>
    </div>
    <div class="flex-1 min-w-0">
        <p class="text-red-300 font-bold text-sm mb-2">⚠ Peringatan Stok Rendah! ({{ $lowStockItems->count() }} item)</p>
        <div class="flex flex-wrap gap-2">
            @foreach($lowStockItems as $item)
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold 
                {{ $item->qty_barang == 0 ? 'bg-red-600/30 text-red-300 border border-red-500/50' : 'bg-orange-900/30 text-orange-300 border border-orange-500/40' }}">
                <i class="bi bi-box-seam"></i>
                {{ $item->nama_barang }} 
                <span class="font-normal text-white/50">({{ $item->nama_tenant }})</span>
                — <strong>{{ $item->qty_barang }} unit</strong>
            </span>
            @endforeach
        </div>
    </div>
</div>
@endif

{{-- Stats Cards --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="glass-panel p-5 flex flex-col gap-1">
        <span class="{{ config('ui.stat_card_label') }}">Outlet</span>
        <span class="{{ config('ui.stat_card_value') }} text-indigo-400">{{ $stats['total_outlet'] }}</span>
        <span class="{{ config('ui.stat_card_desc') }}">Total outlet terdaftar</span>
    </div>
    <div class="glass-panel p-5 flex flex-col gap-1">
        <span class="{{ config('ui.stat_card_label') }}">Tenant</span>
        <span class="{{ config('ui.stat_card_value') }} text-teal-400">{{ $stats['total_tenant'] }}</span>
        <span class="{{ config('ui.stat_card_desc') }}">Total tenant aktif</span>
    </div>
    <div class="glass-panel p-5 flex flex-col gap-1">
        <span class="{{ config('ui.stat_card_label') }}">Barang</span>
        <span class="{{ config('ui.stat_card_value') }} text-amber-400">{{ $stats['total_barang'] }}</span>
        <span class="{{ config('ui.stat_card_desc') }}">Jenis barang</span>
    </div>
    <div class="glass-panel p-5 flex flex-col gap-1">
        <span class="{{ config('ui.stat_card_label') }}">Stok Rendah</span>
        <span class="{{ config('ui.stat_card_value') }} text-red-400">{{ $stats['barang_low_stock'] }}</span>
        <span class="{{ config('ui.stat_card_desc') }}">Barang ≤ 5 unit</span>
    </div>
</div>

<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
    <div class="glass-panel p-5 flex flex-col gap-1">
        <span class="{{ config('ui.stat_card_label') }}">Barang Masuk</span>
        <span class="{{ config('ui.stat_card_value') }} text-blue-400">{{ $stats['total_bm'] }}</span>
        <span class="{{ config('ui.stat_card_desc') }}">Total transaksi</span>
    </div>
    <div class="glass-panel p-5 flex flex-col gap-1">
        <span class="{{ config('ui.stat_card_label') }}">Barang Keluar</span>
        <span class="{{ config('ui.stat_card_value') }} text-orange-400">{{ $stats['total_bk'] }}</span>
        <span class="{{ config('ui.stat_card_desc') }}">Total transaksi</span>
    </div>
    <div class="glass-panel p-5 flex flex-col gap-1">
        <span class="{{ config('ui.stat_card_label') }}">Stock Opname</span>
        <span class="{{ config('ui.stat_card_value') }} text-purple-400">{{ $stats['total_so'] }}</span>
        <span class="{{ config('ui.stat_card_desc') }}">Total dicek</span>
    </div>
    <div class="glass-panel p-5 flex flex-col gap-1">
        <span class="{{ config('ui.stat_card_label') }}">Total Penjualan</span>
        <span class="{{ config('ui.stat_card_value') }} !text-2xl text-emerald-400">Rp {{ number_format($stats['total_keuangan'], 0, ',', '.') }}</span>
        <span class="{{ config('ui.stat_card_desc') }}">Akumulasi seluruh</span>
    </div>
</div>

{{-- Chart Section --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    {{-- Trend Barang Masuk & Keluar --}}
    <div class="{{ config('ui.card_container') }}">
        <div class="{{ config('ui.card_header') }}">
            <h3 class="font-semibold text-glass-title text-sm uppercase tracking-wide">
                <i class="bi bi-bar-chart-line-fill mr-2 text-indigo-400"></i>Tren Barang (7 Hari)
            </h3>
        </div>
        <div class="p-5">
            <canvas id="chartBarang" height="160"></canvas>
        </div>
    </div>

    {{-- Trend Keuangan Harian --}}
    <div class="{{ config('ui.card_container') }}">
        <div class="{{ config('ui.card_header') }}">
            <h3 class="font-semibold text-glass-title text-sm uppercase tracking-wide">
                <i class="bi bi-graph-up-arrow mr-2 text-emerald-400"></i>Tren Penjualan (7 Hari)
            </h3>
        </div>
        <div class="p-5">
            <canvas id="chartKeuangan" height="160"></canvas>
        </div>
    </div>
</div>

{{-- Recent Tables --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    {{-- Barang Masuk Terbaru --}}
    <div class="{{ config('ui.card_container') }}">
        <div class="{{ config('ui.card_header') }}">
            <h3 class="font-semibold text-glass-title text-sm uppercase tracking-wide">Barang Masuk Terbaru</h3>
            @if(session('user')['role'] === 'crew')
                <a href="{{ url('/barang_masuk') }}" class="text-xs text-indigo-300 hover:text-indigo-100 font-medium">Lihat semua →</a>
            @endif
        </div>
        <table class="{{ config('ui.dashboard_table') }} glass-table">
            <thead class="{{ config('ui.dashboard_thead') }}">
                <tr>
                    <th class="{{ config('ui.dashboard_th') }}">Barang</th>
                    <th class="{{ config('ui.dashboard_th') }}">Qty</th>
                    <th class="{{ config('ui.dashboard_th') }}">Tanggal</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recent_bm as $row)
                <tr class="{{ config('ui.dashboard_tr') }}">
                    <td class="{{ config('ui.dashboard_td') }}">{{ $row->nama_barang }}</td>
                    <td class="{{ config('ui.dashboard_td') }}">{{ $row->qty_barang }}</td>
                    <td class="{{ config('ui.dashboard_td') }}">{{ $row->tgl }}</td>
                </tr>
                @empty
                <tr><td colspan="3" class="{{ config('ui.dashboard_td') }} text-center text-indigo-300/50">Belum ada data</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Barang Keluar Terbaru --}}
    <div class="{{ config('ui.card_container') }}">
        <div class="{{ config('ui.card_header') }}">
            <h3 class="font-semibold text-glass-title text-sm uppercase tracking-wide">Barang Keluar Terbaru</h3>
            @if(session('user')['role'] === 'crew')
                <a href="{{ url('/barang_keluar') }}" class="text-xs text-indigo-300 hover:text-indigo-100 font-medium">Lihat semua →</a>
            @endif
        </div>
        <table class="{{ config('ui.dashboard_table') }} glass-table">
            <thead class="{{ config('ui.dashboard_thead') }}">
                <tr>
                    <th class="{{ config('ui.dashboard_th') }}">Barang</th>
                    <th class="{{ config('ui.dashboard_th') }}">Qty</th>
                    <th class="{{ config('ui.dashboard_th') }}">Tanggal</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recent_bk as $row)
                <tr class="{{ config('ui.dashboard_tr') }}">
                    <td class="{{ config('ui.dashboard_td') }}">{{ $row->nama_barang }}</td>
                    <td class="{{ config('ui.dashboard_td') }}">{{ $row->qty_barang }}</td>
                    <td class="{{ config('ui.dashboard_td') }}">{{ $row->tgl }}</td>
                </tr>
                @empty
                <tr><td colspan="3" class="{{ config('ui.dashboard_td') }} text-center text-indigo-300/50">Belum ada data</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const chartLabels   = @json($chartLabels);
const chartBm       = @json($chartBm);
const chartBk       = @json($chartBk);
const chartKeuangan = @json($chartKeuangan);

const gridColor  = 'rgba(99,102,241,0.12)';
const tickColor  = '#7c88c9';
const baseFont   = { family: 'Inclusive Sans, sans-serif', size: 12 };

// ---- Barang Chart (Bar) ----
new Chart(document.getElementById('chartBarang'), {
    type: 'bar',
    data: {
        labels: chartLabels,
        datasets: [
            {
                label: 'Barang Masuk',
                data: chartBm,
                backgroundColor: 'rgba(99,102,241,0.55)',
                borderColor: 'rgba(99,102,241,0.9)',
                borderWidth: 1.5,
                borderRadius: 6,
            },
            {
                label: 'Barang Keluar',
                data: chartBk,
                backgroundColor: 'rgba(251,146,60,0.5)',
                borderColor: 'rgba(251,146,60,0.9)',
                borderWidth: 1.5,
                borderRadius: 6,
            }
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { labels: { color: tickColor, font: baseFont } },
            tooltip: { mode: 'index', intersect: false }
        },
        scales: {
            x: { grid: { color: gridColor }, ticks: { color: tickColor, font: baseFont } },
            y: { grid: { color: gridColor }, ticks: { color: tickColor, font: baseFont, precision: 0 }, beginAtZero: true }
        }
    }
});

// ---- Keuangan Chart (Line) ----
new Chart(document.getElementById('chartKeuangan'), {
    type: 'line',
    data: {
        labels: chartLabels,
        datasets: [{
            label: 'Total Penjualan (Rp)',
            data: chartKeuangan,
            borderColor: 'rgba(52,211,153,0.9)',
            backgroundColor: 'rgba(52,211,153,0.08)',
            pointBackgroundColor: 'rgba(52,211,153,1)',
            pointRadius: 5,
            pointHoverRadius: 7,
            fill: true,
            tension: 0.4,
            borderWidth: 2.5
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { labels: { color: tickColor, font: baseFont } },
            tooltip: {
                callbacks: {
                    label: ctx => 'Rp ' + ctx.parsed.y.toLocaleString('id-ID')
                }
            }
        },
        scales: {
            x: { grid: { color: gridColor }, ticks: { color: tickColor, font: baseFont } },
            y: {
                grid: { color: gridColor }, ticks: {
                    color: tickColor, font: baseFont,
                    callback: v => 'Rp ' + (v >= 1000 ? (v/1000).toFixed(0) + 'K' : v)
                }, beginAtZero: true
            }
        }
    }
});
</script>
@endpush
