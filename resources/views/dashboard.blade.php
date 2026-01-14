@extends('layouts.main')

@section('title', 'Dashboard')

@section('content')

<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            {{-- HEADER CARD: STATUS REAL-TIME --}}
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold">
                    <i class="bi bi-calendar-check me-2"></i>Jadwal Operasional Bank Sampah
                </h6>

                {{-- LOGIKA INDIKATOR STATUS (PHP di dalam Blade) --}}
                @php
                    // Ambil data dari Global Variable yg kita buat di Step 2
                    $hariIni   = date('Y-m-d');
                    $sekarang  = \Carbon\Carbon::now();
                    $buka      = \Carbon\Carbon::parse($globalJamBuka);
                    $tutup     = \Carbon\Carbon::parse($globalJamTutup);

                    // Cek 1: Apakah Hari Ini = Tanggal Jadwal?
                    $isTanggalCocok = ($globalTglBuka == $hariIni);
                    
                    // Cek 2: Apakah Jam Sekarang di antara Buka & Tutup?
                    $isJamCocok     = $sekarang->between($buka, $tutup);
                @endphp

                {{-- TAMPILAN BADGE STATUS --}}
                @if($isTanggalCocok && $isJamCocok)
                    <span class="badge bg-success text-white px-3 py-2 animate__animated animate__pulse animate__infinite">
                        <i class="bi bi-unlock-fill me-1"></i> SEDANG BUKA (LIVE)
                    </span>
                @elseif($isTanggalCocok && !$isJamCocok)
                    <span class="badge bg-warning text-dark px-3 py-2">
                        <i class="bi bi-hourglass-split me-1"></i> HARI INI (Tunggu Jam {{ $globalJamBuka }})
                    </span>
                @else
                    <span class="badge bg-secondary text-white px-3 py-2">
                        <i class="bi bi-lock-fill me-1"></i> SEDANG TUTUP
                    </span>
                @endif
            </div>

            <div class="card-body">
                <div class="row align-items-center">
                    {{-- BAGIAN KIRI: INFO JADWAL SAAT INI --}}
                    <div class="col-md-5 mb-3 mb-md-0 border-end">
                        <p class="text-muted mb-1 small text-uppercase font-weight-bold">Jadwal Buka Berikutnya:</p>
                        
                        {{-- Tampilkan Tanggal Besar --}}
                        <h3 class="font-weight-bold text-gray-800">
                            @if($globalTglBuka)
                                {{ \Carbon\Carbon::parse($globalTglBuka)->translatedFormat('l, d F Y') }}
                            @else
                                <span class="text-danger">Belum Diatur</span>
                            @endif
                        </h3>
                        
                        <p class="mb-0 text-dark">
                            <i class="bi bi-clock me-1"></i> Pukul: 
                            <span class="font-weight-bold">{{ $globalJamBuka }} - {{ $globalJamTutup }} WIB</span>
                        </p>
                    </div>

                    {{-- BAGIAN KANAN: FORM UPDATE JADWAL --}}
                    <div class="col-md-7 pl-md-4">
                        <div class="alert alert-light border mb-2 py-2 px-3 small">
                            <i class="bi bi-info-circle text-primary me-1"></i> 
                            Ubah jadwal di bawah ini untuk menentukan hari buka selanjutnya.
                        </div>

                        <form action="{{ route('pengaturan.update') }}" method="POST" class="row g-2 align-items-end">
                            @csrf
                            @method('PUT')
                            
                            {{-- Input Tanggal --}}
                            <div class="col-md-5">
                                <label class="small font-weight-bold text-muted">Tanggal Event</label>
                                <input type="date" name="tanggal_buka" class="form-control" required 
                                       value="{{ $globalTglBuka }}" min="{{ date('Y-m-d') }}">
                            </div>

                            {{-- Input Jam --}}
                            <div class="col-md-3 col-6">
                                <label class="small font-weight-bold text-muted">Buka</label>
                                <input type="time" name="jam_buka" class="form-control" required value="{{ $globalJamBuka }}">
                            </div>
                            <div class="col-md-3 col-6">
                                <label class="small font-weight-bold text-muted">Tutup</label>
                                <input type="time" name="jam_tutup" class="form-control" required value="{{ $globalJamTutup }}">
                            </div>

                            {{-- Tombol Simpan --}}
                            <div class="col-md-1">
                                <button type="submit" class="btn btn-primary w-100" title="Simpan Jadwal">
                                    <i class="bi bi-save"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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

   <div class="col-md-6 col-lg-3">
    <div class="card shadow-sm border-0">
        <div class="card-body text-center">
            <i class="bi bi-person-check-fill fs-1 text-success"></i>
            @php
                $petugasSiap = \App\Models\User::where('role', 'petugas')
                                ->where('status_tugas', 'siap')
                                ->get();
            @endphp
            <h5 class="card-title mt-3">{{ $petugasSiap->count() }} Orang</h5>
            <p class="card-text text-muted">Petugas Siap</p>
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