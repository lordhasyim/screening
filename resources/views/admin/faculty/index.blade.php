@extends('layouts.admin')

@section('title', 'Manajemen Fakultas - Admin')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Manajemen Fakultas</h1>
    <a href="{{ route('admin.faculties.create') }}" class="btn btn-primary btn-sm shadow-sm">
        <i class="fas fa-plus fa-sm mr-1"></i> Tambah Fakultas
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    </div>
@endif

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Fakultas</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalFaculties }}</div>
                    </div>
                    <div class="col-auto"><i class="fas fa-university fa-2x text-gray-300"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Jurusan</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalDepartments }}</div>
                    </div>
                    <div class="col-auto"><i class="fas fa-book fa-2x text-gray-300"></i></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Table -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Daftar Fakultas</h6>
        <a href="{{ route('admin.departments.index') }}" class="btn btn-outline-primary btn-sm">
            <i class="fas fa-book mr-1"></i> Kelola Semua Jurusan
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" width="100%">
                <thead class="thead-light">
                    <tr>
                        <th width="80">Kode</th>
                        <th>Nama Fakultas</th>
                        <th width="120" class="text-center">Jumlah Jurusan</th>
                        <th width="180" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($faculties as $faculty)
                        <tr>
                            <td><span class="badge badge-primary">{{ $faculty->code }}</span></td>
                            <td>
                                <a href="{{ route('admin.faculties.show', $faculty) }}" class="font-weight-bold text-dark">
                                    {{ $faculty->name }}
                                </a>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('admin.faculties.departments', $faculty) }}"
                                   class="badge badge-info" style="font-size:.85em">
                                    {{ $faculty->departments_count }} jurusan
                                </a>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('admin.faculties.show', $faculty) }}"
                                   class="btn btn-info btn-sm" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.departments.create', ['faculty_id' => $faculty->id]) }}"
                                   class="btn btn-success btn-sm" title="Tambah Jurusan">
                                    <i class="fas fa-plus"></i>
                                </a>
                                <a href="{{ route('admin.faculties.edit', $faculty) }}"
                                   class="btn btn-warning btn-sm" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button class="btn btn-danger btn-sm btn-delete"
                                    data-id="{{ $faculty->id }}"
                                    data-name="{{ $faculty->name }}"
                                    data-count="{{ $faculty->departments_count }}"
                                    title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">Belum ada data fakultas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $faculties->links() }}</div>
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
                <p>Anda akan menghapus fakultas <strong id="deleteName"></strong>.</p>
                <div class="alert alert-warning" id="cascadeWarning" style="display:none">
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    Fakultas ini memiliki <strong id="deleteCount"></strong> jurusan yang akan ikut terhapus.
                </div>
                <p class="mb-0">Tindakan ini tidak dapat dibatalkan.</p>
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
        const count = parseInt(this.dataset.count);
        document.getElementById('deleteName').textContent = this.dataset.name;
        document.getElementById('deleteCount').textContent = count + ' jurusan';
        document.getElementById('cascadeWarning').style.display = count > 0 ? 'block' : 'none';
        $('#deleteModal').modal('show');
    });
});

document.getElementById('confirmDelete').addEventListener('click', function() {
    if (!deleteId) return;
    this.disabled = true;
    this.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Menghapus...';

    fetch(`/admin/faculties/${deleteId}`, {
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
