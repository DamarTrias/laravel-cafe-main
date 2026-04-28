@extends('layouts.dashboard')

@section('title', 'Edit Bahan')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card glass-card">
            <div class="card-header border-0 pb-0 pt-4 px-4">
                <h4 class="card-title text-primary"><i class="bi bi-pencil-square me-2"></i> Edit {{ $ingredient->name }}</h4>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('owner.ingredients.update', $ingredient) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Bahan</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $ingredient->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="unit" class="form-label">Satuan</label>
                        <select class="form-select @error('unit') is-invalid @enderror" id="unit" name="unit" required>
                            <option value="kg" {{ old('unit', $ingredient->unit) == 'kg' ? 'selected' : '' }}>kg (Kilogram)</option>
                            <option value="gram" {{ old('unit', $ingredient->unit) == 'gram' ? 'selected' : '' }}>gram</option>
                            <option value="liter" {{ old('unit', $ingredient->unit) == 'liter' ? 'selected' : '' }}>liter</option>
                            <option value="ml" {{ old('unit', $ingredient->unit) == 'ml' ? 'selected' : '' }}>ml (Mililiter)</option>
                            <option value="pcs" {{ old('unit', $ingredient->unit) == 'pcs' ? 'selected' : '' }}>pcs (Pcs/Butir)</option>
                        </select>
                        @error('unit')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="warehouse_stock" class="form-label">Stok Gudang</label>
                            <input type="number" step="0.01" class="form-control @error('warehouse_stock') is-invalid @enderror" id="warehouse_stock" name="warehouse_stock" value="{{ old('warehouse_stock', $ingredient->warehouse_stock) }}" required min="0">
                            @error('warehouse_stock')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="operational_stock" class="form-label">Stok Operasional</label>
                            <input type="number" step="0.01" class="form-control @error('operational_stock') is-invalid @enderror" id="operational_stock" name="operational_stock" value="{{ old('operational_stock', $ingredient->operational_stock) }}" required min="0">
                            @error('operational_stock')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('owner.ingredients.index') }}" class="btn btn-outline-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary px-4">Update Bahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
