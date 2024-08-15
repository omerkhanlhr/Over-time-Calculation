@extends('admin.admin_dashboard')

@section('admin')
    <div class="page-content">

        <nav class="page-breadcrumb">
            <ol class="breadcrumb">
                <a href="{{ route('invoices.create') }}" class="btn btn-inverse-info">Add Invoice</a>
            </ol>
        </nav>

        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Invoices</h6>

                        <div class="table-responsive">
                            <table id="dataTableExample" class="table">
                                <thead>
                                    <tr>
                                        <th>Invoice ID</th>
                                        <th>Client Name</th>
                                        <th>Date Range</th>
                                        <th>Total Employees</th>
                                        <th>Total Amount</th>
                                        <th>Tax</th>
                                        <th>Grand Total</th>
                                        <th>PDF</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($invoices as $invoice)
                                        <tr>
                                            <td>{{ $invoice->id }}</td>
                                            <td>{{ $invoice->client->name }}</td>
                                            <td>{{ $invoice->from_date }} - {{ $invoice->to_date }}</td>
                                            <td>{{ $invoice->total_employees }}</td>
                                            <td>${{ number_format($invoice->total_amount, 2) }}</td>
                                            <td>${{ number_format($invoice->tax, 2) }}</td>
                                            <td>${{ number_format($invoice->grand_total, 2) }}</td>
                                            <td>
                                                <a href="{{ route('invoice.pdfs', $invoice->id) }}" class="btn btn-primary">View PDF's</a>
                                                &nbsp;&nbsp;&nbsp;&nbsp;
                                                <a href="{{route('invoices.edit',$invoice->id)}}" class="btn btn-inverse-warning"><i class="fa fa-edit" style="font-size:24px;color:yellow"></i></a>
                                                <a href="{{route('delete.invoice',$invoice->id)}}" class="btn btn-inverse-danger" id="delete"><i class="fa fa-trash" style="font-size:24px;color:red"></i></a>

                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
