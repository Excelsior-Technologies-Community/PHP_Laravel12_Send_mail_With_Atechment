<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Laravel Email System')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">

            <a class="navbar-brand" href="{{ route('dashboard') }}">
                Email System
            </a>

            <div>

                <a href="{{ route('dashboard') }}" class="btn btn-outline-light btn-sm">
                    Dashboard
                </a>

                <a href="{{ route('email.form') }}" class="btn btn-outline-light btn-sm">
                    Send Email
                </a>

                <a href="{{ route('email.history') }}" class="btn btn-outline-light btn-sm">
                    History
                </a>

            </div>

        </div>
    </nav>

    <div class="container py-4">

        @yield('content')

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>