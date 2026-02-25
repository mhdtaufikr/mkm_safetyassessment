<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta http-equiv="X-UA-Compatible" content="ie=edge" />
  @php
    $locationRaw     = urldecode($name ?? '');
    $locationDisplay = ucwords(trim(str_replace(['-', '_'], ' ', $locationRaw)));
    $locationForUrl  = rawurlencode($locationRaw);
  @endphp
  <title>Menu — PT MKM · {{ $locationDisplay }}</title>

  <link rel="icon" href="{{ asset('assets/img/Safety Assessment2.png') }}">
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
              light:   'rgba(0,103,127,0.15)',
              border:  'rgba(0,103,127,0.3)',
            }
          },
          keyframes: {
            fadeUp: {
              '0%':   { opacity: '0', transform: 'translateY(32px)' },
              '100%': { opacity: '1', transform: 'translateY(0)' },
            },
            fadeDown: {
              '0%':   { opacity: '0', transform: 'translateY(-20px)' },
              '100%': { opacity: '1', transform: 'translateY(0)' },
            },
            pulse2: {
              '0%, 100%': { opacity: '1' },
              '50%':      { opacity: '0.6' },
            },
            shimmer: {
              '0%':   { backgroundPosition: '-200% center' },
              '100%': { backgroundPosition: '200% center' },
            },
            float: {
              '0%, 100%': { transform: 'translateY(0px)' },
              '50%':      { transform: 'translateY(-8px)' },
            },
            scaleIn: {
              '0%':   { opacity: '0', transform: 'scale(0.9)' },
              '100%': { opacity: '1', transform: 'scale(1)' },
            },
          },
          animation: {
            'fade-up':    'fadeUp 0.7s ease both',
            'fade-up-2':  'fadeUp 0.7s 0.15s ease both',
            'fade-up-3':  'fadeUp 0.7s 0.3s ease both',
            'fade-up-4':  'fadeUp 0.7s 0.45s ease both',
            'fade-down':  'fadeDown 0.6s ease both',
            'float':      'float 3.5s ease-in-out infinite',
            'scale-in':   'scaleIn 0.5s 0.2s ease both',
            'shimmer':    'shimmer 2.5s linear infinite',
          }
        }
      }
    }
  </script>
  <style>
    /* ══ Background image + overlay ══ */
    body {
      background-image: url("{{ asset('assets/img/About-Company-BG-2.jpg') }}");
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
      background-attachment: fixed;
    }

    /* ══ Dark overlay on bg ══ */
    body::before {
      content: '';
      position: fixed;
      inset: 0;
      background: linear-gradient(
        160deg,
        rgba(0,20,28,0.72) 0%,
        rgba(0,75,93,0.55) 50%,
        rgba(0,30,40,0.78) 100%
      );
      z-index: 0;
      pointer-events: none;
    }

    /* ══ Dot grid texture over overlay ══ */
    body::after {
      content: '';
      position: fixed;
      inset: 0;
      background-image: radial-gradient(circle, rgba(255,255,255,0.06) 1px, transparent 1px);
      background-size: 28px 28px;
      z-index: 0;
      pointer-events: none;
    }

    /* ══ Glass card ══ */
    .glass {
      background: rgba(255,255,255,0.10);
      backdrop-filter: blur(20px);
      -webkit-backdrop-filter: blur(20px);
      border: 1px solid rgba(255,255,255,0.18);
    }

    /* ══ Menu card white ══ */
    .menu-glass {
      background: rgba(255,255,255,0.92);
      backdrop-filter: blur(16px);
      -webkit-backdrop-filter: blur(16px);
      border: 1px solid rgba(255,255,255,0.9);
    }

    /* ══ Shimmer button effect ══ */
    .btn-shimmer {
      background-size: 200% auto;
      transition: background-position 0.4s ease, box-shadow 0.3s, transform 0.2s;
    }
    .btn-shimmer:hover {
      background-position: right center;
      transform: translateY(-2px);
    }
    .btn-shimmer:active {
      transform: translateY(0px);
    }

    /* ══ Divider gradient ══ */
    .divider-mkm {
      height: 2px;
      background: linear-gradient(90deg, transparent, rgba(0,103,127,0.5) 30%, rgba(0,103,127,0.8) 50%, rgba(0,103,127,0.5) 70%, transparent);
      border-radius: 2px;
      margin: 1.5rem 0;
    }

    /* ══ Animated border glow on card hover ══ */
    .menu-btn-card {
      position: relative;
      overflow: hidden;
      transition: transform 0.25s ease, box-shadow 0.25s ease;
    }
    .menu-btn-card::after {
      content: '';
      position: absolute;
      inset: 0;
      border-radius: inherit;
      opacity: 0;
      background: linear-gradient(135deg, rgba(255,255,255,0.15), transparent);
      transition: opacity 0.3s;
    }
    .menu-btn-card:hover::after { opacity: 1; }
    .menu-btn-card:hover {
      transform: translateY(-3px) scale(1.015);
      box-shadow: 0 12px 32px rgba(0,103,127,0.35);
    }

    /* ══ Scroll custom ══ */
    ::-webkit-scrollbar { width: 6px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: rgba(0,103,127,0.4); border-radius: 3px; }
  </style>
