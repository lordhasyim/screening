@extends('layouts.admin')

@section('title', 'Data Skrining - Admin Dashboard')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Data Skrining</h1>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.export') }}" class="btn btn-success btn-sm">
            <i class="fas fa-download fa-sm text-white-50"></i> Export Data
        </a>
    </div>
</div>

<!-- Filter Cards -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Filter Data</h6>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.responses') }}">
                    <div class="form-row align-items-end">
                        <div class="col-lg-3 col-md-6 mb-3">
                            <label for="search">Cari (NIM/Nama/Email)</label>
                            <input type="text" class="form-control" name="search" id="search"
                                   value="{{ request('search') }}" placeholder="Masukkan kata kunci...">
                        </div>
                        <div class="col-lg-2 col-md-4 mb-3">
                            <label for="faculty_id">Fakultas</label>
                            <select name="faculty_id" id="faculty_id" class="form-control">
                                <option value="">Semua Fakultas</option>
                                @foreach($faculties as $faculty)
                                    <option value="{{ $faculty->id }}" {{ request('faculty_id') == $faculty->id ? 'selected' : '' }}>
                                        {{ $faculty->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-2 col-md-4 mb-3">
                            <label for="risk_level">Tingkat Risiko</label>
                            <select name="risk_level" id="risk_level" class="form-control">
                                <option value="">Semua Risiko</option>
                                <option value="Low" {{ request('risk_level') == 'Low' ? 'selected' : '' }}>Rendah</option>
                                <option value="Moderate" {{ request('risk_level') == 'Moderate' ? 'selected' : '' }}>Sedang</option>
                                <option value="High" {{ request('risk_level') == 'High' ? 'selected' : '' }}>Tinggi</option>
                                <option value="Critical" {{ request('risk_level') == 'Critical' ? 'selected' : '' }}>Kritis</option>
                            </select>
                        </div>
                        <div class="col-lg-2 col-md-4 mb-3">
                            <label for="status">Status</label>
                            <select name="status" id="status" class="form-control">
                                <option value="">Semua Status</option>
                                <option value="started" {{ request('status') == 'started' ? 'selected' : '' }}>Mulai</option>
                                <option value="phq9_completed" {{ request('status') == 'phq9_completed' ? 'selected' : '' }}>PHQ9 Selesai</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Lengkap</option>
                            </select>
                        </div>
                        <div class="col-lg-1 col-md-3 mb-3">
                            <label for="student_year">Angkatan</label>
                            <select name="student_year" id="student_year" class="form-control">
                                <option value="">Semua</option>
                                @for($year = 2020; $year <= date('Y') + 1; $year++)
                                    <option value="{{ $year }}" {{ request('student_year') == $year ? 'selected' : '' }}>{{ $year }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-lg-1 col-md-3 mb-3">
                            <label for="date_from">Dari</label>
                            <input type="date" class="form-control" name="date_from" id="date_from" value="{{ request('date_from') }}">
                        </div>
                        <div class="col-lg-1 col-md-3 mb-3">
                            <label for="date_to">Sampai</label>
                            <input type="date" class="form-control" name="date_to" id="date_to" value="{{ request('date_to') }}">
                        </div>
                        <div class="col-lg-auto col-md-3 mb-3">
                            <label class="d-block">&nbsp;</label>
                            <button type="submit" class="btn btn-primary mr-2">
                                <i class="fas fa-search"></i> Cari
                            </button>
                            <a href="{{ route('admin.responses') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Summary Cards -->
@if(request()->hasAny(['search', 'faculty_id', 'risk_level', 'status', 'date_from', 'date_to']))
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Risiko Rendah</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $responses->where('overall_risk_level', 'Low')->count() }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-smile fa-2x text-gray-300"></i>
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
                            Risiko Sedang</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $responses->where('overall_risk_level', 'Moderate')->count() }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-meh fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                            Risiko Tinggi</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $responses->where('overall_risk_level', 'High')->count() }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-frown fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-dark shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">
                            Risiko Kritis</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $responses->where('overall_risk_level', 'Critical')->count() }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Data Table -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">
            Daftar Respons ({{ $responses->total() }} total)
        </h6>
        @if($responses->count() > 0)
            <div class="dropdown no-arrow">
                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                    aria-labelledby="dropdownMenuLink">
                    <div class="dropdown-header">Aksi Bulk:</div>
                    <a class="dropdown-item" href="#" onclick="exportFiltered()">
                        <i class="fas fa-download fa-sm fa-fw mr-2 text-gray-400"></i>
                        Export Data Terfilter
                    </a>
                </div>
            </div>
        @endif
    </div>
    <div class="card-body">
        @if($responses->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 60px;">ID</th>
                            <th style="width: 120px;">NIM</th>
                            <th style="width: 200px;">Nama</th>
                            <th style="width: 150px;">Fakultas</th>
                            <th style="width: 150px;">Jurusan</th>
                            <th style="width: 100px;" class="text-center">PHQ9</th>
                            <th style="width: 100px;" class="text-center">DASS21</th>
                            <th style="width: 100px;" class="text-center">Risiko</th>
                            <th style="width: 100px;" class="text-center">Status</th>
                            <th style="width: 120px;" class="text-center">Tanggal</th>
                            <th style="width: 100px;" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($responses as $response)
                        <tr>
                            <td class="text-center">{{ $response->id }}</td>
                            <td>
                                <strong>{{ $response->nim }}</strong>
                                <br>
                                <small class="text-muted">{{ $response->student_year }}</small>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="mr-2">
                                        <div class="avatar-initial rounded-circle bg-light-primary">
                                            {{ strtoupper(substr($response->full_name, 0, 1)) }}
                                        </div>
                                    </div>
                                    <div>
                                        <div class="font-weight-bold">{{ Str::limit($response->full_name, 25) }}</div>
                                        <small class="text-muted">
                                            <i class="fas fa-{{ $response->gender === 'Laki-laki' ? 'mars' : 'venus' }}"></i>
                                            {{ $response->gender }}
                                            @if($response->email)
                                                | {{ $response->email }}
                                            @endif
                                        </small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-light">{{ $response->faculty->name ?? 'N/A' }}</span>
                            </td>
                            <td>
                                <small>{{ Str::limit($response->department->name ?? 'N/A', 20) }}</small>
                            </td>
                            <td class="text-center">
                                @if($response->phq9_total_score !== null)
                                    <div>
                                        <span class="badge badge-info">{{ $response->phq9_total_score }}/27</span>
                                    </div>
                                    <small class="text-muted">{{ $response->phq9_category }}</small>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($response->dass21_total_score)
                                    <div>
                                        <span class="badge badge-info">{{ $response->dass21_total_score }}/90</span>
                                    </div>
                                    <small class="text-muted">{{ $response->dass21_category }}</small>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($response->overall_risk_level)
                                    @php
                                        $riskColors = [
                                            'Low' => 'success',
                                            'Moderate' => 'warning', 
                                            'High' => 'danger',
                                            'Critical' => 'dark'
                                        ];
                                        $riskLabels = [
                                            'Low' => 'Rendah',
                                            'Moderate' => 'Sedang',
                                            'High' => 'Tinggi', 
                                            'Critical' => 'Kritis'
                                        ];
                                    @endphp
                                    <span class="badge badge-{{ $riskColors[$response->overall_risk_level] ?? 'secondary' }}">
                                        {{ $riskLabels[$response->overall_risk_level] ?? $response->overall_risk_level }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @php
                                    $statusColors = [
                                        'started' => 'info',
                                        'phq9_completed' => 'warning', 
                                        'completed' => 'success'
                                    ];
                                    $statusLabels = [
                                        'started' => 'Mulai',
                                        'phq9_completed' => 'PHQ9 Selesai',
                                        'completed' => 'Lengkap'
                                    ];
                                @endphp
                                <span class="badge badge-{{ $statusColors[$response->quiz_status] ?? 'secondary' }}">
                                    {{ $statusLabels[$response->quiz_status] ?? ucfirst($response->quiz_status) }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div>
                                    <small class="font-weight-bold">{{ $response->completed_at ? $response->completed_at->format('d/m/Y') : $response->created_at->format('d/m/Y') }}</small>
                                </div>
                                <small class="text-muted">{{ $response->completed_at ? $response->completed_at->format('H:i') : $response->created_at->format('H:i') }}</small>
                            </td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.responses.show', $response) }}"
                                       class="btn btn-info btn-sm" title="Lihat Detail" data-toggle="tooltip">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($response->quiz_status === 'completed')
                                    {{-- <a href="{{ route('admin.responses.pdf', $response) }}"
                                       class="btn btn-success btn-sm" title="Download PDF" data-toggle="tooltip">
                                        <i class="fas fa-file-pdf"></i>
                                    </a> --}}
                                    @endif
                                    <button class="btn btn-danger btn-sm"
                                            onclick="deleteResponse({{ $response->id }})" title="Hapus" data-toggle="tooltip">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination Section -->
            <div class="pagination-container">
                <div class="pagination-info">
                    <small class="text-muted">
                        Menampilkan {{ $responses->firstItem() ?? 0 }} hingga {{ $responses->lastItem() ?? 0 }}
                        dari {{ $responses->total() }} entri
                    </small>
                </div>
                
                @if ($responses->hasPages())
                    <nav aria-label="Navigasi halaman">
                        <ul class="pagination pagination-sm mb-0">
                            {{-- Previous Page Link --}}
                            @if ($responses->onFirstPage())
                                <li class="page-item disabled">
                                    <span class="page-link">
                                        <i class="fas fa-angle-left"></i> Previous
                                    </span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ $responses->appends(request()->query())->previousPageUrl() }}">
                                        <i class="fas fa-angle-left"></i> Previous
                                    </a>
                                </li>
                            @endif

                            {{-- Pagination Numbers --}}
                            @php
                                $start = max($responses->currentPage() - 2, 1);
                                $end = min($start + 4, $responses->lastPage());
                                $start = max($end - 4, 1);
                            @endphp

                            @if($start > 1)
                                <li class="page-item">
                                    <a class="page-link" href="{{ $responses->appends(request()->query())->url(1) }}">1</a>
                                </li>
                                @if($start > 2)
                                    <li class="page-item disabled">
                                        <span class="page-link">...</span>
                                    </li>
                                @endif
                            @endif

                            @for ($i = $start; $i <= $end; $i++)
                                @if ($i == $responses->currentPage())
                                    <li class="page-item active" aria-current="page">
                                        <span class="page-link">{{ $i }}</span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $responses->appends(request()->query())->url($i) }}">{{ $i }}</a>
                                    </li>
                                @endif
                            @endfor

                            @if($end < $responses->lastPage())
                                @if($end < $responses->lastPage() - 1)
                                    <li class="page-item disabled">
                                        <span class="page-link">...</span>
                                    </li>
                                @endif
                                <li class="page-item">
                                    <a class="page-link" href="{{ $responses->appends(request()->query())->url($responses->lastPage()) }}">{{ $responses->lastPage() }}</a>
                                </li>
                            @endif

                            {{-- Next Page Link --}}
                            @if ($responses->hasMorePages())
                                <li class="page-item">
                                    <a class="page-link" href="{{ $responses->appends(request()->query())->nextPageUrl() }}">
                                        Next <i class="fas fa-angle-right"></i>
                                    </a>
                                </li>
                            @else
                                <li class="page-item disabled">
                                    <span class="page-link">
                                        Next <i class="fas fa-angle-right"></i>
                                    </span>
                                </li>
                            @endif
                        </ul>
                    </nav>
                @endif
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-inbox fa-3x text-gray-300 mb-3"></i>
                <h5 class="text-gray-600">Tidak ada data ditemukan</h5>
                <p class="text-gray-500">Coba ubah filter pencarian Anda atau <a href="{{ route('admin.responses') }}">reset filter</a></p>
            </div>
        @endif
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus data respons ini?</p>
                <p class="text-danger"><strong>Peringatan:</strong> Tindakan ini tidak dapat dibatalkan.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Hapus</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.avatar-initial {
    width: 35px;
    height: 35px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 14px;
}

