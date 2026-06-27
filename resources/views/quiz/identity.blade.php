@extends('layouts.app')

@section('title', 'Identitas Diri - Skrining Kesehatan Mental')

@push('styles')
    <!-- Select2 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2-bootstrap-5-theme/1.3.0/select2-bootstrap-5-theme.min.css"
        rel="stylesheet">
    <!-- Flatpickr -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.css" rel="stylesheet">
    <style>
        /* Custom styles for better form appearance */
        .form-control-pastel,
        .form-select-pastel {
            border: 1px solid #e1e8ed;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .form-control-pastel:focus,
        .form-select-pastel:focus {
            border-color: #4e73df;
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }

        .is-invalid .form-control-pastel,
        .is-invalid .form-select-pastel {
            border-color: #e74a3b;
        }

        .select2-container--bootstrap-5 .select2-selection {
            border: 1px solid #e1e8ed !important;
            border-radius: 8px !important;
            min-height: 38px !important;
        }

        .select2-container--bootstrap-5.select2-container--focus .select2-selection {
            border-color: #4e73df !important;
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25) !important;
        }

        .select2-container--bootstrap-5.is-invalid .select2-selection {
            border-color: #e74a3b !important;
        }

        .select2-container--bootstrap-5.select2-container--disabled .select2-selection {
            background-color: #f8f9fa !important;
            cursor: not-allowed !important;
            opacity: 0.6 !important;
            border-color: #dee2e6 !important;
        }

        /* Never auto-show city error — only JS can reveal it */
        #city-error { display: none !important; }
        #city-error.visible { display: block !important; }

        .loading-spinner {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid #f3f3f3;
            border-top: 2px solid #4e73df;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .error-message {
            color: #e74a3b;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        .success-message {
            color: #1cc88a;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        .form-section {
            border-left: 4px solid #4e73df;
            padding-left: 1rem;
            margin-bottom: 2rem;
        }

        .required-field {
            position: relative;
        }

        .required-field::after {
            content: "*";
            color: #e74a3b;
            margin-left: 3px;
        }
    </style>
