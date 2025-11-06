@extends('layouts.main')

@section('title', 'Monitoring Penjemputan')

@section('content')
<div class="container">
    {{-- Tampilkan pesan sukses atau error --}}
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

    <div class="card">
        <div class="card-body">
            
            <ul class="nav nav-pills mb-3" id="adminPenjemputanTab" role="tablist">
                {{-- ... (Nav Tabs tidak berubah) ... --}}
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="baru-tab" data-bs-toggle="tab" data-bs-target="#permintaan-baru" type="button" role="tab" aria-controls="permintaan-baru" aria-selected="true">
                        Permintaan Baru
                        @if ($permintaanBaruList->count() > 0)
                            <span class="badge bg-danger rounded-pill">{{ $permintaanBaruList->count() }}</span>
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
                
                {{-- TAB 1: PERMINTAAN BARU (UNCLAIMED) --}}
                <div class="tab-pane fade show active" id="permintaan-baru" role="tabpanel" aria-labelledby="baru-tab" tabindex="0">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nasabah</th>
                                    <th>Usulan Tgl.</th>
                                    <th>Alamat</th>
                                    <th>Detail Sampah</th>
                                    <th class="text-center">Aksi</th> {{-- <-- KOLOM BARU --}}
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($permintaanBaruList as $tugas)
                                    <tr>
                                        <td>{{ $tugas->nasabah->nama }}</td>
                                        <td>{{ \Carbon\Carbon::parse($tugas->usulan_tanggal)->translatedFormat('d F Y') }}</td>
                                        <td>{{ $tugas->alamat_penjemputan }}</td>
                                        <td>
                                            <strong>{{ $tugas->jenisSampah->nama_sampah ?? 'N/A' }}</strong><br>
                                            <small class="text-muted">Est: {{ $tugas->estimasi_berat ?? '-' }} kg</small>
                                        </td>
                                        <td class="text-center" style="min-width: 120px;">
                                            {{-- TOMBOL TUGASKAN (BARU) --}}
                                            <button type="button" class="btn btn-sm btn-primary btn-aksi w-100"
                                                data-bs-toggle="modal"
                                                data-bs-target="#tugaskanModal"
                                                data-penjemputan-id="{{ $tugas->id }}"
                                                data-nasabah-nama="{{ $tugas->nasabah->nama }}">
                                                <i class="bi bi-person-check-fill"></i> Tugaskan
                                            </button>

                                            {{-- TOMBOL HAPUS (BARU) --}}
                                            <form action="{{ route('admin.penjemputan.destroy', $tugas) }}" method="POST" class="mt-2">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger btn-aksi w-100" onclick="return confirm('Anda yakin ingin menghapus permintaan ini?')">
                                                    <i class="bi bi-trash"></i> Hapus
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">Tidak ada permintaan penjemputan baru.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- TAB 2: TUGAS BERLANGSUNG --}}
                <div class="tab-pane fade" id="tugas-berlangsung" role="tabpanel" aria-labelledby="berlangsung-tab" tabindex="0">
                    {{-- ... (Tampilan Tab 2 tidak berubah) ... --}}
                </div>

                {{-- TAB 3: RIWAYAT TUGAS --}}
                <div class="tab-pane fade" id="riwayat-tugas" role="tabpanel" aria-labelledby="riwayat-tab" tabindex="0">
                    {{-- ... (Tampilan Tab 3 tidak berubah) ... --}}
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

{{-- TAMBAHKAN MODAL BARU UNTUK ADMIN "TUGASKAN" --}}
@section('modal')
<div class="modal fade" id="tugaskanModal" tabindex="-1" aria-labelledby="tugaskanModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tugaskanModalLabel">Tugaskan Petugas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            {{-- Form ini akan mengirim data ke PenjemputanController@adminAssign --}}
            <form id="tugaskanForm" method="POST"> 
                @csrf
                <div class="modal-body">
                    <p>Pilih petugas untuk menangani permintaan dari: <br><strong><span id="namaNasabahTugas"></span></strong></p>
                    
                    <div class="mb-3">
                        <label for="petugas_id" class="form-label">Petugas yang Tersedia <span class="text-danger">*</span></label>
                        <select class="form-select" id="petugas_id" name="petugas_id" required>
                            <option value="" selected disabled>-- Pilih petugas --</option>
                            @foreach ($daftarPetugas as $petugas)
                                <option value="{{ $petugas->id }}">{{ $petugas->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Penugasan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

{{-- TAMBAHKAN JAVASCRIPT UNTUK MODAL "TUGASKAN" --}}
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // --- Script BARU untuk Modal Tugaskan ---
        const tugaskanModal = document.getElementById('tugaskanModal');
        const tugaskanForm = document.getElementById('tugaskanForm');
        
        tugaskanModal.addEventListener('show.bs.modal', event => {
            // Tombol yang memicu modal
            const button = event.relatedTarget;

            // Ekstrak data dari tombol
            const penjemputanId = button.getAttribute('data-penjemputan-id');
            const nasabahNama = button.getAttribute('data-nasabah-nama');

            // Set nama nasabah di modal
            const namaNasabahSpan = tugaskanModal.querySelector('#namaNasabahTugas');
            namaNasabahSpan.textContent = nasabahNama;

            // Set URL action untuk form
            tugaskanForm.action = `/admin/penjemputan/${penjemputanId}/assign`;
        });
    });
</script>
@endpush