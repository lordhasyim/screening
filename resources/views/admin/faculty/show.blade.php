@extends('layouts.admin')

@section('title', $faculty->name . ' - Admin')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">{{ $faculty->name }}</h1>
        <span class="badge badge-primary mt-1" style="font-size:.9rem">{{ $faculty->code }}</span>
    </div>
    <div>
        <a href="{{ route('admin.departments.create', ['faculty_id' => $faculty->id]) }}"
           class="btn btn-success btn-sm shadow-sm mr-2">
            <i class="fas fa-plus fa-sm mr-1"></i> Tambah Jurusan
        </a>
        <a href="{{ route('admin.faculties.edit', $faculty) }}"
           class="btn btn-warning btn-sm shadow-sm mr-2">
            <i class="fas fa-edit fa-sm mr-1"></i> Edit Fakultas
        </a>
        <a href="{{ route('admin.faculties.index') }}"
           class="btn btn-secondary btn-sm shadow-sm">
            <i class="fas fa-arrow-left fa-sm mr-1"></i> Kembali
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    </div>
@endif

<!-- Stats -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Jurusan</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $faculty->departments_count }}</div>
                    </div>
                    <div class="col-auto"><i class="fas fa-book fa-2x text-gray-300"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Responden</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $faculty->quiz_responses_count }}</div>
                    </div>
                    <div class="col-auto"><i class="fas fa-users fa-2x text-gray-300"></i></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Department List -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">
            Daftar Jurusan
            <span class="badge badge-secondary ml-1">{{ $departments->count() }}</span>
        </h6>
    </div>
    <div class="card-body">
        @if($departments->isEmpty())
            <div class="text-center text-muted py-5">
                <i class="fas fa-book fa-3x mb-3 text-gray-300"></i>
                <p>Belum ada jurusan untuk fakultas ini.</p>
                <a href="{{ route('admin.departments.create', ['faculty_id' => $faculty->id]) }}"
                   class="btn btn-primary btn-sm">
                    <i class="fas fa-plus mr-1"></i> Tambah Jurusan Pertama
                </a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th width="80">Kode</th>
                            <th>Nama Jurusan</th>
                            <th width="220">Jenjang</th>
                            <th width="100" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($departments as $dept)
                            <tr>
                                <td><span class="badge badge-secondary">{{ $dept->code }}</span></td>
                                <td>{{ $dept->name }}</td>
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
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
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
