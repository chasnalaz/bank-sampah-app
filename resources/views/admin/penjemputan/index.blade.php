@extends('layouts.main')

@section('title', 'Monitoring Penjemputan')

@section('content')
<div class="container-fluid">
    
    {{-- ALERT PESAN --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-header py-3 bg-white">
            <ul class="nav nav-tabs card-header-tabs" id="adminTab" role="tablist">
                
                {{-- TAB 1: PERMINTAAN MASUK --}}
                <li class="nav-item">
                    <button class="nav-link active fw-bold text-dark" id="masuk-tab" data-bs-toggle="tab" data-bs-target="#permintaan-masuk" type="button">
                        <i class="bi bi-inbox me-2"></i>Permintaan Masuk
                        @if($permintaanBaru->count() > 0)
                            <span class="badge bg-danger rounded-pill ms-1">{{ $permintaanBaru->count() }}</span>
                        @endif
                    </button>
                </li>

                {{-- TAB 2: MONITORING (TUGAS BERLANGSUNG) --}}
                <li class="nav-item">
                    <button class="nav-link fw-bold text-dark" id="monitoring-tab" data-bs-toggle="tab" data-bs-target="#monitoring-aktif" type="button">
                        <i class="bi bi-activity me-2"></i>Monitoring Petugas
                        @if($tugasBerlangsung->count() > 0)
                            <span class="badge bg-warning text-dark rounded-pill ms-1">{{ $tugasBerlangsung->count() }}</span>
                        @endif
                    </button>
                </li>

                {{-- TAB 3: RIWAYAT SELESAI --}}
                <li class="nav-item">
                    <button class="nav-link fw-bold text-dark" id="riwayat-tab" data-bs-toggle="tab" data-bs-target="#riwayat-admin" type="button">
                        <i class="bi bi-clock-history me-2"></i>Riwayat Selesai
                    </button>
                </li>
            </ul>
        </div>

        <div class="card-body">
            <div class="tab-content" id="adminTabContent">

                {{-- KONTEN TAB 1: PERMINTAAN MASUK --}}
                <div class="tab-pane fade show active" id="permintaan-masuk" tabindex="0">
                    <h6 class="mb-3 text-muted small text-uppercase">Daftar Request Nasabah (Belum Diambil Petugas)</h6>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Tgl. Usulan</th>
                                    <th>Nasabah</th>
                                    <th>Lokasi</th>
                                    <th>Detail Sampah</th>
                                    
                                    {{-- REVISI: Kolom Aksi HANYA UNTUK ADMIN --}}
                                    @if(Auth::user()->role == 'admin')
                                        <th>Aksi</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($permintaanBaru as $item)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($item->usulan_tanggal)->translatedFormat('d M Y') }}</td>
                                    <td class="fw-bold">{{ $item->nasabah->nama }}</td>
                                    <td>{{ Str::limit($item->alamat_penjemputan, 30) }}</td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $item->jenisSampah->nama_sampah ?? 'Umum' }}</span>
                                        <div class="small text-muted mt-1">Est: {{ $item->estimasi_berat ?? '-' }}</div>
                                    </td>
                                    
                                    {{-- REVISI: Tombol Aksi HANYA UNTUK ADMIN --}}
                                    @if(Auth::user()->role == 'admin')
                                        <td>
                                            <div class="d-flex gap-2">
                                                {{-- TOMBOL TUGASKAN (MODAL) --}}
                                                <button type="button" class="btn btn-sm btn-primary btn-tugaskan"
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#assignModal"
                                                        data-url="{{ route('admin.penjemputan.assign', $item->id) }}"
                                                        data-nasabah="{{ $item->nasabah->nama }}">
                                                    <i class="bi bi-send-fill"></i> Tugaskan
                                                </button>

                                                {{-- TOMBOL HAPUS --}}
                                                <form action="{{ route('admin.penjemputan.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus permintaan ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        <i class="bi bi-trash"></i> Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    @endif
                                </tr>
                                @empty
                                <tr>
                                    {{-- Sesuaikan colspan jika admin/ketua --}}
                                    <td colspan="{{ Auth::user()->role == 'admin' ? 5 : 4 }}" class="text-center py-4 text-muted">
                                        Tidak ada permintaan baru.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- KONTEN TAB 2: MONITORING (TUGAS BERLANGSUNG) --}}
                <div class="tab-pane fade" id="monitoring-aktif" tabindex="0">
                    <h6 class="mb-3 text-muted small text-uppercase">Tugas Sedang Dijalankan Petugas</h6>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Petugas</th>
                                    <th>Nasabah</th>
                                    <th>Status</th>
                                    <th>Waktu Update</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tugasBerlangsung as $tugas)
                                <tr>
                                    <td class="py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary text-white rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                                {{ substr($tugas->petugas->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark">{{ $tugas->petugas->name }}</div>
                                                <small class="text-muted"><i class="bi bi-geo-alt-fill me-1 text-danger"></i>Sedang OTW</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $tugas->nasabah->nama }}</div>
                                        <small class="text-muted">{{ Str::limit($tugas->alamat_penjemputan, 40) }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-warning text-dark border border-warning">
                                            <i class="bi bi-truck me-1"></i> {{ $tugas->status }}
                                        </span>
                                    </td>
                                    <td class="text-muted small">
                                        <i class="bi bi-clock me-1"></i> {{ $tugas->updated_at->diffForHumans() }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5 text-muted">
                                        <i class="bi bi-people fs-1 d-block mb-2 text-secondary"></i>
                                        Tidak ada petugas yang sedang menjemput saat ini.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- KONTEN TAB 3: RIWAYAT SELESAI --}}
                <div class="tab-pane fade" id="riwayat-admin" tabindex="0">
                    <h6 class="mb-3 text-muted small text-uppercase">Log Penjemputan Selesai (Semua Petugas)</h6>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Tanggal Selesai</th>
                                    <th>Nasabah</th>
                                    <th>Dilayani Oleh</th>
                                    <th>Status</th>
                                    
                                    {{-- REVISI: Kolom Aksi HANYA UNTUK ADMIN --}}
                                    @if(Auth::user()->role == 'admin')
                                        <th>Aksi</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($riwayatTugas as $history)
                                <tr>
                                    <td>{{ $history->updated_at->translatedFormat('d M Y') }}</td>
                                    <td>{{ $history->nasabah->nama }}</td>
                                    <td>{{ $history->petugas->name ?? '-' }}</td>
                                    <td><span class="badge bg-success">Selesai</span></td>
                                    
                                    {{-- REVISI: Tombol Hapus HANYA UNTUK ADMIN --}}
                                    @if(Auth::user()->role == 'admin')
                                        <td>
                                            <form action="{{ route('admin.penjemputan.destroy', $history->id) }}" method="POST" onsubmit="return confirm('Hapus data riwayat ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    @endif
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="{{ Auth::user()->role == 'admin' ? 5 : 4 }}" class="text-center py-4 text-muted">
                                        Belum ada riwayat penjemputan selesai.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

@section('modal')
{{-- MODAL HANYA MUNCUL UNTUK ADMIN --}}
@if(Auth::user()->role == 'admin')
    <div class="modal fade" id="assignModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Pilih Petugas Penjemputan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="assignForm" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-info py-2 small">
                            <i class="bi bi-info-circle me-1"></i> Menugaskan untuk nasabah: <strong id="namaNasabahModal"></strong>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Pilih Petugas yang Siap</label>
                            <select name="petugas_id" class="form-select" required>
                                <option value="" disabled selected>-- Pilih Petugas --</option>
                                @foreach($daftarPetugas as $p)
                                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan & Tugaskan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Logic Modal hanya jalan kalau elemen modalnya ada (Admin only)
        var assignModal = document.getElementById('assignModal');
        if (assignModal) {
            assignModal.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget;
                var url = button.getAttribute('data-url');
                var namaNasabah = button.getAttribute('data-nasabah');

                var form = assignModal.querySelector('#assignForm');
                form.action = url;
                
                var modalTitle = assignModal.querySelector('#namaNasabahModal');
                modalTitle.textContent = namaNasabah;
            });
        }
    });
</script>
@endpush