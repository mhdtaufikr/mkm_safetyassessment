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
                <div class="table-responsive mt-3">
                    <table class="table table-bordered table-sm align-middle">
                        <thead class="table-primary text-center">
                            <tr>
                                <th style="width: 5%">5S</th>
                                <th style="width: 25%">Check Item</th>
                                <th style="width: 40%">Description</th>
                                <th style="width: 10%">Score</th>
                                <th style="width: 10%">Comment</th>
                                <th style="width: 10%">Files</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $scores = collect($audit->scores ?? [])->map(function ($item, $index) {
                                    $map = [
                                        0 => 'Sort',
                                        1 => 'Sort',
                                        2 => 'Sort',
                                        3 => 'Sort', // moved Frequency
                                        4 => 'Sort',
                                        5 => 'Sort',
                                        6 => 'Set in Order',
                                        7 => 'Set in Order', // moved Storage Indicator
                                        8 => 'Set in Order',
                                        9 => 'Set in Order',
                                        10 => 'Set in Order',
                                        11 => 'Shine',
                                        12 => 'Shine',
                                        13 => 'Shine',
                                        14 => 'Shine',
                                        15 => 'Shine', // moved Check Sheet
                                        16 => 'Standardize',
                                        17 => 'Standardize',
                                        18 => 'Standardize',
                                        19 => 'Standardize',
                                        20 => 'Standardize',
                                        21 => 'Sustain',
                                        22 => 'Sustain',
                                        23 => 'Sustain',
                                        24 => 'Sustain',
                                        25 => 'Sustain',
                                        
                                    ];
                                    $item['category'] = $map[$index] ?? '-';
                                    return $item;
                                })->groupBy('category');

                                $categories = ['Sort', 'Set in Order', 'Shine', 'Standardize', 'Sustain'];
                            @endphp

                            @forelse ($categories as $category)
                                @if ($scores->has($category))
                                    {{-- Header Kategori --}}
                                    <tr style="background-color: #DDEBF7;" class="fw-bold text-uppercase">
                                        <td colspan="6">5S: {{ $category }}</td>
                                    </tr>
                                    

                                    @php $subTotal = 0; @endphp

                                    @foreach ($scores[$category] as $i => $item)
                                        @php $subTotal += $item['score']; @endphp
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration }}</td>
                                            <td>{{ $item['check_item'] }}</td>
                                            <td>{{ $item['description'] }}</td>
                                            <td class="text-center">{{ $item['score'] }}</td>
                                            <td>{{ $item['comment'] ?? '-' }}</td>
                                            <td class="text-center">
                                                @if (!empty($item['file']))
                                                    @php 
                                                        $ext = pathinfo($item['file'], PATHINFO_EXTENSION);
                                                        $isImage = in_array(strtolower($ext), ['jpg', 'jpeg', 'png']);
                                                    @endphp
                                                    @if ($isImage)
                                                        <a href="{{ asset('storage/' . $item['file']) }}" target="_blank">
                                                            <img src="{{ asset('storage/' . $item['file']) }}" class="img-thumbnail" style="max-width: 80px;">
                                                        </a>
                                                    @else
                                                        <a href="{{ asset('storage/' . $item['file']) }}" class="btn btn-sm btn-primary" target="_blank">Download</a>
                                                    @endif
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach

                                    {{-- Sub Total --}}
                                    <tr class="fw-bold">
                                        <td colspan="3" class="text-end">Sub Total</td>
                                        <td class="text-center">{{ $subTotal }}</td>
                                        <td colspan="2"></td>
                                    </tr>
                                @endif
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">No data available</td>
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
