<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Easy Room') }}</title>

    <!-- Include Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

     <!-- Favicon -->
     <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

    <!-- Include Custom Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="gradient-background d-flex flex-column min-vh-100">
    <div class="flex-grow-1">
        <!-- Navigation -->
        <header>
            @include('layouts.navigation')
        </header>

        <!-- Page Header -->
        @if (View::hasSection('header'))
            <header>
                <div class="container py-3">
                    @yield('header')
                </div>
            </header>
        @endif

        <!-- Page Content -->
        <main class="container my-4">
            <!-- Error Message -->
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {!! session('error') !!}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- Footer -->
    <footer class="bg-black text-white py-3">
        <div class="container text-center">
            <p>&copy; {{ date('Y') }} <strong>Easy Room</strong>. Tous droits réservés.</p>
        </div>
    </footer>

    <!-- Include Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
