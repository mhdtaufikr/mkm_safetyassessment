<!DOCTYPE html>
<html lang="en">
<head>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Risk Assessment Form - {{ $shopName }}</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            mkm: {
              DEFAULT: 'rgba(0,103,127,1)',
              dark:    'rgba(0,75,93,1)',
              darker:  'rgba(0,50,62,1)',
              light:   'rgba(0,103,127,0.12)',
              muted:   'rgba(0,103,127,0.06)',
              border:  'rgba(0,103,127,0.25)',
            }
          }
        }
      }
    }
  </script>
  <style>
    /* ══════════════════════════════════════════
       BACKGROUND TEXTURE — corporate dot grid
    ══════════════════════════════════════════ */
    body {
      background-color: #eef3f5;
      background-image:
        radial-gradient(circle, rgba(0,103,127,0.12) 1px, transparent 1px),
        repeating-linear-gradient(
          135deg,
          transparent,
          transparent 40px,
          rgba(0,103,127,0.025) 40px,
          rgba(0,103,127,0.025) 41px
        );
      background-size: 24px 24px, 80px 80px;
    }

    /* ══ Glassmorphism card ══ */
    .glass-card {
      background: rgba(255,255,255,0.85);
      backdrop-filter: blur(8px);
      -webkit-backdrop-filter: blur(8px);
    }

    /* ══ Card left accent bar ══ */
    .card-accent { position: relative; }
    .card-accent::before {
      content: '';
      position: absolute;
      top: 0; left: 0;
      width: 4px;
      height: 100%;
      background: linear-gradient(180deg, rgba(0,103,127,1), rgba(0,50,62,0.4));
      border-radius: 1rem 0 0 1rem;
    }

    /* ══ Section divider ══ */
    .section-divider {
      height: 2px;
      background: linear-gradient(90deg, rgba(0,103,127,0.6), rgba(0,103,127,0.05) 80%, transparent);
      border-radius: 2px;
      margin-bottom: 1.25rem;
    }

    /* ══ Watermark ══ */
    .page-watermark {
      position: fixed;
      bottom: 3rem;
      left: 50%;
      transform: translateX(-50%);
      font-size: 8rem;
      font-weight: 900;
      letter-spacing: 0.3em;
      color: rgba(0,103,127,0.04);
      pointer-events: none;
      user-select: none;
      z-index: 0;
      white-space: nowrap;
    }

    /* ══ Select custom arrow ══ */
    select {
      background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%2300677f' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
      background-position: right 0.5rem center;
      background-repeat: no-repeat;
      background-size: 1.5em 1.5em;
      padding-right: 2.5rem;
      -webkit-appearance: none;
      -moz-appearance: none;
      appearance: none;
    }

    /* ══ Entry card animation ══ */
    @keyframes fadeSlideIn {
      from { opacity: 0; transform: translateY(-10px); }
      to   { opacity: 1; transform: translateY(0); }
    }
    .entry-card { animation: fadeSlideIn 0.2s ease; }

    /* ══ Inline validation ══ */
    .invalid-feedback {
      display: none;
      font-size: 0.75rem;
      color: #dc2626;
      margin-top: 0.25rem;
    }
    .is-invalid ~ .invalid-feedback { display: block; }
    .is-invalid {
      border-color: #dc2626 !important;
      box-shadow: 0 0 0 3px rgba(220,38,38,0.15) !important;
    }

    /* ══ Autosave toast ══ */
    .autosave-toast {
      position: fixed;
      bottom: 1.5rem;
      right: 1.5rem;
      z-index: 2000;
    }

    /* ══ Risk level badges ══ */
    .risk-extreme { background:#fee2e2; color:#991b1b; border:1px solid #fca5a5; }
    .risk-high    { background:#ffedd5; color:#9a3412; border:1px solid #fdba74; }
    .risk-medium  { background:#fef9c3; color:#854d0e; border:1px solid #fde047; }
    .risk-low     { background:#dcfce7; color:#166534; border:1px solid #86efac; }

    /* ══ Focus ring global ══ */
    input:focus, select:focus, textarea:focus {
      outline: none;
      box-shadow: 0 0 0 3px rgba(0,103,127,0.2) !important;
      border-color: rgba(0,103,127,1) !important;
    }
  </style>
</head>

<body class="min-h-screen antialiased">

  <!-- Watermark -->
  <div class="page-watermark">PT MKM</div>

  <!-- ══ PAGE HEADER ══ -->
  <div style="background: linear-gradient(135deg, rgba(0,30,40,1) 0%, rgba(0,75,93,1) 40%, rgba(0,103,127,1) 75%, rgba(0,140,170,1) 100%);"
       class="shadow-xl relative overflow-hidden">

    <!-- Header stripe texture -->
    <div class="absolute inset-0 opacity-10"
         style="background-image: repeating-linear-gradient(45deg, rgba(255,255,255,0.15) 0px, rgba(255,255,255,0.15) 1px, transparent 1px, transparent 12px);
                background-size: 17px 17px;"></div>

    <!-- Decorative blobs -->
    <div class="absolute -top-10 -right-10 w-48 h-48 rounded-full opacity-10"
         style="background:rgba(255,255,255,0.2);"></div>
    <div class="absolute -bottom-6 right-32 w-28 h-28 rounded-full opacity-10"
         style="background:rgba(255,255,255,0.15);"></div>

    <div class="max-w-4xl mx-auto px-4 py-6 flex items-center gap-4 relative z-10">
      <div class="rounded-2xl p-3 shrink-0 shadow-inner"
           style="background:rgba(255,255,255,0.15); border:1px solid rgba(255,255,255,0.2);">
        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
        </svg>
      </div>
      <div>
        <p class="text-white/60 text-xs uppercase tracking-widest font-semibold mb-0.5">PT Mitsubishi Krama Yudha Motors &amp; Manufacturing</p>
        <h1 class="text-white font-black text-xl leading-tight">Risk Assessment Form</h1>
        <p class="text-white/50 text-xs mt-0.5">Workplace Accident Prevention &mdash; {{ $shopName }}</p>
      </div>
    </div>
  </div>

  <!-- ══ BREADCRUMB ══ -->
  <div style="background:rgba(0,103,127,1);" class="shadow-sm">
    <div class="max-w-4xl mx-auto px-4 py-2 flex items-center gap-2 text-white/70 text-xs">
      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
      </svg>
      <span>Home</span>
      <span class="text-white/30">/</span>
      <span class="text-white font-semibold">Risk Assessment</span>
    </div>
  </div>

  <div class="max-w-4xl mx-auto px-4 py-6 mb-10 relative z-10">

    <!-- Flag submit sukses -->
    <script>
      window.__submitSuccess = {{ session('success') ? 'true' : 'false' }};
    </script>

    <!-- Shop Image -->
    @if(!empty($shopImage) && file_exists(public_path('storage/' . $shopImage)))
    <div class="glass-card rounded-2xl shadow-sm border border-white/80 p-4 mb-5 flex justify-center card-accent">
      <img src="{{ asset('storage/' . $shopImage) . '?v=' . \Carbon\Carbon::parse($shopUpdatedAt)->timestamp }}"
           class="max-h-64 w-auto rounded-xl object-contain shadow-sm"
           alt="{{ $shopName }}">
    </div>
    @endif

    <!-- Success Alert -->
    @if (session('success'))
    <div id="success-alert"
         class="flex items-center gap-3 px-4 py-3 rounded-2xl mb-5 shadow-sm"
         style="background:rgba(0,103,127,0.08); border:1px solid rgba(0,103,127,0.3);">
      <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"
           style="color:rgba(0,103,127,1);">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
      </svg>
      <span class="text-sm font-semibold flex-1" style="color:rgba(0,75,93,1);">{{ session('success') }}</span>
      <button onclick="document.getElementById('success-alert').remove()"
              class="text-xl leading-none font-bold"
              style="color:rgba(0,103,127,0.5);">&times;</button>
    </div>
    @endif

    <!-- FORM -->
    <form id="risk-form"
          method="POST"
          action="{{ route('risk-assessment.store') }}"
          enctype="multipart/form-data"
          autocomplete="off">
      @csrf

      <!-- General Info Card -->
      <div class="glass-card rounded-2xl shadow-sm border border-white/80 mb-5 card-accent">
        <div class="px-5 py-4 flex items-center gap-2"
             style="border-bottom:1px solid rgba(0,103,127,0.1);">
          <span class="w-1.5 h-5 rounded-full inline-block"
                style="background:linear-gradient(180deg,rgba(0,103,127,1),rgba(0,50,62,0.6));"></span>
          <h2 class="font-bold text-sm uppercase tracking-wide" style="color:rgba(0,50,62,1);">General Information</h2>
        </div>
        <div class="px-5 py-5 grid grid-cols-1 sm:grid-cols-3 gap-4">

          <!-- Shop -->
          <div>
            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wide mb-1">Shop</label>
            <input type="text"
                   class="w-full rounded-xl border border-slate-200 bg-slate-100/80 px-3 py-2 text-sm text-slate-500 cursor-not-allowed"
                   value="{{ $shopName }}" readonly disabled>
            <input type="hidden" name="shop_id" value="{{ $shopId }}">
          </div>

          <!-- Date -->
          <div>
            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wide mb-1">Date</label>
            @php $today = date('Y-m-d'); @endphp
            <input type="date"
                   class="w-full rounded-xl border border-slate-200 bg-slate-100/80 px-3 py-2 text-sm text-slate-500 cursor-not-allowed"
                   value="{{ $today }}" readonly disabled>
            <input type="hidden" name="date" value="{{ $today }}">
          </div>

          <!-- Accessor -->
          <div>
            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wide mb-1">
              Accessor <span class="text-red-400">*</span>
            </label>
            <input type="text"
                   id="main-accessor"
                   name="accessor_main"
                   class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm text-slate-700 bg-white/80 transition"
                   value="{{ old('accessor_main') }}"
                   placeholder="Enter accessor name"
                   required>
          </div>

        </div>
      </div>

      <!-- Action Bar -->
      <div class="flex flex-col sm:flex-row items-center justify-between gap-3 mb-4">

        <!-- Clear Draft -->
        <button type="button"
                id="btn-clear-draft"
                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 text-sm font-semibold px-4 py-2 rounded-xl border transition"
                style="border-color:rgba(0,103,127,0.3); color:rgba(0,103,127,1); background:transparent;"
                onmouseover="this.style.background='rgba(0,103,127,0.08)'"
                onmouseout="this.style.background='transparent'">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
          </svg>
          Clear Draft (Local)
        </button>

        <!-- Submit -->
        <button type="submit"
                id="submit-btn"
                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 text-white font-bold text-sm px-7 py-2.5 rounded-xl shadow-lg transition"
                style="background:linear-gradient(135deg, rgba(0,75,93,1) 0%, rgba(0,103,127,1) 100%);"
                onmouseover="this.style.background='linear-gradient(135deg,rgba(0,50,62,1) 0%,rgba(0,75,93,1) 100%)'"
                onmouseout="this.style.background='linear-gradient(135deg,rgba(0,75,93,1) 0%,rgba(0,103,127,1) 100%)'">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
          </svg>
          Submit Assessment
        </button>
      </div>

      <!-- Add Entry Button -->
      <div class="mb-4">
        <button type="button"
                id="add-entry-btn"
                class="inline-flex items-center gap-2 text-sm font-bold px-4 py-2 rounded-xl border-2 transition"
                style="border-color:rgba(0,103,127,1); color:rgba(0,103,127,1); background:transparent;"
                onmouseover="this.style.background='rgba(0,103,127,0.08)'"
                onmouseout="this.style.background='transparent'">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
          </svg>
          Add Entry
        </button>
      </div>

      <!-- Entry Container -->
      <div id="risk-assessment-container"></div>

    </form>
  </div>

  <!-- Autosave Toast -->
  <div class="autosave-toast">
    <div id="autosaveToast"
         class="hidden items-center gap-3 text-white text-sm px-4 py-3 rounded-2xl shadow-xl min-w-[220px]"
         style="background:rgba(0,30,40,0.95); border:1px solid rgba(0,103,127,0.4); backdrop-filter:blur(8px);">
      <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"
           style="color:rgba(0,200,160,1);">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
      </svg>
      <span class="flex-1 text-white/80">Draft tersimpan di perangkat.</span>
      <button id="autosave-close" class="text-white/40 hover:text-white text-lg leading-none">&times;</button>
    </div>
  </div>


  <!-- =====================================================================
       SCRIPT 1 — Core: createEntry, accessor sync, submit guard, remove
  ===================================================================== -->
  <script>
  document.addEventListener('DOMContentLoaded', function () {
    const container     = document.getElementById('risk-assessment-container');
    const addEntryBtn   = document.getElementById('add-entry-btn');
    const accessorInput = document.getElementById('main-accessor');
    const DRAFT_KEY     = 'risk_assessment_draft_v1';
    let index = 1;

    function applyRiskStyle(riskInput, value) {
      riskInput.classList.remove('risk-extreme','risk-high','risk-medium','risk-low');
      if (value === 'Extreme')     riskInput.classList.add('risk-extreme');
      else if (value === 'High')   riskInput.classList.add('risk-high');
      else if (value === 'Medium') riskInput.classList.add('risk-medium');
      else if (value === 'Low')    riskInput.classList.add('risk-low');
    }

    window.createEntry = function (i, preset = null) {
      const div = document.createElement('div');
      div.className = 'entry-card';
      div.innerHTML = `
        <div class="card border-primary rounded-2xl shadow-sm border border-white/80 mb-5 overflow-hidden"
             style="background:rgba(255,255,255,0.85); backdrop-filter:blur(8px);">

          <!-- Card Header -->
          <div class="card-header flex items-center justify-between px-5 py-3.5 relative overflow-hidden"
               style="background:linear-gradient(135deg, rgba(0,50,62,1) 0%, rgba(0,103,127,1) 60%, rgba(0,130,155,1) 100%);">
            <!-- Header texture -->
            <div class="absolute inset-0 opacity-10"
                 style="background-image: repeating-linear-gradient(45deg, rgba(255,255,255,0.2) 0px, rgba(255,255,255,0.2) 1px, transparent 1px, transparent 10px);
                        background-size: 14px 14px;"></div>
            <span class="text-white font-black text-sm flex items-center gap-2 relative z-10">
              <span class="rounded-lg px-2 py-0.5 text-xs font-black"
                    style="background:rgba(255,255,255,0.18); border:1px solid rgba(255,255,255,0.2);">
                #${i}
              </span>
              Form Entry ${i}
            </span>
            <button type="button"
                    class="remove-entry inline-flex items-center gap-1 text-white text-xs font-bold px-3 py-1.5 rounded-lg transition relative z-10"
                    style="background:rgba(220,38,38,0.75); border:1px solid rgba(255,255,255,0.2);"
                    onmouseover="this.style.background='rgba(185,28,28,0.95)'"
                    onmouseout="this.style.background='rgba(220,38,38,0.75)'">
              <svg class="w-3 h-3 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                  class="pointer-events-none"/>
              </svg>
              Remove
            </button>
          </div>

          <!-- Card Body -->
          <div class="card-body px-5 py-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-4">

              <!-- Scope -->
              <div class="lg:col-span-4">
                <label class="form-label block text-xs font-bold text-slate-400 uppercase tracking-wide mb-1">
                  Scope <span class="text-red-400">*</span>
                </label>
                <select name="scope_number[]"
                        class="w-full rounded-xl border border-slate-300 bg-white/80 px-3 py-2 text-sm text-slate-700 transition"
                        required>
                  <option value="">-- Select Scope --</option>
                  <option value="1">1 - Man</option>
                  <option value="2">2 - Machine</option>
                  <option value="3">3 - Method</option>
                  <option value="4">4 - Material</option>
                  <option value="5">5 - Environment</option>
                </select>
              </div>

              <!-- Finding Problem -->
              <div class="lg:col-span-8">
                <label class="form-label block text-xs font-bold text-slate-400 uppercase tracking-wide mb-1">
                  Finding Problem <span class="text-red-400">*</span>
                </label>
                <input name="finding_problem[]" type="text"
                       class="w-full rounded-xl border border-slate-300 bg-white/80 px-3 py-2 text-sm text-slate-700 transition"
                       required>
              </div>

              <!-- Potential Hazard -->
              <div class="lg:col-span-4">
                <label class="form-label block text-xs font-bold text-slate-400 uppercase tracking-wide mb-1">
                  Potential Hazard <span class="text-red-400">*</span>
                </label>
                <input name="potential_hazards[]" type="text"
                       class="w-full rounded-xl border border-slate-300 bg-white/80 px-3 py-2 text-sm text-slate-700 transition"
                       required>
              </div>

              <!-- Severity -->
              <div class="lg:col-span-4">
                <label class="form-label block text-xs font-bold text-slate-400 uppercase tracking-wide mb-1">
                  Severity <span class="text-red-400">*</span>
                </label>
                <select name="severity[]"
                        class="severity w-full rounded-xl border border-slate-300 bg-white/80 px-3 py-2 text-sm text-slate-700 transition"
                        required>
                  <option value="">-- Select --</option>
                  <option value="1">1 - Insignificant</option>
                  <option value="2">2 - Minor</option>
                  <option value="3">3 - Moderate</option>
                  <option value="4">4 - Major</option>
                  <option value="5">5 - Catastrophic</option>
                </select>
              </div>

              <!-- Possibility -->
              <div class="lg:col-span-4">
                <label class="form-label block text-xs font-bold text-slate-400 uppercase tracking-wide mb-1">
                  Possibility <span class="text-red-400">*</span>
                </label>
                <select name="possibility[]"
                        class="possibility w-full rounded-xl border border-slate-300 bg-white/80 px-3 py-2 text-sm text-slate-700 transition"
                        required>
                  <option value="">-- Select --</option>
                  <option value="1">1 - Very Rare</option>
                  <option value="2">2 - Unlikely</option>
                  <option value="3">3 - Occasional</option>
                  <option value="4">4 - Frequent</option>
                  <option value="5">5 - Always</option>
                </select>
              </div>

              <!-- Score -->
              <div class="lg:col-span-3">
                <label class="form-label block text-xs font-bold text-slate-400 uppercase tracking-wide mb-1">Score</label>
                <input name="score[]" type="number"
                       class="score w-full rounded-xl border border-slate-200 bg-slate-100/80 px-3 py-2 text-sm text-center font-black cursor-not-allowed"
                       style="color:rgba(0,103,127,1);"
                       readonly>
              </div>

              <!-- Risk Level -->
              <div class="lg:col-span-3">
                <label class="form-label block text-xs font-bold text-slate-400 uppercase tracking-wide mb-1">Risk Level</label>
                <input name="risk_level[]" type="text"
                       class="risk-level w-full rounded-xl border border-slate-200 bg-slate-100/80 px-3 py-2 text-sm text-center font-black cursor-not-allowed"
                       readonly>
              </div>

              <!-- Risk Reduction Proposal -->
              <div class="lg:col-span-6">
                <label class="form-label block text-xs font-bold text-slate-400 uppercase tracking-wide mb-1">
                  Risk Reduction Measures Proposal <span class="text-red-400">*</span>
                </label>
                <input name="risk_reduction_proposal[]" type="text"
                       class="w-full rounded-xl border border-slate-300 bg-white/80 px-3 py-2 text-sm text-slate-700 transition"
                       required>
              </div>

              <!-- Attach File -->
              <div class="lg:col-span-12">
                <label class="form-label block text-xs font-bold text-slate-400 uppercase tracking-wide mb-1">
                  Attach File
                  <span class="text-slate-300 font-normal normal-case">(optional)</span>
                </label>
                <input name="file[]" type="file"
                       class="file-input w-full text-sm text-slate-600 transition
                              file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0
                              file:text-sm file:font-bold"
                       style="--tw-file-selector-color: rgba(0,103,127,1);">
                <style>
                  .file-input::file-selector-button {
                    background: rgba(0,103,127,0.08);
                    color: rgba(0,103,127,1);
                    border: 1px solid rgba(0,103,127,0.3);
                    padding: 0.4rem 1rem;
                    border-radius: 0.5rem;
                    font-size: 0.8rem;
                    font-weight: 700;
                    cursor: pointer;
                    transition: background 0.15s;
                  }
                  .file-input::file-selector-button:hover {
                    background: rgba(0,103,127,0.15);
                  }
                </style>
              </div>

            </div>

            <!-- Hidden accessor -->
            <input type="hidden" name="accessor[]" class="accessor-hidden">
          </div>
        </div>
      `;

      container.appendChild(div);

      const scopeSel       = div.querySelector('select[name="scope_number[]"]');
      const findingInp     = div.querySelector('input[name="finding_problem[]"]');
      const hazardInp      = div.querySelector('input[name="potential_hazards[]"]');
      const sev            = div.querySelector('select[name="severity[]"]');
      const prob           = div.querySelector('select[name="possibility[]"]');
      const score          = div.querySelector('input[name="score[]"]');
      const riskLevel      = div.querySelector('input[name="risk_level[]"]');
      const proposalInp    = div.querySelector('input[name="risk_reduction_proposal[]"]');
      const hiddenAccessor = div.querySelector('.accessor-hidden');

      hiddenAccessor.value = accessorInput.value || '';

      function updateRisk() {
        const s      = parseInt(sev.value)  || 0;
        const p      = parseInt(prob.value) || 0;
        const result = s * p;
        score.value  = result;

        if (result > 16)       riskLevel.value = 'Extreme';
        else if (result >= 10) riskLevel.value = 'High';
        else if (result >= 5)  riskLevel.value = 'Medium';
        else if (result > 0)   riskLevel.value = 'Low';
        else                   riskLevel.value = '';

        applyRiskStyle(riskLevel, riskLevel.value);
      }

      sev.addEventListener('input', updateRisk);
      prob.addEventListener('input', updateRisk);

      if (preset) {
        if (preset.scope       !== undefined) scopeSel.value    = preset.scope;
        if (preset.finding     !== undefined) findingInp.value  = preset.finding;
        if (preset.hazard      !== undefined) hazardInp.value   = preset.hazard;
        if (preset.severity    !== undefined) sev.value         = preset.severity;
        if (preset.possibility !== undefined) prob.value        = preset.possibility;
        if (preset.proposal    !== undefined) proposalInp.value = preset.proposal;
        updateRisk();
      }
    };

    window.createEntry(index++);

    addEntryBtn.addEventListener('click', function () {
      window.createEntry(index++);
    });

    accessorInput.addEventListener('input', function () {
      const value = accessorInput.value;
      document.querySelectorAll('input.accessor-hidden').forEach(input => {
        input.value = value;
      });
    });

    const form      = document.getElementById('risk-form');
    const submitBtn = document.getElementById('submit-btn');

    form.addEventListener('submit', function (e) {
      const hasInvalid = form.querySelector('.is-invalid');
      if (!hasInvalid) {
        try { localStorage.removeItem(DRAFT_KEY); } catch (_) {}
        submitBtn.disabled = true;
        submitBtn.innerHTML = `
          <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
          </svg>
          Submitting…
        `;
      }
    });

    container.addEventListener('click', function (e) {
      if (e.target && e.target.classList.contains('remove-entry')) {
        e.preventDefault();
        const card = e.target.closest('.card.border-primary');
        if (card) card.remove();
      }
    });
  });
  </script>


  <!-- =====================================================================
       SCRIPT 2 — Validasi inline
  ===================================================================== -->
  <script>
  document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('risk-form');

    function fieldLabel(el) {
      const wrap = el.closest('[class*="lg:col-span"], .col-md-4, .col-md-6, .col-md-8, .col-12');
      const lbl  = wrap ? wrap.querySelector('label.form-label') : null;
      return (lbl ? lbl.textContent.trim() : (el.name || el.id || 'This field')).replace(/\[\]$/, '');
    }

    function ensureFeedback(el) {
      let fb = el.nextElementSibling?.classList?.contains('invalid-feedback')
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
      const ok = el.tagName === 'SELECT'
                 ? el.value !== ''
                 : String(el.value || '').trim().length > 0;
      if (!ok) {
        ensureFeedback(el).textContent = fieldLabel(el) + ' is required.';
        el.classList.add('is-invalid');
      } else {
        el.classList.remove('is-invalid');
        const fb = el.nextElementSibling;
        if (fb?.classList.contains('invalid-feedback')) fb.textContent = '';
      }
      return ok;
    }

    form.addEventListener('focusout', (e) => {
      const el = e.target;
      if (form.contains(el) && el.matches('input[required], select[required], textarea[required]'))
        validate(el);
    });

    form.addEventListener('input', (e) => {
      if (e.target.classList.contains('is-invalid')) validate(e.target);
    });

    form.addEventListener('submit', (e) => {
      const fields = form.querySelectorAll('input[required], select[required], textarea[required]');
      let allValid = true;
      fields.forEach(el => { if (!validate(el)) allValid = false; });

      if (!allValid) {
        e.preventDefault();
        const firstInvalid = form.querySelector('.is-invalid');
        if (firstInvalid) firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
        const btn = document.getElementById('submit-btn');
        if (btn) {
          btn.disabled = false;
          btn.style.background = 'linear-gradient(135deg, rgba(0,75,93,1) 0%, rgba(0,103,127,1) 100%)';
          btn.innerHTML = `
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
            </svg>
            Submit Assessment
          `;
        }
      }
    });
  });
  </script>


  <!-- =====================================================================
       SCRIPT 3 — Autosave draft
  ===================================================================== -->
  <script>
  document.addEventListener('DOMContentLoaded', function () {
    const DRAFT_KEY = 'risk_assessment_draft_v1';
    const form      = document.getElementById('risk-form');
    const container = document.getElementById('risk-assessment-container');
    const toastEl   = document.getElementById('autosaveToast');

    function showToast() {
      if (!toastEl) return;
      toastEl.classList.remove('hidden');
      toastEl.classList.add('flex');
      setTimeout(() => {
        toastEl.classList.add('hidden');
        toastEl.classList.remove('flex');
      }, 1500);
    }

    document.getElementById('autosave-close')?.addEventListener('click', () => {
      toastEl?.classList.add('hidden');
      toastEl?.classList.remove('flex');
    });

    function collectDraftData() {
      const data = { accessor_main: form.querySelector('#main-accessor')?.value || '', entries: [] };
      container.querySelectorAll('.card.border-primary').forEach(card => {
        data.entries.push({
          scope:       card.querySelector('select[name="scope_number[]"]')?.value           || '',
          finding:     card.querySelector('input[name="finding_problem[]"]')?.value         || '',
          hazard:      card.querySelector('input[name="potential_hazards[]"]')?.value       || '',
          severity:    card.querySelector('select[name="severity[]"]')?.value               || '',
          possibility: card.querySelector('select[name="possibility[]"]')?.value            || '',
          proposal:    card.querySelector('input[name="risk_reduction_proposal[]"]')?.value || '',
        });
      });
      return data;
    }

    function restoreDraft() {
      if (!form) return;

      if (window.__submitSuccess) {
        try { localStorage.removeItem(DRAFT_KEY); } catch (_) {}
        if (container.children.length === 0) {
          if (typeof window.createEntry === 'function') window.createEntry(1);
        }
        return;
      }

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
            if (typeof window.createEntry === 'function') window.createEntry(idx++, e);
          });
        } else {
          if (typeof window.createEntry === 'function') window.createEntry(1);
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
          localStorage.setItem(DRAFT_KEY, JSON.stringify(collectDraftData()));
          showToast();
        } catch (e) {
          console.warn('autosave draft failed', e);
        }
      }, 700);
    }

    form?.addEventListener('input',  scheduleSave);
    form?.addEventListener('change', scheduleSave);

    document.getElementById('btn-clear-draft')?.addEventListener('click', function () {
      if (!confirm('Hapus draft lokal? Data yang belum disubmit akan hilang.')) return;
      try {
        localStorage.removeItem(DRAFT_KEY);
        form?.reset();
        container.innerHTML = '';
        if (typeof window.createEntry === 'function') window.createEntry(1);
      } catch (e) {
        console.warn('clear draft failed', e);
      }
    });
  });
  </script>


  <!-- =====================================================================
       SCRIPT 4 — CSRF keepalive + token refresh
  ===================================================================== -->
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

</body>
</html>
