@extends('admin.admin_dashboard')
@section('admin')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <div class="page-content">

        <div class="col-md-8 col-xl-8 middle-wrapper">
            <div class="row">
                <div class="card">
                    <div class="card-body">

                        <h6 class="card-title">Add Invoice</h6>

                        <form id="myForm" method="post" action="{{ route('invoices.store') }}" class="forms-sample">
                            @csrf
                            <div class="form-group mb-3 mt-3">
                                <label for="client_id">Select Client</label>
                                <select name="client_id" id="client_id" class="form-select" required>
                                    <option value="" disabled selected>Select Client</option>
                                    @foreach($clients as $client)
                                        <option value="{{ $client->id }}">{{ $client->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group mb-3 mt-3">
                                <label for="from_date">From</label>
                                <input type="date" name="from_date" id="from_date" class="form-control" required>
                                <span class="text-danger">
                                    @error('from_date') {{ $message }} @enderror
                                </span>
                            </div>
                            <div class="form-group mb-3 mt-3">
                                <label for="to_date">To</label>
                                <input type="date" name="to_date" id="to_date" class="form-control" required>
                                <span class="text-danger">
                                    @error('to_date') {{ $message }} @enderror
                                </span>
                            </div>
                            <div class="form-group mb-3 mt-3">
                                <label for="to_date">Due Date</label>
                                <input type="date" name="due_date" id="due_date" class="form-control" required>
                                <span class="text-danger">
                                    @error('due_date') {{ $message }} @enderror
                                </span>
                            </div>
                            <div class="form-group mb-3 mt-3">
                                <label for="labor_types">Labor Type Rates</label>
                                <div id="labor-type-rates">
                                    <!-- This will be dynamically populated -->
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label for="rate">GST Tax</label>
                                <input type="number" name="tax" class="form-control rate-input" step="0.01" min="0" required>
                                <span class="text-danger">
                                    @error('tax') {{$message}} @enderror
                                </span>
                            </div>
                            <div class="form-group mb-3">
                                <label for="exampleFormControlTextarea1" class="form-label">Remarks</label>
                                <textarea class="form-control" id="exampleFormControlTextarea1" rows="3" name="remarks">

                                </textarea>
                              </div>

                            <button type="submit" class="btn btn-primary me-2">Add Invoice</button>
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
                            <p><strong>Total Employees:</strong> <span id="total-employees">0</span></p>
                            <p><strong>Total Amount:</strong> $<span id="total-amount">0.00</span></p>
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
