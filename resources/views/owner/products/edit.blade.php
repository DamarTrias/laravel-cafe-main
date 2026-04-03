@extends('layouts.dashboard')

@section('title', 'Edit Produk')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col-12">
        <a href="{{ route('owner.products.index') }}" class="text-decoration-none text-muted mb-2 d-inline-block">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
        <h2 class="fw-bold text-white mb-0">Edit Produk: {{ $product->name }}</h2>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="glass-card card border-0">
            <div class="card-body p-4">
                <form action="{{ route('owner.products.update', $product) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="name" class="form-label text-white">Nama Produk</label>
                        <input type="text" class="form-control text-white" id="name" name="name" required value="{{ old('name', $product->name) }}">
                    </div>

                    <div class="mb-3">
                        <label for="category_id" class="form-label text-white">Kategori</label>
                        <select class="form-select text-white" id="category_id" name="category_id" required>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="price" class="form-label text-white">Harga (Rp)</label>
                            <input type="number" class="form-control text-white" id="price" name="price" required value="{{ old('price', $product->price) }}">
                        </div>
                        <div class="col-md-6">
                            <label for="stock" class="form-label text-white">Stok</label>
                            <input type="number" class="form-control text-white" id="stock" name="stock" required value="{{ old('stock', $product->stock) }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label text-white">Deskripsi</label>
                        <textarea class="form-control text-white" id="description" name="description" rows="3">{{ old('description', $product->description) }}</textarea>
                    </div>

                    <div class="mb-4">
                        <label for="image" class="form-label text-white">Foto Produk</label>
                        @if($product->image)
                            <div class="mb-3 p-2 bg-dark bg-opacity-50 rounded border border-secondary d-inline-block">
                                <img src="{{ Storage::url($product->image) }}" alt="product image" class="rounded shadow-sm" style="max-height: 150px; width: auto; object-fit: cover;">
                                <div class="mt-2 text-center">
                                    <span class="badge bg-primary px-2 py-1">Foto Saat Ini</span>
                                </div>
                            </div>
                        @endif
                        <input type="file" class="form-control text-white" id="image" name="image" accept="image/*">
                        <small class="text-muted">Kosongkan jika tidak ingin mengubah foto. Format: JPG, PNG, JPEG. Max: 2MB</small>
                    </div>

                    <div class="mb-4 form-check">
                        <input type="checkbox" class="form-check-input" id="is_available" name="is_available" value="1" {{ $product->is_available ? 'checked' : '' }}>
                        <label class="form-check-label text-white" for="is_available">Tersedia untuk Dipesan</label>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold shadow">
                        <i class="bi bi-check-lg me-1"></i> Simpan Perubahan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection