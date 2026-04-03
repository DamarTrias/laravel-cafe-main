<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Laravel Cafe') }} - @yield('title')</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        :root {
            --primary-color: #d4a373;
            --secondary-color: #faedcd;
            --dark-color: #343a40;
            --bg-color: #0f0f11;
            --card-bg: rgba(255, 255, 255, 0.05);
            --text-main: #f8f9fa;
            --text-muted: #adb5bd;
        }

        .text-primary {
            color: var(--primary-color) !important;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--bg-color);
            background-image:
                radial-gradient(circle at 15% 50%, rgba(212, 163, 115, 0.08), transparent 25%),
                radial-gradient(circle at 85% 30%, rgba(250, 237, 205, 0.05), transparent 25%);
            color: var(--text-main);
            min-height: 100vh;
        }

        /* Glassmorphism Navbar */
        .navbar-glass {
            background: rgba(15, 15, 17, 0.7) !important;
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .navbar-brand {
            font-weight: 700;
            color: var(--primary-color) !important;
            letter-spacing: 1px;
        }

        .nav-link {
            color: var(--text-main) !important;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .nav-link:hover,
        .nav-link.active {
            color: var(--primary-color) !important;
        }

        .glass-card {
            background: var(--card-bg);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 16px;
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.3);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            color: #ffffff !important;
        }

        .glass-card:hover {
            transform: translateY(-5px);
            border-color: rgba(212, 163, 115, 0.3);
            box-shadow: 0 12px 40px 0 rgba(0, 0, 0, 0.5);
        }

        .card-header {
            background: transparent;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            color: var(--primary-color);
            font-weight: 600;
        }

        /* Buttons Update */
        .btn-primary {
            background: linear-gradient(135deg, #d4a373 0%, #cc8b4c 100%);
            border: none;
            color: #fff;
            border-radius: 8px;
            font-weight: 500;
            padding: 8px 20px;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(212, 163, 115, 0.4);
        }

        .btn-outline-primary {
            border-color: var(--primary-color);
            color: var(--primary-color);
        }

        .btn-outline-primary:hover {
            background-color: var(--primary-color);
            color: #fff;
        }

        /* Tables */
        .table {
            color: var(--text-main);
        }

        .table th {
            border-bottom: 2px solid rgba(212, 163, 115, 0.3);
            color: var(--primary-color);
            background: transparent;
        }

        .table td {
            background: transparent;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            vertical-align: middle;
            color: #ffffff !important;
        }

        /* Form Inputs */
        .form-control,
        .form-select {
            background: rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: var(--text-main);
            border-radius: 8px;
        }

        .form-control:focus,
        .form-select:focus {
            background: rgba(0, 0, 0, 0.3);
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(212, 163, 115, 0.25);
            color: var(--text-main);
        }

        /* Badges */
        .badge.bg-pending {
            background-color: #ffc107;
            color: #000;
        }

        .badge.bg-diproses {
            background-color: #0dcaf0;
            color: #000;
        }

        .badge.bg-selesai {
            background-color: #198754;
        }

        .badge.bg-dibatalkan {
            background-color: #dc3545;
        }

        /* Animation */
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Custom Utilities */
        .placeholder-white-50::placeholder {
            color: rgba(255, 255, 255, 0.7) !important;
            opacity: 1;
        }

        /* Profile / Form Inputs - scoped to card.glass-card (profile forms only) */
        .card.glass-card input[type="text"],
        .card.glass-card input[type="email"],
        .card.glass-card input[type="password"] {
            background: rgba(255, 255, 255, 0.1) !important;
            border: 1px solid rgba(255, 255, 255, 0.25) !important;
            color: #ffffff !important;
        }

        .card.glass-card input[type="text"]::placeholder,
        .card.glass-card input[type="email"]::placeholder,
        .card.glass-card input[type="password"]::placeholder {
            color: rgba(255, 255, 255, 0.5) !important;
        }

        .card.glass-card input[type="text"]:focus,
        .card.glass-card input[type="email"]:focus,
        .card.glass-card input[type="password"]:focus {
            border-color: var(--primary-color) !important;
            box-shadow: 0 0 0 0.2rem rgba(212, 163, 115, 0.25) !important;
        }

        /* Form Labels on Dark Background */
        label {
            color: rgba(255, 255, 255, 0.85) !important;
            margin-bottom: 6px;
            display: block;
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-dark navbar-glass sticky-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="bi bi-cup-hot-fill me-2"></i>14 CAFE
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-toggle="collapse"
                data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    @if(auth()->check())
                        @if(auth()->user()->role === 'owner')
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('owner.dashboard') ? 'active' : '' }}"
                                    href="{{ route('owner.dashboard') }}">Dashboard</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('owner.categories.*') ? 'active' : '' }}"
                                    href="{{ route('owner.categories.index') }}">Kategori</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('owner.products.*') ? 'active' : '' }}"
                                    href="{{ route('owner.products.index') }}">Produk</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('owner.reports.*') ? 'active' : '' }}"
                                    href="{{ route('owner.reports.index') }}">Laporan</a>
                            </li>
                        @elseif(auth()->user()->role === 'admin')
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                                    href="{{ route('admin.dashboard') }}">Pesanan Masuk</a>
                            </li>
                        @else
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('pelanggan.store') ? 'active' : '' }}"
                                    href="{{ route('pelanggan.store') }}">Menu</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('pelanggan.orders') ? 'active' : '' }}"
                                    href="{{ route('pelanggan.orders') }}">Pesanan Saya</a>
                            </li>
                        @endif
                    @endif
                </ul>
                <ul class="navbar-nav">
                    @auth
                        @if(auth()->user()->role === 'pelanggan')
                            <li class="nav-item me-3">
                                <a class="nav-link" href="{{ route('pelanggan.cart.index') }}">
                                    <i class="bi bi-cart3"></i>
                                    @if(session('cart') && count(session('cart')) > 0)
                                        <span
                                            class="badge bg-danger rounded-pill">{{ collect(session('cart'))->sum('quantity') }}</span>
                                    @endif
                                </a>
                            </li>
                        @endif
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                                data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle me-1"></i> {{ auth()->user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Profile</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item">Logout</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Login</a></li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <main class="container py-5 fade-in">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show glass-card" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show glass-card" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>

</html>