@extends('layouts.main')
@section('title', 'Laporan Keuangan')
@section('header_title', 'LAPORAN KEUANGAN')

@section('content')
<link rel="stylesheet" href="{{ asset('css/print.css') }}?v={{ time() }}">

<div class="space-y-6">

    {{-- Header Print --}}
    <div class="print-header">
        <h2 style="font-size:24px;font-weight:bold;">Laporan Keuangan</h2>
        <p>Periode: {{ date('d M Y', strtotime($start_date)) }} s/d {{ date('d M Y', strtotime($end_date)) }}</p>
    </div>

    {{-- Filter --}}
    <div class="glass-panel p-5 no-print">
        <form action="{{ url('/laporan_keuangan') }}" method="GET" class="flex flex-col sm:flex-row items-end gap-4 flex-wrap">
            <div>
                <label class="block text-sm font-semibold text-glass-subtitle mb-1.5">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ $start_date }}" class="{{ config('ui.form_input') }}">
            </div>
            <div>
                <label class="block text-sm font-semibold text-glass-subtitle mb-1.5">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ $end_date }}" class="{{ config('ui.form_input') }}">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-5 rounded-lg shadow-sm transition-colors">
                    <i class="bi bi-funnel-fill mr-1"></i> Filter
                </button>
                <button type="button" onclick="openModal('printModal')" class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-2 px-5 rounded-lg shadow-sm transition-colors">
                    <i class="bi bi-printer-fill mr-1"></i> Cetak / PDF
                </button>
            </div>
        </form>
    </div>

    {{-- Summary Card --}}
    <div class="grid grid-cols-1 md:grid-cols-1 gap-4">
        <div class="glass-panel p-6 border border-emerald-500/30">
            <p class="text-emerald-400 text-sm font-semibold mb-1 uppercase tracking-wide">Total Pendapatan Periode Ini</p>
            <h3 class="text-3xl font-bold text-emerald-300">Rp {{ number_format($total_pendapatan_all, 0, ',', '.') }}</h3>
            <p class="text-xs text-indigo-300/60 mt-1">{{ date('d M Y', strtotime($start_date)) }} — {{ date('d M Y', strtotime($end_date)) }}</p>
        </div>
    </div>

    {{-- Tabel Rekap Harian --}}
    <div class="{{ config('ui.card_container') }}">
        <div class="{{ config('ui.card_header') }}">
            <h3 class="{{ config('ui.card_title') }}">Rekap Pendapatan Harian</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="{{ config('ui.table_main') }} glass-table">
                <thead class="{{ config('ui.table_head') }}">
                    <tr>
                        <th class="{{ config('ui.table_th') }}">No</th>
                        <th class="{{ config('ui.table_th') }}">Tanggal</th>
                        <th class="{{ config('ui.table_th') }}">Pendapatan (Rp)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-indigo-900/50">
                    @if(count($rekap_harian) === 0)
                    <tr>
                        <td colspan="3" class="{{ config('ui.table_td') }} text-center text-indigo-300/50">Tidak ada data pada periode ini.</td>
                    </tr>
                    @else
                    @php $iter = 1; @endphp
                    @foreach($rekap_harian as $tgl => $pendapatan)
                    <tr class="{{ config('ui.table_row') }}">
                        <td class="{{ config('ui.table_td') }} text-glass-muted">{{ $iter++ }}</td>
                        <td class="{{ config('ui.table_td') }} text-glass-title">{{ date('d M Y', strtotime($tgl)) }}</td>
                        <td class="{{ config('ui.table_td') }} font-semibold text-emerald-300">Rp {{ number_format($pendapatan, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                    @endif
                </tbody>
                @if(count($rekap_harian) > 0)
                <tfoot>
                    <tr class="border-t border-indigo-500/50">
                        <td colspan="2" class="{{ config('ui.table_td') }} font-bold text-glass-title text-right">TOTAL</td>
                        <td class="{{ config('ui.table_td') }} font-bold text-emerald-300 text-lg">Rp {{ number_format($total_pendapatan_all, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>

{{-- Modal Print Preview --}}
<div id="printModal" class="fixed inset-0 bg-black/80 z-[100] hidden items-center justify-center p-4 sm:p-6 opacity-0 transition-opacity duration-300 backdrop-blur-sm no-print">
    <div class="relative w-full max-w-4xl max-h-[90vh] overflow-hidden flex flex-col glass-panel rounded-2xl shadow-2xl transform scale-90 opacity-0 -translate-y-10 transition-all duration-300 border border-indigo-500/30">
        
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
            <div class="w-full bg-[#0b0b2a] rounded-xl shadow-[0_4px_20px_rgba(0,0,0,0.3)] overflow-hidden border border-indigo-500/30 flex flex-col mx-auto min-h-[600px]" id="documentPreviewArea">
                
                {{-- Document Header --}}
                <div class="p-8 border-b border-indigo-500/30 flex flex-col items-center justify-center text-center bg-indigo-900/20">
                    <h1 class="text-3xl font-bold text-glass-title mb-2 tracking-tight uppercase">LAPORAN KEUANGAN</h1>
                    <p class="text-sm font-medium text-indigo-300">Periode: {{ date('d M Y', strtotime($start_date)) }} - {{ date('d M Y', strtotime($end_date)) }}</p>
                </div>

                {{-- Total Highlight --}}
                <div class="px-8 mt-8">
                    <div class="bg-emerald-900/20 border border-emerald-500/30 rounded-xl p-6 flex flex-col items-center justify-center">
                        <span class="text-xs font-semibold text-emerald-400 uppercase tracking-wider mb-2">Total Pendapatan Periode Ini</span>
                        <span class="text-4xl font-bold text-emerald-300">Rp {{ number_format($total_pendapatan_all, 0, ',', '.') }}</span>
                    </div>
                </div>

                {{-- Summary Table --}}
                <div class="p-8 flex-grow">
                    <h3 class="text-xl font-bold text-glass-title mb-4 border-b border-indigo-500/30 pb-3">Rekap Pendapatan Harian</h3>
                    <div class="overflow-x-auto rounded-lg border border-indigo-500/30 bg-indigo-900/20">
                        <table class="w-full text-left border-collapse glass-table">
                            <thead>
                                <tr class="bg-indigo-900/40 text-indigo-300">
                                    <th class="py-3 px-4 font-semibold text-xs uppercase tracking-wider border-b border-indigo-500/30">No</th>
                                    <th class="py-3 px-4 font-semibold text-xs uppercase tracking-wider border-b border-indigo-500/30">Tanggal</th>
                                    <th class="py-3 px-4 font-semibold text-xs uppercase tracking-wider border-b border-indigo-500/30 text-right">Pendapatan (Rp)</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm text-glass-subtitle">
                                @if(count($rekap_harian) === 0)
                                <tr>
                                    <td colspan="3" class="py-4 px-4 text-center text-indigo-300/50">Tidak ada data.</td>
                                </tr>
                                @else
                                    @php $iter = 1; @endphp
                                    @foreach($rekap_harian as $tgl => $pendapatan)
                                    <tr class="border-b border-indigo-500/20 hover:bg-indigo-900/30 transition-colors">
                                        <td class="py-3 px-4 text-glass-muted">{{ $iter++ }}</td>
                                        <td class="py-3 px-4 text-glass-title font-medium">{{ date('d M Y', strtotime($tgl)) }}</td>
                                        <td class="py-3 px-4 text-emerald-300 text-right font-medium">{{ number_format($pendapatan, 0, ',', '.') }}</td>
                                    </tr>
                                    @endforeach
                                @endif
                            </tbody>
                            @if(count($rekap_harian) > 0)
                            <tfoot>
                                <tr class="bg-indigo-900/40 border-t border-indigo-500/30">
                                    <td colspan="2" class="py-4 px-4 font-bold text-glass-title text-right uppercase tracking-wider">Total</td>
                                    <td class="py-4 px-4 font-bold text-emerald-300 text-right text-lg">Rp {{ number_format($total_pendapatan_all, 0, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>
                </div>

                {{-- Document Footer --}}
                <div class="p-6 bg-indigo-900/20 border-t border-indigo-500/30 flex justify-between items-center text-sm font-medium text-indigo-400 mt-auto">
                    <span>Page 1 of 1</span>
                </div>
            </div>
        </div>

        {{-- Floating Action Bar (Bottom of modal) --}}
        <div class="p-4 border-t border-indigo-900/50 flex justify-center gap-4 bg-[#08081e]/80 backdrop-blur-md z-10">
            <button type="button" onclick="printPage()" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold h-12 px-8 rounded-full shadow-[0_4px_15px_rgba(79,70,229,0.3)] hover:-translate-y-1 transition-all flex items-center justify-center gap-2">
                <i class="bi bi-printer text-xl"></i> Cetak
            </button>
            <button type="button" onclick="downloadPDF('Laporan_Keuangan.pdf')" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold h-12 px-8 rounded-full shadow-[0_4px_15px_rgba(16,185,129,0.3)] hover:-translate-y-1 transition-all flex items-center justify-center gap-2">
                <i class="bi bi-file-earmark-pdf text-xl"></i> Simpan PDF
            </button>
        </div>
    </div>
</div>
@endsection