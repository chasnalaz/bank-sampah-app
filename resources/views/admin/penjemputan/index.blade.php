@extends('layouts.main')

@section('title', 'Monitoring Penjemputan')

@section('content')
<div class="container">
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            
            {{-- Nav Tabs --}}
            <ul class="nav nav-pills mb-3" id="adminPenjemputanTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="baru-tab" data-bs-toggle="tab" data-bs-target="#permintaan-baru" type="button" role="tab" aria-controls="permintaan-baru" aria-selected="true">
                        Permintaan Baru
                        @if ($permintaanBaruList->count() > 0)
                            <span class="badge bg-danger rounded-pill ms-1">{{ $permintaanBaruList->count() }}</span>
                        @endif
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="berlangsung-tab" data-bs-toggle="tab" data-bs-target="#tugas-berlangsung" type="button" role="tab" aria-controls="tugas-berlangsung" aria-selected="false">
                        Tugas Berlangsung
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="riwayat-tab" data-bs-toggle="tab" data-bs-target="#riwayat-tugas" type="button" role="tab" aria-controls="riwayat-tugas" aria-selected="false">
                        Riwayat Tugas
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="adminPenjemputanTabContent">
                
                {{-- TAB 1: PERMINTAAN BARU --}}
                <div class="tab-pane fade show active" id="permintaan-baru" role="tabpanel" aria-labelledby="baru-tab" tabindex="0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Nasabah</th>
                                    <th>Usulan Tgl.</th>
                                    <th>Alamat</th>
                                    <th>Detail Sampah</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($permintaanBaruList as $tugas)
                                    <tr>
                                        <td>
                                            <div class="fw-bold">{{ $tugas->nasabah->nama }}</div>
                                            <small class="text-muted">{{ $tugas->nasabah->telepon }}</small>
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($tugas->usulan_tanggal)->translatedFormat('d F Y') }}</td>
                                        <td>{{ $tugas->alamat_penjemputan }}</td>
                                        <td>
                                            <span class="badge bg-info text-dark">{{ $tugas->jenisSampah->nama_sampah ?? 'Campuran' }}</span>
                                            <br><small class="text-muted">Est: {{ $tugas->estimasi_berat ?? '-' }} kg</small>
                                        </td>
                                        
                                        {{-- LOGIKA PEMBEDA ADMIN VS KETUA --}}
                                        <td class="text-center" style="min-width: 140px;">
                                            @can('isAdmin')
                                                {{-- AREA ADMIN: TOMBOL LENGKAP --}}
                                                <button type="button" class="btn btn-sm btn-primary btn-aksi w-100 mb-2"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#tugaskanModal"
                                                    data-penjemputan-id="{{ $tugas->id }}"
                                                    data-nasabah-nama="{{ $tugas->nasabah->nama }}">
                                                    <i class="bi bi-person-check-fill me-1"></i> Tugaskan
                                                </button>

                                                <form action="{{ route('admin.penjemputan.destroy', $tugas) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger btn-aksi w-100" onclick="return confirm('Anda yakin ingin menghapus permintaan ini?')">
                                                        <i class="bi bi-trash me-1"></i> Hapus
                                                    </button>
                                                </form>
                                            @else
                                                {{-- AREA KETUA: HANYA INFO --}}
                                                <div class="d-grid gap-1">
                                                    <span class="badge bg-warning text-dark border">
                                                        <i class="bi bi-hourglass-split"></i> Menunggu
                                                    </span>
                                                    <small class="text-muted fst-italic" style="font-size: 0.7rem;">
                                                        Menunggu Admin menugaskan
                                                    </small>
                                                </div>
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">
                                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                            Tidak ada permintaan penjemputan baru.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- TAB 2: TUGAS BERLANGSUNG (Isi sesuai kebutuhan nanti) --}}
                <div class="tab-pane fade" id="tugas-berlangsung" role="tabpanel" aria-labelledby="berlangsung-tab" tabindex="0">
                     <div class="alert alert-info text-center">Belum ada tugas yang sedang berlangsung.</div>
                </div>

                {{-- TAB 3: RIWAYAT TUGAS (Isi sesuai kebutuhan nanti) --}}
                <div class="tab-pane fade" id="riwayat-tugas" role="tabpanel" aria-labelledby="riwayat-tab" tabindex="0">
                     <div class="alert alert-info text-center">Belum ada riwayat tugas selesai.</div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

{{-- MODAL HANYA MUNCUL JIKA ADMIN --}}
@can('isAdmin')
@section('modal')
<div class="modal fade" id="tugaskanModal" tabindex="-1" aria-labelledby="tugaskanModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="tugaskanModalLabel">Pilih Petugas Penjemput</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="tugaskanForm" method="POST"> 
                @csrf
                <div class="modal-body">
                    <div class="alert alert-light border mb-3">
                        <small class="text-muted d-block">Nasabah:</small>
                        <strong><span id="namaNasabahTugas" class="fs-5"></span></strong>
                    </div>
                    
                    <div class="mb-3">
                        <label for="petugas_id" class="form-label fw-bold">Petugas Siap (Status: Ready) <span class="text-danger">*</span></label>
                        <select class="form-select" id="petugas_id" name="petugas_id" required>
                            <option value="" selected disabled>-- Pilih petugas --</option>
                            @forelse ($daftarPetugas as $petugas)
                                <option value="{{ $petugas->id }}">{{ $petugas->name }}</option>
                            @empty
                                <option disabled>Tidak ada petugas yang statusnya SIAP</option>
                            @endforelse
                        </select>
                        <div class="form-text">Hanya menampilkan petugas yang sudah absen "SIAP" hari ini.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-send-fill me-1"></i> Kirim Tugas</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@endcan

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const tugaskanModal = document.getElementById('tugaskanModal');
        // Cek dulu apakah modalnya ada (karena di ketua modal ini ga dirender)
        if (tugaskanModal) {
            const tugaskanForm = document.getElementById('tugaskanForm');
            
            tugaskanModal.addEventListener('show.bs.modal', event => {
                const button = event.relatedTarget;
                const penjemputanId = button.getAttribute('data-penjemputan-id');
                const nasabahNama = button.getAttribute('data-nasabah-nama');

                const namaNasabahSpan = tugaskanModal.querySelector('#namaNasabahTugas');
                namaNasabahSpan.textContent = nasabahNama;

                tugaskanForm.action = `/admin/penjemputan/${penjemputanId}/assign`;
            });
        }
    });
</script>
@endpush