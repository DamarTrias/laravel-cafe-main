@extends('layouts.dashboard')

@section('title', 'Owner Dashboard')

@section('content')
    <div class="row align-items-center mb-4">
        <div class="col-md-6">
            <h2 class="fw-bold text-white mb-0">Dashboard Owner</h2>
            <p class="text-primary mb-0">Selamat datang, {{ auth()->user()->name }}</p>
        </div>
        <div class="col-md-6 mt-3 mt-md-0">
            <form action="{{ route('owner.dashboard') }}" method="GET" class="row g-2 justify-content-md-end">
                <div class="col-auto">
                    <input type="date" name="start_date"
                        class="form-control form-control-sm bg-dark text-white border-secondary" value="{{ $startDate }}">
                </div>
                <div class="col-auto d-flex align-items-center text-muted">-</div>
                <div class="col-auto">
                    <input type="date" name="end_date"
                        class="form-control form-control-sm bg-dark text-white border-secondary" value="{{ $endDate }}">
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-sm btn-primary px-3">Filter</button>
                </div>
            </form>
        </div>
    </div>

    @if($lowStockIngredients->count() > 0)
        <div class="row mb-4">
            <div class="col-12">
                <div class="alert alert-warning border-0 glass-card d-flex align-items-center p-3 shadow-sm" role="alert">
                    <div class="bg-warning bg-opacity-25 rounded-circle p-2 me-3 d-flex align-items-center justify-content-center"
                        style="width: 40px; height: 40px;">
                        <i class="bi bi-exclamation-triangle-fill text-warning fs-5"></i>
                    </div>
                    <div class="flex-grow-1">
                        <strong class="text-warning">Peringatan Stok Bahan Rendah!</strong>
                        <p class="mb-0 text-white small">Ada {{ $lowStockIngredients->count() }} bahan yang stoknya hampir habis (Gudang < 5 atau Operasional < 2). Harap segera cek stok bahan.</p>
                    </div>
                    <a href="{{ route('owner.ingredients.index') }}" class="btn btn-sm btn-warning fw-bold px-3 ms-3">Lihat Stok Bahan</a>
                </div>
            </div>
        </div>
    @endif

    <div class="row g-4 mb-4">
        <div class="col-md-6 col-lg-3">
            <div class="glass-card card h-100 border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle bg-primary bg-opacity-25 p-3 text-primary d-flex align-items-center justify-content-center me-3"
                            style="width: 50px; height: 50px;">
                            <i class="bi bi-box-seam fs-4"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1 mb-0">Total Produk</h6>
                            <h3 class="fw-bold text-white mb-0">{{ $productCount }}</h3>
                        </div>
                    </div>
                    <a href="{{ route('owner.products.index') }}"
                        class="text-decoration-none small text-primary fw-medium">Lihat Semua <i
                            class="bi bi-arrow-right ms-1"></i></a>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="glass-card card h-100 border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle bg-info bg-opacity-25 p-3 text-info d-flex align-items-center justify-content-center me-3"
                            style="width: 50px; height: 50px;">
                            <i class="bi bi-tags fs-4"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1 mb-0">Total Kategori</h6>
                            <h3 class="fw-bold text-white mb-0">{{ $categoryCount }}</h3>
                        </div>
                    </div>
                    <a href="{{ route('owner.categories.index') }}"
                        class="text-decoration-none small text-info fw-medium">Lihat Semua <i
                            class="bi bi-arrow-right ms-1"></i></a>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="glass-card card h-100 border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle bg-success bg-opacity-25 p-3 text-success d-flex align-items-center justify-content-center me-3"
                            style="width: 50px; height: 50px;">
                            <i class="bi bi-cash-stack fs-4"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1 mb-0">Total Penjualan</h6>
                            <h4 class="fw-bold text-white mb-0 text-truncate" style="max-width: 150px;">
                                Rp {{ number_format($totalPenjualan, 0, ',', '.') }}
                            </h4>
                        </div>
                    </div>
                    <div class="small text-success fw-medium">
                        <i class="bi bi-graph-up me-1"></i> Dari {{ $jumlahPesanan }} Pesanan Selesai
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <div class="glass-card card border-0">
                <div class="card-header pt-4 pb-3">Grafik Pendapatan</div>
                <div class="card-body p-4">
                    <canvas id="revenueChart" style="max-height: 300px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="glass-card card border-0">
                <div class="card-header pt-4 pb-3">Rincian Transaksi ({{ Carbon\Carbon::parse($startDate)->format('d/m') }}
                    - {{ Carbon\Carbon::parse($endDate)->format('d/m') }})</div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0 text-white align-middle">
                            <thead>
                                <tr>
                                    <th class="ps-4">No</th>
                                    <th>ID Pesanan</th>
                                    <th>Meja</th>
                                    <th>Pelanggan</th>
                                    <th>Daftar Menu</th>
                                    <th>Metode Bayar</th>
                                    <th>Waktu Selesai</th>
                                    <th class="text-end pe-4">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ordersDetail as $order)
                                    <tr>
                                        <td class="ps-4">{{ $loop->iteration }}</td>
                                        <td class="fw-bold text-primary">#{{ $order->id }}</td>
                                        <td class="fw-bold">{{ $order->table_number ?? '-' }}</td>
                                        <td>{{ $order->user->name }}</td>
                                        <td>
                                            <ul class="list-unstyled mb-0 small">
                                                @foreach($order->items as $item)
                                                    <li>- {{ $item->quantity }}x {{ $item->product->name ?? 'Produk Dihapus' }}</li>
                                                @endforeach
                                            </ul>
                                        </td>
                                        <td>{{ $order->payment_method }}</td>
                                        <td class="text-muted small">
                                            {{ $order->updated_at->format('d/m H:i') }}
                                        </td>
                                        <td class="text-end pe-4 fw-medium text-success">
                                            + Rp {{ number_format($order->total_price, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5 text-muted">Belum ada transaksi selesai dalam
                                            periode ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('revenueChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($labels) !!},
                datasets: [{
                    label: 'Pendapatan (Rp)',
                    data: {!! json_encode($revenue) !!},
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13, 110, 253, 0.1)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 3,
                    pointBackgroundColor: '#0d6efd',
                    pointRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        },
                        ticks: {
                            color: '#6c757d',
                            callback: function (value) {
                                return 'Rp ' + value.toLocaleString();
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#6c757d'
                        }
                    }
                }
            }
        });
    </script>
@endpush