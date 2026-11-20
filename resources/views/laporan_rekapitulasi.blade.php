@extends('layouts.main')
@section('title', 'Laporan Rekapitulasi')
@section('header_title', 'LAPORAN REKAPITULASI')

@section('content')
<link rel="stylesheet" href="{{ asset('css/print.css') }}?v={{ time() }}">

<div class="space-y-6">

    {{-- Header Print --}}
    <div class="print-header">
        <h2 style="font-size:24px;font-weight:bold;">Laporan Rekapitulasi</h2>
        <p>Periode: {{ date('d M Y', strtotime($start_date)) }} s/d {{ date('d M Y', strtotime($end_date)) }}</p>
    </div>

    {{-- Filter --}}
    <div class="glass-panel p-5 no-print">
        <form action="{{ url('/laporan_rekapitulasi') }}" method="GET" class="flex flex-col sm:flex-row items-end gap-4 flex-wrap">
            <div>
                <label class="block text-sm font-semibold text-glass-subtitle mb-1.5">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ $start_date }}" class="{{ config('ui.form_input') }}">
            </div>
            <div>
                <label class="block text-sm font-semibold text-glass-subtitle mb-1.5">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ $end_date }}" class="{{ config('ui.form_input') }}">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-5 rounded-lg transition-colors">
                    <i class="bi bi-funnel-fill mr-1"></i> Filter
                </button>
                <button type="button" onclick="openModal('printModal')" class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-2 px-5 rounded-lg transition-colors">
                    <i class="bi bi-printer-fill mr-1"></i> Cetak / PDF
                </button>
            </div>
        </form>
    </div>

    {{-- Dua Panel Utama --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Panel Performa Keuangan --}}
        <div class="glass-panel p-6 border border-indigo-500/20">
            <div class="flex items-center gap-2 mb-5">
                <i class="bi bi-graph-up-arrow text-emerald-400 text-lg"></i>
                <h3 class="text-glass-title font-bold text-lg">Performa Keuangan</h3>
            </div>

            {{-- Total Pendapatan (POINT 1) --}}
            <div class="mb-4 p-4 rounded-xl bg-emerald-900/20 border border-emerald-500/30">
                <p class="text-xs font-semibold text-emerald-400 uppercase tracking-wide mb-1">Total Pendapatan</p>
                <p class="text-2xl font-bold text-emerald-300">Rp {{ number_format($pendapatan, 0, ',', '.') }}</p>
                <p class="text-xs text-emerald-400/60 mt-1">Dari laporan keuangan harian</p>
            </div>

            {{-- Total Keluar Barang (POINT 2) --}}
            <div class="mb-4 p-4 rounded-xl bg-orange-900/20 border border-orange-500/30">
                <p class="text-xs font-semibold text-orange-400 uppercase tracking-wide mb-1">Total Keluar Barang</p>
                <p class="text-2xl font-bold text-orange-300">Rp {{ number_format($total_keluar_barang, 0, ',', '.') }}</p>
                <p class="text-xs text-orange-400/60 mt-1">Qty keluar × Harga barang ({{ $total_item_keluar }} unit)</p>
            </div>

            {{-- Selisih Penjualan --}}
            <div class="p-4 rounded-xl bg-red-900/20 border border-red-500/30">
                <p class="text-xs font-semibold text-red-400 uppercase tracking-wide mb-1">Selisih Penjualan</p>
                <p class="text-2xl font-bold text-red-300">
                    {{ $selisih_penjualan >= 0 ? '+' : '' }}Rp {{ number_format($selisih_penjualan, 0, ',', '.') }}
                </p>
                <p class="text-xs text-red-400/60 mt-1">
                    Nominal: Pendapatan − Total Keluar Barang
                </p>
            </div>
        </div>

        {{-- Panel Pergerakan Fisik Barang --}}
        <div class="glass-panel p-6 border border-indigo-500/20">
            <div class="flex items-center gap-2 mb-5">
                <i class="bi bi-boxes text-indigo-400 text-lg"></i>
                <h3 class="text-glass-title font-bold text-lg">Pergerakan Fisik Barang</h3>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-5">
                <div class="text-center p-4 rounded-xl bg-indigo-900/30 border border-indigo-500/30">
                    <p class="text-xs font-semibold text-indigo-400 uppercase tracking-wide mb-1">Item Masuk</p>
                    <p class="text-3xl font-bold text-indigo-300">{{ $total_item_masuk }}</p>
                    <p class="text-xs text-indigo-400/60">unit</p>
                </div>
                <div class="text-center p-4 rounded-xl bg-purple-900/30 border border-purple-500/30">
                    <p class="text-xs font-semibold text-purple-400 uppercase tracking-wide mb-1">Item Keluar</p>
                    <p class="text-3xl font-bold text-purple-300">{{ $total_item_keluar }}</p>
                    <p class="text-xs text-purple-400/60">unit</p>
                </div>
            </div>

            {{-- Barang Bermasalah (selisih SO) --}}
            @if(count($barang_bermasalah) > 0)
            <div>
                <p class="text-xs font-semibold text-amber-400 uppercase tracking-wide mb-2">Temuan Stock Opname</p>
                <div class="overflow-x-auto rounded-xl">
                    <table class="w-full text-sm glass-table">
                        <thead>
                            <tr class="glass-header">
                                <th class="{{ config('ui.table_th') }}">Barang</th>
                                <th class="{{ config('ui.table_th') }}">Selisih</th>
                                <th class="{{ config('ui.table_th') }}">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-indigo-900/50">
                            @foreach($barang_bermasalah as $b)
                            <tr class="{{ config('ui.table_row') }}">
                                <td class="{{ config('ui.table_td') }} text-glass-title">{{ $b['nama'] }}</td>
                                <td class="{{ config('ui.table_td') }}">
                                    <span class="px-2 py-0.5 rounded-full text-xs font-bold {{ $b['selisih'] < 0 ? 'text-red-300 bg-red-900/30' : 'text-emerald-300 bg-emerald-900/30' }}">
                                        {{ $b['selisih'] > 0 ? '+' : '' }}{{ $b['selisih'] }}
                                    </span>
                                </td>
                                <td class="{{ config('ui.table_td') }} text-glass-muted">{{ $b['tgl'] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @else
            <div class="text-center py-6 text-indigo-300/40">
                <i class="bi bi-check-circle text-2xl"></i>
                <p class="text-sm mt-1">Tidak ada selisih stok opname</p>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Modal Print Preview --}}
