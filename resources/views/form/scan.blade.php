<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta http-equiv="X-UA-Compatible" content="ie=edge" />
  <title>Document</title>
  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

  <div class="container text-center mt-5">
    <h1 class="mb-4">Menu</h1>
    <div class="d-grid gap-3 col-6 mx-auto">
      <a href="{{url('form/'.$name)}}" class="btn btn-primary btn-lg">Safety Assessment</a>
      <a href="{{url('/form/audit/5s/'.$name)}}" class="btn btn-success btn-lg">5S</a>
    </div>
  </div>

  <!-- Bootstrap 5 JS Bundle (includes Popper) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
