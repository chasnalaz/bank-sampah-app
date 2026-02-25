@extends('layouts.main')

@section('title', 'Dashboard')

@section('content')


    {{-- BAGIAN 1: SALDO & STATUS OPERASIONAL --}}
    <div class="row g-3 mb-4">
        
        {{-- CARD SALDO --}}
        {{-- REVISI: Ubah col-lg-8 jadi col-lg-7 (Biar memendek sedikit) --}}
        <div class="col-12 col-lg-7">
            <div class="card bg-warning text-white h-100 border-0 shadow-sm" 
                 style="background: linear-gradient(135deg, #ff8c00 0%, #ff5e00 100%); border-radius: 12px;">
                <div class="card-body p-4 d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="fw-bold mb-1 opacity-75">Saldo Tabungan</h6>
                        {{-- Font kita kembalikan ke display-5 biar pas --}}
                        <h2 class="display-5 fw-bold mb-0">Rp {{ number_format($nasabah->saldo, 0, ',', '.') }}</h2>
                    </div>
                    <div class="text-end opacity-25">
                         <i class="bi bi-wallet2" style="font-size: 3.5rem;"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- CARD STATUS --}}
        {{-- REVISI: Ubah col-lg-4 jadi col-lg-5 (Menyesuaikan sisa ruang) --}}
        <div class="col-12 col-lg-5">
            <div class="card h-100 border-0 shadow-sm" style="border-radius: 12px; background-color: #ffffff;">
                <div class="card-body p-4 d-flex align-items-center justify-content-between">
                    <div>
                        <small class="text-muted fw-bold d-block mb-1 text-uppercase">Status Operasional</small>
                        
                        {{-- Jam Buka --}}
                        <div class="fw-bold text-dark fs-5">
                            <i class="bi bi-clock me-1 text-warning"></i> {{ $jamBuka }} - {{ $jamTutup }}
                        </div>
                        
                        {{-- Tanggal --}}
                        <small class="text-muted">
                            {{ $tglBuka ? \Carbon\Carbon::parse($tglBuka)->translatedFormat('d F Y') : '-' }}
                        </small>
                    </div>
                    
                    {{-- Badge Status --}}
                    <div>
                        @if($sedangBuka)
                            <span class="badge bg-success fs-6 px-4 py-2 rounded-pill shadow-sm">
                                BUKA
                            </span>
                        @else
                            <span class="badge bg-secondary fs-6 px-4 py-2 rounded-pill shadow-sm">
                                TUTUP
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>


    {{-- BAGIAN 2: DAFTAR HARGA SAMPAH --}}
    <h5 class="fw-bold mb-3 text-dark"><i class="bi bi-tags-fill me-2 text-warning"></i>Harga Sampah Hari Ini</h5>
    
    {{-- REVISI UKURAN KARTU HARGA --}}
    {{-- Ganti 'row-cols-lg-6' (terlalu kecil) jadi 'row-cols-lg-5' (agak gedean dikit) --}}
    <div class="row row-cols-2 row-cols-md-3 row-cols-lg-5 g-3 mb-5">
        @foreach($daftarSampah as $sampah)
        <div class="col">
            {{-- TAMBAHKAN ONCLICK DAN CURSOR POINTER --}}
            <div class="card border-0 shadow-sm text-center h-100 position-relative" 
                 style="border-radius: 10px; transition: transform 0.2s; cursor: pointer;"
                 onclick="showGrafikHarga({{ $sampah->id }}, '{{ $sampah->nama_sampah }}', {{ $sampah->harga_per_kg }})">
                
                {{-- Badge Info kecil --}}
                <span class="position-absolute top-0 end-0 translate-middle p-1 bg-info border border-light rounded-circle" style="width:12px; height:12px;" title="Klik untuk lihat grafik">
                    <span class="visually-hidden">Info</span>
                </span>

                <div class="card-body p-3 d-flex flex-column justify-content-center">
                    <h6 class="fw-bold text-dark mb-2 text-uppercase" style="font-size: 0.9rem;">
                        {{ $sampah->nama_sampah }}
                    </h6>
                    <div class="text-warning fw-bold fs-5">
                        Rp {{ number_format($sampah->harga_per_kg, 0, ',', '.') }}
                    </div>
                    <small class="text-muted" style="font-size: 0.7rem;">/ kg</small>
                </div>
            </div>
        </div>
        @endforeach
    </div>


    {{-- BAGIAN 3: EDUKASI (Tetap, tidak ada perubahan request) --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-lightbulb-fill me-2 text-warning"></i>Pojok Edukasi</h5>
        <a href="{{ route('edukasi.index') }}" class="text-decoration-none small fw-bold">Lihat Semua <i class="bi bi-arrow-right"></i></a>
    </div>

    <div class="row g-4">
        @forelse($edukasiList as $item)
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100 overflow-hidden" style="border-radius: 12px;">
                <div class="row g-0 h-100">
                    <div class="col-lg-5 position-relative bg-light" style="min-height: 200px;">
                        @if($item->kategori == 'video' && $item->youtube_id)
                            <div class="ratio ratio-1x1 h-100">
                                <iframe src="https://www.youtube.com/embed/{{ $item->youtube_id }}" style="object-fit: cover;" allowfullscreen></iframe>
                            </div>
                        @elseif($item->kategori == 'poster' && $item->gambar)
                            <img src="{{ asset('storage/' . $item->gambar) }}" class="w-100 h-100 object-fit-cover" alt="Poster">
                        @else
                            <div class="d-flex align-items-center justify-content-center h-100 text-muted">
                                <i class="bi bi-book fs-1"></i>
                            </div>
                        @endif
                        <div class="position-absolute top-0 start-0 m-2">
                            <span class="badge bg-white text-dark shadow-sm">
                                <i class="bi {{ $item->kategori == 'video' ? 'bi-play-circle-fill text-danger' : 'bi-image-fill text-primary' }}"></i> {{ ucfirst($item->kategori) }}
                            </span>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <div class="card-body d-flex flex-column h-100 justify-content-center p-4">
                            <h5 class="card-title fw-bold mb-2">{{ $item->judul }}</h5>
                            <p class="card-text text-muted small mb-3">
                                {{ Str::limit($item->deskripsi, 100) }}
                            </p>
                            <div>
                                <a href="{{ route('edukasi.index') }}" class="btn btn-sm btn-outline-warning rounded-pill px-3">Lihat Detail</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="alert alert-light border text-center text-muted">Belum ada konten edukasi terbaru.</div>
        </div>
        @endforelse
    </div>

    {{-- MODAL GRAFIK RIWAYAT (POP-UP) --}}
    <div class="modal fade" id="modalGrafik" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
                <div class="modal-header bg-0 border-0" style="border-radius: 15px 15px 0 0;">
                    <div>
                        <h5 class="modal-title fw-bold" id="judulModal">Detail Harga</h5>
                        <small class="opacity-75">Riwayat perubahan harga beli</small>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    {{-- Harga Saat Ini --}}
                    <div class="text-center mb-3">
                        <span class="text-muted small text-uppercase fw-bold">Harga Saat Ini</span>
                        <h2 class="fw-bold text-dark" id="hargaSaatIni">Rp 0</h2>
                    </div>

                    {{-- Area Grafik --}}
                    <div style="height: 250px;">
                        <canvas id="chartNasabah"></canvas>
                    </div>
                </div>
                <div class="modal-footer border-0 justify-content-center pb-4">
                    <button type="button" class="btn btn-light text-muted rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    {{-- SCRIPT CHART.JS --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // 1. Terima Data dari Controller
        const dataRiwayat = {!! json_encode($chartRiwayat) !!};
        let myChart = null; // Variabel chart
        
        // 2. Fungsi dipanggil saat kartu diklik
        function showGrafikHarga(id, nama, hargaSekarang) {
            // Update Teks Modal
            document.getElementById('judulModal').innerText = 'Grafik Harga: ' + nama;
            document.getElementById('hargaSaatIni').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(hargaSekarang);

            // Tampilkan Modal Bootstrap
            var myModal = new bootstrap.Modal(document.getElementById('modalGrafik'));
            myModal.show();

            // Siapkan Data Grafik
            const dataSampah = dataRiwayat[id];
            const labels = dataSampah ? dataSampah.map(item => item.tgl) : ['Hari Ini'];
            const values = dataSampah ? dataSampah.map(item => item.harga) : [hargaSekarang];

            // Render Chart
            const ctx = document.getElementById('chartNasabah').getContext('2d');
            
            // Hapus chart lama kalau ada (biar ga numpuk/glitch)
            if (myChart) {
                myChart.destroy();
            }

            // Buat Chart Baru
            myChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Harga (Rp)',
                        data: values,
                        borderColor: '#6c757d',          // Abu-abu bootstrap (Secondary)
                        backgroundColor: 'rgba(108, 117, 125, 0.1)', // Transparan halus
                        borderWidth: 2,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#6c757d',
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        fill: true,
                        tension: 0.4 // Lebih melengkung halus
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } }, // Sembunyikan legenda biar bersih
                    scales: {
                        y: { 
                            beginAtZero: false, // Biar fluktuasinya kelihatan jelas
                            ticks: { callback: function(val) { return val/1000 + 'k'; } } // Format sumbu Y jadi "5k"
                        },
                        x: { display: false } // Sembunyikan tanggal di bawah biar ga semak (cukup di tooltip)
                    }
                }
            });
        }
    </script>
@endsection