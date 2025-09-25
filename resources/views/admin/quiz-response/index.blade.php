@extends('layouts.admin')

@section('title', 'Quiz Responses Management - Admin Dashboard')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Quiz Responses Management</h1>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.quiz-responses.export', request()->query()) }}" class="btn btn-success btn-sm">
            <i class="fas fa-download fa-sm text-white-50"></i> Export Data
        </a>
        <button class="btn btn-danger btn-sm" onclick="bulkDelete()" id="bulkDeleteBtn" style="display: none;">
            <i class="fas fa-trash fa-sm text-white-50"></i> Delete Selected
        </button>
    </div>
</div>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Responses</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total'] }}</div>
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
                            Completed</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['completed'] }}</div>
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
                            High Risk</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['high_risk'] }}</div>
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
                            Today</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['today'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calendar-day fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter Cards -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Filter Responses</h6>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.quiz-responses.index') }}" class="row">
                    <div class="col-md-3 mb-3">
                        <label for="search">Search (NIM/Name/Email)</label>
                        <input type="text" class="form-control" name="search" id="search"
                               value="{{ request('search') }}" placeholder="Enter keywords...">
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="faculty_id">Faculty</label>
                        <select name="faculty_id" id="faculty_id" class="form-control">
                            <option value="">All Faculties</option>
                            @foreach($faculties as $faculty)
                                <option value="{{ $faculty->id }}" {{ request('faculty_id') == $faculty->id ? 'selected' : '' }}>
                                    {{ $faculty->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="risk_level">Risk Level</label>
                        <select name="risk_level" id="risk_level" class="form-control">
                            <option value="">All Risks</option>
                            <option value="Low" {{ request('risk_level') == 'Low' ? 'selected' : '' }}>Low</option>
                            <option value="Moderate" {{ request('risk_level') == 'Moderate' ? 'selected' : '' }}>Moderate</option>
                            <option value="High" {{ request('risk_level') == 'High' ? 'selected' : '' }}>High</option>
                            <option value="Critical" {{ request('risk_level') == 'Critical' ? 'selected' : '' }}>Critical</option>
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="status">Status</label>
                        <select name="status" id="status" class="form-control">
                            <option value="">All Status</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="incomplete" {{ request('status') == 'incomplete' ? 'selected' : '' }}>Incomplete</option>
                        </select>
                    </div>
                    <div class="col-md-1 mb-3">
                        <label for="date_from">From Date</label>
                        <input type="date" class="form-control" name="date_from" id="date_from" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-1 mb-3">
                        <label for="date_to">To Date</label>
                        <input type="date" class="form-control" name="date_to" id="date_to" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-1 mb-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-search"></i> Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Data Table -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">
            Response List ({{ $responses->total() }} total)
        </h6>
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="selectAll">
            <label class="form-check-label" for="selectAll">
                Select All
            </label>
        </div>
    </div>
    <div class="card-body">
        @if($responses->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th width="30px">
                                <input type="checkbox" id="selectAllHeader">
                            </th>
                            <th>Student</th>
                            <th>Faculty/Department</th>
                            <th>Scores</th>
                            <th>Risk Level</th>
                            <th>Status</th>
                            <th>Completed</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($responses as $response)
                        <tr>
                            <td>
                                <input type="checkbox" class="row-checkbox" value="{{ $response->id }}">
                            </td>
                            <td>
                                <div class="font-weight-bold">{{ $response->full_name }}</div>
                                <small class="text-muted">{{ $response->nim }}</small>
                            </td>
                            <td>
                                <div>{{ $response->faculty->name ?? 'N/A' }}</div>
                                <small class="text-muted">{{ $response->department->name ?? 'N/A' }}</small>
                            </td>
                            <td class="text-center">
                                @if($response->phq9_total_score)
                                    <div class="mb-1">
                                        <span class="badge badge-info">PHQ-9: {{ $response->phq9_total_score }}</span>
                                    </div>
                                @endif
                                @if($response->dass21_total_score)
                                    <div>
                                        <span class="badge badge-success">DASS-21: {{ $response->dass21_total_score }}</span>
                                    </div>
                                @endif
                                @if(!$response->phq9_total_score && !$response->dass21_total_score)
                                    <span class="text-muted">No scores</span>
                                @endif
                            </td>
                            <td>
                                @if($response->overall_risk_level)
                                    <span class="badge badge-{{ getRiskBadgeColor($response->overall_risk_level) }}">
                                        {{ $response->overall_risk_level }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-{{ $response->quiz_status == 'completed' ? 'success' : 'warning' }}">
                                    {{ $response->quiz_status == 'completed' ? 'Completed' : 'Incomplete' }}
                                </span>
                            </td>
                            <td>
                                @if($response->completed_at)
                                    <small>{{ $response->completed_at->format('d M Y H:i') }}</small>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.quiz-responses.show', $response) }}"
                                       class="btn btn-primary btn-sm" title="View Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if(in_array($response->overall_risk_level, ['High', 'Critical']))
                                        <button class="btn btn-warning btn-sm"
                                                onclick="flagForFollowup({{ $response->id }})" title="Flag for Follow-up">
                                            <i class="fas fa-flag"></i>
                                        </button>
                                    @endif
                                    <a href="{{ route('admin.quiz-responses.edit', $response) }}"
                                       class="btn btn-info btn-sm" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button class="btn btn-danger btn-sm"
                                            onclick="deleteResponse({{ $response->id }})" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>
                    Showing {{ $responses->firstItem() }} to {{ $responses->lastItem() }}
                    of {{ $responses->total() }} entries
                </div>
                {{ $responses->appends(request()->query())->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-inbox fa-3x text-gray-300 mb-3"></i>
                <h5 class="text-gray-600">No data found</h5>
                <p class="text-gray-500">Try changing your search filters</p>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
// Select all functionality
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.row-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
    updateBulkDeleteButton();
});

document.getElementById('selectAllHeader').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.row-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
    updateBulkDeleteButton();
});

// Individual checkbox change
document.querySelectorAll('.row-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', updateBulkDeleteButton);
});

function updateBulkDeleteButton() {
    const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
    const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');

    if (checkedBoxes.length > 0) {
        bulkDeleteBtn.style.display = 'inline-block';
        bulkDeleteBtn.textContent = `Delete Selected (${checkedBoxes.length})`;
    } else {
        bulkDeleteBtn.style.display = 'none';
    }
}

function bulkDelete() {
    const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
    if (checkedBoxes.length === 0) {
        alert('Please select responses to delete');
        return;
    }

    if (confirm(`Are you sure you want to delete ${checkedBoxes.length} selected responses?`)) {
        const ids = Array.from(checkedBoxes).map(cb => cb.value);

        fetch('{{ route("admin.quiz-responses.bulk-delete") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ ids: ids })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('Failed to delete responses');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred');
        });
    }
}

function deleteResponse(id) {
    if (confirm('Are you sure you want to delete this response?')) {
        fetch(`{{ url('admin/quiz-responses') }}/${id}`, {
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
                alert(data.message);
                location.reload();
            } else {
                alert('Failed to delete response');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred');
        });
    }
}

function flagForFollowup(id) {
    if (confirm('Flag this response for follow-up?')) {
        fetch(`{{ url('admin/quiz-responses') }}/${id}/flag`, {
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
                alert(data.message);
            } else {
                alert('Failed to flag response');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred');
        });
    }
}
</script>
@endpush