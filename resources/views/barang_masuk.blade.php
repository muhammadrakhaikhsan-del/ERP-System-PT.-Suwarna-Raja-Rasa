@extends('layouts.main')
@section('title', 'Barang Masuk')
@section('header_title', 'BARANG MASUK')
@section('content')

@if(session('success'))
    <div class="{{ config('ui.alert_success') }}">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="{{ config('ui.alert_error') }}">{{ session('error') }}</div>
@endif

<div class="{{ config('ui.card_container') }}">
    <div class="{{ config('ui.card_header') }}">
        <h3 class="{{ config('ui.card_title') }}">Riwayat Barang Masuk</h3>
        <button onclick="openAddModal()" class="{{ config('ui.btn_primary') }}">
            <i class="bi bi-plus-lg text-lg"></i> Tambah Barang Masuk
        </button>
    </div>
    <div class="overflow-x-auto">
        <table class="{{ config('ui.table_main') }} glass-table">
            <thead class="{{ config('ui.table_head') }}">
                <tr>
                    <th class="{{ config('ui.table_th') }}">No</th>
                    <th class="{{ config('ui.table_th') }}">Tenant</th>
                    <th class="{{ config('ui.table_th') }}">Barang</th>
                    <th class="{{ config('ui.table_th') }}">Qty</th>
                    <th class="{{ config('ui.table_th') }}">Tanggal</th>
                    <th class="{{ config('ui.table_th_center') }}">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-indigo-900/50">
                @forelse($data as $i => $row)
                <tr class="{{ config('ui.table_row') }}">
                    <td class="{{ config('ui.table_td') }} text-glass-muted">{{ $i + 1 }}</td>
                    <td class="{{ config('ui.table_td') }} text-glass-title">{{ $row->barang->tenant->nama_tenant ?? '-' }}</td>
                    <td class="{{ config('ui.table_td') }} text-glass-title font-medium">{{ $row->barang->nama_barang ?? '-' }}</td>
                    <td class="{{ config('ui.table_td') }}">{{ $row->qty_barang }} unit</td>
                    <td class="{{ config('ui.table_td') }}">{{ $row->tgl }}</td>
                    <td class="{{ config('ui.table_td') }}">
                        <div class="{{ config('ui.btn_action_wrap') }}">
                            <button onclick="editBarangMasuk({{ $row->id_bm }}, {{ $row->id_barang }}, {{ $row->qty_barang }}, '{{ $row->tgl }}')" class="{{ config('ui.btn_edit') }}">Ubah</button>
                            <button onclick="confirmDelete('{{ url('/barang_masuk/'.$row->id_bm) }}')" class="{{ config('ui.btn_delete') }}">Hapus</button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="{{ config('ui.table_td') }} text-center text-indigo-300/50">Belum ada data barang masuk.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4 px-2">
        {{ $data->links() }}
    </div>
</div>

{{-- Modal Form Tambah/Edit Barang Masuk --}}
<div id="addModal" class="{{ config('ui.modal_overlay') }}">
    <div class="{{ config('ui.modal_content') }} max-w-lg">
        <div class="{{ config('ui.modal_header') }}">
            <h3 class="{{ config('ui.modal_title') }}" id="modalTitle">Form Barang Masuk</h3>
            <button onclick="closeModal('addModal')" class="{{ config('ui.modal_close') }}">
                <i class="bi bi-x-lg text-xl"></i>
            </button>
        </div>
        <form id="crudForm" method="POST" action="{{ url('/barang_masuk') }}" class="{{ config('ui.modal_body') }} space-y-4">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">

            {{-- Pilih Tenant --}}
            <div class="{{ config('ui.form_group') }}">
                <label class="{{ config('ui.form_label') }}">Pilih Tenant</label>
                <select id="select_tenant" class="{{ config('ui.form_input') }}" onchange="loadBarangByTenant(this.value)">
                    <option value="">-- Pilih Tenant Terlebih Dahulu --</option>
                    @foreach($tenants as $t)
                        <option value="{{ $t->id_tenant }}">{{ $t->nama_tenant }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Pilih Barang (cascade) --}}
            <div class="{{ config('ui.form_group') }}">
                <label class="{{ config('ui.form_label') }}">Pilih Barang</label>
                <select id="input_id_barang" name="id_barang" class="{{ config('ui.form_input') }}" required disabled>
                    <option value="">-- Pilih Tenant dahulu --</option>
                </select>
            </div>

            <div class="{{ config('ui.form_group') }}">
                <label class="{{ config('ui.form_label') }}">Jumlah Masuk (Qty)</label>
                <input type="number" id="input_qty_barang" name="qty_barang" class="{{ config('ui.form_input') }}" min="1" placeholder="Contoh: 50" required>
            </div>
            <div class="{{ config('ui.form_group') }}">
                <label class="{{ config('ui.form_label') }}">Tanggal Masuk</label>
                <input type="date" id="input_tgl" name="tgl" class="{{ config('ui.form_input') }}" required>
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
const rawBarang = @json($barang);
const allBarang = rawBarang.map(b => ({
    id: b.id_barang,
    nama: b.nama_barang,
    id_tenant: b.id_tenant
}));

function loadBarangByTenant(tenantId, selectedBarangId = null) {
    const select = document.getElementById('input_id_barang');
    select.innerHTML = '<option value="">-- Pilih Barang --</option>';

    if (!tenantId) {
        select.disabled = true;
        select.innerHTML = '<option value="">-- Pilih Tenant dahulu --</option>';
        return;
    }

    const filtered = allBarang.filter(b => b.id_tenant == tenantId);
    filtered.forEach(b => {
        const opt = document.createElement('option');
        opt.value = b.id;
        opt.textContent = b.nama;
        if (selectedBarangId && b.id == selectedBarangId) opt.selected = true;
        select.appendChild(opt);
    });

    select.disabled = filtered.length === 0;
    if (filtered.length === 0) {
        select.innerHTML = '<option value="">-- Tidak ada barang --</option>';
    }
}

function openAddModal() {
    openModal('addModal');
    // Reset form
    document.getElementById('crudForm').action = '{{ url("/barang_masuk") }}';
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('modalTitle').textContent = 'Tambah Barang Masuk';
    document.getElementById('select_tenant').value = '';
    document.getElementById('input_id_barang').innerHTML = '<option value="">-- Pilih Tenant dahulu --</option>';
    document.getElementById('input_id_barang').disabled = true;
    document.getElementById('input_qty_barang').value = '';
    document.getElementById('input_tgl').value = '';
}

function editBarangMasuk(id, idBarang, qty, tgl) {
    const row = allBarang.find(b => b.id == idBarang);
    const tenantId = row ? row.id_tenant : '';

    openModal('addModal');
    document.getElementById('crudForm').action = '{{ url("/barang_masuk") }}/' + id;
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('modalTitle').textContent = 'Ubah Barang Masuk';
    document.getElementById('select_tenant').value = tenantId;
    loadBarangByTenant(tenantId, idBarang);
    document.getElementById('input_qty_barang').value = qty;
    document.getElementById('input_tgl').value = tgl;
}
</script>
@endpush

@endsection
