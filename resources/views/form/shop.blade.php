<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Risk Assessment Form - {{ $shopName }}</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    table, th, td { border: 1px solid black !important; }
    th, td { text-align: center; vertical-align: middle; }
    .card-header { background-color: #0d6efd; color: white; font-weight: bold; }
    .form-label { font-weight: 500; }
  </style>
</head>
<body>
<div class="container mt-4 mb-5">
  <h4 class="text-center fw-bold mb-4">Risk Assessment for Preventing Workplace Accidents</h4>

  @if(!empty($shopImage) && file_exists(public_path('storage/shop_images/' . $shopImage)))
    <div class="text-center mb-4">
      <img src="{{ asset('storage/shop_images/' . $shopImage) }}" class="img-fluid rounded shadow" style="max-height: 400px;" alt="{{ $shopName }}">
    </div>
  @endif

  @if (session('success'))
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
  @endif

  <form id="risk-form" method="POST" action="{{ route('risk-assessment.store') }}" enctype="multipart/form-data">
    @csrf

    <div class="row g-3 mb-4">
      <div class="col-md-4 col-12">
        <label class="form-label fw-bold">Shop</label>
        <input type="text" class="form-control" value="{{ $shopName }}" readonly disabled>
        <input type="hidden" name="shop_id" value="{{ $shopId }}">
      </div>

      <div class="col-md-4 col-12">
        <label class="form-label fw-bold">Date</label>
        @php $today = date('Y-m-d'); @endphp
        <input type="date" class="form-control" value="{{ $today }}" readonly disabled>
        <input type="hidden" name="date" value="{{ $today }}">
      </div>

      <div class="col-md-4 col-12">
        <label class="form-label fw-bold">Accessor</label>
        <input type="text" id="main-accessor" class="form-control" value="{{ old('accessor') }}" required>
      </div>
    </div>

    <div id="risk-assessment-container"></div>

    <div class="d-flex justify-content-end mb-2">
      <button type="submit" id="submit-btn" class="btn btn-success"> Submit </button>
    </div>

    <div class="d-flex justify-content-start mb-3">
      <button type="button" class="btn btn-outline-primary" id="add-entry-btn">
        + Add Entry
      </button>
    </div>
  </form>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('risk-assessment-container');
    const addEntryBtn = document.getElementById('add-entry-btn');
    const accessorInput = document.getElementById('main-accessor');
    let index = 1;

    function createEntry(i) {
  const div = document.createElement('div');
  div.className = '';
  div.innerHTML = `
    <div class="card border-primary mb-4">
      <div class="card-header bg-primary text-white fw-bold">Form Entry ${i}</div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label">Scope</label>
            <select name="scope_number[]" class="form-select" required>
              <option value="">-- Select Scope --</option>
              <option value="1">1 - Man</option>
              <option value="2">2 - Machine</option>
              <option value="3">3 - Method</option>
              <option value="4">4 - Material</option>
              <option value="5">5 - Environment</option>
            </select>
          </div>
          <div class="col-md-8">
            <label class="form-label">Finding Problem</label>
            <input name="finding_problem[]" type="text" class="form-control" required>
          </div>
          <div class="col-md-4">
            <label class="form-label">Potential Hazard</label>
            <input name="potential_hazards[]" type="text" class="form-control" required>
          </div>
          <div class="col-md-4">
            <label class="form-label">Severity</label>
            <select name="severity[]" class="severity form-select" required>
              <option value="">-- Select --</option>
              <option value="1">1 - Insignificant</option>
              <option value="2">2 - Minor</option>
              <option value="3">3 - Moderate</option>
              <option value="4">4 - Major</option>
              <option value="5">5 - Catastrophic</option>
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label">Possibility</label>
            <select name="possibility[]" class="possibility form-select" required>
              <option value="">-- Select --</option>
              <option value="1">1 - Very Rare</option>
              <option value="2">2 - Unlikely</option>
              <option value="3">3 - Occasional</option>
              <option value="4">4 - Frequent</option>
              <option value="5">5 - Always</option>
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label">Score</label>
            <input name="score[]" type="number" class="score form-control" readonly>
          </div>
          <div class="col-md-6">
            <label class="form-label">Risk Level</label>
            <input name="risk_level[]" type="text" class="risk-level form-control" readonly>
          </div>
          <div class="col-md-6">
            <label class="form-label">Risk Reduction Measures Proposal</label>
            <input name="risk_reduction_proposal[]" type="text" class="form-control" required>
          </div>
          <div class="col-12">
            <label class="form-label">Attach File (optional)</label>
            <input name="file[]" type="file" class="form-control">
          </div>
        </div>
      </div>
    </div>
  `;

  // Tambahkan input hidden accessor
  const accessorValue = document.getElementById('main-accessor').value;
  const accessorInput = document.createElement('input');
  accessorInput.type = 'hidden';
  accessorInput.name = 'accessor[]';
  accessorInput.value = accessorValue;
  div.querySelector('.card-body').appendChild(accessorInput);

  container.appendChild(div);

  // Score calculation
  const sev = div.querySelector('.severity');
  const prob = div.querySelector('.possibility');
  const score = div.querySelector('.score');
  const riskLevel = div.querySelector('.risk-level');

  function updateRisk() {
    const s = parseInt(sev.value) || 0;
    const p = parseInt(prob.value) || 0;
    const result = s * p;
    score.value = result;

    if (result > 16) riskLevel.value = "Extreme";
    else if (result >= 10) riskLevel.value = "High";
    else if (result >= 5) riskLevel.value = "Medium";
    else if (result > 0) riskLevel.value = "Low";
    else riskLevel.value = "";
  }

  sev.addEventListener('input', updateRisk);
  prob.addEventListener('input', updateRisk);
}

// Update semua accessor[] jika main accessor berubah
accessorInput.addEventListener('input', function () {
  const value = accessorInput.value;
  document.querySelectorAll('input[name="accessor[]"]').forEach(input => {
    input.value = value;
  });
});

    createEntry(index++);
    addEntryBtn.addEventListener('click', function () {
      createEntry(index++);
    });

    // Prevent multiple submission
    const form = document.getElementById('risk-form');
    const submitBtn = document.getElementById('submit-btn');
    form.addEventListener('submit', function () {
      submitBtn.disabled = true;
      submitBtn.innerText = 'Submitting…';
    });
  });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
