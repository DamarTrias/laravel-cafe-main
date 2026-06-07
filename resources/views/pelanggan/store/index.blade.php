@extends('layouts.dashboard')
@section('title', 'Katalog Menu')
@section('content')

{{-- Search --}}
<div class="row justify-content-center mb-5 mt-4">
    <div class="col-md-8 text-center">
        <h1 class="display-4 fw-bold text-primary mb-3">Menu Kami</h1>
        <p class="text-muted fs-5 mb-4">Pesan kopi dan hidangan favoritmu sekarang.</p>
        @php $baseUrl = isset($category) ? route('pelanggan.category', $category) : route('pelanggan.store'); @endphp
        <form action="{{ $baseUrl }}" method="GET">
            <div class="input-group glass-card p-1 rounded-pill overflow-hidden shadow-sm" style="background:rgba(255,255,255,.25);border:1px solid rgba(255,255,255,.4)!important">
                <span class="input-group-text bg-transparent border-0 text-white ps-4"><i class="bi bi-search"></i></span>
                <input type="text" name="search" class="form-control bg-transparent border-0 text-white py-3 shadow-none menu-search-input" placeholder="Cari kopi, cemilan, atau makanan..." value="{{ request('search') }}">
                <button class="btn btn-primary rounded-pill px-4 me-1 py-1 my-1" type="submit">Cari</button>
            </div>
            @if(request('search'))
                <div class="mt-3"><a href="{{ $baseUrl }}" class="text-muted text-decoration-none small"><i class="bi bi-x-circle me-1"></i>Hapus Pencarian: "{{ request('search') }}"</a></div>
            @endif
        </form>
    </div>
</div>

{{-- Category Filter (Desktop) --}}
<div class="d-none d-md-flex overflow-auto pb-3 mb-4 justify-content-md-center gap-2">
    <a href="{{ route('pelanggan.store') }}" class="btn {{ !isset($category) ? 'btn-primary' : 'glass-card border-0 text-white' }} px-4 py-2 rounded-pill">Semua Menu</a>
    @foreach($categories as $cat)
        <a href="{{ route('pelanggan.category', $cat->id) }}" class="btn {{ isset($category) && $category->id == $cat->id ? 'btn-primary' : 'glass-card border-0 text-white' }} px-4 py-2 rounded-pill">{{ $cat->name }}</a>
    @endforeach
</div>

{{-- Category Filter (Mobile) --}}
<div id="mobileCategorySticky" class="d-md-none mb-4 mobile-category-sticky">
    <div id="mobileCategoryTabs" class="mobile-category-tabs">
        <a href="{{ isset($category) ? route('pelanggan.store') : '#mobile-category-all' }}" class="mobile-category-tab {{ !isset($category) && !$searchCategoryId ? 'active' : '' }}" data-target="mobile-category-all">Semua Menu</a>
        @foreach($categories as $cat)
            <a href="{{ isset($category) ? route('pelanggan.category', $cat->id) : '#mobile-category-' . $cat->id }}" class="mobile-category-tab {{ $searchCategoryId == $cat->id ? 'active' : '' }}" data-target="mobile-category-{{ $cat->id }}">{{ $cat->name }}</a>
        @endforeach
    </div>
</div>

{{-- Product Grid --}}
<div id="mobile-category-all" class="mobile-menu-section d-md-none" @unless($searchCategoryId) data-category-id="mobile-category-all" @endunless>
    <h4 class="mobile-category-heading">{{ isset($category) ? $category->name : 'Semua Menu' }}</h4>
</div>
<div class="row g-4 mt-2 menu-product-list desktop-menu-grid d-none d-md-flex">
    @forelse($products as $product)
    @php $maxQty = $product->max_quantity; @endphp
    <div class="col-12 col-sm-6 col-lg-3 col-xl-2 menu-product-col">
        <div class="glass-card card h-100 border-0 p-2 text-center d-flex flex-column product-card"
             style="overflow:hidden;cursor:pointer"
             data-bs-toggle="modal" data-bs-target="#productDetailModal"
             data-name="{{ $product->name }}"
             data-category="{{ $product->category->name }}"
             data-price="Rp {{ number_format($product->price, 0, ',', '.') }}"
             data-description="{{ $product->description ?? '' }}"
             data-image="{{ $product->image ? Storage::url($product->image) : '' }}"
             data-available="{{ $product->is_actually_available ? '1' : '0' }}"
             data-max-qty="{{ $maxQty }}"
             data-addons='@json($product->addons->map(fn ($addon) => ["id" => $addon->id, "name" => $addon->name, "price" => (int) $addon->price])->values())'
             data-add-url="{{ route('pelanggan.cart.add', $product) }}">

            <div class="position-relative bg-dark rounded mb-3 overflow-hidden shadow-sm product-image-wrap" style="aspect-ratio:1/1">
                @if($product->image)
                    <img data-src="{{ Storage::url($product->thumbnail_image) }}" class="w-100 h-100 object-fit-cover rounded deferred-desktop-image" alt="{{ $product->name }}" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='{{ Storage::url($product->image) }}';">
                @else
                    <div class="w-100 h-100 d-flex align-items-center justify-content-center bg-secondary bg-opacity-10 text-muted"><i class="bi bi-cup-hot fs-1"></i></div>
                @endif
                @if($maxQty < 5 && $maxQty > 0)
                    <div class="position-absolute top-0 end-0 m-2"><span class="badge bg-danger bg-opacity-75">Sisa {{ $maxQty }} porsi</span></div>
                @endif
                @if(!$product->is_actually_available)
                    <div class="position-absolute top-0 start-0 w-100 h-100 bg-dark bg-opacity-50 d-flex align-items-center justify-content-center">
                        <span class="badge bg-secondary px-3 py-2 fs-6">Habis</span>
                    </div>
                @endif
            </div>

            <div class="card-body p-0 d-flex flex-column flex-grow-1">
                <span class="text-primary small fw-semibold mb-1 d-block">{{ $product->category->name }}</span>
                <h5 class="fw-bold text-white mb-1">{{ $product->name }}</h5>
                <p class="text-white small mb-3" style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;line-height:1.5;min-height:2.8em">{{ $product->description ?? '-' }}</p>
                <div class="mt-auto">
                    <div class="fs-5 fw-bold text-white mb-3">Rp {{ number_format($product->price, 0, ',', '.') }}</div>
                    @if($product->is_actually_available)
                        <div class="btn btn-primary w-100 py-2 rounded-pill fw-bold"><i class="bi bi-eye me-1"></i> Lihat Detail</div>
                    @else
                        <button class="btn btn-secondary w-100 py-2 rounded-pill" disabled>Stok Habis</button>
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

