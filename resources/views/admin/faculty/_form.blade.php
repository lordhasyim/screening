<div class="form-group">
    <label for="name">Nama Fakultas <span class="text-danger">*</span></label>
    <input type="text" id="name" name="name"
        class="form-control @error('name') is-invalid @enderror"
        value="{{ old('name', $faculty->name ?? '') }}"
        placeholder="Contoh: Fakultas Ilmu Pendidikan"
        required>
    @error('name')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="form-group">
    <label for="code">Kode Fakultas <span class="text-danger">*</span></label>
    <input type="text" id="code" name="code"
        class="form-control @error('code') is-invalid @enderror"
        value="{{ old('code', $faculty->code ?? '') }}"
        placeholder="Contoh: FIP"
        maxlength="10"
        style="text-transform:uppercase"
        required>
    <small class="form-text text-muted">Maksimal 10 karakter. Otomatis diubah ke huruf kapital.</small>
    @error('code')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
