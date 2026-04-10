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
              dark: 'rgba(0,75,93,1)',
              darker: 'rgba(0,50,62,1)',
              light: 'rgba(0,103,127,0.12)',
              muted: 'rgba(0,103,127,0.06)',
              border: 'rgba(0,103,127,0.25)',
            }
          }
        }
      }
    }
  </script>
  <style>
    body {
      background-color: #eef3f5;
      background-image:
        radial-gradient(circle, rgba(0, 103, 127, 0.12) 1px, transparent 1px),
        repeating-linear-gradient(135deg, transparent, transparent 40px, rgba(0, 103, 127, 0.025) 40px, rgba(0, 103, 127, 0.025) 41px);
      background-size: 24px 24px, 80px 80px;
    }

    .glass-card {
      background: rgba(255, 255, 255, 0.85);
      backdrop-filter: blur(8px);
      -webkit-backdrop-filter: blur(8px);
    }

    .card-accent {
      position: relative;
    }

    .card-accent::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 4px;
      height: 100%;
      background: linear-gradient(180deg, rgba(0, 103, 127, 1), rgba(0, 50, 62, 0.4));
      border-radius: 1rem 0 0 1rem;
    }

    .section-divider {
      height: 2px;
      background: linear-gradient(90deg, rgba(0, 103, 127, 0.6), rgba(0, 103, 127, 0.05) 80%, transparent);
      border-radius: 2px;
      margin-bottom: 1.25rem;
    }

    .page-watermark {
      position: fixed;
      bottom: 3rem;
      left: 50%;
      transform: translateX(-50%);
      font-size: 8rem;
      font-weight: 900;
      letter-spacing: 0.3em;
      color: rgba(0, 103, 127, 0.04);
      pointer-events: none;
      user-select: none;
      z-index: 0;
      white-space: nowrap;
    }

    /* ── Select custom arrow ── */
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

    /* ── Entry card animation ── */
    @keyframes fadeSlideIn {
      from {
        opacity: 0;
        transform: translateY(-10px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .entry-card {
      animation: fadeSlideIn 0.2s ease;
    }

    /* ── Inline validation ── */
    .invalid-feedback {
      display: none;
      font-size: 0.75rem;
      color: #dc2626;
      margin-top: 0.25rem;
    }

    .field-invalid~.invalid-feedback {
      display: block;
    }

    .field-invalid {
      border-color: #dc2626 !important;
      box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.15) !important;
    }

    /* ── Risk level badges ── */
    .risk-extreme {
      background: #fee2e2;
      color: #991b1b;
      border: 1px solid #fca5a5;
    }

    .risk-high {
      background: #ffedd5;
      color: #9a3412;
      border: 1px solid #fdba74;
    }

    .risk-medium {
      background: #fef9c3;
      color: #854d0e;
      border: 1px solid #fde047;
    }

    .risk-low {
      background: #dcfce7;
      color: #166534;
      border: 1px solid #86efac;
    }

    /* ── Focus ring ── */
    input:focus,
    select:focus,
    textarea:focus {
      outline: none;
      box-shadow: 0 0 0 3px rgba(0, 103, 127, 0.2) !important;
      border-color: rgba(0, 103, 127, 1) !important;
    }

    /* ── File input styling ── */
    .file-input::file-selector-button {
      background: rgba(0, 103, 127, 0.08);
      color: rgba(0, 103, 127, 1);
      border: 1px solid rgba(0, 103, 127, 0.3);
      padding: 0.4rem 1rem;
      border-radius: 0.5rem;
      font-size: 0.8rem;
      font-weight: 700;
      cursor: pointer;
      transition: background 0.15s;
      margin-right: 0.75rem;
    }

    .file-input::file-selector-button:hover {
      background: rgba(0, 103, 127, 0.15);
    }

    /* ── Full Page Submit Overlay ── */
    #submit-overlay {
      display: none;
      position: fixed;
      inset: 0;
      z-index: 9999;
      background: rgba(0, 20, 30, 0.82);
      backdrop-filter: blur(6px);
      -webkit-backdrop-filter: blur(6px);
      flex-direction: column;
      align-items: center;
      justify-content: center;
      gap: 1.5rem;
    }

    #submit-overlay.active {
      display: flex;
    }

    @keyframes spin {
      to {
        transform: rotate(360deg);
      }
    }

    .spinner {
      width: 52px;
      height: 52px;
      border: 4px solid rgba(255, 255, 255, 0.15);
      border-top-color: rgba(0, 200, 160, 1);
      border-radius: 50%;
      animation: spin 0.8s linear infinite;
    }

    /* ── Warning bar ── */
    #submit-warning-bar {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      z-index: 10000;
      background: #dc2626;
      color: #fff;
      text-align: center;
      font-size: 0.8rem;
      font-weight: 700;
      padding: 0.5rem 1rem;
      letter-spacing: 0.02em;
    }

    #submit-warning-bar.active {
      display: block;
    }
  </style>
