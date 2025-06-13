@extends('layouts.master')

@section('content')
<div class="container mt-5">
    <h4 class="mb-3">Detail Tindak Lanjut Temuan</h4>

    @if ($finding)
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">Deskripsi Temuan</h5>
                <p>{{ $finding->countermeasure }}</p>

                <p><strong>Shop:</strong> {{ $finding->shop }}</p>
                <p><strong>PIC Area:</strong> {{ $finding->pic_area }}</p>
                <p><strong>PIC Perbaikan:</strong> {{ $finding->pic_repair }}</p>
                <p><strong>Tanggal Genba:</strong> {{ $finding->genba_date }}</p>
                <p><strong>Tanggal Progress:</strong> {{ $finding->progress_date }}</p>
                <p><strong>Tenggat Waktu:</strong> {{ $finding->due_date ? $finding->due_date->format('d-m-Y') : '-' }}
</p>
                <p><strong>Status:</strong> {{ $finding->status }}</p>
                <p><strong>Checked:</strong> {{ $finding->checked }}</p>
                <p><strong>Kode:</strong> {{ $finding->code }}</p>
            </div>
        </div>
    @else
        <div class="alert alert-warning">Data temuan tidak ditemukan.</div>
    @endif

    <a href="{{ route('formAction', $finding->id_assessment ?? 0) }}" class="btn btn-secondary">Kembali</a>
</div>
@endsection
