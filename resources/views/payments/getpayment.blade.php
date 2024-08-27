@extends('admin.admin_dashboard')
@section('admin')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <div class="page-content">

        <div class="col-md-8 col-xl-8 middle-wrapper">
            <div class="row">
                <div class="card">
                    <div class="card-body">

                        <h6 class="card-title">Edit Client</h6>

                        <form id="myForm" method="post" action="{{ route('payment.invoice',$invoice->id) }}" class="forms-sample">
                            @csrf
                            <div class="form-group mb-3">
                                <label for="name" class="form-label">Invoice Id</label>
                                <input type="text"
                                    class="form-control"
                                    name="id" id="id" value="{{$invoice->id}}">

                            </div>
                            <div class="form-group mb-3">
                                <label for="rate">Amount Due</label>
                                <input type="number" name="amount" class="form-control rate-input" step="0.01" min="0" value="{{$invoice->grand_total}}" required>
                                <span class="text-danger">
                                    @error('amount') {{$message}} @enderror
                                </span>
                            </div>

                            <button type="submit" class="btn btn-primary me-2">Add Payment</button>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- middle wrapper end -->
    <!-- right wrapper start -->
    </div>
    <!-- right wrapper end -->
    </div>

    </div>
@endsection
