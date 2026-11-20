@extends('layouts.main')
@section('title', 'Laporan Barang')
@section('header_title', 'LAPORAN BARANG')

@section('content')

{{-- Print Stylesheet --}}
<link rel="stylesheet" href="{{ asset('css/print.css') }}?v={{ time() }}">

<div class="space-y-6">
    
    {{-- Header Khusus Print --}}
    <div class="print-header">
        <h2 style="font-size: 24px; font-weight: bold;">RestoLink - Laporan Pergerakan Barang</h2>
        <p>Periode: {{ date('d M Y', strtotime($start_date)) }} s/d {{ date('d M Y', strtotime($end_date)) }}</p>
    </div>

    {{-- Filter Rentang Tanggal --}}
    <div class="glass-panel p-5 no-print">
        <form action="{{ url('/laporan_barang') }}" method="GET" class="flex flex-col sm:flex-row items-end gap-4">
            <div class="w-full sm:w-auto">
                <label class="block text-sm font-semibold text-glass-subtitle mb-1.5">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ $start_date }}" class="{{ config('ui.form_input') }}">
            </div>
            <div class="w-full sm:w-auto">
                <label class="block text-sm font-semibold text-glass-subtitle mb-1.5">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ $end_date }}" class="{{ config('ui.form_input') }}">
            </div>
            <div class="flex gap-2 w-full sm:w-auto">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-5 rounded-lg shadow-sm transition-colors">
                    <i class="bi bi-funnel-fill mr-1"></i> Filter
                </button>
                <button type="button" onclick="openModal('printModal')" class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-2 px-5 rounded-lg shadow-sm transition-colors">
                    <i class="bi bi-printer-fill mr-1"></i> Cetak / PDF
                </button>
            </div>
        </form>
    </div>

    {{-- Tabel Laporan --}}
    <div class="{{ config('ui.card_container') }}">
        <div class="{{ config('ui.card_header') }} no-print">
            <h3 class="{{ config('ui.card_title') }}">Tabel Pergerakan Barang</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="{{ config('ui.table_main') }} glass-table">
                <thead class="glass-header text-glass-title uppercase font-semibold text-xs">
                    <tr>
                        <th class="px-4 py-3 border-r border-indigo-500/20">Nama Barang</th>
                        <th class="px-4 py-3 border-r border-indigo-500/20 text-center">Masuk<br><span class="text-[10px] font-normal text-indigo-300">(Periode ini)</span></th>
                        <th class="px-4 py-3 border-r border-indigo-500/20 text-center">Keluar<br><span class="text-[10px] font-normal text-indigo-300">(Periode ini)</span></th>
                        <th class="px-4 py-3 border-r border-indigo-500/20 text-center">Stok Sistem<br><span class="text-[10px] font-normal text-indigo-300">(Live)</span></th>
                        <th class="px-4 py-3 border-r border-indigo-500/20 text-center">Stok Fisik SO<br><span class="text-[10px] font-normal text-indigo-300">(Terakhir)</span></th>
                        <th class="px-4 py-3 text-center">Selisih SO</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-indigo-900/50">
                    @forelse($laporan as $row)
                        <tr class="{{ config('ui.table_row') }}">
                            <td class="px-4 py-3 font-semibold text-glass-title">{{ $row['nama_barang'] }}</td>
                            <td class="px-4 py-3 text-center text-emerald-400 font-medium">+{{ $row['total_masuk'] }}</td>
                            <td class="px-4 py-3 text-center text-red-400 font-medium">-{{ $row['total_keluar'] }}</td>
                            <td class="px-4 py-3 text-center font-bold text-indigo-200">{{ $row['stok_akhir_sistem'] }}</td>
                            <td class="px-4 py-3 text-center text-glass-subtitle">{{ $row['stok_fisik_so'] }}</td>
                            <td class="px-4 py-3 text-center font-bold">
                                @if($row['selisih'] === '-')
                                    <span class="text-indigo-300/40">-</span>
                                @elseif($row['selisih'] > 0)
                                    <span class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-900/30 text-amber-300 border border-amber-500/40">+{{ $row['selisih'] }}</span>
                                @elseif($row['selisih'] < 0)
                                    <span class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-900/30 text-red-300 border border-red-500/40">{{ $row['selisih'] }}</span>
                                @else
                                    <span class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-900/30 text-indigo-300 border border-indigo-500/40">0</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="{{ config('ui.table_td') }} text-center text-indigo-300/50">Tidak ada data barang.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
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
            {{-- Document Wrapper (This mimics the paper) --}}
            <div class="w-full bg-[#0b0b2a] rounded-xl shadow-[0_4px_20px_rgba(0,0,0,0.3)] overflow-hidden border border-indigo-500/30 flex flex-col mx-auto" id="documentPreviewArea">
                
                {{-- Document Header --}}
                <div class="p-8 border-b border-indigo-500/30 flex flex-col items-center justify-center text-center bg-indigo-900/20">
                    <h1 class="text-3xl font-bold text-glass-title mb-2 tracking-tight uppercase">LAPORAN PERGERAKAN BARANG</h1>
                    <p class="text-sm font-medium text-indigo-300">Periode: {{ date('d/m/Y', strtotime($start_date)) }} - {{ date('d/m/Y', strtotime($end_date)) }}</p>
                </div>

                {{-- Table Section --}}
                <div class="p-8 flex-grow overflow-x-auto">
                    <h2 class="text-xl font-bold text-glass-title mb-6">Tabel Pergerakan Barang</h2>
                    <table class="w-full text-left border-collapse min-w-[800px] glass-table">
                        <thead>
                            <tr class="border-b-2 border-indigo-500/30 text-indigo-300">
                                <th class="py-3 px-4 font-semibold text-xs uppercase tracking-wider">Nama Barang</th>
                                <th class="py-3 px-4 font-semibold text-xs uppercase tracking-wider text-center">Masuk<br><span class="text-[10px] font-normal text-indigo-400">(Periode Ini)</span></th>
                                <th class="py-3 px-4 font-semibold text-xs uppercase tracking-wider text-center">Keluar<br><span class="text-[10px] font-normal text-indigo-400">(Periode Ini)</span></th>
                                <th class="py-3 px-4 font-semibold text-xs uppercase tracking-wider text-center">Stok Sistem<br><span class="text-[10px] font-normal text-indigo-400">(Live)</span></th>
                                <th class="py-3 px-4 font-semibold text-xs uppercase tracking-wider text-center">Stok Fisik SO<br><span class="text-[10px] font-normal text-indigo-400">(Terakhir)</span></th>
                                <th class="py-3 px-4 font-semibold text-xs uppercase tracking-wider text-center">Selisih SO</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm text-glass-subtitle">
                            @forelse($laporan as $row)
                            <tr class="border-b border-indigo-900/50 hover:bg-indigo-900/20 transition-colors">
                                <td class="py-4 px-4 font-bold text-glass-title">{{ $row['nama_barang'] }}</td>
                                <td class="py-4 px-4 text-center font-medium">+{{ $row['total_masuk'] }}</td>
                                <td class="py-4 px-4 text-center font-medium">-{{ $row['total_keluar'] }}</td>
                                <td class="py-4 px-4 text-center font-bold text-indigo-200">{{ $row['stok_akhir_sistem'] }}</td>
                                <td class="py-4 px-4 text-center">{{ $row['stok_fisik_so'] }}</td>
                                <td class="py-4 px-4 text-center font-bold">
                                    @if($row['selisih'] === '-')
                                        <span class="text-indigo-300/40">-</span>
                                    @elseif($row['selisih'] > 0)
                                        <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-900/40 text-emerald-300 border border-emerald-500/40">+{{ $row['selisih'] }}</span>
                                    @elseif($row['selisih'] < 0)
                                        <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-full text-xs font-bold bg-red-900/40 text-red-300 border border-red-500/40">{{ $row['selisih'] }}</span>
                                    @else
                                        <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-full text-xs font-bold bg-indigo-900/40 text-indigo-300 border border-indigo-500/40">0</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="py-6 px-4 text-center text-indigo-300/50">Tidak ada data.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
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
            <button type="button" onclick="downloadPDF('Laporan_Barang.pdf')" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold h-12 px-8 rounded-full shadow-[0_4px_15px_rgba(16,185,129,0.3)] hover:-translate-y-1 transition-all flex items-center justify-center gap-2">
                <i class="bi bi-file-earmark-pdf text-xl"></i> Simpan PDF
            </button>
        </div>
    </div>
</div>

@endsection