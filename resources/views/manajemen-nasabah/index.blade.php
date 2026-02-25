@extends('layouts.main')

@section('title', 'Manajemen Data Nasabah')

@section('content')
<div class="container">
    {{-- BAGIAN ATAS: JUDUL, PENCARIAN & TOMBOL TAMBAH --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">
        
        {{-- Form Pencarian --}}
        <form action="{{ route('nasabah.manajemen') }}" method="GET" class="d-flex gap-2 w-100" style="max-width: 500px;">
            <div class="input-group shadow-sm">
                <input type="text" name="search" class="form-control border-0" placeholder="Cari nama, alamat, atau telepon..." value="{{ request('search') }}">
                
                {{-- TOMBOL CARI: WARNA ORANYE MANUAL --}}
                <button class="btn text-white fw-bold" type="submit" style="background-color: #fd7e14; border-color: #fd7e14;">
                    <i class="bi bi-search"></i> Cari
                </button>
            </div>
            
            {{-- Tombol Reset (Muncul kalau lagi cari) --}}
            @if(request('search'))
                <a href="{{ route('nasabah.manajemen') }}" class="btn btn-secondary shadow-sm" title="Reset Pencarian">
                    <i class="bi bi-x-lg"></i>
                </a>
            @endif
        </form>

        {{-- Tombol Tambah --}}
        <button type="button" class="btn btn-primary shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#tambahModal">
            <i class="bi bi-plus-circle me-2"></i> Tambah Nasabah
        </button>
    </div>

    <div class="card shadow-sm border-0" style="border-radius: 10px;">
        <div class="card-body p-0">
            <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-secondary">
                    <tr>
                        <th class="ps-4 py-3">Nama Lengkap</th>
                        <th>Alamat</th>
                        <th>Telepon</th>
                        <th>Saldo</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($nasabahList as $nasabah)
                        <tr>
                            <td class="ps-4 fw-bold text-dark">{{ $nasabah->nama }}</td>
                            <td class="small text-muted" style="max-width: 250px;">{{ Str::limit($nasabah->alamat, 40) }}</td>
                            <td>{{ $nasabah->telepon ?? '-' }}</td>
                            <td class="text-success fw-bold">Rp {{ number_format($nasabah->saldo, 0, ',', '.') }}</td>
                            <td class="text-center">
                                {{-- TOMBOL AKSI: RAPI & TERPISAH (ADA TULISANNYA) --}}
                                <div class="d-flex justify-content-center gap-1">
                                    {{-- Detail (Biru Muda) --}}
                                    <a href="{{ route('nasabah.show', $nasabah->id) }}" class="btn btn-sm btn-info text-white" title="Lihat Detail">
                                        <i class="bi bi-eye-fill me-1"></i>Detail
                                    </a>
                                    
                                    @can('isAdmin')
                                    {{-- Edit (Kuning/Oranye) --}}
                                    <button type="button" class="btn btn-sm btn-warning text-white btn-aksi" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editModal"
                                            data-id="{{ $nasabah->id }}"
                                            data-nama="{{ $nasabah->nama }}"
                                            data-alamat="{{ $nasabah->alamat }}"
                                            data-telepon="{{ $nasabah->telepon }}">
                                        <i class="bi bi-pencil-square me-1"></i>Edit
                                    </button>

                                    {{-- Hapus (Merah) --}}
                                    <button type="button" class="btn btn-sm btn-danger btn-aksi" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#hapusModal"
                                            data-id="{{ $nasabah->id }}">
                                        <i class="bi bi-trash3 me-1"></i>Hapus
                                    </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-emoji-frown fs-1 d-block mb-2"></i>
                                Data nasabah tidak ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            </div>
            
            {{-- PAGINATION --}}
            <div class="d-flex justify-content-end p-3">
                {{-- Laravel otomatis akan dirender, CSS di atas akan otomatis mengubah tampilannya --}}
                {{ $nasabahList->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

{{-- BAGIAN MODAL --}}
@section('modal')
{{-- (Bagian Modal Tambah, Edit, Hapus DI SINI SAMA PERSIS SEPERTI KODEMU SEBELUMNYA) --}}
{{-- Kamu bisa copy-paste ulang modal dari kodemu yang lama ke sini --}}

<div class="modal fade" id="tambahModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Nasabah Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('nasabah.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control" name="nama" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <textarea class="form-control" name="alamat" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nomor Telepon</label>
                        <input type="text" class="form-control" name="telepon">
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

<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Data Nasabah</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="" method="POST" id="editForm">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control" id="edit_nama" name="nama" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <textarea class="form-control" id="edit_alamat" name="alamat" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nomor Telepon</label>
                        <input type="text" class="form-control" id="edit_telepon" name="telepon">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="hapusModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Yakin ingin menghapus data ini? Semua riwayat transaksi nasabah juga akan terhapus.
            </div>
            <div class="modal-footer">
                <form action="" method="POST" id="hapusForm">
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
    // Script Modal Edit
    const editModal = document.getElementById('editModal');
    if (editModal) {
        editModal.addEventListener('show.bs.modal', event => {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            const nama = button.getAttribute('data-nama');
            const alamat = button.getAttribute('data-alamat');
            const telepon = button.getAttribute('data-telepon');

            const form = editModal.querySelector('#editForm');
            editModal.querySelector('#edit_nama').value = nama;
            editModal.querySelector('#edit_alamat').value = alamat;
            editModal.querySelector('#edit_telepon').value = telepon;
            
            form.action = `/manajemen-nasabah/${id}`;
        });
    }

    // Script Modal Hapus
    const hapusModal = document.getElementById('hapusModal');
    if (hapusModal) {
        hapusModal.addEventListener('show.bs.modal', event => {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            const form = hapusModal.querySelector('#hapusForm');
            form.action = `/manajemen-nasabah/${id}`;
        });
    }
</script>
@endpush