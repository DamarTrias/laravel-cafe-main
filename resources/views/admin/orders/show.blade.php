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
                            <td class="text-end pe-4 fw-bold">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</td>
                        </tr>
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
    </div>

    <div class="col-md-4">
        <div class="glass-card card border-0 mb-4">
            <div class="card-header">Info Pelanggan</div>
            <div class="card-body">
                <p class="mb-1 text-muted small">Nama</p>
                <p class="fw-bold fs-5 text-white mb-3">{{ $order->user->name }}</p>

                <p class="mb-1 text-muted small pt-2">Nomor Meja / Tipe</p>
                <p class="fw-bold text-primary fs-5 mb-3">
                    <i class="bi bi-geo-alt me-1"></i> {{ $order->table_number ?? 'Bawa Pulang' }}
                </p>
                
                <p class="mb-1 text-muted small">Email</p>
                <p class="text-white">{{ $order->user->email }}</p>
                
                <p class="mb-1 text-muted small pt-2">Catatan</p>
                <p class="text-white">{{ $order->note ?? '-' }}</p>
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
        <div class="glass-card card border-0">
            <div class="card-header">Status</div>
            <div class="card-body">
                <p class="mb-1 text-muted small">Pembayaran</p>
                <p class="fw-bold text-white">{{ $order->payment_method }}</p>

                <p class="mb-1 text-muted small mt-3">Status Saat Ini</p>
                <span class="badge w-100 py-2 fs-6 bg-{{ $order->status }} mb-4">
                    {{ ucfirst($order->status) }}
                </span>

                <form action="{{ route('admin.orders.update', $order) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label text-white small">Ubah Status</label>
                        <select name="status" class="form-select bg-dark text-white border-secondary">
                            <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="diproses" {{ $order->status == 'diproses' ? 'selected' : '' }}>Diproses</option>
                            <option value="selesai" {{ $order->status == 'selesai' ? 'selected' : '' }}>Selesai</option>
                            <option value="dibatalkan" {{ $order->status == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                        </select>
                    </div>
                    <button class="btn btn-primary w-100">Simpan Status</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
