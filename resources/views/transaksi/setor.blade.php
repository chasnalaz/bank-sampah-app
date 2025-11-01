@extends('layouts.app')

@section('content')
<div class="container" style="max-width: 600px;">
    <div class="card">
        <div class="card-header">
            <h3>Form Setor Sampah</h3>
            <p class="mb-0">Nasabah: <strong>{{ $nasabah->nama }}</strong></p>
        </div>
        <div class="card-body">
            {{-- Arahkan action ke route yang akan kita buat & tambahkan method POST --}}
            <form action="{{ route('transaksi.storeSetor') }}" method="POST">
                {{-- Token Keamanan Wajib Laravel --}}
                @csrf

                {{-- Input tersembunyi untuk mengirim ID nasabah --}}
                <input type="hidden" name="nasabah_id" value="{{ $nasabah->id }}">

                <div class="mb-3">
                    <label for="jenis_sampah" class="form-label">Jenis Sampah</label>
                    <select class="form-select" id="jenis_sampah" name="jenis_sampah" required>
                        <option value="" disabled selected>Pilih Jenis Sampah...</option>
                        <option value="plastik">Plastik</option>
                        <option value="kertas">Kertas</option>
                        <option value="logam">Logam</option>
                        <option value="kaca">Botol Kaca</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="berat" class="form-label">Berat (kg)</label>
                    <input type="number" step="0.1" class="form-control" id="berat" name="berat" placeholder="Contoh: 1.5" required>
                </div>

                <div class="d-flex justify-content-end">
                    <a href="{{ route('transaksi.pilih', ['nasabah' => $nasabah->id]) }}" class="btn btn-secondary me-2">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan Transaksi</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection