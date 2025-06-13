@extends('layouts.master')

@section('content')
<main>
    <div class="container-xl px-4 mt-4">

        {{-- Chart --}}
        <div class="card shadow mb-4">
            <div class="card-header bg-primary text-white fw-bold d-flex justify-content-between align-items-center">
                <h5 class=" text-white mb-0">Average Shop Score</h5>
                <div>
                    <select id="chartType" class="form-select form-select-sm">
                        <option value="bar" selected>Bar Chart</option>
                        <option value="horizontalBar">Horizontal Bar</option>
                        <option value="line">Line Chart</option>
                        <option value="radar">Radar Chart</option>
                        <option value="pie">Pie Chart</option>
                        <option value="doughnut">Doughnut Chart</option>
                    </select>
                </div>
            </div>
            <div class="card-body">
                @if(!empty($chartLabels) && count($chartLabels) > 0)
                    <div style="max-height: 600px; overflow-x: auto;">
                        <canvas id="myChart"></canvas>
                    </div>
                @else
                    <div class="alert alert-info text-center">Belum ada data untuk ditampilkan di chart.</div>
                @endif
            </div>
        </div>

        {{-- Table --}}
        <div class="card shadow">
            <div class="card-header bg-primary text-white d-flex justify-content-between">
                <h5 class="text-white mb-0">5S Audit Data</h5>
                <a href="{{ route('saudit.create') }}" class="btn btn-light btn-sm"><i class="fas fa-plus"></i>Add Audit</a>
            </div>
            <div class="card-body table-responsive">
                <table id='table' class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Shop</th>
                            <th>Auditor</th>
                            <th>Final score</th>
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
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

@if(!empty($chartLabels) && count($chartLabels) > 0)
<script>
    let chart;
    const ctx = document.getElementById('myChart').getContext('2d');
    const chartData = {
        labels: {!! json_encode($chartLabels) !!},
        datasets: [{
            label: 'Average Final Score',
            data: {!! json_encode($chartData) !!},
            backgroundColor: 'rgba(54, 162, 235, 0.7)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
        }]
    };

    function renderChart(type) {
        if (chart) chart.destroy();

        const isHorizontal = type === 'horizontalBar';
        const chartType = isHorizontal ? 'bar' : type;

        const newConfig = {
            type: chartType,
            data: chartData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true
                    },
                    tooltip: {
                        enabled: true
                    }
                }
            }
        };

        if (!['radar', 'pie', 'doughnut'].includes(type)) {
            newConfig.options.indexAxis = isHorizontal ? 'y' : 'x';
            newConfig.options.scales = {
                y: {
                    beginAtZero: true,
                    max: 100
                }
            };
        }

        chart = new Chart(ctx, newConfig);
    }

    renderChart('bar');

    document.getElementById('chartType').addEventListener('change', function () {
        renderChart(this.value);
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
    });
</script>
@endpush
