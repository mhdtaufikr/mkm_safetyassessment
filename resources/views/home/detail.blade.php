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
                           {{$dataDetail->physinvdoc}} - {{$dataDetail->material}}
                        </h1>
                        <div class="page-header-subtitle">{{$dataDetail->sloc}}</div>
                    </div>
                    <div class=" page-header-title col-12 col-xl-auto mt-4">
                      <strong>
                        Qty : {{$dataDetail->total_qty_per_inventory}}
                      </strong>
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
                                    <h3 class="card-title">{{$dataDetail->material}} - {{$dataDetail->sloc}}</h3>
                                </div>

                                @include('partials.alert')

                                <div class="card-body">
                                    <div class="row">
                                    </div>

                                    <!-- DataTable -->
                                    <div class="table-responsive">
                                        <table id="tablehistory" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Serial No.</th>
                                                    <th>QTY</th>
                                                    <th>Remarks</th>
                                                </tr>
                                            </thead>
                                        </table>

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
<script>
    $(document).ready(function() {
        $('#tablehistory').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route("home.detail", ["id" => $id]) }}',
            columns: [
                { data: 'serial_number', name: 'serial_number' },
                { data: 'actqty', name: 'actqty' },
                { data: 'remarks', name: 'remarks' }
            ]
        });
    });
</script>


@endsection

