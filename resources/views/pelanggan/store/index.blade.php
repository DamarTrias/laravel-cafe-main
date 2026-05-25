@extends('layouts.dashboard')

@section('title', 'Katalog Menu')

@section('content')
<div class="row justify-content-center mb-5 mt-4">
    <div class="col-md-8 text-center">
        <h1 class="display-4 fw-bold text-primary mb-3">Menu Kami</h1>
        <p class="text-muted fs-5 mb-4">Pesan kopi dan hidangan favoritmu sekarang.</p>
        
        <form action="{{ isset($category) ? route('pelanggan.category', $category) : route('pelanggan.store') }}" method="GET" class="position-relative">
            <div class="input-group glass-card p-1 rounded-pill overflow-hidden shadow-sm" style="background: rgba(255, 255, 255, 0.25); border: 1px solid rgba(255, 255, 255, 0.4) !important;">
                <span class="input-group-text bg-transparent border-0 text-white ps-4">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text" name="search" class="form-control bg-transparent border-0 text-white py-3 shadow-none placeholder-white-50" 
                    placeholder="Cari kopi, cemilan, atau makanan..." value="{{ request('search') }}">
                <button class="btn btn-primary rounded-pill px-4 me-1 py-1 my-1" type="submit">Cari</button>
            </div>
            @if(request('search'))
                <div class="mt-3">
                    <a href="{{ isset($category) ? route('pelanggan.category', $category) : route('pelanggan.store') }}" class="text-muted text-decoration-none small">
                        <i class="bi bi-x-circle me-1"></i> Hapus Pencarian: "{{ request('search') }}"
                    </a>
                </div>
            @endif
        </form>
    </div>
</div>

<!-- Category Filters -->
<!-- Desktop View: Horizontal Scroll/Sliding Buttons -->
<div class="d-none d-md-flex overflow-auto pb-3 mb-4 justify-content-md-center gap-2">
    <a href="{{ route('pelanggan.store') }}" class="btn {{ !isset($category) ? 'btn-primary' : 'glass-card border-0 text-white' }} px-4 py-2 rounded-pill whitespace-nowrap">
        Semua Menu
    </a>
    @foreach($categories as $cat)
        <a href="{{ route('pelanggan.category', $cat->id) }}" class="btn {{ isset($category) && $category->id == $cat->id ? 'btn-primary' : 'glass-card border-0 text-white' }} px-4 py-2 rounded-pill whitespace-nowrap">
            {{ $cat->name }}
        </a>
    @endforeach
</div>

<!-- Mobile View: Hamburger Dropdown Menu -->
<div class="d-flex d-md-none justify-content-center mb-4">
    <div class="dropdown">
        <button class="btn glass-card border-0 text-white px-4 py-2 rounded-pill d-flex align-items-center gap-2 dropdown-toggle" 
                type="button" 
                id="categoryDropdown" 
                data-bs-toggle="dropdown" 
                aria-expanded="false"
                style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1) !important;">
            <i class="bi bi-list fs-5 text-primary"></i> 
            <span class="fw-semibold">Kategori: {{ isset($category) ? $category->name : 'Semua Menu' }}</span>
        </button>
        <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-start shadow-lg border-0 mt-2 p-2" 
            aria-labelledby="categoryDropdown" 
            style="background: rgba(15, 15, 17, 0.95); border: 1px solid rgba(255, 255, 255, 0.1) !important; backdrop-filter: blur(10px); border-radius: 12px; min-width: 200px;">
            <li>
                <a class="dropdown-item py-2 px-3 rounded {{ !isset($category) ? 'active bg-primary text-white' : 'text-white-50' }}" 
                   href="{{ route('pelanggan.store') }}">
                    Semua Menu
                </a>
            </li>
            @foreach($categories as $cat)
                <li>
                    <a class="dropdown-item py-2 px-3 rounded mt-1 {{ isset($category) && $category->id == $cat->id ? 'active bg-primary text-white' : 'text-white-50' }}" 
                       href="{{ route('pelanggan.category', $cat->id) }}">
                        {{ $cat->name }}
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
</div>

<div class="row g-4 mt-2">
    @forelse($products as $product)
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="glass-card card h-100 border-0 p-2 text-center d-flex flex-column transition hover-up" style="overflow: hidden;">
            <div class="position-relative bg-dark rounded mb-3 overflow-hidden shadow-sm" style="aspect-ratio: 1/1;">
                @if($product->image)
                    <img src="{{ Storage::url($product->image) }}" class="w-100 h-100 object-fit-cover rounded" alt="{{ $product->name }}">
                @else
                    <div class="w-100 h-100 d-flex align-items-center justify-content-center bg-secondary bg-opacity-10 text-muted">
                        <i class="bi bi-cup-hot fs-1"></i>
                    </div>
                @endif
                
                @php $maxQty = $product->max_quantity; @endphp
                
                @if($maxQty < 5 && $maxQty > 0)
                    <div class="position-absolute top-0 end-0 m-2">
                        <span class="badge bg-danger shadow-sm bg-opacity-75 backdrop-blur">Sisa {{ $maxQty }} porsi</span>
                    </div>
                @endif

                @if(!$product->is_actually_available)
                    <div class="position-absolute top-0 start-0 w-100 h-100 bg-dark bg-opacity-50 d-flex align-items-center justify-content-center backdrop-blur">
                        <span class="badge bg-secondary px-3 py-2 fs-6">Habis</span>
                    </div>
                @endif
            </div>
            
            <div class="card-body p-0 d-flex flex-column flex-grow-1">
                <span class="text-primary small fw-semibold mb-1 d-block">{{ $product->category->name }}</span>
                <h5 class="fw-bold text-white mb-2">{{ $product->name }}</h5>
                <p class="text-muted small lh-sm mb-3 text-truncate" title="{{ $product->description }}">{{ $product->description ?? 'Nikmati kelezatan menu favorit kami.' }}</p>
                <div class="mt-auto">
                    <div class="fs-5 fw-bold text-white mb-3">Rp {{ number_format($product->price, 0, ',', '.') }}</div>
                    
                    @if($product->is_actually_available)
                    <form action="{{ route('pelanggan.cart.add', $product) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary w-100 py-2 rounded-pill shadow-sm fw-bold">
                            <i class="bi bi-cart-plus me-1"></i> Tambah
                        </button>
                    </form>
                    @else
                    <button class="btn btn-secondary w-100 py-2 rounded-pill shadow-sm" disabled>Stok Habis</button>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12 text-center py-5">
        <i class="bi bi-inbox fs-1 text-muted mb-3 d-block"></i>
        <h4 class="text-white">Tidak ada produk ditemukan.</h4>
    </div>
    @endforelse
</div>
@endsection