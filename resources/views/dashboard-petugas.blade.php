@extends('layouts.main')

@section('title', 'Dashboard')

@section('content')
    {{-- Salam Pembuka --}}
    <div class="mb-4">
        <h3>Halo, {{ Auth::user()->name }}!</h3>
    </div>

    {{-- Notifikasi Sukses --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Daftar Harga Sampah (Bentuk Kartu) --}}
    <div>
        <h5 class="mb-3">Daftar Harga Sampah Hari Ini</h5>
        <div class="row g-3">
            @forelse ($daftarHargaSampah as $sampah)
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="card shadow-sm border-0 text-center">
                        <div class="card-body">
                            <h6 class="card-title text-muted small">{{ $sampah->nama_sampah }}</h6>
                            <p class="card-text fw-bold text-primary mb-0">
                                Rp {{ number_format($sampah->harga_per_kg, 0, ',', '.') }}/kg
                            </p>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        Belum ada data jenis sampah.
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    {{-- KARTU BARU: Permintaan Penjemputan --}}
    <div class="card shadow-sm border-0 mt-4">
        <div class="card-header bg-white border-0">
            <h5 class="mb-0">Permintaan Penjemputan Baru</h5>
        </div>
        <div class="list-group list-group-flush">
            @forelse ($permintaanPenjemputan as $permintaan)
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <strong>{{ $permintaan->nasabah->nama }}</strong><br>
                        <small class="text-muted">{{ $permintaan->alamat_penjemputan }}</small>
                    </div>
                    {{-- TOMBOL BARU UNTUK MEMBUKA MODAL --}}
                    <button type="button" class="btn btn-sm btn-outline-primary"
                            data-bs-toggle="modal"
                            data-bs-target="#detailPenjemputanModal"
                            data-id="{{ $permintaan->id }}"
                            data-nama="{{ $permintaan->nasabah->nama }}"
                            data-alamat="{{ $permintaan->alamat_penjemputan }}"
                            data-tanggal="{{ \Carbon\Carbon::parse($permintaan->usulan_tanggal)->translatedFormat('d F Y') }}"
                            data-jenis-sampah="{{ $permintaan->jenisSampah->nama_sampah ?? 'Tidak spesifik' }}"
                            data-estimasi-berat="{{ $permintaan->estimasi_berat ?? '-' }}"
                            data-catatan="{{ $permintaan->catatan_nasabah ?? '-' }}">
                        Lihat Detail
                    </button>
                </div>
            @empty
                <div class="list-group-item text-center text-muted">
                    Tidak ada permintaan penjemputan saat ini.
                </div>
            @endforelse
        </div>
    </div>
@endsection

@section('modal')
<div class="modal fade" id="detailPenjemputanModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailModalLabel">Detail Permintaan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">Nama Nasabah</label>
                    <p id="detailNama"></p>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Alamat Penjemputan</label>
                    <p id="detailAlamat"></p>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Usulan Tanggal</label>
                    <p id="detailTanggal"></p>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Jenis Sampah Utama</label>
                    <p id="detailJenisSampah"></p>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Estimasi Berat</label>
                    <p id="detailEstimasiBerat"></p>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Catatan Nasabah</label>
                    <p id="detailCatatan"></p>
                </div>
            </div>
            <div class="modal-footer">
                {{-- Tombol Tolak --}}
                <form method="POST" id="tolakForm" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger">Tolak Permintaan</button>
                </form>
                {{-- Tombol Terima --}}
                <form method="POST" id="terimaForm" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-primary">Terima Permintaan</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const detailModal = document.getElementById('detailPenjemputanModal');
    if (detailModal) {
        detailModal.addEventListener('show.bs.modal', event => {
            const button = event.relatedTarget;
            
            // Ekstrak data dari tombol
            const id = button.getAttribute('data-id');
            const nama = button.getAttribute('data-nama');
            const alamat = button.getAttribute('data-alamat');
            const tanggal = button.getAttribute('data-tanggal');
            const jenisSampah = button.getAttribute('data-jenis-sampah');
            const estimasiBerat = button.getAttribute('data-estimasi-berat');
            const catatan = button.getAttribute('data-catatan');

            // Set action untuk form
            const tolakForm = detailModal.querySelector('#tolakForm');
            tolakForm.action = `/penjemputan/${id}/tolak`;
            
            const terimaForm = detailModal.querySelector('#terimaForm');
            terimaForm.action = `/penjemputan/${id}/terima`;

            // Isi konten modal
            detailModal.querySelector('#detailNama').textContent = nama;
            detailModal.querySelector('#detailAlamat').textContent = alamat;
            detailModal.querySelector('#detailTanggal').textContent = tanggal;
            detailModal.querySelector('#detailJenisSampah').textContent = jenisSampah;
            detailModal.querySelector('#detailEstimasiBerat').textContent = estimasiBerat;
            detailModal.querySelector('#detailCatatan').textContent = catatan;
        });
    }
</script>
@endpush