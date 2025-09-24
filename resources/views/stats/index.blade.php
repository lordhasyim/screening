@extends('layouts.app')

@section('title', 'Statistik Kesehatan Mental -  UM')

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="text-center mb-5 fade-in">
        <div class="mb-3" style="font-size: 4rem; color: var(--compassion-purple);">
            <i class="bi bi-graph-up"></i>
        </div>
        <h1 class="hero-title display-4 mb-3">Statistik Kesehatan Mental</h1>
        <p class="lead text-muted mb-4">
            Data agregat skrining kesehatan mental mahasiswa Universitas Negeri Malang
        </p>
        <div class="small text-muted">
            Data diperbarui: {{ now()->format('d F Y') }} | 
            Total Partisipan: {{ number_format($totalResponses) }} mahasiswa
        </div>
    </div>

    <!-- Key Statistics Cards -->
    <div class="row mb-5">
        <div class="col-md-3 mb-4">
            <div class="card card-pastel text-center slide-up">
                <div class="card-body">
                    <div class="mb-2" style="font-size: 2.5rem; color: var(--healing-green);">
                        <i class="bi bi-people"></i>
                    </div>
                    <h3 class="fw-bold text-primary">{{ number_format($totalResponses) }}</h3>
                    <small class="text-muted">Total Partisipan</small>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-4">
            <div class="card card-pastel text-center slide-up" style="animation-delay: 0.1s;">
                <div class="card-body">
                    <div class="mb-2" style="font-size: 2.5rem; color: var(--calm-blue);">
                        <i class="bi bi-building"></i>
                    </div>
                    <h3 class="fw-bold text-primary">{{ $facultyCount }}</h3>
                    <small class="text-muted">Fakultas Terwakili</small>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-4">
            <div class="card card-pastel text-center slide-up" style="animation-delay: 0.2s;">
                <div class="card-body">
                    <div class="mb-2" style="font-size: 2.5rem; color: var(--hopeful-yellow);">
                        <i class="bi bi-calendar-range"></i>
                    </div>
                    <h3 class="fw-bold text-primary">{{ $monthsActive }}</h3>
                    <small class="text-muted">Bulan Aktif</small>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-4">
            <div class="card card-pastel text-center slide-up" style="animation-delay: 0.3s;">
                <div class="card-body">
                    <div class="mb-2" style="font-size: 2.5rem; color: var(--gentle-pink);">
                        <i class="bi bi-heart-pulse"></i>
                    </div>
                    <h3 class="fw-bold text-primary">{{ number_format($completionRate, 1) }}%</h3>
                    <small class="text-muted">Tingkat Penyelesaian</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row 1 -->
    <div class="row mb-5">
        <!-- PHQ-9 Distribution -->
        <div class="col-lg-6 mb-4">
            <div class="card card-pastel slide-up" style="animation-delay: 0.4s;">
                <div class="card-body">
                    <h5 class="text-primary mb-3">
                        <i class="bi bi-pie-chart me-2"></i>
                        Distribusi Hasil PHQ-9
                    </h5>
                    <div style="height: 300px;">
                        <canvas id="phq9Chart"></canvas>
                    </div>
                    <div class="text-center mt-3">
                        <small class="text-muted">
                            Skrining depresi berdasarkan {{ number_format($totalResponses) }} responden
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- DASS-21 Distribution -->
        <div class="col-lg-6 mb-4">
            <div class="card card-pastel slide-up" style="animation-delay: 0.5s;">
                <div class="card-body">
                    <h5 class="text-primary mb-3">
                        <i class="bi bi-pie-chart me-2"></i>
                        Distribusi Hasil DASS-21
                    </h5>
                    <div style="height: 300px;">
                        <canvas id="dass21Chart"></canvas>
                    </div>
                    <div class="text-center mt-3">
                        <small class="text-muted">
                            Skrining lanjutan berdasarkan {{ number_format($dass21Responses) }} responden
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row 2 -->
    <div class="row mb-5">
        <!-- Faculty Distribution -->
        <div class="col-lg-8 mb-4">
            <div class="card card-pastel slide-up" style="animation-delay: 0.6s;">
                <div class="card-body">
                    <h5 class="text-primary mb-3">
                        <i class="bi bi-bar-chart me-2"></i>
                        Partisipasi per Fakultas
                    </h5>
                    <div style="height: 400px;">
                        <canvas id="facultyChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Monthly Trends -->
        <div class="col-lg-4 mb-4">
            <div class="card card-pastel slide-up" style="animation-delay: 0.7s;">
                <div class="card-body">
                    <h5 class="text-primary mb-3">
                        <i class="bi bi-graph-up me-2"></i>
                        Tren Bulanan
                    </h5>
                    <div style="height: 400px;">
                        <canvas id="monthlyChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Risk Level Overview -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="card card-pastel slide-up" style="animation-delay: 0.8s;">
                <div class="card-body">
                    <h5 class="text-primary mb-4">
                        <i class="bi bi-shield-check me-2"></i>
                        Gambaran Tingkat Risiko Keseluruhan
                    </h5>
                    
                    <div class="row">
                        @foreach($riskDistribution as $risk => $data)
                            <div class="col-md-3 mb-3">
                                <div class="text-center p-3 rounded-3" 
                                     style="background: {{ $data['background'] }}; border: 2px solid {{ $data['border'] }};">
                                    <div class="fw-bold" style="font-size: 2rem; color: {{ $data['color'] }};">
                                        {{ number_format($data['percentage'], 1) }}%
                                    </div>
                                    <div class="fw-semibold mb-1">{{ $risk }}</div>
                                    <small class="text-muted">{{ number_format($data['count']) }} mahasiswa</small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <div class="mt-4 p-3 bg-light rounded">
                        <h6 class="text-primary mb-2">Interpretasi Data:</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <ul class="mb-0 small">
                                    <li><strong>Low Risk:</strong> Kondisi kesehatan mental baik</li>
                                    <li><strong>Moderate Risk:</strong> Perlu perhatian dan monitoring</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul class="mb-0 small">
                                    <li><strong>High Risk:</strong> Memerlukan konsultasi profesional</li>
                                    <li><strong>Critical Risk:</strong> Perlu intervensi segera</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Insights -->
    <div class="row mb-5">
        <div class="col-md-4 mb-4">
            <div class="card card-pastel slide-up" style="animation-delay: 0.9s;">
                <div class="card-body text-center">
                    <div class="mb-2" style="font-size: 2.5rem; color: var(--healing-green);">
                        <i class="bi bi-check-circle"></i>
                    </div>
                    <h4 class="fw-bold text-success">{{ number_format($positiveOutcomes, 1) }}%</h4>
                    <p class="mb-0 small text-muted">
                        Mahasiswa dengan hasil skrining positif (low risk)
                    </p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card card-pastel slide-up" style="animation-delay: 1.0s;">
                <div class="card-body text-center">
                    <div class="mb-2" style="font-size: 2.5rem; color: var(--compassion-purple);">
                        <i class="bi bi-people"></i>
                    </div>
                    <h4 class="fw-bold text-primary">{{ number_format($avgAge, 1) }}</h4>
                    <p class="mb-0 small text-muted">
                        Rata-rata usia partisipan
                    </p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card card-pastel slide-up" style="animation-delay: 1.1s;">
                <div class="card-body text-center">
                    <div class="mb-2" style="font-size: 2.5rem; color: var(--calm-blue);">
                        <i class="bi bi-clock"></i>
                    </div>
                    <h4 class="fw-bold text-info">{{ $avgCompletionTime }}</h4>
                    <p class="mb-0 small text-muted">
                        Rata-rata waktu penyelesaian
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Important Notes -->
    <div class="card card-pastel mb-5">
        <div class="card-body">
            <h5 class="text-primary mb-3">
                <i class="bi bi-info-circle me-2"></i>
                Catatan Penting
            </h5>
            <div class="row">
                <div class="col-md-6">
                    <h6>Tentang Data:</h6>
                    <ul class="small text-muted">
                        <li>Data yang ditampilkan bersifat agregat dan anonim</li>
                        <li>Identitas individu tidak dapat diidentifikasi</li>
                        <li>Statistik diperbarui secara berkala</li>
                        <li>Data mencakup periode {{ $dataRange }}</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h6>Interpretasi Hasil:</h6>
                    <ul class="small text-muted">
                        <li>Hasil ini adalah skrining awal, bukan diagnosis medis</li>
                        <li>Data membantu memahami tren kesehatan mental kampus</li>
                        <li>Mendukung pengembangan program kesehatan mental</li>
                        <li>Konsultasi profesional tetap disarankan jika diperlukan</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Call to Action -->
    <div class="text-center mb-5">
        <div class="card card-gradient text-white">
            <div class="card-body py-4">
                <h4 class="mb-3">Kesehatan Mental Adalah Prioritas</h4>
                <p class="mb-4 opacity-90">
                    Jika Anda merasa membutuhkan dukungan, jangan ragu untuk mengikuti skrining 
                    atau menghubungi layanan konseling kampus.
                </p>
                <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                    <a href="{{ route('quiz.index') }}" class="btn btn-light btn-lg me-md-2">
                        <i class="bi bi-clipboard-pulse me-2"></i>
                        Ikuti Skrining
                    </a>
                    <a href="tel:0341551312" class="btn btn-outline-light btn-lg">
                        <i class="bi bi-telephone me-2"></i>
                        Hubungi Konseling
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    // Initialize all charts
    initializeCharts();
});

