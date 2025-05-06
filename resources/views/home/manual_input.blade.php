@extends('layouts.master')

@section('content')
<main>
    <header class="page-header page-header-dark bg-gradient-primary-to-secondary pb-10">
        <div class="container-fluid px-4">
            <div class="page-header-content pt-4">
                <div class="row align-items-center justify-content-between">
                    <div class="col-auto mt-4">
                        <h1 class="page-header-title">
                            <div class="page-header-icon"><i data-feather="tool"></i></div>
                            Your View
                        </h1>
                        <div class="page-header-subtitle">Manage Your View</div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="container-fluid px-4 mt-n10">
        <div class="content-wrapper">
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">List of Your View</h3>
                                </div>

                                @include('partials.alert')

                                <div class="card-body">
                                    @if($inventory)
                                    <h5>Inventory Info</h5>
                                    <div class="row mb-3">
                                        <div class="col-md-3 mb-2">
                                            <label><strong>Material</strong></label>
                                            <div>{{ $inventory->material }}</div>
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <label><strong>Material Description</strong></label>
                                            <div>{{ $inventory->materialdescription }}</div>
                                        </div>

                                        <div class="col-md-3 mb-2">
                                            <label><strong>Group No</strong></label>
                                            <div>{{ $inventory->groupno }}</div>
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <label><strong>SLOC</strong></label>
                                            <div>{{ $inventory->sloc }}</div>
                                        </div>

                                        <div class="col-md-3 mb-2">
                                            <label><strong>Description</strong></label>
                                            <div>{{ $inventory->descr }}</div>
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <label><strong>Unit (BUN)</strong></label>
                                            <div>{{ $inventory->bun }}</div>
                                        </div>

                                        <div class="col-md-3 mb-2">
                                            <label><strong>Posting Date</strong></label>
                                            <div>{{ \Carbon\Carbon::parse($inventory->postingdate)->format('Y-m-d') }}</div>
                                        </div>
                                    </div>

                                <form action="{{ route('inventory.update.store') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="sap_inventory_id" value="{{ $inventory->id }}">

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="actqty"><strong>Actual Qty</strong></label>
                                            <input type="number" name="actqty" id="actqty" class="form-control" required>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="serial_number"><strong>Serial Number</strong></label>
                                            <input type="text" name="serial_number" id="serial_number" class="form-control" required>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="remarks"><strong>Remarks</strong></label>
                                            <input type="text" name="remarks" id="remarks" class="form-control">
                                        </div>

                                        <div class="col-md-6">
                                            <label for="pic"><strong>PIC</strong></label>
                                            <input type="text" name="pic" id="pic" class="form-control" required>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="checker"><strong>Checker</strong></label>
                                            <input type="text" name="checker" id="checker" class="form-control" required>
                                        </div>
                                    </div>

                                    <div class="mt-3">
                                        <button type="submit" class="btn btn-success btn-sm">
                                            <i class="fas fa-save"></i> Save Update
                                        </button>
                                    </div>
                                </form>
                                <div class="table-responsive">
                                    <h5 class="mt-4">Update Logs</h5>
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Qty</th>
                                                <th>Serial Number</th>
                                                <th>Remarks</th>
                                                <th>PIC</th>
                                                <th>Checker</th>
                                                <th>Time</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($updates as $log)
                                                <tr>
                                                    <td>{{ $log->actqty }}</td>
                                                    <td>{{ $log->serial_number }}</td>
                                                    <td>{{ $log->remarks }}</td>
                                                    <td>{{ $log->pic }}</td>
                                                    <td>{{ $log->checker }}</td>
                                                    <td>{{ \Carbon\Carbon::parse($log->created_at)->format('Y-m-d H:i') }}</td>
                                                </tr>
                                            @empty
                                                <tr><td colspan="6" class="text-center">No logs found</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                    @else
                                        <div class="alert alert-warning">Inventory data not found.</div>
                                    @endif
                                </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

</main>

<style>
    table td {
        white-space: nowrap;  /* Prevent text wrapping */
        overflow: hidden;     /* Hide overflow text */
        text-overflow: ellipsis; /* Show ellipsis for overflow */
    }
</style>

<style>
    .modal-lg-x {
    max-width: 90%;
}
.modal-lg {
    max-width: 70%;
}
</style>


@endsection