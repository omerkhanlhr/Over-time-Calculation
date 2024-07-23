@extends('admin.admin_dashboard')
@section('admin')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <div class="page-content">

        <div class="col-md-8 col-xl-8 middle-wrapper">
            <div class="row">
                <div class="card">
                    <div class="card-body">
                        <form action="" method="post" class="forms-sample">
                            @csrf

                            <div class="form-group mb-3 mt-3">
                                <label for="designation_id">Select Client</label>
                                <select name="client_id" class="form-select" id="client_id" required>
                                    <option value="" disabled selected>Select Client</option>
                                    @foreach($clients as $client)
                                        <option value="{{ $client->id }}">{{ $client->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group mb-3 mt-3">
                                <label for="employee_id">Select Employee</label>
                                <select name="employee_id" class="form-select" id="employee_id" required>
                                    <option value="" disabled selected>Select Employee</option>
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group mb-3 mt-3">
                                <label for="promotion_date">Date</label>
                                <input type="date" name="date" class="form-control" required>
                                <span class="text-danger">
                                    @error('date') {{ $message }} @enderror
                                </span>
                            </div>

                            <div class="form-group mb-3">
                                <label for="amount">Per Hour Wage Rate</label>
                                <input type="number" name="rate" id="rate" class="form-control" step="0.01" min="0" required>
                                <span class="text-danger">
                                    @error('rate')
                                    {{$message}}
                                    @enderror
                                </span>
                            </div>
                            <div class="form-group mb-3">
                                <label for="amount">Check In time</label>
                                <input type="time" name="check_in_time" id="check_in_time" class="form-control"  required>
                                <span class="text-danger">
                                    @error('check_in_time')
                                    {{$message}}
                                    @enderror
                                </span>
                            </div>
                            <div class="form-group mb-3">
                                <label for="amount">Check Out time</label>
                                <input type="time" name="check_out_time" id="check_out_time" class="form-control"  required>
                                <span class="text-danger">
                                    @error('check_out_time')
                                    {{$message}}
                                    @enderror
                                </span>
                            </div>


                            <button type="submit" class="btn btn-primary">Add Work Hours</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
