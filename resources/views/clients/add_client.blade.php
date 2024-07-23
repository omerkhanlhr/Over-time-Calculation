@extends('admin.admin_dashboard')
@section('admin')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <div class="page-content">

        <div class="col-md-8 col-xl-8 middle-wrapper">
            <div class="row">
                <div class="card">
                    <div class="card-body">

                        <h6 class="card-title">Add Client</h6>

                        <form id="myForm" method="post" action="{{ route('save.client') }}" class="forms-sample">
                            @csrf
                            <div class="form-group mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text"
                                    class="form-control"
                                    name="name" id="name" value="{{old('name')}}">
                                    <span class="text-danger">
                                        @error('name')
                                            {{$message}}
                                        @enderror
                                    </span>

                            </div>
                            <div class="form-group mb-3">
                                <label for="name" class="form-label">Email</label>
                                <input type="email"
                                    class="form-control"
                                    name="email" id="email" value="{{old('email')}}">
                                    <span class="text-danger">
                                        @error('email')
                                            {{$message}}
                                        @enderror
                                    </span>

                            </div>
                            <div class="form-group mb-3">
                                <label for="name" class="form-label">Company</label>
                                <input type="text"
                                    class="form-control"
                                    name="company" id="company" value="{{old('company')}}" placeholder="Optional.....">
                                    <span class="text-danger">
                                        @error('company')
                                            {{$message}}
                                        @enderror
                                    </span>

                            </div>
                            <div class="form-group mb-3">
                                <label for="name" class="form-label">Phone</label>
                                <input type="tel" id="phone" name="phone" class="form-control" pattern="[0-9]{5}-[0-9]{7}-[0-9]{1}">
                                    <span class="text-danger">
                                        @error('phone')
                                            {{$message}}
                                        @enderror
                                    </span>

                            </div>
                            <div class="form-group mb-3">
                                <label for="name" class="form-label">Address</label>
                                <textarea name="address" id="address" class="form-control" rows="3">
                                </textarea>
                                    <span class="text-danger">
                                        @error('address')
                                            {{$message}}
                                        @enderror
                                    </span>

                            </div>

                            <button type="submit" class="btn btn-primary me-2">Add Client</button>
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
