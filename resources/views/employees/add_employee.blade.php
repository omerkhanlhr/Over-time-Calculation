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
                                <label for="name" class="form-label">Enter CNIC</label>
                                <input type="text"
                                    class="form-control"
                                    name="employee_cnic" id="cnic" value="{{old('employee_cnic')}}" pattern="\d{13}" placeholder="3310011122222" maxlength="13">
                                    <span class="text-danger">
                                        @error('employee_cnic')
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
                            <div class="form-group mb-3 mt-3">
                                <label for="invoice_id">Select Designation</label>
                                <select name="designation_id" class="form-select" required onchange="updatePayments()">
                                    <option value="" disabled selected>Select Designation</option>
                                    @foreach($designations as $designation)
                                            <option value="{{ $designation->id }}">{{ $designation->position }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group mb-3 mt-3">
                                <label for="payment_date">Joining Date</label>
                                <input type="date" name="joining_date" class="form-control" required>
                                <span class="text-danger">
                                    @error('joining_date')
                                    {{$message}}
                                    @enderror
                                </span>
                            </div>
                            <div class="form-group mb-3">
                                <label for="amount">Base Salary</label>
                                <input type="number" name="base_salary" class="form-control" step="0.01" min="0" required>
                                <span class="text-danger">
                                    @error('base_salary')
                                    {{$message}}
                                    @enderror
                                </span>
                            </div>
                            <div class="form-group mb-3">
                                <label for="amount">Commision</label>
                                <input type="number" name="commission" class="form-control" step="0.01" min="0" required>
                                <span class="text-danger">
                                    @error('commision')
                                    {{$message}}
                                    @enderror
                                </span>
                            </div>
                            <div class="form-group mb-3">
                                <label for="amount">Rent</label>
                                <input type="number" name="rent" class="form-control" step="0.01" min="0" required>
                                <span class="text-danger">
                                    @error('rent')
                                    {{$message}}
                                    @enderror
                                </span>
                            </div>
                            <div class="form-group mb-3">
                                <label for="amount">Allowance</label>
                                <input type="number" name="allowance" class="form-control" step="0.01" min="0" required>
                                <span class="text-danger">
                                    @error('allowance')
                                    {{$message}}
                                    @enderror
                                </span>
                            </div>
                            <div class="form-group mb-3">
                                <label for="amount">Deduction</label>
                                <input type="number" name="deduction" class="form-control" step="0.01" min="0" required>
                                <span class="text-danger">
                                    @error('deduction')
                                    {{$message}}
                                    @enderror
                                </span>
                            </div>

                            <div class="form-group mb-3">
                                <label for="salary_month">Select Salary Month</label>
                                <input type="month" name="salary_month" class="form-control" value="{{ old('salary_month') }}" required>
                                <span class="text-danger">
                                    @error('salary_month')
                                        {{ $message }}
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



