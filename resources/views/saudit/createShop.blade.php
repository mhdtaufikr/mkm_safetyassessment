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
    body {
      background-color: #eef3f5;
      background-image:
        radial-gradient(circle, rgba(0,103,127,0.12) 1px, transparent 1px),
        repeating-linear-gradient(135deg, transparent, transparent 40px, rgba(0,103,127,0.025) 40px, rgba(0,103,127,0.025) 41px);
      background-size: 24px 24px, 80px 80px;
    }

    /* ── Card accent bar kiri ── */
    .card-accent::before {
      content: '';
      position: absolute;
      top: 0; left: 0;
      width: 4px; height: 100%;
      background: linear-gradient(180deg, rgba(0,103,127,1), rgba(0,50,62,0.4));
      border-radius: 1rem 0 0 1rem;
    }
    .card-accent { position: relative; }

    /* ── Score radio toggle ── */
    .score-radio-group { display: flex; gap: 0.4rem; flex-wrap: wrap; }
    .score-radio-group input[type="radio"] { display: none; }
    .score-radio-group label {
      flex: 1; min-width: 2.4rem; text-align: center;
      padding: 0.4rem 0.2rem;
      border: 1.5px solid rgba(0,103,127,0.4);
      border-radius: 0.5rem;
      font-size: 0.875rem; font-weight: 700;
      color: rgba(0,103,127,1);
      cursor: pointer;
      transition: background 0.15s, color 0.15s, border-color 0.15s, box-shadow 0.15s;
      user-select: none; background: #fff;
    }
    .score-radio-group input[type="radio"]:checked + label {
      background: rgba(0,103,127,1); color: #fff;
      border-color: rgba(0,103,127,1);
      box-shadow: 0 2px 8px rgba(0,103,127,0.35);
    }
    .score-radio-group label:hover { background: rgba(0,103,127,0.1); }

    /* ── Misc ── */
    .file-name-display { font-style: italic; color: #6b7280; font-size: 0.8rem; }
    .border-danger-highlight { outline: 2px solid #dc2626; border-radius: 0.75rem; }

    @keyframes fadeSlideIn {
      from { opacity: 0; transform: translateY(-8px); }
      to   { opacity: 1; transform: translateY(0); }
    }
    .fade-in { animation: fadeSlideIn 0.2s ease; }

    input:focus, select:focus, textarea:focus {
      outline: none;
      box-shadow: 0 0 0 3px rgba(0,103,127,0.2);
      border-color: rgba(0,103,127,1) !important;
    }

    .glass-card {
      background: rgba(255,255,255,0.85);
      backdrop-filter: blur(8px);
      -webkit-backdrop-filter: blur(8px);
    }

    .section-divider {
      height: 2px;
      background: linear-gradient(90deg, rgba(0,103,127,0.6), rgba(0,103,127,0.05) 80%, transparent);
      border-radius: 2px; margin-bottom: 1.25rem;
    }

    .page-watermark {
      position: fixed; bottom: 3rem; left: 50%;
      transform: translateX(-50%);
      font-size: 8rem; font-weight: 900; letter-spacing: 0.3em;
      color: rgba(0,103,127,0.04);
      pointer-events: none; user-select: none;
      z-index: 0; white-space: nowrap;
    }

    /* ── Full Page Submit Overlay ── */
    #submit-overlay {
      display: none;
      position: fixed; inset: 0; z-index: 9999;
      background: rgba(0, 20, 30, 0.82);
      backdrop-filter: blur(6px);
      -webkit-backdrop-filter: blur(6px);
      flex-direction: column;
      align-items: center;
      justify-content: center;
      gap: 1.5rem;
    }
    #submit-overlay.active { display: flex; }

    @keyframes spin {
      to { transform: rotate(360deg); }
    }
    .spinner {
      width: 52px; height: 52px;
      border: 4px solid rgba(255,255,255,0.15);
      border-top-color: rgba(0,200,160,1);
      border-radius: 50%;
      animation: spin 0.8s linear infinite;
    }

    /* ── Warning banner saat submit sedang berjalan ── */
    #submit-warning-bar {
      display: none;
      position: fixed; top: 0; left: 0; right: 0;
      z-index: 10000;
      background: #dc2626;
      color: #fff;
      text-align: center;
      font-size: 0.8rem; font-weight: 700;
      padding: 0.5rem 1rem;
      letter-spacing: 0.02em;
    }
    #submit-warning-bar.active { display: block; }
  </style>
