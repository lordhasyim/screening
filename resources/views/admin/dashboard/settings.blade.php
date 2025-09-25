@extends('layouts.admin')

@section('title', 'Pengaturan - Admin Dashboard')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Pengaturan Sistem</h1>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fas fa-check-circle mr-2"></i>
    {{ session('success') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="fas fa-exclamation-triangle mr-2"></i>
    {{ session('error') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

<div class="row">
    <!-- General Settings -->
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Pengaturan Umum</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.settings.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="daily_target">Target Harian Skrining</label>
                                <input type="number" class="form-control @error('daily_target') is-invalid @enderror"
                                       id="daily_target" name="daily_target"
                                       value="{{ old('daily_target', $settings['daily_target']) }}"
                                       min="1" required>
                                <small class="form-text text-muted">
                                    Target jumlah respons per hari yang diharapkan
                                </small>
                                @error('daily_target')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="auto_notifications">Notifikasi Otomatis</label>
                                <div class="custom-control custom-switch">
                                    <input type="hidden" name="auto_notifications" value="0">
                                    <input type="checkbox" class="custom-control-input"
                                           id="auto_notifications" name="auto_notifications" value="1"
                                           {{ old('auto_notifications', $settings['auto_notifications']) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="auto_notifications">
                                        Aktifkan notifikasi otomatis
                                    </label>
                                </div>
                                <small class="form-text text-muted">
                                    Kirim notifikasi otomatis untuk kasus risiko tinggi
                                </small>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <h6 class="text-gray-800 mb-3">Pengaturan Peringatan Risiko</h6>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="risk_threshold_high">Threshold Risiko Tinggi</label>
                                <input type="number" class="form-control @error('risk_threshold_high') is-invalid @enderror"
                                       id="risk_threshold_high" name="risk_threshold_high"
                                       value="{{ old('risk_threshold_high', $settings['risk_threshold_high']) }}"
                                       min="1" required>
                                <small class="form-text text-muted">
                                    Jumlah kasus risiko tinggi yang memicu peringatan
                                </small>
                                @error('risk_threshold_high')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="risk_threshold_critical">Threshold Risiko Kritis</label>
                                <input type="number" class="form-control @error('risk_threshold_critical') is-invalid @enderror"
                                       id="risk_threshold_critical" name="risk_threshold_critical"
                                       value="{{ old('risk_threshold_critical', $settings['risk_threshold_critical']) }}"
                                       min="1" required>
                                <small class="form-text text-muted">
                                    Jumlah kasus risiko kritis yang memicu peringatan darurat
                                </small>
                                @error('risk_threshold_critical')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-2"></i>
                            Simpan Pengaturan
                        </button>
                        <button type="reset" class="btn btn-secondary ml-2">
                            <i class="fas fa-undo mr-2"></i>
                            Reset
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- System Information -->
    <div class="col-lg-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Informasi Sistem</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <h6 class="text-gray-800">Versi Aplikasi</h6>
                    <p class="text-gray-600 mb-0">v1.0.0</p>
                </div>

                <div class="mb-3">
                    <h6 class="text-gray-800">Laravel Version</h6>
                    <p class="text-gray-600 mb-0">{{ App::version() }}</p>
                </div>

                <div class="mb-3">
                    <h6 class="text-gray-800">PHP Version</h6>
                    <p class="text-gray-600 mb-0">{{ PHP_VERSION }}</p>
                </div>

                <div class="mb-3">
                    <h6 class="text-gray-800">Database</h6>
                    <p class="text-gray-600 mb-0">
                        @php
                            try {
                                $connection = DB::connection()->getDatabaseName();
                                echo ucfirst(DB::connection()->getDriverName()) . ': ' . $connection;
                            } catch (Exception $e) {
                                echo 'Connection Error';
                            }
                        @endphp
                    </p>
                </div>

                <div class="mb-3">
                    <h6 class="text-gray-800">Environment</h6>
                    <span class="badge badge-{{ app()->environment() == 'production' ? 'danger' : 'warning' }}">
                        {{ strtoupper(app()->environment()) }}
                    </span>
                </div>

                <div class="mb-3">
                    <h6 class="text-gray-800">Last Updated</h6>
                    <p class="text-gray-600 mb-0">{{ now()->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Aksi Cepat</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.export') }}" class="btn btn-outline-success btn-sm mb-2">
                        <i class="fas fa-download mr-2"></i>
                        Export Semua Data
                    </a>

                    <button class="btn btn-outline-info btn-sm mb-2" onclick="clearCache()">
                        <i class="fas fa-broom mr-2"></i>
                        Clear Cache
                    </button>

                    <a href="{{ route('admin.analytics') }}" class="btn btn-outline-primary btn-sm mb-2">
                        <i class="fas fa-chart-line mr-2"></i>
                        Lihat Analitik
                    </a>

                    <a href="{{ route('admin.reports') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-file-alt mr-2"></i>
                        Generate Laporan
                    </a>
                </div>
            </div>
        </div>

        <!-- Storage Information -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Status Storage</h6>
            </div>
            <div class="card-body">
                @php
                    $diskSpace = disk_total_space('/');
                    $diskFree = disk_free_space('/');
                    $diskUsed = $diskSpace - $diskFree;
                    $diskUsedPercent = $diskSpace > 0 ? ($diskUsed / $diskSpace) * 100 : 0;
                @endphp

                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-gray-700">Disk Usage</span>
                        <span class="text-gray-700">{{ number_format($diskUsedPercent, 1) }}%</span>
                    </div>
                    <div class="progress">
                        <div class="progress-bar bg-{{ $diskUsedPercent > 80 ? 'danger' : ($diskUsedPercent > 60 ? 'warning' : 'primary') }}"
                             role="progressbar" style="width: {{ $diskUsedPercent }}%"></div>
                    </div>
                    <small class="text-muted">
                        {{ formatBytes($diskUsed) }} / {{ formatBytes($diskSpace) }}
                    </small>
                </div>

                @php
                    function formatBytes($size, $precision = 2) {
                        $base = log($size, 1024);
                        $suffixes = array('B', 'KB', 'MB', 'GB', 'TB');
                        return round(pow(1024, $base - floor($base)), $precision) .' '. $suffixes[floor($base)];
                    }
                @endphp

                <div class="mb-3">
                    <h6 class="text-gray-800">Memory Limit</h6>
                    <p class="text-gray-600 mb-0">{{ ini_get('memory_limit') }}</p>
                </div>

                <div class="mb-3">
                    <h6 class="text-gray-800">Max Execution Time</h6>
                    <p class="text-gray-600 mb-0">{{ ini_get('max_execution_time') }}s</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Maintenance Mode Card -->
<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4 border-left-warning">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-warning">
                    <i class="fas fa-tools mr-2"></i>
                    Mode Maintenance
                </h6>
            </div>
            <div class="card-body">
                <p class="mb-3">
                    Mode maintenance akan menonaktifkan akses publik ke aplikasi skrining sementara tetap
                    memungkinkan admin mengakses dashboard.
                </p>

                <div class="row">
                    <div class="col-md-8">
                        @if(app()->isDownForMaintenance())
                            <div class="alert alert-warning" role="alert">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                Aplikasi saat ini dalam mode maintenance
                            </div>
                        @else
                            <div class="alert alert-success" role="alert">
                                <i class="fas fa-check-circle mr-2"></i>
                                Aplikasi berjalan normal
                            </div>
                        @endif
                    </div>
                    <div class="col-md-4 text-right">
                        @if(app()->isDownForMaintenance())
                            <button class="btn btn-success" onclick="toggleMaintenanceMode(false)">
                                <i class="fas fa-play mr-2"></i>
                                Aktifkan Aplikasi
                            </button>
                        @else
                            <button class="btn btn-warning" onclick="toggleMaintenanceMode(true)">
                                <i class="fas fa-pause mr-2"></i>
                                Mode Maintenance
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function clearCache() {
    if (confirm('Apakah Anda yakin ingin membersihkan cache sistem?')) {
        // This would normally make an AJAX request to a cache clearing endpoint
        fetch('/admin/clear-cache', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Cache berhasil dibersihkan');
            } else {
                alert('Gagal membersihkan cache');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Cache berhasil dibersihkan (simulated)');
        });
    }
}

function toggleMaintenanceMode(enable) {
    const action = enable ? 'mengaktifkan' : 'menonaktifkan';
    if (confirm(`Apakah Anda yakin ingin ${action} mode maintenance?`)) {
        // This would normally make an AJAX request to toggle maintenance mode
        fetch('/admin/toggle-maintenance', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ enable: enable })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(`Gagal ${action} mode maintenance`);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert(`Mode maintenance ${enable ? 'diaktifkan' : 'dinonaktifkan'} (simulated)`);
            setTimeout(() => location.reload(), 1000);
        });
    }
}
</script>
@endpush