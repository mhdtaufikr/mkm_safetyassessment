@extends('layouts.master')

@section('content')
    @php
        $month = $month ?? date('m');
        $year = $year ?? date('Y');
    @endphp

    <main>
        <div class="container-xl mt-4 px-4">

            <!-- Filter Form -->
            <form method="GET" class="row g-3 mb-4 mt-2">
                <div class="col-md-4">
                    <label for="month" class="form-label">Filter by Month</label>
                    <select name="month" id="month" class="form-select">
                        @foreach (range(1, 12) as $m)
                            <option value="{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}"
                                {{ request('month') == str_pad($m, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                                {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="year" class="form-label">Filter by Year</label>
                    <select name="year" id="year" class="form-select">
                        @for ($y = now()->year; $y >= 2020; $y--)
                            <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>
                                {{ $y }}</option>
                        @endfor
                    </select>
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-outline-primary w-100">Filter</button>
                </div>
            </form>


            {{-- Radar Chart --}}
            <div class="card mb-4 shadow">
                <div class="card-header bg-primary fw-bold d-flex justify-content-between align-items-center text-white">
                    <h5 class="mb-0 text-white">5S Radar Chart</h5>
                </div>
                <div class="card-body">
                    <div style="max-height: 500px;">
                        <canvas id="radarChart"></canvas>
                    </div>
                </div>
            </div>

            <button type="button" class="btn btn-success btn-sm mb-2" data-bs-toggle="modal" data-bs-target="#modal-add">
                <i class="bi bi-file-earmark-excel"></i> Export Excel
            </button>

            <!-- Modal -->
            <div class="modal fade" id="modal-add" tabindex="-1" aria-labelledby="modal-add-label" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modal-add-label">Export Excel</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <form action="{{ route('audit5s.exportAll') }}" method="GET" target="_blank">
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="start_date">Tanggal Mulai<span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="start_date" name="start_date" required>
                                </div>
                                <br>
                                <div class="form-group">
                                    <label for="end_date">Tanggal Selesai<span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="end_date" name="end_date" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-dark" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>


            {{-- Table --}}
            <div class="card mt-3 shadow">
                <div class="card-header bg-primary d-flex justify-content-between text-white">
                    <h5 class="mb-0 text-white">5S Audit Data</h5>
                    <a href="{{ route('saudit.create') }}" class="btn btn-light btn-sm"><i class="fas fa-plus"></i> Add
                        Audit</a>
                </div>
                <div class="card-body table-responsive">
                    <table id="table" class="table-bordered table-striped table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Shop</th>
                                <th>Auditor</th>
                                <th>Final Score</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($audits as $audit)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($audit->date)->format('Y-m-d') }}</td>
                                    <td>{{ $audit->shop }}</td>
                                    <td>{{ $audit->auditor }}</td>
                                    <td>{{ number_format($audit->final_score, 2) }}%</td>
                                    <td>
                                        <a href="{{ route('saudit.show', $audit->id) }}" class="btn btn-info btn-sm"><i
                                                class="fas fa-eye"></i></a>
                                        <a href="{{ route('saudit.edit', $audit->id) }}" class="btn btn-warning btn-sm"><i
                                                class="fas fa-edit"></i></a>
                                        <form action="{{ route('saudit.destroy', $audit->id) }}" method="POST"
                                            class="d-inline" onsubmit="return confirm('Hapus audit ini?')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-danger"><i class="fas fa-trash-alt"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">Belum ada data audit.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
@endsection



@push('scripts')
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Radar Chart Script -->
    <script>
        const radarCtx = document.getElementById('radarChart').getContext('2d');

        const radarData = {
            labels: ['Sort', 'Set in Order', 'Shine', 'Standardize', 'Sustain'],
            datasets: [{
                    label: 'Target',
                    data: [16, 16, 16, 16, 16],
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 2
                },
                {
                    label: 'Actual ({{ \Carbon\Carbon::createFromDate(null, $month, 1)->locale('id')->translatedFormat('F') }} {{ $year }})',
                    data: @json($actualScores),
                    backgroundColor: 'rgba(255, 159, 64, 0.2)',
                    borderColor: 'rgba(255, 159, 64, 1)',
                    borderWidth: 2
                }

            ]
        };

        const radarConfig = {
            type: 'radar',
            data: radarData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    r: {
                        suggestedMin: 0,
                        suggestedMax: 16,
                        ticks: {
                            stepSize: 2
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'top'
                    },
                    title: {
                        display: true,
                        text: '5S Implementation vs Target'
                    }
                }
            }
        };

        new Chart(radarCtx, radarConfig);
    </script>

    <!-- DataTables Script -->
    <script>
        $(document).ready(function() {
            $('#table').DataTable({
                responsive: true,
                lengthChange: false,
                autoWidth: false,
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('#modal-add form');
            const startDateInput = form.querySelector('#start_date');
            const endDateInput = form.querySelector('#end_date');

            form.addEventListener('submit', function(e) {
                let startDate = startDateInput.value;
                let endDate = endDateInput.value;
                let valid = true;

                // Reset pesan error
                form.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
                startDateInput.classList.remove('is-invalid');
                endDateInput.classList.remove('is-invalid');

                // Validasi end_date >= start_date
                if (startDate && endDate && endDate < startDate) {
                    showError(endDateInput, 'Tanggal selesai tidak boleh kurang dari Tanggal Mulai');
                    valid = false;
                }

                if (!valid) {
                    e.preventDefault(); // cegah submit
                }
            });

            function showError(input, message) {
                input.classList.add('is-invalid');
                const div = document.createElement('div');
                div.className = 'invalid-feedback';
                div.textContent = message;
                input.parentNode.appendChild(div);
            }
        });
    </script>
@endpush
