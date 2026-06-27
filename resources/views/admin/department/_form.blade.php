<div class="form-group">
    <label for="faculty_id">Fakultas <span class="text-danger">*</span></label>
    <select id="faculty_id" name="faculty_id"
        class="form-control @error('faculty_id') is-invalid @enderror" required>
        <option value="">-- Pilih Fakultas --</option>
        @foreach($faculties as $f)
            <option value="{{ $f->id }}"
                {{ old('faculty_id', $department->faculty_id ?? $selectedFacultyId ?? '') == $f->id ? 'selected' : '' }}>
                {{ $f->code }} - {{ $f->name }}
            </option>
        @endforeach
    </select>
    @error('faculty_id')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="form-group">
    <label for="name">Nama Jurusan <span class="text-danger">*</span></label>
    <input type="text" id="name" name="name"
        class="form-control @error('name') is-invalid @enderror"
        value="{{ old('name', $department->name ?? '') }}"
        placeholder="Contoh: Bimbingan dan Konseling"
        required>
    @error('name')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="form-group">
    <label for="code">Kode Jurusan <span class="text-danger">*</span></label>
    <input type="text" id="code" name="code"
        class="form-control @error('code') is-invalid @enderror"
        value="{{ old('code', $department->code ?? '') }}"
        placeholder="Contoh: BK"
        maxlength="20"
        style="text-transform:uppercase"
        required>
    <small class="form-text text-muted">Maksimal 20 karakter. Harus unik.</small>
    @error('code')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="form-group">
    <label>Jenjang Pendidikan</label>
    <div class="d-flex flex-wrap gap-3">
        @php
            $levels = ['S1', 'S2', 'S3', 'D IV', 'PROFESI'];
            $selected = old('level',
                isset($selectedLevels) ? $selectedLevels :
                (isset($department) && $department->level ? explode(',', $department->level) : [])
            );
        @endphp
        @foreach($levels as $level)
            <div class="form-check mr-3">
                <input class="form-check-input" type="checkbox"
                    name="level[]" value="{{ $level }}"
                    id="level_{{ Str::slug($level) }}"
                    {{ in_array($level, (array)$selected) ? 'checked' : '' }}>
                <label class="form-check-label" for="level_{{ Str::slug($level) }}">
                    {{ $level }}
                </label>
            </div>
        @endforeach
    </div>
    <small class="form-text text-muted">Biarkan kosong jika tidak ada jenjang khusus.</small>
    @error('level')
        <div class="text-danger small mt-1">{{ $message }}</div>
    @enderror
</div>
