@extends('layouts.admin')

@section('title', 'Manajemen Jurusan - Admin')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">
            Manajemen Jurusan
            @isset($faculty)
                <small class="text-muted">&mdash; {{ $faculty->name }}</small>
            @endisset
        </h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent p-0 mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.faculties.index') }}">Fakultas</a></li>
                @isset($faculty)
                    <li class="breadcrumb-item active">{{ $faculty->code }}</li>
                @else
                    <li class="breadcrumb-item active">Semua Jurusan</li>
                @endisset
            </ol>
        </nav>
    </div>
    <a href="{{ route('admin.departments.create', isset($faculty) ? ['faculty_id' => $faculty->id] : []) }}"
       class="btn btn-primary btn-sm shadow-sm">
        <i class="fas fa-plus fa-sm mr-1"></i> Tambah Jurusan
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    </div>
@endif

<!-- Filter -->
@unless(isset($faculty))
<div class="card shadow mb-4">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('admin.departments.index') }}" class="form-inline">
            <div class="form-group mr-3 mb-2">
                <select name="faculty_id" class="form-control form-control-sm">
                    <option value="">Semua Fakultas</option>
                    @foreach($faculties as $f)
                        <option value="{{ $f->id }}" {{ request('faculty_id') == $f->id ? 'selected' : '' }}>
                            {{ $f->code }} - {{ $f->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group mr-3 mb-2">
                <input type="text" name="search" class="form-control form-control-sm"
                    placeholder="Cari nama / kode..." value="{{ request('search') }}">
            </div>
            <button type="submit" class="btn btn-primary btn-sm mb-2 mr-2">
                <i class="fas fa-search mr-1"></i> Cari
            </button>
            @if(request()->hasAny(['faculty_id', 'search']))
                <a href="{{ route('admin.departments.index') }}" class="btn btn-secondary btn-sm mb-2">Reset</a>
            @endif
        </form>
    </div>
</div>
@endunless

<!-- Table -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            Daftar Jurusan
            <span class="badge badge-secondary ml-2">{{ $departments->total() }}</span>
        </h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" width="100%">
                <thead class="thead-light">
                    <tr>
                        <th width="80">Kode</th>
                        <th>Nama Jurusan</th>
                        @unless(isset($faculty))
                            <th>Fakultas</th>
                        @endunless
                        <th width="200">Jenjang</th>
                        <th width="130" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($departments as $dept)
                        <tr>
                            <td><span class="badge badge-secondary">{{ $dept->code }}</span></td>
                            <td>{{ $dept->name }}</td>
                            @unless(isset($faculty))
                                <td>
                                    <span class="badge badge-primary">{{ $dept->faculty->code ?? '-' }}</span>
                                    <small class="text-muted d-block">{{ $dept->faculty->name ?? '-' }}</small>
                                </td>
                            @endunless
                            <td>
                                @if($dept->level)
                                    @foreach(explode(',', $dept->level) as $lvl)
                                        <span class="badge badge-light border mr-1">{{ trim($lvl) }}</span>
                                    @endforeach
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('admin.departments.edit', $dept) }}"
                                   class="btn btn-warning btn-sm" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button class="btn btn-danger btn-sm btn-delete"
                                    data-id="{{ $dept->id }}"
                                    data-name="{{ $dept->name }}"
                                    title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">Belum ada data jurusan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $departments->links() }}</div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-exclamation-triangle mr-2"></i>Konfirmasi Hapus</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <p>Anda akan menghapus jurusan <strong id="deleteName"></strong>.</p>
                <p class="mb-0 text-muted">Tindakan ini tidak dapat dibatalkan.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">
                    <i class="fas fa-trash mr-1"></i> Hapus
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let deleteId = null;

document.querySelectorAll('.btn-delete').forEach(btn => {
    btn.addEventListener('click', function() {
        deleteId = this.dataset.id;
        document.getElementById('deleteName').textContent = this.dataset.name;
        $('#deleteModal').modal('show');
    });
});

document.getElementById('confirmDelete').addEventListener('click', function() {
    if (!deleteId) return;
    this.disabled = true;
    this.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Menghapus...';

    fetch(`/admin/departments/${deleteId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            $('#deleteModal').modal('hide');
            location.reload();
        }
    });
});
</script>
@endpush