</head>

<body class="min-h-screen flex flex-col justify-between relative">

  <!-- ══════════════════════════════════
       TOP — Logo + Company bar
  ══════════════════════════════════ -->
  <div class="relative z-10 pt-8 pb-4 flex flex-col items-center gap-4 animate-fade-down">

    <!-- Logo card -->
    <div class="glass rounded-2xl px-6 py-4 shadow-2xl animate-float">
      <img src="{{ asset('assets/img/Logo Option 3 (1).png') }}"
           alt="PT MKM Logo"
           class="h-16 w-auto object-contain drop-shadow-md">
    </div>

    <!-- Company name pill -->
    <div class="glass rounded-full px-5 py-2 flex items-center gap-2 shadow-lg">
      <span class="w-2 h-2 rounded-full bg-white/60 animate-pulse"></span>
      <span class="text-white/90 text-xs font-semibold tracking-widest uppercase">
        PT. Mitsubishi Krama Yudha Motors &amp; Manufacturing
      </span>
    </div>

  </div>


  <!-- ══════════════════════════════════
       CENTER — Main Menu Card
  ══════════════════════════════════ -->
  <div class="relative z-10 flex-1 flex items-center justify-center px-4 py-6">

    <div class="menu-glass rounded-3xl shadow-2xl w-full max-w-md p-7 animate-scale-in">

      <!-- Portal Title -->
      <div class="text-center mb-1 animate-fade-up">
        <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl mb-3 shadow-lg"
             style="background:linear-gradient(135deg,rgba(0,50,62,1),rgba(0,103,127,1));">
          <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
          </svg>
        </div>
        <h1 class="text-2xl font-black tracking-tight" style="color:rgba(0,50,62,1);">
          Digital Assessment Portal
        </h1>
        <p class="text-xs font-semibold text-slate-500 mt-1 uppercase tracking-widest">
          Workplace Standards &amp; Safety
        </p>
      </div>

      <!-- Location Badge -->
      <div class="flex justify-center mt-3 mb-1 animate-fade-up-2">
        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-xl shadow-sm"
             style="background:rgba(0,103,127,0.08); border:1px solid rgba(0,103,127,0.2);">
          <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"
               style="color:rgba(0,103,127,1);">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
          </svg>
          <span class="text-xs font-bold uppercase tracking-wide" style="color:rgba(0,103,127,1);">
            Location:
          </span>
          <span class="text-sm font-black" style="color:rgba(0,50,62,1);">{{ $locationDisplay }}</span>
        </div>
      </div>

      <div class="divider-mkm animate-fade-up-2"></div>

      <!-- Description -->
      <p class="text-center text-sm text-slate-500 mb-5 animate-fade-up-2">
        Please select one of the assessment menus below to get started.
      </p>

      <!-- Success Alert -->
      @if(session('success'))
      <div id="success-alert"
           class="flex items-center gap-3 px-4 py-3 rounded-xl mb-4 animate-fade-up-2"
           style="background:rgba(0,103,127,0.08); border:1px solid rgba(0,103,127,0.25);">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"
             style="color:rgba(0,103,127,1);">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span class="text-sm font-semibold flex-1" style="color:rgba(0,75,93,1);">{{ session('success') }}</span>
        <button onclick="document.getElementById('success-alert').remove()"
                class="text-lg leading-none font-bold"
                style="color:rgba(0,103,127,0.4);">&times;</button>
      </div>
      @endif

      <!-- ── Menu Buttons ── -->
      <div class="flex flex-col gap-3 animate-fade-up-3">

        <!-- Safety Assessment -->
        <a href="{{ url('form/'.$locationForUrl) }}"
           class="menu-btn-card group flex items-center gap-4 px-5 py-4 rounded-2xl text-white font-bold shadow-lg"
           style="background:linear-gradient(135deg, rgba(0,50,62,1) 0%, rgba(0,103,127,1) 60%, rgba(0,140,170,1) 100%);">
          <div class="shrink-0 w-11 h-11 rounded-xl flex items-center justify-center shadow-inner"
               style="background:rgba(255,255,255,0.18); border:1px solid rgba(255,255,255,0.25);">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
          </div>
          <div class="flex-1 text-left">
            <p class="text-base font-black leading-tight">Safety Assessment</p>
            <p class="text-white/65 text-xs font-medium mt-0.5">Risk identification &amp; hazard evaluation</p>
          </div>
          <svg class="w-5 h-5 text-white/60 group-hover:translate-x-1 transition-transform shrink-0"
               fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
          </svg>
        </a>

        <!-- 5S Audit -->
        <a href="{{ url('form/audit/5s/'.$locationForUrl) }}"
           class="menu-btn-card group flex items-center gap-4 px-5 py-4 rounded-2xl font-bold shadow-md border transition"
           style="background:rgba(255,255,255,0.95); border-color:rgba(0,103,127,0.25); color:rgba(0,50,62,1);">
          <div class="shrink-0 w-11 h-11 rounded-xl flex items-center justify-center shadow-sm"
               style="background:rgba(0,103,127,0.1); border:1px solid rgba(0,103,127,0.2);">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                 style="color:rgba(0,103,127,1);">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2M9 12h6m-3-3v6"/>
            </svg>
          </div>
          <div class="flex-1 text-left">
            <p class="text-base font-black leading-tight" style="color:rgba(0,50,62,1);">5S Audit</p>
            <p class="text-xs font-medium mt-0.5 text-slate-400">Sort · Set · Shine · Standardize · Sustain</p>
          </div>
          <svg class="w-5 h-5 text-slate-300 group-hover:translate-x-1 group-hover:text-mkm transition-all shrink-0"
               fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
          </svg>
        </a>

      </div>

      <!-- Footer note -->
      <p class="text-center text-xs text-slate-400 mt-5 animate-fade-up-4">
        &copy; {{ date('Y') }} PT. Mitsubishi Krama Yudha Motors &amp; Manufacturing
      </p>

    </div>
  </div>


  <!-- ══════════════════════════════════
       BOTTOM — Version / status bar
  ══════════════════════════════════ -->
  <div class="relative z-10 pb-5 flex justify-center animate-fade-up-4">
    <div class="glass rounded-full px-5 py-2 flex items-center gap-3 shadow-lg">
      <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse shadow-sm shadow-emerald-400/50"></span>
      <span class="text-white/70 text-xs font-medium">System Online</span>
      <span class="text-white/30 text-xs">·</span>
      <span class="text-white/50 text-xs">{{ date('d M Y') }}</span>
    </div>
  </div>


  <!-- ══ Auto-dismiss success alert after 3s ══ -->
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const alert = document.getElementById('success-alert');
      if (alert) {
        setTimeout(() => {
          alert.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
          alert.style.opacity = '0';
          alert.style.transform = 'translateY(-8px)';
          setTimeout(() => alert.remove(), 500);
        }, 3000);
      }
    });
  </script>

</body>
</html>