@endpush

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-10">

                <!-- Step Navigation -->
                <div class="step-nav fade-in">
                    <div class="step-indicator">
                        <div class="d-flex align-items-center">
                            <div class="step-number">1</div>
                            <div class="ms-3">
                                <div class="step-title">Identitas Diri</div>
                                <small class="text-muted">Lengkapi data pribadi Anda</small>
                            </div>
                        </div>
                        <div class="text-end">
                            <small class="text-muted">Langkah 1 dari 4</small>
                        </div>
                    </div>
                    <div class="progress progress-pastel mt-3">
                        <div class="progress-bar progress-bar-pastel" style="width: 25%;"></div>
                    </div>
                </div>

                <!-- Error Alert -->
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <h6 class="alert-heading"><i class="bi bi-exclamation-triangle me-2"></i>Ada kesalahan dalam
                            pengisian form:</h6>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- Success Alert -->
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- Main Form -->
                <div class="card card-pastel slide-up">
                    <div class="card-body p-4">
                        <form id="identityForm" method="POST" action="{{ route('quiz.identity') }}" novalidate>
                            @csrf

                            <!-- Basic Information Section -->
                            <div class="form-section">
                                <h5 class="mb-4 text-primary">
                                    <i class="bi bi-person-badge me-2"></i>
                                    Informasi Dasar
                                </h5>

                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label for="student_year" class="form-label fw-semibold required-field">
                                            Angkatan
                                        </label>
                                        <select id="student_year" name="student_year"
                                            class="form-select form-select-pastel @error('student_year') is-invalid @enderror"
                                            required>
                                            <option value="">Pilih Angkatan</option>
                                            @for ($year = 2015; $year <= date('Y') + 10; $year++)
                                                <option value="{{ $year }}"
                                                    {{ old('student_year') == $year ? 'selected' : '' }}>
                                                    {{ $year }}
                                                </option>
                                            @endfor
                                        </select>
                                        @error('student_year')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label for="faculty_id" class="form-label fw-semibold required-field">
                                            Fakultas
                                        </label>
                                        <select id="faculty_id" name="faculty_id"
                                            class="form-select form-select-pastel @error('faculty_id') is-invalid @enderror"
                                            required>
                                            <option value="">Pilih Fakultas</option>
                                            @foreach ($faculties as $faculty)
                                                <option value="{{ $faculty->id }}"
                                                    {{ old('faculty_id') == $faculty->id ? 'selected' : '' }}>
                                                    {{ $faculty->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('faculty_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label for="department_id" class="form-label fw-semibold required-field">
                                            Jurusan
                                        </label>
                                        <select id="department_id" name="department_id"
                                            class="form-select form-select-pastel @error('department_id') is-invalid @enderror"
                                            disabled>
                                            <option value="">Pilih Fakultas dulu</option>
                                        </select>
                                        <div id="department-loading" style="display: none;" class="mt-2">
                                            <span class="loading-spinner"></span> Memuat jurusan...
                                        </div>
                                        <div id="department-other-container" style="display: none;" class="mt-2">
                                            <input type="text" id="department_name" name="department_name"
                                                class="form-control form-control-pastel @error('department_name') is-invalid @enderror"
                                                placeholder="Tulis nama jurusan Anda"
                                                value="{{ old('department_name') }}">
                                            @error('department_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        @error('department_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- NEW: Education Level field -->
                                    <div class="col-md-4" id="level-container" style="display: none;">
                                        <label for="education_level" class="form-label fw-semibold required-field">
                                            Jenjang Pendidikan
                                        </label>
                                        <select id="education_level" name="education_level"
                                            class="form-select form-select-pastel @error('education_level') is-invalid @enderror"
                                            required disabled>
                                            <option value="">Pilih Jurusan dulu</option>
                                        </select>
                                        <div id="level-loading" style="display: none;" class="mt-2">
                                            <span class="loading-spinner"></span> Memuat jenjang...
                                        </div>
                                        @error('education_level')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted mt-1 d-block">
                                            <i class="bi bi-info-circle me-1"></i>
                                            D4: Diploma 4 | S1: Sarjana | Pascasarjana: S2/S3
                                        </small>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="nim" class="form-label fw-semibold required-field">
                                            NIM
                                        </label>
                                        <input type="text" id="nim" name="nim"
                                            class="form-control form-control-pastel @error('nim') is-invalid @enderror"
                                            value="{{ old('nim') }}" required maxlength="50"
                                            placeholder="Nomor Induk Mahasiswa">
                                        @error('nim')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="full_name" class="form-label fw-semibold required-field">
                                            Nama Lengkap
                                        </label>
                                        <input type="text" id="full_name" name="full_name"
                                            class="form-control form-control-pastel @error('full_name') is-invalid @enderror"
                                            value="{{ old('full_name') }}" required
                                            placeholder="Nama lengkap sesuai KTP">
                                        @error('full_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Personal Information Section -->
                            <div class="form-section">
                                <h5 class="mb-4 text-primary">
                                    <i class="bi bi-person me-2"></i>
                                    Informasi Pribadi
                                </h5>

                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label for="gender" class="form-label fw-semibold required-field">
                                            Jenis Kelamin
                                        </label>
                                        <select id="gender" name="gender"
                                            class="form-select form-select-pastel @error('gender') is-invalid @enderror"
                                            required>
                                            <option value="">Pilih</option>
                                            <option value="Laki-laki"
                                                {{ old('gender') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                            <option value="Perempuan"
                                                {{ old('gender') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                        </select>
                                        @error('gender')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label for="origin_province_id" class="form-label fw-semibold required-field">
                                            Provinsi Asal
                                        </label>
                                        <select id="origin_province_id" name="origin_province_id"
                                            class="form-select form-select-pastel @error('origin_province_id') is-invalid @enderror"
                                            required>
                                            <option value="">Pilih Provinsi</option>
                                            @foreach ($provinces as $province)
                                                <option value="{{ $province->id }}"
                                                    {{ old('origin_province_id') == $province->id ? 'selected' : '' }}>
                                                    {{ $province->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('origin_province_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label for="origin_city_id" class="form-label fw-semibold required-field">
                                            Kota/Kabupaten Asal
                                        </label>
                                        <select id="origin_city_id" name="origin_city_id"
                                            class="form-select form-select-pastel select2-city"
                                            disabled>
                                            <option value="">Pilih Provinsi dulu</option>
                                        </select>
                                        <div id="city-loading" style="display: none;" class="mt-2">
                                            <span class="loading-spinner"></span> Memuat kota/kabupaten...
                                        </div>
                                        @error('origin_city_id')
                                            <div class="invalid-feedback" id="city-error">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label for="birth_place" class="form-label fw-semibold required-field">
                                            Tempat Lahir
                                        </label>
                                        <input type="text" id="birth_place" name="birth_place"
                                            class="form-control form-control-pastel @error('birth_place') is-invalid @enderror"
                                            value="{{ old('birth_place') }}" required placeholder="Kota tempat lahir">
                                        @error('birth_place')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label for="birth_date_display" class="form-label fw-semibold required-field">
                                            Tanggal Lahir
                                        </label>
                                        <input type="hidden" id="birth_date" name="birth_date" value="{{ old('birth_date') }}">
                                        <div class="input-group">
                                            <input type="text" id="birth_date_display"
                                                class="form-control form-control-pastel @error('birth_date') is-invalid @enderror"
                                                placeholder="Pilih tanggal lahir" readonly autocomplete="off">
                                            <span class="input-group-text" style="cursor:pointer" onclick="document.getElementById('birth_date_display')._flatpickr.open()">
                                                <i class="bi bi-calendar3"></i>
                                            </span>
                                        </div>
                                        @error('birth_date')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label for="phone" class="form-label fw-semibold required-field">
                                            Nomor Telepon/HP
                                        </label>
                                        <input type="text" id="phone" name="phone"
                                            class="form-control form-control-pastel @error('phone') is-invalid @enderror"
                                            value="{{ old('phone') }}" required placeholder="08xxxxxxxxxx"
                                            inputmode="numeric" pattern="[0-9]*" maxlength="20">
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="email" class="form-label fw-semibold">
                                            Email (Opsional)
                                        </label>
                                        <input type="email" id="email" name="email"
                                            class="form-control form-control-pastel @error('email') is-invalid @enderror"
                                            value="{{ old('email') }}" placeholder="email@example.com">
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="religion" class="form-label fw-semibold required-field">
                                            Agama/Kepercayaan
                                        </label>
                                        <select id="religion" name="religion"
                                            class="form-select form-select-pastel @error('religion') is-invalid @enderror"
                                            required>
                                            <option value="">Pilih</option>
                                            <option value="Islam" {{ old('religion') == 'Islam' ? 'selected' : '' }}>
                                                Islam</option>
                                            <option value="Kristen" {{ old('religion') == 'Kristen' ? 'selected' : '' }}>
                                                Kristen Protestan</option>
                                            <option value="Katolik" {{ old('religion') == 'Katolik' ? 'selected' : '' }}>
                                                Katolik</option>
                                            <option value="Hindu" {{ old('religion') == 'Hindu' ? 'selected' : '' }}>
                                                Hindu</option>
                                            <option value="Budha" {{ old('religion') == 'Budha' ? 'selected' : '' }}>
                                                Buddha</option>
                                            <option value="Konghucu"
                                                {{ old('religion') == 'Konghucu' ? 'selected' : '' }}>Konghucu</option>
                                        </select>
                                        @error('religion')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12">
                                        <label for="address" class="form-label fw-semibold required-field">
                                            Alamat Domisili
                                        </label>
                                        <textarea id="address" name="address"
                                            class="form-control form-control-pastel @error('address') is-invalid @enderror" rows="3" required
                                            placeholder="Alamat lengkap tempat tinggal saat ini">{{ old('address') }}</textarea>
                                        @error('address')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Living & Origin Information -->
                            <div class="form-section">
                                <h5 class="mb-4 text-primary">
                                    <i class="bi bi-geo-alt me-2"></i>
                                    Informasi Tempat Tinggal & Asal
                                </h5>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="living_arrangement" class="form-label fw-semibold required-field">
                                            Status Tempat Tinggal
                                        </label>
                                        <select id="living_arrangement" name="living_arrangement"
                                            class="form-select form-select-pastel @error('living_arrangement') is-invalid @enderror"
                                            required>
                                            <option value="">Pilih</option>
                                            <option value="Kos"
                                                {{ old('living_arrangement') == 'Kos' ? 'selected' : '' }}>Kos</option>
                                            <option value="Rumah orang tua"
                                                {{ old('living_arrangement') == 'Rumah orang tua' ? 'selected' : '' }}>
                                                Rumah orang tua</option>
                                            <option value="Rumah keluarga"
                                                {{ old('living_arrangement') == 'Rumah keluarga' ? 'selected' : '' }}>Rumah
                                                keluarga</option>
                                            <option value="Asrama"
                                                {{ old('living_arrangement') == 'Asrama' ? 'selected' : '' }}>Asrama
                                            </option>
                                            <option value="Kontrak"
                                                {{ old('living_arrangement') == 'Kontrak' ? 'selected' : '' }}>Kontrak
                                            </option>
                                        </select>
                                        @error('living_arrangement')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="origin_area_type" class="form-label fw-semibold required-field">
                                            Tipe Daerah Asal
                                        </label>
                                        <select id="origin_area_type" name="origin_area_type"
                                            class="form-select form-select-pastel @error('origin_area_type') is-invalid @enderror"
                                            required>
                                            <option value="">Pilih</option>
                                            <option value="perkotaan"
                                                {{ old('origin_area_type') == 'perkotaan' ? 'selected' : '' }}>
                                                Perkotaan/Urban</option>
                                            <option value="pedesaan"
                                                {{ old('origin_area_type') == 'pedesaan' ? 'selected' : '' }}>
                                                Pedesaan/Rural</option>
                                            <option value="pinggiran kota"
                                                {{ old('origin_area_type') == 'pinggiran kota' ? 'selected' : '' }}>
                                                Pinggiran Kota/Suburban</option>
                                            <option value="daerah terpencil"
                                                {{ old('origin_area_type') == 'daerah terpencil' ? 'selected' : '' }}>
                                                Daerah Terpencil/Terisolasi</option>
                                            <option value="daerah industri"
                                                {{ old('origin_area_type') == 'daerah industri' ? 'selected' : '' }}>Daerah
                                                Industri</option>
                                        </select>
                                        @error('origin_area_type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Family Information -->
                            <div class="form-section">
                                <h5 class="mb-4 text-primary">
                                    <i class="bi bi-people me-2"></i>
                                    Informasi Keluarga
                                </h5>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="parents_marital_status" class="form-label fw-semibold required-field">
                                            Status Pernikahan Orang Tua
                                        </label>
                                        <select id="parents_marital_status" name="parents_marital_status"
                                            class="form-select form-select-pastel @error('parents_marital_status') is-invalid @enderror"
                                            required>
                                            <option value="">Pilih</option>
                                            <option value="menikah"
                                                {{ old('parents_marital_status') == 'menikah' ? 'selected' : '' }}>Menikah
                                            </option>
                                            <option value="cerai hidup"
                                                {{ old('parents_marital_status') == 'cerai hidup' ? 'selected' : '' }}>
                                                Cerai Hidup</option>
                                            <option value="cerai mati"
                                                {{ old('parents_marital_status') == 'cerai mati' ? 'selected' : '' }}>Cerai
                                                Mati/Meninggal</option>
                                            <option value="pisah tidak resmi"
                                                {{ old('parents_marital_status') == 'pisah tidak resmi' ? 'selected' : '' }}>
                                                Pisah Tidak Resmi</option>
                                            <option value="menikah lagi"
                                                {{ old('parents_marital_status') == 'menikah lagi' ? 'selected' : '' }}>
                                                Menikah Lagi</option>
                                        </select>
                                        @error('parents_marital_status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-3">
                                        <label for="child_order" class="form-label fw-semibold required-field">
                                            Anak ke-
                                        </label>
                                        <input type="number" id="child_order" name="child_order"
                                            class="form-control form-control-pastel @error('child_order') is-invalid @enderror"
                                            value="{{ old('child_order') }}" required min="1" max="20">
                                        @error('child_order')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-3">
                                        <label for="siblings_count" class="form-label fw-semibold required-field">
                                            Dari berapa bersaudara
                                        </label>
                                        <input type="number" id="siblings_count" name="siblings_count"
                                            class="form-control form-control-pastel @error('siblings_count') is-invalid @enderror"
                                            value="{{ old('siblings_count') }}" required min="1" max="20">
                                        @error('siblings_count')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="scholarship" class="form-label fw-semibold">
                                            Beasiswa (Opsional)
                                        </label>
                                        <input type="text" id="scholarship" name="scholarship"
                                            class="form-control form-control-pastel @error('scholarship') is-invalid @enderror"
                                            value="{{ old('scholarship') }}" placeholder="KIP, Beasiswa Prestasi, dll">
                                        @error('scholarship')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="admission_path" class="form-label fw-semibold required-field">
                                            Jalur Masuk Mahasiswa
                                        </label>
                                        <select id="admission_path" name="admission_path"
                                            class="form-select form-select-pastel @error('admission_path') is-invalid @enderror"
                                            required>
                                            <option value="">Pilih</option>
                                            <option value="SNBP"
                                                {{ old('admission_path') == 'SNBP' ? 'selected' : '' }}>SNBP (Prestasi)
                                            </option>
                                            <option value="SNBT"
                                                {{ old('admission_path') == 'SNBT' ? 'selected' : '' }}>SNBT (Tes)</option>
                                            <option value="Mandiri"
                                                {{ old('admission_path') == 'Mandiri' ? 'selected' : '' }}>Mandiri</option>
                                            <option value="Lainnya"
                                                {{ old('admission_path') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                                        </select>
                                        @error('admission_path')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label for="parents_education" class="form-label fw-semibold required-field">
                                            Pendidikan Terakhir Orang Tua
                                        </label>
                                        <select id="parents_education" name="parents_education"
                                            class="form-select form-select-pastel @error('parents_education') is-invalid @enderror"
                                            required>
                                            <option value="">Pilih</option>
                                            <option value="SD"
                                                {{ old('parents_education') == 'SD' ? 'selected' : '' }}>SD/Sederajat
                                            </option>
                                            <option value="SMP"
                                                {{ old('parents_education') == 'SMP' ? 'selected' : '' }}>SMP/Sederajat
                                            </option>
                                            <option value="SMA"
                                                {{ old('parents_education') == 'SMA' ? 'selected' : '' }}>SMA/Sederajat
                                            </option>
                                            <option value="D3"
                                                {{ old('parents_education') == 'D3' ? 'selected' : '' }}>D3</option>
                                            <option value="S1"
                                                {{ old('parents_education') == 'S1' ? 'selected' : '' }}>S1</option>
                                            <option value="S2"
                                                {{ old('parents_education') == 'S2' ? 'selected' : '' }}>S2</option>
                                            <option value="S3"
                                                {{ old('parents_education') == 'S3' ? 'selected' : '' }}>S3</option>
                                        </select>
                                        @error('parents_education')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label for="parents_income" class="form-label fw-semibold required-field">
                                            Penghasilan Orang Tua
                                        </label>
                                        <select id="parents_income" name="parents_income"
                                            class="form-select form-select-pastel @error('parents_income') is-invalid @enderror"
                                            required>
                                            <option value="">Pilih</option>
                                            <option value="<2000000"
                                                {{ old('parents_income') == '<2000000' ? 'selected' : '' }}>
                                                < Rp 2.000.000</option>
                                            <option value="2000000-5000000"
                                                {{ old('parents_income') == '2000000-5000000' ? 'selected' : '' }}>Rp
                                                2.000.000 - 5.000.000</option>
                                            <option value="5000000-10000000"
                                                {{ old('parents_income') == '5000000-10000000' ? 'selected' : '' }}>Rp
                                                5.000.000 - 10.000.000</option>
                                            <option value=">10000000"
                                                {{ old('parents_income') == '>10000000' ? 'selected' : '' }}>> Rp
                                                10.000.000</option>
                                        </select>
                                        @error('parents_income')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label for="family_members_count" class="form-label fw-semibold required-field">
                                            Jumlah Anggota Keluarga di Rumah
                                        </label>
                                        <input type="number" id="family_members_count" name="family_members_count"
                                            class="form-control form-control-pastel @error('family_members_count') is-invalid @enderror"
                                            value="{{ old('family_members_count') }}" required min="1"
                                            max="20" placeholder="Termasuk Anda">
                                        @error('family_members_count')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Medical History (Collapsed by default) -->
                            <div class="form-section">
                                <h5 class="mb-4 text-primary">
                                    <i class="bi bi-shield-plus me-2"></i>
                                    Riwayat Kesehatan
                                    <button type="button" class="btn btn-sm btn-outline-secondary ms-2"
                                        data-bs-toggle="collapse" data-bs-target="#medicalHistory" aria-expanded="false">
                                        <i class="bi bi-chevron-down"></i>
                                    </button>
                                </h5>

                                <div id="medicalHistory" class="collapse">
                                    <div class="alert alert-info">
                                        <i class="bi bi-info-circle me-2"></i>
                                        <strong>Bagian Opsional:</strong> Informasi kesehatan membantu kami memberikan
                                        dukungan yang lebih tepat. Anda dapat melewati bagian ini jika tidak ingin mengisi.
                                    </div>

                                    <div class="row g-3">
                                        <!-- Chronic Disease -->
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="has_chronic_disease"
                                                    name="has_chronic_disease" value="1"
                                                    {{ old('has_chronic_disease') ? 'checked' : '' }}>
                                                <label class="form-check-label fw-semibold" for="has_chronic_disease">
                                                    Memiliki riwayat penyakit kronis
                                                </label>
                                            </div>
                                            <textarea id="chronic_disease_details" name="chronic_disease_details" class="form-control form-control-pastel mt-2"
                                                rows="2" placeholder="Sebutkan jenis penyakit (Maag, Asma, Lupus, Epilepsi, dll)" style="display: none;">{{ old('chronic_disease_details') }}</textarea>
                                        </div>

                                        <!-- Current Medication -->
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="current_medication"
                                                    name="current_medication" value="1"
                                                    {{ old('current_medication') ? 'checked' : '' }}>
                                                <label class="form-check-label fw-semibold" for="current_medication">
                                                    Sedang menjalani pengobatan medis
                                                </label>
                                            </div>
                                            <textarea id="medication_details" name="medication_details" class="form-control form-control-pastel mt-2"
                                                rows="2" placeholder="Sebutkan obat yang dikonsumsi" style="display: none;">{{ old('medication_details') }}</textarea>
                                        </div>

                                        <!-- Head Injury -->
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="head_injury_history"
                                                    name="head_injury_history" value="1"
                                                    {{ old('head_injury_history') ? 'checked' : '' }}>
                                                <label class="form-check-label fw-semibold" for="head_injury_history">
                                                    Pernah mengalami cedera kepala atau trauma fisik berat
                                                </label>
                                            </div>
                                            <textarea id="injury_details" name="injury_details" class="form-control form-control-pastel mt-2" rows="2"
                                                placeholder="Jelaskan secara singkat" style="display: none;">{{ old('injury_details') }}</textarea>
                                        </div>

                                        <!-- Substance Use -->
                                        <div class="col-md-6">
                                            <label for="substance_use" class="form-label fw-semibold">
                                                Pola konsumsi zat (alkohol, rokok, narkotika, dll)
                                            </label>
                                            <select id="substance_use" name="substance_use"
                                                class="form-select form-select-pastel @error('substance_use') is-invalid @enderror">
                                                <option value="Tidak Pernah"
                                                    {{ old('substance_use', 'Tidak Pernah') == 'Tidak Pernah' ? 'selected' : '' }}>
                                                    Tidak Pernah</option>
                                                <option value="Pernah"
                                                    {{ old('substance_use') == 'Pernah' ? 'selected' : '' }}>Pernah
                                                </option>
                                                <option value="Masih aktif"
                                                    {{ old('substance_use') == 'Masih aktif' ? 'selected' : '' }}>Masih
                                                    aktif</option>
                                            </select>
                                            <textarea id="substance_details" name="substance_details" class="form-control form-control-pastel mt-2"
                                                rows="2" placeholder="Jelaskan jenis, frekuensi, dan durasi (jika ada)" style="display: none;">{{ old('substance_details') }}</textarea>
                                            @error('substance_use')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Psychological Treatment -->
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox"
                                                    id="psychological_treatment_history"
                                                    name="psychological_treatment_history" value="1"
                                                    {{ old('psychological_treatment_history') ? 'checked' : '' }}>
                                                <label class="form-check-label fw-semibold"
                                                    for="psychological_treatment_history">
                                                    Pernah mendapat perawatan dari psikolog atau psikiater
                                                </label>
                                            </div>
                                            <textarea id="treatment_details" name="treatment_details" class="form-control form-control-pastel mt-2"
                                                rows="2" placeholder="Jelaskan bentuk perawatan" style="display: none;">{{ old('treatment_details') }}</textarea>
                                        </div>

                                        <!-- Family Mental Health -->
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox"
                                                    id="family_mental_health_history" name="family_mental_health_history"
                                                    value="1"
                                                    {{ old('family_mental_health_history') ? 'checked' : '' }}>
                                                <label class="form-check-label fw-semibold"
                                                    for="family_mental_health_history">
                                                    Ada anggota keluarga dengan riwayat gangguan kesehatan mental
                                                </label>
                                            </div>
                                            <textarea id="family_history_details" name="family_history_details" class="form-control form-control-pastel mt-2"
                                                rows="2" placeholder="Sebutkan hubungan keluarga dan kondisinya" style="display: none;">{{ old('family_history_details') }}</textarea>
                                        </div>

                                        <!-- Family Relationship -->
                                        <div class="col-12">
                                            <label for="family_relationship_description" class="form-label fw-semibold">
                                                Bagaimana hubungan Anda dengan keluarga inti? (Opsional)
                                            </label>
                                            <textarea id="family_relationship_description" name="family_relationship_description"
                                                class="form-control form-control-pastel" rows="3"
                                                placeholder="Jelaskan secara singkat hubungan dengan orang tua dan saudara">{{ old('family_relationship_description') }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Form Navigation -->
                            <div class="d-flex justify-content-between align-items-center pt-4 border-top">
                                <div>
                                    <a href="{{ route('quiz.index') }}" class="btn btn-secondary">
                                        <i class="bi bi-arrow-left me-2"></i>
                                        Kembali
                                    </a>
                                </div>
                                <div class="d-flex align-items-center">
                                    <span class="d-none d-sm-inline-flex align-items-center me-2">
                                        <div id="form-progress" style="display: none;">
                                            <small class="text-muted">
                                                <span id="filled-fields">0</span> dari <span id="total-fields">0</span> field
                                                terisi
                                            </small>
                                        </div>
                                    </span>
                                    <button type="submit" id="submitForm" class="btn btn-primary" disabled>
                                        <span id="submit-text">
                                            Lanjut ke Skrining
                                            <i class="bi bi-arrow-right ms-2"></i>
                                        </span>
                                        <span id="submit-loading" style="display: none;">
                                            <span class="loading-spinner"></span>
                                            Menyimpan...
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <!-- Select2 JS -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
        <!-- Flatpickr -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/l10n/id.min.js"></script>

        <script>
            $(document).ready(function() {
                // Initialize Select2 for city dropdown — only once, never destroy/reinit
                $('#origin_city_id').select2({
                    theme: 'bootstrap-5',
                    placeholder: 'Pilih Kota/Kabupaten',
                    allowClear: true,
                    width: '100%',
                    minimumResultsForSearch: 5
                });

                // Initialize form validation
                validateForm();

                // Real-time form validation
                $('#identityForm input, #identityForm select, #identityForm textarea').on('input change', function() {
                    validateForm();
                });

                // Faculty change handler with better error handling
                $('#faculty_id').on('change', function() {
                    const facultyId = $(this).val();
                    const $departmentSelect = $('#department_id');
                    const $loading = $('#department-loading');

                    // Reset department select
                    $departmentSelect.prop('disabled', true).html('<option value="">Pilih Fakultas dulu</option>');
                    $('#department-other-container').hide();
                    $('#department_name').prop('required', false).val('');

                    if (facultyId) {
                        $loading.show();
                        $departmentSelect.removeClass('is-invalid');

                        $.ajax({
                            url: `/api/quiz/departments/${facultyId}`,
                            method: 'GET',
                            timeout: 10000,
                            success: function(departments) {
                                $loading.hide();

                                let options = '<option value="">Pilih Jurusan</option>';
                                options += '<option value="other" {{ old("department_id") === "other" ? "selected" : "" }}>-- Jurusan saya tidak ada dalam daftar --</option>';

                                if (Array.isArray(departments) && departments.length > 0) {
                                    options += '<option value="" disabled>────────────────────</option>';
                                    departments.forEach(function(dept) {
                                        const selected = "{{ old('department_id') }}" == dept.id ? 'selected' : '';
                                        const name = String(dept.name || '').replace(/</g, '&lt;').replace(/>/g, '&gt;');
                                        options += '<option value="' + dept.id + '" ' + selected + '>' + name + '</option>';
                                    });
                                }

                                $departmentSelect.html(options).prop('disabled', false).trigger('change');
                            },
                            error: function(xhr, status, error) {
                                $loading.hide();
                                let options = '<option value="">Pilih Jurusan</option>';
                                options += '<option value="other">-- Jurusan saya tidak ada dalam daftar --</option>';
                                $departmentSelect.html(options).prop('disabled', false);

                                let errorMessage = 'Gagal memuat data jurusan. ';
                                if (status === 'timeout') {
                                    errorMessage += 'Koneksi timeout.';
                                } else if (xhr.status >= 500) {
                                    errorMessage += 'Terjadi kesalahan server.';
                                } else {
                                    errorMessage += 'Periksa koneksi internet Anda.';
                                }
                                showNotification(errorMessage, 'error');
                            }
                        });
                    } else {
                        $loading.hide();
                    }

                    validateForm();
                });

                // Department change handler - Load education levels
                $('#department_id').on('change', function() {
                    const departmentId = $(this).val();
                    const $levelSelect = $('#education_level');
                    const $levelContainer = $('#level-container');
                    const $levelLoading = $('#level-loading');
                    const $otherContainer = $('#department-other-container');
                    const $otherInput = $('#department_name');

                    if (departmentId === 'other') {
                        // Show the free-text input, hide education level
                        $levelSelect.prop('disabled', true).val('').html('<option value="">Pilih Jurusan dulu</option>');
                        $levelContainer.hide();
                        $otherContainer.show();
                        $otherInput.prop('required', true);
                        validateForm();
                        return;
                    }

                    $otherContainer.hide();
                    $otherInput.prop('required', false).val('');

                    // Reset level select
                    $levelSelect.prop('disabled', true).html('<option value="">Pilih Jurusan dulu</option>');
                    $levelContainer.hide();

                    if (departmentId) {
                        $levelLoading.show();
                        $levelSelect.removeClass('is-invalid');

                        $.ajax({
                            url: `/api/quiz/departments/${departmentId}/levels`,
                            method: 'GET',
                            timeout: 10000,
                            success: function(levels) {
                                $levelLoading.hide();

                                if (levels && levels.length > 0) {
                                    // If only one level, auto-select it
                                    if (levels.length === 1) {
                                        $levelSelect.html(
                                            `<option value="${levels[0].value}" selected>${levels[0].label}</option>`
                                            );
                                        $levelSelect.prop('disabled', false);
                                        $levelContainer.show();

                                        showNotification(
                                            `Jenjang otomatis dipilih: ${levels[0].label}`,
                                            'info');
                                    } else {
                                        // Multiple levels - show dropdown
                                        let options =
                                            '<option value="">Pilih Jenjang Pendidikan</option>';
                                        levels.forEach(function(level) {
                                            const selected =
                                                "{{ old('education_level') }}" == level
                                                .value ? 'selected' : '';
                                            options +=
                                                `<option value="${level.value}" ${selected}>${level.label}</option>`;
                                        });
                                        $levelSelect.html(options).prop('disabled', false);
                                        $levelContainer.show();
                                    }

                                    // Restore old value if exists
                                    if ("{{ old('education_level') }}") {
                                        $levelSelect.val("{{ old('education_level') }}").trigger(
                                            'change');
                                    }
                                } else {
                                    // No levels found - default to S1
                                    $levelSelect.html(
                                        '<option value="S1" selected>S1 (Sarjana)</option>');
                                    $levelSelect.prop('disabled', false);
                                    $levelContainer.show();
                                }

                                validateForm(); // Revalidate form
                            },
                            error: function(xhr, status, error) {
                                $levelLoading.hide();
                                $levelSelect.html('<option value="">Error memuat data</option>');

                                let errorMessage = 'Gagal memuat jenjang pendidikan. ';
                                if (status === 'timeout') {
                                    errorMessage += 'Koneksi timeout.';
                                } else {
                                    errorMessage += 'Periksa koneksi internet Anda.';
                                }

                                showNotification(errorMessage, 'error');
                            }
                        });
                    } else {
                        $levelLoading.hide();
                        $levelContainer.hide();
                    }

                    validateForm();
                });

                // Level change handler - trigger validation
                $('#education_level').on('change', function() {
                    validateForm();
                });

                // Province change handler with better error handling
                $('#origin_province_id').on('change', function() {
                    const provinceId = $(this).val();
                    const $citySelect = $('#origin_city_id');
                    const $loading = $('#city-loading');

                    // Reset and disable city select
                    $citySelect.val(null).trigger('change');
                    $citySelect.prop('disabled', true);

                    if (provinceId) {
                        $loading.show();
                        $citySelect.removeClass('is-invalid');
                        $citySelect.html('<option value="">Loading...</option>');

                        $.ajax({
                            url: `/api/quiz/cities/${provinceId}`,
                            method: 'GET',
                            timeout: 10000,
                            success: function(cities) {
                                $loading.hide();

                                if (cities && cities.length > 0) {
                                    let options = '<option value="">Pilih Kota/Kabupaten</option>';
                                    cities.forEach(function(city) {
                                        const selected = "{{ old('origin_city_id') }}" ==
                                            city.id ? 'selected' : '';
                                        options +=
                                            `<option value="${city.id}" ${selected}>${city.name}</option>`;
                                    });
                                    $citySelect.html(options).prop('disabled', false).trigger('change');

                                    // Restore old value if exists
                                    if ("{{ old('origin_city_id') }}") {
                                        $citySelect.val("{{ old('origin_city_id') }}").trigger('change');
                                    }

                                    // Show city error only now (after cities loaded) if city was still not selected
                                } else {
                                    $citySelect.html('<option value="">Tidak ada kota/kabupaten tersedia</option>').trigger('change');
                                    showNotification('Tidak ada kota/kabupaten tersedia untuk provinsi ini', 'warning');
                                }
                            },
                            error: function(xhr, status, error) {
                                $loading.hide();

                                let errorMessage = 'Gagal memuat data kota/kabupaten. ';
                                if (status === 'timeout') {
                                    errorMessage += 'Koneksi timeout.';
                                } else if (xhr.status === 404) {
                                    errorMessage += 'Data tidak ditemukan.';
                                } else if (xhr.status >= 500) {
                                    errorMessage += 'Terjadi kesalahan server.';
                                } else {
                                    errorMessage += 'Periksa koneksi internet Anda.';
                                }

                                $citySelect.html('<option value="">Error memuat data</option>').trigger('change');
                                showNotification(errorMessage, 'error');
                            }
                        });
                    } else {
                        $loading.hide();
                        $citySelect.html('<option value="">Pilih Provinsi dulu</option>').trigger('change');
                    }

                    validateForm();
                });

                // City select change handler
                $('#origin_city_id').on('change', function() {
                    if ($(this).val()) {
                        $(this).next('.select2-container').removeClass('is-invalid');
                        $('#city-error').removeClass('visible');
                    }
                    // Auto-fill birth_place with selected city
                    const selectedCityText = $(this).find('option:selected').text();
                    if (selectedCityText && selectedCityText !== 'Pilih Kota/Kabupaten' && selectedCityText !==
                        'Pilih Provinsi dulu') {
                        if (!$('#birth_place').val()) {
                            $('#birth_place').val(selectedCityText);
                        }
                    }
                    validateForm();
                });

                // Medical history checkbox handlers
                $('#has_chronic_disease').on('change', function() {
                    toggleDetails('#chronic_disease_details', this.checked);
                });

                $('#current_medication').on('change', function() {
                    toggleDetails('#medication_details', this.checked);
                });

                $('#head_injury_history').on('change', function() {
                    toggleDetails('#injury_details', this.checked);
                });

                $('#substance_use').on('change', function() {
                    const show = $(this).val() === 'Pernah' || $(this).val() === 'Masih aktif';
                    toggleDetails('#substance_details', show);
                });

                $('#psychological_treatment_history').on('change', function() {
                    toggleDetails('#treatment_details', this.checked);
                });

                $('#family_mental_health_history').on('change', function() {
                    toggleDetails('#family_history_details', this.checked);
                });

                // Show existing details if already checked (for form errors)
                if ($('#has_chronic_disease').is(':checked')) {
                    $('#chronic_disease_details').show();
                }
                if ($('#current_medication').is(':checked')) {
                    $('#medication_details').show();
                }
                if ($('#head_injury_history').is(':checked')) {
                    $('#injury_details').show();
                }
                if ($('#substance_use').val() === 'Pernah' || $('#substance_use').val() === 'Masih aktif') {
                    $('#substance_details').show();
                }
                if ($('#psychological_treatment_history').is(':checked')) {
                    $('#treatment_details').show();
                }
                if ($('#family_mental_health_history').is(':checked')) {
                    $('#family_history_details').show();
                }

                // Form submission handler
                $('#identityForm').on('submit', function(e) {
                    const $submitBtn = $('#submitForm');
                    const $submitText = $('#submit-text');
                    const $submitLoading = $('#submit-loading');

                    // Disable submit button and show loading
                    $submitBtn.prop('disabled', true);
                    $submitText.hide();
                    $submitLoading.show();

                    // Re-enable form if there's an error (will be handled by Laravel)
                    setTimeout(function() {
                        $submitBtn.prop('disabled', false);
                        $submitText.show();
                        $submitLoading.hide();
                    }, 5000);
                });

                // Restore faculty and department if old values exist
                @if (old('faculty_id'))
                    $('#faculty_id').trigger('change');
                @endif

                // Restore "other" department state on validation error
                @if (old('department_id') === 'other')
                    $('#department-other-container').show();
                    $('#department_name').prop('required', true);
                @endif

                @if (old('origin_province_id'))
                    $('#origin_province_id').trigger('change');
                @endif

                // Auto-save form data periodically
                let formBackupTimer;

                function startFormBackup() {
                    formBackupTimer = setInterval(function() {
                        const formData = $('#identityForm').serializeArray();
                        sessionStorage.setItem('quiz_identity_backup', JSON.stringify(formData));
                    }, 30000); // Every 30 seconds
                }

                function restoreFormBackup() {
                    const backup = sessionStorage.getItem('quiz_identity_backup');
                    if (backup && !@json(old())) {
                        try {
                            const formData = JSON.parse(backup);
                            if (confirm('Ditemukan data form yang belum tersimpan. Apakah Anda ingin memulihkannya?')) {
                                formData.forEach(function(field) {
                                    const $field = $(`[name="${field.name}"]`);
                                    if ($field.length) {
                                        if ($field.is(':checkbox') || $field.is(':radio')) {
                                            $field.prop('checked', field.value == '1' || field.value == $field
                                                .val());
                                        } else {
                                            $field.val(field.value);
                                        }
                                    }
                                });
                                validateForm();
                            }
                        } catch (e) {
                            console.error('Error restoring form backup:', e);
                        }
                    }
                }

                function clearFormBackup() {
                    sessionStorage.removeItem('quiz_identity_backup');
                    if (formBackupTimer) {
                        clearInterval(formBackupTimer);
                    }
                }

                // Birth date picker
                const maxYear = new Date().getFullYear() - 15;
                const minYear = new Date().getFullYear() - 60;
                flatpickr('#birth_date_display', {
                    locale: 'id',
                    dateFormat: 'd/m/Y',
                    maxDate: new Date(maxYear, 11, 31),
                    minDate: new Date(minYear, 0, 1),
                    disableMobile: false,
                    onChange: function(selectedDates, dateStr) {
                        if (selectedDates.length > 0) {
                            const d = selectedDates[0];
                            const iso = d.getFullYear() + '-' +
                                String(d.getMonth() + 1).padStart(2, '0') + '-' +
                                String(d.getDate()).padStart(2, '0');
                            $('#birth_date').val(iso);
                        } else {
                            $('#birth_date').val('');
                        }
                        validateForm();
                    }
                });

                // Restore old birth_date value into picker
                const oldBirthDate = $('#birth_date').val();
                if (oldBirthDate) {
                    document.getElementById('birth_date_display')._flatpickr.setDate(oldBirthDate, false, 'Y-m-d');
                }

                // Initialize backup system
                startFormBackup();
                restoreFormBackup();

                // Clear backup on successful submission
                $('#identityForm').on('submit', function() {
                    clearFormBackup();
                });
            });

            function toggleDetails(selector, show) {
                const $element = $(selector);
                if (show) {
                    $element.slideDown(300);
                } else {
                    $element.slideUp(300).val('');
                }
            }

            function validateForm() {
                const requiredFields = [
                    '#student_year', '#faculty_id', '#department_id', '#education_level',
                    '#nim', '#full_name',
                    '#gender', '#birth_place', '#birth_date', '#phone', '#address',
                    '#living_arrangement', '#origin_province_id', '#origin_city_id', 
                    '#origin_area_type', '#religion', '#parents_marital_status', 
                    '#child_order', '#siblings_count', '#admission_path',
                    '#parents_education', '#parents_income', '#family_members_count'
                ];
                
                let filledCount = 0;
                let allValid = true;
                
                requiredFields.forEach(function(fieldSelector) {
                    const $field = $(fieldSelector);

                    // Skip education_level when level-container is hidden
                    if ($field.closest('#level-container').length && !$field.closest('#level-container').is(':visible')) {
                        return;
                    }

                    // For department_id: if 'other' is selected, validate department_name instead
                    if (fieldSelector === '#department_id' && $field.val() === 'other') {
                        const otherVal = $('#department_name').val();
                        if (otherVal && otherVal.trim() !== '') {
                            filledCount++;
                        } else {
                            allValid = false;
                        }
                        return;
                    }

                    const value = $field.val();

                    if (value && value.trim() !== '') {
                        filledCount++;
                        $field.removeClass('is-invalid');
                    } else {
                        allValid = false;
                    }
                });
                
                // Adjust total fields count based on visible fields
                const totalFields = requiredFields.filter(selector => {
                    const $field = $(selector);
                    if ($field.closest('#level-container').length) {
                        return $field.closest('#level-container').is(':visible');
                    }
                    return true;
                }).length;
                
                // Update progress display
                $('#total-fields').text(totalFields);
                $('#filled-fields').text(filledCount);
                $('#form-progress').toggle(filledCount > 0);
                
                // Enable/disable submit button
                const $submitBtn = $('#submitForm');
                $submitBtn.prop('disabled', !allValid);
                
                if (allValid) {
                    $submitBtn.removeClass('btn-secondary').addClass('btn-primary');
                } else {
                    $submitBtn.removeClass('btn-primary').addClass('btn-secondary');
                }
                
                // Show completion percentage
                const completionPercentage = Math.round((filledCount / totalFields) * 100);
                if (completionPercentage > 0 && completionPercentage < 100) {
                    $('#form-progress').html(`<small class="text-muted">${completionPercentage}% lengkap</small>`);
                }
            }

            function showNotification(message, type = 'info') {
                const alertTypes = {
                    'success': 'alert-success',
                    'error': 'alert-danger',
                    'warning': 'alert-warning',
                    'info': 'alert-info'
                };

                const icons = {
                    'success': 'bi-check-circle',
                    'error': 'bi-exclamation-circle',
                    'warning': 'bi-exclamation-triangle',
                    'info': 'bi-info-circle'
                };

                const alertClass = alertTypes[type] || 'alert-info';
                const iconClass = icons[type] || 'bi-info-circle';

                const notification = $(`
        <div class="alert ${alertClass} alert-dismissible fade show position-fixed" 
             style="top: 20px; right: 20px; z-index: 9999; max-width: 400px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            <i class="bi ${iconClass} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `);

                $('body').append(notification);

                // Auto dismiss after 5 seconds
                setTimeout(function() {
                    notification.alert('close');
                }, 5000);
            }

            // Validation helpers
            function validateNIM(nim) {
                // Basic NIM validation - adjust according to your university's format
                return nim && nim.length >= 8 && nim.length <= 15 && /^[0-9]+$/.test(nim);
            }

            // Add real-time validation for specific fields
            $('#nim').on('blur', function() {
                const nim = $(this).val();
                if (nim && !validateNIM(nim)) {
                    $(this).addClass('is-invalid');
                    showNotification('Format NIM tidak valid. Gunakan hanya angka (8-15 karakter)', 'warning');
                } else {
                    $(this).removeClass('is-invalid');
                }
            });

            $('#phone').on('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
            });

            $('#email').on('blur', function() {
                const email = $(this).val();
                if (email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                    $(this).addClass('is-invalid');
                    showNotification('Format email tidak valid', 'warning');
                } else {
                    $(this).removeClass('is-invalid');
                }
            });
        </script>
    @endpush
@endsection
