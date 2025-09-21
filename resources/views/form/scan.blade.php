<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    @php
        // Normalisasi nama lokasi untuk ditampilkan
        $locationRaw = urldecode($name ?? '');
        $locationDisplay = ucwords(trim(str_replace(['-', '_'], ' ', $locationRaw)));
        // Untuk URL (aman)
        $locationForUrl = rawurlencode($locationRaw);
    @endphp
    <title>Menu - PT MKM — {{ $locationDisplay }}</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('assets/img/Safety Assessment2.png') }}">

    <!-- Styling -->
    <style>
        html, body { height: 100%; margin: 0; padding: 0; }
        body {
            background-image: url("{{ asset('assets/img/About-Company-BG-2.jpg') }}");
            background-size: cover; background-position: center;
            background-repeat: no-repeat; background-attachment: fixed;
            display: flex; flex-direction: column; justify-content: space-between;
        }
        .content-wrapper {
            background: rgba(255, 255, 255, 0.55);
            padding: 30px; border-radius: 15px;
            box-shadow: 0 0 10px rgba(0,0,0,.2); width: 100%;
        }
        .bottom-container { padding-bottom: 3rem; }
        .top-logo-container { padding-top: 2rem; }
        .logo-img {
            max-width: 250px; height: auto;
            background-color: rgba(255,255,255,.7);
            border-radius: 10px; padding: 10px;
        }
        .company-name { font-weight: 500; }
        .loc-badge {
            display: inline-block;
            background: #A6CAD8; /* Petrol 00% (soft) */
            color: #003545;
            padding: .35rem .6rem;
            border-radius: .5rem;
            font-weight: 600;
            letter-spacing: .2px;
        }
    </style>
</head>
<body>

    <!-- Logo container at the top -->
    <div class="container text-center top-logo-container">
        <img src="{{ asset('assets/img/Logo Option 3 (1).png') }}" alt="Company Logo" class="logo-img">
    </div>

    <!-- Menu container at the bottom -->
    <div class="container text-center bottom-container">
        <div class="content-wrapper">
            
            <!-- Header + Location -->
            <h1 class="h2 mb-2">Digital Assessment Portal</h1>
            <p class="lead company-name mb-2">PT. Mitsubishi Krama Yudha Motors & Manufacturing</p>
            <p class="mb-4">
                <span class="loc-badge">Location: <strong>{{ $locationDisplay }}</strong> </span>
            </p>
            <p>This portal is used to conduct digital assessments. Please select one of the menus below to get started.</p>
            <hr class="my-4">
            
            <!-- Success Notification -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Menu Buttons -->
            <div class="d-grid gap-3 col-md-6 col-sm-10 mx-auto">
                <a href="{{ url('form/'.$locationForUrl) }}" class="btn btn-primary btn-lg">Safety Assessment</a>
                <a href="{{ url('form/audit/5s/'.$locationForUrl) }}" class="btn btn-success btn-lg">5S</a>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle (includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Script to dismiss alert after 3 seconds -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function () {
                let alertNode = document.querySelector('.alert');
                if (alertNode) {
                    let alertInstance = bootstrap.Alert.getOrCreateInstance(alertNode);
                    alertInstance.close();
                }
            }, 3000);
        });
    </script>
</body>
</html>