</head>

<body class="min-h-screen antialiased">

  <!-- Watermark -->
  <div class="page-watermark">PT MKM</div>

  <!-- ══ Full Page Submit Overlay ══ -->
  <div id="submit-overlay">
    <div class="spinner"></div>
    <div style="text-align:center;">
      <p class="text-white font-black text-lg tracking-wide">Submitting Audit...</p>
      <p class="text-white/60 text-sm mt-1">Mohon tunggu, jangan tutup atau refresh halaman ini.</p>
    </div>
    <div style="background:rgba(220,38,38,0.15); border:1px solid rgba(220,38,38,0.5); border-radius:0.75rem; padding:0.75rem 1.25rem; max-width:340px; text-align:center;">
      <p class="text-red-300 text-xs font-semibold">
        ⚠️ Menutup atau merefresh halaman sekarang dapat menyebabkan data tidak tersimpan.
      </p>
    </div>
  </div>

  <!-- ══ Warning Bar (beforeunload) ══ -->
  <div id="submit-warning-bar">
    ⚠️ Submit sedang berlangsung — JANGAN tutup atau refresh halaman ini!
  </div>

  <!-- ══ PAGE HEADER ══ -->
  <div style="background: linear-gradient(135deg, rgba(0,30,40,1) 0%, rgba(0,75,93,1) 40%, rgba(0,103,127,1) 75%, rgba(0,140,170,1) 100%);"
       class="shadow-xl relative overflow-hidden">
    <div class="absolute inset-0 opacity-10"
         style="background-image: repeating-linear-gradient(45deg, rgba(255,255,255,0.15) 0px, rgba(255,255,255,0.15) 1px, transparent 1px, transparent 12px); background-size: 17px 17px;"></div>
    <div class="absolute -top-10 -right-10 w-48 h-48 rounded-full opacity-10" style="background:rgba(255,255,255,0.2);"></div>
    <div class="absolute -bottom-6 right-32 w-28 h-28 rounded-full opacity-10" style="background:rgba(255,255,255,0.15);"></div>
    <div class="max-w-5xl mx-auto px-4 py-6 flex items-center gap-4 relative z-10">
      <div class="rounded-2xl p-3 shrink-0 shadow-inner"
           style="background:rgba(255,255,255,0.15); border:1px solid rgba(255,255,255,0.2);">
        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
        </svg>
      </div>
      <div>
        <p class="text-white/60 text-xs uppercase tracking-widest font-semibold mb-0.5">PT Mitsubishi Krama Yudha Motors & Manufacturing</p>
        <h1 class="text-white font-black text-xl leading-tight">5S Audit Checklist And Report</h1>
        <p class="text-white/50 text-xs mt-0.5">Workplace Standards Audit</p>
      </div>
    </div>
  </div>

  <!-- ══ BREADCRUMB ══ -->
  <div style="background:rgba(0,103,127,1);" class="shadow-sm">
    <div class="max-w-5xl mx-auto px-4 py-2 flex items-center gap-2 text-white/70 text-xs">
      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
      </svg>
      <span>Home</span>
      <span class="text-white/30">/</span>
      <span class="text-white font-semibold">5S Audit</span>
    </div>
  </div>

  <!-- ══ MAIN CONTENT ══ -->
  <div class="max-w-5xl mx-auto px-4 py-6 mb-10 relative z-10">

    {{-- Flag server-side submit success --}}
    <script>
      window.__submitSuccess = @json((bool) session('success'));
    </script>

    {{-- Success Alert --}}
    @if (session('success'))
    <div id="success-alert"
         class="flex items-center gap-3 px-4 py-3 rounded-2xl mb-5 shadow-sm"
         style="background:rgba(0,103,127,0.08); border:1px solid rgba(0,103,127,0.3);">
      <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:rgba(0,103,127,1);">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
      </svg>
      <span class="text-sm font-semibold flex-1" style="color:rgba(0,75,93,1);">{{ session('success') }}</span>
      <button onclick="document.getElementById('success-alert').remove()"
              class="text-xl leading-none font-bold" style="color:rgba(0,103,127,0.5);">&times;</button>
    </div>
    @endif

    {{-- Error Alert --}}
    @if ($errors->any())
    <div class="flex items-start gap-3 px-4 py-3 rounded-2xl mb-5 shadow-sm"
         style="background:rgba(220,38,38,0.06); border:1px solid rgba(220,38,38,0.25);">
      <svg class="w-5 h-5 shrink-0 mt-0.5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
      </svg>
      <div>
        <p class="text-sm font-bold text-red-700 mb-1">Terdapat kesalahan, silakan periksa kembali:</p>
        <ul class="text-xs text-red-600 space-y-0.5 list-disc list-inside">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    </div>
    @endif

    {{-- Shop Image --}}
    @if(!empty($shopImage) && file_exists(public_path('storage/shop_images/' . $shopImage)))
    <div class="glass-card rounded-2xl shadow-sm border border-white/80 p-4 mb-5 flex justify-center card-accent">
      <img src="{{ asset('storage/shop_images/' . $shopImage) . '?v=' . \Carbon\Carbon::parse($shopUpdatedAt)->timestamp }}"
           class="max-h-64 w-auto rounded-xl object-contain shadow-sm" alt="{{ $name }}">
    </div>
    @endif

    <!-- Key Beliefs + Scoring Criteria -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
      <div class="md:col-span-2 glass-card rounded-2xl shadow-sm border border-white/80 p-5 card-accent">
        <div class="section-divider"></div>
        <p class="font-black text-sm mb-3 uppercase tracking-wide" style="color:rgba(0,103,127,1);">Key Beliefs</p>
        <ol class="list-decimal list-inside text-sm text-slate-600 space-y-2">
          <li>Everything HAS a place and everything IN its place.</li>
          <li>Nothing on the Floor, except Legs, Wheels, Deck Footstep or Pallets.</li>
          <li>Clean to Inspect, Inspect to Detect, Detect to Correct, and Correct to Perfect.</li>
        </ol>
      </div>
      <div class="glass-card rounded-2xl shadow-sm border border-white/80 overflow-hidden">
        <div class="px-4 py-2.5 flex items-center gap-2" style="background:rgba(0,103,127,1);">
          <svg class="w-3.5 h-3.5 text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
          </svg>
          <p class="text-xs font-bold uppercase tracking-wide text-white">Scoring Criteria</p>
        </div>
        <table class="w-full text-sm">
          <tbody>
            @foreach([1=>'Strongly Disagree',2=>'Disagree',3=>'Neutral',4=>'Agree',5=>'Strongly Agree'] as $val => $label)
            <tr class="border-b last:border-0" style="border-color:rgba(0,103,127,0.08);">
              <td class="px-4 py-2.5 font-black w-10" style="color:rgba(0,103,127,1);">{{ $val }}</td>
              <td class="px-4 py-2.5 text-slate-600">{{ $label }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>

    <!-- ══ FORM ══ -->
    <form id="auditForm"
          action="{{ isset($audit) ? route('saudit.update', $audit->id) : route('saudit.store') }}"
          method="POST"
          enctype="multipart/form-data"
          autocomplete="off">
      @csrf
      @if(isset($audit)) @method('PUT') @endif

      <!-- Audit Details -->
      <div class="glass-card rounded-2xl shadow-sm border border-white/80 mb-6 card-accent">
        <div class="px-5 py-4 flex items-center gap-2" style="border-bottom:1px solid rgba(0,103,127,0.1);">
          <span class="w-1.5 h-5 rounded-full inline-block"
                style="background:linear-gradient(180deg,rgba(0,103,127,1),rgba(0,50,62,0.6));"></span>
          <h2 class="font-bold text-sm uppercase tracking-wide" style="color:rgba(0,50,62,1);">Audit Details</h2>
        </div>
        <div class="px-5 py-5 grid grid-cols-1 sm:grid-cols-3 gap-4">

          <!-- Shop (read-only) -->
          <div>
            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wide mb-1">Shop</label>
            <input type="text"
                   class="w-full rounded-xl border border-slate-200 bg-slate-100/80 px-3 py-2 text-sm text-slate-500 cursor-not-allowed"
                   value="{{ $name }}" readonly disabled>
            <input type="hidden" name="shop" value="{{ $name }}">
          </div>

          <!-- Date (read-only, server-side) -->
          <div>
            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wide mb-1">Date</label>
            @php $dateValue = isset($audit) ? $audit->date : date('Y-m-d'); @endphp
            <input type="date"
                   class="w-full rounded-xl border border-slate-200 bg-slate-100/80 px-3 py-2 text-sm text-slate-500 cursor-not-allowed"
                   value="{{ $dateValue }}" disabled>
            <input type="hidden" name="date" value="{{ $dateValue }}">
          </div>

          <!-- Auditor -->
          <div>
            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wide mb-1">
              Auditor <span class="text-red-400">*</span>
            </label>
            <input type="text"
                   name="auditor"
                   class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm text-slate-700 bg-white/80 transition"
                   value="{{ old('auditor', $audit->auditor ?? '') }}"
                   placeholder="Enter auditor name"
                   required>
          </div>
        </div>
      </div>

      <!-- ══ 5S Categories ══ -->
      @php
        $categories = [
          '1S' => [
            ['Materials or parts',                    'There are no unneeded materials or parts around'],
            ['Machines or other equipment',            'There are no unused machines or other equipment around'],
            ['Tools, Supplies, Parts',                 'There are no items on the Floor (except Legs, Wheels, Deck Footstep or Pallets)'],
            ['Frequency',                              'All items been sorted by everyday use vs. those used occasionally'],
            ['Visual identification',                  'Unnecessary items are clearly marked for disposal (red-tagged or labeled for removal)'],
          ],
          '2S' => [
            ['Item indicators',                        'Everything HAS a place and everything is IN its place'],
            ['Marking of walkways, tools, daisha and pallets', 'Lines or markers are used to clearly indicate walkways, tools, daisha and pallets'],
            ['Tools',                                  'Tools are arranged functionally to facilitate picking them and returning them'],
            ['Storage indicator',                      'Shelves or storage have marked (Kanban)'],
            ['Height and accessibility',               'Frequently used items are stored at optimal height and reach level (ergonomic and efficient access)'],
          ],
          '3S' => [
            ['Floors',                                 'Floors are kept shiny and clean and free of waste, water, dust and/or oil'],
            ['Machine, Tools & Equipment',             'The machines and equipment are wiped clean often; and kept free of waste, dust, and/or oil'],
            ['Habitual cleanliness',                   'There is a cleaning checklist being followed'],
            ['Cleaning Tools',                         'There are cleaning tools present in the area and in good condition'],
            ['Cleaning ownership',                     'Cleaning responsibilities are clearly assigned and visible in the area'],
          ],
          '4S' => [
            ['Maintenance Schedule',                   'The maintenance schedules are clearly displayed, followed, and verified for all machines and equipment'],
            ['Measurement Tools',                      'The measurements tools are calibrated periodically'],
            ['Work Instruction',                       'The standards procedures are written, clear and actively used'],
            ['KPI',                                    'The KPIs related to the standard work are visualized, maintained and monitored'],
            ['Visual management',                      'Standardized visuals (kanban, labels, color codes, etc.) are used consistently across areas'],
          ],
          '5S' => [
            ['Tools and parts',                        'Tools and parts are being stored correctly'],
            ['Stock controls',                         'Stock controls are being adhered to (Kanban)'],
            ['Procedures',                             'Procedures are updated (within last year) and regularly reviewed'],
            ['Check sheet',                            'The standardize check sheet are available and updated in each workstation'],
            ['Area Person in charge',                  'Ownership of areas/zones is clearly displayed'],
          ],
        ];
        $categoryLabels = ['1S'=>'Sort','2S'=>'Set in Order','3S'=>'Shine','4S'=>'Standardize','5S'=>'Sustain'];
        $itemCounter    = 1;
      @endphp

      @foreach($categories as $key => $items)
      <div class="glass-card rounded-2xl shadow-sm border border-white/80 mb-5 overflow-hidden fade-in">

        <!-- Category Header -->
        <div class="px-5 py-3.5 flex items-center justify-between relative overflow-hidden"
             style="background:linear-gradient(135deg, rgba(0,50,62,1) 0%, rgba(0,103,127,1) 60%, rgba(0,130,155,1) 100%);">
          <div class="absolute inset-0 opacity-10"
               style="background-image: repeating-linear-gradient(45deg, rgba(255,255,255,0.2) 0px, rgba(255,255,255,0.2) 1px, transparent 1px, transparent 10px); background-size: 14px 14px;"></div>
          <span class="text-white font-black text-sm flex items-center gap-2 relative z-10">
            <span class="rounded-lg px-2.5 py-0.5 text-xs font-black tracking-wide"
                  style="background:rgba(255,255,255,0.18); border:1px solid rgba(255,255,255,0.2);">{{ $key }}</span>
            {{ $categoryLabels[$key] }}
          </span>
          <span class="text-white/80 text-sm font-semibold relative z-10">
            Subtotal: <span id="subtotal-{{ $key }}" class="font-black text-white">0</span>
          </span>
        </div>

        <!-- Items -->
        <div class="p-4 space-y-3">
          @foreach($items as $item)
          <div class="rounded-xl p-4 transition hover:shadow-md"
               style="background:rgba(255,255,255,0.7); border:1px solid rgba(0,103,127,0.12);"
               data-category="{{ $key }}">

            <!-- Check Item + Description -->
            <div class="flex items-start gap-3 mb-3">
              <span class="shrink-0 w-6 h-6 rounded-full flex items-center justify-center text-xs font-black text-white mt-0.5"
                    style="background:rgba(0,103,127,1);">{{ $loop->iteration }}</span>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 flex-1">
                <div>
                  <p class="text-xs font-bold uppercase tracking-wide mb-0.5 text-slate-400">Check Item</p>
                  <p class="text-sm font-semibold text-slate-700">{{ $item[0] }}</p>
                </div>
                <div>
                  <p class="text-xs font-bold uppercase tracking-wide mb-0.5 text-slate-400">Description</p>
                  <p class="text-sm text-slate-600">{{ $item[1] }}</p>
                </div>
              </div>
            </div>

            <div class="h-px mb-3" style="background:rgba(0,103,127,0.08);"></div>

            <!-- Score -->
            <div class="mb-3">
              <p class="text-xs font-bold uppercase tracking-wide mb-2" style="color:rgba(0,103,127,1);">Score</p>
              <div class="score-radio-group">
                @for ($i = 1; $i <= 5; $i++)
                  <input type="radio"
                         name="items[{{ $itemCounter }}][score]"
                         id="score-{{ $itemCounter }}-{{ $i }}"
                         value="{{ $i }}"
                         data-category="{{ $key }}"
                         {{ isset($audit->scores[$itemCounter]['score']) && $audit->scores[$itemCounter]['score'] == $i ? 'checked' : '' }}
                         autocomplete="off">
                  <label for="score-{{ $itemCounter }}-{{ $i }}">{{ $i }}</label>
                @endfor
              </div>
            </div>

            <!-- Comment -->
            <div class="mb-3">
              <label class="block text-xs font-bold uppercase tracking-wide mb-1 text-slate-400">Comment</label>
              <input type="text"
                     name="items[{{ $itemCounter }}][comment]"
                     class="w-full rounded-xl border border-slate-200 bg-white/80 px-3 py-2 text-sm text-slate-700 transition"
                     value="{{ isset($audit->scores[$itemCounter]['comment']) ? $audit->scores[$itemCounter]['comment'] : '' }}"
                     placeholder="Add a comment...">
            </div>

            <!-- File Upload -->
            <div>
              <label class="block text-xs font-bold uppercase tracking-wide mb-1 text-slate-400">Evidence (Photo)</label>
              <div class="flex items-center gap-3">
                <input type="file"
                       name="items[{{ $itemCounter }}][file]"
                       id="file-upload-{{ $itemCounter }}"
                       accept="image/*"
                       class="hidden file-input-listener">
                <label for="file-upload-{{ $itemCounter }}"
                       class="inline-flex items-center gap-1.5 text-xs font-bold px-3 py-2 rounded-lg border cursor-pointer transition"
                       style="background:rgba(0,103,127,0.07); color:rgba(0,103,127,1); border-color:rgba(0,103,127,0.25);"
                       onmouseover="this.style.background='rgba(0,103,127,0.15)'"
                       onmouseout="this.style.background='rgba(0,103,127,0.07)'">
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                  </svg>
                  Upload Picture
                </label>
                <span class="file-name-display">No file chosen</span>
              </div>
            </div>

            <input type="hidden" name="items[{{ $itemCounter }}][check_item]"   value="{{ $item[0] }}">
            <input type="hidden" name="items[{{ $itemCounter }}][description]"  value="{{ $item[1] }}">
          </div>
          @php $itemCounter++; @endphp
          @endforeach
        </div>
      </div>
      @endforeach

      <!-- Score Summary + Submit -->
      <div class="glass-card rounded-2xl shadow-sm border border-white/80 p-5 mb-5 card-accent">
        <div class="section-divider"></div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 items-end mb-5">
          <div>
            <label class="block text-xs font-bold uppercase tracking-wide mb-1 text-slate-400">Final Score (%)</label>
            <input type="text"
                   name="final_score"
                   id="finalScore"
                   class="w-full rounded-xl border border-slate-200 bg-slate-100/80 px-3 py-2.5 text-lg font-black text-center cursor-not-allowed"
                   style="color:rgba(0,103,127,1);"
                   readonly>
          </div>
          <div class="text-right">
            <p class="text-2xl font-black" style="color:rgba(0,50,62,1);">
              Total: <span id="totalScore" style="color:rgba(0,103,127,1);">0</span>
            </p>
            <p class="text-xs text-slate-400 mt-1">
              Progress: <span id="progressCounter" class="font-bold text-slate-600">0 / 0</span>
            </p>
          </div>
        </div>

        <!-- Overall Comments -->
        <div class="mb-5">
          <label class="block text-xs font-bold uppercase tracking-wide mb-1 text-slate-400">Overall Comments</label>
          <textarea name="comments"
                    class="w-full rounded-xl border border-slate-200 bg-white/80 px-3 py-2 text-sm text-slate-700 transition"
                    rows="3"
                    placeholder="Add overall comments here...">{{ old('comments', $audit->comments ?? '') }}</textarea>
        </div>

        <!-- Submit Button — warna berbeda (hijau emerald) supaya menonjol -->
        <div class="flex justify-end">
          <button type="submit"
                  id="submit-btn"
                  class="inline-flex items-center gap-2 text-white font-black text-sm px-8 py-3.5 rounded-xl shadow-lg transition-all"
                  style="background: linear-gradient(135deg, #065f46 0%, #059669 100%); letter-spacing:0.03em;"
                  onmouseover="this.style.background='linear-gradient(135deg,#064e3b 0%,#047857 100%)'"
                  onmouseout="this.style.background='linear-gradient(135deg,#065f46 0%,#059669 100%)'">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
            </svg>
            {{ isset($audit) ? 'Update Audit' : 'Submit Audit' }}
          </button>
        </div>

      </div>
    </form>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const categories   = ['1S','2S','3S','4S','5S'];
      const form         = document.getElementById('auditForm');
      const overlay      = document.getElementById('submit-overlay');
      const warningBar   = document.getElementById('submit-warning-bar');
      let   isSubmitting = false;

      // ─── Hitung total & subtotal ───────────────────────────
      function calculateTotals() {
        let totalScore   = 0;
        const totalItems = document.querySelectorAll('div[data-category]').length;

        categories.forEach(cat => {
          let sub = 0;
          document.querySelectorAll(`input[type="radio"][data-category="${cat}"]:checked`)
            .forEach(r => { sub += parseInt(r.value); });
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

      // ─── File name display ─────────────────────────────────
      document.querySelectorAll('.file-input-listener').forEach(input => {
        input.addEventListener('change', function (e) {
          const span = e.target.closest('div').querySelector('.file-name-display');
          if (span) span.textContent = e.target.files.length ? e.target.files[0].name : 'No file chosen';
        });
      });

      // ─── beforeunload: hanya warning kalau USER yang mau pergi
      //     BUKAN saat form submit (isSubmitting = true) ─────────
      window.addEventListener('beforeunload', function (e) {
        if (isSubmitting) return; // submit sedang jalan → JANGAN intercept, biarkan redirect
        // (hapus 2 baris di bawah kalau tidak mau peringatan apapun)
        // e.preventDefault();
        // e.returnValue = '';
      });

      // ─── Submit guard ──────────────────────────────────────
      form.addEventListener('submit', function (e) {
        const allItems   = document.querySelectorAll('div[data-category]');
        const totalItems = allItems.length;
        const filled     = document.querySelectorAll('input[type="radio"]:checked').length;

        // Validasi: semua score harus diisi
        if (filled < totalItems) {
          e.preventDefault();

          // Hapus highlight lama
          document.querySelectorAll('.border-danger-highlight').forEach(el => {
            el.classList.remove('border-danger-highlight');
          });
          document.querySelectorAll('.score-missing-msg').forEach(el => el.remove());

          // Highlight item pertama yang belum diisi
          for (let item of allItems) {
            const checked = Array.from(item.querySelectorAll('input[type="radio"]')).some(r => r.checked);
            if (!checked) {
              item.classList.add('border-danger-highlight');
              item.scrollIntoView({ behavior: 'smooth', block: 'center' });
              const msg = document.createElement('p');
              msg.className = 'score-missing-msg text-xs text-red-500 font-semibold mt-2';
              msg.textContent = '⚠️ Score belum dipilih';
              item.appendChild(msg);
              break;
            }
          }
          return; // stop — jangan submit
        }

        // ✅ Semua valid — set flag DULU baru tampil overlay
        isSubmitting = true;

        overlay.classList.add('active');
        warningBar.classList.add('active');

        const btn = document.getElementById('submit-btn');
        btn.disabled      = true;
        btn.style.opacity = '0.6';
        btn.style.cursor  = 'not-allowed';

        // Form submit jalan secara normal (tidak di-preventDefault)
      });

      // ─── Hapus highlight saat user pilih score ─────────────
      document.querySelectorAll('input[type="radio"]').forEach(r => {
        r.addEventListener('change', function () {
          const item = this.closest('div[data-category]');
          if (item) {
            item.classList.remove('border-danger-highlight');
            const msg = item.querySelector('.score-missing-msg');
            if (msg) msg.remove();
          }
        });
      });

      // ─── Init ─────────────────────────────────────────────
      calculateTotals();
    });
    </script>

  <!-- ══ SCRIPT — CSRF keepalive + token refresh ══ -->
  <script>
  document.addEventListener('DOMContentLoaded', function () {
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
        headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
        cache: 'no-store', credentials: 'same-origin',
      }).catch(() => {});
    }

    async function refreshCsrf() {
      try {
        const res = await fetch("{{ route('csrf.refresh') }}", {
          cache: 'no-store', credentials: 'same-origin',
        });
        if (!res.ok) return;
        const data = await res.json();
        if (data?.token) setToken(data.token);
      } catch (_) {}
    }

    pingKeepAlive();
    refreshCsrf();
    setInterval(pingKeepAlive, 5 * 60 * 1000);
    setInterval(refreshCsrf,   10 * 60 * 1000);

    document.addEventListener('visibilitychange', () => {
      if (document.visibilityState === 'visible') pingKeepAlive();
    });
  });
  </script>

</body>
</html>
