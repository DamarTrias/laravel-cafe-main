@extends('layouts.dashboard')

@section('title', 'Detail Pesanan #' . $order->id)

@section('content')
<div class="row align-items-center mb-4">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <div>
            <a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted mb-2 d-inline-block">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
            <h2 class="fw-bold text-white mb-0">Detail Pesanan #{{ $order->id }}</h2>
        </div>
        <a href="{{ route('admin.orders.print', $order) }}" target="_blank" class="btn btn-warning px-4 shadow-sm border-0 fw-bold">
            <i class="bi bi-printer me-2"></i> Cetak Struk
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="glass-card card border-0 mb-4">
            <div class="card-header">Daftar Item</div>
            <div class="card-body p-0">
                <table class="table mb-0 text-white align-middle">
                    <thead>
                        <tr>
                            <th class="ps-4">Produk</th>
                            <th>Harga</th>
                            <th>Qty</th>
                            <th class="text-end pe-4">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                        <tr>
                            <td class="ps-4 fw-medium">{{ $item->product->name ?? 'Produk Dihapus' }}</td>
                            <td>Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td class="text-end pe-4 fw-bold">Rp {{ number_format(($item->price + $item->addons->sum('price')) * $item->quantity, 0, ',', '.') }}</td>
                        </tr>
                        @foreach($item->addons as $addon)
                        <tr>
                            <td colspan="4" class="ps-4 pt-0 text-white small border-0">
                                <span class="text-primary fw-bold">+ {{ $addon->name }}</span> Rp {{ number_format($addon->price, 0, ',', '.') }}
                            </td>
                        </tr>
                        @endforeach
                        @if($item->note)
                        <tr>
                            <td colspan="4" class="ps-4 pt-0 text-white small border-0">
                                <span class="text-primary fw-bold">Catatan:</span> {{ $item->note }}
                            </td>
                        </tr>
                        @endif
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3" class="text-end border-0 pt-4">Total:</th>
                            <th class="text-end pe-4 border-0 pt-4 text-primary fs-5">Rp {{ number_format($order->total_price, 0, ',', '.') }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        @php $isTransfer = in_array($order->payment_method, ['Transfer', 'QRIS']); @endphp
        @if($isTransfer)
        <div class="glass-card card border-0 mb-4">
            <div class="card-header">Bukti Pembayaran (QRIS/Transfer)</div>
            <div class="card-body">
                @if($order->proof_of_transfer)
                    <div class="p-2 border border-secondary rounded mb-3">
                        <img src="{{ Storage::url($order->proof_of_transfer) }}" class="img-fluid rounded cursor-pointer" onclick="window.open(this.src)" title="Klik untuk perbesar">
                    </div>
                    <p class="text-muted small mb-0">Klik gambar untuk melihat dalam ukuran penuh.</p>
                @else
                    <div class="p-4 text-center border border-dashed border-secondary rounded">
                        <i class="bi bi-image text-muted fs-1 mb-2 d-block"></i>
                        <p class="text-muted mb-0">Belum ada bukti pembayaran yang diunggah.</p>
                    </div>
                @endif
            </div>
        </div>
        @endif
        <div class="glass-card card border-0 mb-4">
            <div class="card-header">Status</div>
            <div class="card-body">
                <p class="mb-1 order-detail-label small">Pembayaran</p>
                <p class="fw-bold text-white">{{ $order->payment_method }}</p>

                <p class="mb-1 order-detail-label small mt-3">Status Saat Ini</p>
                <span class="badge w-100 py-2 fs-6 bg-{{ $order->status }} mb-4">
                    {{ ucfirst($order->status) }}
                </span>

                <form action="{{ route('admin.orders.update', $order) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label text-white small">Ubah Status</label>
                        <select name="status" id="orderDetailStatusSelect" class="d-none">
                            <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="diproses" {{ $order->status == 'diproses' ? 'selected' : '' }}>Diproses</option>
                            <option value="selesai" {{ $order->status == 'selesai' ? 'selected' : '' }}>Selesai</option>
                            <option value="dibatalkan" {{ $order->status == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                        </select>
                        <div class="dropdown order-detail-status-dropdown">
                            <button id="orderDetailStatusButton" class="btn btn-outline-secondary dropdown-toggle order-detail-status-button" type="button" data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false">
                                {{ ucfirst($order->status) }}
                            </button>
                            <ul class="dropdown-menu dropdown-menu-dark order-detail-status-menu">
                                @foreach(['pending' => 'Pending', 'diproses' => 'Diproses', 'selesai' => 'Selesai', 'dibatalkan' => 'Dibatalkan'] as $statusValue => $statusLabel)
                                    <li>
                                        <button type="button"
                                            class="dropdown-item order-detail-status-option {{ $order->status === $statusValue ? 'active' : '' }}"
                                            data-status="{{ $statusValue }}">
                                            {{ $statusLabel }}
                                        </button>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <button class="btn btn-primary w-100">Simpan Status</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="glass-card card border-0 mb-4">
            <div class="card-header">Info Pelanggan</div>
            <div class="card-body">
                <p class="mb-1 order-detail-label small">Nama</p>
                <p class="fw-bold fs-5 text-primary mb-3">{{ $order->user->name }}</p>

                <p class="mb-1 order-detail-label small pt-2">Nomor Meja / Tipe</p>
                <p class="fw-bold text-primary fs-5 mb-3">
                    <i class="bi bi-geo-alt me-1"></i> {{ $order->table_number ?? 'Bawa Pulang' }}
                </p>
                
                <p class="mb-1 order-detail-label small">Email</p>
                <p class="text-primary">{{ $order->user->email }}</p>
                
            </div>
        </div>
    </div>
</div>

@push('scripts')
<style>
    .order-detail-status-dropdown,
    .order-detail-status-button,
    .order-detail-status-menu {
        width: 100% !important;
        max-width: none !important;
    }

    .order-detail-status-button {
        display: flex;
        align-items: center;
        justify-content: space-between;
        color: #ffffff;
        text-align: left;
    }

    .order-detail-status-menu {
        min-width: 100% !important;
        padding: .25rem;
    }

    .order-detail-status-option {
        padding: .45rem .65rem;
        border-radius: .25rem;
    }

    .order-detail-label {
        color: #ffffff !important;
        font-weight: 700;
        letter-spacing: .01em;
    }

    @media (max-width: 767.98px) {
        .order-detail-status-dropdown,
        .order-detail-status-button,
        .order-detail-status-menu {
            width: 120px !important;
            min-width: 120px !important;
            max-width: 120px !important;
        }

        .order-detail-status-button {
            overflow: hidden;
            padding: .3rem .45rem;
            font-size: .78rem;
            line-height: 1.2;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .order-detail-status-menu {
            padding: .2rem;
        }

        .order-detail-status-option {
            padding: .35rem .45rem;
            font-size: .78rem;
            line-height: 1.2;
            border-radius: .2rem;
        }
    }
</style>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const statusSelect = document.getElementById('orderDetailStatusSelect');
        const statusButton = document.getElementById('orderDetailStatusButton');

        document.querySelectorAll('.order-detail-status-option').forEach(function(option) {
            option.addEventListener('click', function() {
                statusSelect.value = this.dataset.status;
                statusButton.textContent = this.textContent.trim();

                document.querySelectorAll('.order-detail-status-option').forEach(function(item) {
                    item.classList.remove('active');
                });
                this.classList.add('active');
            });
        });
    });
</script>
@endpush
@endsection
