<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MKM - @yield('title', 'Your Apps')</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            padding: 20px;
            background-color: #f8f9fa;
        }
        .nav {
            margin-bottom: 20px;
        }
        .nav-item {
            padding: 5px 0;
        }
        .card {
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
    </style>
</head>
<body>
    <div id="app">
    <div class="d-flex">
    <!-- Sidebar -->
    <div class="bg-white border-end" style="width: 220px; min-height: 100vh;">
        <div class="p-3">
            <img src="{{ asset('logo.png') }}" alt="Logo" style="max-width: 100%;">
            <h5 class="mt-3">Safety Assessment</h5>
            <hr>
            <h6 class="text-muted">CORE</h6>
            <div class="nav flex-column">
                <a href="#" class="nav-link">Home</a>
            </div>
            <h6 class="text-muted mt-3">MASTER</h6>
            <div class="nav flex-column">
                <a href="#" class="nav-link">Master Shop</a>
            </div>
            <div class="nav flex-column">
                <a href="#" class="nav-link">Master 5S</a>
            </div>
            <h6 class="text-muted mt-3">CONFIGURATION</h6>
            <div class="nav flex-column">
                <a href="#" class="nav-link">Master Configuration</a>
            </div>
            <div class="mt-4 text-muted small">
                Logged in as: <strong>IT</strong>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="flex-grow-1 p-4">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4>@yield('title', 'Your Apps')</h4>
            <img src="https://i.ibb.co/7Y0Y4rB/avatar.png" alt="User Avatar" style="width: 40px; border-radius: 50%;">
        </div>

        @yield('content')

        <!-- Footer -->
        <footer class="mt-5 text-muted">
            <div>Copyright PT Mitsubishi Krama Yudha Motors and Manufacturing© {{ date('Y') }}</div>
        </footer>
    </div>
</div>

    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>