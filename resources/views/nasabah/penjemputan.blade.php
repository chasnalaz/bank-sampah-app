@extends('layouts.main')

@section('title', 'Penjemputan Sampah')

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 fw-bold"><i class="bi bi-clock-history me-2"></i>Riwayat Permintaan</h6>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#formPenjemputanModal">
                <i class="bi bi-plus-lg me-1"></i> Buat Permintaan Baru
            </button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Tanggal Usulan</th>
                            <th>Alamat</th>
                            <th>Jenis Sampah</th>
                            <th>Status</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($riwayatPenjemputan as $permintaan)
                        <tr>
                            <td class="ps-4">{{ \Carbon\Carbon::parse($permintaan->usulan_tanggal)->translatedFormat('d F Y') }}</td>
                            <td>{{ Str::limit($permintaan->alamat_penjemputan, 30) }}</td>
                            <td>{{ $permintaan->jenisSampah->nama_sampah ?? '-' }} <br> <small class="text-muted">{{ $permintaan->estimasi_berat }}</small></td>
                            <td>
                                @if ($permintaan->status == 'Selesai')
                                    <span class="badge bg-success">Selesai</span>
                                @elseif ($permintaan->status == 'Diterima')
                                    <span class="badge bg-info">Diterima</span>
                                @else
                                    <span class="badge bg-warning text-dark">Menunggu Konfirmasi</span>
                                @endif
                            </td>
                            <td><small class="text-muted">{{ $permintaan->catatan_nasabah ?? '-' }}</small></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">Belum ada riwayat permintaan penjemputan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('modal')
    {{-- Copy Paste Modal dari file lama kamu (tidak perlu diubah kodenya, cuma pastikan ada di sini) --}}
    <div class="modal fade" id="formPenjemputanModal" tabindex="-1" aria-hidden="true">
        {{-- ... Isi Modal Form Penjemputan (Sama persis kayak sebelumnya) ... --}}
        {{-- Gunakan kode modal yang ada di file penjemputan.blade.php kamu sebelumnya --}}
         <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Formulir Permintaan Penjemputan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('nasabah.penjemputan.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Usulan Tanggal</label>
                            <input type="date" class="form-control" name="usulan_tanggal" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Alamat Penjemputan</label>
                            <textarea class="form-control" name="alamat_penjemputan" rows="2" required>{{ $nasabah->alamat }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Jenis Sampah Utama</label>
                            <select class="form-select" name="jenis_sampah_id" required>
                                <option value="" disabled selected>Pilih jenis sampah...</option>
                                @foreach ($jenisSampahList as $sampah)
                                    <option value="{{ $sampah->id }}">{{ $sampah->nama_sampah }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Estimasi Berat</label>
                            <input type="text" class="form-control" name="estimasi_berat" placeholder="Contoh: 1 karung / 5 Kg">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Catatan</label>
                            <input type="text" class="form-control" name="catatan_nasabah" placeholder="Contoh: Pagar warna hitam">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Kirim</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection