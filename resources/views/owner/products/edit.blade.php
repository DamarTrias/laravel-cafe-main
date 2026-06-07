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
                    <form action="{{ route('owner.products.update', $product) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label text-white">Nama Produk</label>
                            <input type="text" class="form-control text-white" id="name" name="name" required
                                value="{{ old('name', $product->name) }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-white d-flex justify-content-between">
                                Kategori
                                <small class="text-muted">Pilih dari daftar atau ketik manual</small>
                            </label>
                            <div class="row">
                                <div class="col-md-5 mb-2 mb-md-0">
                                    <select class="form-select bg-dark text-white border-secondary" id="category_select">
                                        <option value="">-- Pilih dari yang sudah ada --</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->name }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-7">
                                    <input type="text" class="form-control bg-dark text-white border-secondary" id="category_name" name="category_name" required
                                        value="{{ old('category_name', $product->category->name ?? '') }}" placeholder="Nama kategori (cth: Minuman)">
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-12">
                                <label for="price" class="form-label text-white">Harga (Rp)</label>
                                <input type="number" class="form-control text-white" id="price" name="price" required
                                    value="{{ old('price', $product->price) }}">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label text-white d-flex justify-content-between">
                                Resep / Bahan Baku
                                <small class="text-muted">Pilih dari daftar atau ketik manual</small>
                            </label>
                            <div id="ingredients-container">
                                @foreach($product->ingredients as $index => $pivot)
                                    <div
                                        class="mb-3 ingredient-row p-3 rounded border border-secondary border-opacity-25 bg-dark bg-opacity-25">
                                        <div class="row mb-2">
                                            <div class="col-10">
                                                <select
                                                    class="form-select bg-dark text-white border-secondary ingredient-select">
                                                    <option value="">-- Pilih dari bahan yang sudah ada (opsional) --</option>
                                                    @foreach($ingredients as $ing)
                                                        <option value="{{ $ing->name }}" data-unit="{{ $ing->unit }}" {{ $pivot->name == $ing->name ? 'selected' : '' }}>{{ $ing->name }}
                                                            ({{ $ing->unit }})</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-2">
                                                <button type="button"
                                                    class="btn btn-outline-danger btn-sm w-100 h-100 remove-ingredient">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-5">
                                                <input type="text" name="ingredient_names[]"
                                                    class="form-control bg-dark text-white border-secondary ingredient-name"
                                                    placeholder="Nama bahan (cth: Kopi Arabika)" value="{{ $pivot->name }}" required>
                                            </div>
                                            <div class="col-4">
                                                <input type="number" step="0.01" name="amounts[]"
                                                    class="form-control bg-dark text-white border-secondary"
                                                    placeholder="Jumlah" value="{{ $pivot->pivot->amount_needed }}" required>
                                            </div>
                                            <div class="col-3">
                                                <input type="text" name="units[]"
                                                    class="form-control bg-dark text-white border-secondary ingredient-unit"
                                                    placeholder="Satuan (cth: gr)" value="{{ $pivot->unit }}" required>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                                @if($product->ingredients->isEmpty())
                                    <div
                                        class="mb-3 ingredient-row p-3 rounded border border-secondary border-opacity-25 bg-dark bg-opacity-25">
                                        <div class="row mb-2">
                                            <div class="col-10">
                                                <select
                                                    class="form-select bg-dark text-white border-secondary ingredient-select">
                                                    <option value="">-- Pilih dari bahan yang sudah ada (opsional) --</option>
                                                    @foreach($ingredients as $ing)
                                                        <option value="{{ $ing->name }}" data-unit="{{ $ing->unit }}">
                                                            {{ $ing->name }} ({{ $ing->unit }})</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-2">
                                                <button type="button"
                                                    class="btn btn-outline-danger btn-sm w-100 h-100 remove-ingredient">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-5">
                                                <input type="text" name="ingredient_names[]"
                                                    class="form-control bg-dark text-white border-secondary ingredient-name"
                                                    placeholder="Nama bahan (cth: Kopi Arabika)" required>
                                            </div>
                                            <div class="col-4">
                                                <input type="number" step="0.01" name="amounts[]"
                                                    class="form-control bg-dark text-white border-secondary"
                                                    placeholder="Jumlah" required>
                                            </div>
                                            <div class="col-3">
                                                <input type="text" name="units[]"
                                                    class="form-control bg-dark text-white border-secondary ingredient-unit"
                                                    placeholder="Satuan (cth: gr)" required>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <button type="button" id="add-ingredient" class="btn btn-outline-success btn-sm mt-2">
                                <i class="bi bi-plus-circle me-1"></i> Tambah Bahan Baku
                            </button>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label text-white">Deskripsi</label>
                            <textarea class="form-control text-white" id="description" name="description"
                                rows="3">{{ old('description', $product->description) }}</textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label text-white d-flex justify-content-between">
                                Add-on Berbayar
                                <small class="text-muted">Opsional, contoh: Telur Ceplok + Rp 5.000</small>
                            </label>
                            <div id="addons-container">
                                @forelse($product->addons as $addon)
                                    <div class="mb-2 addon-row row g-2">
                                        <div class="col-md-7">
                                            <input type="text" name="addon_names[]" class="form-control bg-dark text-white border-secondary" value="{{ old('addon_names.' . $loop->index, $addon->name) }}" placeholder="Nama add-on">
                                        </div>
                                        <div class="col-md-4">
                                            <input type="number" name="addon_prices[]" class="form-control bg-dark text-white border-secondary" value="{{ old('addon_prices.' . $loop->index, (int) $addon->price) }}" placeholder="Harga">
                                        </div>
                                        <div class="col-md-1">
                                            <button type="button" class="btn btn-outline-danger w-100 remove-addon"><i class="bi bi-trash"></i></button>
                                        </div>
                                    </div>
                                @empty
                                    <div class="mb-2 addon-row row g-2">
                                        <div class="col-md-7">
                                            <input type="text" name="addon_names[]" class="form-control bg-dark text-white border-secondary" placeholder="Nama add-on">
                                        </div>
                                        <div class="col-md-4">
                                            <input type="number" name="addon_prices[]" class="form-control bg-dark text-white border-secondary" placeholder="Harga">
                                        </div>
                                        <div class="col-md-1">
                                            <button type="button" class="btn btn-outline-danger w-100 remove-addon"><i class="bi bi-trash"></i></button>
                                        </div>
                                    </div>
                                @endforelse
                            </div>
                            <button type="button" id="add-addon" class="btn btn-outline-success btn-sm mt-2">
                                <i class="bi bi-plus-circle me-1"></i> Tambah Add-on
                            </button>
                        </div>

                        <div class="mb-4">
                            <label for="image" class="form-label text-white">Foto Produk</label>
                            @if($product->image)
                                <div class="mb-3 p-2 bg-dark bg-opacity-50 rounded border border-secondary d-inline-block">
                                    <img src="{{ Storage::url($product->image) }}" alt="product image" class="rounded shadow-sm"
                                        style="max-height: 150px; width: auto; object-fit: cover;">
                                    <div class="mt-2 text-center">
                                        <span class="badge bg-primary px-2 py-1">Foto Saat Ini</span>
                                    </div>
                                </div>
                            @endif
                            <input type="file" class="form-control text-white" id="image" name="image" accept="image/*">
                            <small class="text-muted">Kosongkan jika tidak ingin mengubah foto. Format: JPG, PNG, JPEG. Max:
                                2MB</small>
                        </div>

                        <div class="mb-4 form-check">
                            <input type="checkbox" class="form-check-input" id="is_available" name="is_available" value="1"
                                {{ $product->is_available ? 'checked' : '' }}>
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

    @push('scripts')
        <script>
            const container = document.getElementById('ingredients-container');
            const addButton = document.getElementById('add-ingredient');
            const addonsContainer = document.getElementById('addons-container');
            const addAddonButton = document.getElementById('add-addon');

            // Data bahan yang sudah ada
            const existingIngredients = @json($ingredients->map(fn($i) => ['name' => $i->name, 'unit' => $i->unit]));

            // Auto-fill name & unit when dropdown selected
            container.addEventListener('change', (e) => {
                if (e.target.classList.contains('ingredient-select')) {
                    const row = e.target.closest('.ingredient-row');
                    const nameInput = row.querySelector('.ingredient-name');
                    const unitInput = row.querySelector('.ingredient-unit');
                    const selectedOption = e.target.options[e.target.selectedIndex];

                    if (e.target.value) {
                        nameInput.value = e.target.value;
                        unitInput.value = selectedOption.getAttribute('data-unit') || '';
                    }
                }
            });

            const categorySelect = document.getElementById('category_select');
            const categoryNameInput = document.getElementById('category_name');

            if (categorySelect && categoryNameInput) {
                categorySelect.addEventListener('change', function() {
                    if (this.value) {
                        categoryNameInput.value = this.value;
                    }
                });
            }

            function buildOptions() {
                return existingIngredients.map(ing =>
                    `<option value="${ing.name}" data-unit="${ing.unit}">${ing.name} (${ing.unit})</option>`
                ).join('');
            }

            if (addButton && container) {
                addButton.addEventListener('click', () => {
                    const row = document.createElement('div');
                    row.className = 'mb-3 ingredient-row p-3 rounded border border-secondary border-opacity-25 bg-dark bg-opacity-25';
                    row.innerHTML = `
                        <div class="row mb-2">
                            <div class="col-10">
                                <select class="form-select bg-dark text-white border-secondary ingredient-select">
                                    <option value="">-- Pilih dari bahan yang sudah ada (opsional) --</option>
                                    ${buildOptions()}
                                </select>
                            </div>
                            <div class="col-2">
                                <button type="button" class="btn btn-outline-danger btn-sm w-100 h-100 remove-ingredient">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-5">
                                <input type="text" name="ingredient_names[]" class="form-control bg-dark text-white border-secondary ingredient-name" placeholder="Nama bahan (cth: Kopi Arabika)" required>
                            </div>
                            <div class="col-4">
                                <input type="number" step="0.01" name="amounts[]" class="form-control bg-dark text-white border-secondary" placeholder="Jumlah" required>
                            </div>
                            <div class="col-3">
                                <input type="text" name="units[]" class="form-control bg-dark text-white border-secondary ingredient-unit" placeholder="Satuan (cth: gr)" required>
                            </div>
                        </div>
                    `;
                    container.appendChild(row);
                    attachRemoveEvent(row.querySelector('.remove-ingredient'));
                });
            }

            function attachRemoveEvent(button) {
                if (!button) return;
                button.addEventListener('click', (e) => {
                    const rows = document.querySelectorAll('.ingredient-row');
                    if (rows.length > 1) {
                        const elementToRemove = e.target.closest('.ingredient-row');
                        if (elementToRemove) elementToRemove.remove();
                    } else {
                        alert('Minimal satu baris bahan baku.');
                    }
                });
            }

            document.querySelectorAll('.remove-ingredient').forEach(attachRemoveEvent);

            function attachRemoveAddon(button) {
                if (!button) return;
                button.addEventListener('click', (e) => {
                    const row = e.target.closest('.addon-row');
                    if (row && document.querySelectorAll('.addon-row').length > 1) {
                        row.remove();
                    } else if (row) {
                        row.querySelectorAll('input').forEach(input => input.value = '');
                    }
                });
            }

            if (addAddonButton && addonsContainer) {
                addAddonButton.addEventListener('click', () => {
                    const row = document.createElement('div');
                    row.className = 'mb-2 addon-row row g-2';
                    row.innerHTML = `
                        <div class="col-md-7">
                            <input type="text" name="addon_names[]" class="form-control bg-dark text-white border-secondary" placeholder="Nama add-on">
                        </div>
                        <div class="col-md-4">
                            <input type="number" name="addon_prices[]" class="form-control bg-dark text-white border-secondary" placeholder="Harga">
                        </div>
                        <div class="col-md-1">
                            <button type="button" class="btn btn-outline-danger w-100 remove-addon"><i class="bi bi-trash"></i></button>
                        </div>
                    `;
                    addonsContainer.appendChild(row);
                    attachRemoveAddon(row.querySelector('.remove-addon'));
                });
            }

            document.querySelectorAll('.remove-addon').forEach(attachRemoveAddon);
        </script>
    @endpush
@endsection
