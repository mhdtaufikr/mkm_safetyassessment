@extends('layouts.master')

@section('content')
<main>
    <header class="page-header page-header-dark bg-gradient-primary-to-secondary pb-10">
        <div class="container-xl px-4">
            <div class="page-header-content pt-4">
                <h1 class="page-header-title text-white"></h1>
            </div>
        </div>
    </header>

    <div class="container-xl px-4 mt-n10">
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <div class="row g-4">
            <!-- Card Stats -->
            <div class="col-xl-3 col-sm-6">
                <div class="card bg-primary text-white h-100">
                    <div class="card-body">Total Shops: {{ $totalShops }}</div>
                    <div class="card-footer d-flex align-items-center justify-content-between">
                        <a class="small text-white stretched-link" href="{{ route('shop.index') }}">View Details</a>
                        <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card bg-success text-white h-100">
                    <div class="card-body">Total Assessments: {{ $totalAssessments }}</div>
                    <div class="card-footer d-flex align-items-center justify-content-between">
                        <a class="small text-white stretched-link" href="http://localhost:8080/mkm_safetyassessment/public/form">Create New</a>
                        <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card bg-warning text-white h-100">
                    <div class="card-body text-white h-100">Pending Actions: {{ $allAssessments->where('is_followed_up', false)->count() }}</div>
                    <div class="card-footer d-flex align-items-center justify-content-between">
                        <a class="small text-white stretched-link" href="#all-risk-table">Review Now</a>
                        <div class="small text-dark"><i class="fas fa-angle-right"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card bg-danger text-white h-100">
                    <div class="card-body">Users Active: {{ $activeUsers ?? 0 }}</div>
                    <div class="card-footer d-flex align-items-center justify-content-between">
                        <a class="small text-white stretched-link" href="{{ route('users.index') }}">Manage</a>
                        <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Form -->
        <form method="GET" class="row g-3 mb-4 mt-2">
            <div class="col-md-4">
                <label for="month" class="form-label">Filter by Month</label>
                <select name="month" id="month" class="form-select">
                    @foreach(range(1,12) as $m)
                        <option value="{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}" {{ $month == str_pad($m, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                            {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label for="year" class="form-label">Filter by Year</label>
                <select name="year" id="year" class="form-select">
                    @for($y = now()->year; $y >= 2020; $y--)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-4">
    <label for="status" class="form-label">Follow-up Status</label>
    <select name="status" id="status" class="form-select">
        <option value="">-- Semua --</option>
<option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Open</option>
<option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Close</option>
    </select>
</div>

            <div class="col-12">
                <button type="submit" class="btn btn-outline-primary w-100">Filter</button>
            </div>
        </form>

        <!-- Chart & Table Section -->
        <div class="row">
             <div class="col-xl-6">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white h-100">Risk Level Summary</div>
            <div class="card-body d-flex" id="risk-chart-container">

                <!-- Box Summary Tahun & Follow Up -->
                <div class="me-3 d-flex flex-column justify-content-start align-items-center" style="width: 100px;">
                    <div class="border rounded p-2 shadow-sm bg-light w-90 text-center">
                        <div class="fw-bold text-primary">Year</div>
                        <div class="fs-5">{{ $year }}</div>
                        <hr class="my-1">
                        <div class="text-success small">Closed: {{ $allAssessments->where('is_followed_up', true)->count() }}</div>
                        <div class="text-warning small">Open: {{ $allAssessments->where('is_followed_up', false)->count() }}</div>
                    </div>
                </div>

                <!-- Chart tetap -->
                <div class="flex-grow-1">
                    <canvas id="riskChart" width="90%" height="30"></canvas>
                </div>

            </div>
        </div>
    </div>
            <div class="col-xl-6">
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white h-100">Recent Risk Assessments</div>
                    <div class="card-body" id="recent-assessments">
                        <div class="table-responsive">
                            <table id='tableShop' class="table table-sm table-striped">
                                <thead>
                                    <tr>
                                        <th class="card-header bg-primary text-white h-100">No.</th>
                                        <th class="card-header bg-primary text-white h-100">Shop</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentAssessments as $index => $assessment)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $assessment->shop->name ?? 'Unknown Shop' }}</td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="2" class="text-center">No recent assessments found.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        

        <div class="mb-3 d-flex gap-2">
    <form action="{{ route('export.excel') }}" method="GET" target="_blank" style="display: inline;">
    <button type="submit" class="btn btn-success btn-sm">
        <i class="bi bi-file-earmark-excel"></i> Export Excel
    </button>
</form>
 
</div>


        <!-- All Risk Table -->
<div class="card mb-4" id="all-risk-table">
    <div class="card-header bg-primary text-white h-100">All Risk Assessments</div>
    <div class="card-body">
        <div class="table-responsive">
            <table id='table' class="table table-bordered table-sm table-striped">
                <thead class="table-light">
                    <tr>
                        <th>No.</th>
                         <!-- Kolom gambar baru -->
                        <th>Shop</th>
                        <th>Scope</th>
                        <th>Problem</th>
                        <th>Hazard</th>
                        <th>Accessor</th>
                        <th>Severity</th>
                        <th>Probability</th>
                        <th>Score</th>
                        <th>Risk Level</th>
                        <th>Reduction Measures</th>
                        <th>File</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($allAssessments as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        
                        <td>{{ $item->shop->name ?? 'Unknown Shop' }}</td>
                        <td>{{ $item->scope_number ?? '-' }}</td>
                        <td>{{ $item->finding_problem }}</td>
                        <td>{{ $item->potential_hazards }}</td>
                        <td>{{ $item->accessor}}</td>
                        <td>{{ $item->severity }}</td>
                        <td>{{ $item->possibility }}</td>
                        <td>{{ $item->score }}</td>
                        <td>
                            <span class="badge 
                                @if($item->risk_level == 'Low') bg-success
                                @elseif($item->risk_level == 'Medium') bg-warning text-dark
                                @elseif($item->risk_level == 'High') bg-orange text-dark
                                @elseif($item->risk_level == 'Extreme') bg-danger
                                @else bg-secondary
                                @endif">
                                {{ $item->risk_level }}
                            </span>
                        </td>
                        <td>{{ $item->risk_reduction_proposal }}</td>
                        <td>
                            @if ($item->file)
                                <a href="{{ asset('storage/' . $item->file) }}" class="btn btn-outline-dark btn-sm" download>Download</a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>{{ \Carbon\Carbon::parse($item->created_at)->format('d M Y') }}</td>
                        <td>
                            @if($item->is_followed_up)
                                <span class="badge bg-success">Close</span>
                            @else
                                <span class="badge bg-yellow">Open</span>
                            @endif
                        </td>
                        <td class="text-nowrap">
                            <form action="{{ route('assessments.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Are you sure to delete this assessment?')" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger"><i class="fas fa-trash-alt"></i></button>
                            </form>
                            @if($item->is_followed_up)
                                <a href="{{ route('formAction.view', ['id' => $item->id]) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                            @else
                                <a href="{{ route('formAction', ['assessmentId' => $item->id]) }}" class="btn btn-sm btn-outline-primary">Follow Up</a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="15" class="text-center">No risk assessments found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

    
    </div>
</main>

@if(array_sum($riskLevelCounts) > 0)
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('riskChart');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Low', 'Medium', 'High', 'Extreme'],
            datasets: [{
                label: 'Risk Levels',
                data: @json($riskLevelCounts),
                backgroundColor: ['#28a745', '#ffc107', '#fd7e14', '#dc3545'],
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
</script>
@endif

<!-- DataTables -->
<script>
    $(document).ready(function() {
        $("#table").DataTable({
            responsive: true,
            lengthChange: false,
            autoWidth: false,
        });
        $("#tableShop").DataTable({
            responsive: true,
            lengthChange: false,
            autoWidth: false,
        });

        // Match chart height with table height
        function matchHeight() {
            const rightHeight = $('#recent-assessments').outerHeight();
            $('#risk-chart-container').height(rightHeight);
        }

        matchHeight();
        $(window).resize(matchHeight);
    });
</script>

<!-- Bootstrap Bundle for Alert & Modal support -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Script Hilangkan Alert -->
<script>
    setTimeout(function () {
        let alert = document.querySelector('.alert');
        if (alert) {
            let fade = bootstrap.Alert.getOrCreateInstance(alert);
            fade.close();
        }
    }, 3000);
</script>
@endsection