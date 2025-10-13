@extends('layouts.app')

@section('title', 'Skrining Kesehatan Mental Universitas Negeri Malang')

@push('styles')
<link rel="stylesheet" href="/css/welcome.css">
@endpush

@section('content')
<!-- Hero Section -->
<section class="hero-section">
    <div class="hero-overlay"></div>
    <div class="hero-background"></div>
    
    <!-- Navigation -->
    <nav class="navbar-custom">
        <div class="container-fluid px-4 px-lg-5">
            <div class="d-flex justify-content-between align-items-center w-100">
                <div class="logo-container">
                    <img src="/images/um-logo.png" alt="UM Logo" class="um-logo">
                    {{-- <img src="/images/um-text-logo.png" alt="UM The Learning University" class="um-text-logo"> --}}
                </div>
                <div class="nav-links d-none d-md-flex">
                    {{-- <a href="#" class="nav-link-custom">Lorem Ipsum</a>
                    <a href="#" class="nav-link-custom">Lorem Ipsum</a>
                    <a href="#" class="nav-link-custom">Lorem Ipsum</a> --}}
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Content -->
    <div class="hero-content-wrapper">
        <div class="container-fluid px-4 px-lg-5">
            <div class="row align-items-center">
                <!-- Left Content - Mascot -->
                <div class="col-lg-3 col-xl-3">
                    <div class="mascot-container">
                        <img src="/images/mascot.png" alt="UM Mascot" class="mascot-image">
                    </div>
                </div>
                
                <!-- Right Content - Text and Button -->
                <div class="col-lg-9 col-xl-9">
                    <!-- Title -->
                    <div class="hero-text-content">
                        <h1 class="welcome-hero-title">
                            Sehat Mental<br>
                            Tumbuh Jiwa yang Kuat<br>
                            Wujudkan <span class="highlight-text">Masa Depan Nyata</span>
                        </h1>
                    </div>
                    
                    <!-- Subtitle and Button Row -->
                    <div class="row align-items-center">
                        <div class="col-lg-7">
                            <p class="hero-subtitle">
                                Skrining Kesehatan Mental Mahasiswa Baru<br>
                                Universitas Negeri Malang
                            </p>
                        </div>
                        <div class="col-lg-5">
                            <div class="hero-button-container">
                                <a href="{{ route('quiz.identity') }}" class="btn-start-screening">
                                    Mulai Skrining
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="features-section py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <!-- Feature Cards -->
                <div class="row g-4 mb-5">
                    <div class="col-md-6 col-lg-3">
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="bi bi-shield-check"></i>
                            </div>
                            <h6 class="feature-title">Rahasia & Aman</h6>
                            <p class="feature-desc">Data Anda dijamin kerahasiaan</p>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="bi bi-clock"></i>
                            </div>
                            <h6 class="feature-title">Cepat & Mudah</h6>
                            <p class="feature-desc">Hanya membutuhkan 10-15 menit</p>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="bi bi-graph-up"></i>
                            </div>
                            <h6 class="feature-title">Tim Um</h6>
                            <p class="feature-desc">dibuat oleh Tim Satgas Kesehatan Mental Universitas Negeri Malang</p>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="bi bi-people"></i>
                            </div>
                            <h6 class="feature-title">Dukungan Tersedia</h6>
                            <p class="feature-desc">Tim konseling siap membantu</p>
                        </div>
                    </div>
                </div>

                <!-- Instructions -->
                <div class="instructions-card mb-5">
                    <h6 class="instructions-title">
                        <i class="bi bi-info-circle me-2"></i>
                        Petunjuk Penting:
                    </h6>
                    <ul class="instructions-list">
                        <li>Jawab semua pertanyaan dengan <strong>jujur</strong> sesuai kondisi Anda</li>
                        <li>Tidak ada jawaban yang benar atau salah</li>
                        <li>Hasil ini hanya untuk <strong>skrining awal</strong>, bukan diagnosis</li>
                        <li>Jika hasil menunjukkan indikasi, konsultasi lanjutan tersedia</li>
                    </ul>
                </div>

                <!-- Statistics -->
                <div class="row text-center mb-5">
                    <div class="col-4">
                        <div class="stat-card">
                            <div class="stat-number">{{ number_format($faculties->count()) }}</div>
                            <div class="stat-label">Fakultas</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="stat-card">
                            <div class="stat-number">{{ number_format($faculties->sum(fn($f) => $f->departments->count())) }}</div>
                            <div class="stat-label">Jurusan</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="stat-card">
                            <div class="stat-number">15</div>
                            <div class="stat-label">Menit</div>
                        </div>
                    </div>
                </div>

                <!-- Info Cards -->
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="info-card">
                            <div class="info-icon">
                                <i class="bi bi-heart-pulse"></i>
                            </div>
                            <h5 class="info-title">Tim Satgas Kesehatan Mental UM</h5>
                            <p class="info-desc">
                                dibuat oleh Tim Satgas Kesehatan Mental Universitas Negeri Malang
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-card">
                            <div class="info-icon">
                                <i class="bi bi-telephone"></i>
                            </div>
                            <h5 class="info-title">Bantuan Tersedia</h5>
                            <p class="info-desc mb-3">
                                Tim konseling UM siap membantu 24/7. 
                                Jangan ragu untuk menghubungi jika membutuhkan dukungan.
                            </p>
                            <div class="hotline">Hotline: (0341) 551-312</div>
                        </div>
                    </div>
                </div>

                <!-- Disclaimer -->
                <div class="text-center mt-5">
                    <small class="disclaimer-text">
                        Dengan melanjutkan, Anda menyetujui bahwa data akan digunakan untuk keperluan akademik dan konseling
                    </small>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script src="/js/welcome.js"></script>
@endpush