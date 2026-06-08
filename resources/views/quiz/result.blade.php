@extends('layouts.app')

@section('title', 'Hasil Skrining - Kesehatan Mental ')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">

        
            
            <!-- Completion Header -->
            <div class="text-center mb-5 fade-in">
                <div class="mb-3" style="font-size: 4rem; color: var(--healing-green);">
                    <i class="bi bi-check-circle"></i>
                </div>
                <h2 class="hero-title mb-2">Skrining Selesai</h2>
                <p class="lead text-muted">Terima kasih telah menyelesaikan penilaian kesehatan mental</p>
                <small class="text-muted">
                    Diselesaikan pada {{ $quizResponse->completed_at->format('d F Y, H:i') }} WIB
                </small>
                <a href="/">Kembali Ke halaman utama</a>
            </div>

            <!-- Personal Summary Card -->
            {{-- <div class="card card-pastel mb-4 slide-up">
                <div class="card-body">
                    <h5 class="text-primary mb-3">
                        <i class="bi bi-person-badge me-2"></i>
                        Ringkasan Identitas
                    </h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-2">
                                <strong>Nama:</strong> {{ $quizResponse->full_name }}
                            </div>
                            <div class="mb-2">
                                <strong>NIM:</strong> {{ $quizResponse->nim }}
                            </div>
                            <div class="mb-2">
                                <strong>Fakultas:</strong> {{ $quizResponse->faculty->name }}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-2">
                                <strong>Jurusan:</strong> {{ $quizResponse->department->name }}
                            </div>
                            <div class="mb-2">
                                <strong>Angkatan:</strong> {{ $quizResponse->student_year }}
                            </div>
                            <div class="mb-2">
                                <strong>Usia:</strong> {{ $quizResponse->age ?? '-' }} tahun
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Results Cards -->
            <div class="row">
                <!-- PHQ-9 Results -->
                <div class="col-md-6 mb-4">
                    <div class="result-card slide-up" style="animation-delay: 0.2s;">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">
                                <i class="bi bi-clipboard-pulse me-2 text-primary"></i>
                                PHQ-9 (Tahap I)
                            </h5>
                            <span class="score-badge badge-{{ str_replace(' ', '-', strtolower($quizResponse->phq9_category)) }}">
                                {{ $quizResponse->phq9_category }}
                            </span>
                        </div>
                        
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <small class="text-muted">Skor Total</small>
                                <small class="fw-bold">{{ $quizResponse->phq9_total_score }} / 36</small>
                            </div>
                            <div class="progress progress-pastel">
                                <div class="progress-bar progress-bar-pastel"
                                     style="width: {{ ($quizResponse->phq9_total_score / 36) * 100 }}%;"></div>
                            </div>
                        </div>
                        
                        <div class="small text-muted">
                            {{ getPhq9InterpretationText($quizResponse->phq9_category) }}
                        </div>
                    </div>
                </div>

                <!-- DASS-21 Results (if available) -->
                @if($quizResponse->dass21_responses)
                <div class="col-md-6 mb-4">
                    <div class="result-card slide-up" style="animation-delay: 0.4s;">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">
                                <i class="bi bi-clipboard2-pulse me-2 text-primary"></i>
                                DASS-21 (Tahap II)
                            </h5>
                            <span class="score-badge badge-{{ str_replace(' ', '-', strtolower($quizResponse->dass21_category)) }}">
                                {{ $quizResponse->dass21_category }}
                            </span>
                        </div>
                        
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <small class="text-muted">Skor Total</small>
                                <small class="fw-bold">{{ $quizResponse->dass21_total_score }} / 120</small>
                            </div>
                            <div class="progress progress-pastel">
                                <div class="progress-bar progress-bar-pastel"
                                     style="width: {{ ($quizResponse->dass21_total_score / 120) * 100 }}%;"></div>
                            </div>
                        </div>
                        
                        <div class="small text-muted">
                            {{ getDass21InterpretationText($quizResponse->dass21_category) }}
                        </div>
                    </div>
                </div>
                @else
                <div class="col-md-6 mb-4">
                    <div class="result-card slide-up" style="animation-delay: 0.4s;">
                        <div class="text-center py-4">
                            <div class="mb-3" style="font-size: 2.5rem; color: var(--healing-green);">
                                <i class="bi bi-check-circle"></i>
                            </div>
                            <h6 class="text-success mb-2">Tahap II Tidak Diperlukan</h6>
                            <p class="small text-muted mb-0">
                                Berdasarkan hasil PHQ-9, Anda tidak perlu melanjutkan ke penilaian tahap kedua.
                                Ini menunjukkan kondisi kesehatan mental yang relatif baik.
                            </p>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Overall Risk Assessment -->
            <div class="card card-pastel mb-4 slide-up" style="animation-delay: 0.6s;">
                <div class="card-body">
                    <h5 class="text-primary mb-3">
                        <i class="bi bi-shield-check me-2"></i>
                        Penilaian Keseluruhan
                    </h5>
                    
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="mb-2">
                                <strong>Tingkat Risiko:</strong> 
                                <span class="badge bg-{{ getRiskBadgeColor($quizResponse->overall_risk_level) }} ms-2">
                                    {{ $quizResponse->overall_risk_level }}
                                </span>
                            </div>
                            <p class="mb-0 text-muted">
                                {{ getOverallRecommendation($quizResponse->overall_risk_level) }}
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            @if(in_array($quizResponse->overall_risk_level, ['High', 'Critical']))
                                <div class="alert alert-warning mb-0 text-center">
                                    <i class="bi bi-exclamation-triangle"></i>
                                    <div class="small fw-bold">Perlu Tindak Lanjut</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recommendations Based on Risk Level -->
            @if($quizResponse->overall_risk_level === 'Critical' || $quizResponse->overall_risk_level === 'High')
                <!-- High/Critical Risk Recommendations -->
                <div class="card card-pastel border-warning mb-4 slide-up" style="animation-delay: 0.8s;">
                    <div class="card-header bg-warning bg-opacity-10">
                        <h5 class="mb-0">
                            <i class="bi bi-exclamation-triangle text-warning me-2"></i>
                            Rekomendasi Penting
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <h6 class="text-warning mb-3">Langkah yang Disarankan:</h6>
                                <ul class="mb-3">
                                    <li>Segera hubungi layanan konseling UM untuk konsultasi lanjutan</li>
                                    <li>Pertimbangkan untuk berkonsultasi dengan psikolog atau psikiater</li>
                                    <li>Jangan ragu untuk mencari dukungan dari keluarga atau teman terdekat</li>
                                    <li>Jika merasa dalam krisis, segera hubungi hotline darurat</li>
                                </ul>
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle me-2"></i>
                                    <small>
                                        <strong>Catatan:</strong> Hasil ini adalah skrining awal, bukan diagnosis medis. 
                                        Konsultasi dengan profesional kesehatan mental sangat disarankan.
                                    </small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="bg-light rounded p-3">
                                    <h6 class="text-primary mb-2">Kontak Darurat</h6>
                                    <div class="mb-2">
                                        <strong>Hotline Crisis:</strong><br>
                                        <span class="text-danger">119 ext. 8</span><br>
                                        <small class="text-muted">24 jam</small>
                                    </div>
                                    <div class="mb-2">
                                        <strong>Konseling UM:</strong><br>
                                        (0341) 551-312<br>
                                        <small class="text-muted">konseling@um.ac.id</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif($quizResponse->overall_risk_level === 'Moderate')
                <!-- Moderate Risk Recommendations -->
                <div class="card card-pastel border-info mb-4 slide-up" style="animation-delay: 0.8s;">
                    <div class="card-header bg-info bg-opacity-10">
                        <h5 class="mb-0">
                            <i class="bi bi-lightbulb text-info me-2"></i>
                            Saran untuk Anda
                        </h5>
                    </div>
                    <div class="card-body">
                        <h6 class="text-info mb-3">Langkah Preventif yang Disarankan:</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <ul class="mb-3">
                                    <li>Pertimbangkan konsultasi dengan konselor kampus</li>
                                    <li>Jaga pola hidup sehat dan tidur yang cukup</li>
                                    <li>Lakukan aktivitas yang menyenangkan secara rutin</li>
                                    <li>Bangun sistem dukungan sosial yang kuat</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul class="mb-3">
                                    <li>Praktikkan teknik relaksasi atau meditasi</li>
                                    <li>Olahraga teratur untuk menjaga kesehatan mental</li>
                                    <li>Hindari penggunaan alkohol atau zat berbahaya</li>
                                    <li>Monitor perubahan mood atau gejala</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <!-- Low Risk - Positive Reinforcement -->
                <div class="card card-success mb-4 slide-up" style="animation-delay: 0.8s;">
                    <div class="card-body text-center text-white">
                        <div class="mb-3" style="font-size: 3rem;">
                            <i class="bi bi-heart"></i>
                        </div>
                        <h5 class="mb-2">Kesehatan Mental Anda Baik!</h5>
                        <p class="mb-3 opacity-90">
                            Hasil skrining menunjukkan kondisi kesehatan mental yang positif. 
                            Tetap jaga kesehatan mental Anda dengan gaya hidup sehat dan dukungan sosial yang baik.
                        </p>
                        <div class="small opacity-75">
                            Jika di kemudian hari mengalami perubahan, jangan ragu untuk mencari bantuan profesional.
                        </div>
                    </div>
                </div>
            @endif

            <!-- Resources and Support -->
            <div class="card card-pastel mb-4 slide-up" style="animation-delay: 1.0s;">
                <div class="card-body">
                    <h5 class="text-primary mb-3">
                        <i class="bi bi-bookmark-heart me-2"></i>
                        Sumber Daya dan Dukungan
                    </h5>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="text-center p-3">
                                <div class="mb-2" style="font-size: 2rem; color: var(--compassion-purple);">
                                    <i class="bi bi-people"></i>
                                </div>
                                <h6>Konseling Kampus</h6>
                                <p class="small text-muted mb-2">
                                    Layanan konseling gratis untuk mahasiswa UM
                                </p>
                                <div class="small">
                                    <strong>(0341) 551-312</strong><br>
                                    konseling@um.ac.id
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="text-center p-3">
                                <div class="mb-2" style="font-size: 2rem; color: var(--healing-green);">
                                    <i class="bi bi-heart-pulse"></i>
                                </div>
                                <h6>Aktivitas Kesehatan Mental</h6>
                                <p class="small text-muted mb-2">
                                    Workshop, seminar, dan kegiatan wellbeing
                                </p>
                                <div class="small">
                                    Cek agenda di website UM
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="text-center p-3">
                                <div class="mb-2" style="font-size: 2rem; color: var(--calm-blue);">
                                    <i class="bi bi-book"></i>
                                </div>
                                <h6>Sumber Pembelajaran</h6>
                                <p class="small text-muted mb-2">
                                    Materi edukasi kesehatan mental
                                </p>
                                <div class="small">
                                    Library.um.ac.id/mental-health
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="text-center mb-5 slide-up" style="animation-delay: 1.2s;">
                <div class="d-grid gap-3 d-md-flex justify-content-md-center">
                    @if(in_array($quizResponse->overall_risk_level, ['High', 'Critical']))
                        <a href="tel:0341551312" class="btn btn-warning btn-lg">
                            <i class="bi bi-telephone me-2"></i>
                            Hubungi Konseling Sekarang
                        </a>
                    @endif
                    
                    <button onclick="window.print()" class="btn btn-pastel-secondary btn-lg">
                        <i class="bi bi-printer me-2"></i>
                        Cetak Hasil
                    </button>
                    
                    <a href="{{ route('quiz.index') }}" class="btn btn-pastel-primary btn-lg">
                        <i class="bi bi-arrow-clockwise me-2"></i>
                        Skrining Lagi
                    </a>
                </div>
            </div> --}}

            <!-- Privacy Note -->
            <div class="text-center">
                <small class="text-muted">
                    <i class="bi bi-shield-lock me-1"></i>
                    Data Anda aman dan hanya digunakan untuk keperluan akademik serta penyediaan layanan konseling yang sesuai.
                </small>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Track result viewing
    trackUserInteraction('result_viewed', {
        risk_level: '{{ $quizResponse->overall_risk_level }}',
        phq9_category: '{{ $quizResponse->phq9_category }}',
        dass21_category: '{{ $quizResponse->dass21_category ?? 'N/A' }}'
    });
    
    // Clear any backup data
    localStorage.removeItem('phq9_answers_backup');
    localStorage.removeItem('dass21_answers_backup');
    localStorage.removeItem('quiz_backup_identityForm');
    
    // Add celebration animation for low risk
    @if($quizResponse->overall_risk_level === 'Low')
        setTimeout(function() {
            createCelebrationEffect();
        }, 1000);
    @endif
});

function createCelebrationEffect() {
    // Simple confetti-like animation for positive results
    for(let i = 0; i < 30; i++) {
        setTimeout(function() {
            const confetti = $('<div>').css({
                position: 'fixed',
                top: '10%',
                left: Math.random() * 100 + '%',
                width: '10px',
                height: '10px',
                background: ['var(--healing-green)', 'var(--compassion-purple)', 'var(--hopeful-yellow)'][Math.floor(Math.random() * 3)],
                borderRadius: '50%',
                pointerEvents: 'none',
                zIndex: 1000
            });
            
            $('body').append(confetti);
            
            confetti.animate({
                top: '100%',
                opacity: 0
            }, 2000, function() {
                confetti.remove();
            });
        }, i * 100);
    }
}
</script>
@endpush
@endsection