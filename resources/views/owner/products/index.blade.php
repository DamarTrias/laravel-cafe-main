@extends('layouts.dashboard')

@section('title', 'Produk')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-white mb-0">Manajemen Produk</h2>
    <a href="{{ route('owner.products.create') }}" class="btn btn-primary shadow">
        <i class="bi bi-plus-lg me-1"></i> Tambah Produk
    </a>
</div>

<div class="row mb-4">
    <div class="col-md-6 col-lg-4">
        <form action="{{ route('owner.products.index') }}" method="GET">
            <div class="input-group glass-card p-1 rounded-pill overflow-hidden shadow-sm" style="background: rgba(255, 255, 255, 0.25); border: 1px solid rgba(255, 255, 255, 0.4) !important;">
                <span class="input-group-text bg-transparent border-0 text-white ps-3">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text" name="search" class="form-control bg-transparent border-0 text-white shadow-none placeholder-white-50" 
                    placeholder="Cari nama produk..." value="{{ request('search') }}">
                <button class="btn btn-primary rounded-pill px-3 py-1 me-1" type="submit">Cari</button>
            </div>
        </form>
    </div>
    @if(request('search'))
        <div class="col-md-6 col-lg-8 d-flex align-items-center">
            <a href="{{ route('owner.products.index') }}" class="btn btn-link text-muted text-decoration-none">
                <i class="bi bi-x-circle me-1"></i> Hapus Pencarian
            </a>
        </div>
    @endif
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show glass-card border-0 text-white mb-4" role="alert">
        <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="glass-card card border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0 text-white align-middle">
                <thead>
                    <tr>
                        <th class="ps-4 py-3">No</th>
                        <th class="py-3">Produk</th>
                        <th class="py-3">Kategori</th>
                        <th class="py-3">Harga</th>
                        <th class="py-3 text-center">Tersedia (Porsi)</th>
                        <th class="py-3">Status</th>
                        <th class="text-end pe-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        @php $maxQty = $product->max_quantity; @endphp
                        <tr class="border-light border-opacity-10 {{ $maxQty < 5 ? 'bg-warning bg-opacity-10' : '' }}">
                            <td class="ps-4">{{ $loop->iteration }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="rounded me-3 overflow-hidden border border-light border-opacity-25" style="width: 48px; height: 48px;">
                                        @if($product->image)
                                            <img src="{{ Storage::url($product->image) }}" alt="image" class="w-100 h-100 object-fit-cover">
                                        @else
                                            <div class="w-100 h-100 d-flex align-items-center justify-content-center bg-secondary bg-opacity-25">
                                                <i class="bi bi-image text-muted"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $product->name }}</div>
                                        <div class="text-muted small text-truncate" style="max-width: 150px;">{{ $product->description ?? 'Tidak ada deskripsi' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td><span class="badge bg-dark bg-opacity-50 text-white border border-light border-opacity-25 fw-normal">{{ $product->category->name }}</span></td>
                            <td class="fw-bold">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                            <td class="text-center">
                                <span class="badge {{ $maxQty > 10 ? 'bg-success' : ($maxQty > 0 ? 'bg-warning' : 'bg-danger') }} bg-opacity-75">
                                    {{ $maxQty }} porsi
                                </span>
                            </td>
                            <td>
                                @if($product->is_actually_available)
                                    <span class="badge bg-primary bg-opacity-25 text-primary border border-primary border-opacity-25">Tersedia</span>
                                @else
                                    <span class="badge bg-secondary bg-opacity-25 text-muted border border-secondary border-opacity-25">Habis</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group">
                                    <a href="{{ route('owner.products.edit', $product) }}" class="btn btn-sm btn-outline-info border-opacity-25 rounded-pill me-2 px-3">
                                        <i class="bi bi-pencil-square me-1"></i> Edit
                                    </a>
                                    <form action="{{ route('owner.products.destroy', $product) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger border-opacity-25 rounded-pill px-3" onclick="return confirm('Apakah Anda yakin ingin menghapus produk ini?')">
                                            <i class="bi bi-trash me-1"></i> Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-box-seam fs-1 mb-3 d-block"></i>
                                    <p class="mb-0">Belum ada produk yang ditambahkan.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection