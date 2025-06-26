<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Risk Assessment Form</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    table, th, td { border: 1px solid black !important; }
    th, td { text-align: center; vertical-align: middle; }
    .no-border { border: none !important; }
    @media (max-width: 576px) {
      .table-responsive { overflow-x: auto; }
    }
  </style>
</head>
<body>
<div class="container mt-4">
  <h4 class="text-center fw-bold">Risk Assessment for Preventing Workplace Accidents</h4>
    <div class="text-center mb-3">
        <img src="{{ asset('assets/img/Screenshot (755).png') }}" alt="Risk Assessment Layout" class="img-fluid" style="max-width: 100%; height: auto;">
    </div>
  @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  <form method="POST" action="{{ route('risk-assessment.store') }}" enctype="multipart/form-data">
    @csrf

    <!-- Shop & Accessor -->
    <div class="row g-3 mb-4">
      <div class="col-md-4">
        <label class="fw-bold">Shop:</label>
        <select name="shop_id" class="form-select" required>
          <option value="">-- Select Shop --</option>
          @foreach ($shops as $shop)
            <option value="{{ $shop->id }}">{{ $shop->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-4">
        <label class="fw-bold">Date:</label>
        @php $today = date('Y-m-d'); @endphp
        <input type="date" class="form-control" value="{{ $today }}" readonly disabled>
        <input type="hidden" name="date" value="{{ $today }}">
      </div>
      <div class="col-md-4">
        <label class="fw-bold">Accessor:</label>
        <input type="text" id="main-accessor" class="form-control" placeholder="Enter accessor name" required>
      </div>
    </div>

    <div id="risk-assessment-container"></div>

    <div class="d-flex justify-content-end mb-2">
      <button type="submit" class="btn btn-success">Submit</button>
    </div>

    <div class="d-flex justify-content-start mb-3">
      <button type="button" class="btn btn-outline-primary" id="add-entry-btn">+ Add Entry</button>
    </div>
  </form>
</div>

<!-- JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  setTimeout(() => {
    const alert = document.querySelector('.alert');
    if (alert) bootstrap.Alert.getOrCreateInstance(alert).close();
  }, 3000);
</script>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('risk-assessment-container');
    const addEntryBtn = document.getElementById('add-entry-btn');
    let index = 1;

    function createEntry(i) {
      const div = document.createElement('div');
      div.className = 'card border-primary mb-4';
      div.innerHTML = `
        <div class="card-header bg-primary text-white fw-bold">Form Entry #${i}</div>
        <div class="card-body">
          <div class="row gy-2 gx-3">
            <div class="col-md-4">
              <label>Scope</label>
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
              <label>Finding Problem</label>
              <input name="finding_problem[]" type="text" class="form-control" required>
            </div>
            <div class="col-md-4">
              <label>Potential Hazard</label>
              <input name="potential_hazards[]" type="text" class="form-control" required>
            </div>
            <div class="col-md-4">
              <label>Severity</label>
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
              <label>Possibility</label>
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
              <label>Score</label>
              <input name="score[]" type="number" class="score form-control" readonly>
            </div>
            <div class="col-md-6">
              <label>Risk Level</label>
              <input name="risk_level[]" type="text" class="risk-level form-control" readonly>
            </div>
            <div class="col-md-6">
              <label>Risk Reduction Measures Proposal</label>
              <input name="risk_reduction_proposal[]" type="text" class="form-control" required>
            </div>
            <div class="col-12">
              <label>Attach File (optional)</label>
              <input name="file[]" type="file" class="form-control">
            </div>
            <!-- Inject accessor -->
            <input type="hidden" name="accessor[]" value="${document.getElementById('main-accessor').value}">
          </div>
        </div>
      `;
      container.appendChild(div);

      const sev = div.querySelector('.severity');
      const prob = div.querySelector('.possibility');
      const score = div.querySelector('.score');
      const riskLevel = div.querySelector('.risk-level');

      function updateRisk() {
        const s = parseInt(sev.value) || 0;
        const p = parseInt(prob.value) || 0;
        const result = s * p;
        score.value = result;
        riskLevel.value =
          result > 16 ? "Extreme" :
          result >= 10 ? "High" :
          result >= 5  ? "Medium" :
          result > 0   ? "Low" : "";
      }

      sev.addEventListener('input', updateRisk);
      prob.addEventListener('input', updateRisk);
    }

    addEntryBtn.addEventListener('click', () => createEntry(index++));
    createEntry(index++);
  });
</script>

<script>
  document.querySelector('form').addEventListener('submit', function () {
    const submitBtn = this.querySelector('button[type="submit"]');
    if (submitBtn) {
      submitBtn.disabled = true;
      submitBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Submitting...`;
    }
  });
</script>
</body>
</html>
