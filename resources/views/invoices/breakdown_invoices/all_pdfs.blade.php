@extends('admin.admin_dashboard')

@section('admin')
    <div class="page-content">

        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Invoice #{{ $invoice->id }} - PDF Options</h6>

                    <div class="table-responsive">
                        <table id="dataTableExample" class="table">
                            <thead>
                                <tr>
                                    <th>Labour Type</th>
                                    <th>Total Hours Worked</th>
                                    <th>Rate</th>
                                    <th>Total Amount</th>
                                    <th>View PDF</th>
                                    {{-- <th>Actions</th> --}}
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($groupedBreakdowns as $laborType => $breakdowns)
                                    @php
                                        $totalHours = $breakdowns->sum('hours_worked');
                                        $totalAmount = $breakdowns->sum('total_amount');
                                        $rate = $breakdowns->first()->rate;
                                    @endphp
                                    <tr>
                                        <td>{{ $laborType }}</td>
                                        <td>{{ $totalHours }}</td>
                                        <td>${{ number_format($rate, 2) }}</td>
                                        <td>${{ number_format($totalAmount, 2) }}</td>
                                        <td>
                                            <a href="{{ route('invoice.breakdown.pdf', ['invoiceId' => $invoice->id, 'laborType' => $laborType]) }}"
                                                class="btn btn-primary" target="_blank">View PDF</a>
                                        </td>
                                         {{-- <td>
                                            <!-- Iterate over breakdowns to get the ID for edit and delete -->
                                           @foreach ($breakdowns as $breakdown)
                                                <a href="{{ route('invoice.breakdown.delete', ['id' => $breakdown->id]) }}" class="btn btn-inverse-danger" id="delete">
                                                    <i class="fa fa-trash" style="font-size:24px;color:red"></i>
                                                </a>
                                                <a href="{{ route('invoice.breakdown.edit', ['id' => $breakdown->id]) }}" class="btn btn-inverse-warning">
                                                    <i class="fa fa-edit" style="font-size:24px;color:yellow"></i>
                                                </a>
                                                <!-- Since you're editing/deleting individual breakdowns, no need to loop further -->
                                                @break
                                             @endforeach
                                        </td> --}}
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('invoice.downloadPdf', ['id' => $invoice->id]) }}" class="btn btn-success" target="_blank">View
                            Combined PDF</a>
                    </div>

                </div>
            </div>
        </div>

    </div>
@endsection
