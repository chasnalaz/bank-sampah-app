@extends('layouts.main')

@section('title', 'Tugas Penjemputan')

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
            
            <ul class="nav nav-pills mb-3" id="penjemputanTab" role="tablist">
                {{-- TAB 1: PERMINTAAN BARU (UNCLAIMED) --}}
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="baru-tab" data-bs-toggle="tab" data-bs-target="#permintaan-baru" type="button" role="tab" aria-controls="permintaan-baru" aria-selected="true">
                        Permintaan Baru
                        {{-- Tampilkan badge jika ada permintaan baru --}}
                        @if ($permintaanBaruList->count() > 0)
                            <span class="badge bg-danger rounded-pill">{{ $permintaanBaruList->count() }}</span>
                        @endif
                    </button>
                </li>
                {{-- TAB 2: TUGAS AKTIF SAYA --}}
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="aktif-tab" data-bs-toggle="tab" data-bs-target="#tugas-aktif" type="button" role="tab" aria-controls="tugas-aktif" aria-selected="false">
                        Tugas Aktif Saya
                    </button>
                </li>
                {{-- TAB 3: RIWAYAT TUGAS SAYA --}}
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
                                            {{-- Sesuaikan 'nama_sampah' atau 'nama_jenis' --}}
                                            <strong>{{ $tugas->jenisSampah->nama_jenis ?? 'N/A' }}</strong><br> 
                                            <small class="text-muted">Est: {{ $tugas->estimasi_berat ?? '-' }} kg</small><br>
                                            <small class="text-muted">Cat: {{ $tugas->catatan_nasabah ?? '-' }}</small>
                                        </td>
                                        <td>
                                            {{-- TOMBOL TERIMA / CLAIM --}}
                                            <form action="{{ route('penjemputan.terima', $tugas) }}" method="POST" style="display: inline-block;">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success btn-aksi">
                                                    <i class="bi bi-check-circle"></i> Ambil Tugas
                                                </button>
                                            </form>
                                            
                                            {{-- TOMBOL TOLAK --}}
                                            <form action="{{ route('penjemputan.tolak', $tugas) }}" method="POST" style="display: inline-block; margin-top: 5px;">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-danger btn-aksi">
                                                    <i class="bi bi-x-circle"></i> Tolak
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">Tidak ada permintaan penjemputan baru saat ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- KONTEN TAB 2: TUGAS AKTIF SAYA --}}
                <div class="tab-pane fade" id="tugas-aktif" role="tabpanel" aria-labelledby="aktif-tab" tabindex="0">
                    <div class="table-responsive">
                        <table class="table table-hover">
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
                                            <strong>{{ $tugas->jenisSampah->nama_jenis ?? 'N/A' }}</strong><br>
                                            <small class="text-muted">Est: {{ $tugas->estimasi_berat ?? '-' }} kg</small><br>
                                            <small class="text-muted">Cat: {{ $tugas->catatan_nasabah ?? '-' }}</small>
                                        </td>
                                        <td>
                                            <form action="{{ route('penjemputan.selesaikan', $tugas) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success btn-aksi">
                                                    <i class="bi bi-check-circle"></i> Selesaikan
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">Belum ada tugas penjemputan yang Anda ambil.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- KONTEN TAB 3: RIWAYAT TUGAS SAYA --}}
                <div class="tab-pane fade" id="riwayat-selesai" role="tabpanel" aria-labelledby="riwayat-tab" tabindex="0">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nasabah</th>
                                    <th>Tanggal</th>
                                    <th>Alamat</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($riwayatSelesaiList as $tugas)
                                    <tr>
                                        <td>{{ $tugas->nasabah->nama }}</td>
                                        <td>{{ \Carbon\Carbon::parse($tugas->usulan_tanggal)->translatedFormat('d F Y') }}</td>
                                        <td>{{ $tugas->alamat_penjemputan }}</td>
                                        <td>
                                            @if ($tugas->status == 'Selesai')
                                                <span class="badge bg-success-subtle text-success-emphasis">Selesai</span>
                                            @else
                                                <span class="badge bg-danger-subtle text-danger-emphasis">Ditolak</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">Belum ada riwayat penjemputan.</td>
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