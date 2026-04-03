@extends('layouts.dashboard')

@section('title', 'Detail Pesanan #' . $order->id)

@section('content')
<div class="row align-items-center mb-4">
    <div class="col-12">
        <a href="{{ route('pelanggan.orders') }}" class="text-decoration-none text-muted mb-2 d-inline-block">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Riwayat
        </a>
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h2 class="fw-bold text-white mb-0">Pesanan #{{ $order->id }}</h2>
                <div class="text-muted small">Dipesan pada {{ $order->created_at->format('d M Y, H:i') }}</div>
            </div>
            <span id="order-status-badge" class="badge bg-{{ $order->status }} py-2 px-4 fw-bold fs-6 shadow-sm transition-all" style="transition: all 0.5s ease;">
                {{ ucfirst($order->status) }}
            </span>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-8">
        <div class="glass-card card border-0 mb-4">
            <div class="card-header border-bottom border-light border-opacity-10 py-3">
                <h5 class="mb-0 text-white fw-bold"><i class="bi bi-receipt me-2"></i>Rincian Produk</h5>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush rounded bg-transparent">
                    @foreach($order->items as $item)
                        <li class="list-group-item bg-transparent text-white border-bottom border-light border-opacity-10 p-4">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                                <div class="d-flex align-items-center">
                                    <div class="rounded me-3 overflow-hidden border border-light border-opacity-25 shadow-sm" style="width: 70px; height: 70px;">
                                        @if(isset($item->product->image) && $item->product->image)
                                            <img src="{{ Storage::url($item->product->image) }}" class="w-100 h-100 object-fit-cover" alt="{{ $item->product->name }}">
                                        @else
                                            <div class="w-100 h-100 d-flex align-items-center justify-content-center bg-dark bg-opacity-50">
                                                <i class="bi bi-image text-muted"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <h5 class="fw-bold mb-1 text-white">{{ $item->product->name ?? 'Produk Dihapus' }}</h5>
                                        <div class="text-secondary small">
                                            {{ $item->quantity }} x Rp {{ number_format($item->price, 0, ',', '.') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <h5 class="fw-bold text-primary mb-0">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</h5>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="card-footer bg-transparent border-top border-light border-opacity-10 p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted fs-5">Total Pembayaran</span>
                    <h3 class="fw-bold text-primary mb-0 shadow-sm">Rp {{ number_format($order->total_price, 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- Payment Info Card -->
        <div class="glass-card card border-0 mb-4 shadow-sm">
            <div class="card-header border-bottom border-light border-opacity-10 py-3">
                <h5 class="mb-0 text-white fw-bold"><i class="bi bi-credit-card me-2"></i>Pembayaran</h5>
            </div>
            <div class="card-body p-4">
                <div class="mb-4 d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted small mb-1">Metode</p>
                        <span class="badge bg-primary bg-opacity-25 text-primary border border-primary border-opacity-25 px-3 py-2 fw-bold">
                            {{ $order->payment_method }}
                        </span>
                    </div>
                    @if($order->table_number)
                    <div class="text-end">
                        <p class="text-muted small mb-1">Meja / Tipe</p>
                        <span class="text-white fw-bold fs-5"><i class="bi bi-geo-alt me-1 text-primary"></i>{{ $order->table_number }}</span>
                    </div>
                    @endif
                </div>

                @php $isTransfer = in_array($order->payment_method, ['Transfer', 'QRIS']); @endphp
                @if($isTransfer)
                    <div class="mb-0">
                        <p class="text-muted small mb-2">Bukti Pembayaran</p>
                        @if($order->proof_of_transfer)
                            <div class="p-2 border border-light border-opacity-25 rounded overflow-hidden shadow-sm bg-dark bg-opacity-50">
                                <img src="{{ Storage::url($order->proof_of_transfer) }}" class="img-fluid rounded cursor-pointer transition hover-opacity" onclick="window.open(this.src)" title="Klik untuk perbesar">
                            </div>
                            @if($order->status === 'pending')
                                <div class="mt-3 alert alert-info py-2 px-3 border-0 bg-primary bg-opacity-10 text-primary small">
                                    <i class="bi bi-hourglass-split me-2"></i> Menunggu verifikasi admin.
                                </div>
                            @endif
                        @else
                            @if($order->status === 'pending')
                                <div class="alert alert-warning py-2 px-3 border-0 bg-warning bg-opacity-10 text-warning small mb-3">
                                    <i class="bi bi-exclamation-triangle me-2"></i> Harap upload bukti pembayaran.
                                </div>
                                <form action="{{ route('pelanggan.orders.upload_proof', $order) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="mb-3">
                                        <input type="file" name="proof_of_transfer" class="form-control form-control-sm bg-dark text-white border-light border-opacity-25" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-sm w-100 fw-bold">
                                        <i class="bi bi-cloud-upload me-1"></i> Upload Bukti
                                    </button>
                                </form>
                            @else
                                <div class="text-danger small"><i class="bi bi-x-circle me-1"></i> Bukti tidak diunggah.</div>
                            @endif
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <!-- Note Card -->
        <div class="glass-card card border-0 shadow-sm">
            <div class="card-header border-bottom border-light border-opacity-10 py-3">
                <h5 class="mb-0 text-white fw-bold"><i class="bi bi-chat-left-text me-2"></i>Catatan</h5>
            </div>
            <div class="card-body p-4">
                <div class="p-3 bg-dark bg-opacity-50 rounded border border-light border-opacity-10 text-white small lh-base">
                    {!! nl2br(e($order->note ?? 'Tidak ada catatan untuk pesanan ini.')) !!}
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const orderId = "{{ $order->id }}";
    const statusBadge = document.getElementById('order-status-badge');
    let currentStatus = "{{ $order->status }}";

    function checkOrderStatus() {
        // Jangan pooling jika pesanan sudah selesai atau dibatalkan
        if (currentStatus === 'selesai' || currentStatus === 'dibatalkan') return;

        fetch(`/store/orders/${orderId}/status`)
            .then(response => response.json())
            .then(data => {
                if (data.status !== currentStatus) {
                    // Beri efek transisi saat status berubah
                    statusBadge.style.transform = 'scale(1.2)';
                    
                    setTimeout(() => {
                        // Update class warna dan teks
                        statusBadge.className = `badge bg-${data.status_color} py-2 px-4 fw-bold fs-6 shadow-sm transition-all`;
                        statusBadge.innerText = data.status_label;
                        statusBadge.style.transform = 'scale(1)';
                        
                        // Jika status berubah menjadi selesai, reload halaman
                        if (data.status === 'selesai') {
                            location.reload(); 
                        }
                    }, 300);

                    currentStatus = data.status;
                }
            })
            .catch(error => console.error('Error polling status:', error));
    }

    // Cek setiap 7 detik sesuai rencana
    setInterval(checkOrderStatus, 7000);
</script>
@endpush
@endsection