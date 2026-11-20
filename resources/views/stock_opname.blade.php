@extends('layouts.main')
@section('title', 'Stock Opname')
@section('header_title', 'STOCK OPNAME')
@section('content')

@if(session('success'))
    <div class="{{ config('ui.alert_success') }}">{{ session('success') }}</div>
@endif

<div class="{{ config('ui.card_container') }}">
    <div class="{{ config('ui.card_header') }}">
        <h3 class="{{ config('ui.card_title') }}">Riwayat Stock Opname</h3>
        <button onclick="openAddModal()" class="{{ config('ui.btn_primary') }}">
            <i class="bi bi-plus-lg text-lg"></i> Tambah Catatan Stok
        </button>
    </div>
    <div class="overflow-x-auto">
        <table class="{{ config('ui.table_main') }} glass-table">
            <thead class="{{ config('ui.table_head') }}">
                <tr>
                    <th class="{{ config('ui.table_th') }}">No</th>
                    <th class="{{ config('ui.table_th') }}">Tenant</th>
                    <th class="{{ config('ui.table_th') }}">Barang</th>
                    <th class="{{ config('ui.table_th') }}">Stok Sistem</th>
                    <th class="{{ config('ui.table_th') }}">Stok Fisik</th>
                    <th class="{{ config('ui.table_th') }}">Selisih</th>
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
                    <td class="{{ config('ui.table_td') }}">{{ $row->stok_sistem }} unit</td>
                    <td class="{{ config('ui.table_td') }}">{{ $row->stok_fisik }} unit</td>
                    <td class="{{ config('ui.table_td') }}">
                        <span class="px-2 py-1 rounded-full text-xs font-semibold
                            {{ $row->selisih < 0 ? 'bg-red-900/40 text-red-300 border border-red-500/40' : ($row->selisih > 0 ? 'bg-emerald-900/40 text-emerald-300 border border-emerald-500/40' : 'bg-indigo-900/30 text-indigo-300 border border-indigo-500/30') }}">
                            {{ $row->selisih > 0 ? '+' : '' }}{{ $row->selisih }}
                        </span>
                    </td>
                    <td class="{{ config('ui.table_td') }}">{{ $row->tgl }}</td>
                    <td class="{{ config('ui.table_td') }}">
                        <div class="{{ config('ui.btn_action_wrap') }}">
                            <button onclick="editStockOpname({{ $row->id_so }}, {{ $row->id_barang }}, {{ $row->stok_fisik }}, '{{ $row->tgl }}')" class="{{ config('ui.btn_edit') }}">Ubah</button>
                            <button onclick="confirmDelete('{{ url('/stock_opname/'.$row->id_so) }}')" class="{{ config('ui.btn_delete') }}">Hapus</button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="{{ config('ui.table_td') }} text-center text-indigo-300/50">Belum ada data stock opname.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4 px-2">
        {{ $data->links() }}
    </div>
</div>

{{-- Modal Form Tambah/Edit Stock Opname --}}
<div id="addModal" class="{{ config('ui.modal_overlay') }}">
    <div class="{{ config('ui.modal_content') }} max-w-md">
        <div class="{{ config('ui.modal_header') }}">
            <h3 class="{{ config('ui.modal_title') }}" id="modalTitle">Form Stock Opname</h3>
            <button onclick="closeModal('addModal')" class="{{ config('ui.modal_close') }}">
                <i class="bi bi-x-lg text-xl"></i>
            </button>
        </div>
        <form id="crudForm" method="POST" action="{{ url('/stock_opname') }}" class="{{ config('ui.modal_body') }} space-y-4">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">

            <div class="{{ config('ui.form_group') }}">
                <label class="{{ config('ui.form_label') }}">Pilih Tenant</label>
                <select id="select_tenant" class="{{ config('ui.form_input') }}" onchange="loadBarangByTenant(this.value)">
                    <option value="">-- Pilih Tenant Terlebih Dahulu --</option>
                    @foreach($tenants as $t)
                        <option value="{{ $t->id_tenant }}">{{ $t->nama_tenant }}</option>
                    @endforeach
                </select>
            </div>

            <div class="{{ config('ui.form_group') }}">
                <label class="{{ config('ui.form_label') }}">Pilih Barang</label>
                <select id="input_id_barang" name="id_barang" class="{{ config('ui.form_input') }}" required disabled onchange="fillStokSistem(this)">
                    <option value="">-- Pilih Tenant dahulu --</option>
                </select>
            </div>
            <div class="{{ config('ui.form_group') }}">
                <label class="{{ config('ui.form_label') }}">Stok Sistem (Otomatis)</label>
                <input type="number" id="display_stok_sistem" class="{{ config('ui.form_input') }} opacity-70 cursor-not-allowed" readonly value="0">
            </div>
            <div class="{{ config('ui.form_group') }}">
                <label class="{{ config('ui.form_label') }}">Stok Fisik (Input Manual)</label>
                <input type="number" id="input_stok_fisik" name="stok_fisik" class="{{ config('ui.form_input') }}" min="0" placeholder="Hitung stok secara fisik" required oninput="calcSelisih()">
            </div>
            <div class="{{ config('ui.form_group') }}">
                <label class="{{ config('ui.form_label') }}">Selisih (Otomatis)</label>
                <input type="number" id="display_selisih" class="{{ config('ui.form_input') }} opacity-70 cursor-not-allowed" readonly value="0">
            </div>
            <div class="{{ config('ui.form_group') }}">
                <label class="{{ config('ui.form_label') }}">Tanggal Opname</label>
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
    id_tenant: b.id_tenant,
    stok: b.qty_barang
}));

function loadBarangByTenant(tenantId, selectedBarangId = null) {
    const select = document.getElementById('input_id_barang');
    select.innerHTML = '<option value="">-- Pilih Barang --</option>';

    if (!tenantId) {
        select.disabled = true;
        select.innerHTML = '<option value="">-- Pilih Tenant dahulu --</option>';
        document.getElementById('display_stok_sistem').value = 0;
        calcSelisih();
        return;
    }

    const filtered = allBarang.filter(b => b.id_tenant == tenantId);
    filtered.forEach(b => {
        const opt = document.createElement('option');
        opt.value = b.id;
        opt.textContent = b.nama + ' (Stok: ' + b.stok + ')';
        opt.dataset.stok = b.stok;
        if (selectedBarangId && b.id == selectedBarangId) opt.selected = true;
        select.appendChild(opt);
    });

    select.disabled = filtered.length === 0;
    if (filtered.length === 0) {
        select.innerHTML = '<option value="">-- Tidak ada barang --</option>';
    }

    if (selectedBarangId) select.dispatchEvent(new Event('change'));
}

function fillStokSistem(select) {
    const stok = select.options[select.selectedIndex]?.dataset.stok || 0;
    document.getElementById('display_stok_sistem').value = stok;
    calcSelisih();
}

function calcSelisih() {
    const sistem = parseInt(document.getElementById('display_stok_sistem').value) || 0;
    const fisik  = parseInt(document.getElementById('input_stok_fisik').value) || 0;
    document.getElementById('display_selisih').value = fisik - sistem;
}

function openAddModal() {
    openModal('addModal');
    document.getElementById('crudForm').action = '{{ url("/stock_opname") }}';
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('modalTitle').textContent = 'Tambah Stock Opname';
    document.getElementById('select_tenant').value = '';
    document.getElementById('input_id_barang').innerHTML = '<option value="">-- Pilih Tenant dahulu --</option>';
    document.getElementById('input_id_barang').disabled = true;
    document.getElementById('display_stok_sistem').value = 0;
    document.getElementById('input_stok_fisik').value = '';
    document.getElementById('display_selisih').value = 0;
    document.getElementById('input_tgl').value = '';
}

function editStockOpname(id, idBarang, stokFisik, tgl) {
    const row = allBarang.find(b => b.id == idBarang);
    const tenantId = row ? row.id_tenant : '';

    openModal('addModal');
    document.getElementById('crudForm').action = '{{ url("/stock_opname") }}/' + id;
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('modalTitle').textContent = 'Ubah Stock Opname';

    document.getElementById('select_tenant').value = tenantId;
    loadBarangByTenant(tenantId, idBarang);

    document.getElementById('input_stok_fisik').value = stokFisik;
    document.getElementById('input_tgl').value = tgl;
    // fillStokSistem and calcSelisih are already triggered by loadBarangByTenant firing 'change' event
}
</script>
@endpush

@endsection