<div class="mobile-product-sections d-md-none">
    @forelse($mobileGroups as $group)
        <section id="mobile-category-{{ $group['category']->id }}" class="mobile-menu-section" data-category-id="mobile-category-{{ $group['category']->id }}">
            @unless(isset($category))
                <h4 class="mobile-category-heading">{{ $group['category']->name }}</h4>
            @endunless

            <div class="row g-4 mt-2 menu-product-list">
                @foreach($group['products'] as $product)
                    @php $maxQty = $product->max_quantity; @endphp
                    <div class="col-12 menu-product-col">
                        <div class="glass-card card h-100 border-0 p-2 text-center d-flex flex-column product-card"
                             style="overflow:hidden;cursor:pointer"
                             data-bs-toggle="modal" data-bs-target="#productDetailModal"
                             data-name="{{ $product->name }}"
                             data-category="{{ $product->category->name }}"
                             data-price="Rp {{ number_format($product->price, 0, ',', '.') }}"
                             data-description="{{ $product->description ?? '' }}"
                             data-image="{{ $product->image ? Storage::url($product->image) : '' }}"
                             data-available="{{ $product->is_actually_available ? '1' : '0' }}"
                             data-max-qty="{{ $maxQty }}"
                             data-addons='@json($product->addons->map(fn ($addon) => ["id" => $addon->id, "name" => $addon->name, "price" => (int) $addon->price])->values())'
                             data-add-url="{{ route('pelanggan.cart.add', $product) }}">

                            <div class="position-relative bg-dark rounded mb-3 overflow-hidden shadow-sm product-image-wrap" style="aspect-ratio:1/1">
                                @if($product->image)
                                    <img src="{{ Storage::url($product->thumbnail_image) }}" class="w-100 h-100 object-fit-cover rounded" alt="{{ $product->name }}" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='{{ Storage::url($product->image) }}';">
                                @else
                                    <div class="w-100 h-100 d-flex align-items-center justify-content-center bg-secondary bg-opacity-10 text-muted"><i class="bi bi-cup-hot fs-1"></i></div>
                                @endif
                                @if($maxQty < 5 && $maxQty > 0)
                                    <div class="position-absolute top-0 end-0 m-2"><span class="badge bg-danger bg-opacity-75">Sisa {{ $maxQty }} porsi</span></div>
                                @endif
                                @if(!$product->is_actually_available)
                                    <div class="position-absolute top-0 start-0 w-100 h-100 bg-dark bg-opacity-50 d-flex align-items-center justify-content-center">
                                        <span class="badge bg-secondary px-3 py-2 fs-6">Habis</span>
                                    </div>
                                @endif
                            </div>

                            <div class="card-body p-0 d-flex flex-column flex-grow-1">
                                <span class="text-primary small fw-semibold mb-1 d-block">{{ $product->category->name }}</span>
                                <h5 class="fw-bold text-white mb-1">{{ $product->name }}</h5>
                                <p class="text-white small mb-3" style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;line-height:1.5;min-height:2.8em">{{ $product->description ?? '-' }}</p>
                                <div class="mt-auto">
                                    <div class="fs-5 fw-bold text-white mb-3">Rp {{ number_format($product->price, 0, ',', '.') }}</div>
                                    @if($product->is_actually_available)
                                        <div class="btn btn-primary w-100 py-2 rounded-pill fw-bold"><i class="bi bi-eye me-1"></i> Lihat Detail</div>
                                    @else
                                        <button class="btn btn-secondary w-100 py-2 rounded-pill" disabled>Stok Habis</button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @empty
        <div class="col-12 text-center py-5">
            <i class="bi bi-inbox fs-1 text-muted mb-3 d-block"></i>
            <h4 class="text-white">Tidak ada produk ditemukan.</h4>
        </div>
    @endforelse
</div>

{{-- Modal Detail Produk --}}
<div class="modal fade" id="productDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="background:rgba(18,18,24,.97);backdrop-filter:blur(20px);border:1px solid rgba(255,255,255,.08)!important;border-radius:20px;overflow:hidden">
            <div class="modal-header border-0 pb-0 px-4 pt-4">
                <span id="modalCategory" class="badge bg-primary bg-opacity-25 text-primary fw-semibold px-3 py-2 rounded-pill"></span>
                <button type="button" class="btn-close btn-close-white opacity-50" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 pb-0">
                <div class="row g-4 align-items-center">
                    <div class="col-md-5">
                        <div class="rounded-3 overflow-hidden shadow" style="aspect-ratio:1/1;background:rgba(255,255,255,.04)">
                            <img id="modalImage" src="" alt="" class="w-100 h-100 object-fit-cover" style="display:none">
                            <div id="modalImagePlaceholder" class="w-100 h-100 d-flex align-items-center justify-content-center text-muted">
                                <i class="bi bi-cup-hot" style="font-size:4rem"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <h2 id="modalName" class="fw-bold text-white mb-1" style="font-size:1.6rem"></h2>
                        <div id="modalStockBadge" class="mb-3"></div>
                        <div class="mb-3">
                            <span class="text-muted small d-block mb-1" style="text-transform:uppercase;letter-spacing:1px;font-size:.72rem">Harga</span>
                            <span id="modalPrice" class="fw-bold text-white" style="font-size:2rem"></span>
                        </div>
                        <div id="modalDescriptionBlock" class="mb-4">
                            <span class="text-muted small d-block mb-1" style="text-transform:uppercase;letter-spacing:1px;font-size:.72rem">Deskripsi</span>
                            <p id="modalDescription" class="text-white mb-0" style="font-size:.95rem;line-height:1.6"></p>
                        </div>
                        <div id="modalAddonsBlock" class="mb-4" style="display:none">
                            <span class="text-muted small d-block mb-2" style="text-transform:uppercase;letter-spacing:1px;font-size:.72rem">Add-on</span>
                            <div id="modalAddonsList" class="d-grid gap-2"></div>
                        </div>
                        <div id="modalActionBlock"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 px-4 pb-4 pt-3">
                <button type="button" class="btn btn-outline-secondary text-white rounded-pill px-4" data-bs-dismiss="modal">Kembali</button>
            </div>
        </div>
    </div>
