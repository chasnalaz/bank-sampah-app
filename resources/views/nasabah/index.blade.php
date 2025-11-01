@extends('layouts.main')

@section('title', 'Catat Transaksi')


@section('content')
<div class="container" style="max-width: 800px;">
    
    {{-- Notifikasi Sukses --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Search Bar --}}
    <div class="input-group input-group-lg mb-4">
        <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
        <input type="text" class="form-control" placeholder="Cari nama nasabah...">
    </div>

    {{-- Daftar Nasabah --}}
    <div class="d-grid gap-3">
        @foreach ($nasabahList as $nasabah)
            <div class="card nasabah-card">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div class="nasabah-info">
                        <h5 class="fw-bold mb-1">{{ $nasabah->nama }}</h5>
                        <p class="text-muted mb-0">Saldo: Rp {{ number_format($nasabah->saldo, 0, ',', '.') }}</p>
                    </div>
                    <div class="nasabah-actions">
                        <button type="button" class="btn btn-success open-setor-modal" 
                                data-bs-toggle="modal" 
                                data-bs-target="#setorModal"
                                data-nasabah-id="{{ $nasabah->id }}"
                                data-nasabah-nama="{{ $nasabah->nama }}">
                            <i class="bi bi-arrow-down-circle me-1"></i> Setor
                        </button>
                        <button type="button" class="btn btn-info text-white"
                                data-bs-toggle="modal"
                                data-bs-target="#tarikModal"
                                data-nasabah-id="{{ $nasabah->id }}"
                                data-nasabah-nama="{{ $nasabah->nama }}"
                                data-nasabah-saldo="{{ $nasabah->saldo }}">
                            <i class="bi bi-arrow-up-circle me-1"></i> Tarik
                        </button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection

@section('modal')
<div class="modal fade" id="setorModal" tabindex="-1" aria-labelledby="setorModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="setorModalLabel">Setor Sampah untuk [Nama Nasabah]</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('transaksi.storeSetor') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="nasabah_id" id="nasabahIdInput">
                    <div class="mb-3">
                        <label for="tanggal_setor" class="form-label">Tanggal Setor</label>
                        <input type="date" class="form-control" id="tanggal_setor" name="tanggal_setor" required>
                    </div>
                    <div class="row">
                        <div class="col-md-7">
                            <div class="mb-3">
                                <label for="jenisSampahSelect" class="form-label">Jenis Sampah</label>
                                <select class="form-select" id="jenisSampahSelect" name="jenis_sampah" required>
                                    <option value="" data-harga="0" disabled selected>Pilih Jenis Sampah...</option>
                                    @foreach ($jenisSampahList as $sampah)
                                        <option value="{{ $sampah->nama_sampah }}" data-harga="{{ $sampah->harga_per_kg }}">
                                            {{ $sampah->nama_sampah }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="mb-3">
                                <label for="beratInput" class="form-label">Berat (kg)</label>
                                <input type="number" step="0.1" class="form-control" id="beratInput" name="berat" placeholder="Contoh: 1.5" required>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="hargaSampahInput" class="form-label">Total Harga</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" class="form-control" id="hargaSampahInput" name="total_harga_display" placeholder="0" readonly>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Transaksi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="tarikModal" tabindex="-1" aria-labelledby="tarikModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="tarikModalLabel">Tarik Saldo untuk [Nama Nasabah]</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('transaksi.storeTarik') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="nasabah_id" id="tarikNasabahIdInput">
                    <div class="alert alert-light border">
                        Saldo Saat Ini: <strong id="saldoSaatIni" class="fs-5">Rp 0</strong>
                    </div>
                    <div class="mb-3">
                        <label for="tanggal_tarik" class="form-label">Tanggal Penarikan</label>
                        <input type="date" class="form-control" id="tanggal_tarik" name="tanggal_tarik" required>
                    </div>
                    <div class="mb-3">
                        <label for="nominal_penarikan" class="form-label">Nominal Penarikan</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" class="form-control" id="nominal_penarikan" name="nominal_penarikan" placeholder="0" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Penarikan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Script untuk Modal Setor
    const setorModal = document.getElementById('setorModal');
    if (setorModal) {
        setorModal.addEventListener('show.bs.modal', event => {
            const button = event.relatedTarget;
            const nasabahId = button.getAttribute('data-nasabah-id');
            const nasabahNama = button.getAttribute('data-nasabah-nama');
            setorModal.querySelector('.modal-title').textContent = `Setor Sampah untuk ${nasabahNama}`;
            setorModal.querySelector('#nasabahIdInput').value = nasabahId;
            document.getElementById('tanggal_setor').valueAsDate = new Date();
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

    // Script untuk Modal Tarik
    const tarikModal = document.getElementById('tarikModal');
    if (tarikModal) {
        tarikModal.addEventListener('show.bs.modal', event => {
            const button = event.relatedTarget;
            const nasabahId = button.getAttribute('data-nasabah-id');
            const nasabahNama = button.getAttribute('data-nasabah-nama');
            const nasabahSaldo = parseFloat(button.getAttribute('data-nasabah-saldo'));

            tarikModal.querySelector('.modal-title').textContent = `Tarik Saldo untuk ${nasabahNama}`;
            tarikModal.querySelector('#tarikNasabahIdInput').value = nasabahId;
            tarikModal.querySelector('#saldoSaatIni').textContent = `Rp ${nasabahSaldo.toLocaleString('id-ID')}`;
            document.getElementById('tanggal_tarik').valueAsDate = new Date();
            tarikModal.querySelector('#nominal_penarikan').setAttribute('max', nasabahSaldo);
        });

        tarikModal.addEventListener('hidden.bs.modal', () => {
            tarikModal.querySelector('form').reset();
        });
    }
</script>
@endpush