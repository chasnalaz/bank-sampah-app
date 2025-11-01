@extends('layouts.main')

@section('title', 'Manajemen Jenis Sampah')

@section('content')
<div class="container">
    <div class="d-flex justify-content-end align-items-center mb-3">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahModal">
            <i class="bi bi-plus-circle me-2"></i> Tambah Jenis Sampah
        </button>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Nama Sampah</th>
                        <th>Harga per Kg</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($sampahList as $sampah)
                        <tr>
                            <td>{{ $sampah->nama_sampah }}</td>
                            <td>Rp {{ number_format($sampah->harga_per_kg, 0, ',', '.') }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                <button type="button" class="btn btn-sm btn-warning btn-aksi" data-bs-toggle="modal" data-bs-target="#editModal"
                                        data-id="{{ $sampah->id }}"
                                        data-nama="{{ $sampah->nama_sampah }}"
                                        data-harga="{{ $sampah->harga_per_kg }}">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </button>
                                <button type="button" class="btn btn-sm btn-danger btn-aksi" data-bs-toggle="modal" data-bs-target="#hapusModal"
                                        data-id="{{ $sampah->id }}">
                                    <i class="bi bi-trash3"></i> Hapus
                                </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center">Belum ada data jenis sampah.</td>
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
                <h5 class="modal-title">Tambah Jenis Sampah</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('sampah.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nama_sampah" class="form-label">Nama Sampah</label>
                        <input type="text" class="form-control" name="nama_sampah" required>
                    </div>
                    <div class="mb-3">
                        <label for="harga_per_kg" class="form-label">Harga per Kg</label>
                        <input type="number" class="form-control" name="harga_per_kg" required>
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
                <h5 class="modal-title">Edit Jenis Sampah</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="editForm">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_nama_sampah" class="form-label">Nama Sampah</label>
                        <input type="text" class="form-control" id="edit_nama_sampah" name="nama_sampah" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_harga_per_kg" class="form-label">Harga per Kg</label>
                        <input type="number" class="form-control" id="edit_harga_per_kg" name="harga_per_kg" required>
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
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus data ini?</p>
            </div>
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
    // Script untuk Modal Edit
    const editModal = document.getElementById('editModal');
    if (editModal) {
        editModal.addEventListener('show.bs.modal', event => {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            const nama = button.getAttribute('data-nama');
            const harga = button.getAttribute('data-harga');
            
            const form = editModal.querySelector('#editForm');
            form.action = `/manajemen-sampah/${id}`;
            
            editModal.querySelector('#edit_nama_sampah').value = nama;
            editModal.querySelector('#edit_harga_per_kg').value = harga;
        });
    }

    // Script untuk Modal Hapus
    const hapusModal = document.getElementById('hapusModal');
    if (hapusModal) {
        hapusModal.addEventListener('show.bs.modal', event => {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            const form = hapusModal.querySelector('#hapusForm');
            form.action = `/manajemen-sampah/${id}`;
        });
    }
</script>
@endpush