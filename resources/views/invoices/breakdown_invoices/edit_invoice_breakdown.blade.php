@extends('admin.admin_dashboard')
@section('admin')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <div class="page-content">

        <div class="col-md-8 col-xl-8 middle-wrapper">
            <div class="row">
                <div class="card">
                    <div class="card-body">

                        <h6 class="card-title">Edit Labour Type</h6>

                        <form id="myForm" method="post" action="{{ route('invoice.breakdown.update',$breakdown->id) }}" class="forms-sample">
                            @csrf
                            <div class="form-group mb-3">
                                <label for="name" class="form-label">Labour Type: {{ $breakdown->labour->name }}</label>
                                <input type="text"
                                    class="form-control"
                                    name="rate" id="rate" value="{{ $breakdown->rate }}">
                                    <span class="text-danger">
                                        @error('rate')
                                            {{$message}}
                                        @enderror
                                    </span>
                            </div>

                            <button type="submit" class="btn btn-primary me-2">Update Amount</button>
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