.bg-light-primary {
    background-color: rgba(78, 115, 223, 0.1) !important;
    color: #4e73df !important;
}

.table th {
    border-top: none;
    font-weight: 600;
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.table td {
    vertical-align: middle;
    font-size: 0.875rem;
}

.table-hover tbody tr:hover {
    background-color: rgba(0,0,0,.02);
}

.btn-group .btn {
    margin-right: 2px;
}

.btn-group .btn:last-child {
    margin-right: 0;
}

@media (max-width: 768px) {
    .table-responsive {
        font-size: 0.8rem;
    }
    
    .btn-group .btn {
        padding: 0.25rem 0.4rem;
        font-size: 0.8rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
let responseToDelete = null;

function deleteResponse(id) {
    responseToDelete = id;
    $('#deleteModal').modal('show');
}

$(document).ready(function() {
    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();
    
    // Handle delete confirmation
    $('#confirmDelete').click(function() {
        if (responseToDelete) {
            fetch(`/admin/responses/${responseToDelete}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                $('#deleteModal').modal('hide');
                if (data.success) {
                    // Show success toast/alert
                    showAlert('success', 'Data berhasil dihapus');
                    // Reload page after short delay
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                } else {
                    showAlert('error', 'Gagal menghapus data: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                $('#deleteModal').modal('hide');
                console.error('Error:', error);
                showAlert('error', 'Terjadi kesalahan saat menghapus data');
            });
        }
    });
    
    // Clear form helper
    $('.btn-clear-filters').click(function(e) {
        e.preventDefault();
        window.location.href = "{{ route('admin.responses') }}";
    });
});

function showAlert(type, message) {
    // Create toast notification
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const iconClass = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
    
    const alert = $(`
        <div class="alert ${alertClass} alert-dismissible fade show position-fixed" style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
            <i class="fas ${iconClass} mr-2"></i>
            ${message}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    `);
    
    $('body').append(alert);
    
    // Auto dismiss after 5 seconds
    setTimeout(() => {
        alert.alert('close');
    }, 5000);
}

function exportFiltered() {
    // Build export URL with current filters
    const params = new URLSearchParams(window.location.search);
    params.set('export', '1');
    window.location.href = "{{ route('admin.export') }}?" + params.toString();
}
</script>
@endpush