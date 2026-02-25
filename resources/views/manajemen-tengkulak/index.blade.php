@extends('layouts.main')

@section('title', 'Manajemen Data Tengkulak')

@section('content')
<div class="container">
    <div class="d-flex justify-content-end align-items-center mb-3">
        @can('isAdmin')
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahModal">
                <i class="bi bi-plus-circle me-2"></i> Tambah Data Tengkulak
            </button>
        @endcan
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Nama Tengkulak</th>
                            <th>Jenis Sampah</th>
                            <th>Harga Beli</th>
                            <th>Kontak</th>
                            @can('isAdmin')
                                <th class="text-center">Aksi</th>
                            @endcan
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($tengkulakList as $tengkulak)
                            <tr>
                                <td>{{ $tengkulak->nama_tengkulak }}</td>
                                <td>{{ $tengkulak->jenisSampah->nama_sampah }}</td>
                                <td>Rp {{ number_format($tengkulak->harga_beli, 0, ',', '.') }}</td>
                                <td>{{ $tengkulak->kontak ?? '-' }}</td>
                                @can('isAdmin')
                                    <td>
                                        <div class="d-flex justify-content-center gap-1">
                                            <button type="button" class="btn btn-sm btn-warning text-white btn-aksi" 
                                                    data-bs-toggle="modal" data-bs-target="#editModal"
                                                    data-id="{{ $tengkulak->id }}"
                                                    data-nama="{{ $tengkulak->nama_tengkulak }}"
                                                    data-sampah="{{ $tengkulak->jenis_sampah_id }}"
                                                    data-harga="{{ $tengkulak->harga_beli }}"
                                                    data-kontak="{{ $tengkulak->kontak }}">
                                                <i class="bi bi-pencil-square me-1"></i>Edit
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger btn-aksi" 
                                                    data-bs-toggle="modal" data-bs-target="#hapusModal"
                                                    data-id="{{ $tengkulak->id }}">
                                                <i class="bi bi-trash3 me-1"></i>Hapus
                                            </button>
                                        </div>
                                    </td>
                                @endcan
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">Belum ada data tengkulak.</td>
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
                <h5 class="modal-title">Tambah Data Tengkulak</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('manajemen-tengkulak.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Tengkulak</label>
                        <input type="text" class="form-control" name="nama_tengkulak" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jenis Sampah</label>
                        <select name="jenis_sampah_id" class="form-select" required>
                            <option value="">-- Pilih Jenis Sampah --</option>
                            @foreach($jenisSampahList as $js)
                                <option value="{{ $js->id }}">{{ $js->nama_sampah }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Harga Beli (per Kg)</label>
                        <input type="number" class="form-control" name="harga_beli" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kontak (WA/Telp)</label>
                        <input type="text" class="form-control" name="kontak">
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
                <h5 class="modal-title">Edit Data Tengkulak</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="editForm">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Tengkulak</label>
                        <input type="text" class="form-control" id="edit_nama" name="nama_tengkulak" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jenis Sampah</label>
                        <select name="jenis_sampah_id" id="edit_sampah" class="form-select" required>
                            @foreach($jenisSampahList as $js)
                                <option value="{{ $js->id }}">{{ $js->nama_sampah }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Harga Beli (per Kg)</label>
                        <input type="number" class="form-control" id="edit_harga" name="harga_beli" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kontak</label>
                        <input type="text" class="form-control" id="edit_kontak" name="kontak">
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
                <p>Apakah Anda yakin ingin menghapus data tengkulak ini?</p>
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
            const sampah = button.getAttribute('data-sampah');
            const harga = button.getAttribute('data-harga');
            const kontak = button.getAttribute('data-kontak');
            
            const form = editModal.querySelector('#editForm');
            form.action = `/manajemen-tengkulak/${id}`;
            
            editModal.querySelector('#edit_nama').value = nama;
            editModal.querySelector('#edit_sampah').value = sampah;
            editModal.querySelector('#edit_harga').value = harga;
            editModal.querySelector('#edit_kontak').value = kontak;
        });
    }

    // Script untuk Modal Hapus
    const hapusModal = document.getElementById('hapusModal');
    if (hapusModal) {
        hapusModal.addEventListener('show.bs.modal', event => {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            const form = hapusModal.querySelector('#hapusForm');
            form.action = `/manajemen-tengkulak/${id}`;
        });
    }
</script>
@endpush