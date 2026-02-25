@extends('layouts.main')

@section('title', 'Pojok Edukasi')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
        <div>
            <h4 class="fw-bold mb-0 text-dark">Pojok Edukasi</h4>
            <small class="text-muted">Pelajari cara mengelola sampah dengan benar</small>
        </div>
        <div class="bg-warning bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" 
             style="width: 45px; height: 45px;">
            <i class="bi bi-lightbulb text-warning fs-4"></i>
        </div>
    </div>

    <div class="row g-4">
        @forelse($semuaEdukasi as $item)
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                {{-- MEDIA DISPLAY --}}
                <div class="card-img-top overflow-hidden bg-light border-bottom position-relative" style="height: 200px;">
                    @if($item->kategori == 'video' && $item->youtube_id)
                        <div class="ratio ratio-16x9 h-100">
                            <iframe src="https://www.youtube.com/embed/{{ $item->youtube_id }}" allowfullscreen></iframe>
                        </div>
                    @elseif($item->kategori == 'poster' && $item->gambar)
                        <img src="{{ asset('storage/' . $item->gambar) }}" 
                             class="w-100 h-100 object-fit-contain" 
                             alt="{{ $item->judul }}">
                    @else
                        <div class="d-flex align-items-center justify-content-center h-100 text-muted">
                            <div class="text-center">
                                <i class="bi bi-book fs-1"></i>
                                <p class="small mb-0 mt-2">Artikel Bacaan</p>
                            </div>
                        </div>
                    @endif
                    
                    {{-- Badge Kategori di Pojok --}}
                    <div class="position-absolute top-0 end-0 m-2">
                        @if($item->kategori == 'video')
                            <span class="badge bg-danger shadow-sm"><i class="bi bi-play-btn me-1"></i> Video</span>
                        @elseif($item->kategori == 'poster')
                            <span class="badge bg-info shadow-sm"><i class="bi bi-image me-1"></i> Poster</span>
                        @else
                            <span class="badge bg-secondary shadow-sm"><i class="bi bi-card-text me-1"></i> Artikel</span>
                        @endif
                    </div>
                </div>

                <div class="card-body d-flex flex-column">
                    <h5 class="card-title fw-bold text-dark mb-2">{{ $item->judul }}</h5>
                    <p class="card-text text-muted small flex-grow-1" style="line-height: 1.6;">
                        {{ Str::limit($item->deskripsi, 120) }}
                    </p>
                    <div class="mt-3 pt-3 border-top d-flex justify-content-between align-items-center">
                        <small class="text-muted fst-italic">
                            <i class="bi bi-calendar3 me-1"></i> {{ $item->created_at->format('d M Y') }}
                        </small>
                        {{-- Kalau artikel panjang, bisa tambah tombol baca selengkapnya (opsional) --}}
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <div class="text-muted">
                <i class="bi bi-journal-x fs-1 d-block mb-3"></i>
                <h5>Belum ada konten edukasi.</h5>
                <p>Silakan cek kembali nanti.</p>
            </div>
        </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div class="d-flex justify-content-center mt-5">
        {{ $semuaEdukasi->links('pagination::bootstrap-5') }}
    </div>
@endsection