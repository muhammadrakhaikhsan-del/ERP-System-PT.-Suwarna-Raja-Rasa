@extends('layouts.main')
@section('title', 'Kelola Pengguna')
@section('header_title', 'KELOLA PENGGUNA')

@section('content')
<div class="space-y-8">
    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="{{ config('ui.alert_success') }}">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="{{ config('ui.alert_error') }}">
            {{ session('error') }}
        </div>
    @endif
    @if($errors->any())
        <div class="{{ config('ui.alert_error') }}">
            <ul class="list-disc pl-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Area Form Tambah Pengguna ini kita sembunyikan dari halaman utama dan dipindah ke popup modal di bawah ya --}}

    {{-- Tabel Daftar Pengguna --}}
    <div class="{{ config('ui.card_container') }}">
        <div class="{{ config('ui.card_header') }}">
            <h3 class="{{ config('ui.card_title') }}">Daftar Pengguna Sistem</h3>
            {{-- Tombol ini yang akan memunculkan popup modal form tambah pengguna --}}
            <button onclick="openModal('addModal')" class="{{ config('ui.btn_primary') }}">
                <i class="bi bi-plus-lg text-lg"></i> Tambah Pengguna
            </button>
        </div>
        <div class="overflow-x-auto">
            <table class="{{ config('ui.table_main') }} glass-table">
                <thead class="{{ config('ui.table_head') }}">
                    <tr>
                        <th class="{{ config('ui.table_th') }}">No</th>
                        <th class="{{ config('ui.table_th') }}">Nama</th>
                        <th class="{{ config('ui.table_th') }}">Username</th>
                        <th class="{{ config('ui.table_th') }}">Role</th>
                        <th class="{{ config('ui.table_th_center') }}">Aksi (Reset Password)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-indigo-900/50">
                    @forelse($data as $i => $row)
                        <tr class="{{ config('ui.table_row') }}">
                            <td class="{{ config('ui.table_td') }} text-glass-muted font-medium w-16">{{ $i + 1 }}</td>
                            <td class="{{ config('ui.table_td') }}">{{ $row->nama }}</td>
                            <td class="{{ config('ui.table_td') }}">{{ $row->username }}</td>
                            <td class="{{ config('ui.table_td') }}">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold border
                                    @if($row->role === 'manager') bg-purple-900/40 text-purple-300 border-purple-500/40
                                    @elseif($row->role === 'crew') bg-emerald-900/40 text-emerald-300 border-emerald-500/40
                                    @elseif($row->role === 'purchasing') bg-amber-900/40 text-amber-300 border-amber-500/40
                                    @else bg-cyan-900/40 text-cyan-300 border-cyan-500/40 @endif">
                                    {{ ucfirst($row->role) }}
                                </span>
                            </td>
                            <td class="{{ config('ui.table_td') }} flex justify-center gap-3">
                                {{-- Tombol Reset Password --}}
                                <button onclick="openResetModal({{ $row->id }}, '{{ $row->username }}')" class="{{ config('ui.btn_reset') }}" title="Reset Password">
                                    <i class="bi bi-key-fill"></i> Reset
                                </button>

                                {{-- Tombol Hapus --}}
                                @if(session('user')['id'] != $row->id)
                                <button onclick="confirmDelete('{{ url('/pengguna/'.$row->id) }}')" class="{{ config('ui.btn_delete_sm') }}">
                                    <i class="bi bi-trash"></i> Hapus
                                </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="{{ config('ui.table_td') }} text-center text-indigo-300/50">Belum ada data pengguna.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4 px-2">
            {{ $data->links() }}
        </div>
    </div>
</div>

