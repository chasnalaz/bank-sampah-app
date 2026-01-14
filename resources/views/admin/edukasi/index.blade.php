@extends('layouts.main')
@section('title', 'Kelola Konten Edukasi')

@section('content')
<div class="container-fluid">
    <div class="row">
        
        {{-- FORM TAMBAH KONTEN --}}
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header py-3 bg-primary text-white">
                    <h6 class="m-0 fw-bold">Tambah Konten Baru</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.edukasi.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label>Judul</label>
                            <input type="text" name="judul" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Kategori</label>
                            <select name="kategori" class="form-select" id="kategoriSelect">
                                <option value="poster">Poster / Gambar</option>
                                <option value="video">Video Youtube</option>
                            </select>
                        </div>

                        {{-- Input Gambar (Muncul jika Poster) --}}
                        <div class="mb-3" id="inputGambar">
                            <label>Upload Gambar</label>
                            <input type="file" name="gambar" class="form-control">
                        </div>

                        {{-- Input Youtube (Muncul jika Video) --}}
                        <div class="mb-3 d-none" id="inputVideo">
                            <label>Link Youtube</label>
                            <input type="url" name="link_url" class="form-control" placeholder="https://youtube.com/...">
                        </div>

                        <div class="mb-3">
                            <label>Deskripsi Singkat</label>
                            <textarea name="deskripsi" class="form-control" rows="3"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Terbitkan</button>
                    </form>
                </div>
            </div>
        </div>

        {{-- LIST KONTEN --}}
        <div class="col-md-8">
            <div class="card">
                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold text-primary text-center">Daftar Konten Edukasi</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="text-center">
                                <tr>
                                    <th>Judul</th>
                                    <th>Preview</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($edukasiList as $item)
                                <tr class="text-center">
                                    <td>
                                        <strong>{{ $item->judul }}</strong><br>
                                        <small class="text-muted">{{ ucfirst($item->kategori) }}</small>
                                    </td>
                                    <td>
                                        @if($item->kategori == 'poster' && $item->gambar)
                                            <img src="{{ asset('storage/' . $item->gambar) }}" width="100" class="rounded">
                                        @elseif($item->kategori == 'video')
                                            <a href="{{ $item->link_url }}" target="_blank" class="btn btn-sm btn-danger"><i class="bi bi-youtube"></i> Lihat Video</a>
                                        @endif
                                    </td>
                                    <td>
                                        <form action="{{ route('admin.edukasi.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus konten ini?')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Script sederhana untuk ganti input Video/Gambar
    document.getElementById('kategoriSelect').addEventListener('change', function() {
        if(this.value == 'video') {
            document.getElementById('inputVideo').classList.remove('d-none');
            document.getElementById('inputGambar').classList.add('d-none');
        } else {
            document.getElementById('inputVideo').classList.add('d-none');
            document.getElementById('inputGambar').classList.remove('d-none');
        }
    });
</script>
@endsection