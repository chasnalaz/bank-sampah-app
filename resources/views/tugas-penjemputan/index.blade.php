@extends('layouts.main')

@section('title', 'Tugas Penjemputan')

@section('content')
<div class="container">
    {{-- ... (Pesan error, dll. tidak berubah) ... --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ $errors->any() ? $errors->first() : session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Gagal Menyelesaikan Tugas!</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif


    <div class="card">
        <div class="card-body">
            
            <ul class="nav nav-pills mb-3" id="penjemputanTab" role="tablist">
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
                    <button class="nav-link" id="aktif-tab" data-bs-toggle="tab" data-bs-target="#tugas-aktif" type="button" role="tab" aria-controls="tugas-aktif" aria-selected="false">
                        Tugas Aktif Saya
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="riwayat-tab" data-bs-toggle="tab" data-bs-target="#riwayat-selesai" type="button" role="tab" aria-controls="riwayat-selesai" aria-selected="false">
                        Riwayat Tugas Saya
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="penjemputanTabContent">
                
                {{-- KONTEN TAB 1: PERMINTAAN BARU --}}
                <div class="tab-pane fade show active" id="permintaan-baru" role="tabpanel" aria-labelledby="baru-tab" tabindex="0">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            {{-- ... (thead tidak berubah) ... --}}
                            <thead>
                                <tr>
                                    <th>Nasabah</th>
                                    <th>Usulan Tgl.</th>
                                    <th>Alamat</th>
                                    <th>Detail Sampah</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($permintaanBaruList as $tugas)
                                    <tr>
                                        <td>{{ $tugas->nasabah->nama }}</td>
                                        <td>{{ \Carbon\Carbon::parse($tugas->usulan_tanggal)->translatedFormat('d F Y') }}</td>
                                        <td>{{ $tugas->alamat_penjemputan }}</td>
                                        <td>
                                            {{-- <-- PERBAIKAN DI SINI --}}
                                            <strong>{{ $tugas->jenisSampah->nama_sampah ?? 'N/A' }}</strong><br> 
                                            <small class="text-muted">Est: {{ $tugas->estimasi_berat ?? '-' }} kg</small><br>
                                            <small class="text-muted">Cat: {{ $tugas->catatan_nasabah ?? '-' }}</small>
                                        </td>
                                        <td>
                                            {{-- ... (Tombol 'Ambil Tugas' tidak berubah) ... --}}
                                            <form action="{{ route('penjemputan.terima', $tugas) }}" method="POST" style="display: inline-block;">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success btn-aksi">
                                                    <i class="bi bi-check-circle"></i> Ambil Tugas
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-center">Tidak ada permintaan penjemputan baru saat ini.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- KONTEN TAB 2: TUGAS AKTIF SAYA --}}
                <div class="tab-pane fade" id="tugas-aktif" role="tabpanel" aria-labelledby="aktif-tab" tabindex="0">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            {{-- ... (thead tidak berubah) ... --}}
                            <thead>
                                <tr>
                                    <th>Nasabah</th>
                                    <th>Usulan Tgl.</th>
                                    <th>Alamat</th>
                                    <th>Detail Sampah</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($tugasAktifList as $tugas)
                                    <tr>
                                        <td>{{ $tugas->nasabah->nama }}</td>
                                        <td>{{ \Carbon\Carbon::parse($tugas->usulan_tanggal)->translatedFormat('d F Y') }}</td>
                                        <td>{{ $tugas->alamat_penjemputan }}</td>
                                        <td>
                                            {{-- <-- PERBAIKAN DI SINI --}}
                                            <strong>{{ $tugas->jenisSampah->nama_sampah ?? 'N/A' }}</strong><br>
                                            <small class="text-muted">Est: {{ $tugas->estimasi_berat ?? '-' }} kg</small><br>
                                            <small class="text-muted">Cat: {{ $tugas->catatan_nasabah ?? '-' }}</small>
                                        </td>
                                        <td style="min-width: 120px;">
                                            {{-- ... (Tombol 'Selesaikan' dan 'Batalkan' tidak berubah) ... --}}
                                            <button type="button" class="btn btn-sm btn-success btn-aksi w-100 btn-selesaikan"
                                                data-bs-toggle="modal"
                                                data-bs-target="#selesaikanModal"
                                                data-penjemputan-id="{{ $tugas->id }}"
                                                data-estimasi-jenis="{{ $tugas->jenis_sampah_id ?? '' }}"
                                                data-estimasi-berat="{{ $tugas->estimasi_berat ?? '' }}">
                                                <i class="bi bi-check-circle"></i> Selesaikan
                                            </button>
                                            <form action="{{ route('penjemputan.batalkan', $tugas) }}" method="POST" class="mt-2">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-danger btn-aksi w-100" onclick="return confirm('Anda yakin ingin membatalkan tugas ini? Tugas akan dikembalikan ke daftar.')">
                                                    <i class="bi bi-x-circle"></i> Batalkan
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-center">Belum ada tugas penjemputan yang Anda ambil.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- KONTEN TAB 3: RIWAYAT TUGAS SAYA --}}
                <div class="tab-pane fade" id="riwayat-selesai" role="tabpanel" aria-labelledby="riwayat-tab" tabindex="0">
                    {{-- ... (Tidak ada perubahan di Tab 3) ... --}}
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

@section('modal')
<div class="modal fade" id="selesaikanModal" tabindex="-1" aria-labelledby="selesaikanModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="selesaikanModalLabel">Selesaikan & Catat Transaksi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="selesaikanForm" method="POST"> 
                @csrf
                <div class="modal-body">
                    {{-- ... (Input Estimasi Awal tidak berubah) ... --}}
                    <div class="mb-3">
                        <label for="estimasi" class="form-label">Estimasi Awal:</label>
                        <input type="text" id="estimasi" class="form-control" disabled>
                    </div>

                    <div class="mb-3">
                        <label for="jenis_sampah_id" class="form-label">Jenis Sampah (Aktual) <span class="text-danger">*</span></label>
                        <select class="form-select" id="jenis_sampah_id" name="jenis_sampah_id" required>
                            <option value="" selected disabled>-- Pilih jenis sampah --</option>
                            @foreach ($allJenisSampah as $jenis)
                                {{-- <-- PERBAIKAN DI SINI --}}
                                <option value="{{ $jenis->id }}">
                                    {{ $jenis->nama_sampah }} (Rp {{ number_format($jenis->harga_per_kg, 0, ',', '.') }}/kg)
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        {{-- ... (Input Berat Aktual tidak berubah) ... --}}
                        <label for="berat_aktual" class="form-label">Berat Aktual (kg) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="berat_aktual" name="berat_aktual" step="0.01" min="0.01" placeholder="Contoh: 1.5" required>
                    </div>
                </div>
                <div class="modal-footer">
                    {{-- ... (Tombol Modal tidak berubah) ... --}}
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Selesaikan dan Catat Transaksi</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- ... (JavaScript tidak berubah, tapi saya sertakan lagi untuk kelengkapan) ... --}}
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // --- Script untuk auto-open tab dari hash (sudah ada) ---
        const hash = window.location.hash;
        if (hash) {
            const tabButton = document.querySelector(hash);
            if (tabButton) {
                const tab = new bootstrap.Tab(tabButton);
                tab.show();
            }
        }

        // --- Script BARU untuk Modal Selesaikan ---
        const selesaikanModal = document.getElementById('selesaikanModal');
        const selesaikanForm = document.getElementById('selesaikanForm');
        
        selesaikanModal.addEventListener('show.bs.modal', event => {
            const button = event.relatedTarget;
            const penjemputanId = button.getAttribute('data-penjemputan-id');
            const estimasiJenisId = button.getAttribute('data-estimasi-jenis');
            const estimasiBerat = button.getAttribute('data-estimasi-berat');
            
            let estimasiJenisTeks = "Tidak spesifik";
            if (estimasiJenisId) {
                // <-- PERBAIKAN DI SINI
                const estimasiOption = document.querySelector(`#jenis_sampah_id option[value="${estimasiJenisId}"]`);
                if (estimasiOption) {
                    estimasiJenisTeks = estimasiOption.text.split('(')[0].trim();
                }
            }

            const estimasiInput = selesaikanModal.querySelector('#estimasi');
            estimasiInput.value = `${estimasiJenisTeks} / Estimasi: ${estimasiBerat} kg`;

            const jenisSampahSelect = selesaikanModal.querySelector('#jenis_sampah_id');
            if (estimasiJenisId) {
                jenisSampahSelect.value = estimasiJenisId;
            } else {
                jenisSampahSelect.value = "";
            }

            const beratAktualInput = selesaikanModal.querySelector('#berat_aktual');
            beratAktualInput.value = estimasiBerat.replace(' kg', '').trim();

            selesaikanForm.action = `/penjemputan/${penjemputanId}/selesaikan`;
        });
    });
</script>
@endpush