</head>

<body class="min-h-screen antialiased">

  <div class="page-watermark">PT MKM</div>

  <!-- ══ Full Page Submit Overlay ══ -->
  <div id="submit-overlay">
    <div class="spinner"></div>
    <div style="text-align:center;">
      <p class="text-lg font-black tracking-wide text-white">Submitting Assessment...</p>
      <p class="mt-1 text-sm text-white/60">Mohon tunggu, jangan tutup atau refresh halaman ini.</p>
    </div>
    <div
      style="background:rgba(220,38,38,0.15); border:1px solid rgba(220,38,38,0.5); border-radius:0.75rem; padding:0.75rem 1.25rem; max-width:340px; text-align:center;">
      <p class="text-xs font-semibold text-red-300">
        ⚠️ Menutup atau merefresh halaman sekarang dapat menyebabkan data tidak tersimpan.
      </p>
    </div>
  </div>

  <!-- ══ Warning Bar ══ -->
  <div id="submit-warning-bar">
    ⚠️ Submit sedang berlangsung — JANGAN tutup atau refresh halaman ini!
  </div>

  <!-- ══ PAGE HEADER ══ -->
  <div
    style="background: linear-gradient(135deg, rgba(0,30,40,1) 0%, rgba(0,75,93,1) 40%, rgba(0,103,127,1) 75%, rgba(0,140,170,1) 100%);"
    class="relative overflow-hidden shadow-xl">
    <div class="absolute inset-0 opacity-10"
      style="background-image: repeating-linear-gradient(45deg, rgba(255,255,255,0.15) 0px, rgba(255,255,255,0.15) 1px, transparent 1px, transparent 12px); background-size: 17px 17px;">
    </div>
    <div class="absolute -right-10 -top-10 h-48 w-48 rounded-full opacity-10" style="background:rgba(255,255,255,0.2);">
    </div>
    <div class="absolute -bottom-6 right-32 h-28 w-28 rounded-full opacity-10"
      style="background:rgba(255,255,255,0.15);"></div>
    <div class="relative z-10 mx-auto flex max-w-4xl items-center gap-4 px-4 py-6">
      <div class="shrink-0 rounded-2xl p-3 shadow-inner"
        style="background:rgba(255,255,255,0.15); border:1px solid rgba(255,255,255,0.2);">
        <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
        </svg>
      </div>
      <div>
        <p class="mb-0.5 text-xs font-semibold uppercase tracking-widest text-white/60">PT Mitsubishi Krama Yudha Motors
          &amp; Manufacturing</p>
        <h1 class="text-xl font-black leading-tight text-white">Risk Assessment Form</h1>
        <p class="mt-0.5 text-xs text-white/50">Workplace Accident Prevention &mdash; {{ $shopName }}</p>
      </div>
    </div>
  </div>

  <!-- ══ BREADCRUMB ══ -->
  <div style="background:rgba(0,103,127,1);" class="shadow-sm">
    <div class="mx-auto flex max-w-4xl items-center gap-2 px-4 py-2 text-xs text-white/70">
      <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
      </svg>
      <span>Home</span>
      <span class="text-white/30">/</span>
      <span class="font-semibold text-white">Risk Assessment</span>
    </div>
  </div>

  <!-- ══ MAIN CONTENT ══ -->
  <div class="relative z-10 mx-auto mb-10 max-w-4xl px-4 py-6">

    {{-- ✅ FIX: pakai @json() agar tidak rawan JS syntax error --}}
    <script>
      window.__submitSuccess = @json((bool) session('success'));
    </script>

    {{-- Shop Image --}}
    @if (!empty($shopImage) && file_exists(public_path('storage/' . $shopImage)))
      <div class="glass-card card-accent mb-5 flex justify-center rounded-2xl border border-white/80 p-4 shadow-sm">
        <img src="{{ asset('storage/' . $shopImage) . '?v=' . \Carbon\Carbon::parse($shopUpdatedAt)->timestamp }}"
          class="max-h-64 w-auto rounded-xl object-contain shadow-sm" alt="{{ $shopName }}">
      </div>
    @endif

    {{-- Success Alert --}}
    @if (session('success'))
      <div id="success-alert" class="mb-5 flex items-center gap-3 rounded-2xl px-4 py-3 shadow-sm"
        style="background:rgba(0,103,127,0.08); border:1px solid rgba(0,103,127,0.3);">
        <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"
          style="color:rgba(0,103,127,1);">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span class="flex-1 text-sm font-semibold" style="color:rgba(0,75,93,1);">{{ session('success') }}</span>
        <button onclick="document.getElementById('success-alert').remove()" class="text-xl font-bold leading-none"
          style="color:rgba(0,103,127,0.5);">&times;</button>
      </div>
    @endif

    {{-- Error Alert --}}
    @if ($errors->any())
      <div class="mb-5 flex items-start gap-3 rounded-2xl px-4 py-3 shadow-sm"
        style="background:rgba(220,38,38,0.06); border:1px solid rgba(220,38,38,0.25);">
        <svg class="mt-0.5 h-5 w-5 shrink-0 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
        <div>
          <p class="mb-1 text-sm font-bold text-red-700">Terdapat kesalahan, silakan periksa kembali:</p>
          <ul class="list-inside list-disc space-y-0.5 text-xs text-red-600">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      </div>
    @endif

    <!-- FORM -->
    <form id="risk-form" method="POST" action="{{ route('risk-assessment.store') }}" enctype="multipart/form-data"
      autocomplete="off">
      @csrf

      <!-- General Info Card -->
      <div class="glass-card card-accent mb-5 rounded-2xl border border-white/80 shadow-sm">
        <div class="flex items-center gap-2 px-5 py-4" style="border-bottom:1px solid rgba(0,103,127,0.1);">
          <span class="inline-block h-5 w-1.5 rounded-full"
            style="background:linear-gradient(180deg,rgba(0,103,127,1),rgba(0,50,62,0.6));"></span>
          <h2 class="text-sm font-bold uppercase tracking-wide" style="color:rgba(0,50,62,1);">General Information</h2>
        </div>
        <div class="grid grid-cols-1 gap-4 px-5 py-5 sm:grid-cols-3">

          <!-- Shop (read-only) -->
          <div>
            <label class="mb-1 block text-xs font-bold uppercase tracking-wide text-slate-400">Shop</label>
            <input type="text"
              class="w-full cursor-not-allowed rounded-xl border border-slate-200 bg-slate-100/80 px-3 py-2 text-sm text-slate-500"
              value="{{ $shopName }}" readonly disabled>
            <input type="hidden" name="shop_id" value="{{ $shopId }}">
          </div>

          <!-- Date (server-side, read-only) -->
          <div>
            <label class="mb-1 block text-xs font-bold uppercase tracking-wide text-slate-400">Date</label>
            <input type="date"
              class="w-full cursor-not-allowed rounded-xl border border-slate-200 bg-slate-100/80 px-3 py-2 text-sm text-slate-500"
              value="{{ date('Y-m-d') }}" disabled>
            <input type="hidden" name="date" value="{{ date('Y-m-d') }}">
          </div>

          <!-- Accessor -->
          <div>
            <label class="mb-1 block text-xs font-bold uppercase tracking-wide text-slate-400">
              Accessor <span class="text-red-400">*</span>
            </label>
            <input type="text" id="main-accessor" name="accessor_main"
              class="w-full rounded-xl border border-slate-300 bg-white/80 px-3 py-2 text-sm text-slate-700 transition"
              value="{{ old('accessor_main') }}" placeholder="Enter accessor name" required>
          </div>
        </div>
      </div>

      <!-- Action Bar (ATAS) -->
      <div class="mb-4 flex flex-col items-center justify-between gap-3 sm:flex-row">
        <!-- Add Entry -->
        <button type="button" id="add-entry-btn"
          class="inline-flex w-full items-center justify-center gap-2 rounded-xl border-2 px-4 py-2 text-sm font-bold transition sm:w-auto"
          style="border-color:rgba(0,103,127,1); color:rgba(0,103,127,1); background:transparent;"
          onmouseover="this.style.background='rgba(0,103,127,0.08)'" onmouseout="this.style.background='transparent'">
          <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
          </svg>
          Add Entry
        </button>

        {{-- Submit Button ATAS --}}
        <button type="submit"
          class="submit-btn inline-flex w-full items-center justify-center gap-2 rounded-xl px-8 py-3 text-sm font-black text-white shadow-lg transition-all sm:w-auto"
          style="background: linear-gradient(135deg, #065f46 0%, #059669 100%); letter-spacing:0.03em;"
          onmouseover="this.style.background='linear-gradient(135deg,#064e3b 0%,#047857 100%)'"
          onmouseout="this.style.background='linear-gradient(135deg,#065f46 0%,#059669 100%)'">
          <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
          </svg>
          Submit Assessment
        </button>
      </div>

      <!-- Entry Container -->
      <div id="risk-assessment-container"></div>

      <!-- Action Bar (BAWAH) -->
      <div class="mt-4 flex flex-col items-center justify-end gap-3 sm:flex-row">
        {{-- Submit Button BAWAH (utama untuk state JS) --}}
        <button type="submit" id="submit-btn"
          class="submit-btn inline-flex w-full items-center justify-center gap-2 rounded-xl px-8 py-3 text-sm font-black text-white shadow-lg transition-all sm:w-auto"
          style="background: linear-gradient(135deg, #065f46 0%, #059669 100%); letter-spacing:0.03em;"
          onmouseover="this.style.background='linear-gradient(135deg,#064e3b 0%,#047857 100%)'"
          onmouseout="this.style.background='linear-gradient(135deg,#065f46 0%,#059669 100%)'">
          <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
          </svg>
          Submit Assessment
        </button>
      </div>

    </form>
  </div>

  <!-- ══ MAIN SCRIPT ══ -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const container = document.getElementById('risk-assessment-container');
      const addEntryBtn = document.getElementById('add-entry-btn');
      const accessorInput = document.getElementById('main-accessor');
      const form = document.getElementById('risk-form');
      const overlay = document.getElementById('submit-overlay');
      const warningBar = document.getElementById('submit-warning-bar');
      const bottomSubmit = document.getElementById('submit-btn');
      const submitButtons = document.querySelectorAll('button.submit-btn');
      let entryIndex = 1;
      let isSubmitting = false;

      // ─── Risk level style ────────────────────────────────
      function applyRiskStyle(el, value) {
        el.classList.remove('risk-extreme', 'risk-high', 'risk-medium', 'risk-low');
        if (value === 'Extreme') el.classList.add('risk-extreme');
        else if (value === 'High') el.classList.add('risk-high');
        else if (value === 'Medium') el.classList.add('risk-medium');
        else if (value === 'Low') el.classList.add('risk-low');
      }

      // ─── Check apakah entry benar-benar kosong semua ─────
      function isEntryCompletelyEmpty(card) {
        const scope = card.querySelector('select[name="scope_number[]"]')?.value || '';
        const finding = card.querySelector('input[name="finding_problem[]"]')?.value || '';
        const hazard = card.querySelector('input[name="potential_hazards[]"]')?.value || '';
        const severity = card.querySelector('select[name="severity[]"]')?.value || '';
        const prob = card.querySelector('select[name="possibility[]"]')?.value || '';
        const proposal = card.querySelector('input[name="risk_reduction_proposal[]"]')?.value || '';
        return !scope && !finding && !hazard && !severity && !prob && !proposal;
      }

      // ─── Create entry card ───────────────────────────────
      function createEntry(i, preset = null) {
        const div = document.createElement('div');
        div.className = 'entry-card';
        div.dataset.entryId = i;
        div.innerHTML = `
        <div class="entry-card-inner glass-card rounded-2xl shadow-sm border border-white/80 mb-5 overflow-hidden">

          <!-- Card Header -->
          <div class="flex items-center justify-between px-5 py-3.5 relative overflow-hidden"
               style="background:linear-gradient(135deg, rgba(0,50,62,1) 0%, rgba(0,103,127,1) 60%, rgba(0,130,155,1) 100%);">
            <div class="absolute inset-0 opacity-10"
                 style="background-image: repeating-linear-gradient(45deg, rgba(255,255,255,0.2) 0px, rgba(255,255,255,0.2) 1px, transparent 1px, transparent 10px); background-size: 14px 14px;"></div>
            <span class="text-white font-black text-sm flex items-center gap-2 relative z-10">
              <span class="rounded-lg px-2 py-0.5 text-xs font-black"
                    style="background:rgba(255,255,255,0.18); border:1px solid rgba(255,255,255,0.2);">#${i}</span>
              Form Entry ${i}
            </span>
            <button type="button"
                    class="btn-remove-entry inline-flex items-center gap-1 text-white text-xs font-bold px-3 py-1.5 rounded-lg transition relative z-10"
                    style="background:rgba(220,38,38,0.75); border:1px solid rgba(255,255,255,0.2);"
                    onmouseover="this.style.background='rgba(185,28,28,0.95)'"
                    onmouseout="this.style.background='rgba(220,38,38,0.75)'">
              <svg class="w-3 h-3 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="pointer-events-none"
                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
              </svg>
              Remove
            </button>
          </div>

          <!-- Card Body -->
          <div class="px-5 py-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-4">

              <!-- Scope -->
              <div class="lg:col-span-4">
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wide mb-1">
                  Scope <span class="text-red-400 req-star">*</span>
                </label>
                <select name="scope_number[]"
                        class="entry-required w-full rounded-xl border border-slate-300 bg-white/80 px-3 py-2 text-sm text-slate-700 transition">
                  <option value="">-- Select Scope --</option>
                  <option value="1">1 - Man</option>
                  <option value="2">2 - Machine</option>
                  <option value="3">3 - Method</option>
                  <option value="4">4 - Material</option>
                  <option value="5">5 - Environment</option>
                </select>
                <div class="invalid-feedback"></div>
              </div>

              <!-- Finding Problem -->
              <div class="lg:col-span-8">
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wide mb-1">
                  Finding Problem <span class="text-red-400 req-star">*</span>
                </label>
                <input name="finding_problem[]" type="text"
                       class="entry-required w-full rounded-xl border border-slate-300 bg-white/80 px-3 py-2 text-sm text-slate-700 transition"
                       placeholder="Describe the finding...">
                <div class="invalid-feedback"></div>
              </div>

              <!-- Potential Hazard -->
              <div class="lg:col-span-4">
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wide mb-1">
                  Potential Hazard <span class="text-red-400 req-star">*</span>
                </label>
                <input name="potential_hazards[]" type="text"
                       class="entry-required w-full rounded-xl border border-slate-300 bg-white/80 px-3 py-2 text-sm text-slate-700 transition"
                       placeholder="Potential hazard...">
                <div class="invalid-feedback"></div>
              </div>

              <!-- Severity -->
              <div class="lg:col-span-4">
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wide mb-1">
                  Severity <span class="text-red-400 req-star">*</span>
                </label>
                <select name="severity[]"
                        class="entry-required severity w-full rounded-xl border border-slate-300 bg-white/80 px-3 py-2 text-sm text-slate-700 transition">
                  <option value="">-- Select --</option>
                  <option value="1">1 - Insignificant</option>
                  <option value="2">2 - Minor</option>
                  <option value="3">3 - Moderate</option>
                  <option value="4">4 - Major</option>
                  <option value="5">5 - Catastrophic</option>
                </select>
                <div class="invalid-feedback"></div>
              </div>

              <!-- Possibility -->
              <div class="lg:col-span-4">
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wide mb-1">
                  Possibility <span class="text-red-400 req-star">*</span>
                </label>
                <select name="possibility[]"
                        class="entry-required possibility w-full rounded-xl border border-slate-300 bg-white/80 px-3 py-2 text-sm text-slate-700 transition">
                  <option value="">-- Select --</option>
                  <option value="1">1 - Very Rare</option>
                  <option value="2">2 - Unlikely</option>
                  <option value="3">3 - Occasional</option>
                  <option value="4">4 - Frequent</option>
                  <option value="5">5 - Always</option>
                </select>
                <div class="invalid-feedback"></div>
              </div>

              <!-- Score (auto) -->
              <div class="lg:col-span-3">
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wide mb-1">Score</label>
                <input name="score[]" type="number"
                       class="score w-full rounded-xl border border-slate-200 bg-slate-100/80 px-3 py-2 text-sm text-center font-black cursor-not-allowed"
                       style="color:rgba(0,103,127,1);"
                       readonly>
              </div>

              <!-- Risk Level (auto) -->
              <div class="lg:col-span-3">
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wide mb-1">Risk Level</label>
                <input name="risk_level[]" type="text"
                       class="risk-level w-full rounded-xl border border-slate-200 bg-slate-100/80 px-3 py-2 text-sm text-center font-black cursor-not-allowed"
                       readonly>
              </div>

              <!-- Risk Reduction Proposal -->
              <div class="lg:col-span-6">
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wide mb-1">
                  Risk Reduction Measures Proposal <span class="text-red-400 req-star">*</span>
                </label>
                <input name="risk_reduction_proposal[]" type="text"
                       class="entry-required w-full rounded-xl border border-slate-300 bg-white/80 px-3 py-2 text-sm text-slate-700 transition"
                       placeholder="Proposed measures...">
                <div class="invalid-feedback"></div>
              </div>

              <!-- File Upload -->
              <div class="lg:col-span-6">
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wide mb-1">
                  Attach File <span class="text-slate-300 font-normal normal-case">(optional)</span>
                </label>
                <input name="file[]" type="file"
                       class="file-input w-full text-sm text-slate-600 transition">
              </div>

              <div class="lg:col-span-6">
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wide mb-1">
                  Detail Place <span class="text-red-400 req-star">*</span>
                </label>
                <input name="detail_place[]" type="text"
                       class="entry-required w-full rounded-xl border border-slate-300 bg-white/80 px-3 py-2 text-sm text-slate-700 transition"
                       placeholder="Explain exactly places...">
                <div class="invalid-feedback"></div>
              </div>

            </div>

            <!-- Hidden accessor -->
            <input type="hidden" name="accessor[]" class="accessor-hidden" value="${accessorInput.value || ''}">
          </div>
        </div>
      `;

        container.appendChild(div);

        // Risk calculation
        const sev = div.querySelector('.severity');
        const prob = div.querySelector('.possibility');
        const scoreEl = div.querySelector('.score');
        const riskLevel = div.querySelector('.risk-level');

        function updateRisk() {
          const s = parseInt(sev.value) || 0;
          const p = parseInt(prob.value) || 0;
          const result = s * p;
          scoreEl.value = result || '';

          if (result > 16) riskLevel.value = 'Extreme';
          else if (result >= 10) riskLevel.value = 'High';
          else if (result >= 5) riskLevel.value = 'Medium';
          else if (result > 0) riskLevel.value = 'Low';
          else riskLevel.value = '';

          applyRiskStyle(riskLevel, riskLevel.value);
        }

        sev.addEventListener('change', updateRisk);
        prob.addEventListener('change', updateRisk);

        // Remove entry button
        div.querySelector('.btn-remove-entry').addEventListener('click', function() {
          div.remove();
        });

        // Restore preset values (jika ada)
        if (preset) {
          const scopeSel = div.querySelector('select[name="scope_number[]"]');
          const findingInp = div.querySelector('input[name="finding_problem[]"]');
          const hazardInp = div.querySelector('input[name="potential_hazards[]"]');
          const propInp = div.querySelector('input[name="risk_reduction_proposal[]"]');
          const detailPlace = div.querySelector('input[name="detail_place[]"]');
          if (preset.scope != null) scopeSel.value = preset.scope;
          if (preset.finding != null) findingInp.value = preset.finding;
          if (preset.hazard != null) hazardInp.value = preset.hazard;
          if (preset.severity != null) sev.value = preset.severity;
          if (preset.possibility != null) prob.value = preset.possibility;
          if (preset.proposal != null) propInp.value = preset.proposal;
          if (preset.detailPlace != null) detailPlace.value = preset.detailPlace;
          updateRisk();
        }

        // Inline validation on blur
        div.querySelectorAll('.entry-required').forEach(el => {
          el.addEventListener('blur', function() {
            validateField(el);
          });
          el.addEventListener('change', function() {
            if (el.classList.contains('field-invalid')) validateField(el);
          });
          el.addEventListener('input', function() {
            if (el.classList.contains('field-invalid')) validateField(el);
          });
        });
      }

      // ─── Inline field validation ─────────────────────────
      function validateField(el) {
        const val = el.tagName === 'SELECT' ? el.value : (el.value || '').trim();
        const ok = val !== '' && val !== null;
        const fb = el.nextElementSibling;
        if (!ok) {
          el.classList.add('field-invalid');
          if (fb && fb.classList.contains('invalid-feedback')) {
            const label = el.closest('div')?.querySelector('label')?.textContent?.replace('*', '').trim() ||
              'This field';
            fb.textContent = label + ' is required.';
          }
        } else {
          el.classList.remove('field-invalid');
          if (fb && fb.classList.contains('invalid-feedback')) fb.textContent = '';
        }
        return ok;
      }

      // ─── Validate all fields in an entry card ────────────
      function validateEntryCard(card) {
        if (isEntryCompletelyEmpty(card)) {
          card.querySelectorAll('.entry-required').forEach(el => {
            el.classList.remove('field-invalid');
            const fb = el.nextElementSibling;
            if (fb && fb.classList.contains('invalid-feedback')) fb.textContent = '';
          });
          return true;
        }

        let cardValid = true;
        card.querySelectorAll('.entry-required').forEach(el => {
          if (!validateField(el)) cardValid = false;
        });
        return cardValid;
      }

      // ─── Accessor sync ke semua entry ────────────────────
      accessorInput.addEventListener('input', function() {
        document.querySelectorAll('input.accessor-hidden').forEach(inp => {
          inp.value = accessorInput.value;
        });
      });

      // ─── Add entry button ─────────────────────────────────
      addEntryBtn.addEventListener('click', function() {
        createEntry(entryIndex++);
      });

      // ─── beforeunload: HANYA intercept kalau BUKAN submit ─
      window.addEventListener('beforeunload', function(e) {
        if (isSubmitting) return;
      });

      // ─── Submit handler ───────────────────────────────────
      form.addEventListener('submit', function(e) {

        // Validasi accessor
        const accessorVal = (accessorInput.value || '').trim();
        if (!accessorVal) {
          e.preventDefault();
          accessorInput.classList.add('field-invalid');
          accessorInput.scrollIntoView({
            behavior: 'smooth',
            block: 'center'
          });
          accessorInput.focus();
          return;
        }
        accessorInput.classList.remove('field-invalid');

        // Validasi setiap entry
        const allCards = container.querySelectorAll('.entry-card');
        let formValid = true;
        let firstInvalidCard = null;

        allCards.forEach(entryDiv => {
          const card = entryDiv.querySelector('.entry-card-inner');
          if (!card) return;
          const cardValid = validateEntryCard(card);
          if (!cardValid && !firstInvalidCard) {
            firstInvalidCard = card;
            formValid = false;
          }
        });

        if (!formValid) {
          e.preventDefault();
          firstInvalidCard.scrollIntoView({
            behavior: 'smooth',
            block: 'center'
          });
          return;
        }

        // ✅ Semua valid — aktifkan overlay dan disable semua submit button
        isSubmitting = true;
        overlay.classList.add('active');
        warningBar.classList.add('active');

        submitButtons.forEach(btn => {
          btn.disabled = true;
          btn.style.opacity = '0.6';
          btn.style.cursor = 'not-allowed';
        });

        // (opsional) ubah teks di tombol bawah saja
        if (bottomSubmit) {
          bottomSubmit.innerHTML = `
          <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="10" stroke-width="4" class="text-emerald-700" stroke="currentColor" stroke-opacity="0.25"></circle>
            <path d="M4 12a8 8 0 018-8" stroke-width="4" class="text-white" stroke-linecap="round"></path>
          </svg>
          Submitting...
        `;
        }
      });

      // ─── Initial entry ────────────────────────────────────
      createEntry(entryIndex++);
    });
  </script>

  <!-- ══ CSRF keepalive + token refresh ══ -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      function getToken() {
        return document.querySelector('meta[name="csrf-token"]')?.content ||
          document.querySelector('input[name="_token"]')?.value || '';
      }

      function setToken(t) {
        const meta = document.querySelector('meta[name="csrf-token"]');
        if (meta) meta.setAttribute('content', t);
        document.querySelectorAll('input[name="_token"]').forEach(i => i.value = t);
      }

      function pingKeepAlive() {
        const token = getToken();
        if (!token) return;
        fetch("{{ route('keepalive') }}", {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json'
          },
          cache: 'no-store',
          credentials: 'same-origin',
        }).catch(() => {});
      }

      async function refreshCsrf() {
        try {
          const res = await fetch("{{ route('csrf.refresh') }}", {
            cache: 'no-store',
            credentials: 'same-origin',
          });
          if (!res.ok) return;
          const data = await res.json();
          if (data?.token) setToken(data.token);
        } catch (_) {}
      }

      pingKeepAlive();
      refreshCsrf();
      setInterval(pingKeepAlive, 5 * 60 * 1000);
      setInterval(refreshCsrf, 10 * 60 * 1000);
      document.addEventListener('visibilitychange', () => {
        if (document.visibilityState === 'visible') pingKeepAlive();
      });
    });
  </script>

</body>

</html>
