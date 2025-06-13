@extends('layouts.master')

@section('content')
<main>
    <div class="container-xl px-4 mt-4">
        <div class="card white-lg mb-4">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">View 5S Audit</h4>
            </div>

            <div class="card-body">

                {{-- Informasi Header --}}
                <div class="row mb-3">
                    <div class="col-md-4 mb-2">
                        <label class="form-label fw-bold">Shop</label>
                        <input type="text" class="form-control" value="{{ $audit->shop }}" readonly>
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label fw-bold">Date</label>
                        <input type="text" class="form-control" value="{{ \Carbon\Carbon::parse($audit->date)->format('d M Y') }}" readonly>
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label fw-bold">Auditor</label>
                        <input type="text" class="form-control" value="{{ $audit->auditor }}" readonly>
                    </div>
                </div>

                {{-- Tabel Checklist --}}
                <div class="table-responsive">
                    <table class="table table-bordered table-sm align-middle">
                        <thead class="table-primary text-center">
                            <tr>
                                <th style="width: 20%">Check Item</th>
                                <th style="width: 40%">Description</th>
                                <th style="width: 10%">Score</th>
                                <th style="width: 20%">Comment</th>
                                <th style="width: 10%">Files</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $scores = $audit->scores ?? [];
                            @endphp

                            @forelse($scores as $item)
                                <tr>
                                    <td>{{ $item['check_item'] }}</td>
                                    <td>{{ $item['description'] }}</td>
                                    <td class="text-center">{{ $item['score'] }}</td>
                                    <td>{{ $item['comment'] }}</td>
                                    <td class="text-center">
    @if (!empty($item['file']))
        @php 
            $fileExtension = pathinfo($item['file'], PATHINFO_EXTENSION);
            $isImage = in_array(strtolower($fileExtension), ['jpg', 'jpeg', 'png']);
        @endphp

        @if ($isImage)
            <a href="{{ asset('storage/'.$item['file']) }}" target="_blank">
                <img src="{{ asset('storage/'.$item['file']) }}" alt="Uploaded File" class="img-thumbnail" style="max-width: 120px; max-height: 120px;">
            </a>
        @else
            <a href="{{ asset('storage/'.$item['file']) }}" target="_blank" class="btn btn-sm btn-primary">Download</a>
        @endif
    @else
        <span class="text-muted">-</span>
    @endif
</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No data found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Final Score --}}
                <div class="mt-4">
                    <label class="form-label fw-bold">Final Score (%)</label>
                    <input type="text" class="form-control w-25" value="{{ number_format($audit->final_score, 2) }}" readonly>
                </div>

                {{-- General Comment --}}
                <div class="mt-4">
                    <label class="form-label fw-bold">General Comments</label>
                    <textarea class="form-control" rows="3" readonly>{{ $audit->comments }}</textarea>
                </div>

                {{-- Tombol Kembali --}}
                <div class="mt-4">
                    <a href="{{ route('saudit.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>

            </div>
        </div>
    </div>
</main>
@endsection
