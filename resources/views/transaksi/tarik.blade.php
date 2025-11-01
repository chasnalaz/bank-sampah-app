@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3>Form Tarik Saldo</h3>
            <p>Nasabah: <strong>{{ $nasabah->nama }}</strong></p>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                Saldo saat ini: <strong>Rp {{ number_format($nasabah->saldo, 0, ',', '.') }}</strong>
            </div>
            <form action="#" method="POST">
                <div class="mb-3">
                    <label for="jumlah_tarik" class="form-label">Jumlah Penarikan (Rp)</label>
                    <input type="number" class="form-control" id="jumlah_tarik" name="jumlah_tarik" placeholder="Masukkan nominal penarikan">
                </div>
                <button type="submit" class="btn btn-success">Tarik Saldo</button>
            </form>
        </div>
    </div>
@endsection