@extends('admin.admin_dashboard')
@section('admin')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <div class="page-content">

        <div class="col-md-8 col-xl-8 middle-wrapper">
            <div class="row">
                <div class="card">
                    <div class="card-body">

                        <h4 class="card-title">Edit Employee</h4>

                        <form action="{{ route('update.employee', $employee->id) }}" method="post" class="forms-sample">
                            @csrf
                            @method('PUT')
                            <div class="form-group mb-3">
                                <label for="name" class="form-label">Employee Name</label>
                                <input type="text"
                                    class="form-control"
                                    name="employee_name" id="name" value="{{ old('employee_name', $employee->name) }}">
                                    <span class="text-danger">
                                        @error('employee_name')
                                            {{$message}}
                                        @enderror
                                    </span>

                            </div>
                            <div class="form-group mb-3">
                                <label for="name" class="form-label">Email</label>
                                <input type="disabled"
                                    class="form-control"
                                    name="email" id="cnic" value="{{ old('email', $employee->email) }}" pattern="\d{13}" placeholder="3310011122222" maxlength="13" disabled>
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
                                    name="phone" id="phone" value="{{ old('phone', $employee->phone) }}" disabled>
                                    <span class="text-danger">
                                        @error('phone')
                                            {{$message}}
                                        @enderror
                                    </span>

                            </div>
                            
                            <button type="submit" class="btn btn-primary">Update Employee</button>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
