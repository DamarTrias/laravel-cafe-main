@extends('layouts.dashboard')

@section('title', 'Tambah Kategori')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col-12">
        <a href="{{ route('owner.categories.index') }}" class="text-decoration-none text-muted mb-2 d-inline-block">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
        <h2 class="fw-bold text-white mb-0">Tambah Kategori</h2>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="glass-card card border-0">
            <div class="card-body p-4">
                <form action="{{ route('owner.categories.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="name" class="form-label text-white">Nama Kategori</label>
                        <input type="text" class="form-control" id="name" name="name" required value="{{ old('name') }}">
                        @error('name')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label text-white">Deskripsi</label>
                        <textarea class="form-control" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                        @error('description')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100">Simpan Kategori</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
