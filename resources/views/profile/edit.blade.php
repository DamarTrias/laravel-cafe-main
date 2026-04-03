@extends('layouts.dashboard')

@section('title', 'Profil Saya')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h2 class="fw-bold text-white mb-0">Pengaturan Profil</h2>
        <p class="text-muted mb-0">Kelola informasi akun dan kata sandi Anda.</p>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-6">
        <div class="glass-card card border-0 h-100 shadow-sm">
            <div class="card-header border-bottom border-light border-opacity-10 py-3">
                <h5 class="mb-0 text-white fw-bold">Informasi Profil</h5>
            </div>
            <div class="card-body p-4">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="glass-card card border-0 h-100 shadow-sm">
            <div class="card-header border-bottom border-light border-opacity-10 py-3">
                <h5 class="mb-0 text-white fw-bold">Perbarui Kata Sandi</h5>
            </div>
            <div class="card-body p-4">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="glass-card card border-0 shadow-sm border-danger border-opacity-10">
            <div class="card-header border-bottom border-danger border-opacity-20 py-3 bg-danger bg-opacity-10">
                <h5 class="mb-0 text-danger fw-bold">Zona Bahaya: Hapus Akun</h5>
            </div>
            <div class="card-body p-4">
                <div class="max-w-xl text-white">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
