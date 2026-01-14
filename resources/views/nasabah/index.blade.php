@extends('layouts.main')

@section('title', 'Catat Transaksi')

@section('content')
<div class="container" style="max-width: 800px;">
    
    {{-- 1. ALERT SUKSES (HIJAU) --}}
    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
        <div class="d-flex justify-content-between align-items-center">
            <span>
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            </span>
            @if(session('trx_id'))
                <a href="{{ route('transaksi.struk', session('trx_id')) }}" 
                   target="_blank" 
                   class="btn btn-sm btn-light fw-bold text-success border shadow-sm">
                    <i class="bi bi-printer-fill me-1"></i> Cetak Struk
                </a>
            @endif
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    {{-- 2. ALERT ERROR / SATPAM JADWAL (MERAH) - WAJIB ADA INI! --}}
    @if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
        <div class="d-flex align-items-center">
            <i class="bi bi-exclamation-triangle-fill me-2 fs-4"></i>
            <div>
                <strong>Transaksi Ditolak!</strong><br>
                {{ session('error') }}
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    {{-- Search Bar --}}
    <div class="input-group input-group-lg mb-4">
        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
        <input type="text" class="form-control border-start-0 ps-0" placeholder="Cari nama nasabah...">
    </div>

    {{-- Daftar Nasabah --}}
    <div class="d-grid gap-3">
        @foreach ($nasabahList as $nasabah)
            <div class="card nasabah-card shadow-sm border-0 hover-effect">
                <div class="card-body d-flex justify-content-between align-items-center p-3">
                    <div class="nasabah-info">
                        <h5 class="fw-bold mb-1 text-dark">{{ $nasabah->nama }}</h5>
                        <p class="text-muted mb-0 small">
                            <i class="bi bi-wallet2 me-1"></i> Saldo: 
                            <span class="text-success fw-bold">Rp {{ number_format($nasabah->saldo, 0, ',', '.') }}</span>
                        </p>
                    </div>
                    <div class="nasabah-actions">
                        {{-- Tombol Trigger Modal Setor --}}
                        <button type="button" class="btn btn-primary btn-sm px-3 open-setor-modal" 
                                data-bs-toggle="modal" 
                                data-bs-target="#setorModal"
                                data-nasabah-id="{{ $nasabah->id }}"
                                data-nasabah-nama="{{ $nasabah->nama }}">
                            <i class="bi bi-plus-circle me-1"></i> Setor
                        </button>
                        
                        {{-- Tombol Trigger Modal Tarik --}}
                        <button type="button" class="btn btn-outline-danger btn-sm px-3 ms-1"
                                data-bs-toggle="modal" 
                                data-bs-target="#tarikModal"
                                data-nasabah-id="{{ $nasabah->id }}"
                                data-nasabah-nama="{{ $nasabah->nama }}"
                                data-nasabah-saldo="{{ $nasabah->saldo }}">
                            <i class="bi bi-dash-circle me-1"></i> Tarik
                        </button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection

