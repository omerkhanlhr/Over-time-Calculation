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
                                    <th>Total Amount</th>
                                    <th>Actions</th>
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
                                        <td>${{ number_format($totalAmount, 2) }}</td>
                                        <td>
                                            <a href="{{ route('invoice.breakdown.preview.pdf', ['invoiceId' => $invoice->id, 'laborType' => $laborType]) }}"
                                                class="btn btn-primary" target="_blank">View PDF</a>
                                            &nbsp;&nbsp;
                                            <a href="{{ route('invoice.breakdown.download.pdf', ['invoiceId' => $invoice->id, 'laborType' => $laborType]) }}"
                                                class="btn btn-primary" target="_blank">Download PDF</a>
                                                &nbsp;&nbsp;
<<<<<<< HEAD
=======
                                            <a href="{{ route('invoice.breakdown.edit', ['invoiceId' => $invoice->id, 'laborType' => $laborType]) }}"
                                                class="btn btn-warning">Edit</a>
>>>>>>> f00aac00c0b8581246a1b112796c1e2853b27609
                                        </td>


                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('invoice.previewPdf', ['id' => $invoice->id]) }}" class="btn btn-primary" target="_blank">View
                            Combined PDF</a>
                            &nbsp;&nbsp;&nbsp;
                        <a href="{{ route('invoice.downloadPdf', ['id' => $invoice->id]) }}" class="btn btn-info" >Download
                            Combined PDF</a>

                    </div>

                </div>
            </div>
        </div>

    </div>
@endsection
