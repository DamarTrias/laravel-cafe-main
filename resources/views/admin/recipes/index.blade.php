@extends('layouts.dashboard')

@section('title', 'Panduan Resep Menu')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col-md-8">
        <h2 class="fw-bold text-white mb-0 text-primary">Panduan Resep Menu</h2>
        <p class="text-muted small mb-0">Rincian patokan bahan baku yang digunakan untuk setiap porsi menu.</p>
    </div>
</div>

<div class="row">
    @forelse($categories as $category)
        @if($category->products->isNotEmpty())
            <div class="col-12 mb-4">
                <div class="card glass-card border-0 shadow-lg">
                    <div class="card-header bg-dark bg-opacity-25 py-3">
                        <h5 class="fw-bold mb-0 text-white">
                            <i class="bi bi-tags text-primary me-2"></i> Kategori: {{ $category->name }}
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 text-white align-middle">
                                <thead style="border-bottom: 2px solid rgba(255,255,255,0.1);">
                                    <tr>
                                        <th class="ps-4 py-3" style="width: 35%;">Nama Menu</th>
                                        <th class="py-3">Rincian Bahan Baku per Porsi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($category->products as $product)
                                    <tr class="border-light border-opacity-10">
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center">
                                                <div class="rounded me-3 overflow-hidden border border-light border-opacity-25" style="width: 48px; height: 48px;">
                                                    @if($product->image)
                                                        <img src="{{ Storage::url($product->image) }}" alt="image" class="w-100 h-100 object-fit-cover">
                                                    @else
                                                        <div class="w-100 h-100 d-flex align-items-center justify-content-center bg-secondary bg-opacity-25">
                                                            <i class="bi bi-image text-muted"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div>
                                                    <div class="fw-bold fs-6">{{ $product->name }}</div>
                                                    @if($product->ingredients->isEmpty())
                                                        <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 mt-1 small">
                                                            Belum Ada Resep
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if($product->ingredients->isNotEmpty())
                                                <ul class="list-unstyled mb-0">
                                                    @foreach($product->ingredients as $ing)
                                                        <li class="mb-1 d-flex align-items-center">
                                                            <i class="bi bi-check2-circle text-success me-2"></i>
                                                            <span class="badge bg-primary bg-opacity-25 text-primary border border-primary border-opacity-25 px-2 py-1 me-2" style="min-width: 70px; text-align: center;">
                                                                {{ number_format($ing->pivot->amount_needed, 0, ',', '.') }} {{ $ing->unit }}
                                                            </span>
                                                            <span class="text-white">{{ $ing->name }}</span>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @else
                                                <span class="text-muted small">Resep belum ditambahkan oleh Owner.</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @empty
        <div class="col-12 text-center py-5">
            <h5 class="text-muted">Tidak ada kategori menu.</h5>
        </div>
    @endforelse
</div>
@endsection
