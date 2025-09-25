@extends('layouts.admin')

@section('title', 'Detail Respons - Admin Dashboard')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Detail Respons #{{ $quizResponse->id }}</h1>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.responses') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
        <button class="btn btn-danger btn-sm" onclick="deleteResponse({{ $quizResponse->id }})">
            <i class="fas fa-trash"></i> Hapus
        </button>
    </div>
</div>

<!-- Summary Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            PHQ-9 Score</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $quizResponse->phq9_total_score ?? 'N/A' }}
                        </div>
                        <div class="text-xs">{{ $quizResponse->phq9_category ?? 'Belum selesai' }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-brain fa-2x text-gray-300"></i>
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
                            DASS-21 Score</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $quizResponse->dass21_total_score ?? 'N/A' }}
                        </div>
                        <div class="text-xs">{{ $quizResponse->dass21_category ?? 'Belum selesai' }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-heartbeat fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-{{ getRiskBadgeColor($quizResponse->overall_risk_level ?? 'secondary') }} shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-{{ getRiskBadgeColor($quizResponse->overall_risk_level ?? 'secondary') }} text-uppercase mb-1">
                            Risk Level</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $quizResponse->overall_risk_level ?? 'N/A' }}
                        </div>
                        <div class="text-xs">Tingkat Risiko</div>
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
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Status</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ ucfirst($quizResponse->quiz_status) }}
                        </div>
                        <div class="text-xs">{{ $quizResponse->completed_at ? $quizResponse->completed_at->format('d/m/Y H:i') : 'Belum selesai' }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clipboard-check fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Personal Information -->
<div class="row">
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Informasi Personal</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>NIM</strong></td>
                                <td>{{ $quizResponse->nim }}</td>
                            </tr>
                            <tr>
                                <td><strong>Nama Lengkap</strong></td>
                                <td>{{ $quizResponse->full_name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Jenis Kelamin</strong></td>
                                <td>{{ $quizResponse->gender }}</td>
                            </tr>
                            <tr>
                                <td><strong>Tempat, Tanggal Lahir</strong></td>
                                <td>{{ $quizResponse->birth_place }}, {{ $quizResponse->birth_date->format('d/m/Y') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Umur</strong></td>
                                <td>{{ $quizResponse->age }} tahun</td>
                            </tr>
                            <tr>
                                <td><strong>Email</strong></td>
                                <td>{{ $quizResponse->email ?: 'Tidak tersedia' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Telepon</strong></td>
                                <td>{{ $quizResponse->phone }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Fakultas</strong></td>
                                <td>{{ $quizResponse->faculty->name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Jurusan</strong></td>
                                <td>{{ $quizResponse->department->name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Tahun Mahasiswa</strong></td>
                                <td>{{ $quizResponse->student_year }}</td>
                            </tr>
                            <tr>
                                <td><strong>Jalur Masuk</strong></td>
                                <td>{{ $quizResponse->admission_path }}</td>
                            </tr>
                            <tr>
                                <td><strong>Tempat Tinggal</strong></td>
                                <td>{{ $quizResponse->living_arrangement }}</td>
                            </tr>
                            <tr>
                                <td><strong>Asal Provinsi</strong></td>
                                <td>{{ $quizResponse->origin_province }}</td>
                            </tr>
                            <tr>
                                <td><strong>Tipe Daerah Asal</strong></td>
                                <td>{{ ucfirst($quizResponse->origin_area_type) }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="mt-3">
                    <h6 class="font-weight-bold text-gray-800">Alamat</h6>
                    <p class="text-gray-600">{{ $quizResponse->address }}</p>
                </div>
            </div>
        </div>

        <!-- Medical History -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Riwayat Kesehatan</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="font-weight-bold text-gray-800">Penyakit Kronis</h6>
                        <p>{{ $quizResponse->has_chronic_disease ? 'Ya' : 'Tidak' }}</p>
                        @if($quizResponse->has_chronic_disease && $quizResponse->chronic_disease_details)
                            <small class="text-gray-600">Detail: {{ $quizResponse->chronic_disease_details }}</small>
                        @endif

                        <h6 class="font-weight-bold text-gray-800 mt-3">Obat-obatan</h6>
                        <p>{{ $quizResponse->current_medication ? 'Ya' : 'Tidak' }}</p>
                        @if($quizResponse->current_medication && $quizResponse->medication_details)
                            <small class="text-gray-600">Detail: {{ $quizResponse->medication_details }}</small>
                        @endif

                        <h6 class="font-weight-bold text-gray-800 mt-3">Cedera Kepala</h6>
                        <p>{{ $quizResponse->head_injury_history ? 'Ya' : 'Tidak' }}</p>
                        @if($quizResponse->head_injury_history && $quizResponse->injury_details)
                            <small class="text-gray-600">Detail: {{ $quizResponse->injury_details }}</small>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <h6 class="font-weight-bold text-gray-800">Penggunaan Zat</h6>
                        <p>{{ $quizResponse->substance_use }}</p>
                        @if($quizResponse->substance_details)
                            <small class="text-gray-600">Detail: {{ $quizResponse->substance_details }}</small>
                        @endif

                        <h6 class="font-weight-bold text-gray-800 mt-3">Riwayat Perawatan Psikologi</h6>
                        <p>{{ $quizResponse->psychological_treatment_history ? 'Ya' : 'Tidak' }}</p>
                        @if($quizResponse->psychological_treatment_history && $quizResponse->treatment_details)
                            <small class="text-gray-600">Detail: {{ $quizResponse->treatment_details }}</small>
                        @endif

                        <h6 class="font-weight-bold text-gray-800 mt-3">Riwayat Mental Keluarga</h6>
                        <p>{{ $quizResponse->family_mental_health_history ? 'Ya' : 'Tidak' }}</p>
                        @if($quizResponse->family_mental_health_history && $quizResponse->family_history_details)
                            <small class="text-gray-600">Detail: {{ $quizResponse->family_history_details }}</small>
                        @endif
                    </div>
                </div>

                @if($quizResponse->family_relationship_description)
                <div class="mt-3">
                    <h6 class="font-weight-bold text-gray-800">Deskripsi Hubungan Keluarga</h6>
                    <p class="text-gray-600">{{ $quizResponse->family_relationship_description }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Assessment Results -->
    <div class="col-lg-4">
        <!-- PHQ-9 Results -->
        @if($quizResponse->phq9_responses)
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Hasil PHQ-9</h6>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <div class="h2 text-primary">{{ $quizResponse->phq9_total_score }}</div>
                    <div class="badge badge-{{ getRiskBadgeColor($quizResponse->phq9_category) }} badge-pill">
                        {{ $quizResponse->phq9_category }}
                    </div>
                </div>
                <div class="small text-gray-600">
                    {{ getPhq9InterpretationText($quizResponse->phq9_category) }}
                </div>
                <div class="mt-3">
                    <strong>Selesai pada:</strong><br>
                    <small>{{ $quizResponse->phq9_completed_at->format('d/m/Y H:i') }}</small>
                </div>
            </div>
        </div>
        @endif

        <!-- DASS-21 Results -->
        @if($quizResponse->dass21_responses)
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Hasil DASS-21</h6>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <div class="h2 text-success">{{ $quizResponse->dass21_total_score }}</div>
                    <div class="badge badge-{{ getRiskBadgeColor($quizResponse->dass21_category) }} badge-pill">
                        {{ $quizResponse->dass21_category }}
                    </div>
                </div>
                <div class="small text-gray-600">
                    {{ getDass21InterpretationText($quizResponse->dass21_category) }}
                </div>
                <div class="mt-3">
                    <strong>Selesai pada:</strong><br>
                    <small>{{ $quizResponse->dass21_completed_at->format('d/m/Y H:i') }}</small>
                </div>
            </div>
        </div>
        @endif

        <!-- Overall Recommendation -->
        @if($quizResponse->overall_risk_level)
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Rekomendasi</h6>
            </div>
            <div class="card-body">
                <div class="alert alert-{{ getRiskBadgeColor($quizResponse->overall_risk_level) }}" role="alert">
                    <strong>Tingkat Risiko: {{ $quizResponse->overall_risk_level }}</strong><br>
                    <small>{{ getOverallRecommendation($quizResponse->overall_risk_level) }}</small>
                </div>
            </div>
        </div>
        @endif

        <!-- Timeline -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Timeline</h6>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <div class="timeline-item">
                        <i class="fas fa-play bg-primary"></i>
                        <div class="timeline-item-content">
                            <h6>Memulai Skrining</h6>
                            <small>{{ $quizResponse->started_at->format('d/m/Y H:i') }}</small>
                        </div>
                    </div>
                    @if($quizResponse->phq9_completed_at)
                    <div class="timeline-item">
                        <i class="fas fa-check bg-success"></i>
                        <div class="timeline-item-content">
                            <h6>PHQ-9 Selesai</h6>
                            <small>{{ $quizResponse->phq9_completed_at->format('d/m/Y H:i') }}</small>
                        </div>
                    </div>
                    @endif
                    @if($quizResponse->dass21_completed_at)
                    <div class="timeline-item">
                        <i class="fas fa-check bg-success"></i>
                        <div class="timeline-item-content">
                            <h6>DASS-21 Selesai</h6>
                            <small>{{ $quizResponse->dass21_completed_at->format('d/m/Y H:i') }}</small>
                        </div>
                    </div>
                    @endif
                    @if($quizResponse->completed_at)
                    <div class="timeline-item">
                        <i class="fas fa-flag bg-info"></i>
                        <div class="timeline-item-content">
                            <h6>Skrining Selesai</h6>
                            <small>{{ $quizResponse->completed_at->format('d/m/Y H:i') }}</small>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    height: 100%;
    width: 2px;
    background: #e3e6f0;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-item i {
    position: absolute;
    left: -45px;
    top: 0;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    text-align: center;
    line-height: 30px;
    color: white;
    font-size: 12px;
}

.timeline-item-content h6 {
    margin: 0;
    font-size: 14px;
    font-weight: 600;
}

.timeline-item-content small {
    color: #6c757d;
    font-size: 12px;
}
</style>
@endpush

@push('scripts')
<script>
function deleteResponse(id) {
    if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
        fetch(`/admin/responses/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Data berhasil dihapus');
                window.location.href = '{{ route("admin.responses") }}';
            } else {
                alert('Gagal menghapus data');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan');
        });
    }
}
</script>
@endpush