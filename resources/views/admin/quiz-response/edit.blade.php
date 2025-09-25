@extends('layouts.admin')

@section('title', 'Edit Quiz Response - Admin Dashboard')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Edit Response #{{ $quizResponse->id }}</h1>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.quiz-responses.show', $quizResponse) }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Back to Detail
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Edit Basic Information</h6>
            </div>
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.quiz-responses.update', $quizResponse) }}">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="full_name">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('full_name') is-invalid @enderror"
                                       id="full_name" name="full_name"
                                       value="{{ old('full_name', $quizResponse->full_name) }}" required>
                                @error('full_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                       id="email" name="email"
                                       value="{{ old('email', $quizResponse->email) }}">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('phone') is-invalid @enderror"
                               id="phone" name="phone"
                               value="{{ old('phone', $quizResponse->phone) }}" required>
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-2"></i>
                            Save Changes
                        </button>
                        <a href="{{ route('admin.quiz-responses.show', $quizResponse) }}" class="btn btn-secondary ml-2">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Response Information</h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless table-sm">
                    <tr>
                        <td><strong>NIM:</strong></td>
                        <td>{{ $quizResponse->nim }}</td>
                    </tr>
                    <tr>
                        <td><strong>Faculty:</strong></td>
                        <td>{{ $quizResponse->faculty->name }}</td>
                    </tr>
                    <tr>
                        <td><strong>Department:</strong></td>
                        <td>{{ $quizResponse->department->name }}</td>
                    </tr>
                    <tr>
                        <td><strong>Student Year:</strong></td>
                        <td>{{ $quizResponse->student_year }}</td>
                    </tr>
                    <tr>
                        <td><strong>Status:</strong></td>
                        <td>
                            <span class="badge badge-{{ $quizResponse->quiz_status == 'completed' ? 'success' : 'warning' }}">
                                {{ ucfirst($quizResponse->quiz_status) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Risk Level:</strong></td>
                        <td>
                            @if($quizResponse->overall_risk_level)
                                <span class="badge badge-{{ getRiskBadgeColor($quizResponse->overall_risk_level) }}">
                                    {{ $quizResponse->overall_risk_level }}
                                </span>
                            @else
                                <span class="text-muted">Not assessed</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="card shadow mb-4 border-left-warning">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-warning">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Important Note
                </h6>
            </div>
            <div class="card-body">
                <p class="text-sm mb-0">
                    Only basic contact information can be edited. Assessment responses and scores
                    cannot be modified to maintain data integrity.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection