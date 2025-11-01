@extends('layouts.app')

@section('content')
    <div class="card text-center">
        <div class="card-header">
            Pilih Transaksi
        </div>
        <div class="card-body">
            <h5 class="card-title">Nasabah: {{ $nasabah->nama }}</h5>
            <p class="card-text">Saldo Saat Ini: Rp {{ number_format($nasabah->saldo, 0, ',', '.') }}</p>
            <div class="d-grid gap-2">
                <a href="{{ route('transaksi.createSetor', ['nasabah' => $nasabah->id]) }}" class="btn btn-primary">Setor Sampah</a>
                <a href="{{ route('transaksi.createTarik', ['nasabah' => $nasabah->id]) }}" class="btn btn-success">Tarik Saldo</a>
            </div>
        </div>
    </div>
@endsection