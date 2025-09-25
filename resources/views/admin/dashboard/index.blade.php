@extends('layouts.admin')

@section('title', 'Dashboard - Skrining Kesehatan Mental UM')

@section('content')

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Dashboard Overview</h1>
    <div class="d-flex gap-2">
        <button class="btn btn-success btn-sm" onclick="exportDashboardData()">
            <i class="fas fa-download fa-sm text-white-50"></i> Export Data
        </button>
        <button class="btn btn-primary btn-sm" onclick="refreshDashboard()">
            <i class="fas fa-sync fa-sm text-white-50"></i> Refresh
        </button>
    </div>
</div>

<!-- Alert Container -->
<div id="alertContainer">
    @foreach($alerts as $alert)
        <div class="alert alert-admin-{{ $alert['type'] }} alert-dismissible fade show" role="alert">
            <i class="{{ $alert['icon'] }} me-2"></i>
            <strong>{{ $alert['title'] }}:</strong> {{ $alert['message'] }}
            @if($alert['action_url'])
                <a href="{{ $alert['action_url'] }}" class="alert-link ms-2">{{ $alert['action_text'] }}</a>
            @endif
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endforeach
</div>

<!-- Content Row - Main Statistics -->
<div class="row">

    <!-- Total Responses Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2 stats-card">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Responden
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalResponses">
                            {{ number_format($stats['total_responses']) }}
                        </div>
                        <div class="small text-muted">
                            Selesai: {{ number_format($stats['completed_responses']) }} ({{ $stats['completion_rate'] }}%)
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="stats-icon bg-primary">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- High Risk Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-danger shadow h-100 py-2 stats-card">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                            Risiko Tinggi
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="highRiskCount">
                            {{ number_format($stats['high_risk_count']) }}
                        </div>
                        <div class="small text-muted">
                            Perlu tindak lanjut segera
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="stats-icon bg-danger">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Today's Responses -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2 stats-card">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Respons Hari Ini
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ number_format($stats['today_responses']) }}
                        </div>
                        <div class="small text-muted">
                            Minggu ini: {{ number_format($stats['week_responses']) }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="stats-icon bg-success">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- DASS-21 Completion Rate -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2 stats-card">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Tingkat Lanjutan</div>
                        <div class="row no-gutters align-items-center">
                            <div class="col-auto">
                                <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">{{ $stats['dass21_percentage'] }}%</div>
                            </div>
                            <div class="col">
                                <div class="progress progress-sm mr-2">
                                    <div class="progress-bar bg-info" role="progressbar"
                                         style="width: {{ $stats['dass21_percentage'] }}%"></div>
                                </div>
                            </div>
                        </div>
                        <div class="small text-muted">
                            {{ number_format($stats['dass21_completed']) }} dari {{ number_format($stats['phq9_completed']) }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="stats-icon bg-info">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Content Row - Charts -->
<div class="row">

    <!-- PHQ-9 Distribution Chart -->
    <div class="col-xl-4 col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Distribusi PHQ-9</h6>
                <div class="dropdown no-arrow">
                    <a class="dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                        <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in">
                        <a class="dropdown-item" href="{{ route('admin.analytics') }}">Lihat Detail</a>
                        <a class="dropdown-item" href="#" onclick="exportChart('phq9Chart')">Export Chart</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-pie-container">
                    <canvas id="phq9Chart"></canvas>
                </div>
                <div class="mt-4 text-center small">
                    <span class="mr-2">
                        <i class="fas fa-circle text-success"></i> Sangat Rendah
                    </span>
                    <span class="mr-2">
                        <i class="fas fa-circle text-info"></i> Rendah
                    </span>
                    <span class="mr-2">
                        <i class="fas fa-circle text-warning"></i> Sedang
                    </span>
                    <span class="mr-2">
                        <i class="fas fa-circle text-danger"></i> Tinggi
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Risk Level Distribution -->
    <div class="col-xl-4 col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Distribusi Tingkat Risiko</h6>
            </div>
            <div class="card-body">
                <div class="chart-pie-container">
                    <canvas id="riskChart"></canvas>
                </div>
                <div class="mt-4 text-center small">
                    <span class="mr-2">
                        <i class="fas fa-circle text-success"></i> Low ({{ $stats['low_risk_count'] }})
                    </span>
                    <span class="mr-2">
                        <i class="fas fa-circle text-info"></i> Moderate ({{ $stats['moderate_risk_count'] }})
                    </span>
                    <span class="mr-2">
                        <i class="fas fa-circle text-danger"></i> High ({{ $stats['high_risk_count'] }})
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Trends -->
    <div class="col-xl-4 col-lg-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Tren 6 Bulan Terakhir</h6>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="monthlyTrendsChart"></canvas>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Content Row - Tables and Activity -->
<div class="row">

    <!-- Recent Activity -->
    <div class="col-lg-8 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Aktivitas Terbaru</h6>
                <a href="{{ route('admin.responses') }}" class="btn btn-primary btn-sm">Lihat Semua</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-admin table-hover">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Fakultas</th>
                                <th>Risiko</th>
                                <th>PHQ-9</th>
                                <th>Waktu</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentActivity as $activity)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.responses.show', $activity['id']) }}" class="text-decoration-none">
                                            {{ $activity['name'] }}
                                        </a>
                                    </td>
                                    <td>{{ $activity['faculty'] }}</td>
                                    <td>
                                        <span class="mental-health-badge badge-risk-{{ strtolower($activity['risk_level']) }}">
                                            {{ $activity['risk_level'] }}
                                        </span>
                                    </td>
                                    <td>{{ $activity['phq9_category'] }}</td>
                                    <td class="small text-muted">
                                        {{ $activity['completed_at']->diffForHumans() }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        <i class="fas fa-inbox fa-3x mb-3 text-gray-300"></i><br>
                                        Belum ada aktivitas terbaru
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="col-lg-4 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Aksi Cepat</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.responses', ['filter' => 'high-risk']) }}" class="quick-action-btn btn-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Tinjau Risiko Tinggi
                    </a>
                    
                    <a href="{{ route('admin.responses', ['filter' => 'today']) }}" class="quick-action-btn btn-success">
                        <i class="fas fa-calendar-day me-2"></i>
                        Respons Hari Ini
                    </a>
                    
                    <button onclick="showExportModal('excel')" class="quick-action-btn btn-primary">
                        <i class="fas fa-file-excel me-2"></i>
                        Export Data
                    </button>
                    
                    <a href="{{ route('admin.analytics') }}" class="quick-action-btn btn-warning">
                        <i class="fas fa-chart-line me-2"></i>
                        Lihat Analitik
                    </a>
                </div>
            </div>
        </div>

        <!-- System Info -->
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Informasi Sistem</h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <div class="fw-bold text-primary" style="font-size: 1.5rem;">{{ $stats['active_faculties'] }}</div>
                        <small class="text-muted">Fakultas Aktif</small>
                    </div>
                    <div class="col-6">
                        <div class="fw-bold text-success" style="font-size: 1.5rem;">{{ number_format($stats['avg_phq9_score'], 1) }}</div>
                        <small class="text-muted">Rata-rata PHQ-9</small>
                    </div>
                </div>
                
                <hr>
                
                <div class="small">
                    <div class="d-flex justify-content-between mb-1">
                        <span>Bulan Ini:</span>
                        <span class="fw-bold">{{ number_format($stats['month_responses']) }} responden</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span>Tingkat Penyelesaian:</span>
                        <span class="fw-bold">{{ $stats['completion_rate'] }}%</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Update Terakhir:</span>
                        <span class="fw-bold">{{ now()->format('H:i') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Chart Data from Server
const chartData = @json($charts);

$(document).ready(function() {
    initializeDashboardCharts();
});

function initializeDashboardCharts() {
    // PHQ-9 Distribution Chart
    const phq9Ctx = document.getElementById('phq9Chart').getContext('2d');
    new Chart(phq9Ctx, {
        type: 'doughnut',
        data: {
            labels: Object.keys(chartData.phq9_distribution),
            datasets: [{
                data: Object.values(chartData.phq9_distribution),
                backgroundColor: ['#4CAF50', '#2196F3', '#FF9800', '#F44336', '#9C27B0'],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });

    // Risk Distribution Chart
    const riskCtx = document.getElementById('riskChart').getContext('2d');
    new Chart(riskCtx, {
        type: 'doughnut',
        data: {
            labels: Object.keys(chartData.risk_distribution),
            datasets: [{
                data: Object.values(chartData.risk_distribution),
                backgroundColor: ['#4CAF50', '#2196F3', '#FF9800', '#F44336'],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });

    // Monthly Trends Chart
    const trendsCtx = document.getElementById('monthlyTrendsChart').getContext('2d');
    new Chart(trendsCtx, {
        type: 'line',
        data: {
            labels: Object.keys(chartData.monthly_trends),
            datasets: [{
                label: 'Responden per Bulan',
                data: Object.values(chartData.monthly_trends),
                borderColor: '#4facfe',
                backgroundColor: 'rgba(79, 172, 254, 0.1)',
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

function refreshDashboard() {
    showLoading('Memperbarui dashboard...');
    location.reload();
}

function exportDashboardData() {
    window.location.href = '/admin/export/dashboard-summary';
}
</script>
@endpush