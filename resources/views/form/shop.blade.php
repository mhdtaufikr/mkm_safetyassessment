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
    .autosave-toast {
      position: fixed;
      bottom: 1.5rem;
      right: 1.5rem;
      z-index: 2000;
    }
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
        <input type="text" id="main-accessor" name="accessor_main" class="form-control" value="{{ old('accessor_main') }}" required>
      </div>
    </div>

    {{-- Tombol clear draft di bagian atas form --}}
    <div class="d-flex justify-content-between mb-2">
      <button type="button" class="btn btn-outline-secondary btn-sm" id="btn-clear-draft">
        Clear Draft (Local)
      </button>
      <button type="submit" id="submit-btn" class="btn btn-success">
        Submit
      </button>
    </div>

    <div class="d-flex justify-content-start mb-3">
      <button type="button" class="btn btn-outline-primary" id="add-entry-btn">
        + Add Entry
      </button>
    </div>

    <div id="risk-assessment-container"></div>

  </form>
</div>

{{-- Toast autosave --}}
<div class="autosave-toast">
  <div id="autosaveToast" class="toast align-items-center text-bg-secondary border-0" role="status" aria-live="polite" aria-atomic="true">
    <div class="d-flex">
      <div class="toast-body">
        Draft tersimpan di perangkat.
      </div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" id="autosave-close"></button>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const container = document.getElementById('risk-assessment-container');
  const addEntryBtn = document.getElementById('add-entry-btn');
  const accessorInput = document.getElementById('main-accessor');
  let index = 1;

  // GLOBAL: createEntry
  window.createEntry = function (i, preset = null) {
    const div = document.createElement('div');
    div.className = '';
    div.innerHTML = `
      <div class="card border-primary mb-4">
        <div class="card-header bg-primary text-white fw-bold">
          Form Entry ${i}
          <button type="button" class="btn btn-danger btn-sm float-end remove-entry">Remove</button>
        </div>
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
              <input name="file[]" type="file" class="form-control file-input">
            </div>
          </div>
          <input type="hidden" name="accessor[]" class="accessor-hidden">
        </div>
      </div>
    `;

    container.appendChild(div);

    const scopeSel = div.querySelector('select[name="scope_number[]"]');
    const findingInp = div.querySelector('input[name="finding_problem[]"]');
    const hazardInp = div.querySelector('input[name="potential_hazards[]"]');
    const sev = div.querySelector('select[name="severity[]"]');
    const prob = div.querySelector('select[name="possibility[]"]');
    const score = div.querySelector('input[name="score[]"]');
    const riskLevel = div.querySelector('input[name="risk_level[]"]');
    const proposalInp = div.querySelector('input[name="risk_reduction_proposal[]"]');
    const hiddenAccessor = div.querySelector('.accessor-hidden');

    hiddenAccessor.value = accessorInput.value || '';

    function updateRisk() {
      const s = parseInt(sev.value) || 0;
      const p = parseInt(prob.value) || 0;
      const result = s * p;
      score.value = result;

      if (result > 16) riskLevel.value = 'Extreme';
      else if (result >= 10) riskLevel.value = 'High';
      else if (result >= 5) riskLevel.value = 'Medium';
      else if (result > 0) riskLevel.value = 'Low';
      else riskLevel.value = '';
    }

    sev.addEventListener('input', updateRisk);
    prob.addEventListener('input', updateRisk);

    // apply preset dari draft
    if (preset) {
      if (preset.scope !== undefined) scopeSel.value = preset.scope;
      if (preset.finding !== undefined) findingInp.value = preset.finding;
      if (preset.hazard !== undefined) hazardInp.value = preset.hazard;
      if (preset.severity !== undefined) sev.value = preset.severity;
      if (preset.possibility !== undefined) prob.value = preset.possibility;
      if (preset.proposal !== undefined) proposalInp.value = preset.proposal;
      updateRisk();
    }
  };

  // initial satu entry
  window.createEntry(index++);

  addEntryBtn.addEventListener('click', function () {
    window.createEntry(index++);
  });

  // sinkron accessor ke hidden accessor[]
  accessorInput.addEventListener('input', function () {
    const value = accessorInput.value;
    document.querySelectorAll('input.accessor-hidden').forEach(input => {
      input.value = value;
    });
  });

  // prevent double submit
  const form = document.getElementById('risk-form');
  const submitBtn = document.getElementById('submit-btn');
  form.addEventListener('submit', function () {
    submitBtn.disabled = true;
    submitBtn.innerText = 'Submitting…';
  });

  // remove entry
  container.addEventListener('click', function (e) {
    if (e.target && e.target.classList.contains('remove-entry')) {
      e.preventDefault();
      const card = e.target.closest('.card.border-primary');
      if (card) card.remove();
    }
  });
});
</script>

