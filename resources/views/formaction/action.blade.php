@extends('layouts.master')

@section('content')
<div class="container mt-5">
    <h4 class="mb-3">Findings Action Input Form</h4>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('formAction.store') }}" method="POST" enctype="multipart/form-data" class="row g-3">
        @csrf

        <!-- Hidden inputs -->
        <input type="hidden" name="id_assessment" value="{{ $assessmentId }}">
        <input type="hidden" name="shop_id" value="{{ $shopId }}">
        <input type="hidden" name="shop" value="{{ $shopName }}">

        <!-- Form Fields -->
        <div class="col-md-6">
            <label class="form-label">Status</label>
            <input type="text" name="status" class="form-control" value="Close" readonly>
        </div>

        <div class="col-md-6">
            <label class="form-label">Shop</label>
            <input type="text" class="form-control" value="{{ $shopName }}" readonly>
        </div>

        <div class="col-md-12">
            <label class="form-label">Countermeasure Description</label>
            <textarea name="countermeasure" class="form-control" rows="2" required>{{ old('countermeasure') }}</textarea>
        </div>

        <div class="col-md-6">
            <label class="form-label">PIC Area</label>
            <input type="text" name="pic_area" class="form-control" value="{{ old('pic_area') }}" placeholder="Nama Penanggung Jawab Area">
        </div>

        <div class="col-md-6">
            <label class="form-label">PIC Repair</label>
            <input type="text" name="pic_repair" class="form-control" value="{{ old('pic_repair') }}" placeholder="Nama Penanggung Jawab Perbaikan">
        </div>

        <div class="col-md-4">
            <label class="form-label">Deadlines</label>
            <input type="date" name="due_date" class="form-control" value="{{ old('due_date') }}">
        </div>

        <div class="col-md-4">
            <label class="form-label">Genba Date</label>
            <input type="date" name="genba_date" class="form-control" value="{{ old('genba_date') }}">
        </div>

        <div class="col-md-4">
            <label class="form-label">Progress Date</label>
            <input type="date" name="progress_date" class="form-control" value="{{ old('progress_date') }}">
        </div>

        <div class="col-md-4">
            <label class="form-label">Checked</label>
            <select name="checked" class="form-select" required>
                <option value="">-- Select --</option>
                <option value="YES" {{ old('checked') == 'YES' ? 'selected' : '' }}>YES</option>
                <option value="NO" {{ old('checked') == 'NO' ? 'selected' : '' }}>NO</option>
            </select>
        </div>

        <div class="col-md-4">
            <label class="form-label">Code</label>
            <input type="text" name="code" class="form-control" value="{{ old('code') }}" placeholder="Kode temuan (opsional)">
        </div>

        <div class="col-md-4">
            <label class="form-label">Upload Files (opsional)</label>
            <input type="file" name="file" class="form-control" multiple>
            <small class="text-muted">Format: jpg, jpeg, png, pdf, doc(x), xls(x) max 10MB</small>
        </div>

        <div class="col-12">
            <button type="submit" class="btn btn-success">Save</button>
        </div>
    </form>
    
    <hr>
    
    <h5 class="mt-4">Prior Findings Action List</h5>
    <ul class="list-group">
        @forelse ($findings as $finding)
        <li class="list-group-item">
            <strong>Deskripsi:</strong> {{ $finding->countermeasure }}<br>
            <strong>Status:</strong> {{ $finding->status }}<br>
            <strong>PIC Area:</strong> {{ $finding->pic_area }}<br>
            <strong>Tanggal Genba:</strong> {{ $finding->genba_date }}<br>
        </li>
        @empty
        <li class="list-group-item">No actions found yet.</li>
        @endforelse
    </ul>
    
    @if($formFiles->count())
        <div class="mt-5">
            <h5 class="mb-3">📁 File </h5>
            <div class="row">
                @foreach ($formFiles as $item)
                    @if($item->file)
                        @php
                            $ext = strtolower(pathinfo($item->file, PATHINFO_EXTENSION));
                            $path = 'storage/' . $item->file;
                        @endphp
                        <div class="col-md-4 mb-4">
                            <div class="card shadow-sm">
                                @if(in_array($ext, ['jpg', 'jpeg', 'png']))
                                    <img src="{{ asset($path) }}" class="card-img-top" alt="File Image" style="height: 200px; object-fit: cover;">
                                @else
                                    <div class="p-4 text-center">
                                        <i class="bi bi-file-earmark-text" style="font-size: 3rem;"></i>
                                        <p class="mt-2">Dokumen</p>
                                    </div>
                                @endif
                                <div class="card-body">
                                    <h6 class="card-title">Scope #{{ $item->scope_number }}</h6>
                                    <p class="card-text text-truncate">{{ basename($item->file) }}</p>
                                    <a href="{{ asset($path) }}" class="btn btn-primary btn-sm" target="_blank">View Files</a>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
