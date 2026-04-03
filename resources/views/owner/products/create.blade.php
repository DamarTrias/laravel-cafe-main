@extends('layouts.dashboard')

@section('title', 'Tambah Produk')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col-12">
        <a href="{{ route('owner.products.index') }}" class="text-decoration-none text-muted mb-2 d-inline-block">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
        <h2 class="fw-bold text-white mb-0">Tambah Produk Baru</h2>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="glass-card card border-0">
            <div class="card-body p-4">
                <form action="{{ route('owner.products.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label text-white">Nama Produk</label>
                        <input type="text" class="form-control text-white" id="name" name="name" required value="{{ old('name') }}" placeholder="Masukkan nama produk">
                    </div>

                    <div class="mb-3">
                        <label for="category_id" class="form-label text-white">Kategori</label>
                        <select class="form-select text-white" id="category_id" name="category_id" required>
                            <option value="" disabled selected>Pilih Kategori</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="price" class="form-label text-white">Harga (Rp)</label>
                            <input type="number" class="form-control text-white" id="price" name="price" required value="{{ old('price') }}" placeholder="0">
                        </div>
                        <div class="col-md-6">
                            <label for="stock" class="form-label text-white">Stok</label>
                            <input type="number" class="form-control text-white" id="stock" name="stock" required value="{{ old('stock', 0) }}" placeholder="0">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="description" class="form-label text-white">Deskripsi (Opsional)</label>
                        <textarea class="form-control text-white" id="description" name="description" rows="3" placeholder="Gambarkan rasa atau komposisi menu...">{{ old('description') }}</textarea>
                    </div>

                    <div class="mb-4">
                        <label for="image" class="form-label text-white">Foto Produk</label>
                        <input type="file" class="form-control text-white" id="image" name="image" accept="image/*">
                        <small class="text-muted">Format: JPG, PNG, JPEG. Rekomendasi: 1:1 (Persegi). Max: 2MB</small>
                    </div>

                    <div class="mb-4 form-check">
                        <input type="checkbox" class="form-check-input" id="is_available" name="is_available" checked value="1" {{ old('is_available') ? 'checked' : '' }}>
                        <label class="form-check-label text-white" for="is_available">Tersedia untuk Dipesan</label>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold shadow">
                        <i class="bi bi-save me-1"></i> Simpan Produk
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection