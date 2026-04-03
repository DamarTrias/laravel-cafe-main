@extends('layouts.dashboard')

@section('title', 'Pesanan Saya')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h2 class="fw-bold text-white mb-0">Riwayat Pesanan</h2>
        <p class="text-muted">Pantau status pesanan Anda di sini.</p>
    </div>
</div>

<div class="glass-card card border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0 text-white align-middle">
                <thead>
                    <tr>
                        <th class="ps-4">ID Pesanan</th>
                        <th>Total Pembayaran</th>
                        <th>Metode</th>
                        <th>Status</th>
                        <th>Meja</th>
                        <th>Tanggal</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                    <tr>
                        <td class="ps-4 fw-bold text-primary">#{{ $order->id }}</td>
                        <td class="fw-medium">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                        <td>{{ $order->payment_method }}</td>
                        <td>
                            <span class="badge bg-{{ $order->status }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>
                        <td class="fw-bold">{{ $order->table_number ?? '-' }}</td>
                        <td class="text-muted small">{{ $order->created_at->format('d M Y H:i') }}</td>
                        <td class="text-end pe-4">
                            <a href="{{ route('pelanggan.orders.show', $order) }}" class="btn btn-sm btn-outline-info me-1">
                                <i class="bi bi-eye"></i> Detail
                            </a>
                            @php $isTransfer = in_array($order->payment_method, ['Transfer', 'QRIS']); @endphp
                            @if($order->status === 'pending' && $isTransfer && !$order->proof_of_transfer)
                                <a href="{{ route('pelanggan.orders.show', $order) }}" class="btn btn-sm btn-primary">
                                    <i class="bi bi-upload"></i> Upload Bukti
                                </a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <i class="bi bi-clock-history fs-1 text-muted mb-3 d-block"></i>
                            <h5 class="text-white">Anda belum pernah memesan sesuatu.</h5>
                            <a href="{{ route('pelanggan.store') }}" class="btn btn-primary mt-3 px-4 rounded-pill">Pesan Sekarang</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
