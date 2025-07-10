<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Safety & 5S</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="icon" href="{{ asset('assets/img/Safety Assessment2.png') }}">
    <style>
        body, html {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            height: 100%;
        }

        .container-fluid {
            display: flex;
            flex-direction: row;
            min-height: 100vh;
            padding: 0;
        }

        .picture-section {
            flex: 6;
            background-image: url('{{ asset('assets/img/shutterstock_2102677933-scaled.jpg') }}');
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #fff;
        }

        .picture-section h1 {
            font-size: 2.5rem;
            font-weight: bold;
            background: rgba(0, 0, 0, 0.5);
            padding: 20px;
            border-radius: 10px;
        }

        .login-card {
            flex: 1.5;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #fff;
            padding: 30px;
            box-shadow: -2px 0 10px rgba(0, 0, 0, 0.1);
        }

        .login-card-content {
            max-width: 350px;
            width: 100%;
            text-align: center;
        }

        .login-card img {
            width: 150px;
            margin-bottom: 25px;
        }

        .form-control {
            margin-bottom: 15px;
        }

        .btn-dark {
            width: 100%;
        }

        .footer {
            margin-top: 20px;
            font-size: 0.9rem;
        }

        .btn-primary {
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            gap: 0.5rem;
        }

        /* RESPONSIVE SECTION */
        @media (max-width: 992px) {
            .container-fluid {
                flex-direction: column;
            }

            .picture-section {
                flex: none;
                height: 200px;
            }

            .login-card {
                flex: none;
                width: 100%;
                padding: 20px;
                box-shadow: none;
            }

            .picture-section h1 {
                font-size: 1.5rem;
                padding: 10px;
            }

            .login-card-content {
                max-width: 100%;
            }
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <!-- Left Section -->
        <div class="picture-section">
            
        </div>

        <!-- Right Section -->
        <div class="login-card">
            <div class="login-card-content">
                <img src="{{ asset('assets/img/Safety Assessment2.png') }}" alt="SafetyAssessment" style="height:60px; width:auto;">
                <h2>Safety & 5S</h2>
                <small>Manage Your Login Page</small>

                <!-- Alerts -->
                @if (session('statusLogin'))
                <div class="alert alert-warning" role="alert">
                    <strong>{{ session('statusLogin') }}</strong>
                </div>
                @elseif(session('statusLogout'))
                <div class="alert alert-success" role="alert">
                    <strong>{{ session('statusLogout') }}</strong>
                </div>
                @endif

                <!-- Login Form -->
                <form action="{{ url('auth/login') }}" method="POST">
                    @csrf
                    <div class="mb-3 mt-3">
                        <input type="text" class="form-control" placeholder="Username" name="email" required />
                    </div>
                    <div class="mb-3">
                        <input type="password" class="form-control" placeholder="Password" name="password" required />
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="rememberMe">
                            <label class="form-check-label" for="rememberMe">Remember me</label>
                        </div>
                        <a href="#" class="text-muted">Forgot password?</a>
                    </div>
                    <button type="submit" class="btn btn-dark btn-sm btn-block mb-3">Log In</button>
                </form>

                <div class="text-center my-3">
                    <span class="text-muted">OR</span>
                    <hr />
                </div>

                <!-- Request Access -->
                <button type="button" class="btn btn-sm btn-info w-100 mb-2 text-white" data-bs-toggle="modal" data-bs-target="#requestAccessModal">Request Access</button>

                <div class="footer text-center mt-4">
                    <p>&copy; 2025 PT Mitsubishi Krama Yudha Motors and Manufacturing</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="requestAccessModal" tabindex="-1" aria-labelledby="requestAccessModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Request Access</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="requestAccessForm" action="{{ url('request/access') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="inputName" class="form-label">Name</label>
                            <input type="text" class="form-control" id="inputName" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="inputEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="inputEmail" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="inputDepartment" class="form-label">Department</label>
                            <input type="text" class="form-control" id="inputDepartment" name="department" required>
                        </div>
                        <div class="mb-3">
                            <label for="inputPlant" class="form-label">Plant</label>
                            <input type="text" class="form-control" id="inputPlant" name="plant" required>
                        </div>
                        <div class="mb-3">
                            <label for="inputPurpose" class="form-label">Purpose</label>
                            <textarea class="form-control" id="inputPurpose" name="purpose" rows="3" required></textarea>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Submit Request</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
