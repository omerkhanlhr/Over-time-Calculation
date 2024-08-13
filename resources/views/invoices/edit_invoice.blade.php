@extends('admin.admin_dashboard')
@section('admin')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>

<div class="page-content">
    <div class="col-md-8 col-xl-8 middle-wrapper">
        <div class="row">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Edit Invoice</h6>

                    <form id="myForm" method="post" action="{{ route('invoices.update', $invoice->id) }}" class="forms-sample">
                        @csrf

                        <div class="form-group mb-3 mt-3">
                            <label for="client_id">Select Client</label>
                            <select name="client_id" id="client_id" class="form-select" required>
                                <option value="" disabled>Select Client</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}" {{ $client->id == $invoice->client_id ? 'selected' : '' }}>
                                        {{ $client->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group mb-3 mt-3">
                            <label for="from_date">From</label>
                            <input type="date" name="from_date" id="from_date" class="form-control" value="{{ $invoice->from_date }}" required>
                            <span class="text-danger">@error('from_date') {{ $message }} @enderror</span>
                        </div>

                        <div class="form-group mb-3 mt-3">
                            <label for="to_date">To</label>
                            <input type="date" name="to_date" id="to_date" class="form-control" value="{{ $invoice->to_date }}" required>
                            <span class="text-danger">@error('to_date') {{ $message }} @enderror</span>
                        </div>

                        <div class="form-group mb-3 mt-3">
                            <label for="due_date">Due Date</label>
                            <input type="date" name="due_date" id="due_date" class="form-control" value="{{ $invoice->due_date }}" required>
                            <span class="text-danger">@error('due_date') {{ $message }} @enderror</span>
                        </div>

                        <div class="form-group mb-3 mt-3">
                            <label for="labor_types">Labor Type Rates</label>
                            <div id="labor-type-rates">
                                @foreach($invoice->invoiceBreakdowns as $breakdown)
                                    <div class="form-group mb-3">
                                        <label>{{ $breakdown->labour->name }}</label>
                                        <input type="number" name="labor_types[{{ $breakdown->labor_type_id }}]" class="form-control rate-input" step="0.01" min="0" value="{{ $breakdown->rate }}" required>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="rate">GST Tax</label>
                            <input type="number" name="tax" class="form-control rate-input" step="0.01" min="0" value="{{ $invoice->tax }}" required>
                            <span class="text-danger">@error('tax') {{$message}} @enderror</span>
                        </div>

                        <button type="submit" class="btn btn-primary me-2">Update Invoice</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8 col-xl-8 middle-wrapper mt-3">
        <div class="row">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Work Hours Details</h6>

                    <div id="workhours-details">
                        <p><strong>Total Employees:</strong> <span id="total-employees">{{ $invoice->total_employees }}</span></p>
                        <p><strong>Total Amount:</strong> $<span id="total-amount">{{ number_format($invoice->grand_total, 2) }}</span></p>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {

     function fetchWorkhoursDetails() {
         var client_id = $('#client_id').val();
         var from_date = $('#from_date').val();
         var to_date = $('#to_date').val();

         if (client_id && from_date && to_date) {
             console.log('Request Data:', { client_id, from_date, to_date }); // Check request data
             $.ajax({
                 url: "{{ route('workhours.details') }}",
                 method: 'GET',
                 data: {
                     client_id: client_id,
                     from_date: from_date,
                     to_date: to_date,
                 },
                 success: function(response) {
                     console.log('Response Data:', response); // Check response data
                     $('#total-employees').text(response.total_employees);
                     $('#total-amount').text(response.total_amount.toFixed(2));
                 },
                 error: function(xhr, status, error) {
                     console.log('AJAX Error:', error); // Log any AJAX errors
                 }
             });
         }

         if (client_id && from_date && to_date) {
                 $.ajax({
                     url: "{{ route('labor.types') }}",
                      method: 'GET',
                     data: {
                         client_id: client_id,
                         from_date: from_date,
                         to_date: to_date,
                     },
                     success: function(response) {
                         $('#labor-type-rates').html('');
                         response.labor_types.forEach(function(labor_type) {
                             $('#labor-type-rates').append(`
             <div class="form-group mb-3">
                 <label>${labor_type.name}</label>
                 <input type="number" name="labor_types[${labor_type.id}]" class="form-control rate-input" step="0.01" min="0" required>
             </div>
         `);
                         });
                     },
                     error: function(xhr, status, error) {
                         console.log('AJAX Error:', error);
                     }
                 });
             }
     }

     // Fetch workhours details on change of client or date range
     $('#client_id, #from_date, #to_date').change(function() {
         fetchWorkhoursDetails();
     });
 });

 </script>
@endsection
