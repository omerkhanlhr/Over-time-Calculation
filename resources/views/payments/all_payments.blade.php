@extends('admin.admin_dashboard')

@section('styles')
<style>
    /* Custom CSS for enhanced styling */
    .card-title {
        color: #333;
    }

    .table {
        font-size: 14px;
    }

    .table th,
    .table td {
        vertical-align: middle;
    }

    .table thead th {
        background-color: #f8f9fa;
        border-top: none;
    }

    .table-bordered th,
    .table-bordered td {
        border: 1px solid #dee2e6;
    }

    .table-hover tbody tr:hover {
        background-color: #f2f2f2;
    }

    .table a {
        color: #007bff;
    }
</style>
@endsection

@section('admin')
<div class="page-content">

    <div class="row">
        <div class="col-md-12 mb-4 mt-2">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">All Payments</h4>
                    <div class="table-responsive">
                        <table id="dataTableExample" class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Payment ID</th>
                                    <th>Client Name</th>
                                    <th>Invoice ID</th>
                                    <th>Invoice Amount</th>
                                    <th>Received Amount</th>
                                    <th>Payment Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($payments as $payment)
                                <tr>
                                    <td> {{ $payment->id }}   </td>
                                    <td>{{ $payment->invoice->client->name }}</td>
                                    <td>
                                        <a href="{{route('show.invoice',$payment->invoice_id)}}">{{ $payment->invoice_id }}</a>
                                    </td>
                                    <td>{{ $payment->invoice->grand_total }}</td>
                                    <td>{{ $payment->amount }}</td>
                                    <td>{{ $payment->payment_date }}</td>

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
