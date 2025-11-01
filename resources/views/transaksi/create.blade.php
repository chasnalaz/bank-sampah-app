@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3>Form Setor Sampah Baru</h3>
        </div>
        <div class="card-body">
            <form action="#" method="POST">
                
                {{-- Input untuk Nama Nasabah --}}
                <div class="mb-3">
                    <label for="nama_nasabah" class="form-label">Nama Nasabah</label>
                    <input type="text" class="form-control" id="nama_nasabah" name="nama_nasabah" placeholder="Masukkan nama nasabah">
                </div>

                {{-- Input untuk Jenis Sampah --}}
                <div class="mb-3">
                    <label for="jenis_sampah" class="form-label">Jenis Sampah</label>
                    <select class="form-select" id="jenis_sampah" name="jenis_sampah">
                        <option selected>Pilih Jenis Sampah...</option>
                        <option value="plastik">Plastik</option>
                        <option value="kertas">Kertas</option>
                        <option value="logam">Logam</option>
                        <option value="kaca">Botol Kaca</option>
                    </select>
                </div>

                {{-- Input untuk Berat Sampah --}}
                <div class="mb-3">
                    <label for="berat" class="form-label">Berat (kg)</label>
                    <input type="number" step="0.1" class="form-control" id="berat" name="berat" placeholder="Contoh: 1.5">
                </div>

                <button type="submit" class="btn btn-primary">Simpan Transaksi</button>
            </form>
        </div>
    </div>
@endsection