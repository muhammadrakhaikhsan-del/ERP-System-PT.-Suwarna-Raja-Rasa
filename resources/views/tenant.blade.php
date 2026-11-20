@extends('layouts.main')
@section('title', 'Tenant')
@section('header_title', 'TENANT')
@section('content')

@if(session('success'))
    <div class="{{ config('ui.alert_success') }}">{{ session('success') }}</div>
@endif

<div class="{{ config('ui.card_container') }}">
    <div class="{{ config('ui.card_header') }}">
        <h3 class="{{ config('ui.card_title') }}">Daftar Tenant</h3>
        <button onclick="openAddModal()" class="{{ config('ui.btn_primary') }}">
            <i class="bi bi-plus-lg text-lg"></i> Tambah Tenant
        </button>
    </div>
    <div class="overflow-x-auto">
        <table class="{{ config('ui.table_main') }} glass-table">
            <thead class="{{ config('ui.table_head') }}">
                <tr>
                    <th class="{{ config('ui.table_th') }}">No</th>
                    <th class="{{ config('ui.table_th') }}">Outlet</th>
                    <th class="{{ config('ui.table_th') }}">Nama Tenant</th>
                    <th class="{{ config('ui.table_th_center') }}">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-indigo-900/50">
                @forelse($data as $i => $row)
                <tr class="{{ config('ui.table_row') }}">
                    <td class="{{ config('ui.table_td') }} text-glass-muted">{{ $i + 1 }}</td>
                    <td class="{{ config('ui.table_td') }}">{{ $row->outlet->nama_outlet ?? '-' }}</td>
                    <td class="{{ config('ui.table_td') }} text-glass-title font-semibold">{{ $row->nama_tenant }}</td>
                    <td class="{{ config('ui.table_td') }}">
                        <div class="{{ config('ui.btn_action_wrap') }}">
                            <button onclick="editTenant({{ $row->id_tenant }}, {{ $row->id_outlet }}, '{{ addslashes($row->nama_tenant) }}')" class="{{ config('ui.btn_edit') }}">Ubah</button>
                            <button onclick="confirmDelete('{{ url('/tenant/'.$row->id_tenant) }}')" class="{{ config('ui.btn_delete') }}">Hapus</button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="{{ config('ui.table_td') }} text-center text-indigo-300/50">Belum ada data tenant.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4 px-2">
        {{ $data->links() }}
    </div>
</div>

{{-- Modal Form Tambah/Edit Tenant --}}
<div id="addModal" class="{{ config('ui.modal_overlay') }}">
    <div class="{{ config('ui.modal_content') }} max-w-md">
        <div class="{{ config('ui.modal_header') }}">
            <h3 class="{{ config('ui.modal_title') }}" id="modalTitle">Form Tenant</h3>
            <button onclick="closeModal('addModal')" class="{{ config('ui.modal_close') }}">
                <i class="bi bi-x-lg text-xl"></i>
            </button>
        </div>
        <form id="crudForm" method="POST" action="{{ url('/tenant') }}" class="{{ config('ui.modal_body') }} space-y-4">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            
            <div class="{{ config('ui.form_group') }}">
                <label class="{{ config('ui.form_label') }}">Pilih Outlet</label>
                <select id="input_id_outlet" name="id_outlet" class="{{ config('ui.form_input') }}" required>
                    <option value="">-- Pilih Outlet --</option>
                    @foreach($outlets as $o)
                        <option value="{{ $o->id_outlet }}">{{ $o->nama_outlet }}</option>
                    @endforeach
                </select>
            </div>
            <div class="{{ config('ui.form_group') }}">
                <label class="{{ config('ui.form_label') }}">Nama Tenant</label>
                <input type="text" id="input_nama_tenant" name="nama_tenant" class="{{ config('ui.form_input') }}" placeholder="Contoh: Tenant Makanan" required>
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
    document.getElementById('crudForm').action = '{{ url("/tenant") }}';
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('modalTitle').textContent = 'Tambah Tenant';
    document.getElementById('input_id_outlet').value = '';
    document.getElementById('input_nama_tenant').value = '';
}

function editTenant(id, idOutlet, namaTenant) {
    openModal('addModal');
    document.getElementById('crudForm').action = '{{ url("/tenant") }}/' + id;
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('modalTitle').textContent = 'Ubah Tenant';
    document.getElementById('input_id_outlet').value = idOutlet;
    document.getElementById('input_nama_tenant').value = namaTenant;
}
</script>
@endpush
@endsection
