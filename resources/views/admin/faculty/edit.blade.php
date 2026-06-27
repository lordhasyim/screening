@extends('layouts.admin')

@section('title', 'Edit Fakultas - Admin')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Edit Fakultas</h1>
    <a href="{{ route('admin.faculties.index') }}" class="btn btn-secondary btn-sm shadow-sm">
        <i class="fas fa-arrow-left fa-sm mr-1"></i> Kembali
    </a>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">{{ $faculty->name }}</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.faculties.update', $faculty) }}">
                    @csrf
                    @method('PUT')
                    @include('admin.faculty._form')
                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i> Perbarui
                        </button>
                        <a href="{{ route('admin.faculties.index') }}" class="btn btn-secondary ml-2">Batal</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow mb-4 border-left-info">
            <div class="card-body py-3">
                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Jumlah Jurusan</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $faculty->departments_count }}</div>
                <a href="{{ route('admin.faculties.departments', $faculty) }}" class="small">Lihat semua jurusan &rarr;</a>
            </div>
        </div>
    </div>
</div>
@endsection
