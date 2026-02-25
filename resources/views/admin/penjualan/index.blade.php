@extends('layouts.main')

@section('title', 'Riwayat Penjualan ke Tengkulak')

@section('content')
<div class="container">
    
    {{-- 1. HEADER: JUDUL & TOMBOL TAMBAH --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">
        <h5 class="fw-bold text-dark mb-0">
            <i class="bi bi-cash-stack me-2 text-success"></i>Data Penjualan
        </h5>
        
        @can('isAdmin')
            <button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#tambahModal">
                <i class="bi bi-plus-circle me-2"></i> Catat Penjualan Baru
            </button>
        @endcan
    </div>

    {{-- 2. ALERT SUKSES (SAMA SEPERTI KODEMU) --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm mb-4" role="alert">
            <div class="d-flex justify-content-between align-items-center">
                <span>
                    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                </span>
                @if(session('trx_id'))
                    <a href="{{ route('penjualan.struk', session('trx_id')) }}" 
                       target="_blank" 
                       class="btn btn-sm btn-light fw-bold text-success border shadow-sm">
                        <i class="bi bi-printer-fill me-1"></i> Cetak Struk
                    </a>
                @endif
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- 3. FILTER BAR (BARU) --}}
    <div class="card shadow-sm border-0 mb-4" style="border-radius: 10px;">
        <div class="card-body py-3">
            <form action="{{ route('penjualan.index') }}" method="GET" class="row g-2 align-items-center">
                
                {{-- Label --}}
                <div class="col-auto">
                    <span class="fw-bold text-muted"><i class="bi bi-funnel me-1"></i> Filter:</span>
                </div>

                {{-- Dropdown Bulan --}}
                <div class="col-auto">
                    <select name="bulan" class="form-select form-select-sm border-secondary" style="width: 150px;">
                        <option value="">- Semua Bulan -</option>
                        @foreach(range(1, 12) as $m)
                            <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>
                                {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Dropdown Tahun --}}
                <div class="col-auto">
                    <select name="tahun" class="form-select form-select-sm border-secondary" style="width: 120px;">
                        <option value="">- Semua Thn -</option>
                        @foreach(range(date('Y'), 2024) as $y)
                            <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Tombol Filter (Warna Oranye) --}}
                <div class="col-auto">
                    <button type="submit" class="btn btn-sm text-white fw-bold" style="background-color: #fd7e14;">
                        Terapkan
                    </button>
                    <a href="{{ route('penjualan.index') }}" class="btn btn-sm btn-outline-secondary ms-1">
                        Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- 4. TABEL DATA --}}
    <div class="card shadow-sm border-0" style="border-radius: 10px;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-secondary">
                        <tr>
                            <th class="ps-4 py-3">Tanggal</th>
                            <th>Tengkulak</th>
                            <th>Jenis Sampah</th>
                            <th>Berat (Kg)</th>
                            <th>Harga Deal</th>
                            <th>Total Pendapatan</th>
                            @can('isAdmin')
                                <th class="text-center">Aksi</th>
                            @endcan
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($penjualans as $jual)
                            <tr>
                                <td class="ps-4">{{ \Carbon\Carbon::parse($jual->tanggal_jual)->format('d M Y') }}</td>
                                <td class="fw-bold text-dark">{{ $jual->tengkulak->nama_tengkulak }}</td>
                                <td>
                                    <span class="badge bg-light text-dark border">
                                        {{ $jual->jenisSampah->nama_sampah }}
                                    </span>
                                </td>
                                <td>{{ $jual->berat_kg }} kg</td>
                                <td>Rp {{ number_format($jual->harga_per_kg, 0, ',', '.') }}</td>
                                <td class="fw-bold text-success">Rp {{ number_format($jual->total_pendapatan, 0, ',', '.') }}</td>
                                @can('isAdmin')
                                    <td class="text-center">
                                        {{-- Tombol Hapus Simple --}}
                                        <form action="{{ route('penjualan.destroy', $jual->id) }}" method="POST" onsubmit="return confirm('Hapus data penjualan ini?');" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-light text-danger" title="Hapus Data">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                @endcan
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bi bi-clipboard-x fs-1 d-block mb-2"></i>
                                    Belum ada data penjualan{{ $bulan ? ' pada periode ini' : '' }}.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination (Gaya Minimalis yg tadi) --}}
            <div class="d-flex justify-content-end p-3">
                {{ $penjualans->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@section('modal')
{{-- --- MODAL CATAT PENJUALAN (SAMA SEPERTI KODEMU) --- --}}
<div class="modal fade" id="tambahModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Catat Penjualan Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('penjualan.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Tanggal Transaksi</label>
                        <input type="date" class="form-control" name="tanggal_jual" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Jenis Sampah</label>
                        <select name="jenis_sampah_id" id="pilih_sampah" class="form-select" required>
                            <option value="">-- Pilih Jenis Sampah --</option>
                            @foreach($jenisSampahList as $s)
                                <option value="{{ $s->id }}">{{ $s->nama_sampah }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Pilih Tengkulak</label>
                        <select name="tengkulak_id" id="pilih_tengkulak" class="form-select" required disabled>
                            <option value="">-- Pilih Sampah Terlebih Dahulu --</option>
                            </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Berat (Kg)</label>
                            <input type="number" step="0.1" class="form-control" name="berat_kg" id="berat_kg" placeholder="0.0" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Harga Jual / Kg</label>
                            <input type="number" class="form-control bg-light" name="harga_per_kg" id="harga_auto" readonly required>
                            <small class="text-muted" id="info_harga">Otomatis dari data tengkulak.</small>
                        </div>
                    </div>

                    <div class="alert alert-success d-flex justify-content-between fw-bold">
                        <span>Estimasi Total:</span>
                        <span id="total_preview">Rp 0</span>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Catatan (Opsional)</label>
                        <textarea class="form-control" name="catatan" rows="2"></textarea>
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
@endsection

@push('scripts')
{{-- --- SCRIPT JAVASCRIPT (SAMA SEPERTI KODEMU) --- --}}
<script>
    // 1. Siapkan Data dari Laravel ke JavaScript
    const tengkulakData = @json($tengkulakList);

    const selectSampah = document.getElementById('pilih_sampah');
    const selectTengkulak = document.getElementById('pilih_tengkulak');
    const inputHarga = document.getElementById('harga_auto');
    const inputBerat = document.getElementById('berat_kg');
    const labelTotal = document.getElementById('total_preview');

    // 2. Logika saat Jenis Sampah dipilih
    selectSampah.addEventListener('change', function() {
        const sampahId = this.value;
        
        // Reset Dropdown Tengkulak
        selectTengkulak.innerHTML = '<option value="">-- Pilih Tengkulak --</option>';
        selectTengkulak.disabled = true;
        inputHarga.value = '';
        hitungTotal();

        if (sampahId) {
            // Filter Tengkulak yang menerima jenis sampah ini
            const filteredTengkulak = tengkulakData.filter(item => item.jenis_sampah_id == sampahId);

            if (filteredTengkulak.length > 0) {
                selectTengkulak.disabled = false;
                filteredTengkulak.forEach(t => {
                    // Tampilkan Nama + Harga di opsi dropdown
                    const option = document.createElement('option');
                    option.value = t.id;
                    option.dataset.harga = t.harga_beli; // Simpan harga di atribut data
                    option.textContent = `${t.nama_tengkulak} (Rp ${new Intl.NumberFormat('id-ID').format(t.harga_beli)})`;
                    selectTengkulak.appendChild(option);
                });
            } else {
                const option = document.createElement('option');
                option.textContent = "Tidak ada tengkulak untuk sampah ini";
                selectTengkulak.appendChild(option);
            }
        }
    });

    // 3. Logika saat Tengkulak dipilih -> Isi Harga Otomatis
    selectTengkulak.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const harga = selectedOption.dataset.harga; // Ambil dari atribut data-harga

        if (harga) {
            inputHarga.value = harga;
        } else {
            inputHarga.value = '';
        }
        hitungTotal();
    });

    // 4. Hitung Total Real-time saat berat diketik
    inputBerat.addEventListener('input', hitungTotal);

    function hitungTotal() {
        const berat = parseFloat(inputBerat.value) || 0;
        const harga = parseFloat(inputHarga.value) || 0;
        const total = berat * harga;
        
        // Format Rupiah
        labelTotal.textContent = "Rp " + new Intl.NumberFormat('id-ID').format(total);
    }
</script>
@endpush