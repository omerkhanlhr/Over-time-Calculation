@extends('admin.admin_dashboard')

@section('styles')
    <style>
        .card-title {
            font-size: 24px;
            margin-bottom: 20px;
        }

        .invoice-info {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .product-table th {
            background-color: #007bff;
            color: #fff;
        }

        .total-amount {
            font-weight: bold;
        }
    </style>
@endsection

@section('admin')
@php
$lateFee = 0;

        if ($invoice->status == 0 && \Carbon\Carbon::now()->gt(\Carbon\Carbon::parse($invoice->due_date)))
        {
            $lateFee = $invoice->grand_total * 0.02; // 2% late fee
        }
        $finalTotal = $invoice->grand_total + $lateFee;

        @endphp
    <div class="page-content">
        <h4 class="card-title">Invoice Details</h4>

        <div class="invoice-info">
            <br>
            <p>Invoice ID: {{ $invoice->id }}</p>
            <br>
            <p>Client Name: {{ $invoice->client->name }}</p>
            <br>
            <p>Invoice From Date: {{ \Carbon\Carbon::parse($invoice->from_date)->format('M d y') }}
            </p>
            <br>
            <p>Invoice To Date: {{ \Carbon\Carbon::parse($invoice->to_date)->format('M d y') }}
            </p>
            <br>
            <p>Invoice Due Date: {{$invoice->due_date}}
            </p>
            <br>
            <p>Invoice Tax: {{$invoice->tax}}%
            </p>
            <br>
            <p>Total Amount: {{$invoice->total_amount}}
            </p>
            <br>
            <p>Grand Total: {{  $finalTotal  }}
            </p>
            <br>
        </div>
        @if ($invoice->status == 1 )
        <div class="row">
            <div class="col-md-12 mb-4 mt-2">
                <div class="card">
                    <div class="card-body">


                            <h4 class="card-title mt-3">Payment Details</h4>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Payment ID</th>
                                        <th>Payment Date</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($payments as $payment)
                                        <tr>
                                            <td>{{ $payment->id }}</td>
                                            <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('F d Y') }}</td>
                                            <td>{{ $payment->amount }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
