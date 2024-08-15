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
                            <label for="to_date">Due Date</label>
                            <input type="date" name="due_date" id="due_date" class="form-control" value="{{ $invoice->due_date }}" required>
                            <span class="text-danger">@error('due_date') {{ $message }} @enderror</span>
                        </div>

                        <div class="form-group mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select name="status" id="exampleFormControlSelect1" class="form-select">

                                <option selected disabled>Select Status</option>
]
                                <option value="1"{{$invoice->status==1? 'selected' : ''}}>Complete</option>
                                <option value="0"{{$invoice->status==0? 'selected' : ''}}>Pending</option>

                            </select>
                        </div>


                        <div class="form-group mb-3">
                            <label for="rate">GST Tax</label>
                            <input type="number" name="tax" class="form-control rate-input" step="0.01" min="0" value="{{ $invoice->tax }}" required>
                            <span class="text-danger">@error('tax') {{$message}} @enderror</span>
                        </div>
                        <div class="form-group mb-3">
                            <label for="exampleFormControlTextarea1" class="form-label">Remarks</label>
                            <textarea class="form-control" id="exampleFormControlTextarea1" rows="3" name="remarks">
                                {{ $invoice->remarks  }}
                            </textarea>
                          </div>

                        <button type="submit" class="btn btn-primary me-2">Update Invoice</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>


@endsection