function initializeCharts() {
    // PHQ-9 Distribution Chart
    const phq9Ctx = document.getElementById('phq9Chart').getContext('2d');
    new Chart(phq9Ctx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode(array_keys($phq9Distribution)) !!},
            datasets: [{
                data: {!! json_encode(array_values($phq9Distribution)) !!},
                backgroundColor: [
                    '#4CAF50', // Sangat rendah - Healing Green
                    '#2196F3', // Rendah - Calm Blue  
                    '#FF9800', // Sedang - Hopeful Yellow
                    '#F44336', // Tinggi - Warning Red
                    '#9C27B0'  // Sangat tinggi - Compassion Purple
                ],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((context.parsed / total) * 100).toFixed(1);
                            return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });

    // DASS-21 Distribution Chart
    const dass21Ctx = document.getElementById('dass21Chart').getContext('2d');
    new Chart(dass21Ctx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode(array_keys($dass21Distribution)) !!},
            datasets: [{
                data: {!! json_encode(array_values($dass21Distribution)) !!},
                backgroundColor: [
                    '#4CAF50', // Sangat rendah
                    '#2196F3', // Rendah
                    '#FF9800', // Sedang
                    '#F44336', // Tinggi
                    '#9C27B0'  // Sangat tinggi
                ],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true
                    }
                }
            }
        }
    });

    // Faculty Distribution Chart
    const facultyCtx = document.getElementById('facultyChart').getContext('2d');
    new Chart(facultyCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode(array_keys($facultyDistribution)) !!},
            datasets: [{
                label: 'Jumlah Partisipan',
                data: {!! json_encode(array_values($facultyDistribution)) !!},
                backgroundColor: 'rgba(63, 81, 181, 0.8)',
                borderColor: 'rgba(63, 81, 181, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                },
                x: {
                    ticks: {
                        maxRotation: 45
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });

    // Monthly Trends Chart
    const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
    new Chart(monthlyCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode(array_keys($monthlyTrends)) !!},
            datasets: [{
                label: 'Partisipan per Bulan',
                data: {!! json_encode(array_values($monthlyTrends)) !!},
                borderColor: 'rgba(156, 39, 176, 1)',
                backgroundColor: 'rgba(156, 39, 176, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
}
</script>
@endpush
@endsection