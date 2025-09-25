@extends('layouts.admin')

@section('title', 'Create Quiz Response - Admin Dashboard')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Create New Response</h1>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.quiz-responses.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Basic Information</h6>
            </div>
            <div class="card-body">
                <div class="alert alert-info" role="alert">
                    <i class="fas fa-info-circle mr-2"></i>
                    <strong>Note:</strong> This feature is primarily for administrative purposes.
                    Regular quiz responses should be created through the public screening interface.
                </div>

                <form method="POST" action="{{ route('admin.quiz-responses.store') }}">
                    @csrf

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="student_year">Student Year <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('student_year') is-invalid @enderror"
                                       id="student_year" name="student_year"
                                       value="{{ old('student_year', date('Y')) }}"
                                       min="2020" max="{{ date('Y') + 1 }}" required>
                                @error('student_year')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nim">NIM <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('nim') is-invalid @enderror"
                                       id="nim" name="nim"
                                       value="{{ old('nim') }}" required>
                                @error('nim')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="full_name">Full Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('full_name') is-invalid @enderror"
                               id="full_name" name="full_name"
                               value="{{ old('full_name') }}" required>
                        @error('full_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="faculty_id">Faculty <span class="text-danger">*</span></label>
                                <select name="faculty_id" id="faculty_id" class="form-control @error('faculty_id') is-invalid @enderror" required>
                                    <option value="">Select Faculty</option>
                                    @foreach($faculties as $faculty)
                                        <option value="{{ $faculty->id }}" {{ old('faculty_id') == $faculty->id ? 'selected' : '' }}>
                                            {{ $faculty->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('faculty_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="department_id">Department <span class="text-danger">*</span></label>
                                <select name="department_id" id="department_id" class="form-control @error('department_id') is-invalid @enderror" required>
                                    <option value="">Select Department</option>
                                </select>
                                @error('department_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                       id="email" name="email"
                                       value="{{ old('email') }}">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="phone">Phone <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                       id="phone" name="phone"
                                       value="{{ old('phone') }}" required>
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-2"></i>
                            Create Response
                        </button>
                        <a href="{{ route('admin.quiz-responses.index') }}" class="btn btn-secondary ml-2">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card shadow mb-4 border-left-info">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-info">
                    <i class="fas fa-lightbulb mr-2"></i>
                    Information
                </h6>
            </div>
            <div class="card-body">
                <h6 class="text-gray-800">Manual Response Creation</h6>
                <p class="text-sm mb-2">
                    This form allows you to manually create a quiz response record. This is typically used for:
                </p>
                <ul class="text-sm">
                    <li>Administrative corrections</li>
                    <li>Data migration purposes</li>
                    <li>Testing scenarios</li>
                </ul>

                <h6 class="text-gray-800 mt-3">Next Steps</h6>
                <p class="text-sm mb-0">
                    After creating a response, you can view and edit the details, but assessment
                    scores will need to be completed through the regular screening process.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Department dropdown based on faculty selection
document.getElementById('faculty_id').addEventListener('change', function() {
    const facultyId = this.value;
    const departmentSelect = document.getElementById('department_id');

    // Clear existing options
    departmentSelect.innerHTML = '<option value="">Select Department</option>';

    if (!facultyId) return;

    // Fetch departments for selected faculty
    fetch(`{{ url('/departments') }}/${facultyId}`)
        .then(response => response.json())
        .then(departments => {
            departments.forEach(department => {
                const option = document.createElement('option');
                option.value = department.id;
                option.textContent = department.name;
                if ('{{ old("department_id") }}' == department.id) {
                    option.selected = true;
                }
                departmentSelect.appendChild(option);
            });
        })
        .catch(error => {
            console.error('Error fetching departments:', error);
        });
});

// Trigger change event on page load if faculty is pre-selected
document.addEventListener('DOMContentLoaded', function() {
    const facultySelect = document.getElementById('faculty_id');
    if (facultySelect.value) {
        facultySelect.dispatchEvent(new Event('change'));
    }
});
</script>
@endpush