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
                                    name="employee_name" id="name" value="{{ old('employee_name', $employee->emp_name) }}">
                                    <span class="text-danger">
                                        @error('employee_name')
                                            {{$message}}
                                        @enderror
                                    </span>

                            </div>
                            <div class="form-group mb-3">
                                <label for="name" class="form-label">Enter CNIC</label>
                                <input type="disabled"
                                    class="form-control"
                                    name="employee_cnic" id="cnic" value="{{ old('employee_cnic', $employee->cnic) }}" pattern="\d{13}" placeholder="3310011122222" maxlength="13" disabled>
                                    <span class="text-danger">
                                        @error('employee_cnic')
                                            {{$message}}
                                        @enderror
                                    </span>

                            </div>
                            <div class="form-group mb-3">
                                <label for="name" class="form-label">Email</label>
                                <input type="disabled"
                                    class="form-control"
                                    name="email" id="email" value="{{ old('email', $employee->email) }}" disabled>
                                    <span class="text-danger">
                                        @error('email')
                                            {{$message}}
                                        @enderror
                                    </span>

                            </div>
                            <div class="form-group mb-3 mt-3">
                                <label for="designation_id">Select Designation</label>
                                <select name="designation_id" class="form-select" required>
                                    <option value="" disabled>Select Designation</option>
                                    @foreach($designations as $designation)
                                            <option value="{{ $designation->id }}" {{ $designation->id == $employee->designation_id ? 'selected' : '' }}>{{ $designation->position }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group mb-3 mt-3">
                                <label for="joining_date">Joining Date</label>
                                <input type="date" name="joining_date" class="form-control" value="{{ old('joining_date', $employee->joining_date) }}" required>
                                <span class="text-danger">
                                    @error('joining_date')
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
