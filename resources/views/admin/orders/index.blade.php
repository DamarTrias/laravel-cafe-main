@extends('layouts.dashboard')

@section('title', 'Manajemen Pesanan')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h2 class="fw-bold text-white mb-0">Pesanan Masuk</h2>
        <p class="text-muted">Kelola pesanan dari pelanggan.</p>
    </div>
</div>

@if($lowStockIngredients->count() > 0)
<div class="row mb-4">
    <div class="col-12">
        <div class="glass-card border-0 shadow-sm p-4" style="background: rgba(220, 53, 69, 0.1); border: 1px solid rgba(220, 53, 69, 0.2) !important;">
            <div class="d-flex align-items-center mb-3 text-danger">
                <i class="bi bi-exclamation-triangle-fill fs-4 me-2"></i>
                <h5 class="mb-0 fw-bold text-white">Peringatan: Stok Operasional Menipis!</h5>
            </div>
            <p class="text-white text-opacity-75 small mb-3">Beberapa bahan di bawah ini sudah menipis di area operasional. Silakan ambil dari gudang owner sekarang.</p>
            <div class="d-flex flex-wrap gap-2">
                @foreach($lowStockIngredients as $ing)
                    <div class="badge bg-dark bg-opacity-50 border border-danger border-opacity-50 p-2 d-flex align-items-center rounded-pill">
                        <span class="text-white me-2">{{ $ing->name }}</span>
                        <span class="badge bg-danger text-white">{{ number_format($ing->operational_stock, 2) }} {{ $ing->unit }}</span>
                        <a href="{{ route('admin.ingredients.index') }}" class="ms-2 text-primary" title="Transfer Sekarang">
                             <i class="bi bi-arrow-right-circle-fill"></i>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endif

<div class="glass-card card border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0 text-white align-middle">
                <thead>
                    <tr>
                        <th class="ps-4">ID</th>
                        <th>Meja</th>
                        <th>Pelanggan</th>
                        <th>Total Harga</th>
                        <th>Metode Bayar</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                    <tr>
                        <td class="ps-4 fw-bold text-primary">#{{ $order->id }}</td>
                        <td class="fw-bold">{{ $order->table_number ?? '-' }}</td>
                        <td>{{ $order->user->name }}</td>
                        <td class="fw-medium">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                        <td>{{ $order->payment_method }}</td>
                        <td>
                            <span class="badge bg-{{ $order->status }}">
                                {{ ucfirst($order->status) }}
                            </span>
                            @php $isTransfer = in_array($order->payment_method, ['Transfer', 'QRIS']); @endphp
                            @if($isTransfer && $order->proof_of_transfer)
                                <i class="bi bi-image text-primary ms-1" title="Bukti Pembayaran Terunggah"></i>
                            @endif
                        </td>
                        <td class="text-muted small">{{ $order->created_at->format('d M Y H:i') }}</td>
                        <td class="text-end pe-4">
                            <a href="{{ route('admin.orders.print', $order) }}" target="_blank" class="btn btn-sm btn-outline-warning me-1">Cetak</a>
                            <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-info me-1">Detail</a>
                            
                            <form action="{{ route('admin.orders.update', $order) }}" method="POST" class="d-inline-block order-status-form">
                                @csrf
                                @method('PUT')
                                <div class="input-group input-group-sm order-status-group d-none d-md-flex">
                                    <select name="status" class="form-select bg-dark text-white border-secondary order-status-select" onchange="this.form.submit()">
                                        <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="diproses" {{ $order->status == 'diproses' ? 'selected' : '' }}>Diproses</option>
                                        <option value="selesai" {{ $order->status == 'selesai' ? 'selected' : '' }}>Selesai</option>
                                        <option value="dibatalkan" {{ $order->status == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                                    </select>
                                </div>
                                <div class="dropdown d-md-none order-status-mobile-dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle order-status-mobile-button" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        {{ ucfirst($order->status) }}
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-dark order-status-mobile-menu">
                                        @foreach(['pending' => 'Pending', 'diproses' => 'Diproses', 'selesai' => 'Selesai', 'dibatalkan' => 'Dibatalkan'] as $statusValue => $statusLabel)
                                            <li>
                                                <button type="button"
                                                    class="dropdown-item order-status-mobile-option {{ $order->status === $statusValue ? 'active' : '' }}"
                                                    data-status="{{ $statusValue }}">
                                                    {{ $statusLabel }}
                                                </button>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">Belum ada pesanan masuk.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<style>
    .order-status-group,
    .order-status-select {
        width: auto;
        min-width: 120px;
    }

    @media (max-width: 767.98px) {
        .order-status-form,
        .order-status-mobile-dropdown {
            display: inline-block !important;
            width: 88px !important;
            min-width: 88px !important;
            max-width: 88px !important;
        }

        .order-status-mobile-button {
            width: 88px !important;
            min-width: 88px !important;
            max-width: 88px !important;
            overflow: hidden;
            padding: .2rem .35rem;
            font-size: .72rem;
            line-height: 1.2;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .order-status-mobile-menu {
            min-width: 88px !important;
            width: 88px !important;
            padding: .2rem;
        }

        .order-status-mobile-option {
            padding: .3rem .4rem;
            font-size: .75rem;
            line-height: 1.2;
            border-radius: .2rem;
        }
    }
</style>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.order-status-mobile-option').forEach(function(option) {
            option.addEventListener('click', function() {
                const form = this.closest('.order-status-form');
                const select = form.querySelector('.order-status-select');

                select.value = this.dataset.status;
                form.submit();
            });
        });
    });
</script>
@endpush
@endsection
