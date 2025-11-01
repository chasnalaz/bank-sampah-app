@extends('layouts.main')

@section('title', 'Dashboard')

@section('content')
<div class="row g-4">
    {{-- Card Jumlah Nasabah --}}
    <div class="col-md-6 col-lg-3">
        <div class="card shadow-sm border-0">
            <div class="card-body text-center">
                <i class="bi bi-people-fill fs-1 text-primary"></i>
                <h5 class="card-title mt-3">{{ $jumlahNasabah }}</h5>
                <p class="card-text text-muted">Total Nasabah</p>
            </div>
        </div>
    </div>

    {{-- Card Total Saldo --}}
    <div class="col-md-6 col-lg-3">
        <div class="card shadow-sm border-0">
            <div class="card-body text-center">
                <i class="bi bi-wallet2 fs-1 text-success"></i>
                <h5 class="card-title mt-3">Rp {{ number_format($totalSaldo, 0, ',', '.') }}</h5>
                <p class="card-text text-muted">Total Saldo Nasabah</p>
            </div>
        </div>
    </div>

    {{-- Card Jumlah Setoran --}}
    <div class="col-md-6 col-lg-3">
        <div class="card shadow-sm border-0">
            <div class="card-body text-center">
                <i class="bi bi-arrow-down-circle-fill fs-1 text-info"></i>
                <h5 class="card-title mt-3">{{ $jumlahSetoran }}</h5>
                <p class="card-text text-muted">Jumlah Setoran</p>
            </div>
        </div>
    </div>

    {{-- Card Jumlah Penarikan --}}
    <div class="col-md-6 col-lg-3">
        <div class="card shadow-sm border-0">
            <div class="card-body text-center">
                <i class="bi bi-arrow-up-circle-fill fs-1 text-danger"></i>
                <h5 class="card-title mt-3">{{ $jumlahPenarikan }}</h5>
                <p class="card-text text-muted">Jumlah Penarikan</p>
            </div>
        </div>
    </div>
</div>
@endsection