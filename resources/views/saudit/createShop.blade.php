<!DOCTYPE html>
<html lang="en">
<head>
    <style>
        .score-label {
            display: inline-block;
            width: 20px;
            text-align: center;
        }
        .file-name-display {
            font-style: italic;
            color: #6c757d; /* Bootstrap's secondary text color */
        }
    </style>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>5S Audit Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>

<div class="container mt-4 mb-5">
    <h4 class="text-center fw-bold mb-4">5S Audit Checklist And Report</h4>

<main>

@if(!empty($shopImage) && file_exists(public_path('storage/shop_images/' . $shopImage)))
    <div class="text-center mb-4">
        <img src="{{ asset('storage/shop_images/' . $shopImage) . '?v=' . \Carbon\Carbon::parse($shopUpdatedAt)->timestamp }}"
             class="img-fluid rounded shadow" style="max-height: 270px;" alt="{{ $name }}">
    </div>
@endif


<div class="container my-4">
    <div class="row">
        <div class="col-md-8 d-flex">
            <div class="card w-100 h-100">
                <div class="card-body">
                    <h5><strong>KEY BELIEFS:</strong></h5>
                    <ol class="mb-0">
                        <li>Everything HAS a place and everything IN its place.</li>
                        <li>Nothing on the Floor, except Legs, Wheels, Deck Footstep or Pallets.</li>
                        <li>Clean to Inspect, Inspect to Detect, Detect to Correct, and Correct to Perfect.</li>
                    </ol>
                </div>
            </div>
        </div>
        <div class="col-md-4 d-flex">
            <div class="card w-100 h-100">
                <div class="card-body p-0">
                    <table class="table table-bordered mb-0">
                        <thead class="table-secondary">
                            <tr>
                                <th>Item</th>
                                <th>Criteria</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td>1</td><td>Strongly disagree</td></tr>
                            <tr><td>2</td><td>Disagree</td></tr>
                            <tr><td>3</td><td>Neutral</td></tr>
                            <tr><td>4</td><td>Agree </td></tr>
                            <tr><td>5</td><td>Strongly Agree</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Form Mulai --}}
