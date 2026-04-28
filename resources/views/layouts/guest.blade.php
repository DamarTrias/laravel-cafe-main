<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        <!-- Bootstrap Icons -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            body {
                font-family: 'Outfit', sans-serif;
                background-color: #0f0f11;
                background-image:
                    radial-gradient(circle at 15% 50%, rgba(212, 163, 115, 0.08), transparent 25%),
                    radial-gradient(circle at 85% 30%, rgba(250, 237, 205, 0.05), transparent 25%);
                min-height: 100vh;
            }

            .guest-card {
                background: rgba(255, 255, 255, 0.05);
                backdrop-filter: blur(16px);
                -webkit-backdrop-filter: blur(16px);
                border: 1px solid rgba(255, 255, 255, 0.1);
                border-radius: 20px;
                box-shadow: 0 25px 50px rgba(0, 0, 0, 0.4);
            }

            .brand-logo {
                font-size: 2rem;
                font-weight: 700;
                color: #d4a373;
                letter-spacing: 2px;
            }

            /* Override browser autofill white background */
            input:-webkit-autofill,
            input:-webkit-autofill:hover,
            input:-webkit-autofill:focus,
            input:-webkit-autofill:active {
                -webkit-box-shadow: 0 0 0px 1000px rgba(15, 15, 17, 0.95) inset !important;
                -webkit-text-fill-color: white !important;
                transition: background-color 5000s ease-in-out 0s;
            }

            /* Password Reveal Icon (Edge) */
            input::-ms-reveal {
                filter: invert(100%);
            }

            /* Password Reveal Icon (Webkit/Chrome) - if applicable, though usually not default */
            input::-webkit-contacts-auto-fill-button,
            input::-webkit-credentials-auto-fill-button {
                filter: invert(100%);
            }
        </style>
    </head>
    <body>
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
            <div class="mb-6 text-center">
                <a href="/" class="brand-logo text-decoration-none">
                    <i class="bi bi-cup-hot-fill me-2"></i>69 CAFE
                </a>
            </div>

            <div class="w-full sm:max-w-md px-6 py-8 guest-card overflow-hidden">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