{{-- Popup Modal untuk Menambah Pengguna Baru --}}
<div id="addModal" class="{{ config('ui.modal_overlay') }}">
    <div class="{{ config('ui.modal_content') }} max-w-2xl">
        <div class="{{ config('ui.modal_header') }}">
            <h3 class="{{ config('ui.modal_title') }}">Tambah Pengguna Baru</h3>
            <button onclick="closeModal('addModal')" class="{{ config('ui.modal_close') }}">
                <i class="bi bi-x-lg text-xl"></i>
            </button>
        </div>
        {{-- Kita bungkus inputannya dengan grid agar rapi bersebelahan --}}
        <form action="{{ url('/pengguna') }}" method="POST" class="{{ config('ui.modal_body') }}">
            @csrf
            <div class="{{ config('ui.form_grid_2') }}">
                <div class="{{ config('ui.form_group') }}">
                    <label class="{{ config('ui.form_label') }}">Nama Lengkap</label>
                    <input type="text" name="nama" value="{{ old('nama') }}" class="{{ config('ui.form_input') }}" required placeholder="Contoh: Budi Santoso">
                </div>
                
                <div class="{{ config('ui.form_group') }}">
                    <label class="{{ config('ui.form_label') }}">Username</label>
                    <input type="text" name="username" value="{{ old('username') }}" class="{{ config('ui.form_input') }}" required placeholder="Contoh: budi123">
                </div>
                
                <div class="{{ config('ui.form_group') }}">
                    <label class="{{ config('ui.form_label') }}">Password</label>
                    <input type="password" name="password" class="{{ config('ui.form_input') }}" required placeholder="Minimal 6 karakter">
                </div>
                
                <div class="{{ config('ui.form_group') }}">
                    <label class="{{ config('ui.form_label') }}">Hak Akses (Role)</label>
                    <select name="role" class="{{ config('ui.form_input') }}" required>
                        <option value="">-- Tentukan Role --</option>
                        <option value="crew" {{ old('role') == 'crew' ? 'selected' : '' }}>Crew (Operasional Kasir & Stok)</option>
                        <option value="purchasing" {{ old('role') == 'purchasing' ? 'selected' : '' }}>Purchasing (Pengadaan Barang)</option>
                        <option value="accounting" {{ old('role') == 'accounting' ? 'selected' : '' }}>Accounting (Laporan & Keuangan)</option>
                        <option value="manager" {{ old('role') == 'manager' ? 'selected' : '' }}>Manager (Pemilik/Pimpinan)</option>
                    </select>
                </div>
            </div>
            
            <div class="{{ config('ui.modal_footer') }}">
                <button type="button" onclick="closeModal('addModal')" class="{{ config('ui.btn_secondary') }}">Batal</button>
                <button type="submit" class="{{ config('ui.btn_submit') }}">
                    <i class="bi bi-save2-fill"></i> Simpan Pengguna
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Popup Modal untuk Mengganti Password (Reset) --}}
<div id="resetModal" class="{{ config('ui.modal_overlay') }}">
    <div class="{{ config('ui.modal_content') }} max-w-md">
        <div class="{{ config('ui.modal_header') }}">
            <h3 class="{{ config('ui.modal_title') }}">Ubah Password</h3>
            <button onclick="closeModal('resetModal')" class="{{ config('ui.modal_close') }}">
                <i class="bi bi-x-lg text-xl"></i>
            </button>
        </div>
        <form id="resetForm" method="POST" class="{{ config('ui.modal_body') }} space-y-4">
            @csrf
            <div>
                <p class="text-sm text-indigo-200 mb-3 bg-indigo-900/30 p-3 rounded-lg border border-indigo-500/30">
                    Anda sedang mengubah password untuk: <br>
                    <span id="resetUsername" class="font-bold text-indigo-300 text-base"></span>
                </p>
                <label class="{{ config('ui.form_label') }} mb-1.5">Masukkan Password Baru</label>
                <div class="relative">
                    <input type="password" name="new_password" id="newPassword" class="{{ config('ui.form_input') }}" placeholder="Buat sandi yang aman..." required minlength="6">
                    <button type="button" onclick="toggleModalPassword()" class="absolute right-3 top-3 text-gray-400 hover:text-indigo-600 transition-colors">
                        <i class="bi bi-eye-fill" id="modalEyeIcon"></i>
                    </button>
                </div>
            </div>
            <div class="{{ config('ui.modal_footer') }} mt-5">
                <button type="button" onclick="closeModal('resetModal')" class="{{ config('ui.btn_secondary') }}">Batal</button>
                <button type="submit" class="{{ config('ui.btn_submit') }}">Perbarui</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openResetModal(id, username) {
    openModal('resetModal');
    document.getElementById('resetUsername').innerText = username;
    document.getElementById('resetForm').action = '{{ url("/pengguna") }}/' + id + '/reset-password';
}

function closeResetModal() {
    closeModal('resetModal');
    document.getElementById('newPassword').value = '';
}

function toggleModalPassword() {
    const input = document.getElementById('newPassword');
    const icon = document.getElementById('modalEyeIcon');
    if(input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('bi-eye-fill', 'bi-eye-slash-fill');
    } else {
        input.type = 'password';
        icon.classList.replace('bi-eye-slash-fill', 'bi-eye-fill');
    }
}
</script>
@endpush

@endsection
