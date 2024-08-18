@extends('admin.admin_dashboard')
@section('admin')
    <div class="page-content">

        <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
            <div>
                <h4 class="mb-3 mb-md-0">Welcome to Dashboard</h4>
            </div>
            <div class="d-flex align-items-center flex-wrap text-nowrap">
            </div>
        </div>

        <div class="row">
            <div class="col-12 col-xl-12 stretch-card">
                <div class="row flex-grow-1">
                    <div class="col-md-4 grid-margin stretch-card">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-baseline">
                                    <h6 class="card-title mb-0">Total Amount</h6>
                                    <div class="dropdown mb-2">
                                        <a type="button" id="dropdownMenuButton" data-bs-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false">
                                            <i class="icon-lg text-muted pb-3px" data-feather="more-horizontal"></i>
                                        </a>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                            <a class="dropdown-item d-flex align-items-center" href="javascript:;"><i
                                                    data-feather="eye" class="icon-sm me-2"></i> <span
                                                    class="">View</span></a>
                                            <a class="dropdown-item d-flex align-items-center" href="javascript:;"><i
                                                    data-feather="edit-2" class="icon-sm me-2"></i> <span
                                                    class="">Edit</span></a>
                                            <a class="dropdown-item d-flex align-items-center" href="javascript:;"><i
                                                    data-feather="trash" class="icon-sm me-2"></i> <span
                                                    class="">Delete</span></a>
                                            <a class="dropdown-item d-flex align-items-center" href="javascript:;"><i
                                                    data-feather="printer" class="icon-sm me-2"></i> <span
                                                    class="">Print</span></a>
                                            <a class="dropdown-item d-flex align-items-center" href="javascript:;"><i
                                                    data-feather="download" class="icon-sm me-2"></i> <span
                                                    class="">Download</span></a>
                                        </div>
                                    </div>
                                </div>
                                @php
                            $pendingInvoicesCount = App\Models\Invoice::where('status', 0)->count();
                            $sumOfAmount = App\Models\Payment::sum('amount');
                                @endphp
                                <div class="row">
                                    <div class="col-6 col-md-12 col-xl-5">
                                        <h3 class="mb-2">
                                            {{ $sumOfAmount }}
                                        </h3>
                                        <div class="d-flex align-items-baseline">
                                            <p class="text-success">
                                                <span>+3.3%</span>
                                                <i data-feather="arrow-up" class="icon-sm mb-1"></i>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-12 col-xl-7">
                                        <div id="customersChart" class="mt-md-3 mt-xl-0"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 grid-margin stretch-card">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-baseline">
                                    <h6 class="card-title mb-0">Pending Invoices</h6>
                                    <div class="dropdown mb-2">
                                        <a type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false">
                                            <i class="icon-lg text-muted pb-3px" data-feather="more-horizontal"></i>
                                        </a>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                            <a class="dropdown-item d-flex align-items-center" href="javascript:;"><i
                                                    data-feather="eye" class="icon-sm me-2"></i> <span
                                                    class="">View</span></a>
                                            <a class="dropdown-item d-flex align-items-center" href="javascript:;"><i
                                                    data-feather="edit-2" class="icon-sm me-2"></i> <span
                                                    class="">Edit</span></a>
                                            <a class="dropdown-item d-flex align-items-center" href="javascript:;"><i
                                                    data-feather="trash" class="icon-sm me-2"></i> <span
                                                    class="">Delete</span></a>
                                            <a class="dropdown-item d-flex align-items-center" href="javascript:;"><i
                                                    data-feather="printer" class="icon-sm me-2"></i> <span
                                                    class="">Print</span></a>
                                            <a class="dropdown-item d-flex align-items-center" href="javascript:;"><i
                                                    data-feather="download" class="icon-sm me-2"></i> <span
                                                    class="">Download</span></a>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6 col-md-12 col-xl-5">
                                        <h3 class="mb-2">{{$pendingInvoicesCount}}</h3>
                                        <div class="d-flex align-items-baseline">
                                            <p class="text-danger">
                                                <span>-2.8%</span>
                                                <i data-feather="arrow-down" class="icon-sm mb-1"></i>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-12 col-xl-7">
                                        <div id="ordersChart" class="mt-md-3 mt-xl-0"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div> <!-- row -->


        <div class="row">
            <div class="col-lg-12 col-xl-12 stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-baseline mb-2">
                            @php
                                $invoices = App\Models\Invoice::where('status',0)->get();
                            @endphp
                            <h6 class="card-title mb-0">Invoices</h6>
                            <div class="dropdown mb-2">
                                <a type="button" id="dropdownMenuButton7" data-bs-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                    <i class="icon-lg text-muted pb-3px" data-feather="more-horizontal"></i>
                                </a>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton7">
                                    <a class="dropdown-item d-flex align-items-center" href="javascript:;"><i
                                            data-feather="eye" class="icon-sm me-2"></i> <span
                                            class="">View</span></a>
                                    <a class="dropdown-item d-flex align-items-center" href="javascript:;"><i
                                            data-feather="edit-2" class="icon-sm me-2"></i> <span
                                            class="">Edit</span></a>
                                    <a class="dropdown-item d-flex align-items-center" href="javascript:;"><i
                                            data-feather="trash" class="icon-sm me-2"></i> <span
                                            class="">Delete</span></a>
                                    <a class="dropdown-item d-flex align-items-center" href="javascript:;"><i
                                            data-feather="printer" class="icon-sm me-2"></i> <span
                                            class="">Print</span></a>
                                    <a class="dropdown-item d-flex align-items-center" href="javascript:;"><i
                                            data-feather="download" class="icon-sm me-2"></i> <span
                                            class="">Download</span></a>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
      <table id="dataTableExample" class="table">
        <thead>
          <tr>
            <th>Invoice ID</th>
            <th>Client Name</th>
            <th>Total Amount</th>
            <th>Status</th>
            <th>PDF</th>
          </tr>
        </thead>
        <tbody>
            @foreach ($invoices as $invoice)
    <tr>
        <td>{{ $invoice->id }}</td>
        <td>{{ $invoice->client->name }}</td>
        <td>{{ $invoice->grand_total }}</td>
        <td>
        @if ($invoice->status==0)
        <span class="badge bg-warning">Pending</span>
        @endif
    </td>
    <td>
        <a href="{{ route('invoice.pdfs', $invoice->id) }}" class="btn btn-info">View PDF's</a>
        &nbsp;&nbsp;&nbsp;
        <a href="{{ route('payment.invoice', $invoice->id) }}" class="btn btn-success">Payment</a>
    </td>





    </tr>
@endforeach

        </tbody>
      </table>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- row -->

</div>
@endsection
