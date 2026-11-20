@extends('layouts.main')
@section('title', 'Barang')
@section('header_title', 'MASTER DATA BARANG')
@section('content')

@if(session('success'))
    <div class="{{ config('ui.alert_success') }}">{{ session('success') }}</div>
@endif

<div class="{{ config('ui.card_container') }}">
    <div class="{{ config('ui.card_header') }}">
        <h3 class="{{ config('ui.card_title') }}">Daftar Barang</h3>
        <button onclick="openAddModal()" class="{{ config('ui.btn_primary') }}">
            <i class="bi bi-plus-lg text-lg"></i> Tambah Barang
        </button>
    </div>
    <div class="overflow-x-auto">
        <table class="{{ config('ui.table_main') }} glass-table">
            <thead class="{{ config('ui.table_head') }}">
                <tr>
                    <th class="{{ config('ui.table_th') }}">No</th>
                    <th class="{{ config('ui.table_th') }}">Tenant</th>
                    <th class="{{ config('ui.table_th') }}">Nama Barang</th>
                    <th class="{{ config('ui.table_th') }}">Stok Saat Ini</th>
                    <th class="{{ config('ui.table_th') }}">Harga (Rp)</th>
                    <th class="{{ config('ui.table_th_center') }}">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-indigo-900/50">
                @forelse($data as $i => $row)
                <tr class="{{ config('ui.table_row') }}">
                    <td class="{{ config('ui.table_td') }} text-glass-muted">{{ $i + 1 }}</td>
                    <td class="{{ config('ui.table_td') }} text-glass-subtitle">{{ $row->tenant->nama_tenant ?? '-' }}</td>
                    <td class="{{ config('ui.table_td') }} text-glass-title font-medium">{{ $row->nama_barang }}</td>
                    <td class="{{ config('ui.table_td') }}">
                        <span class="px-2 py-1 rounded-full text-xs font-semibold bg-indigo-900/40 text-indigo-300 border border-indigo-500/40">
                            {{ $row->qty_barang }} unit
                        </span>
                    </td>
                    <td class="{{ config('ui.table_td') }} text-emerald-300 font-semibold">Rp {{ number_format($row->harga_barang, 0, ',', '.') }}</td>
                    <td class="{{ config('ui.table_td') }}">
                        <div class="{{ config('ui.btn_action_wrap') }}">
                            <button onclick="editBarang({{ $row->id_barang }}, {{ $row->id_tenant }}, '{{ addslashes($row->nama_barang) }}', '{{ $row->harga_barang }}')" class="{{ config('ui.btn_edit') }}">Ubah</button>
                            <button onclick="confirmDelete('{{ url('/barang/'.$row->id_barang) }}')" class="{{ config('ui.btn_delete') }}">Hapus</button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="{{ config('ui.table_td') }} text-center text-indigo-300/50">Belum ada data barang.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4 px-2">
        {{ $data->links() }}
    </div>
</div>

{{-- Modal Form Tambah/Edit Barang --}}
<div id="addModal" class="{{ config('ui.modal_overlay') }}">
    <div class="{{ config('ui.modal_content') }} max-w-lg">
        <div class="{{ config('ui.modal_header') }}">
            <h3 class="{{ config('ui.modal_title') }}" id="modalTitle">Form Barang</h3>
            <button onclick="closeModal('addModal')" class="{{ config('ui.modal_close') }}">
                <i class="bi bi-x-lg text-xl"></i>
            </button>
        </div>
        <form id="crudForm" method="POST" action="{{ url('/barang') }}" class="{{ config('ui.modal_body') }} space-y-4">
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
                <label class="{{ config('ui.form_label') }}">Nama Barang</label>
                <input type="text" id="input_nama_barang" name="nama_barang" class="{{ config('ui.form_input') }}" placeholder="Contoh: Nasi Goreng" required>
            </div>
            <div class="{{ config('ui.form_group') }}">
                <label class="{{ config('ui.form_label') }}">Harga Barang (Rp)</label>
                <input type="number" id="input_harga_barang" name="harga_barang" class="{{ config('ui.form_input') }}" placeholder="Contoh: 15000" min="0" required>
            </div>
            <p class="text-xs text-indigo-300/60"><i class="bi bi-info-circle me-1"></i>Stok dikelola otomatis via Barang Masuk / Keluar.</p>

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
    document.getElementById('crudForm').action = '{{ url("/barang") }}';
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('modalTitle').textContent = 'Tambah Barang';
    document.getElementById('input_id_tenant').value = '';
    document.getElementById('input_nama_barang').value = '';
    document.getElementById('input_harga_barang').value = '';
}

function editBarang(id, idTenant, nama, harga) {
    openModal('addModal');
    document.getElementById('crudForm').action = '{{ url("/barang") }}/' + id;
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('modalTitle').textContent = 'Ubah Barang';
    document.getElementById('input_id_tenant').value = idTenant;
    document.getElementById('input_nama_barang').value = nama;
    document.getElementById('input_harga_barang').value = harga;
}
</script>
@endpush

@endsection
