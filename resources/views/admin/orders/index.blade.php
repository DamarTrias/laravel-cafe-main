@extends('layouts.dashboard')

@section('title', 'Manajemen Pesanan')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h2 class="fw-bold text-white mb-0">Pesanan Masuk</h2>
        <p class="text-muted">Kelola pesanan dari pelanggan.</p>
    </div>
</div>

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
                            
                            <form action="{{ route('admin.orders.update', $order) }}" method="POST" class="d-inline-block">
                                @csrf
                                @method('PUT')
                                <div class="input-group input-group-sm" style="width: auto;">
                                    <select name="status" class="form-select bg-dark text-white border-secondary" style="width: auto; min-width: 120px;" onchange="this.form.submit()">
                                        <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="diproses" {{ $order->status == 'diproses' ? 'selected' : '' }}>Diproses</option>
                                        <option value="selesai" {{ $order->status == 'selesai' ? 'selected' : '' }}>Selesai</option>
                                        <option value="dibatalkan" {{ $order->status == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                                    </select>
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
@endsection
