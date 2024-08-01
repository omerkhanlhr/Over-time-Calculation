@extends('admin.admin_dashboard')
@section('admin')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<div class="page-content">
    <div class="col-md-8 col-xl-8 middle-wrapper">
        <div class="row">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('store.work.hours') }}" method="post" class="forms-sample">
                        @csrf
                        <div class="form-group mb-3 mt-3">
                            <label for="client_search">Select Client</label>
                            <input type="text" name="client_search" id="client_search" class="form-control" placeholder="Search Client" required>
                            <input type="hidden" name="client_id" id="client_id">
                            <div id="client_list"></div>
                        </div>

                        <div id="employeeContainer">
                            <div class="employee-group">
                                <div class="form-group mb-3 mt-3">
                                    <label for="employee_search">Select Employee</label>
                                    <input type="text" name="employee_search[]" class="form-control employee_search" placeholder="Search Employee" required>
                                    <input type="hidden" name="employee_id[]" class="employee_id">
                                    <div class="employee_list"></div>
                                </div>

                                <div class="form-group mb-3 mt-3">
                                    <label for="date">Date</label>
                                    <input type="date" name="date[]" class="form-control" required>
                                    <span class="text-danger">
                                        @error('date') {{ $message }} @enderror
                                    </span>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="rate">Per Hour Wage Rate</label>
                                    <input type="number" name="rate[]" class="form-control" step="0.01" min="0" required>
                                    <span class="text-danger">
                                        @error('rate') {{$message}} @enderror
                                    </span>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="check_in_time">Check In Time</label>
                                    <input type="time" name="check_in_time[]" class="form-control" required>
                                    <span class="text-danger">
                                        @error('check_in_time') {{$message}} @enderror
                                    </span>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="check_out_time">Check Out Time</label>
                                    <input type="time" name="check_out_time[]" class="form-control" required>
                                    <span class="text-danger">
                                        @error('check_out_time') {{$message}} @enderror
                                    </span>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="break_time">Break Time (minutes)</label>
                                    <input type="number" name="break_time[]" class="form-control" step="1" min="0" required>
                                    <span class="text-danger">
                                        @error('break_time') {{$message}} @enderror
                                    </span>
                                </div>

                                <button type="button" class="btn btn-danger remove-row">Remove Row</button>
                                <hr>
                            </div>
                        </div>

                        <button type="button" class="btn btn-success add-row">Add Employee</button>
                        <button type="submit" class="btn btn-primary">Add Work Hours</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    var client_id = null;
    var client_name = null;

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
        client_id = $(this).val();
        client_name = $(this).next('label').text();
        $('#client_search').val(client_name);
        $('#client_id').val(client_id);
        $('#client_list').empty();
    });

    function setEmployeeSearchEvent() {
        $('.employee_search').off('keyup').on('keyup', function () {
            var query = $(this).val();
            var $this = $(this);
            var $list = $this.nextAll('.employee_list').first();
            if (query.length > 0) {
                $.ajax({
                    url: "{{ route('search.employees') }}",
                    type: "GET",
                    data: {'query': query},
                    success: function (data) {
                        $list.empty();
                        if (data.length > 0) {
                            var list = '<div class="form-group">';
                            $.each(data, function (index, employee) {
                                list += '<div class="form-check">';
                                list += '<input class="form-check-input employee-radio" type="radio" name="employee_radio" id="employee' + employee.id + '" value="' + employee.id + '">';
                                list += '<label class="form-check-label" for="employee' + employee.id + '">' + employee.name + '</label>';
                                list += '</div>';
                            });
                            list += '</div>';
                            $list.html(list);
                        } else {
                            $list.html('<p class="text-danger">No employees found</p>');
                        }
                    }
                });
            } else {
                $list.empty();
            }
        });

        $(document).on('click', '.employee-radio', function () {
            var id = $(this).val();
            var name = $(this).next('label').text();
            var $searchInput = $(this).closest('.form-check').parent().parent().prevAll('.employee_search');
            $searchInput.val(name);
            $searchInput.nextAll('.employee_id').first().val(id);
            $(this).closest('.employee_list').empty();
        });
    }

    setEmployeeSearchEvent();

    $('.add-row').on('click', function () {
        var newRow = `
            <div class="employee-group">
                <div class="form-group mb-3 mt-3">
                    <label for="employee_search">Select Employee</label>
                    <input type="text" name="employee_search[]" class="form-control employee_search" placeholder="Search Employee" required>
                    <input type="hidden" name="employee_id[]" class="employee_id">
                    <div class="employee_list"></div>
                </div>

                <div class="form-group mb-3 mt-3">
                    <label for="date">Date</label>
                    <input type="date" name="date[]" class="form-control" required>
                    <span class="text-danger">
                        @error('date') {{ $message }} @enderror
                    </span>
                </div>

                <div class="form-group mb-3">
                    <label for="rate">Per Hour Wage Rate</label>
                    <input type="number" name="rate[]" class="form-control" step="0.01" min="0" required>
                    <span class="text-danger">
                        @error('rate') {{$message}} @enderror
                    </span>
                </div>

                <div class="form-group mb-3">
                    <label for="check_in_time">Check In Time</label>
                    <input type="time" name="check_in_time[]" class="form-control" required>
                    <span class="text-danger">
                        @error('check_in_time') {{$message}} @enderror
                    </span>
                </div>

                <div class="form-group mb-3">
                    <label for="check_out_time">Check Out Time</label>
                    <input type="time" name="check_out_time[]" class="form-control" required>
                    <span class="text-danger">
                        @error('check_out_time') {{$message}} @enderror
                    </span>
                </div>

                <div class="form-group mb-3">
                    <label for="break_time">Break Time (minutes)</label>
                    <input type="number" name="break_time[]" class="form-control" step="1" min="0" required>
                    <span class="text-danger">
                        @error('break_time') {{$message}} @enderror
                    </span>
                </div>

                <button type="button" class="btn btn-danger remove-row">Remove Row</button>
                <hr>
            </div>
        `;
        $('#employeeContainer').append(newRow);
        setEmployeeSearchEvent();
    });

    $(document).on('click', '.remove-row', function () {
        $(this).closest('.employee-group').remove();
    });
});
</script>
@endsection
