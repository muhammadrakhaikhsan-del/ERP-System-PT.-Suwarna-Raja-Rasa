@extends('layouts.main')
@section('title', 'Outlet')
@section('header_title', 'OUTLET')
@section('content')

@if(session('success'))
    <div class="{{ config('ui.alert_success') }}">{{ session('success') }}</div>
@endif

<div class="{{ config('ui.card_container') }}">
    <div class="{{ config('ui.card_header') }}">
        <h3 class="{{ config('ui.card_title') }}">Daftar Outlet</h3>
        <button onclick="openAddModal()" class="{{ config('ui.btn_primary') }}">
            <i class="bi bi-plus-lg text-lg"></i> Tambah Outlet
        </button>
    </div>
    <div class="overflow-x-auto">
        <table class="{{ config('ui.table_main') }} glass-table">
            <thead class="{{ config('ui.table_head') }}">
                <tr>
                    <th class="{{ config('ui.table_th') }}">No</th>
                    <th class="{{ config('ui.table_th') }}">Nama Outlet</th>
                    <th class="{{ config('ui.table_th_center') }}">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-indigo-900/50">
                @forelse($data as $i => $row)
                <tr class="{{ config('ui.table_row') }}">
                    <td class="{{ config('ui.table_td') }} text-glass-muted w-16">{{ $i + 1 }}</td>
                    <td class="{{ config('ui.table_td') }} text-glass-title font-semibold">{{ $row->nama_outlet }}</td>
                    <td class="{{ config('ui.table_td') }} w-40">
                        <div class="{{ config('ui.btn_action_wrap') }}">
                            <button onclick="editOutlet({{ $row->id_outlet }}, '{{ addslashes($row->nama_outlet) }}')" class="{{ config('ui.btn_edit') }}">Ubah</button>
                            <button onclick="confirmDelete('{{ url('/outlet/'.$row->id_outlet) }}')" class="{{ config('ui.btn_delete') }}">Hapus</button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="{{ config('ui.table_td') }} text-center text-indigo-300/50">Belum ada data outlet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4 px-2">
        {{ $data->links() }}
    </div>
</div>

{{-- Modal Form Tambah/Edit Outlet --}}
<div id="addModal" class="{{ config('ui.modal_overlay') }}">
    <div class="{{ config('ui.modal_content') }} max-w-md">
        <div class="{{ config('ui.modal_header') }}">
            <h3 class="{{ config('ui.modal_title') }}" id="modalTitle">Form Outlet</h3>
            <button onclick="closeModal('addModal')" class="{{ config('ui.modal_close') }}">
                <i class="bi bi-x-lg text-xl"></i>
            </button>
        </div>
        <form id="crudForm" method="POST" action="{{ url('/outlet') }}" class="{{ config('ui.modal_body') }} space-y-4">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            
            <div class="{{ config('ui.form_group') }}">
                <label class="{{ config('ui.form_label') }}">Nama Outlet</label>
                <input type="text" id="input_nama_outlet" name="nama_outlet" class="{{ config('ui.form_input') }}" placeholder="Contoh: Outlet Sudirman" required>
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
    document.getElementById('crudForm').action = '{{ url("/outlet") }}';
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('modalTitle').textContent = 'Tambah Outlet';
    document.getElementById('input_nama_outlet').value = '';
}

function editOutlet(id, namaOutlet) {
    openAddModal();
    document.getElementById('crudForm').action = '{{ url("/outlet") }}/' + id;
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('modalTitle').textContent = 'Ubah Outlet';
    document.getElementById('input_nama_outlet').value = namaOutlet;
}
</script>
@endpush
@endsection
