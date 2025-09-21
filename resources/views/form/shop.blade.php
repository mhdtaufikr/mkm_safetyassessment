<!DOCTYPE html>
<html lang="en">
<head>
  <meta name="csrf-token" content="{{ csrf_token() }}">

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

  @if(!empty($shopImage) && file_exists(public_path('storage/' . $shopImage)))
  <div class="text-center mb-4">
    <img src="{{ asset('storage/' . $shopImage) . '?v=' . \Carbon\Carbon::parse($shopUpdatedAt)->timestamp }}" 
         class="img-fluid rounded shadow" style="max-height: 270px;" alt="{{ $shopName }}">
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
<script>
document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('risk-form');
  const container = document.getElementById('risk-assessment-container');

  // === Required field helpers ===
  function fieldLabel(el) {
    const wrap = el.closest('.col-md-4, .col-md-6, .col-md-8, .col-12');
    const lbl = wrap ? wrap.querySelector('label.form-label') : null;
    return (lbl ? lbl.textContent.trim() : (el.name || el.id || 'This field')).replace(/\[\]$/, '');
  }
  function ensureFeedback(el) {
    let fb = el.nextElementSibling && el.nextElementSibling.classList?.contains('invalid-feedback')
      ? el.nextElementSibling : null;
    if (!fb) {
      fb = document.createElement('div');
      fb.className = 'invalid-feedback';
      el.parentNode.insertBefore(fb, el.nextSibling);
    }
    return fb;
  }
  function validate(el) {
    if (!el.hasAttribute('required')) return true;
    let ok = true;
    if (el.tagName === 'SELECT') ok = el.value !== '';
    else ok = String(el.value || '').trim().length > 0;

    if (!ok) {
      const fb = ensureFeedback(el);
      fb.textContent = fieldLabel(el) + ' is required.';
      el.classList.add('is-invalid');
    } else {
      el.classList.remove('is-invalid');
      const fb = el.nextElementSibling;
      if (fb && fb.classList.contains('invalid-feedback')) fb.textContent = '';
    }
    return ok;
  }

  form.addEventListener('focusout', (e) => {
    const el = e.target;
    if (form.contains(el) && el.matches('input[required], select[required], textarea[required]')) {
      validate(el);
    }
  });
  form.addEventListener('input', (e) => {
    const el = e.target;
    if (el.classList.contains('is-invalid')) validate(el);
  });

  form.addEventListener('submit', (e) => {
    const requiredFields = form.querySelectorAll('input[required], select[required], textarea[required]');
    let allValid = true;
    requiredFields.forEach((el) => { if (!validate(el)) allValid = false; });
    if (!allValid) {
      e.preventDefault();
      const firstInvalid = form.querySelector('.is-invalid');
      if (firstInvalid) firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
      const btn = document.getElementById('submit-btn');
      if (btn) { btn.disabled = false; btn.innerText = 'Submit'; }
    }
  });

  // === Add "Remove" button dynamically for all future cards ===
  function addRemoveButton(card) {
    if (!card) return;
    const header = card.querySelector('.card-header');
    if (!header || header.querySelector('.remove-entry')) return;

    const btn = document.createElement('button');
    btn.type = 'button';
    btn.className = 'btn btn-danger btn-sm remove-entry';
    btn.textContent = 'Remove';
    btn.style.float = 'right';
    header.appendChild(btn);
  }

  // Observe new cards and add remove button immediately
  const mo = new MutationObserver((mutations) => {
    mutations.forEach((m) => {
      m.addedNodes.forEach((node) => {
        if (node.nodeType !== 1) return;
        if (node.matches?.('.card.border-primary')) {
          addRemoveButton(node);
        } else {
          const card = node.querySelector?.('.card.border-primary');
          if (card) addRemoveButton(card);
        }
      });
    });
  });
  mo.observe(container, { childList: true });

  // Delegate remove button click
  container.addEventListener('click', (e) => {
    if (e.target && e.target.classList.contains('remove-entry')) {
      e.preventDefault();
      const card = e.target.closest('.card.border-primary');
      if (card) card.remove();
    }
  });
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
  // Helpers
  function getToken() {
    return document.querySelector('meta[name="csrf-token"]')?.content ||
           document.querySelector('input[name="_token"]')?.value || '';
  }
  function setToken(newToken) {
    // Update meta tag
    const meta = document.querySelector('meta[name="csrf-token"]');
    if (meta) meta.setAttribute('content', newToken);
    // Update all hidden _token inputs (Blade @csrf fields)
    document.querySelectorAll('input[name="_token"]').forEach(i => i.value = newToken);
  }

  // 1) Keep session alive (every 5 minutes)
  function pingKeepAlive() {
    const token = getToken();
    if (!token) return;
    fetch("{{ route('keepalive') }}", {
      method: "POST",
      headers: { "X-CSRF-TOKEN": token, "Accept": "application/json" },
      cache: "no-store",
      credentials: "same-origin",
    }).catch(() => {});
  }
  pingKeepAlive();
  setInterval(pingKeepAlive, 5 * 60 * 1000);

  // Also ping immediately when user returns to the tab (after being idle/inactive)
  document.addEventListener('visibilitychange', function () {
    if (document.visibilityState === 'visible') pingKeepAlive();
  });

  // 2) Periodically refresh CSRF token (every 10 minutes)
  async function refreshCsrf() {
    try {
      const res = await fetch("{{ route('csrf.refresh') }}", {
        cache: "no-store",
        credentials: "same-origin",
      });
      if (!res.ok) return;
      const data = await res.json();
      if (data?.token) setToken(data.token);
    } catch (_) {}
  }
  refreshCsrf();
  setInterval(refreshCsrf, 10 * 60 * 1000);
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
