@extends('layouts.main')
@section('title', 'Keuangan Harian')
@section('header_title', 'KEUANGAN HARIAN')
@section('content')

@if(session('success'))
    <div class="{{ config('ui.alert_success') }}">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="{{ config('ui.alert_error') }}">{{ session('error') }}</div>
@endif

<div class="{{ config('ui.card_container') }}">
    <div class="{{ config('ui.card_header') }}">
        <h3 class="{{ config('ui.card_title') }}">Catatan Keuangan Harian</h3>
        <button onclick="openAddModal()" class="{{ config('ui.btn_primary') }}">
            <i class="bi bi-plus-lg text-lg"></i> Tambah Keuangan
        </button>
    </div>
    <div class="overflow-x-auto">
        <table class="{{ config('ui.table_main') }} glass-table">
            <thead class="{{ config('ui.table_head') }}">
                <tr>
                    <th class="{{ config('ui.table_th') }}">No</th>
                    <th class="{{ config('ui.table_th') }}">Tenant</th>
                    <th class="{{ config('ui.table_th') }}">Tanggal</th>
                    <th class="{{ config('ui.table_th') }}">Total Penjualan (Rp)</th>
                    <th class="{{ config('ui.table_th_center') }}">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-indigo-900/50">
                @forelse($data as $i => $row)
                <tr class="{{ config('ui.table_row') }}">
                    <td class="{{ config('ui.table_td') }} text-glass-muted">{{ $i + 1 }}</td>
                    <td class="{{ config('ui.table_td') }} text-glass-title">{{ $row->tenant->nama_tenant ?? '-' }}</td>
                    <td class="{{ config('ui.table_td') }}">{{ $row->tgl }}</td>
                    <td class="{{ config('ui.table_td') }} font-semibold text-emerald-300">Rp {{ number_format($row->total_penjualan, 0, ',', '.') }}</td>
                    <td class="{{ config('ui.table_td') }}">
                        <div class="{{ config('ui.btn_action_wrap') }}">
                            <button onclick="editKeuangan({{ $row->id_kh }}, {{ $row->id_tenant }}, '{{ $row->tgl }}', {{ $row->total_penjualan }})" class="{{ config('ui.btn_edit') }}">Ubah</button>
                            <button onclick="confirmDelete('{{ url('/keuangan_harian/'.$row->id_kh) }}')" class="{{ config('ui.btn_delete') }}">Hapus</button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="{{ config('ui.table_td') }} text-center text-indigo-300/50">Belum ada data keuangan harian.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4 px-2">
        {{ $data->links() }}
    </div>
</div>

{{-- Modal Tambah/Edit --}}
<div id="addModal" class="{{ config('ui.modal_overlay') }}">
    <div class="{{ config('ui.modal_content') }} max-w-md">
        <div class="{{ config('ui.modal_header') }}">
            <h3 class="{{ config('ui.modal_title') }}" id="modalTitle">Form Keuangan Harian</h3>
            <button onclick="closeModal('addModal')" class="{{ config('ui.modal_close') }}">
                <i class="bi bi-x-lg text-xl"></i>
            </button>
        </div>
        <form id="crudForm" method="POST" action="{{ url('/keuangan_harian') }}" class="{{ config('ui.modal_body') }} space-y-4">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">

            <div class="{{ config('ui.form_group') }}">
                <label class="{{ config('ui.form_label') }}">Pilih Tenant</label>
                <select id="input_id_tenant" name="id_tenant" class="{{ config('ui.form_input') }}" required>
                    <option value="">-- Pilih Tenant --</option>
                    @foreach($tenants as $t)
                        <option value="{{ $t->id_tenant }}">{{ $t->nama_tenant }}</option>
                    @endforeach
                </select>
            </div>
            <div class="{{ config('ui.form_group') }}">
                <label class="{{ config('ui.form_label') }}">Tanggal Transaksi</label>
                <input type="date" id="input_tgl" name="tgl" class="{{ config('ui.form_input') }}" required>
            </div>
            <div class="{{ config('ui.form_group') }}">
                <label class="{{ config('ui.form_label') }}">Total Penjualan (Rp)</label>
                <input type="number" id="input_total" name="total_penjualan" class="{{ config('ui.form_input') }}" min="0" placeholder="Contoh: 500000" required>
            </div>

            <div class="{{ config('ui.modal_footer') }}">
                <button type="button" onclick="closeModal('addModal')" class="{{ config('ui.btn_secondary') }}">Batal</button>
                <button type="submit" id="btnSubmit" class="{{ config('ui.btn_submit') }}">
                    <i class="bi bi-save2-fill"></i> Simpan
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openAddModal() {
    openModal('addModal');
    document.getElementById('crudForm').action = '{{ url("/keuangan_harian") }}';
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('modalTitle').textContent = 'Tambah Keuangan Harian';
    document.getElementById('input_id_tenant').value = '';
    document.getElementById('input_tgl').value = '';
    document.getElementById('input_total').value = '';
}

function editKeuangan(id, idTenant, tgl, total) {
    openModal('addModal');
    document.getElementById('crudForm').action = '{{ url("/keuangan_harian") }}/' + id;
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('modalTitle').textContent = 'Ubah Keuangan Harian';
    document.getElementById('input_id_tenant').value = idTenant;
    document.getElementById('input_tgl').value = tgl;
    document.getElementById('input_total').value = total;
}
</script>
@endpush

@endsection