{{-- Validasi --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('risk-form');

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
});
</script>

{{-- AUTOSAVE draft risk assessment --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
  const DRAFT_KEY = 'risk_assessment_draft_v1';
  const form = document.getElementById('risk-form');
  const container = document.getElementById('risk-assessment-container');
  const autosaveToastEl = document.getElementById('autosaveToast');
  const bsToast = autosaveToastEl ? new bootstrap.Toast(autosaveToastEl, { delay: 1500 }) : null;

  function showToast() {
    if (bsToast) bsToast.show();
  }
  document.getElementById('autosave-close')?.addEventListener('click', () => {
    if (bsToast) bsToast.hide();
  });

  function collectDraftData() {
    const data = {};
    const mainAccessor = form.querySelector('#main-accessor');
    data.accessor_main = mainAccessor ? mainAccessor.value : '';

    const cards = container.querySelectorAll('.card.border-primary');
    data.entries = [];
    cards.forEach(card => {
      const entry = {
        scope: card.querySelector('select[name="scope_number[]"]')?.value || '',
        finding: card.querySelector('input[name="finding_problem[]"]')?.value || '',
        hazard: card.querySelector('input[name="potential_hazards[]"]')?.value || '',
        severity: card.querySelector('select[name="severity[]"]')?.value || '',
        possibility: card.querySelector('select[name="possibility[]"]')?.value || '',
        proposal: card.querySelector('input[name="risk_reduction_proposal[]"]')?.value || '',
      };
      data.entries.push(entry);
    });

    return data;
  }

  function restoreDraft() {
    if (!form) return;
    try {
      const raw = localStorage.getItem(DRAFT_KEY);
      if (!raw) return;
      const data = JSON.parse(raw);

      if (data.accessor_main !== undefined) {
        const mainAcc = form.querySelector('#main-accessor');
        if (mainAcc) mainAcc.value = data.accessor_main;
      }

      container.innerHTML = '';
      const entries = Array.isArray(data.entries) ? data.entries : [];
      let idx = 1;
      if (entries.length) {
        entries.forEach(e => {
          if (typeof window.createEntry === 'function') {
            window.createEntry(idx++, e);
          }
        });
      } else {
        if (typeof window.createEntry === 'function') {
          window.createEntry(1);
        }
      }
    } catch (e) {
      console.warn('restore draft failed', e);
    }
  }

  setTimeout(restoreDraft, 200);

  let saveTimer;
  function scheduleSave() {
    if (!form) return;
    clearTimeout(saveTimer);
    saveTimer = setTimeout(() => {
      try {
        const data = collectDraftData();
        localStorage.setItem(DRAFT_KEY, JSON.stringify(data));
        showToast();
      } catch (e) {
        console.warn('autosave draft failed', e);
      }
    }, 700);
  }

  if (form) {
    form.addEventListener('input', scheduleSave);
    form.addEventListener('change', scheduleSave);
  }

  document.getElementById('btn-clear-draft')?.addEventListener('click', function () {
    if (!confirm('Hapus draft lokal? Data yang belum disubmit akan hilang.')) return;
    try {
      localStorage.removeItem(DRAFT_KEY);
      form?.reset();
      container.innerHTML = '';
      if (typeof window.createEntry === 'function') {
        window.createEntry(1);
      }
    } catch (e) {
      console.warn('clear draft failed', e);
    }
  });
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
  function getToken() {
    return document.querySelector('meta[name="csrf-token"]')?.content ||
           document.querySelector('input[name="_token"]')?.value || '';
  }
  function setToken(newToken) {
    const meta = document.querySelector('meta[name="csrf-token"]');
    if (meta) meta.setAttribute('content', newToken);
    document.querySelectorAll('input[name="_token"]').forEach(i => i.value = newToken);
  }

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

  document.addEventListener('visibilitychange', function () {
    if (document.visibilityState === 'visible') pingKeepAlive();
  });

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
