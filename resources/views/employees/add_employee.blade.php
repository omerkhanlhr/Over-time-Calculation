@extends('admin.admin_dashboard')
@section('admin')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <div class="page-content">

        <div class="col-md-8 col-xl-8 middle-wrapper">
            <div class="row">
                <div class="card">
                    <div class="card-body">

                        <h4 class="card-title">Add Employee</h4>

                        <form action="{{ route('save.employee')}}" method="post" class="forms-sample">
                            @csrf
                            <div class="form-group mb-3">
                                <label for="name" class="form-label">Employee Name</label>
                                <input type="text"
                                    class="form-control"
                                    name="employee_name" id="name" value="{{old('employee_name')}}">
                                    <span class="text-danger">
                                        @error('employee_name')
                                            {{$message}}
                                        @enderror
                                    </span>

                            </div>

                            <div class="form-group mb-3">
                                <label for="name" class="form-label">Email</label>
                                <input type="email"
                                    class="form-control"
                                    name="email" id="cnic" value="{{old('email')}}">
                                    <span class="text-danger">
                                        @error('email')
                                            {{$message}}
                                        @enderror
                                    </span>

                            </div>
                            <div class="form-group mb-3">
                                <label for="name" class="form-label">Phone</label>
                                <input type="number"
                                    class="form-control"
                                    name="phone" id="cnic" value="{{old('phone')}}">
                                    <span class="text-danger">
                                        @error('phone')
                                            {{$message}}
                                        @enderror
                                    </span>

                            </div>


                            <button type="submit" class="btn btn-primary">Add Employee</button>
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



