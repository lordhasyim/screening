@extends('layouts.admin')

@section('title', 'Quiz Response Detail - Admin Dashboard')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Response Detail #{{ $quizResponse->id }}</h1>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.quiz-responses.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
        <a href="{{ route('admin.quiz-responses.edit', $quizResponse) }}" class="btn btn-info btn-sm">
            <i class="fas fa-edit"></i> Edit
        </a>
        <button class="btn btn-danger btn-sm" onclick="deleteResponse({{ $quizResponse->id }})">
            <i class="fas fa-trash"></i> Delete
        </button>
    </div>
</div>

<!-- Assessment Summary Cards -->
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
                        <div class="text-xs">{{ $quizResponse->phq9_category ?? 'Not completed' }}</div>
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
                        <div class="text-xs">{{ $quizResponse->dass21_category ?? 'Not completed' }}</div>
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
                        <div class="text-xs">Overall Assessment</div>
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
                        <div class="text-xs">{{ $quizResponse->completed_at ? $quizResponse->completed_at->format('d/m/Y H:i') : 'Not completed' }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clipboard-check fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Personal Information & Assessment Results -->
<div class="row">
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Personal Information</h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <td><strong>NIM</strong></td>
                        <td>{{ $quizResponse->nim }}</td>
                    </tr>
                    <tr>
                        <td><strong>Full Name</strong></td>
                        <td>{{ $quizResponse->full_name }}</td>
                    </tr>
                    <tr>
                        <td><strong>Gender</strong></td>
                        <td>{{ $quizResponse->gender }}</td>
                    </tr>
                    <tr>
                        <td><strong>Birth Place & Date</strong></td>
                        <td>{{ $quizResponse->birth_place }}, {{ $quizResponse->birth_date->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <td><strong>Age</strong></td>
                        <td>{{ $quizResponse->age }} years old</td>
                    </tr>
                    <tr>
                        <td><strong>Faculty</strong></td>
                        <td>{{ $quizResponse->faculty->name }}</td>
                    </tr>
                    <tr>
                        <td><strong>Department</strong></td>
                        <td>{{ $quizResponse->department->name }}</td>
                    </tr>
                    <tr>
                        <td><strong>Student Year</strong></td>
                        <td>{{ $quizResponse->student_year }}</td>
                    </tr>
                    <tr>
                        <td><strong>Email</strong></td>
                        <td>{{ $quizResponse->email ?: 'Not provided' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Phone</strong></td>
                        <td>{{ $quizResponse->phone }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <!-- PHQ-9 Results -->
        @if($scoring['phq9'])
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">PHQ-9 Assessment Results</h6>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <div class="h2 text-primary">{{ $scoring['phq9']['total_score'] }}/{{ $scoring['phq9']['max_score'] }}</div>
                    <div class="badge badge-{{ getRiskBadgeColor($scoring['phq9']['category']) }} badge-pill">
                        {{ $scoring['phq9']['category'] }}
                    </div>
                </div>
                <div class="small text-gray-600 mb-3">
                    {{ $scoring['phq9']['interpretation'] }}
                </div>
                <div class="progress mb-3">
                    <div class="progress-bar bg-primary" role="progressbar"
                         style="width: {{ ($scoring['phq9']['total_score'] / $scoring['phq9']['max_score']) * 100 }}%">
                    </div>
                </div>
                <div class="mt-3">
                    <strong>Completed:</strong><br>
                    <small>{{ $quizResponse->phq9_completed_at->format('d/m/Y H:i') }}</small>
                </div>
            </div>
        </div>
        @endif

        <!-- DASS-21 Results -->
        @if($scoring['dass21'])
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">DASS-21 Assessment Results</h6>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <div class="h2 text-success">{{ $scoring['dass21']['total_score'] }}/{{ $scoring['dass21']['max_score'] }}</div>
                    <div class="badge badge-{{ getRiskBadgeColor($scoring['dass21']['category']) }} badge-pill">
                        {{ $scoring['dass21']['category'] }}
                    </div>
                </div>
                <div class="small text-gray-600 mb-3">
                    {{ $scoring['dass21']['interpretation'] }}
                </div>
                <div class="progress mb-3">
                    <div class="progress-bar bg-success" role="progressbar"
                         style="width: {{ ($scoring['dass21']['total_score'] / $scoring['dass21']['max_score']) * 100 }}%">
                    </div>
                </div>
                <div class="mt-3">
                    <strong>Completed:</strong><br>
                    <small>{{ $quizResponse->dass21_completed_at->format('d/m/Y H:i') }}</small>
                </div>
            </div>
        </div>
        @endif

        <!-- Overall Recommendation -->
        @if($quizResponse->overall_risk_level)
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Recommendation</h6>
            </div>
            <div class="card-body">
                <div class="alert alert-{{ getRiskBadgeColor($quizResponse->overall_risk_level) }}" role="alert">
                    <strong>Risk Level: {{ $quizResponse->overall_risk_level }}</strong><br>
                    <small>{{ getOverallRecommendation($quizResponse->overall_risk_level) }}</small>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Detailed Assessment Breakdown -->
@if($scoring['phq9'] || $scoring['dass21'])
<div class="row">
    @if($scoring['phq9'])
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">PHQ-9 Question Breakdown</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Question</th>
                                <th>Answer</th>
                                <th>Score</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($scoring['phq9']['questions'] as $index => $question)
                            <tr>
                                <td class="small">{{ Str::limit($question['question'], 50) }}</td>
                                <td><span class="badge badge-secondary">{{ $question['answer'] }}</span></td>
                                <td><strong>{{ $question['score'] }}</strong></td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="table-primary">
                                <td><strong>Total</strong></td>
                                <td></td>
                                <td><strong>{{ $scoring['phq9']['total_score'] }}</strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if($scoring['dass21'])
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">DASS-21 Question Breakdown</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                    <table class="table table-sm">
                        <thead class="thead-light">
                            <tr>
                                <th>Question</th>
                                <th>Answer</th>
                                <th>Score</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($scoring['dass21']['questions'] as $index => $question)
                            <tr>
                                <td class="small">{{ Str::limit($question['question'], 40) }}</td>
                                <td><span class="badge badge-secondary">{{ $question['answer'] }}</span></td>
                                <td><strong>{{ $question['score'] }}</strong></td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="thead-light">
                            <tr class="table-success">
                                <td><strong>Total</strong></td>
                                <td></td>
                                <td><strong>{{ $scoring['dass21']['total_score'] }}</strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endif

<!-- Timeline -->
<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Assessment Timeline</h6>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <div class="timeline-item">
                        <i class="fas fa-play bg-primary"></i>
                        <div class="timeline-item-content">
                            <h6>Assessment Started</h6>
                            <small>{{ $quizResponse->started_at->format('d/m/Y H:i') }}</small>
                        </div>
                    </div>
                    @if($quizResponse->phq9_completed_at)
                    <div class="timeline-item">
                        <i class="fas fa-check bg-success"></i>
                        <div class="timeline-item-content">
                            <h6>PHQ-9 Completed</h6>
                            <small>{{ $quizResponse->phq9_completed_at->format('d/m/Y H:i') }}</small>
                        </div>
                    </div>
                    @endif
                    @if($quizResponse->dass21_completed_at)
                    <div class="timeline-item">
                        <i class="fas fa-check bg-success"></i>
                        <div class="timeline-item-content">
                            <h6>DASS-21 Completed</h6>
                            <small>{{ $quizResponse->dass21_completed_at->format('d/m/Y H:i') }}</small>
                        </div>
                    </div>
                    @endif
                    @if($quizResponse->completed_at)
                    <div class="timeline-item">
                        <i class="fas fa-flag bg-info"></i>
                        <div class="timeline-item-content">
                            <h6>Assessment Completed</h6>
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
                window.location.href = '{{ route("admin.quiz-responses.index") }}';
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
</script>
@endpush