<div class="container-xl px-4 mt-4">
    <form id="auditForm" action="{{ isset($audit) ? route('saudit.update', $audit->id) : route('saudit.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @if(isset($audit)) @method('PUT') @endif

        <div class="card shadow-lg mb-4">
            <div class="card-header bg-primary text-white fw-bold">
                Audit Details
            </div>
            <div class="card-body">

                {{-- Header --}}
                <div class="row g-3 mb-4">
                    <div class="col-md-4 col-12">
                        <label class="form-label">Shop</label>
                        <input class="form-control" type="text" value="{{ $name }}" readonly disabled>
                        <input type="hidden" name="shop" value="{{ $name }}">
                    </div>

                    <div class="col-md-4 col-12">
                        <label class="form-label">Date</label>
                        @php
                            $today = date('Y-m-d');
                            $dateValue = old('date', $audit->date ?? $today);
                        @endphp
                        <input type="date" class="form-control" value="{{ $dateValue }}" disabled>
                        <input type="hidden" name="date" value="{{ $dateValue }}">
                    </div>

                    <div class="col-md-4 col-12">
                        <label class="form-label">Auditor</label>
                        <input type="text" name="auditor" class="form-control" value="{{ old('auditor', $audit->auditor ?? '') }}" required>
                    </div>
                </div>

                {{-- Item --}}
                @php
                    $categories = [
                        '1S' => [['Materials or parts', 'There are no unneeded materials or parts around'], ['Machines or other equipment', 'There are no unused machines or other equipment around'], ['Tools, Supplies, Parts', 'There are no items on the Floor (except Legs, Wheels, Deck Footstep or Pallets)'], ['Frequency', 'All items been sorted by everyday use vs. those used occasionally'], ['Visual identification', 'Unnecessary items are clearly marked for disposal (red-tagged or labeled for removal)']],
                        '2S' => [['Item indicators', 'Everything HAS a place and everything is IN its place'], ['Marking of walkways, tools, daisha and pallets', 'Lines or markers are used to clearly indicate walkways, tools, daisha and pallets'], ['Tools', 'Tools are arranged functionally to facilitate picking them and returning them'], ['Storage indicator', 'Shelves or storage have marked (Kanban)'], ['Height and accessibility', 'Frequently used items are stored at optimal height and reach level (ergonomic and efficient access)']],
                        '3S' => [['Floors', 'Floors are kept shiny and clean and free of waste, water, dust and/or oil'], ['Machine, Tools & Equipment', 'The machines and equipment are wiped clean often; and kept free of waste, dust, and/or oil'], ['Habitual cleanliness', 'There is a cleaning checklist being followed'], ['Cleaning Tools', 'There are cleaning tools present in the area and in good condition'], ['Cleaning ownership', 'Cleaning responsibilities are clearly assigned and visible in the area']],
                        '4S' => [['Maintenance Schedule', 'The maintenance schedules are clearly displayed, followed, and verified for all machines and equipment'], ['Measurement Tools', 'The measurements tools are calibrated periodically'], ['Work Instruction', 'The standards procedures are written, clear and actively used'], ['KPI', 'The KPIs related to the standard work are visualized, maintained and monitored'], ['Visual management', 'Standardized visuals (kanban, labels, color codes, etc.) are used consistently across areas']],
                        '5S' => [['Tools and parts', 'Tools and parts are being stored correctly'], ['Stock controls', 'Stock controls are being adhered to (Kanban)'], ['Procedures', 'Procedures are updated (within last year) and regularly reviewed'], ['Check sheet', 'The standardize check sheet are available and updated in each workstation'], ['Area Person in charge', 'Ownership of areas/zones is clearly displayed']],
                    ];
                    $itemCounter = 1;
                @endphp

        @foreach($categories as $key => $items)
        <div class="card border-primary mb-4">
            <div class="card-header bg-primary text-white fw-bold">
                {{ $key }}: {{ ['1S'=>'Sort','2S'=>'Set in Order','3S'=>'Shine','4S'=>'Standardize','5S'=>'Sustain'][$key] }}
                <span class="float-end">Subtotal: <span id="subtotal-{{ $key }}">0</span></span>
            </div>
            <div class="card-body">
                {{-- Perulangan untuk setiap pertanyaan di dalam kategori --}}
                @foreach($items as $item)
                <div class="card mb-3 border-secondary shadow-sm" data-category="{{ $key }}">
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6 col-12"><strong>Check Item:</strong> {{ $item[0] }}</div>
                            <div class="col-md-6 col-12"><strong>Description:</strong> {{ $item[1] }}</div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label d-block"><strong>Score:</strong></label>
                            <div class="btn-group w-100" role="group" aria-label="Score selection">
                                @for ($i = 1; $i <= 5; $i++)
                                    <input type="radio" class="btn-check" name="items[{{ $itemCounter }}][score]" id="score-{{ $itemCounter }}-{{ $i }}" value="{{ $i }}"
                                           data-id="{{ $itemCounter }}" data-category="{{ $key }}"
                                           {{ (isset($audit->scores[$itemCounter]['score']) && $audit->scores[$itemCounter]['score'] == $i) ? 'checked' : '' }} autocomplete="off">
                                    <label class="btn btn-outline-primary" for="score-{{ $itemCounter }}-{{ $i }}">{{ $i }}</label>
                                @endfor
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label"><strong>Comment:</strong></label>
                            <input type="text" name="items[{{ $itemCounter }}][comment]" class="form-control form-control-sm" value="{{ $audit->scores[$itemCounter]['comment'] ?? '' }}">
                        </div>
                        
                        {{-- INPUT FILE PER ITEM DIKEMBALIKAN KE SINI --}}
                        <div class="mb-3">
                            <label class="form-label"><strong>File:</strong></label>
                            <div>
                                <input type="file" name="items[{{ $itemCounter }}][file]" id="file-upload-{{ $itemCounter }}" class="d-none file-input-listener">
                                <label for="file-upload-{{ $itemCounter }}" class="btn btn-secondary btn-sm">
                                    <i class="bi bi-upload me-1"></i> Upload Picture
                                </label>
                                <span class="file-name-display ms-2">No file chosen</span>
                            </div>
                        </div>

                        <input type="hidden" name="items[{{ $itemCounter }}][check_item]" value="{{ $item[0] }}">
                        <input type="hidden" name="items[{{ $itemCounter }}][description]" value="{{ $item[1] }}">
                    </div>
                </div>
                @php $itemCounter++; @endphp
                @endforeach
                
                {{-- Input file per kategori sudah dihapus dari sini --}}

            </div>
        </div>
        @endforeach

                {{-- SCORE SECTION - EDITED FOR ALIGNMENT --}}
                <div class="row mt-4 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">Final Score (%)</label>
                        <input type="text" name="final_score" class="form-control" id="finalScore" readonly>
                    </div>
                    <div class="col-md-8 text-md-end">
                        <h5><strong>Total Score: <span id="totalScore">0</span></strong></h5>
                        <p class="text-muted mb-0">Progress: <span id="progressCounter">0 / 0</span></p>
                    </div>
                </div>

                <div class="mt-4">
                    <label class="form-label">Overall Comments</label>
                    <textarea name="comments" class="form-control" rows="3">{{ old('comments', $audit->comments ?? '') }}</textarea>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">{{ isset($audit) ? 'Update' : 'Submit' }}</button>
                </div>
            </div>
        </div>
    </form>