</div>

<div id="cartSuccessToast" class="cart-success-toast" role="status" aria-live="polite">
    <i class="bi bi-check-circle-fill me-2"></i>
    <span>Produk ditambahkan ke keranjang.</span>
</div>

@push('scripts')
<style>
    .product-card { transition: none; }
    .product-card:hover { transform: none; box-shadow: none !important; }
    .menu-search-input::placeholder { color: #ffffff !important; opacity: 1; }
    .mobile-category-tabs {
        display: flex;
        gap: .65rem;
        overflow-x: auto;
        padding: .25rem 1rem .75rem;
        margin: 0 -1rem;
        scroll-behavior: smooth;
        scrollbar-width: none;
        -webkit-overflow-scrolling: touch;
    }
    .mobile-category-tabs::-webkit-scrollbar { display: none; }
    .mobile-category-tab {
        flex: 0 0 auto;
        color: #ffffff;
        font-weight: 700;
        text-decoration: none;
        padding: .65rem 1rem;
        border-bottom: 3px solid transparent;
        opacity: .8;
        white-space: nowrap;
    }
    .mobile-category-tab.active {
        color: var(--primary-color);
        border-bottom-color: var(--primary-color);
        opacity: 1;
    }
    .mobile-category-heading { display: none; }
    .cart-success-toast {
        position: fixed;
        top: 5rem;
        right: 1.25rem;
        z-index: 2000;
        display: flex;
        align-items: center;
        max-width: min(360px, calc(100vw - 2rem));
        padding: .85rem 1rem;
        border-radius: 14px;
        background: rgba(25, 135, 84, .96);
        color: #ffffff;
        font-weight: 800;
        box-shadow: 0 14px 30px rgba(0, 0, 0, .32);
        opacity: 0;
        pointer-events: none;
        transform: translateY(-12px);
        transition: opacity .2s ease, transform .2s ease;
    }
    .cart-success-toast.show {
        opacity: 1;
        transform: translateY(0);
    }

    @media (min-width: 768px) {
        body:has(.desktop-menu-grid) main.container {
            width: 100%;
            max-width: none;
            padding-left: clamp(1rem, 3vw, 3.5rem) !important;
            padding-right: clamp(1rem, 3vw, 3.5rem) !important;
        }

        body:has(.desktop-menu-grid) .row.justify-content-center.mb-5.mt-4 {
            max-width: 900px;
            margin-left: auto;
            margin-right: auto;
        }

        .desktop-menu-grid {
            --bs-gutter-x: 1.5rem;
            --bs-gutter-y: 1.75rem;
        }

        .navbar-glass,
        .glass-card {
            backdrop-filter: none;
            -webkit-backdrop-filter: none;
        }

        .glass-card {
            background: rgba(18, 18, 24, .82);
            box-shadow: 0 4px 18px rgba(0, 0, 0, .25);
            transition: none;
        }

        .glass-card:hover {
            transform: none;
            box-shadow: 0 4px 18px rgba(0, 0, 0, .25);
        }

        .product-card {
            content-visibility: auto;
            contain-intrinsic-size: 360px;
            contain: layout paint style;
        }
    }

    @media (max-width: 767.98px) {
        html,
        body {
            overflow-x: clip !important;
        }

        main.container {
            width: 100%;
            max-width: 100%;
            overflow: visible;
            padding: 1.25rem 1rem 2rem !important;
        }

        .row.justify-content-center.mb-5.mt-4 {
            margin-top: 1rem !important;
            margin-bottom: 1rem !important;
        }

        .display-4 {
            font-size: 1.65rem;
            line-height: 1.15;
        }

        .row.justify-content-center.mb-5.mt-4 p {
            font-size: .9rem !important;
            margin-bottom: 1rem !important;
        }

        .menu-search-input {
            font-size: .78rem;
            min-width: 0;
        }

        .input-group .btn {
            padding-left: 1rem !important;
            padding-right: 1rem !important;
        }

        .mobile-category-sticky {
            position: sticky;
            top: var(--mobile-navbar-height, 0px);
            z-index: 1019;
            margin: 0 -1rem .75rem;
            background: rgba(15, 15, 17, .96);
            border-bottom: 1px solid rgba(255, 255, 255, .08);
            box-shadow: 0 6px 14px rgba(0, 0, 0, .18);
        }

        .mobile-category-tabs {
            gap: 1.35rem;
            padding: .75rem 1rem .25rem;
            margin: 0;
        }

        .mobile-category-tab {
            padding: .6rem 0 .55rem;
            font-size: .86rem;
            letter-spacing: 0;
        }

        .mobile-category-heading {
            display: block;
            color: #ffffff;
            font-weight: 800;
            font-size: 1.25rem;
            margin: 1rem 0 .75rem;
        }

        .menu-product-list {
            --bs-gutter-x: 0;
            --bs-gutter-y: 0;
            margin-top: 0 !important;
        }

        .menu-product-col {
            border-bottom: 1px solid rgba(255, 255, 255, .12);
            padding: 1rem 0 !important;
            content-visibility: auto;
            contain-intrinsic-size: 140px;
        }

        .product-card {
            display: grid !important;
            grid-template-columns: 112px minmax(0, 1fr);
            column-gap: .85rem;
            align-items: start;
            min-height: 132px;
            padding: .75rem !important;
            background: rgba(18, 18, 24, .78) !important;
            border: 0 !important;
            box-shadow: none !important;
            text-align: left !important;
            cursor: pointer;
            contain: layout paint style;
        }

        .product-card:hover {
            transform: none;
            box-shadow: none !important;
        }

        .product-image-wrap {
            width: 112px;
            height: 112px;
            aspect-ratio: auto !important;
            margin-bottom: 0 !important;
            border-radius: 12px !important;
            grid-row: 1 / span 2;
        }

        .product-image-wrap img,
        .product-image-wrap > div {
            border-radius: 12px !important;
        }

        .product-card .card-body {
            min-width: 0;
            display: block !important;
        }

        .product-card .card-body .text-primary {
            display: none !important;
        }

        .product-card h5 {
            font-size: 1rem;
            line-height: 1.25;
            margin-bottom: .35rem !important;
        }

        .product-card p {
            font-size: .86rem !important;
            line-height: 1.35 !important;
            min-height: 0 !important;
            margin-bottom: .75rem !important;
            -webkit-line-clamp: 2 !important;
        }

        .product-card .fs-5 {
            font-size: 1.05rem !important;
            margin-bottom: .75rem !important;
        }

        .product-card .btn {
            width: auto !important;
            min-width: 96px;
            padding: .45rem 1rem !important;
            border-radius: 12px !important;
            float: right;
            font-size: .85rem;
            color: #ffffff !important;
        }

        .modal-content {
            backdrop-filter: none !important;
            -webkit-backdrop-filter: none !important;
        }

        .cart-success-toast {
            top: auto;
            right: 1rem;
            bottom: 1rem;
            left: 1rem;
            max-width: none;
            justify-content: center;
        }
    }
</style>
<script>
const customerIsLoggedIn = @json(auth()->check());
const loginToOrderUrl = @json(route('login', ['redirect' => url()->full()]));

document.addEventListener('DOMContentLoaded', function() {
    const tabs = document.querySelector('#mobileCategoryTabs');
    const stickyBar = document.querySelector('#mobileCategorySticky');
    const sections = Array.from(document.querySelectorAll('.mobile-menu-section[data-category-id]'));
    const tabLinks = Array.from(document.querySelectorAll('#mobileCategoryTabs .mobile-category-tab'));
    const navbar = document.querySelector('.navbar');

    if (!tabs || !stickyBar || !sections.length || !tabLinks.length) {
        return;
    }

    const updateNavbarHeight = function() {
        document.documentElement.style.setProperty('--mobile-navbar-height', `${navbar ? navbar.offsetHeight : 0}px`);
    };

    const stickyOffset = function() {
        return (navbar ? navbar.offsetHeight : 0) + stickyBar.offsetHeight + 12;
    };

    const documentTop = function(element) {
        return window.scrollY + element.getBoundingClientRect().top;
    };

    const centerTab = function(tab) {
        tabs.scrollLeft = tab.offsetLeft - (tabs.clientWidth / 2) + (tab.clientWidth / 2);
    };

    const setActiveTab = function(targetId) {
        const activeTab = tabLinks.find((tab) => tab.dataset.target === targetId);
        if (!activeTab || activeTab.classList.contains('active')) {
            return;
        }

        tabLinks.forEach((tab) => tab.classList.remove('active'));
        activeTab.classList.add('active');
        centerTab(activeTab);
    };

    const updateActiveByScroll = function() {
        const triggerLine = window.scrollY + stickyOffset();
        let currentSection = sections[0];

        if (window.innerHeight + window.scrollY >= document.documentElement.scrollHeight - 2) {
            setActiveTab(sections[sections.length - 1].dataset.categoryId);
            return;
        }

        sections.forEach((section) => {
            if (documentTop(section) <= triggerLine) {
                currentSection = section;
            }
        });

        if (currentSection) {
            setActiveTab(currentSection.dataset.categoryId);
        }
    };

    let scrollTicking = false;
    const requestScrollUpdate = function() {
        if (scrollTicking) {
            return;
        }

        scrollTicking = true;
        requestAnimationFrame(function() {
            updateActiveByScroll();
            scrollTicking = false;
        });
    };

    tabLinks.forEach((tab) => {
        tab.addEventListener('click', function(event) {
            if (!this.hash) {
                return;
            }

            const target = document.querySelector(this.hash);
            if (!target) {
                return;
            }

            event.preventDefault();
            setActiveTab(this.dataset.target);
            window.scrollTo({
                top: Math.max(documentTop(target) - stickyOffset(), 0),
                behavior: 'smooth'
            });
        });
    });

    const initialActive = document.querySelector('#mobileCategoryTabs .mobile-category-tab.active') || tabLinks[0];
    if (initialActive) {
        centerTab(initialActive);
    }

    updateNavbarHeight();
    updateActiveByScroll();
    window.addEventListener('scroll', requestScrollUpdate, { passive: true });
    window.addEventListener('resize', function() {
        updateNavbarHeight();
        requestScrollUpdate();
    });
});

function loadDesktopImages() {
    if (!window.matchMedia('(min-width: 768px)').matches) {
        return;
    }

    document.querySelectorAll('.deferred-desktop-image[data-src]').forEach(function(image) {
        image.src = image.dataset.src;
        image.removeAttribute('data-src');
    });
}

document.addEventListener('DOMContentLoaded', loadDesktopImages);
window.addEventListener('resize', loadDesktopImages);

function showCartSuccessToast(message) {
    const toast = document.getElementById('cartSuccessToast');
    if (!toast) {
        return;
    }

    toast.querySelector('span').textContent = message || 'Produk ditambahkan ke keranjang.';
    toast.classList.add('show');
    clearTimeout(toast.hideTimer);
    toast.hideTimer = setTimeout(function() {
        toast.classList.remove('show');
    }, 2200);
}

document.getElementById('productDetailModal').addEventListener('show.bs.modal', function(e) {
    const d = e.relatedTarget.dataset;
    const available = d.available === '1', maxQty = parseInt(d.maxQty);
    this.querySelector('#modalCategory').textContent = d.category;
    this.querySelector('#modalName').textContent = d.name;
    this.querySelector('#modalPrice').textContent = d.price;

    const desc = d.description?.trim();
    const descBlock = this.querySelector('#modalDescriptionBlock');
    descBlock.style.display = desc ? '' : 'none';
    if (desc) this.querySelector('#modalDescription').textContent = desc;

    let addons = [];
    try {
        addons = JSON.parse(d.addons || '[]');
    } catch (error) {
        addons = [];
    }

    const addonsBlock = this.querySelector('#modalAddonsBlock');
    const addonsList = this.querySelector('#modalAddonsList');
    addonsBlock.style.display = addons.length ? '' : 'none';
    addonsList.innerHTML = addons.map((addon) => `
        <label class="d-flex align-items-center justify-content-between gap-3 p-2 rounded border border-secondary border-opacity-25 bg-dark bg-opacity-25 text-white">
            <span class="d-flex align-items-center gap-2">
                <input class="form-check-input m-0" type="checkbox" name="addons[]" value="${addon.id}" form="modalAddToCartForm">
                <span>${addon.name}</span>
            </span>
            <span class="text-primary fw-bold">+ Rp ${new Intl.NumberFormat('id-ID').format(addon.price)}</span>
        </label>
    `).join('');

    const img = this.querySelector('#modalImage'), ph = this.querySelector('#modalImagePlaceholder');
    const hasImg = d.image?.trim();
    img.style.display = hasImg ? 'block' : 'none';
    ph.style.display  = hasImg ? 'none'  : 'flex';
    if (hasImg) { img.src = d.image; img.alt = d.name; }

    const stock = this.querySelector('#modalStockBadge');
    stock.innerHTML = !available
        ? '<span class="badge bg-danger bg-opacity-20 text-danger px-3 py-2 rounded-pill"><i class="bi bi-x-circle me-1"></i>Stok Habis</span>'
        : maxQty < 5 && maxQty > 0
            ? `<span class="badge bg-warning bg-opacity-20 text-warning px-3 py-2 rounded-pill"><i class="bi bi-exclamation-circle me-1"></i>Sisa ${maxQty} porsi lagi</span>`
            : '<span class="badge bg-success bg-opacity-20 text-white px-3 py-2 rounded-pill"><i class="bi bi-check-circle me-1"></i>Tersedia</span>';

    if (!available) {
        this.querySelector('#modalActionBlock').innerHTML = '<button class="btn btn-secondary w-100 py-3 rounded-pill fw-bold" disabled>Stok Habis</button>';
        return;
    }

    this.querySelector('#modalActionBlock').innerHTML = customerIsLoggedIn
        ? `<form id="modalAddToCartForm" action="${d.addUrl}" method="POST" class="ajax-add-to-cart"><input type="hidden" name="_token" value="{{ csrf_token() }}"><button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-bold"><i class="bi bi-cart-plus me-2"></i>Tambah ke Keranjang</button></form>`
        : `<a href="${loginToOrderUrl}" class="btn btn-primary w-100 py-3 rounded-pill fw-bold"><i class="bi bi-box-arrow-in-right me-2"></i>Login untuk Pesan</a>`;
});

document.addEventListener('submit', async function(event) {
    const form = event.target;
    if (!form.classList.contains('ajax-add-to-cart')) {
        return;
    }

    event.preventDefault();

    const button = form.querySelector('button');
    const originalText = button.innerHTML;
    button.disabled = true;
    button.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menambahkan...';

    try {
        const response = await fetch(form.action, {
            method: form.method || 'POST',
            body: new FormData(form),
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
        });

        const data = await response.json();
        if (!response.ok || !data.success) {
            alert(data.message || 'Produk gagal ditambahkan.');
            return;
        }

        button.innerHTML = '<i class="bi bi-check-circle me-2"></i>Ditambahkan';

        if (window.updateCartCountBadges) {
            window.updateCartCountBadges(data.cart_count);
        }

        const productModal = document.getElementById('productDetailModal');
        const modalInstance = bootstrap.Modal.getInstance(productModal);
        if (modalInstance) {
            modalInstance.hide();
        }
        showCartSuccessToast(data.message || 'Produk ditambahkan ke keranjang.');

        setTimeout(function() {
            button.disabled = false;
            button.innerHTML = originalText;
        }, 900);
    } catch (error) {
        alert('Koneksi lambat. Coba klik lagi sebentar.');
        button.disabled = false;
        button.innerHTML = originalText;
    }
});
</script>
@endpush
@endsection
