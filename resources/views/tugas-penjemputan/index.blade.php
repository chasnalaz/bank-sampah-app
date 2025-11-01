@extends('layouts.main')

@section('title', 'Tugas Penjemputan')

@section('content')
<div class="container">
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class.card">
        <div class="card-body">
            
            <ul class="nav nav-pills mb-3" id="penjemputanTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="aktif-tab" data-bs-toggle="tab" data-bs-target="#tugas-aktif" type="button" role="tab" aria-controls="tugas-aktif" aria-selected="true">
                        Tugas Aktif
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="riwayat-tab" data-bs-toggle="tab" data-bs-target="#riwayat-selesai" type="button" role="tab" aria-controls="riwayat-selesai" aria-selected="false">
                        Riwayat Selesai
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="penjemputanTabContent">
                
                {{-- TAB 1: TUGAS AKTIF --}}
                <div class="tab-pane fade show active" id="tugas-aktif" role="tabpanel" aria-labelledby="aktif-tab" tabindex="0">
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
                                            <strong>{{ $tugas->jenisSampah->nama_sampah ?? 'N/A' }}</strong><br>
                                            <small class="text-muted">Est: {{ $tugas->estimasi_berat ?? '-' }}</small><br>
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
                                        <td colspan="5" class="text-center">Belum ada tugas penjemputan yang diterima.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- TAB 2: RIWAYAT SELESAI --}}
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
                                        <td colspan="4" class="text-center">Belum ada riwayat penjemputan yang selesai atau ditolak.</td>
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