</div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const categories = ['1S','2S','3S','4S','5S'];

    function calculateTotals() {
        let totalScore = 0;
        const maxScorePerItem = 5;
        const totalItems = document.querySelectorAll('div[data-category]').length;
        const totalPossibleScore = totalItems * maxScorePerItem;

        categories.forEach(category => {
            let subtotal = 0;
            document.querySelectorAll(`input[type="radio"][data-category="${category}"]:checked`).forEach(radio => {
                subtotal += parseInt(radio.value);
            });
            totalScore += subtotal;
            
            // FIX: Check if the subtotal element exists before trying to update its text
            const subtotalElement = document.getElementById('subtotal-' + category);
            if (subtotalElement) {
                subtotalElement.innerText = subtotal;
            }
        });

        document.getElementById('totalScore').innerText = totalScore;
        const percentScore = totalPossibleScore > 0 ? ((totalScore / totalPossibleScore) * 100).toFixed(2) : 0;
        document.getElementById('finalScore').value = percentScore;

        const filledItems = document.querySelectorAll('input[type="radio"]:checked').length;
        document.getElementById('progressCounter').innerText = `${filledItems} / ${totalItems}`;
    }

    document.querySelectorAll('input[type="radio"]').forEach(radio => {
        radio.addEventListener('change', calculateTotals);
    });

    document.querySelectorAll('.file-input-listener').forEach(input => {
        input.addEventListener('change', function(e) {
            const fileNameDisplay = e.target.parentElement.querySelector('.file-name-display');
            if (e.target.files.length > 0) {
                fileNameDisplay.textContent = e.target.files[0].name;
            } else {
                fileNameDisplay.textContent = 'No file chosen';
            }
        });
    });

    document.getElementById('auditForm').addEventListener('submit', function(e) {
        const totalItems = document.querySelectorAll('div[data-category]').length;
        const filled = document.querySelectorAll('input[type="radio"]:checked').length;

        if (filled < totalItems) {
            e.preventDefault();
            const allItems = document.querySelectorAll('div[data-category]');
            for (let item of allItems) {
                const radios = item.querySelectorAll('input[type="radio"]');
                const isChecked = Array.from(radios).some(r => r.checked);
                if (!isChecked) {
                    item.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    item.classList.add('border-danger');
                    alert('Please fill in the missing score.');
                    break;
                }
            }
        } else {
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.innerText = 'Submitting...';
        }
    });

    calculateTotals();
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