{{-- Bagian Modal tetap sama, pastikan script JS di bawah tetap ada --}}
@section('modal')
{{-- ... (Kode modalmu yang tadi sudah benar, biarkan saja) ... --}}
<div class="modal fade" id="setorModal" tabindex="-1" aria-labelledby="setorModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h1 class="modal-title fs-5" id="setorModalLabel">Setor Sampah</h1>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('transaksi.storeSetor') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="nasabah_id" id="nasabahIdInput">
                    
                    <div class="alert alert-primary py-2 d-flex justify-content-between align-items-center small">
                        <span><i class="bi bi-calendar-event me-1"></i> Tanggal Transaksi:</span>
                        {{-- Tampilkan Tanggal Hari Ini Secara Otomatis --}}
                        <strong class="fs-6">{{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</strong>
                    </div>

                    <div class="row">
                        <div class="col-md-7">
                            <div class="mb-3">
                                <label class="form-label fw-bold small text-muted">Jenis Sampah</label>
                                <select class="form-select" id="jenisSampahSelect" name="jenis_sampah" required>
                                    <option value="" data-harga="0" disabled selected>Pilih Jenis...</option>
                                    {{-- Pastikan controller mengirim variabel $jenisSampahList --}}
                                    @foreach ($jenisSampahList as $sampah)
                                        <option value="{{ $sampah->nama_sampah }}" data-harga="{{ $sampah->harga_per_kg }}">
                                            {{ $sampah->nama_sampah }} (Rp {{ number_format($sampah->harga_per_kg) }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="mb-3">
                                <label class="form-label fw-bold small text-muted">Berat (kg)</label>
                                <input type="number" step="0.1" class="form-control" id="beratInput" name="berat" placeholder="0.0" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-3 bg-light rounded border">
                        <label class="form-label small text-muted mb-1">Estimasi Total Harga</label>
                        <div class="input-group">
                            <span class="input-group-text border-0 bg-transparent fw-bold text-success">Rp</span>
                            <input type="text" class="form-control border-0 bg-transparent fw-bold text-success fs-4 p-0" 
                                   id="hargaSampahInput" name="total_harga_display" placeholder="0" readonly>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Transaksi</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Tarik --}}
<div class="modal fade" id="tarikModal" tabindex="-1" aria-labelledby="tarikModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h1 class="modal-title fs-5" id="tarikModalLabel">Tarik Saldo</h1>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('transaksi.storeTarik') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="nasabah_id" id="tarikNasabahIdInput">
                    
                    <div class="alert alert-warning d-flex justify-content-between align-items-center mb-3">
                        <small>Saldo Tersedia:</small>
                        <strong id="saldoSaatIni" class="fs-5 text-dark">Rp 0</strong>
                    </div>

                    <div class="alert alert-secondary py-2 d-flex justify-content-between align-items-center small mb-3">
                        <span><i class="bi bi-calendar-check me-1"></i> Tanggal Penarikan:</span>
                        <strong>{{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</strong>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Nominal Penarikan</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" class="form-control" id="nominal_penarikan" name="nominal_penarikan" placeholder="Min. 1000" min="1000" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Konfirmasi Tarik</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- Script JS Modal (Sama seperti punyamu) --}}
<script>
    const setorModal = document.getElementById('setorModal');
    if (setorModal) {
        setorModal.addEventListener('show.bs.modal', event => {
            const button = event.relatedTarget;
            const nasabahId = button.getAttribute('data-nasabah-id');
            const nasabahNama = button.getAttribute('data-nasabah-nama');
            setorModal.querySelector('.modal-title').textContent = `Setor Sampah: ${nasabahNama}`;
            setorModal.querySelector('#nasabahIdInput').value = nasabahId;
        });

        const jenisSampahSelect = document.getElementById('jenisSampahSelect');
        const beratInput = document.getElementById('beratInput');
        const hargaSampahInput = document.getElementById('hargaSampahInput');

        function hitungHarga() {
            const selectedOption = jenisSampahSelect.options[jenisSampahSelect.selectedIndex];
            const hargaPerKg = parseFloat(selectedOption.getAttribute('data-harga')) || 0;
            const berat = parseFloat(beratInput.value) || 0;
            const totalHarga = hargaPerKg * berat;
            hargaSampahInput.value = totalHarga.toLocaleString('id-ID');
        }

        jenisSampahSelect.addEventListener('change', hitungHarga);
        beratInput.addEventListener('input', hitungHarga);

        setorModal.addEventListener('hidden.bs.modal', () => {
            setorModal.querySelector('form').reset();
            hargaSampahInput.value = '0';
        });
    }

    const tarikModal = document.getElementById('tarikModal');
    if (tarikModal) {
        tarikModal.addEventListener('show.bs.modal', event => {
            const button = event.relatedTarget;
            const nasabahId = button.getAttribute('data-nasabah-id');
            const nasabahNama = button.getAttribute('data-nasabah-nama');
            const nasabahSaldo = parseFloat(button.getAttribute('data-nasabah-saldo'));

            tarikModal.querySelector('.modal-title').textContent = `Tarik Saldo: ${nasabahNama}`;
            tarikModal.querySelector('#tarikNasabahIdInput').value = nasabahId;
            tarikModal.querySelector('#saldoSaatIni').textContent = `Rp ${nasabahSaldo.toLocaleString('id-ID')}`;
            tarikModal.querySelector('#nominal_penarikan').setAttribute('max', nasabahSaldo);
        });
        
        tarikModal.addEventListener('hidden.bs.modal', () => {
            tarikModal.querySelector('form').reset();
        });
    }
</script>
@endpush