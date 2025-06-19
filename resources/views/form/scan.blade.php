<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta http-equiv="X-UA-Compatible" content="ie=edge" />
  <title>Menu</title>
  
  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Background Styling -->
  <style>
    body {
      background-image: url("{{ asset('assets/img/About-Company-BG-2.jpg') }}");
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
      background-attachment: fixed;
      height: 100vh;
      margin: 0;
      padding: 0;
    }

    .content-wrapper {
      background: rgba(255, 255, 255, 0.5);
      padding: 30px;
      border-radius: 15px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
    }
  </style>

  <!-- Favicon -->
  <link rel="icon" href="{{ asset('assets/img/Safety Assessment2.png') }}">
</head>
<body>

  <div class="container text-center mt-5">
    <div class="content-wrapper">
      <h1 class="mb-4 ">Menu</h1>
    <!-- notif success -->
      @if(session('success'))
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
@endif


      <div class="d-grid gap-3 col-6 mx-auto">
        <a href="{{url('form/'.$name)}}" class="btn btn-primary btn-lg">Safety Assessment</a>
        <a href="{{url('/form/audit/5s/'.$name)}}" class="btn btn-success btn-lg">5S</a>
      </div>
    </div>
  </div>

  <!-- Bootstrap 5 JS Bundle (includes Popper) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Script Hilangkan Alert -->
<script>
    setTimeout(function () {
        let alert = document.querySelector('.alert');
        if (alert) {
            let fade = bootstrap.Alert.getOrCreateInstance(alert);
            fade.close();
        }
    }, 3000);
</script>

</html>
