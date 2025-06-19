@extends('layouts.master')

@section('content')
<main>
    <div class="container-xl px-4 mt-4">

        {{-- Radar Chart --}}
        <div class="card shadow mb-4">
            <div class="card-header bg-primary text-white fw-bold d-flex justify-content-between align-items-center">
                <h5 class="text-white mb-0">5S Radar Chart</h5>
            </div>
            <div class="card-body">
                <div style="max-height: 500px;">
                    <canvas id="radarChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="card shadow mt-3">
            <div class="card-header bg-primary text-white d-flex justify-content-between">
                <h5 class="text-white mb-0">5S Audit Data</h5>
                <a href="{{ route('saudit.create') }}" class="btn btn-light btn-sm"><i class="fas fa-plus"></i> Add Audit</a>
            </div>
            <div class="card-body table-responsive">
                <table id="table" class="table table-bordered table-striped">
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
                                    <a href="{{ route('saudit.show', $audit->id) }}" class="btn btn-info btn-sm"><i class="fas fa-eye"></i></a>
                                    <a href="{{ route('saudit.edit', $audit->id) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                                    <form action="{{ route('saudit.destroy', $audit->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus audit ini?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger"><i class="fas fa-trash-alt"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center">Belum ada data audit.</td></tr>
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
        datasets: [
            {
                label: 'Target',
                data: [16, 16, 16, 16, 16],
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 2
            },
            {
                label: 'Actual',
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
                legend: { position: 'top' },
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
    $(document).ready(function () {
        $('#table').DataTable({
            responsive: true,
            lengthChange: false,
            autoWidth: false,
        });
    });
</script>
@endpush
