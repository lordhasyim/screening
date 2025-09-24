@extends('layouts.app')

@section('title', 'Skrining Kesehatan Mental Universitas Negeri Malang')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card card-pastel text-center p-5 fade-in">
                <div class="card-body">
                    <!-- Hero Icon -->
                    <div class="hero-icon mb-4">
                        <i class="bi bi-clipboard-pulse"></i>
                    </div>

                    <!-- Title -->
                    <h1 class="hero-title display-5 fw-bold mb-3">Skrining Kesehatan Mental</h1>
                    <h5 class="text-muted mb-4">Program Universitas Negeri Malang</h5>
                    
                    <!-- Description -->
                    <div class="mb-5">
                        <p class="lead text-secondary mb-4">
                            Selamat datang di sistem skrining kesehatan mental untuk mahasiswa baru. 
                            Penilaian ini membantu mengidentifikasi tingkat kesehatan mental Anda secara dini.
                        </p>
                        
                        <div class="row text-start">
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-shield-check text-success me-3" style="font-size: 1.5rem;"></i>
                                    <div>
                                        <h6 class="mb-1">Rahasia & Aman</h6>
                                        <small class="text-muted">Data Anda dijamin kerahasiaan</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-clock text-info me-3" style="font-size: 1.5rem;"></i>
                                    <div>
                                        <h6 class="mb-1">Cepat & Mudah</h6>
                                        <small class="text-muted">Hanya membutuhkan 10-15 menit</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-graph-up text-warning me-3" style="font-size: 1.5rem;"></i>
                                    <div>
                                        <h6 class="mb-1">Hasil Instan</h6>
                                        <small class="text-muted">Langsung mendapat hasil skrining</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-people text-primary me-3" style="font-size: 1.5rem;"></i>
                                    <div>
                                        <h6 class="mb-1">Dukungan Tersedia</h6>
                                        <small class="text-muted">Tim konseling siap membantu</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Instructions -->
                    <div class="alert alert-pastel-info text-start mb-4">
                        <h6 class="mb-2">
                            <i class="bi bi-info-circle me-2"></i>
                            Petunjuk Penting:
                        </h6>
                        <ul class="mb-0 ps-3">
                            <li>Jawab semua pertanyaan dengan <strong>jujur</strong> sesuai kondisi Anda</li>
                            <li>Tidak ada jawaban yang benar atau salah</li>
                            <li>Hasil ini hanya untuk <strong>skrining awal</strong>, bukan diagnosis</li>
                            <li>Jika hasil menunjukkan indikasi, konsultasi lanjutan tersedia</li>
                        </ul>
                    </div>

                    <!-- Statistics -->
                    <div class="row text-center mb-4">
                        <div class="col-4">
                            <div class="fw-bold text-primary" style="font-size: 2rem;">{{ number_format($faculties->count()) }}</div>
                            <small class="text-muted">Fakultas</small>
                        </div>
                        <div class="col-4">
                            <div class="fw-bold text-success" style="font-size: 2rem;">{{ number_format($faculties->sum(fn($f) => $f->departments->count())) }}</div>
                            <small class="text-muted">Jurusan</small>
                        </div>
                        <div class="col-4">
                            <div class="fw-bold text-info" style="font-size: 2rem;">15</div>
                            <small class="text-muted">Menit</small>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-grid gap-3">
                        <a href="{{ route('quiz.identity') }}" class="btn btn-pastel-primary btn-lg">
                            <i class="bi bi-play-circle me-2"></i>
                            Mulai Skrining Sekarang
                        </a>
                        
                        <div class="text-center">
                            <small class="text-muted">
                                Dengan melanjutkan, Anda menyetujui bahwa data akan digunakan untuk keperluan akademik dan konseling
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Info Cards -->
            <div class="row mt-5">
                <div class="col-md-6 mb-4">
                    <div class="card card-pastel h-100">
                        <div class="card-body text-center">
                            <div class="mb-3" style="font-size: 2.5rem; color: var(--healing-green);">
                                <i class="bi bi-heart-pulse"></i>
                            </div>
                            <h5>Kesehatan Mental Penting</h5>
                            <p class="text-muted small">
                                Kesehatan mental sama pentingnya dengan kesehatan fisik. 
                                Deteksi dini membantu pencegahan dan penanganan yang tepat.
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 mb-4">
                    <div class="card card-pastel h-100">
                        <div class="card-body text-center">
                            <div class="mb-3" style="font-size: 2.5rem; color: var(--calm-blue);">
                                <i class="bi bi-telephone"></i>
                            </div>
                            <h5>Bantuan Tersedia</h5>
                            <p class="text-muted small mb-3">
                                Tim konseling UM siap membantu 24/7. 
                                Jangan ragu untuk menghubungi jika membutuhkan dukungan.
                            </p>
                            <small class="fw-bold text-primary">Hotline: (0341) 551-312</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Add entrance animation delay for cards
    $('.card-pastel').each(function(index) {
        $(this).css('animation-delay', (index * 0.2) + 's');
    });
    
    // Track start button clicks
    $('.btn-pastel-primary').on('click', function() {
        trackUserInteraction('quiz_start_clicked');
    });
});
</script>
@endpush
@endsection