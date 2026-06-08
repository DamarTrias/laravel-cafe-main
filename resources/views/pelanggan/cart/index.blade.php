@extends('layouts.dashboard')

@section('title', 'Keranjang Belanja')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h2 class="fw-bold text-white mb-0">Keranjang Belanja</h2>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="glass-card card border-0 mb-4">
            <div class="card-body p-0">
                @if(count($cart) > 0)
                    <ul class="list-group list-group-flush rounded bg-transparent">
                        @foreach($cart as $id => $item)
                        @php
                            $productId = $item['product_id'] ?? (int) $id;
                            $availableAddons = $cartProducts->get($productId)?->addons ?? collect();
                            $selectedAddonIds = collect($item['addons'] ?? [])->pluck('id')->map(fn($addonId) => (int) $addonId)->all();
                        @endphp
                        <li class="list-group-item bg-transparent text-white border-bottom border-secondary d-flex justify-content-between align-items-center p-4 cart-item-row" data-cart-item="{{ $id }}">
                            <div class="d-flex align-items-center cart-item-main">
                                @if(isset($item['image']) && $item['image'])
                                    @php
                                        $cartThumb = 'products/thumbs/' . basename($item['image']);
                                        $cartImage = Storage::disk('public')->exists($cartThumb) ? $cartThumb : $item['image'];
                                    @endphp
                                    <img src="{{ Storage::url($cartImage) }}" class="rounded me-3 object-fit-cover cart-item-image" width="60" height="60" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='{{ Storage::url($item['image']) }}';">
                                @else
                                    <div class="rounded me-3 d-flex align-items-center justify-content-center bg-dark cart-item-image" style="width: 60px; height: 60px;">
                                        <i class="bi bi-image text-muted"></i>
                                    </div>
                                @endif
                                <div class="cart-item-info">
                                    <h5 class="fw-bold mb-1 cart-item-name">{{ $item['name'] }}</h5>
                                    <span class="text-primary fw-medium mb-2 d-block">Rp {{ number_format($item['base_price'] ?? $item['price'], 0, ',', '.') }}</span>
                                    @if(!empty($item['addons']))
                                        <div class="small text-white mb-2">
                                            @foreach($item['addons'] as $addon)
                                                <div>+ {{ $addon['name'] }} <span class="text-primary">Rp {{ number_format($addon['price'], 0, ',', '.') }}</span></div>
                                            @endforeach
                                        </div>
                                    @endif
                                    <div class="d-flex align-items-center bg-dark rounded-pill px-2 py-1" style="width: fit-content; border: 1px solid rgba(255,255,255,0.1);">
                                        <form action="{{ route('pelanggan.cart.decrement', $item['product_id'] ?? $id) }}" method="POST" class="m-0 p-0 ajax-cart-form">
                                            @csrf
                                            <input type="hidden" name="cart_key" value="{{ $id }}">
                                            <button type="submit" class="btn btn-sm btn-link text-white text-decoration-none p-0 px-2 fs-5"><i class="bi bi-dash"></i></button>
                                        </form>
                                        <span class="mx-2 fw-bold cart-item-quantity">{{ $item['quantity'] }}</span>
                                        <form action="{{ route('pelanggan.cart.add', $item['product_id'] ?? $id) }}" method="POST" class="m-0 p-0 ajax-cart-form">
                                            @csrf
                                            @foreach($item['addons'] ?? [] as $addon)
                                                <input type="hidden" name="addons[]" value="{{ $addon['id'] }}">
                                            @endforeach
                                            <button type="submit" class="btn btn-sm btn-link text-white text-decoration-none p-0 px-2 fs-5"><i class="bi bi-plus"></i></button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="cart-item-note mt-3">
                                <label class="form-label text-white small mb-1" for="item_note_{{ $id }}">Catatan untuk {{ $item['name'] }} (Opsional)</label>
                                <textarea id="item_note_{{ $id }}" class="form-control bg-dark text-white border-secondary" name="item_notes[{{ $id }}]" form="checkoutForm" rows="2" maxlength="500" placeholder="Cth: Es dikurangin, jangan terlalu manis...">{{ old('item_notes.' . $id) }}</textarea>
                            </div>
                            @if($availableAddons->isNotEmpty())
                                <form action="{{ route('pelanggan.cart.update_addons') }}" method="POST" class="cart-item-addons mt-3">
                                    @csrf
                                    <input type="hidden" name="cart_key" value="{{ $id }}">
                                    <button type="button" class="btn btn-primary btn-sm rounded-pill px-3 fw-bold cart-addon-toggle">
                                        <i class="bi bi-sliders me-1"></i> Ubah Tambahan
                                    </button>
                                    <div class="cart-addon-panel mt-3">
                                        <div class="d-flex justify-content-between align-items-center gap-2 mb-2">
                                            <label class="form-label text-white small mb-0">Pilih Add-on</label>
                                            <button type="button" class="btn btn-link btn-sm text-white text-decoration-none p-0 cart-addon-close">Tutup</button>
                                        </div>
                                        <div class="cart-addon-options">
                                            @foreach($availableAddons as $addon)
                                                <label class="cart-addon-option">
                                                    <span class="d-flex align-items-center gap-2">
                                                        <input class="form-check-input m-0" type="checkbox" name="addons[]" value="{{ $addon->id }}" {{ in_array($addon->id, $selectedAddonIds, true) ? 'checked' : '' }}>
                                                        <span>{{ $addon->name }}</span>
                                                    </span>
                                                    <span class="text-primary fw-bold">+ Rp {{ number_format($addon->price, 0, ',', '.') }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                        <button type="submit" class="btn btn-outline-primary btn-sm rounded-pill px-3">Simpan</button>
                                    </div>
                                </form>
                            @endif
                            <div class="d-flex align-items-center cart-item-actions">
                                <span class="fw-bold fs-5 me-4 cart-item-subtotal">Rp {{ number_format(($item['unit_price'] ?? $item['price']) * $item['quantity'], 0, ',', '.') }}</span>
                                <form action="{{ route('pelanggan.cart.remove', $id) }}" method="POST" class="ajax-cart-form">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-outline-danger btn-sm rounded-circle"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-cart-x fs-1 text-muted d-block mb-3"></i>
                        <h5 class="text-white">Keranjang belanja Anda kosong.</h5>
                        <p class="text-white">Yuk, lihat <a href="{{ route('pelanggan.store') }}" class="text-primary text-decoration-none">katalog menu</a>.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @if(count($cart) > 0)
    <div class="col-lg-4">
        <div class="glass-card card border-0">
            <div class="card-header border-bottom border-light pt-4 pb-3">Ringkasan Pesanan</div>
            <div class="card-body p-4">
                <div class="d-flex justify-content-between mb-4 fs-4 fw-bold text-white">
                    <span>Total</span>
                    <span class="text-primary cart-grand-total">Rp {{ number_format($total, 0, ',', '.') }}</span>
                </div>
                
                <hr class="border-secondary mb-4">
                
                @if ($errors->any())
                    <div class="alert alert-danger border-0 rounded-3 small">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger border-0 rounded-3 small">{{ session('error') }}</div>
                @endif

                <form action="{{ route('pelanggan.checkout') }}" method="POST" id="checkoutForm" enctype="multipart/form-data" novalidate>
                    @csrf
                    <div class="mb-3">
                        <label class="form-label text-white">Tipe Pesanan</label>
                        <select class="form-select bg-dark text-white border-secondary mobile-hidden-select" name="order_type" id="order_type" onchange="toggleTableSelection()" required>
                            <option value="Dine In">Makan di Tempat (Dine In)</option>
                            <option value="Take Away">Bawa Pulang (Take Away)</option>
                        </select>
                        <div class="mobile-choice-group" aria-label="Pilih Tipe Pesanan">
                            <button type="button" class="mobile-choice-option active" data-select-target="order_type" data-select-value="Dine In">
                                Makan di Tempat
                            </button>
                            <button type="button" class="mobile-choice-option" data-select-target="order_type" data-select-value="Take Away">
                                Bawa Pulang
                            </button>
                        </div>
                    </div>

                    <div class="mb-3" id="table_selection_container">
                        <label class="form-label text-white d-flex justify-content-between align-items-center">
                            <span>Nomor Meja</span>
                            @if(session('table_number'))
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 small">
                                    <i class="bi bi-qr-code-scan me-1"></i> QR Terdeteksi
                                </span>
                            @endif
                        </label>
                        <select class="form-select bg-dark text-white border-secondary mobile-hidden-select table-number-select" name="table_number" id="table_number">
                            <option value="" disabled {{ !session('table_number') ? 'selected' : '' }}>Pilih Nomor Meja</option>
                            @for($i = 1; $i <= 20; $i++)
                                <option value="Meja {{ $i }}" {{ session('table_number') == 'Meja '.$i ? 'selected' : '' }}>Meja {{ $i }}</option>
                            @endfor
                        </select>
                        <div class="mobile-table-grid" aria-label="Pilih Nomor Meja">
                            @for($i = 1; $i <= 20; $i++)
                                <button type="button" class="mobile-table-option {{ session('table_number') == 'Meja '.$i ? 'active' : '' }}" data-table-number="Meja {{ $i }}">
                                    {{ $i }}
                                </button>
                            @endfor
                        </div>
                        <div class="table-number-error mt-2 small text-danger fw-semibold" style="display:none;">
                            Silakan pilih nomor meja terlebih dahulu.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-white">Metode Pembayaran</label>
                        <select class="form-select bg-dark text-white border-secondary mobile-hidden-select" name="payment_method" id="payment_method" required>
                            <option value="Cash">Cash (Di Kasir)</option>
                            <option value="QRIS">QRIS / Transfer Bank</option>
                        </select>
                        <div class="mobile-choice-group" aria-label="Pilih Metode Pembayaran">
                            <button type="button" class="mobile-choice-option active" data-select-target="payment_method" data-select-value="Cash">
                                Cash
                            </button>
                            <button type="button" class="mobile-choice-option" data-select-target="payment_method" data-select-value="QRIS">
                                QRIS / Transfer
                            </button>
                        </div>
                    </div>

                    <div class="mb-3" id="proof_payment_container" style="display:none;">
                        <label class="form-label text-white">Bukti Pembayaran QRIS</label>
                        <input id="proof_of_transfer" type="file" name="proof_of_transfer" class="form-control bg-dark text-white border-secondary" accept="image/jpeg,image/png,image/gif,image/webp">
                        <div class="proof-payment-error mt-2 small text-danger fw-semibold" style="display:none;">
                            Silakan upload bukti pembayaran QRIS terlebih dahulu.
                        </div>
                        @error('proof_of_transfer')
                            <div class="text-danger small mt-2">{{ $message }}</div>
                        @enderror
                        <div class="text-muted small mt-2">Format gambar: JPG, JPEG, PNG, GIF, atau WEBP. Maksimal 2 MB.</div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-3 fw-bold fs-5 rounded-pill shadow-sm">
                        Checkout Sekarang
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>
@push('scripts')
<style>
    .mobile-table-grid {
        display: none;
    }

    .mobile-choice-group {
        display: none;
    }

    .cart-item-row {
        flex-wrap: wrap;
        gap: .75rem 1rem;
    }

    .cart-item-note {
        width: 100%;
        margin-left: 84px;
        margin-right: 4.5rem;
    }

    .cart-item-addons {
        width: 100%;
        margin-left: 84px;
        margin-right: 4.5rem;
    }

    .cart-addon-options {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: .5rem;
        margin-bottom: .75rem;
    }

    .cart-addon-option {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .75rem;
        padding: .55rem .7rem;
        border: 1px solid rgba(255, 255, 255, .18);
        border-radius: 10px;
        background: rgba(0, 0, 0, .25);
        color: #ffffff;
        font-size: .9rem;
    }

    .cart-addon-panel {
        max-height: 0;
        overflow: hidden;
        opacity: 0;
        margin-top: 0 !important;
        padding: 0 .85rem;
        border: 1px solid transparent;
        border-radius: 12px;
        background: rgba(0, 0, 0, .18);
        transform: translateY(-6px);
        visibility: hidden;
        transition: max-height .28s ease, opacity .2s ease, transform .28s ease, padding .28s ease, margin .28s ease, border-color .28s ease, visibility .28s ease;
    }

    .cart-addon-panel.show {
        max-height: 420px;
        margin-top: .75rem !important;
        padding: .85rem;
        border-color: rgba(255, 255, 255, .14);
        opacity: 1;
        transform: translateY(0);
        visibility: visible;
    }

    .cart-addon-toggle {
        box-shadow: 0 8px 18px rgba(212, 163, 115, .24);
        color: #ffffff !important;
    }

    @media (max-width: 767.98px) {
        .mobile-hidden-select {
            display: none;
        }

        .mobile-choice-group {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: .5rem;
        }

        .mobile-choice-option {
            border: 1px solid rgba(255, 255, 255, .18);
            border-radius: 12px;
            background: rgba(0, 0, 0, .28);
            color: #ffffff;
            font-weight: 700;
            min-height: 42px;
            padding: .55rem .5rem;
            line-height: 1.2;
        }

        .mobile-choice-option.active {
            background: var(--primary-color);
            border-color: var(--primary-color);
            color: #ffffff;
        }

        .mobile-choice-option:disabled,
        .mobile-choice-option.is-hidden {
            display: none;
        }

        .mobile-table-grid {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: .5rem;
        }

        .mobile-table-option {
            border: 1px solid rgba(255, 255, 255, .18);
            border-radius: 10px;
            background: rgba(0, 0, 0, .28);
            color: #ffffff;
            font-weight: 700;
            min-height: 42px;
            padding: .45rem .25rem;
        }

        .mobile-table-option.active {
            background: var(--primary-color);
            border-color: var(--primary-color);
            color: #ffffff;
        }

        .glass-card.card {
            max-width: 100%;
            overflow: hidden;
        }

        .form-select,
        .form-control {
            max-width: 100%;
            font-size: .95rem;
        }

        body {
            overflow-x: hidden;
        }

        main.container {
            width: 100%;
            max-width: 100%;
            padding-left: .9rem !important;
            padding-right: .9rem !important;
        }

        .row {
            --bs-gutter-x: 0;
        }

        .cart-item-row {
            display: block !important;
            padding: 1rem !important;
        }

        .cart-item-note {
            width: 100%;
            margin: .85rem 0 0;
        }

        .cart-item-addons {
            width: 100%;
            margin: .85rem 0 0;
        }

        .cart-addon-options {
            grid-template-columns: 1fr;
        }

        .cart-item-main {
            align-items: flex-start !important;
            min-width: 0;
        }

        .cart-item-image {
            width: 58px !important;
            height: 58px !important;
            flex: 0 0 58px;
        }

        .cart-item-info {
            min-width: 0;
            flex: 1;
        }

        .cart-item-name {
            font-size: 1rem;
            line-height: 1.25;
            overflow-wrap: anywhere;
        }

        .cart-item-actions {
            justify-content: space-between;
            gap: .75rem;
            margin-top: 1rem;
            width: 100%;
        }

        .cart-item-subtotal {
            font-size: 1rem !important;
            margin-right: 0 !important;
            overflow-wrap: anywhere;
        }

        .card-body.p-4 {
            padding: 1rem !important;
        }

        h2.fw-bold {
            font-size: 1.5rem;
        }
    }
</style>
<script>
    function toggleTableSelection() {
        const orderTypeSelect = document.getElementById('order_type');
        const tableContainer = document.getElementById('table_selection_container');
        const tableSelect = document.getElementById('table_number');
        const tableError = document.querySelector('.table-number-error');

        if (!orderTypeSelect || !tableContainer || !tableSelect) {
            return;
        }

        const orderType = orderTypeSelect.value;
        
        if (orderType === 'Take Away') {
            tableContainer.style.display = 'none';
            tableSelect.required = false;
            tableSelect.value = '';
            if (tableError) {
                tableError.style.display = 'none';
            }
        } else {
            tableContainer.style.display = 'block';
            tableSelect.required = true;
        }
    }

    function syncMobileChoice(targetId) {
        const targetSelect = document.getElementById(targetId);
        if (!targetSelect) {
            return;
        }

        document.querySelectorAll(`[data-select-target="${targetId}"]`).forEach(function(button) {
            button.classList.toggle('active', button.dataset.selectValue === targetSelect.value);
        });
    }

    function togglePaymentOptions() {
        const orderTypeSelect = document.getElementById('order_type');
        const paymentSelect = document.getElementById('payment_method');

        if (!orderTypeSelect || !paymentSelect) {
            return;
        }

        const orderType = orderTypeSelect.value;
        const cashOption = paymentSelect.querySelector('option[value="Cash"]');
        const cashButton = document.querySelector('[data-select-target="payment_method"][data-select-value="Cash"]');
        const qrisButton = document.querySelector('[data-select-target="payment_method"][data-select-value="QRIS"]');

        if (orderType === 'Take Away') {
            paymentSelect.value = 'QRIS';

            if (cashOption) {
                cashOption.disabled = true;
                cashOption.hidden = true;
            }

            if (cashButton) {
                cashButton.disabled = true;
                cashButton.classList.add('is-hidden');
                cashButton.classList.remove('active');
            }

            if (qrisButton) {
                qrisButton.classList.add('active');
            }
        } else {
            if (cashOption) {
                cashOption.disabled = false;
                cashOption.hidden = false;
            }

            if (cashButton) {
                cashButton.disabled = false;
                cashButton.classList.remove('is-hidden');
            }
        }

        syncMobileChoice('payment_method');
        toggleProofPayment();
    }

    function toggleProofPayment() {
        const paymentSelect = document.getElementById('payment_method');
        const proofContainer = document.getElementById('proof_payment_container');
        const proofInput = document.getElementById('proof_of_transfer');
        const proofError = document.querySelector('.proof-payment-error');

        if (!paymentSelect || !proofContainer || !proofInput) {
            return;
        }

        const paymentMethod = paymentSelect.value;

        if (paymentMethod === 'QRIS') {
            proofContainer.style.display = 'block';
            proofInput.required = true;
        } else {
            proofContainer.style.display = 'none';
            proofInput.required = false;
            proofInput.value = '';
            if (proofError) {
                proofError.style.display = 'none';
            }
        }
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        toggleTableSelection();
        togglePaymentOptions();
        toggleProofPayment();

        const formatRupiah = function(value) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
        };

        const updateCartTotals = function(data) {
            document.querySelectorAll('.cart-grand-total').forEach(function(totalElement) {
                totalElement.textContent = formatRupiah(data.total);
            });
        };

        document.querySelectorAll('.cart-addon-toggle').forEach(function(button) {
            button.addEventListener('click', function() {
                const panel = this.closest('.cart-item-addons').querySelector('.cart-addon-panel');
                panel.classList.toggle('show');
            });
        });

        document.querySelectorAll('.cart-addon-close').forEach(function(button) {
            button.addEventListener('click', function() {
                this.closest('.cart-addon-panel').classList.remove('show');
            });
        });

        document.querySelectorAll('.ajax-cart-form').forEach(function(form) {
            form.addEventListener('submit', async function(event) {
                event.preventDefault();

                const button = this.querySelector('button');
                if (button) {
                    button.disabled = true;
                }

                try {
                    const response = await fetch(this.action, {
                        method: this.method || 'POST',
                        body: new FormData(this),
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        credentials: 'same-origin',
                    });

                    const data = await response.json();
                    if (!response.ok || !data.success) {
                        alert(data.message || 'Keranjang gagal diperbarui.');
                        return;
                    }

                    const itemRow = document.querySelector(`[data-cart-item="${data.item_id}"]`);
                    if (itemRow && data.item_removed) {
                        itemRow.remove();
                    } else if (itemRow) {
                        itemRow.querySelector('.cart-item-quantity').textContent = data.quantity;
                        itemRow.querySelector('.cart-item-subtotal').textContent = formatRupiah(data.item_subtotal);
                    }

                    updateCartTotals(data);
                    if (window.updateCartCountBadges) {
                        window.updateCartCountBadges(data.cart_count);
                    }

                    if (data.cart_count === 0) {
                        window.location.reload();
                    }
                } catch (error) {
                    alert('Koneksi lambat. Coba klik lagi sebentar.');
                } finally {
                    if (button) {
                        button.disabled = false;
                    }
                }
            });
        });

        document.querySelectorAll('.mobile-choice-option').forEach(function(button) {
            button.addEventListener('click', function() {
                if (this.disabled) {
                    return;
                }

                const targetSelect = document.getElementById(this.dataset.selectTarget);
                targetSelect.value = this.dataset.selectValue;
                targetSelect.dispatchEvent(new Event('change'));

                this.closest('.mobile-choice-group').querySelectorAll('.mobile-choice-option').forEach(function(option) {
                    option.classList.remove('active');
                });
                this.classList.add('active');
            });
        });

        document.querySelectorAll('.mobile-table-option').forEach(function(button) {
            button.addEventListener('click', function() {
                const tableSelect = document.getElementById('table_number');
                const tableError = document.querySelector('.table-number-error');
                tableSelect.value = this.dataset.tableNumber;

                document.querySelectorAll('.mobile-table-option').forEach(function(option) {
                    option.classList.remove('active');
                });
                this.classList.add('active');

                if (tableError) {
                    tableError.style.display = 'none';
                }
            });
        });

        const checkoutForm = document.getElementById('checkoutForm');
        if (checkoutForm) {
            checkoutForm.addEventListener('submit', function(event) {
                const orderType = document.getElementById('order_type').value;
                const tableSelect = document.getElementById('table_number');
                const tableError = document.querySelector('.table-number-error');
                const paymentMethod = document.getElementById('payment_method').value;
                const proofInput = document.getElementById('proof_of_transfer');
                const proofError = document.querySelector('.proof-payment-error');

                if (orderType === 'Dine In' && !tableSelect.value) {
                    event.preventDefault();
                    if (tableError) {
                        tableError.style.display = 'block';
                    }

                    document.getElementById('table_selection_container').scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                    return;
                }

                if (paymentMethod === 'QRIS' && proofInput && !proofInput.files.length) {
                    event.preventDefault();
                    if (proofError) {
                        proofError.style.display = 'block';
                    }

                    document.getElementById('proof_payment_container').scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                    proofInput.focus();
                }
            });
        }

        const orderTypeSelect = document.getElementById('order_type');
        if (orderTypeSelect) {
            orderTypeSelect.addEventListener('change', function() {
                toggleTableSelection();
                togglePaymentOptions();
                syncMobileChoice('order_type');
            });
        }

        const paymentMethodSelect = document.getElementById('payment_method');
        if (paymentMethodSelect) {
            paymentMethodSelect.addEventListener('change', toggleProofPayment);
        }

        const proofInput = document.getElementById('proof_of_transfer');
        if (proofInput) {
            proofInput.addEventListener('change', function() {
                const proofError = document.querySelector('.proof-payment-error');
                if (proofError && this.files.length) {
                    proofError.style.display = 'none';
                }
            });
        }
    });
</script>
@endpush
@endsection
