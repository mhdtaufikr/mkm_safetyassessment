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

    function createEntry(i, preset = null) {
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

      const sev = div.querySelector('.severity');
      const prob = div.querySelector('.possibility');
      const score = div.querySelector('.score');
      const riskLevel = div.querySelector('.risk-level');
      const hiddenAccessor = div.querySelector('.accessor-hidden');

      // Set accessor dari main accessor
      hiddenAccessor.value = accessorInput.value || '';

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

      // Kalau dipanggil untuk restore draft, set nilai awal
      if (preset) {
        const selects = div.querySelectorAll('select');
        const inputs = div.querySelectorAll('input[type="text"], input[type="number"]');

        selects.forEach(sel => {
          const name = sel.name;
          if (preset[name] !== undefined) sel.value = preset[name];
        });
        inputs.forEach(inp => {
          const name = inp.name;
          if (name.endsWith('score[]') || name.endsWith('risk_level[]')) return; // dihitung ulang
          if (preset[name] !== undefined) inp.value = preset[name];
        });
        // hitung ulang score & level setelah restore
        updateRisk();
      }
    }

    // Update semua accessor[] kalau main accessor berubah
    accessorInput.addEventListener('input', function () {
      const value = accessorInput.value;
      document.querySelectorAll('input.accessor-hidden').forEach(input => {
        input.value = value;
      });
    });

    // Initial satu entry
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

    // Delegasi remove entry
    container.addEventListener('click', function (e) {
      if (e.target && e.target.classList.contains('remove-entry')) {
        e.preventDefault();
        const card = e.target.closest('.card.border-primary');
        if (card) card.remove();
      }
    });
  });
</script>

{{-- Validasi & remove button helper (aslinya) --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('risk-form');
  const container = document.getElementById('risk-assessment-container');

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

  // Serialize semua entry ke objek sederhana
  function collectDraftData() {
    const data = {};
    // header
    const mainAccessor = form.querySelector('#main-accessor');
    data['accessor_main'] = mainAccessor ? mainAccessor.value : '';

    // semua card entry
    const cards = container.querySelectorAll('.card.border-primary');
    data['entries'] = [];
    cards.forEach(card => {
      const entry = {};
      entry['scope_number[]'] = card.querySelector('select[name="scope_number[]"]')?.value || '';
      entry['finding_problem[]'] = card.querySelector('input[name="finding_problem[]"]')?.value || '';
      entry['potential_hazards[]'] = card.querySelector('input[name="potential_hazards[]"]')?.value || '';
      entry['severity[]'] = card.querySelector('select[name="severity[]"]')?.value || '';
      entry['possibility[]'] = card.querySelector('select[name="possibility[]"]')?.value || '';
      entry['risk_reduction_proposal[]'] = card.querySelector('input[name="risk_reduction_proposal[]"]')?.value || '';
      // score & risk_level dihitung ulang, tidak perlu disimpan
      data['entries'].push(entry);
    });

    return data;
  }

  // Restore draft: perlu helper createEntry dari scope global (sudah didefinisikan di atas)
  function restoreDraft() {
    if (!form) return;
    try {
      const raw = localStorage.getItem(DRAFT_KEY);
      if (!raw) return;
      const data = JSON.parse(raw);

      // main accessor
      if (data.accessor_main !== undefined) {
        const mainAcc = form.querySelector('#main-accessor');
        if (mainAcc) mainAcc.value = data.accessor_main;
      }

      // hapus entry default, lalu recreate sesuai draft
      container.innerHTML = '';
      const entries = Array.isArray(data.entries) ? data.entries : [];
      let idx = 1;
      entries.forEach(e => {
        // panggil createEntry yang sudah ada di window
        if (typeof window.createEntry === 'function') {
          window.createEntry(idx++, e);
        } else {
          // fallback: buat minimal 1 entry tanpa preset kalau helper belum tersedia
        }
      });
      // kalau tidak ada entry di draft, biarkan createEntry awal yang DOMContentLoaded buat
    } catch (e) {
      console.warn('restore draft failed', e);
    }
  }

  // expose createEntry global supaya bisa dipakai di restoreDraft
  (function exposeCreateEntry() {
    if (!window.createEntryFromDraft && container) {
      // cari fungsi createEntry di closure pertama (hack sederhana: override dari global)
      // tapi di script atas createEntry ada dalam scope lokal,
      // jadi kita definisikan versi global minimal untuk restore:
    }
  })();

  // Karena createEntry asli ada di script pertama dan tidak global,
  // solusi praktis: panggil restoreDraft setelah sedikit delay,
  // saat createEntry sudah ter-register di window (jika kamu refactor createEntry ke window).
  // Versi ini mengasumsikan kamu memindahkan:
  //   function createEntry(...) { ... }
  // ke window.createEntry = function(...) { ... }
  // di script pertama.

  // Autosave (debounce)
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

  // Clear draft
  document.getElementById('btn-clear-draft')?.addEventListener('click', function () {
    if (!confirm('Hapus draft lokal? Data yang belum disubmit akan hilang.')) return;
    try {
      localStorage.removeItem(DRAFT_KEY);
      form?.reset();
      container.innerHTML = '';
      // buat satu entry baru
      if (typeof window.createEntry === 'function') {
        window.createEntry(1);
      }
    } catch (e) {
      console.warn('clear draft failed', e);
    }
  });

  // opsional: panggil restore di sini kalau createEntry sudah global
  // setTimeout(restoreDraft, 200);
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
