@extends('layouts.main')

@section('title', 'Manajemen Data Petugas')
<link rel="icon" href="{{ asset('img/logo.png') }}">

@section('content')
<div class="container">
    {{-- Tombol Tambah --}}
    <div class="d-flex justify-content-end align-items-center mb-3">
        @can('isAdmin')
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahModal">
            <i class="bi bi-plus-circle me-2"></i> Tambah Petugas Baru
        </button>
        @endcan
    </div>

    {{-- Notifikasi --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Letakkan di bawah @if (session('success')) ... @endif --}}

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Tabel Data --}}
    <div class="card">
        <div class="card-body">
            <div class="table table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>No. Telepon</th> {{-- TAMBAHAN --}}
                        @can('isAdmin')
                        <th class = "text-center">Aksi</th> 
                        @endcan
                    </tr>
                </thead>
                <tbody>
                    @forelse ($petugasList as $petugas)
                        <tr>
                            <td>{{ $petugas->name }}</td>
                            <td>{{ $petugas->email }}</td>
                            <td>{{ $petugas->telepon ?? '-' }}</td> {{-- TAMBAHAN --}}
                            @can('isAdmin')
                            <td>
                                <div class="d-flex justify-content-center gap-1">
                                {{-- PERHATIKAN TAMBAHAN DATA ATTRIBUTE DI BAWAH INI --}}
                                <button type="button" class="btn btn-sm btn-warning text-white btn-aksi" data-bs-toggle="modal" data-bs-target="#editModal"
                                        data-id="{{ $petugas->id }}"
                                        data-nama="{{ $petugas->name }}"
                                        data-email="{{ $petugas->email }}"
                                        data-telepon="{{ $petugas->telepon }}" 
                                        data-alamat="{{ $petugas->alamat }}">
                                    <i class="bi bi-pencil-square me-1"></i>Edit
                                </button>
                                {{-- ... tombol hapus tetep sama ... --}}
                                <button type="button" class="btn btn-sm btn-danger btn-aksi" data-bs-toggle="modal" data-bs-target="#hapusModal"
                                        data-id="{{ $petugas->id }}">
                                    <i class="bi bi-trash3 me-1"></i>Hapus
                                </button>
                                </div>
                            </td>
                            @endcan
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center">Belum ada data petugas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('modal')
<div class="modal fade" id="tambahModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Petugas Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('petugas.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">No. Telepon (WA)</label>
                        <input type="text" class="form-control" name="telepon" placeholder="08..." required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alamat Lengkap</label>
                        <textarea class="form-control" name="alamat" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                        <input type="password" class="form-control" name="password_confirmation" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Data Petugas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="editForm">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_name" class="form-label">Nama</label>
                        <input type="text" class="form-control" id="edit_name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="edit_email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_telepon" class="form-label">No. Telepon</label>
                        <input type="text" class="form-control" id="edit_telepon" name="telepon">
                    </div>
                    <div class="mb-3">
                        <label for="edit_alamat" class="form-label">Alamat</label>
                        <textarea class="form-control" id="edit_alamat" name="alamat" rows="2"></textarea>
                    </div>
                    <hr>
                    {{-- Di dalam Modal Edit --}}

                    <div class="mb-3">
                        <label for="edit_password" class="form-label">Password Baru</label>
                        {{-- TAMBAHKAN: autocomplete="new-password" --}}
                        <input type="password" class="form-control" id="edit_password" name="password" autocomplete="new-password">
                    </div>

                    <div class="mb-3">
                        <label for="edit_password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                        {{-- TAMBAHKAN: autocomplete="new-password" --}}
                        <input type="password" class="form-control" id="edit_password_confirmation" name="password_confirmation" autocomplete="new-password">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="hapusModal" tabindex="-1">
     <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body"><p>Apakah Anda yakin ingin menghapus data petugas ini?</p></div>
            <div class="modal-footer">
                <form method="POST" id="hapusForm">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Script untuk Modal Edit Petugas
    const editModal = document.getElementById('editModal');
    if (editModal) {
        editModal.addEventListener('show.bs.modal', event => {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            const nama = button.getAttribute('data-nama');
            const email = button.getAttribute('data-email');
            const telepon = button.getAttribute('data-telepon'); // <--- Baru
            const alamat = button.getAttribute('data-alamat');   // <--- Baru
            
            const form = editModal.querySelector('#editForm');
            form.action = `/manajemen-petugas/${id}`;
            
            editModal.querySelector('#edit_name').value = nama;
            editModal.querySelector('#edit_email').value = email;
            editModal.querySelector('#edit_telepon').value = telepon; // <--- Baru
            editModal.querySelector('#edit_alamat').value = alamat;   // <--- Baru
        });
    }

    // Script untuk Modal Hapus Petugas
    const hapusModal = document.getElementById('hapusModal');
    if (hapusModal) {
        hapusModal.addEventListener('show.bs.modal', event => {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            const form = hapusModal.querySelector('#hapusForm');
            form.action = `/manajemen-petugas/${id}`;
        });
    }
</script>
@endpush