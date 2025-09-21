@extends('layouts.master')
@section('content')

<div class="container mt-5">
    <h4 class="mb-4 fw-bold text-center text-primary">Risk Assessment Follow-up Details</h4>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div><strong>Shop:</strong> {{ optional($assessment->shop)->name ?? '-' }}</div>
    </div>

    @if ($findings->isEmpty())
        <div class="alert alert-warning">Belum ada tindakan temuan untuk assessment ini.</div>
    @else
        @foreach($findings as $finding)
            <div class="card border-primary shadow mb-4">
                <div class="card-body">
                    <h5 class="card-title text-primary fw-bold mb-3">Finding Code: {{ $finding->code ?? '-' }}</h5>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p><strong class="text-dark">Description:</strong> {{ $finding->countermeasure }}</p>
                            <p><strong class="text-dark">Status:</strong>
                                <span class="badge {{ $finding->status == 'Closed' ? 'bg-success' : 'bg-warning' }}">
                                    {{ $finding->status }}
                                </span>
                            </p>
                            <p><strong class="text-dark">Checked:</strong> {{ $finding->checked ?? '-' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong class="text-dark">PIC Area:</strong> {{ $finding->pic_area ?? '-' }}</p>
                            <p><strong class="text-dark">PIC Repair:</strong> {{ $finding->pic_repair ?? '-' }}</p>
                            <p><strong class="text-dark">Genba Date:</strong> {{ $finding->genba_date ?? '-' }}</p>
                            <p><strong class="text-dark">Progress Date:</strong> {{ $finding->progress_date ?? '-' }}</p>
                        </div>
                    </div>

                    <div class="row g-4">
                        {{-- Before Image --}}
                        @if ($assessment->file)
                            <div class="col-md-6 text-center">
                                <h6 class="text-dark fw-semibold">Foto Sebelum (Before)</h6>
                                <img src="{{ asset('storage/' . $assessment->file) }}" class="img-thumbnail shadow-sm" style="max-height: 250px;">
                                <div class="mt-2">
                                    <a href="{{ asset('storage/' . $assessment->file) }}" class="btn btn-outline-dark btn-sm" download>Download</a>
                                </div>
                            </div>
                        @endif

                        {{-- After Image --}}
                        @if ($finding->file && file_exists(public_path('storage/' . $finding->file)))
                            <div class="col-md-6 text-center">
                                <h6 class="text-dark fw-semibold">Foto Sesudah (After)</h6>
                                <img src="{{ asset('storage/' . $finding->file) }}" class="img-thumbnail shadow-sm" style="max-height: 250px;">
                                <div class="mt-2">
                                    <a href="{{ asset('storage/' . $finding->file) }}" class="btn btn-outline-dark btn-sm" download>Download</a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    @endif

    <div class="text-center mt-4">
        <a href="{{ route('home') }}" class="btn btn-outline-primary">← Return to Home Page</a>
    </div>
</div>

@endsection
