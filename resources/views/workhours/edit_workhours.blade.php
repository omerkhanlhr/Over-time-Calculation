@extends('admin.admin_dashboard')
@section('admin')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<div class="page-content">

    <div class="col-md-8 col-xl-8 middle-wrapper">
        <div class="row">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('update.work.hours', $workhour->id) }}" method="post" class="forms-sample">
                        @csrf
                        @method('PUT')

                        <div class="form-group mb-3 mt-3">
                            <label for="client_search">Select Client</label>
                            <input type="text" name="client_search" id="client_search" class="form-control" placeholder="Search Client" value="{{ $workhour->client->name }}" required>
                            <input type="hidden" name="client_id" id="client_id" value="{{ $workhour->client_id }}">
                            <div id="client_list"></div>
                        </div>

                        <div class="form-group mb-3 mt-3">
                            <label for="employee_search">Select Employee</label>
                            <input type="text" name="employee_search" id="employee_search" class="form-control" placeholder="Search Employee" value="{{ $workhour->employee->name }}" required>
                            <input type="hidden" name="employee_id" id="employee_id" value="{{ $workhour->employee_id }}">
                            <div id="employee_list"></div>
                        </div>

                        <div class="form-group mb-3 mt-3">
                            <label for="promotion_date">Date</label>
                            <input type="date" name="date" class="form-control" value="{{ $workhour->work_date }}" required>
                            <span class="text-danger">
                                @error('date') {{ $message }} @enderror
                            </span>
                        </div>

                        <div class="form-group mb-3">
                            <label for="rate">Per Hour Wage Rate</label>
                            <input type="number" name="rate" id="rate" class="form-control" step="0.01" min="0" value="{{ $workhour->rate}}" required>
                            <span class="text-danger">
                                @error('rate')
                                {{$message}}
                                @enderror
                            </span>
                        </div>
                        <div class="form-group mb-3">
                            <label for="check_in_time">Check In Time</label>
                            <input type="time" name="check_in_time" id="check_in_time" class="form-control" value="{{ $workhour->start_time }}" required>
                            <span class="text-danger">
                                @error('check_in_time')
                                {{$message}}
                                @enderror
                            </span>
                        </div>
                        <div class="form-group mb-3">
                            <label for="check_out_time">Check Out Time</label>
                            <input type="time" name="check_out_time" id="check_out_time" class="form-control" value="{{ $workhour->end_time }}" required>
                            <span class="text-danger">
                                @error('check_out_time')
                                {{$message}}
                                @enderror
                            </span>
                        </div>

                        <button type="submit" class="btn btn-primary">Update Work Hours</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('#client_search').on('keyup', function () {
            var query = $(this).val();
            if (query.length > 0) {
                $.ajax({
                    url: "{{ route('search.clients') }}",
                    type: "GET",
                    data: {'query': query},
                    success: function (data) {
                        $('#client_list').empty();
                        if (data.length > 0) {
                            var list = '<div class="form-group">';
                            $.each(data, function (index, client) {
                                list += '<div class="form-check">';
                                list += '<input class="form-check-input client-radio" type="radio" name="client_radio" id="client' + client.id + '" value="' + client.id + '">';
                                list += '<label class="form-check-label" for="client' + client.id + '">' + client.name + '</label>';
                                list += '</div>';
                            });
                            list += '</div>';
                            $('#client_list').html(list);
                        } else {
                            $('#client_list').html('<p class="text-danger">No clients found</p>');
                        }
                    }
                });
            } else {
                $('#client_list').empty();
            }
        });

        $(document).on('click', '.client-radio', function () {
            var id = $(this).val();
            var name = $(this).next('label').text();
            $('#client_search').val(name);
            $('#client_id').val(id);
            $('#client_list').empty();
        });

        $('#employee_search').on('keyup', function () {
            var query = $(this).val();
            if (query.length > 0) {
                $.ajax({
                    url: "{{ route('search.employees') }}",
                    type: "GET",
                    data: {'query': query},
                    success: function (data) {
                        $('#employee_list').empty();
                        if (data.length > 0) {
                            var list = '<div class="form-group">';
                            $.each(data, function (index, employee) {
                                list += '<div class="form-check">';
                                list += '<input class="form-check-input employee-radio" type="radio" name="employee_radio" id="employee' + employee.id + '" value="' + employee.id + '">';
                                list += '<label class="form-check-label" for="employee' + employee.id + '">' + employee.name + '</label>';
                                list += '</div>';
                            });
                            list += '</div>';
                            $('#employee_list').html(list);
                        } else {
                            $('#employee_list').html('<p class="text-danger">No employees found</p>');
                        }
                    }
                });
            } else {
                $('#employee_list').empty();
            }
        });

        $(document).on('click', '.employee-radio', function () {
            var id = $(this).val();
            var name = $(this).next('label').text();
            $('#employee_search').val(name);
            $('#employee_id').val(id);
            $('#employee_list').empty();
        });
    });
</script>
@endsection
