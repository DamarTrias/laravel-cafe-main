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
                        <li class="list-group-item bg-transparent text-white border-bottom border-secondary d-flex justify-content-between align-items-center p-4">
                            <div class="d-flex align-items-center">
                                @if(isset($item['image']) && $item['image'])
                                    <img src="{{ Storage::url($item['image']) }}" class="rounded me-3 object-fit-cover" width="60" height="60">
                                @else
                                    <div class="rounded me-3 d-flex align-items-center justify-content-center bg-dark" style="width: 60px; height: 60px;">
                                        <i class="bi bi-image text-muted"></i>
                                    </div>
                                @endif
                                <div>
                                    <h5 class="fw-bold mb-1">{{ $item['name'] }}</h5>
                                    <span class="text-primary fw-medium">Rp {{ number_format($item['price'], 0, ',', '.') }}</span> x {{ $item['quantity'] }}
                                </div>
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="fw-bold fs-5 me-4">Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</span>
                                <form action="{{ route('pelanggan.cart.remove', $id) }}" method="POST">
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
                        <p class="text-muted">Yuk, lihat <a href="{{ route('pelanggan.store') }}" class="text-primary text-decoration-none">katalog menu</a>.</p>
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
                <div class="d-flex justify-content-between mb-3 text-muted">
                    <span>Subtotal</span>
                    <span>Rp {{ number_format($total, 0, ',', '.') }}</span>
                </div>
                <div class="d-flex justify-content-between mb-4 fs-4 fw-bold text-white">
                    <span>Total</span>
                    <span class="text-primary">Rp {{ number_format($total, 0, ',', '.') }}</span>
                </div>
                
                <hr class="border-secondary mb-4">
                
                <form action="{{ route('pelanggan.checkout') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label text-muted">Tipe Pesanan</label>
                        <select class="form-select bg-dark text-white border-secondary" name="order_type" id="order_type" onchange="toggleTableSelection()" required>
                            <option value="Dine In">Makan di Tempat (Dine In)</option>
                            <option value="Take Away">Bawa Pulang (Take Away)</option>
                        </select>
                    </div>

                    <div class="mb-3" id="table_selection_container">
                        <label class="form-label text-muted d-flex justify-content-between align-items-center">
                            <span>Nomor Meja</span>
                            @if(session('table_number'))
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 small">
                                    <i class="bi bi-qr-code-scan me-1"></i> QR Terdeteksi
                                </span>
                            @endif
                        </label>
                        <select class="form-select bg-dark text-white border-secondary" name="table_number" id="table_number">
                            <option value="" disabled {{ !session('table_number') ? 'selected' : '' }}>Pilih Nomor Meja</option>
                            @for($i = 1; $i <= 20; $i++)
                                <option value="Meja {{ $i }}" {{ session('table_number') == 'Meja '.$i ? 'selected' : '' }}>Meja {{ $i }}</option>
                            @endfor
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted">Metode Pembayaran</label>
                        <select class="form-select bg-dark text-white border-secondary" name="payment_method" required>
                            <option value="Cash">Cash (Di Kasir)</option>
                            <option value="QRIS">QRIS / Transfer Bank</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-muted">Catatan (Opsional)</label>
                        <textarea class="form-control bg-dark text-white border-secondary" name="note" rows="2" placeholder="Cth: Es dikurangin, kurangi gula..."></textarea>
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
<script>
    function toggleTableSelection() {
        const orderType = document.getElementById('order_type').value;
        const tableContainer = document.getElementById('table_selection_container');
        const tableSelect = document.getElementById('table_number');
        
        if (orderType === 'Take Away') {
            tableContainer.style.display = 'none';
            tableSelect.required = false;
            tableSelect.value = '';
        } else {
            tableContainer.style.display = 'block';
            tableSelect.required = true;
        }
    }
    
    // Initial call to set state
    document.addEventListener('DOMContentLoaded', toggleTableSelection);
</script>
@endpush
@endsection
