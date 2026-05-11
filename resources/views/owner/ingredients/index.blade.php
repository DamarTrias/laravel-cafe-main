@extends('layouts.dashboard')

@section('title', 'Manajemen Bahan')

@section('content')
    <div class="row align-items-center mb-4">
        <div class="col-md-6">
            <h2 class="fw-bold text-white mb-0 text-primary">Manajemen Stok & Bahan</h2>
            <p class="text-muted small mb-0">Kelola <strong>Stok Gudang</strong> (Cadangan Besar) dan <strong>Stok
                    Operasional</strong> (Untuk jualan hari ini).</p>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            @if(auth()->user()->role === 'owner')
                <a href="{{ route('owner.ingredients.create') }}" class="btn btn-primary shadow-sm px-4">
                    <i class="bi bi-plus-circle me-1"></i> Tambah Bahan Baru
                </a>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card glass-card border-0 shadow-lg">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 text-white align-middle">
                            <thead class="bg-dark bg-opacity-25">
                                <tr>
                                    <th class="ps-4 py-3 text-uppercase small letter-spacing-1">Nama Bahan</th>
                                    <th class="py-3 text-uppercase small letter-spacing-1">Satuan</th>
                                    <th class="py-3 text-uppercase small letter-spacing-1">Stok Gudang</th>
                                    <th class="py-3 text-uppercase small letter-spacing-1">Stok Operasional</th>
                                    <th class="text-end pe-4 py-3 text-uppercase small letter-spacing-1">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ingredients as $ingredient)
                                    <tr class="border-light border-opacity-10">
                                        <td class="ps-4">
                                            <div class="fw-bold fs-5">{{ $ingredient->name }}</div>
                                        </td>
                                        <td>
                                            <span
                                                class="badge bg-secondary bg-opacity-25 text-white border border-light border-opacity-25 px-3 py-2 fw-normal">
                                                {{ $ingredient->unit }}
                                            </span>
                                        </td>
                                        <td>
                                            <span
                                                class="fs-5">{{ number_format($ingredient->warehouse_stock, 0, ',', '.') }}</span>
                                        </td>
                                        <td>
                                            @if($ingredient->operational_stock < 2)
                                                <div class="d-flex align-items-center text-danger">
                                                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                                    <span
                                                        class="fw-bold fs-5">{{ number_format($ingredient->operational_stock, 0, ',', '.') }}</span>
                                                </div>
                                            @else
                                                <span
                                                    class="text-primary fw-bold fs-5">{{ number_format($ingredient->operational_stock, 0, ',', '.') }}</span>
                                            @endif
                                        </td>
                                        <td class="text-end pe-4">
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-sm btn-primary me-2 px-3 rounded-pill"
                                                    data-bs-toggle="modal" data-bs-target="#transferModal{{ $ingredient->id }}">
                                                    <i class="bi bi-arrow-left-right me-1"></i> Ambil Stok
                                                </button>

                                                @php $role = auth()->user()->role; @endphp
                                                @if($role === 'owner')
                                                    <a href="{{ route('owner.ingredients.edit', $ingredient) }}"
                                                        class="btn btn-sm btn-outline-info rounded-circle p-2 me-2" title="Edit">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </a>
                                                    <form action="{{ route('owner.ingredients.destroy', $ingredient) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="btn btn-sm btn-outline-danger rounded-circle p-2"
                                                            onclick="return confirm('Hapus bahan ini?')" title="Hapus">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            <i class="bi bi-box-seam fs-1 mb-3 d-block opacity-25"></i>
                                            Belum ada data bahan baku.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @foreach($ingredients as $ingredient)
        <!-- Transfer Modal -->
        <div class="modal fade" id="transferModal{{ $ingredient->id }}" tabindex="-1" aria-hidden="true"
            style="backdrop-filter: blur(5px);">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content glass-card border-light shadow-lg"
                    style="background: rgba(20, 20, 25, 0.98) !important;">
                    @php $role = auth()->user()->role; @endphp
                    <form action="{{ route($role . '.ingredients.transfer', $ingredient) }}" method="POST">
                        @csrf
                        <div class="modal-header border-bottom border-light border-opacity-10 py-3">
                            <h5 class="modal-title font-bold text-primary">
                                <i class="bi bi-arrow-left-right me-2"></i>Ambil Stok dari Gudang
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-start p-4">
                            <p class="mb-4 text-muted small lh-sm">Pindahkan stok besar dari gudang ke operasional toko agar
                                menu dapat tersedia untuk dipesan pelanggan hari ini.</p>

                            <div
                                class="p-3 rounded-4 bg-dark bg-opacity-50 border border-light border-opacity-10 mb-4 shadow-inner">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted small">Bahan Baku:</span>
                                    <span class="fw-bold">{{ $ingredient->name }}</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted small">Tersedia di Gudang:</span>
                                    <span
                                        class="fw-bold text-success">{{ number_format($ingredient->warehouse_stock, 0, ',', '.') }}
                                        {{ $ingredient->unit }}</span>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="amount{{ $ingredient->id }}" class="form-label text-white small fw-semibold">Jumlah
                                    yang ingin diambil ({{ $ingredient->unit }})</label>
                                <input type="number" step="0.01"
                                    class="form-control form-control-lg bg-dark text-white border-primary border-opacity-25 shadow-sm p-3"
                                    name="amount" id="amount{{ $ingredient->id }}" required
                                    max="{{ $ingredient->warehouse_stock }}" min="0.01" step="0.01"
                                    placeholder="Masukkan jumlah...">
                                <small class="text-muted mt-2 d-block">Jumlah ini akan langsung menambah **Stok
                                    Operasional**.</small>
                            </div>
                        </div>
                        <div class="modal-footer border-top border-light border-opacity-10 p-3">
                            <button type="button" class="btn btn-link text-muted text-decoration-none px-3"
                                data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary px-4 py-2 fw-bold shadow-sm rounded-pill">
                                Pindahkan Stok
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endsection