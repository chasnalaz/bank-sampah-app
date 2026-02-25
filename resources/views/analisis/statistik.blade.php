@extends('layouts.main')
@section('title', 'Analisis & Statistik Lengkap')

@section('content')
<div class="container-fluid">

    {{-- 1. HEADER & FILTER --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <div>
            <h5 class="fw-bold text-dark mb-0">
                <i class="bi bi-bar-chart-line-fill me-2 text-primary"></i>Analisis Data
            </h5>
            <small class="text-muted fst-italic">• {{ $labelFilter ?? 'Analisis Data' }}</small>
        </div>
        
        <button class="btn btn-sm btn-outline-secondary rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalFilterAnalisis">
            <i class="bi bi-calendar4-week me-1"></i> Ganti Periode
        </button>
    </div>

    {{-- 2. KARTU RINGKASAN --}}
    <div class="row mb-4">
        <div class="col-md-6 mb-3">
            <div class="card shadow border-start-success h-100 py-3">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs fw-bold text-success text-uppercase mb-1">Total Uang Dibayarkan</div>
                        <div class="h3 mb-0 fw-bold text-gray-800">Rp {{ number_format($uangKeluar, 0, ',', '.') }}</div>
                    </div>
                    <i class="bi bi-wallet2 fs-1 text-gray-300"></i>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="card shadow border-start-warning h-100 py-3">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs fw-bold text-warning text-uppercase mb-1">Total Sampah Masuk</div>
                        <div class="h3 mb-0 fw-bold text-gray-800">{{ number_format($totalBerat, 1) }} Kg</div>
                    </div>
                    <i class="bi bi-box-seam fs-1 text-gray-300"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- 3. AREA GRAFIK UTAMA (TREN & KOMPOSISI) --}}
    <div class="row mb-4">
        <div class="col-lg-8 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 fw-bold text-primary">Tren Volume Sampah</h6>
                    <span class="badge bg-light text-muted border">{{ $modeGrafik == 'bulanan' ? 'Per Bulan' : 'Per Tanggal' }}</span>
                </div>
                <div class="card-body">
                    <div style="height: 320px;">
                        <canvas id="chartTren"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold text-primary">Komposisi Jenis</h6>
                </div>
                <div class="card-body">
                    <div style="height: 300px;">
                        <canvas id="chartKomposisi"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 4. GRAFIK RIWAYAT HARGA (DIPINDAH KE SINI / TENGAH) --}}
    <div class="card shadow mb-4 border-start-info">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 fw-bold text-info"><i class="bi bi-graph-up-arrow me-2"></i>Riwayat Harga Beli</h6>
            <select id="pilihSampah" class="form-select form-select-sm" style="width: 180px;">
                @foreach($daftarSampah as $s)
                    <option value="{{ $s->id }}">{{ $s->nama_sampah }}</option>
                @endforeach
            </select>
        </div>
        <div class="card-body">
            <div style="height: 250px;"><canvas id="chartRiwayat"></canvas></div>
        </div>
    </div>

    {{-- 5. DETAIL ANALISIS & SARAN (DIPINDAH KE PALING BAWAH) --}}
    <div class="card shadow mb-4">
        <div class="card-header py-2">
            <ul class="nav nav-tabs card-header-tabs" id="myTab" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active fw-bold text-dark" data-bs-toggle="tab" data-bs-target="#tabNasabah" type="button">
                        <i class="bi bi-tree-fill me-2 text-success"></i>Top Nasabah
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link fw-bold text-dark" data-bs-toggle="tab" data-bs-target="#tabPetugas" type="button">
                        <i class="bi bi-clipboard-data-fill me-2 text-info"></i>Rapor Petugas
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link fw-bold text-dark" data-bs-toggle="tab" data-bs-target="#tabSaran" type="button">
                        <i class="bi bi-lightbulb-fill me-2 text-warning"></i>Saran AI
                    </button>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content">
                
                {{-- TAB 1: NASABAH --}}
                <div class="tab-pane fade show active" id="tabNasabah">
                    <div class="row">
                        <div class="col-md-4 border-end text-center py-4">
                            <h6 class="fw-bold text-muted">Partisipasi Aktif</h6>
                            <h1 class="display-4 fw-bold text-success">{{ number_format($persenAktif, 0) }}<span class="fs-4">%</span></h1>
                            <p class="text-muted small">Dari Total {{ $totalNasabah }} Nasabah</p>
                        </div>
                        <div class="col-md-8">
                            <h6 class="fw-bold text-muted ps-3 mb-3">🏆 Top 5 Penyetor Terbanyak</h6>
                            <div class="table-responsive ps-3">
                                <table class="table table-hover align-middle table-sm">
                                    <thead class="table-light"><tr><th>Nama</th><th class="text-end">Total Sampah</th></tr></thead>
                                    <tbody>
                                        @forelse($topNasabah as $n)
                                            <tr>
                                                <td class="fw-bold">{{ $n->nasabah->nama }}</td>
                                                <td class="text-end fw-bold text-success">{{ number_format($n->total_berat, 1) }} Kg</td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="2" class="text-center text-muted fst-italic">Belum ada data.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- TAB 2: PETUGAS --}}
                <div class="tab-pane fade" id="tabPetugas">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm align-middle">
                            <thead class="bg-light text-center"><tr><th>Nama Petugas</th><th>Kontribusi</th></tr></thead>
                            <tbody>
                                @forelse($kinerjaPetugas as $p)
                                    <tr><td>{{ $p->name }}</td><td class="text-center fw-bold">{{ $p->transaksis_count }}x</td></tr>
                                @empty
                                    <tr><td colspan="2" class="text-center text-muted">Data tidak ditemukan.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- TAB 3: SARAN AI --}}
                <div class="tab-pane fade" id="tabSaran">
                    @if(isset($saran) && count($saran) > 0)
                        @foreach($saran as $s)
                            <div class="alert alert-light border-start border-warning border-4 shadow-sm mb-2">
                                <i class="bi bi-exclamation-circle-fill text-warning me-2"></i> {{ $s }}
                            </div>
                        @endforeach
                    @else
                        <div class="alert alert-success border-0 shadow-sm">
                            <i class="bi bi-check-circle-fill me-2"></i> Sistem berjalan optimal.
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>

