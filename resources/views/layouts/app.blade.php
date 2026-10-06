<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Laravel Email System')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">

        <div class="container">

            <a class="navbar-brand fw-bold" href="{{ route('dashboard') }}">
                📧 Laravel Email System
            </a>

            <button class="navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarNav">

                <span class="navbar-toggler-icon"></span>

            </button>

            <div class="collapse navbar-collapse" id="navbarNav">

                <ul class="navbar-nav ms-auto align-items-center">

                    <li class="nav-item me-2">
                        <a href="{{ route('dashboard') }}"
                            class="btn btn-outline-light btn-sm">
                            📊 Dashboard
                        </a>
                    </li>

                    <li class="nav-item me-2">
                        <a href="{{ route('email.form') }}"
                            class="btn btn-outline-light btn-sm">
                            📨 Send Email
                        </a>
                    </li>

                    <li class="nav-item me-2">
                        <a href="{{ route('email.history') }}"
                            class="btn btn-outline-light btn-sm">
                            🕒 Email History
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('templates.index') }}"
                            class="btn btn-warning btn-sm text-dark fw-semibold">
                            📄 Email Templates
                        </a>
                    </li>

                </ul>

            </div>

        </div>

    </nav>

    <div class="container py-4">

        @yield('content')

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>