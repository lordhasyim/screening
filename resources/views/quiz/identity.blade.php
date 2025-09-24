@extends('layouts.app')

@section('title', 'Identitas Diri - Skrining Kesehatan Mental ')

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

            <!-- Main Form -->
            <div class="card card-pastel slide-up">
                <div class="card-body p-4">
                    <form id="identityForm" method="POST" action="{{ route('quiz.identity') }}">
                        @csrf
                        
                        <!-- Basic Information Section -->
                        <div class="mb-5">
                            <h5 class="mb-4 text-primary">
                                <i class="bi bi-person-badge me-2"></i>
                                Informasi Dasar
                            </h5>
                            
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="student_year" class="form-label fw-semibold">
                                        Angkatan <span class="text-danger">*</span>
                                    </label>
                                    <select id="student_year" name="student_year" class="form-select form-select-pastel" required>
                                        <option value="">Pilih Angkatan</option>
                                        @for($year = 2020; $year <= date('Y') + 1; $year++)
                                            <option value="{{ $year }}" {{ old('student_year') == $year ? 'selected' : '' }}>
                                                {{ $year }}
                                            </option>
                                        @endfor
                                    </select>
                                    @error('student_year')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label for="faculty_id" class="form-label fw-semibold">
                                        Fakultas <span class="text-danger">*</span>
                                    </label>
                                    <select id="faculty_id" name="faculty_id" class="form-select form-select-pastel" required>
                                        <option value="">Pilih Fakultas</option>
                                        @foreach($faculties as $faculty)
                                            <option value="{{ $faculty->id }}" {{ old('faculty_id') == $faculty->id ? 'selected' : '' }}>
                                                {{ $faculty->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('faculty_id')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label for="department_id" class="form-label fw-semibold">
                                        Jurusan <span class="text-danger">*</span>
                                    </label>
                                    <select id="department_id" name="department_id" class="form-select form-select-pastel" required disabled>
                                        <option value="">Pilih Fakultas dulu</option>
                                    </select>
                                    @error('department_id')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="nim" class="form-label fw-semibold">
                                        NIM <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" id="nim" name="nim" class="form-control form-control-pastel" 
                                           value="{{ old('nim') }}" required maxlength="50"
                                           placeholder="Nomor Induk Mahasiswa">
                                    @error('nim')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="full_name" class="form-label fw-semibold">
                                        Nama Lengkap <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" id="full_name" name="full_name" class="form-control form-control-pastel" 
                                           value="{{ old('full_name') }}" required
                                           placeholder="Nama lengkap sesuai KTP">
                                    @error('full_name')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Personal Information Section -->
                        <div class="mb-5">
                            <h5 class="mb-4 text-primary">
                                <i class="bi bi-person me-2"></i>
                                Informasi Pribadi
                            </h5>
                            
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="gender" class="form-label fw-semibold">
                                        Jenis Kelamin <span class="text-danger">*</span>
                                    </label>
                                    <select id="gender" name="gender" class="form-select form-select-pastel" required>
                                        <option value="">Pilih</option>
                                        <option value="Laki-laki" {{ old('gender') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                        <option value="Perempuan" {{ old('gender') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                    </select>
                                    @error('gender')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label for="birth_place" class="form-label fw-semibold">
                                        Tempat Lahir <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" id="birth_place" name="birth_place" class="form-control form-control-pastel" 
                                           value="{{ old('birth_place') }}" required
                                           placeholder="Kota tempat lahir">
                                    @error('birth_place')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label for="birth_date" class="form-label fw-semibold">
                                        Tanggal Lahir <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" id="birth_date" name="birth_date" class="form-control form-control-pastel" 
                                           value="{{ old('birth_date') }}" required max="{{ date('Y-m-d', strtotime('-17 years')) }}">
                                    @error('birth_date')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="phone" class="form-label fw-semibold">
                                        Nomor Telepon/HP <span class="text-danger">*</span>
                                    </label>
                                    <input type="tel" id="phone" name="phone" class="form-control form-control-pastel" 
                                           value="{{ old('phone') }}" required
                                           placeholder="08xxxxxxxxxx">
                                    @error('phone')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="email" class="form-label fw-semibold">
                                        Email
                                    </label>
                                    <input type="email" id="email" name="email" class="form-control form-control-pastel" 
                                           value="{{ old('email') }}"
                                           placeholder="email@example.com (opsional)">
                                    @error('email')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label for="address" class="form-label fw-semibold">
                                        Alamat Domisili <span class="text-danger">*</span>
                                    </label>
                                    <textarea id="address" name="address" class="form-control form-control-pastel" rows="3" required
                                              placeholder="Alamat lengkap tempat tinggal saat ini">{{ old('address') }}</textarea>
                                    @error('address')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Living & Origin Information -->
                        <div class="mb-5">
                            <h5 class="mb-4 text-primary">
                                <i class="bi bi-geo-alt me-2"></i>
                                Informasi Tempat Tinggal & Asal
                            </h5>
                            
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="living_arrangement" class="form-label fw-semibold">
                                        Status Tempat Tinggal <span class="text-danger">*</span>
                                    </label>
                                    <select id="living_arrangement" name="living_arrangement" class="form-select form-select-pastel" required>
                                        <option value="">Pilih</option>
                                        <option value="Kos" {{ old('living_arrangement') == 'Kos' ? 'selected' : '' }}>Kos</option>
                                        <option value="Rumah orang tua" {{ old('living_arrangement') == 'Rumah orang tua' ? 'selected' : '' }}>Rumah orang tua</option>
                                        <option value="Rumah keluarga" {{ old('living_arrangement') == 'Rumah keluarga' ? 'selected' : '' }}>Rumah keluarga</option>
                                        <option value="Asrama" {{ old('living_arrangement') == 'Asrama' ? 'selected' : '' }}>Asrama</option>
                                        <option value="Kontrak" {{ old('living_arrangement') == 'Kontrak' ? 'selected' : '' }}>Kontrak</option>
                                    </select>
                                    @error('living_arrangement')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="origin_province" class="form-label fw-semibold">
                                        Provinsi Asal <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" id="origin_province" name="origin_province" class="form-control form-control-pastel" 
                                           value="{{ old('origin_province') }}" required
                                           placeholder="Provinsi tempat asal">
                                    @error('origin_province')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="origin_area_type" class="form-label fw-semibold">
                                        Tipe Daerah Asal <span class="text-danger">*</span>
                                    </label>
                                    <select id="origin_area_type" name="origin_area_type" class="form-select form-select-pastel" required>
                                        <option value="">Pilih</option>
                                        <option value="perkotaan" {{ old('origin_area_type') == 'perkotaan' ? 'selected' : '' }}>Perkotaan/Urban</option>
                                        <option value="pedesaan" {{ old('origin_area_type') == 'pedesaan' ? 'selected' : '' }}>Pedesaan/Rural</option>
                                        <option value="pinggiran kota" {{ old('origin_area_type') == 'pinggiran kota' ? 'selected' : '' }}>Pinggiran Kota/Suburban</option>
                                        <option value="daerah terpencil" {{ old('origin_area_type') == 'daerah terpencil' ? 'selected' : '' }}>Daerah Terpencil/Terisolasi</option>
                                        <option value="daerah industri" {{ old('origin_area_type') == 'daerah industri' ? 'selected' : '' }}>Daerah Industri</option>
                                    </select>
                                    @error('origin_area_type')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="religion" class="form-label fw-semibold">
                                        Agama/Kepercayaan <span class="text-danger">*</span>
                                    </label>
                                    <select id="religion" name="religion" class="form-select form-select-pastel" required>
                                        <option value="">Pilih</option>
                                        <option value="Islam" {{ old('religion') == 'Islam' ? 'selected' : '' }}>Islam</option>
                                        <option value="Kristen" {{ old('religion') == 'Kristen' ? 'selected' : '' }}>Kristen Protestan</option>
                                        <option value="Katolik" {{ old('religion') == 'Katolik' ? 'selected' : '' }}>Katolik</option>
                                        <option value="Hindu" {{ old('religion') == 'Hindu' ? 'selected' : '' }}>Hindu</option>
                                        <option value="Budha" {{ old('religion') == 'Budha' ? 'selected' : '' }}>Buddha</option>
                                        <option value="Konghucu" {{ old('religion') == 'Konghucu' ? 'selected' : '' }}>Konghucu</option>
                                    </select>
                                    @error('religion')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Family Information -->
                        <div class="mb-5">
                            <h5 class="mb-4 text-primary">
                                <i class="bi bi-people me-2"></i>
                                Informasi Keluarga
                            </h5>
                            
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="parents_marital_status" class="form-label fw-semibold">
                                        Status Pernikahan Orang Tua <span class="text-danger">*</span>
                                    </label>
                                    <select id="parents_marital_status" name="parents_marital_status" class="form-select form-select-pastel" required>
                                        <option value="">Pilih</option>
                                        <option value="menikah" {{ old('parents_marital_status') == 'menikah' ? 'selected' : '' }}>Menikah</option>
                                        <option value="cerai hidup" {{ old('parents_marital_status') == 'cerai hidup' ? 'selected' : '' }}>Cerai Hidup</option>
                                        <option value="cerai mati" {{ old('parents_marital_status') == 'cerai mati' ? 'selected' : '' }}>Cerai Mati/Meninggal</option>
                                        <option value="pisah tidak resmi" {{ old('parents_marital_status') == 'pisah tidak resmi' ? 'selected' : '' }}>Pisah Tidak Resmi</option>
                                        <option value="menikah lagi" {{ old('parents_marital_status') == 'menikah lagi' ? 'selected' : '' }}>Menikah Lagi</option>
                                    </select>
                                    @error('parents_marital_status')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-3">
                                    <label for="child_order" class="form-label fw-semibold">
                                        Anak ke- <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" id="child_order" name="child_order" class="form-control form-control-pastel" 
                                           value="{{ old('child_order') }}" required min="1" max="20">
                                    @error('child_order')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-3">
                                    <label for="siblings_count" class="form-label fw-semibold">
                                        Dari berapa bersaudara <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" id="siblings_count" name="siblings_count" class="form-control form-control-pastel" 
                                           value="{{ old('siblings_count') }}" required min="1" max="20">
                                    @error('siblings_count')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="scholarship" class="form-label fw-semibold">
                                        Beasiswa
                                    </label>
                                    <input type="text" id="scholarship" name="scholarship" class="form-control form-control-pastel" 
                                           value="{{ old('scholarship') }}"
                                           placeholder="KIP, Beasiswa Prestasi, dll (kosongkan jika tidak ada)">
                                    @error('scholarship')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="admission_path" class="form-label fw-semibold">
                                        Jalur Masuk Mahasiswa <span class="text-danger">*</span>
                                    </label>
                                    <select id="admission_path" name="admission_path" class="form-select form-select-pastel" required>
                                        <option value="">Pilih</option>
                                        <option value="SNBP" {{ old('admission_path') == 'SNBP' ? 'selected' : '' }}>SNBP (Prestasi)</option>
                                        <option value="SNBT" {{ old('admission_path') == 'SNBT' ? 'selected' : '' }}>SNBT (Tes)</option>
                                        <option value="Mandiri" {{ old('admission_path') == 'Mandiri' ? 'selected' : '' }}>Mandiri</option>
                                        <option value="Lainnya" {{ old('admission_path') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                                    </select>
                                    @error('admission_path')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label for="parents_education" class="form-label fw-semibold">
                                        Pendidikan Terakhir Orang Tua <span class="text-danger">*</span>
                                    </label>
                                    <select id="parents_education" name="parents_education" class="form-select form-select-pastel" required>
                                        <option value="">Pilih</option>
                                        <option value="SD" {{ old('parents_education') == 'SD' ? 'selected' : '' }}>SD/Sederajat</option>
                                        <option value="SMP" {{ old('parents_education') == 'SMP' ? 'selected' : '' }}>SMP/Sederajat</option>
                                        <option value="SMA" {{ old('parents_education') == 'SMA' ? 'selected' : '' }}>SMA/Sederajat</option>
                                        <option value="D3" {{ old('parents_education') == 'D3' ? 'selected' : '' }}>D3</option>
                                        <option value="S1" {{ old('parents_education') == 'S1' ? 'selected' : '' }}>S1</option>
                                        <option value="S2" {{ old('parents_education') == 'S2' ? 'selected' : '' }}>S2</option>
                                        <option value="S3" {{ old('parents_education') == 'S3' ? 'selected' : '' }}>S3</option>
                                    </select>
                                    @error('parents_education')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label for="parents_income" class="form-label fw-semibold">
                                        Penghasilan Orang Tua <span class="text-danger">*</span>
                                    </label>
                                    <select id="parents_income" name="parents_income" class="form-select form-select-pastel" required>
                                        <option value="">Pilih</option>
                                        <option value="<2000000" {{ old('parents_income') == '<2000000' ? 'selected' : '' }}>< Rp 2.000.000</option>
                                        <option value="2000000-5000000" {{ old('parents_income') == '2000000-5000000' ? 'selected' : '' }}>Rp 2.000.000 - 5.000.000</option>
                                        <option value="5000000-10000000" {{ old('parents_income') == '5000000-10000000' ? 'selected' : '' }}>Rp 5.000.000 - 10.000.000</option>
                                        <option value=">10000000" {{ old('parents_income') == '>10000000' ? 'selected' : '' }}>> Rp 10.000.000</option>
                                    </select>
                                    @error('parents_income')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label for="family_members_count" class="form-label fw-semibold">
                                        Jumlah Anggota Keluarga di Rumah <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" id="family_members_count" name="family_members_count" class="form-control form-control-pastel" 
                                           value="{{ old('family_members_count') }}" required min="1" max="20"
                                           placeholder="Termasuk Anda">
                                    @error('family_members_count')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Medical History (Collapsed by default) -->
                        <div class="mb-5">
                            <h5 class="mb-4 text-primary">
                                <i class="bi bi-shield-plus me-2"></i>
                                Riwayat Kesehatan
                                <button type="button" class="btn btn-sm btn-outline-secondary ms-2" data-bs-toggle="collapse" data-bs-target="#medicalHistory">
                                    <i class="bi bi-chevron-down"></i>
                                </button>
                            </h5>
                            
                            <div id="medicalHistory" class="collapse">
                                <div class="row g-3">
                                    <!-- Medical questions will be here - keeping it simple for now -->
                                    <div class="col-12">
                                        <div class="alert alert-pastel-info">
                                            <i class="bi bi-info-circle me-2"></i>
                                            <strong>Bagian Opsional:</strong> Informasi kesehatan membantu kami memberikan dukungan yang lebih tepat. Anda dapat melewati bagian ini jika tidak ingin mengisi.
                                        </div>
                                    </div>

                                    <!-- Chronic Disease -->
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="has_chronic_disease" name="has_chronic_disease" value="1" {{ old('has_chronic_disease') ? 'checked' : '' }}>
                                            <label class="form-check-label fw-semibold" for="has_chronic_disease">
                                                Memiliki riwayat penyakit kronis
                                            </label>
                                        </div>
                                        <textarea id="chronic_disease_details" name="chronic_disease_details" class="form-control form-control-pastel mt-2" rows="2" 
                                                  placeholder="Sebutkan jenis penyakit (Maag, Asma, Lupus, Epilepsi, dll)" style="display: none;">{{ old('chronic_disease_details') }}</textarea>
                                    </div>

                                    <!-- Current Medication -->
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="current_medication" name="current_medication" value="1" {{ old('current_medication') ? 'checked' : '' }}>
                                            <label class="form-check-label fw-semibold" for="current_medication">
                                                Sedang menjalani pengobatan medis
                                            </label>
                                        </div>
                                        <textarea id="medication_details" name="medication_details" class="form-control form-control-pastel mt-2" rows="2" 
                                                  placeholder="Sebutkan obat yang dikonsumsi" style="display: none;">{{ old('medication_details') }}</textarea>
                                    </div>

                                    <!-- Head Injury -->
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="head_injury_history" name="head_injury_history" value="1" {{ old('head_injury_history') ? 'checked' : '' }}>
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
                                        <select id="substance_use" name="substance_use" class="form-select form-select-pastel">
                                            <option value="Tidak Pernah" {{ old('substance_use') == 'Tidak Pernah' ? 'selected' : '' }}>Tidak Pernah</option>
                                            <option value="Pernah" {{ old('substance_use') == 'Pernah' ? 'selected' : '' }}>Pernah</option>
                                            <option value="Masih aktif" {{ old('substance_use') == 'Masih aktif' ? 'selected' : '' }}>Masih aktif</option>
                                        </select>
                                        <textarea id="substance_details" name="substance_details" class="form-control form-control-pastel mt-2" rows="2" 
                                                  placeholder="Jelaskan jenis, frekuensi, dan durasi (jika ada)" style="display: none;">{{ old('substance_details') }}</textarea>
                                    </div>

                                    <!-- Psychological Treatment -->
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="psychological_treatment_history" name="psychological_treatment_history" value="1" {{ old('psychological_treatment_history') ? 'checked' : '' }}>
                                            <label class="form-check-label fw-semibold" for="psychological_treatment_history">
                                                Pernah mendapat perawatan dari psikolog atau psikiater
                                            </label>
                                        </div>
                                        <textarea id="treatment_details" name="treatment_details" class="form-control form-control-pastel mt-2" rows="2" 
                                                  placeholder="Jelaskan bentuk perawatan" style="display: none;">{{ old('treatment_details') }}</textarea>
                                    </div>

                                    <!-- Family Mental Health -->
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="family_mental_health_history" name="family_mental_health_history" value="1" {{ old('family_mental_health_history') ? 'checked' : '' }}>
                                            <label class="form-check-label fw-semibold" for="family_mental_health_history">
                                                Ada anggota keluarga dengan riwayat gangguan kesehatan mental
                                            </label>
                                        </div>
                                        <textarea id="family_history_details" name="family_history_details" class="form-control form-control-pastel mt-2" rows="2" 
                                                  placeholder="Sebutkan hubungan keluarga dan kondisinya" style="display: none;">{{ old('family_history_details') }}</textarea>
                                    </div>

                                    <!-- Family Relationship -->
                                    <div class="col-12">
                                        <label for="family_relationship_description" class="form-label fw-semibold">
                                            Bagaimana hubungan Anda dengan keluarga inti?
                                        </label>
                                        <textarea id="family_relationship_description" name="family_relationship_description" class="form-control form-control-pastel" rows="3" 
                                                  placeholder="Jelaskan secara singkat hubungan dengan orang tua dan saudara">{{ old('family_relationship_description') }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Navigation -->
                        <div class="d-flex justify-content-between align-items-center pt-4 border-top">
                            <div>
                                <a href="{{ route('quiz.index') }}" class="btn btn-pastel-secondary">
                                    <i class="bi bi-arrow-left me-2"></i>
                                    Kembali
                                </a>
                            </div>
                            <div>
                                <small class="text-muted me-3">
                                    <span class="text-danger">*</span> Wajib diisi
                                </small>
                                <button type="submit" id="submitForm" class="btn btn-pastel-primary" disabled>
                                    Lanjut ke Skrining
                                    <i class="bi bi-arrow-right ms-2"></i>
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
<script>
$(document).ready(function() {
    // Initialize form validation
    validateForm();
    
    // Real-time form validation
    $('#identityForm input, #identityForm select, #identityForm textarea').on('input change', function() {
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
    
    // Show existing details if already checked
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
    
    // Auto-backup form every 30 seconds
    setInterval(function() {
        backupFormData('identityForm');
    }, 30000);
    
    // Try to restore form data
    restoreFormData('identityForm');
});

function toggleDetails(selector, show) {
    if (show) {
        $(selector).slideDown();
    } else {
        $(selector).slideUp().val('');
    }
}

function validateForm() {
    const requiredFields = [
        '#student_year', '#faculty_id', '#department_id', '#nim', '#full_name',
        '#gender', '#birth_place', '#birth_date', '#phone', '#address',
        '#living_arrangement', '#origin_province', '#origin_area_type', '#religion',
        '#parents_marital_status', '#child_order', '#siblings_count', '#admission_path',
        '#parents_education', '#parents_income', '#family_members_count'
    ];
    
    let allValid = true;
    
    requiredFields.forEach(function(field) {
        const $field = $(field);
        const value = $field.val();
        
        if (!value || value.trim() === '') {
            allValid = false;
            return false; // break
        }
    });
    
    // Enable/disable submit button
    $('#submitForm').prop('disabled', !allValid);
    
    if (allValid) {
        $('#submitForm').removeClass('btn-pastel-secondary').addClass('btn-pastel-primary');
    } else {
        $('#submitForm').removeClass('btn-pastel-primary').addClass('btn-pastel-secondary');
    }
}

// Track form completion analytics
$(document).ready(function() {
    let startTime = Date.now();
    
    $('#identityForm').on('submit', function() {
        const completionTime = (Date.now() - startTime) / 1000; // seconds
        trackUserInteraction('identity_form_completed', {
            completion_time: completionTime,
            form_fields_filled: $('#identityForm input:not([value=""]), #identityForm select:not([value=""]), #identityForm textarea:not(:empty)').length
        });
        
        // Clear backup after successful submission
        clearFormBackup('identityForm');
    });
});
</script>
@endpush
@endsection