</div>

{{-- MODAL FILTER --}}
<div class="modal fade" id="modalFilterAnalisis" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold fs-6">Pilih Periode Analisis</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('analisis.statistik') }}" method="GET">
                    
                    {{-- OPSI 1: ANALISIS TAHUNAN --}}
                    <div class="form-check mb-2 position-relative">
                        <input class="form-check-input" type="radio" name="filter_jenis" value="tahun" id="anl_tahun" 
                            {{ (request('filter_jenis') == 'tahun' || !request()->has('filter_jenis')) ? 'checked' : '' }}>
                        <label class="form-check-label w-100" for="anl_tahun">Analisis Tahunan</label>
                    </div>

                    <div class="ms-4 mb-3 collapse {{ (request('filter_jenis') == 'tahun' || !request()->has('filter_jenis')) ? 'show' : '' }}" id="areaTahunAnalisis">
                        <select name="tahun" class="form-select form-select-sm">
                            @foreach(range(date('Y'), 2023) as $y)
                                <option value="{{ $y }}" {{ (request('tahun') ?? date('Y')) == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- OPSI 2: ANALISIS BULANAN --}}
                    <div class="form-check mb-2 position-relative">
                        <input class="form-check-input" type="radio" name="filter_jenis" value="bulan" id="anl_bulan" 
                            {{ request('filter_jenis') == 'bulan' ? 'checked' : '' }}>
                        <label class="form-check-label w-100" for="anl_bulan">Analisis Bulanan</label>
                    </div>

                    <div class="ms-4 mb-3 collapse {{ request('filter_jenis') == 'bulan' ? 'show' : '' }}" id="areaBulanAnalisis">
                        <select name="bulan" class="form-select form-select-sm mb-2">
                            @foreach(range(1, 12) as $m)
                                <option value="{{ $m }}" {{ (request('bulan') ?? date('m')) == $m ? 'selected' : '' }}>
                                    {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                </option>
                            @endforeach
                        </select>
                        <select name="tahun" class="form-select form-select-sm">
                            @foreach(range(date('Y'), 2023) as $y)
                                <option value="{{ $y }}" {{ (request('tahun') ?? date('Y')) == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 rounded-pill mt-3">Tampilkan Data</button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- SCRIPT JAVASCRIPT --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // 1. FILTER TOGGLE
    const radioTahun = document.getElementById('anl_tahun');
    const radioBulan = document.getElementById('anl_bulan');
    const areaTahun = document.getElementById('areaTahunAnalisis');
    const areaBulan = document.getElementById('areaBulanAnalisis');

    function toggleFilter() {
        if (radioTahun.checked) {
            areaTahun.classList.add('show');
            areaTahun.querySelectorAll('select').forEach(el => el.disabled = false);
            areaBulan.classList.remove('show');
            areaBulan.querySelectorAll('select').forEach(el => el.disabled = true);
        } else {
            areaTahun.classList.remove('show');
            areaTahun.querySelectorAll('select').forEach(el => el.disabled = true);
            areaBulan.classList.add('show');
            areaBulan.querySelectorAll('select').forEach(el => el.disabled = false);
        }
    }
    radioTahun.addEventListener('change', toggleFilter);
    radioBulan.addEventListener('change', toggleFilter);
    toggleFilter();

    // 2. CHART TREN
    const ctxTren = document.getElementById('chartTren');
    new Chart(ctxTren, {
        type: 'line',
        data: {
            labels: {!! json_encode($trenLabels) !!},
            datasets: [{
                label: 'Volume (Kg)',
                data: {!! json_encode($trenValues) !!},
                borderColor: '#4e73df',
                backgroundColor: 'rgba(78, 115, 223, 0.05)',
                tension: 0.3, fill: true, pointRadius: 4
            }]
        },
        options: { maintainAspectRatio: false, scales: { y: { beginAtZero: true } } }
    });

    // 3. CHART PIE
    const ctxPie = document.getElementById('chartKomposisi');
    const lblPie = {!! json_encode($lblKomposisi) !!};
    if (lblPie.length > 0) {
        new Chart(ctxPie, {
            type: 'doughnut',
            data: {
                labels: lblPie,
                datasets: [{
                    data: {!! json_encode($valKomposisi) !!},
                    backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796'],
                    hoverOffset: 4
                }]
            },
            options: { maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
        });
    } else {
        ctxPie.parentNode.innerHTML = '<div class="d-flex align-items-center justify-content-center h-100 text-muted small">Tidak ada data sampah pada periode ini.</div>';
    }

    // 4. CHART RIWAYAT
    const dataRiwayat = {!! json_encode($chartRiwayat) !!};
    const ctxRiwayat = document.getElementById('chartRiwayat').getContext('2d');
    let chartRiwayatInstance = null; 

    function renderChart(sampahId) {
        const dataSampah = dataRiwayat[sampahId];
        const labels = dataSampah ? dataSampah.map(item => item.tgl) : ['Sekarang'];
        const values = dataSampah ? dataSampah.map(item => item.harga) : [0];

        if (chartRiwayatInstance) chartRiwayatInstance.destroy();

        chartRiwayatInstance = new Chart(ctxRiwayat, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Harga (Rp)',
                    data: values,
                    borderColor: '#36b9cc', tension: 0.2
                }]
            },
            options: {
                maintainAspectRatio: false,
                scales: { y: { ticks: { callback: function(val) { return val/1000 + 'k'; } } } },
                plugins: { legend: { display: false } }
            }
        });
    }

    const dropdownSampah = document.getElementById('pilihSampah');
    if (dropdownSampah) {
        renderChart(dropdownSampah.value);
        dropdownSampah.addEventListener('change', function() { renderChart(this.value); });
    }
</script>
@endsection