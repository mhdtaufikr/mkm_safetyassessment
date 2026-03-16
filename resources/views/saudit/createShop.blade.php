<!DOCTYPE html>
<html lang="en">

<head>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta http-equiv="X-UA-Compatible" content="ie=edge" />
  <title>5S Audit Form</title>
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

    .card-accent {
      position: relative;
    }

    .score-radio-group {
      display: flex;
      gap: 0.4rem;
      flex-wrap: wrap;
    }

    .score-radio-group input[type="radio"] {
      display: none;
    }

    .score-radio-group label {
      flex: 1;
      min-width: 2.4rem;
      text-align: center;
      padding: 0.4rem 0.2rem;
      border: 1.5px solid rgba(0, 103, 127, 0.4);
      border-radius: 0.5rem;
      font-size: 0.875rem;
      font-weight: 700;
      color: rgba(0, 103, 127, 1);
      cursor: pointer;
      transition: background 0.15s, color 0.15s, border-color 0.15s, box-shadow 0.15s;
      user-select: none;
      background: #fff;
    }

    .score-radio-group input[type="radio"]:checked+label {
      background: rgba(0, 103, 127, 1);
      color: #fff;
      border-color: rgba(0, 103, 127, 1);
      box-shadow: 0 2px 8px rgba(0, 103, 127, 0.35);
    }

    .score-radio-group label:hover {
      background: rgba(0, 103, 127, 0.1);
    }

    .file-name-display {
      font-style: italic;
      color: #6b7280;
      font-size: 0.8rem;
    }

    .border-danger-highlight {
      outline: 2px solid #dc2626;
      border-radius: 0.75rem;
    }

    @keyframes fadeSlideIn {
      from {
        opacity: 0;
        transform: translateY(-8px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .fade-in {
      animation: fadeSlideIn 0.2s ease;
    }

    input:focus,
    select:focus,
    textarea:focus {
      outline: none;
      box-shadow: 0 0 0 3px rgba(0, 103, 127, 0.2);
      border-color: rgba(0, 103, 127, 1) !important;
    }

    .glass-card {
      background: rgba(255, 255, 255, 0.85);
      backdrop-filter: blur(8px);
      -webkit-backdrop-filter: blur(8px);
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

  <div id="submit-overlay">
    <div class="spinner"></div>
    <div style="text-align:center;">
      <p class="text-lg font-black tracking-wide text-white">Submitting Audit...</p>
      <p class="mt-1 text-sm text-white/60">Mohon tunggu, jangan tutup atau refresh halaman ini.</p>
    </div>
    <div
      style="background:rgba(220,38,38,0.15); border:1px solid rgba(220,38,38,0.5); border-radius:0.75rem; padding:0.75rem 1.25rem; max-width:340px; text-align:center;">
      <p class="text-xs font-semibold text-red-300">
        ⚠️ Menutup atau merefresh halaman sekarang dapat menyebabkan data tidak tersimpan.
      </p>
    </div>
  </div>

  <div id="submit-warning-bar">
    ⚠️ Submit sedang berlangsung — JANGAN tutup atau refresh halaman ini!
  </div>

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
    <div class="relative z-10 mx-auto flex max-w-5xl items-center gap-4 px-4 py-6">
      <div class="shrink-0 rounded-2xl p-3 shadow-inner"
        style="background:rgba(255,255,255,0.15); border:1px solid rgba(255,255,255,0.2);">
        <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
        </svg>
      </div>
      <div>
        <p class="mb-0.5 text-xs font-semibold uppercase tracking-widest text-white/60">PT Mitsubishi Krama Yudha Motors
          & Manufacturing</p>
        <h1 class="text-xl font-black leading-tight text-white">5S Audit Checklist And Report</h1>
        <p class="mt-0.5 text-xs text-white/50">Workplace Standards Audit</p>
      </div>
    </div>
  </div>

  <div style="background:rgba(0,103,127,1);" class="shadow-sm">
    <div class="mx-auto flex max-w-5xl items-center gap-2 px-4 py-2 text-xs text-white/70">
      <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
      </svg>
      <span>Home</span>
      <span class="text-white/30">/</span>
      <span class="font-semibold text-white">5S Audit</span>
    </div>
  </div>

  <div class="relative z-10 mx-auto mb-10 max-w-5xl px-4 py-6">

    <script>
      window.__submitSuccess = @json((bool) session('success'));
    </script>

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

    @if (!empty($shopImage) && file_exists(public_path('storage/shop_images/' . $shopImage)))
      <div class="glass-card card-accent mb-5 flex justify-center rounded-2xl border border-white/80 p-4 shadow-sm">
        <img
          src="{{ asset('storage/shop_images/' . $shopImage) . '?v=' . \Carbon\Carbon::parse($shopUpdatedAt)->timestamp }}"
          class="max-h-64 w-auto rounded-xl object-contain shadow-sm" alt="{{ $name }}">
      </div>
    @endif

    <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-3">
      <div class="glass-card card-accent rounded-2xl border border-white/80 p-5 shadow-sm md:col-span-2">
        <div class="section-divider"></div>
        <p class="mb-3 text-sm font-black uppercase tracking-wide" style="color:rgba(0,103,127,1);">Key Beliefs</p>
        <ol class="list-inside list-decimal space-y-2 text-sm text-slate-600">
          <li>Everything HAS a place and everything IN its place.</li>
          <li>Nothing on the Floor, except Legs, Wheels, Deck Footstep or Pallets.</li>
          <li>Clean to Inspect, Inspect to Detect, Detect to Correct, and Correct to Perfect.</li>
        </ol>
      </div>
      <div class="glass-card overflow-hidden rounded-2xl border border-white/80 shadow-sm">
        <div class="flex items-center gap-2 px-4 py-2.5" style="background:rgba(0,103,127,1);">
          <svg class="h-3.5 w-3.5 text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
          </svg>
          <p class="text-xs font-bold uppercase tracking-wide text-white">Scoring Criteria</p>
        </div>
        <table class="w-full text-sm">
          <tbody>
            @foreach ([1 => 'Strongly Disagree', 2 => 'Disagree', 3 => 'Neutral', 4 => 'Agree', 5 => 'Strongly Agree'] as $val => $label)
              <tr class="border-b last:border-0" style="border-color:rgba(0,103,127,0.08);">
                <td class="w-10 px-4 py-2.5 font-black" style="color:rgba(0,103,127,1);">{{ $val }}</td>
                <td class="px-4 py-2.5 text-slate-600">{{ $label }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>

    <form id="auditForm" action="{{ isset($audit) ? route('saudit.update', $audit->id) : route('saudit.store') }}"
      method="POST" enctype="multipart/form-data" autocomplete="off">
      @csrf
      @if (isset($audit))
        @method('PUT')
      @endif

      <div class="glass-card card-accent mb-6 rounded-2xl border border-white/80 shadow-sm">
        <div class="flex items-center gap-2 px-5 py-4" style="border-bottom:1px solid rgba(0,103,127,0.1);">
          <span class="inline-block h-5 w-1.5 rounded-full"
            style="background:linear-gradient(180deg,rgba(0,103,127,1),rgba(0,50,62,0.6));"></span>
          <h2 class="text-sm font-bold uppercase tracking-wide" style="color:rgba(0,50,62,1);">Audit Details</h2>
        </div>
        <div class="grid grid-cols-1 gap-4 px-5 py-5 sm:grid-cols-3">

          <div>
            <label class="mb-1 block text-xs font-bold uppercase tracking-wide text-slate-400">Shop</label>
            <input type="text"
              class="w-full cursor-not-allowed rounded-xl border border-slate-200 bg-slate-100/80 px-3 py-2 text-sm text-slate-500"
              value="{{ old('shop', $name) }}" readonly disabled>
            <input type="hidden" name="shop" value="{{ old('shop', $name) }}">
          </div>

          <div>
            <label class="mb-1 block text-xs font-bold uppercase tracking-wide text-slate-400">Date</label>
            @php $dateValue = old('date', isset($audit) ? $audit->date : date('Y-m-d')); @endphp
            <input type="date"
              class="w-full cursor-not-allowed rounded-xl border border-slate-200 bg-slate-100/80 px-3 py-2 text-sm text-slate-500"
              value="{{ $dateValue }}" disabled>
            <input type="hidden" name="date" value="{{ $dateValue }}">
          </div>

          <div>
            <label class="mb-1 block text-xs font-bold uppercase tracking-wide text-slate-400">
              Auditor <span class="text-red-400">*</span>
            </label>
            <input type="text" name="auditor"
              class="w-full rounded-xl border border-slate-300 bg-white/80 px-3 py-2 text-sm text-slate-700 transition"
              value="{{ old('auditor', $audit->auditor ?? '') }}" placeholder="Enter auditor name" required>
            @error('auditor')
              <p class="mt-2 text-xs font-semibold text-red-500">{{ $message }}</p>
            @enderror
          </div>
        </div>
      </div>

      @php
        $categories = [
            '1S' => [
                ['Materials or parts', 'There are no unneeded materials or parts around'],
                ['Machines or other equipment', 'There are no unused machines or other equipment around'],
                [
                    'Tools, Supplies, Parts',
                    'There are no items on the Floor (except Legs, Wheels, Deck Footstep or Pallets)',
                ],
                ['Frequency', 'All items been sorted by everyday use vs. those used occasionally'],
                [
                    'Visual identification',
                    'Unnecessary items are clearly marked for disposal (red-tagged or labeled for removal)',
                ],
            ],
            '2S' => [
                ['Item indicators', 'Everything HAS a place and everything is IN its place'],
                [
                    'Marking of walkways, tools, daisha and pallets',
                    'Lines or markers are used to clearly indicate walkways, tools, daisha and pallets',
                ],
                ['Tools', 'Tools are arranged functionally to facilitate picking them and returning them'],
                ['Storage indicator', 'Shelves or storage have marked (Kanban)'],
                [
                    'Height and accessibility',
                    'Frequently used items are stored at optimal height and reach level (ergonomic and efficient access)',
                ],
            ],
            '3S' => [
                ['Floors', 'Floors are kept shiny and clean and free of waste, water, dust and/or oil'],
                [
                    'Machine, Tools & Equipment',
                    'The machines and equipment are wiped clean often; and kept free of waste, dust, and/or oil',
                ],
                ['Habitual cleanliness', 'There is a cleaning checklist being followed'],
                ['Cleaning Tools', 'There are cleaning tools present in the area and in good condition'],
                ['Cleaning ownership', 'Cleaning responsibilities are clearly assigned and visible in the area'],
            ],
            '4S' => [
                [
                    'Maintenance Schedule',
                    'The maintenance schedules are clearly displayed, followed, and verified for all machines and equipment',
                ],
                ['Measurement Tools', 'The measurements tools are calibrated periodically'],
                ['Work Instruction', 'The standards procedures are written, clear and actively used'],
                ['KPI', 'The KPIs related to the standard work are visualized, maintained and monitored'],
                [
                    'Visual management',
                    'Standardized visuals (kanban, labels, color codes, etc.) are used consistently across areas',
                ],
            ],
            '5S' => [
                ['Tools and parts', 'Tools and parts are being stored correctly'],
                ['Stock controls', 'Stock controls are being adhered to (Kanban)'],
                ['Procedures', 'Procedures are updated (within last year) and regularly reviewed'],
                ['Check sheet', 'The standardize check sheet are available and updated in each workstation'],
                ['Area Person in charge', 'Ownership of areas/zones is clearly displayed'],
            ],
        ];
        $categoryLabels = [
            '1S' => 'Sort',
            '2S' => 'Set in Order',
            '3S' => 'Shine',
            '4S' => 'Standardize',
            '5S' => 'Sustain',
        ];
        $itemCounter = 1;
      @endphp

      @foreach ($categories as $key => $items)
        <div class="glass-card fade-in mb-5 overflow-hidden rounded-2xl border border-white/80 shadow-sm">
          <div class="relative flex items-center justify-between overflow-hidden px-5 py-3.5"
            style="background:linear-gradient(135deg, rgba(0,50,62,1) 0%, rgba(0,103,127,1) 60%, rgba(0,130,155,1) 100%);">
            <div class="absolute inset-0 opacity-10"
              style="background-image: repeating-linear-gradient(45deg, rgba(255,255,255,0.2) 0px, rgba(255,255,255,0.2) 1px, transparent 1px, transparent 10px); background-size: 14px 14px;">
            </div>
            <span class="relative z-10 flex items-center gap-2 text-sm font-black text-white">
              <span class="rounded-lg px-2.5 py-0.5 text-xs font-black tracking-wide"
                style="background:rgba(255,255,255,0.18); border:1px solid rgba(255,255,255,0.2);">{{ $key }}</span>
              {{ $categoryLabels[$key] }}
            </span>
            <span class="relative z-10 text-sm font-semibold text-white/80">
              Subtotal: <span id="subtotal-{{ $key }}" class="font-black text-white">0</span>
            </span>
          </div>

          <div class="space-y-3 p-4">
            @foreach ($items as $item)
              @php
                $currentIndex = $itemCounter;
                $oldScore = old("items.$currentIndex.score");
                $savedScore = $audit->scores[$currentIndex]['score'] ?? null;
                $selectedScore = $oldScore ?? $savedScore;

                $oldComment = old("items.$currentIndex.comment");
                $savedComment = $audit->scores[$currentIndex]['comment'] ?? '';
                $selectedComment = $oldComment ?? $savedComment;

                $existingFile = $audit->scores[$currentIndex]['file'] ?? null;
              @endphp

              <div class="rounded-xl p-4 transition hover:shadow-md"
                style="background:rgba(255,255,255,0.7); border:1px solid rgba(0,103,127,0.12);"
                data-category="{{ $key }}">

                <div class="mb-3 flex items-start gap-3">
                  <span
                    class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full text-xs font-black text-white"
                    style="background:rgba(0,103,127,1);">{{ $loop->iteration }}</span>
                  <div class="grid flex-1 grid-cols-1 gap-3 sm:grid-cols-2">
                    <div>
                      <p class="mb-0.5 text-xs font-bold uppercase tracking-wide text-slate-400">Check Item</p>
                      <p class="text-sm font-semibold text-slate-700">{{ $item[0] }}</p>
                    </div>
                    <div>
                      <p class="mb-0.5 text-xs font-bold uppercase tracking-wide text-slate-400">Description</p>
                      <p class="text-sm text-slate-600">{{ $item[1] }}</p>
                    </div>
                  </div>
                </div>

                <div class="mb-3 h-px" style="background:rgba(0,103,127,0.08);"></div>

                <div class="mb-3">
                  <p class="mb-2 text-xs font-bold uppercase tracking-wide" style="color:rgba(0,103,127,1);">Score</p>
                  <div class="score-radio-group">
                    @for ($i = 1; $i <= 5; $i++)
                      <input type="radio" name="items[{{ $currentIndex }}][score]"
                        id="score-{{ $currentIndex }}-{{ $i }}" value="{{ $i }}"
                        data-category="{{ $key }}"
                        {{ (string) $selectedScore === (string) $i ? 'checked' : '' }} autocomplete="off">
                      <label for="score-{{ $currentIndex }}-{{ $i }}">{{ $i }}</label>
                    @endfor
                  </div>
                  @error("items.$currentIndex.score")
                    <p class="mt-2 text-xs font-semibold text-red-500">{{ $message }}</p>
                  @enderror
                </div>

                <div class="mb-3">
                  <label class="mb-1 block text-xs font-bold uppercase tracking-wide text-slate-400">Comment</label>
                  <input type="text" name="items[{{ $currentIndex }}][comment]"
                    class="w-full rounded-xl border border-slate-200 bg-white/80 px-3 py-2 text-sm text-slate-700 transition"
                    value="{{ $selectedComment }}" placeholder="Add a comment...">
                  @error("items.$currentIndex.comment")
                    <p class="mt-2 text-xs font-semibold text-red-500">{{ $message }}</p>
                  @enderror
                </div>

                @if (!empty($existingFile) && Storage::disk('public')->exists($existingFile))
                  <div class="mb-3">
                    <p class="mb-2 text-xs font-bold uppercase tracking-wide text-slate-400">Current Evidence</p>
                    <div class="flex items-start gap-3">
                      <a href="{{ asset('storage/' . $existingFile) }}" target="_blank">
                        <img src="{{ asset('storage/' . $existingFile) }}" alt="Evidence"
                          class="h-20 w-20 rounded-xl border border-slate-200 object-cover shadow-sm">
                      </a>
                      <div class="text-xs text-slate-500">
                        <p class="font-semibold text-slate-600">{{ basename($existingFile) }}</p>
                        <p>Foto lama tetap dipakai jika tidak upload foto baru.</p>
                      </div>
                    </div>
                  </div>
                @endif

                <div>
                  <label class="mb-1 block text-xs font-bold uppercase tracking-wide text-slate-400">Evidence
                    (Photo)</label>
                  <div class="flex items-center gap-3">
                    <input type="file" name="items[{{ $currentIndex }}][file]"
                      id="file-upload-{{ $currentIndex }}" accept="image/*" class="file-input-listener hidden">
                    <label for="file-upload-{{ $currentIndex }}"
                      class="inline-flex cursor-pointer items-center gap-1.5 rounded-lg border px-3 py-2 text-xs font-bold transition"
                      style="background:rgba(0,103,127,0.07); color:rgba(0,103,127,1); border-color:rgba(0,103,127,0.25);"
                      onmouseover="this.style.background='rgba(0,103,127,0.15)'"
                      onmouseout="this.style.background='rgba(0,103,127,0.07)'">
                      <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                      </svg>
                      Upload Picture
                    </label>
                    <span class="file-name-display"
                      data-old-name="{{ !empty($existingFile) ? basename($existingFile) : 'No file chosen' }}">No file
                      chosen</span>
                  </div>

                  <p class="mt-1 text-[11px] text-slate-400">
                    Jika validation error, file yang baru dipilih harus dipilih ulang.
                  </p>

                  @error("items.$currentIndex.file")
                    <p class="mt-2 text-xs font-semibold text-red-500">{{ $message }}</p>
                  @enderror
                </div>

                <input type="hidden" name="items[{{ $currentIndex }}][check_item]" value="{{ $item[0] }}">
                <input type="hidden" name="items[{{ $currentIndex }}][description]" value="{{ $item[1] }}">
              </div>
              @php $itemCounter++; @endphp
            @endforeach
          </div>
        </div>
      @endforeach

      <div class="glass-card card-accent mb-5 rounded-2xl border border-white/80 p-5 shadow-sm">
        <div class="section-divider"></div>

        <div class="mb-5 grid grid-cols-1 items-end gap-4 sm:grid-cols-2">
          <div>
            <label class="mb-1 block text-xs font-bold uppercase tracking-wide text-slate-400">Final Score (%)</label>
            <input type="text" name="final_score" id="finalScore"
              class="w-full cursor-not-allowed rounded-xl border border-slate-200 bg-slate-100/80 px-3 py-2.5 text-center text-lg font-black"
              style="color:rgba(0,103,127,1);" readonly>
          </div>
          <div class="text-right">
            <p class="text-2xl font-black" style="color:rgba(0,50,62,1);">
              Total: <span id="totalScore" style="color:rgba(0,103,127,1);">0</span>
            </p>
            <p class="mt-1 text-xs text-slate-400">
              Progress: <span id="progressCounter" class="font-bold text-slate-600">0 / 0</span>
            </p>
          </div>
        </div>

        <div class="mb-5">
          <label class="mb-1 block text-xs font-bold uppercase tracking-wide text-slate-400">Overall Comments</label>
          <textarea name="comments"
            class="w-full rounded-xl border border-slate-200 bg-white/80 px-3 py-2 text-sm text-slate-700 transition"
            rows="3" placeholder="Add overall comments here...">{{ old('comments', $audit->comments ?? '') }}</textarea>
          @error('comments')
            <p class="mt-2 text-xs font-semibold text-red-500">{{ $message }}</p>
          @enderror
        </div>

        <div class="flex justify-end">
          <button type="submit" id="submit-btn"
            class="inline-flex items-center gap-2 rounded-xl px-8 py-3.5 text-sm font-black text-white shadow-lg transition-all"
            style="background: linear-gradient(135deg, #065f46 0%, #059669 100%); letter-spacing:0.03em;"
            onmouseover="this.style.background='linear-gradient(135deg,#064e3b 0%,#047857 100%)'"
            onmouseout="this.style.background='linear-gradient(135deg,#065f46 0%,#059669 100%)'">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
            </svg>
            {{ isset($audit) ? 'Update Audit' : 'Submit Audit' }}
          </button>
        </div>
      </div>
    </form>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const categories = ['1S', '2S', '3S', '4S', '5S'];
      const form = document.getElementById('auditForm');
      const overlay = document.getElementById('submit-overlay');
      const warningBar = document.getElementById('submit-warning-bar');
      let isSubmitting = false;

      function calculateTotals() {
        let totalScore = 0;
        const totalItems = document.querySelectorAll('div[data-category]').length;

        categories.forEach(cat => {
          let sub = 0;
          document.querySelectorAll(`input[type="radio"][data-category="${cat}"]:checked`)
            .forEach(r => {
              sub += parseInt(r.value);
            });
          totalScore += sub;
          const el = document.getElementById('subtotal-' + cat);
          if (el) el.innerText = sub;
        });

        document.getElementById('totalScore').innerText = totalScore;
        const pct = totalItems > 0 ? ((totalScore / (totalItems * 5)) * 100).toFixed(2) : '0.00';
        document.getElementById('finalScore').value = pct;

        const filled = document.querySelectorAll('input[type="radio"]:checked').length;
        document.getElementById('progressCounter').innerText = `${filled} / ${totalItems}`;
      }

      document.querySelectorAll('input[type="radio"]').forEach(r => {
        r.addEventListener('change', calculateTotals);
      });

      document.querySelectorAll('.file-input-listener').forEach(input => {
        const wrapper = input.closest('div');
        const span = wrapper ? wrapper.querySelector('.file-name-display') : null;

        if (span) {
          const oldName = span.dataset.oldName || 'No file chosen';
          span.textContent = oldName;
        }

        input.addEventListener('change', function(e) {
          const span = e.target.closest('div').querySelector('.file-name-display');
          if (span) {
            span.textContent = e.target.files.length ?
              e.target.files[0].name :
              (span.dataset.oldName || 'No file chosen');
          }
        });
      });

      window.addEventListener('beforeunload', function(e) {
        if (isSubmitting) return;
      });

      form.addEventListener('submit', function(e) {
        const allItems = document.querySelectorAll('div[data-category]');
        const totalItems = allItems.length;
        const filled = document.querySelectorAll('input[type="radio"]:checked').length;

        if (filled < totalItems) {
          e.preventDefault();

          document.querySelectorAll('.border-danger-highlight').forEach(el => {
            el.classList.remove('border-danger-highlight');
          });
          document.querySelectorAll('.score-missing-msg').forEach(el => el.remove());

          for (let item of allItems) {
            const checked = Array.from(item.querySelectorAll('input[type="radio"]')).some(r => r.checked);
            if (!checked) {
              item.classList.add('border-danger-highlight');
              item.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
              });
              const msg = document.createElement('p');
              msg.className = 'score-missing-msg text-xs text-red-500 font-semibold mt-2';
              msg.textContent = '⚠️ Score belum dipilih';
              item.appendChild(msg);
              break;
            }
          }
          return;
        }

        isSubmitting = true;
        overlay.classList.add('active');
        warningBar.classList.add('active');

        const btn = document.getElementById('submit-btn');
        btn.disabled = true;
        btn.style.opacity = '0.6';
        btn.style.cursor = 'not-allowed';
      });

      document.querySelectorAll('input[type="radio"]').forEach(r => {
        r.addEventListener('change', function() {
          const item = this.closest('div[data-category]');
          if (item) {
            item.classList.remove('border-danger-highlight');
            const msg = item.querySelector('.score-missing-msg');
            if (msg) msg.remove();
          }
        });
      });

      calculateTotals();
    });
  </script>

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
