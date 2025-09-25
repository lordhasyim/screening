@extends('layouts.admin')

@section('title', 'Laporan - Admin Dashboard')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Laporan Skrining</h1>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.export', ['format' => 'excel']) }}" class="btn btn-success btn-sm">
            <i class="fas fa-file-excel fa-sm text-white-50"></i> Export Excel
        </a>
        <a href="{{ route('admin.export', ['format' => 'csv']) }}" class="btn btn-info btn-sm">
            <i class="fas fa-file-csv fa-sm text-white-50"></i> Export CSV
        </a>
    </div>
</div>

<!-- Summary Statistics Cards -->
<div class="row">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Respons</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $reportData['summary_stats']['total_responses'] }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Respons Selesai</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $reportData['summary_stats']['completed_responses'] }}
                        </div>
                        <div class="text-xs">
                            ({{ $reportData['summary_stats']['completion_rate'] }}%)
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clipboard-check fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Risiko Tinggi</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $reportData['summary_stats']['high_risk_count'] }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Fakultas Aktif</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $reportData['summary_stats']['active_faculties'] }}
                        </div>
                        <div class="text-xs">
                            dari {{ $reportData['summary_stats']['total_faculties'] }} total
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-university fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Risk Level Distribution -->
<div class="row">
    <div class="col-lg-6 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Distribusi Tingkat Risiko</h6>
            </div>
            <div class="card-body">
                <div class="chart-pie pt-4 pb-2">
                    <canvas id="riskPieChart"></canvas>
                </div>
                <div class="mt-4 text-center small">
                    @foreach($reportData['risk_distribution'] as $level => $count)
                    <span class="mr-2">
                        <i class="fas fa-circle text-{{ getRiskBadgeColor($level) }}"></i> {{ $level }}
                    </span>
                    @endforeach
                </div>

                <!-- Risk Level Table -->
                <div class="table-responsive mt-3">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Tingkat Risiko</th>
                                <th>Jumlah</th>
                                <th>Persentase</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $totalRisk = array_sum($reportData['risk_distribution']); @endphp
                            @foreach($reportData['risk_distribution'] as $level => $count)
                            <tr>
                                <td>
                                    <span class="badge badge-{{ getRiskBadgeColor($level) }}">
                                        {{ $level }}
                                    </span>
                                </td>
                                <td>{{ $count }}</td>
                                <td>{{ $totalRisk > 0 ? round(($count / $totalRisk) * 100, 1) : 0 }}%</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Faculty Distribution -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Partisipasi per Fakultas</h6>
            </div>
            <div class="card-body">
                <div class="chart-bar">
                    <canvas id="facultyBarChart"></canvas>
                </div>

                <!-- Faculty Table -->
                <div class="table-responsive mt-3">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Fakultas</th>
                                <th>Respons</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reportData['faculty_breakdown'] as $faculty => $count)
                            <tr>
                                <td>{{ $faculty }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="mr-2">{{ $count }}</span>
                                        <div class="progress flex-grow-1" style="height: 20px;">
                                            <div class="progress-bar bg-primary" role="progressbar"
                                                 style="width: {{ $reportData['summary_stats']['total_responses'] > 0 ? ($count / $reportData['summary_stats']['total_responses']) * 100 : 0 }}%">
                                            </div>
                                        </div>
                                    </div>
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

<!-- Monthly Trends -->
<div class="row">
    <div class="col-lg-12 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Tren Bulanan (6 Bulan Terakhir)</h6>
            </div>
            <div class="card-body">
                <div class="chart-area">
                    <canvas id="monthlyTrendChart"></canvas>
                </div>

                <!-- Monthly Data Table -->
                <div class="table-responsive mt-4">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Bulan</th>
                                @foreach($reportData['monthly_trends'] as $month => $count)
                                <th>{{ $month }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>Jumlah Respons</strong></td>
                                @foreach($reportData['monthly_trends'] as $month => $count)
                                <td>{{ $count }}</td>
                                @endforeach
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Detailed Statistics -->
<div class="row">
    <div class="col-lg-6 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Statistik Skor PHQ-9</h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <div class="h4 text-primary">{{ round($reportData['summary_stats']['avg_phq9_score'], 1) }}</div>
                        <div class="small text-gray-600">Rata-rata Skor</div>
                    </div>
                    <div class="col-6">
                        <div class="h4 text-success">{{ $reportData['summary_stats']['phq9_completed'] }}</div>
                        <div class="small text-gray-600">Total Selesai</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Statistik Skor DASS-21</h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <div class="h4 text-primary">{{ round($reportData['summary_stats']['avg_dass21_score'], 1) }}</div>
                        <div class="small text-gray-600">Rata-rata Skor</div>
                    </div>
                    <div class="col-6">
                        <div class="h4 text-success">{{ $reportData['summary_stats']['dass21_completed'] }}</div>
                        <div class="small text-gray-600">Total Selesai</div>
                        <div class="small text-muted">({{ $reportData['summary_stats']['dass21_percentage'] }}%)</div>
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
// Risk Level Pie Chart
const riskCtx = document.getElementById('riskPieChart').getContext('2d');
const riskData = @json($reportData['risk_distribution']);
const riskChart = new Chart(riskCtx, {
    type: 'doughnut',
    data: {
        labels: Object.keys(riskData),
        datasets: [{
            data: Object.values(riskData),
            backgroundColor: [
                '#1cc88a', // Low - success
                '#36b9cc', // Moderate - info
                '#f6c23e', // High - warning
                '#e74a3b'  // Critical - danger
            ],
            hoverBackgroundColor: [
                '#17a673',
                '#2c9faf',
                '#dda20a',
                '#c0392b'
            ],
            hoverBorderColor: "rgba(234, 236, 244, 1)",
        }],
    },
    options: {
        maintainAspectRatio: false,
        tooltips: {
            backgroundColor: "rgb(255,255,255)",
            bodyFontColor: "#858796",
            borderColor: '#dddfeb',
            borderWidth: 1,
            displayColors: false,
            caretPadding: 10,
        },
        legend: {
            display: false
        },
        cutoutPercentage: 80,
    },
});

// Faculty Bar Chart
const facultyCtx = document.getElementById('facultyBarChart').getContext('2d');
const facultyData = @json($reportData['faculty_breakdown']);
const facultyChart = new Chart(facultyCtx, {
    type: 'horizontalBar',
    data: {
        labels: Object.keys(facultyData),
        datasets: [{
            label: "Respons",
            backgroundColor: "#4e73df",
            hoverBackgroundColor: "#2e59d9",
            borderColor: "#4e73df",
            data: Object.values(facultyData),
        }],
    },
    options: {
        maintainAspectRatio: false,
        layout: {
            padding: {
                left: 10,
                right: 25,
                top: 25,
                bottom: 0
            }
        },
        scales: {
            xAxes: [{
                gridLines: {
                    display: false,
                    drawBorder: false
                },
                ticks: {
                    maxTicksLimit: 6
                },
                maxBarThickness: 25,
            }],
            yAxes: [{
                ticks: {
                    padding: 10,
                    fontSize: 12,
                    callback: function(value, index, values) {
                        return value.length > 20 ? value.substring(0, 20) + '...' : value;
                    }
                },
                gridLines: {
                    color: "rgb(234, 236, 244)",
                    zeroLineColor: "rgb(234, 236, 244)",
                    drawBorder: false,
                    borderDash: [2],
                    zeroLineBorderDash: [2]
                }
            }],
        },
        legend: {
            display: false
        },
        tooltips: {
            titleMarginBottom: 10,
            titleFontColor: '#6e707e',
            titleFontSize: 14,
            backgroundColor: "rgb(255,255,255)",
            bodyFontColor: "#858796",
            borderColor: '#dddfeb',
            borderWidth: 1,
            displayColors: false,
            caretPadding: 10,
        },
    }
});

// Monthly Trend Chart
const monthlyCtx = document.getElementById('monthlyTrendChart').getContext('2d');
const monthlyData = @json($reportData['monthly_trends']);
const monthlyChart = new Chart(monthlyCtx, {
    type: 'line',
    data: {
        labels: Object.keys(monthlyData),
        datasets: [{
            label: "Respons",
            lineTension: 0.3,
            backgroundColor: "rgba(78, 115, 223, 0.05)",
            borderColor: "rgba(78, 115, 223, 1)",
            pointRadius: 3,
            pointBackgroundColor: "rgba(78, 115, 223, 1)",
            pointBorderColor: "rgba(78, 115, 223, 1)",
            pointHoverRadius: 3,
            pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
            pointHoverBorderColor: "rgba(78, 115, 223, 1)",
            pointHitRadius: 10,
            pointBorderWidth: 2,
            data: Object.values(monthlyData),
        }],
    },
    options: {
        maintainAspectRatio: false,
        layout: {
            padding: {
                left: 10,
                right: 25,
                top: 25,
                bottom: 0
            }
        },
        scales: {
            xAxes: [{
                gridLines: {
                    color: "rgb(234, 236, 244)",
                    zeroLineColor: "rgb(234, 236, 244)",
                    drawBorder: false,
                    borderDash: [2],
                    zeroLineBorderDash: [2]
                }
            }],
            yAxes: [{
                ticks: {
                    maxTicksLimit: 5,
                    padding: 10,
                },
                gridLines: {
                    color: "rgb(234, 236, 244)",
                    zeroLineColor: "rgb(234, 236, 244)",
                    drawBorder: false,
                    borderDash: [2],
                    zeroLineBorderDash: [2]
                }
            }],
        },
        legend: {
            display: false
        },
        tooltips: {
            backgroundColor: "rgb(255,255,255)",
            bodyFontColor: "#858796",
            titleMarginBottom: 10,
            titleFontColor: '#6e707e',
            titleFontSize: 14,
            borderColor: '#dddfeb',
            borderWidth: 1,
            displayColors: false,
            intersect: false,
            mode: 'index',
            caretPadding: 10,
        }
    }
});
</script>
@endpush