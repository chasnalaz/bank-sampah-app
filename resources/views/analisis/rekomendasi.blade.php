@extends('layouts.main')

@section('title', 'Rekomendasi Penjualan')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-info">
                <i class="bi bi-lightbulb-fill me-2"></i>
                <strong>Sistem Pendukung Keputusan:</strong> Berikut adalah rekomendasi tengkulak dengan harga beli tertinggi untuk setiap jenis sampah.
            </div>
        </div>
    </div>

    <div class="row">
        @foreach($rekomendasi as $sampah)
        <div class="col-md-6 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">{{ $sampah->nama_sampah }}</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Nama Tengkulak</th>
                                <th>Harga</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sampah->tengkulaks as $index => $t)
                            <tr class="{{ $index == 0 ? 'table-success fw-bold' : '' }}">
                                <td>{{ $t->nama_tengkulak }}</td>
                                <td>Rp {{ number_format($t->harga_beli, 0, ',', '.') }}</td>
                                <td>
                                    @if($index == 0)
                                        <span class="badge bg-success">Rekomendasi Utama</span>
                                    @else
                                        <span class="badge bg-secondary">Alternatif</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection