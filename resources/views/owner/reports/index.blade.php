@extends('layouts.dashboard')

@section('title', 'Laporan Penjualan')

@section('content')
<div class="row align-items-center mb-4 d-print-none">
    <div class="col-md-6">
        <h2 class="fw-bold text-white mb-0">Laporan Penjualan</h2>
        <p class="text-muted mb-0">Rangakuman transaksi bisnis Anda.</p>
    </div>
    <div class="col-md-6 mt-3 mt-md-0 d-flex justify-content-md-end gap-2">
        <button onclick="window.print()" class="btn btn-outline-warning fw-bold">
            <i class="bi bi-printer me-1"></i> Cetak Laporan
        </button>
        <a href="{{ route('owner.reports.export', ['start_date' => $startDate, 'endDate' => $endDate]) }}" class="btn btn-primary fw-bold">
            <i class="bi bi-file-earmark-excel me-1"></i> Unduh CSV
        </a>
    </div>
</div>

<!-- Filter Section -->
<div class="glass-card card border-0 mb-4 d-print-none">
    <div class="card-body p-4">
        <form action="{{ route('owner.reports.index') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label text-muted small">Mulai Tanggal</label>
                <input type="date" name="start_date" class="form-control bg-dark text-white border-secondary" value="{{ $startDate }}">
            </div>
            <div class="col-md-1 text-center text-muted mb-2 d-none d-md-block">-</div>
            <div class="col-md-4">
                <label class="form-label text-muted small">Sampai Tanggal</label>
                <input type="date" name="end_date" class="form-control bg-dark text-white border-secondary" value="{{ $endDate }}">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">
                    <i class="bi bi-filter me-1"></i> Terapkan Filter
                </button>
            </div>
        </form>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="glass-card card border-0 h-100">
            <div class="card-body text-center py-4">
                <p class="text-muted small mb-1">TOTAL PENDAPATAN</p>
                <h2 class="fw-bold text-primary mb-0">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="glass-card card border-0 h-100">
            <div class="card-body text-center py-4">
                <p class="text-muted small mb-1">JUMLAH TRANSAKSI</p>
                <h2 class="fw-bold text-primary mb-0">{{ $totalOrders }} Pesanan</h2>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-lg-5">
        <div class="glass-card card border-0 h-100">
            <div class="card-header border-bottom border-light border-opacity-10 py-3">
                <h5 class="mb-0 text-white fw-bold">Penjualan Menu Terlaris</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0 text-white align-middle">
                        <thead>
                            <tr class="small text-muted">
                                <th class="ps-4">Menu</th>
                                <th class="text-center">Qty</th>
                                <th class="text-end pe-4">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($itemSales as $id => $item)
                            <tr>
                                <td class="ps-4 fw-medium">{{ $item['name'] }}</td>
                                <td class="text-center">{{ $item['qty'] }}</td>
                                <td class="text-end pe-4 fw-bold text-primary">Rp {{ number_format($item['total'], 0, ',', '.') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center py-4 text-muted">Belum ada data.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-7">
        <div class="glass-card card border-0 h-100">
            <div class="card-header border-bottom border-light border-opacity-10 py-3">
                <h5 class="mb-0 text-white fw-bold">Rincian Transaksi</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0 text-white align-middle">
                        <thead>
                            <tr class="small text-muted">
                                <th class="ps-4">ID</th>
                                <th>Meja</th>
                                <th>Pelanggan</th>
                                <th>Metode</th>
                                <th class="text-end pe-4">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $order)
                            <tr>
                                <td class="ps-4 fw-bold text-primary">#{{ $order->id }}</td>
                                <td>{{ $order->table_number ?? '-' }}</td>
                                <td>{{ $order->user->name }}</td>
                                <td>{{ $order->payment_method }}</td>
                                <td class="text-end pe-4 fw-bold text-success">+ Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted text-center pt-5">Belum ada transaksi di periode ini.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Print Styles -->
<style>
    @media print {
        body {
            background-color: #fff !important;
            color: #000 !important;
        }
        .container {
            max-width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        .glass-card {
            background: #fff !important;
            border: 1px solid #ddd !important;
            color: #000 !important;
            box-shadow: none !important;
            backdrop-filter: none !important;
        }
        .text-white, .text-muted, .text-primary, .text-success {
            color: #000 !important;
        }
        .table {
            color: #000 !important;
        }
        .table th, .table td {
            border-bottom: 1px solid #ddd !important;
        }
        .card-header {
            background-color: #f8f9fa !important;
            color: #000 !important;
            border-bottom: 1px solid #ddd !important;
        }
        h2, h3, h5 {
            color: #000 !important;
        }
    }
</style>
@endsection