<div id="printModal" class="fixed inset-0 bg-black/80 z-[100] hidden items-center justify-center p-4 sm:p-6 opacity-0 transition-opacity duration-300 backdrop-blur-sm no-print">
    <div class="relative w-full max-w-5xl max-h-[90vh] overflow-hidden flex flex-col glass-panel rounded-2xl shadow-2xl transform scale-90 opacity-0 -translate-y-10 transition-all duration-300 border border-indigo-500/30">
        
        {{-- Modal Topbar --}}
        <div class="p-4 border-b border-indigo-900/50 flex justify-between items-center bg-[#08081e]/80 backdrop-blur-md z-10">
            <h2 class="text-lg font-bold text-glass-title flex items-center gap-2"><i class="bi bi-file-earmark-text text-indigo-400"></i> Preview Dokumen</h2>
            <button type="button" onclick="closeModal('printModal')" class="text-indigo-300 hover:text-white transition-colors">
                <i class="bi bi-x-lg text-xl"></i>
            </button>
        </div>

        {{-- Scrollable Preview Area --}}
        <div class="p-4 sm:p-8 overflow-y-auto flex-1 bg-black/20">
            {{-- Document Wrapper --}}
            <div class="w-full bg-[#0b0b2a] rounded-xl shadow-[0_4px_20px_rgba(0,0,0,0.3)] overflow-hidden border border-indigo-500/30 flex flex-col mx-auto" id="documentPreviewArea">
                
                {{-- Document Header --}}
                <div class="p-8 flex flex-col items-center justify-center text-center">
                    <h1 class="text-3xl font-bold text-indigo-400 mb-2 tracking-wide uppercase">LAPORAN REKAPITULASI</h1>
                    <p class="text-sm font-medium text-indigo-300">Ringkasan performa keuangan dan pergerakan fisik barang.</p>
                    <p class="text-xs text-indigo-400/60 mt-1">Periode: {{ date('d/m/Y', strtotime($start_date)) }} - {{ date('d/m/Y', strtotime($end_date)) }}</p>
                </div>

                {{-- Two Columns Layout --}}
                <div class="p-8 grid grid-cols-1 lg:grid-cols-2 gap-8 border-t border-indigo-500/30 bg-indigo-900/10">
                    
                    {{-- Left Column: Performa Keuangan --}}
                    <div class="glass-panel p-6 rounded-xl border border-indigo-500/30 flex flex-col gap-6">
                        <div class="flex items-center gap-3 border-b border-indigo-500/30 pb-4">
                            <i class="bi bi-graph-up-arrow text-emerald-400 text-2xl"></i>
                            <h2 class="text-xl font-bold text-glass-title">Performa Keuangan</h2>
                        </div>
                        
                        {{-- Total Pendapatan --}}
                        <div class="bg-emerald-900/20 border border-emerald-500/30 rounded-lg p-5">
                            <p class="text-xs font-semibold text-emerald-400 uppercase tracking-wide mb-2">Total Pendapatan</p>
                            <p class="text-3xl font-bold text-emerald-300 mb-1">Rp {{ number_format($pendapatan, 0, ',', '.') }}</p>
                            <p class="text-xs text-emerald-400/60">Dari laporan keuangan harian</p>
                        </div>
                        
                        {{-- Total Keluar Barang --}}
                        <div class="bg-indigo-900/30 border border-indigo-500/30 rounded-lg p-5">
                            <p class="text-xs font-semibold text-indigo-400 uppercase tracking-wide mb-2">Total Keluar Barang</p>
                            <p class="text-3xl font-bold text-glass-title mb-1">Rp {{ number_format($total_keluar_barang, 0, ',', '.') }}</p>
                            <p class="text-xs text-indigo-400/60">Qty keluar × Harga barang ({{ $total_item_keluar }} unit)</p>
                        </div>
                        
                        {{-- Selisih Penjualan --}}
                        <div class="bg-indigo-600/20 border border-indigo-500/50 rounded-lg p-5">
                            <p class="text-xs font-semibold text-indigo-300 uppercase tracking-wide mb-2">Selisih Penjualan</p>
                            <p class="text-3xl font-bold text-indigo-400 mb-1">{{ $selisih_penjualan >= 0 ? '+' : '' }}Rp {{ number_format($selisih_penjualan, 0, ',', '.') }}</p>
                            <p class="text-xs text-indigo-300/60">Nominal: Pendapatan − Total Keluar Barang</p>
                        </div>
                    </div>

                    {{-- Right Column: Pergerakan Fisik Barang --}}
                    <div class="glass-panel p-6 rounded-xl border border-indigo-500/30 flex flex-col gap-6">
                        <div class="flex items-center gap-3 border-b border-indigo-500/30 pb-4">
                            <i class="bi bi-boxes text-indigo-400 text-2xl"></i>
                            <h2 class="text-xl font-bold text-glass-title">Pergerakan Fisik Barang</h2>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            {{-- Item Masuk --}}
                            <div class="bg-indigo-900/30 border border-indigo-500/30 rounded-lg p-4 text-center">
                                <p class="text-xs font-semibold text-indigo-400 uppercase tracking-wide mb-2">Item Masuk</p>
                                <p class="text-3xl font-bold text-glass-title mb-1">{{ $total_item_masuk }}</p>
                                <p class="text-xs text-indigo-400/60">unit</p>
                            </div>
                            {{-- Item Keluar --}}
                            <div class="bg-indigo-900/30 border border-indigo-500/30 rounded-lg p-4 text-center">
                                <p class="text-xs font-semibold text-indigo-400 uppercase tracking-wide mb-2">Item Keluar</p>
                                <p class="text-3xl font-bold text-glass-title mb-1">{{ $total_item_keluar }}</p>
                                <p class="text-xs text-indigo-400/60">unit</p>
                            </div>
                        </div>
                        
                        {{-- Temuan Stock Opname --}}
                        <div>
                            <h3 class="text-xs font-semibold text-glass-title uppercase tracking-wide mb-3">Temuan Stock Opname</h3>
                            <div class="overflow-x-auto rounded-lg border border-indigo-500/30 bg-indigo-900/20">
                                <table class="w-full text-left border-collapse glass-table">
                                    <thead>
                                        <tr class="text-glass-title font-semibold text-xs bg-indigo-900/40">
                                            <th class="py-3 px-4 border-b border-indigo-500/30">Barang</th>
                                            <th class="py-3 px-4 border-b border-indigo-500/30">Selisih</th>
                                            <th class="py-3 px-4 border-b border-indigo-500/30">Tanggal</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-sm text-glass-subtitle">
                                        @forelse($barang_bermasalah as $b)
                                        <tr class="border-b border-indigo-500/20 last:border-b-0 hover:bg-indigo-900/30 transition-colors">
                                            <td class="py-3 px-4 font-medium text-glass-title">{{ $b['nama'] }}</td>
                                            <td class="py-3 px-4">
                                                <span class="inline-flex items-center justify-center px-2 py-0.5 rounded-full text-xs font-bold {{ $b['selisih'] < 0 ? 'text-red-300 bg-red-900/30 border border-red-500/40' : 'text-emerald-300 bg-emerald-900/30 border border-emerald-500/40' }}">
                                                    {{ $b['selisih'] > 0 ? '+' : '' }}{{ $b['selisih'] }}
                                                </span>
                                            </td>
                                            <td class="py-3 px-4 text-xs">{{ date('Y-m-d', strtotime($b['tgl'])) }}</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="3" class="py-4 px-4 text-center text-indigo-300/50">Tidak ada selisih.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Document Footer --}}
                <div class="p-6 bg-indigo-900/20 border-t border-indigo-500/30 flex justify-between items-center text-sm font-medium text-indigo-400">
                    <span>Generated by Secure Portal</span>
                    <span>Page 1 of 1</span>
                </div>
            </div>
        </div>

        {{-- Floating Action Bar (Bottom of modal) --}}
        <div class="p-4 border-t border-indigo-900/50 flex justify-center gap-4 bg-[#08081e]/80 backdrop-blur-md z-10">
            <button type="button" onclick="printPage()" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold h-12 px-8 rounded-full shadow-[0_4px_15px_rgba(79,70,229,0.3)] hover:-translate-y-1 transition-all flex items-center justify-center gap-2">
                <i class="bi bi-printer text-xl"></i> Cetak
            </button>
            <button type="button" onclick="downloadPDF('Laporan_Rekapitulasi.pdf')" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold h-12 px-8 rounded-full shadow-[0_4px_15px_rgba(16,185,129,0.3)] hover:-translate-y-1 transition-all flex items-center justify-center gap-2">
                <i class="bi bi-file-earmark-pdf text-xl"></i> Simpan PDF
            </button>
        </div>
    </div>
</div>
@endsection