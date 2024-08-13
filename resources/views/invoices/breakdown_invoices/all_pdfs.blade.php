@extends('admin.admin_dashboard')

@section('admin')
<div class="page-content">

    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Invoice #{{ $invoice->id }} - PDF Options</h6>

                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Labour Type</th>
                                <th>Hours Worked</th>
                                <th>Rate</th>
                                <th>Subtotal</th>
                                <th>View PDF</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($invoice->invoiceBreakdowns as $breakdown)
                                <tr>
                                    <td>{{ $breakdown->labour->name }}</td>
                                    <td>{{ $breakdown->hours_worked }}</td>
                                    <td>${{ number_format($breakdown->rate, 2) }}</td>
                                    <td>${{ number_format($breakdown->subtotal, 2) }}</td>
                                    <td>
                                        <a href="{{ route('invoice.breakdown.pdf', ['invoiceId' => $invoice->id, 'breakdownId' => $breakdown->id]) }}" class="btn btn-primary">View PDF</a>
                                    </td>

                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    <a href="{{ route('invoice.downloadPdf', ['id' => $invoice->id]) }}" class="btn btn-success">View Combined PDF</a>
                </div>

            </div>
        </div>
    </div>

</div>
@endsection
