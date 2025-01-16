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
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label for="employee_search">Select Employee</label>
                                        <input type="text" name="employee_search" class="form-control employee_search" placeholder="Search Employee" required>
                                        <input type="hidden" name="employee_id[]" class="employee_id">
                                        <div class="employee_list"></div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="labour_id">Select Labour Type</label>
                                        <select name="labour_id[]" id="labour_id" class="form-select" required>
                                            <option value="" disabled selected>Select Labour Type</option>
                                            @foreach($labours as $labour)
                                                <option value="{{ $labour->id }}">{{ $labour->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="row">

                                <div class="form-group mt-4 col-md-4">
                                    <label for="date">Date</label>
                                    <input type="date" name="date[]" class="form-control date-input" required>
                                    <span class="text-danger date-error">
                                        @error('date') {{ $message }} @enderror
                                    </span>
                                </div>
                                <div class="form-group mt-4 col-md-4">
                                    <label for="check_in_time">Check In Time</label>
                                    <input type="time" name="check_in_time[]" class="form-control check-in-time-input" required>
                                    <span class="text-danger">
                                        @error('check_in_time') {{$message}} @enderror
                                    </span>
                                </div>

                                <div class="form-group mt-4 col-md-4">

                                    <label for="check_out_time">Check Out Time</label>
                                    <input type="time" name="check_out_time[]" class="form-control check-out-time-input" required>
                                    <span class="text-danger">
                                        @error('check_out_time') {{$message}} @enderror
                                    </span>
                                </div>
                                </div>
                                <div class="row">
                                    <div class="form-group mt-4 col-md-6">
                                        <label for="rate">Per Hour Wage Rate</label>
                                        <input type="number" name="rate[]" class="form-control rate-input" step="0.01" min="0" required>
                                        <span class="text-danger">
                                            @error('rate') {{$message}} @enderror
                                        </span>
                                    </div>


                                    <div class="form-group mt-4 col-md-6">
                                        <label for="break_time">Break Time (minutes)</label>
                                        <input type="number" name="break_time[]" class="form-control break-time-input" step="1" min="0" required>
                                        <span class="text-danger">
                                            @error('break_time') {{$message}} @enderror
                                        </span>
                                    </div>

                                </div>



                            </div>
                        </div>

                        <div id="error_message" class="text-danger mb-3"></div>

                        <button type="button" class="btn btn-success add-row">+</button>
                        &nbsp;&nbsp;&nbsp;&nbsp;
                        <button type="submit" class="btn btn-primary">Submit</button>
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

    // function checkDuplicateDates() {
    //     var dates = [];
    //     var duplicate = false;

    //     $('.date-input').each(function () {
    //         var date = $(this).val();
    //         if (date) {
    //             if (dates.includes(date)) {
    //                 duplicate = true;
    //                 return false;
    //             }
    //             dates.push(date);
    //         }
    //     });

    //     return duplicate;
    // }

    // $(document).on('change', '.date-input', function () {
    //     if (checkDuplicateDates()) {
    //         $('#error_message').text('You cannot select the same date more than once.');
    //         $(this).val('');
    //     } else {
    //         $('#error_message').text('');
    //     }
    // });

    setEmployeeSearchEvent();

    $('.add-row').on('click', function () {
        var newRow = `
            <div class="employee-group">
<div class="row">
                                    <div class="form-group mt-4 col-md-6">
                                        <label for="employee_search">Select Employee</label>
                                        <input type="text" name="employee_search" class="form-control employee_search" placeholder="Search Employee" required>
                                        <input type="hidden" name="employee_id[]" class="employee_id">
                                        <div class="employee_list"></div>
                                    </div>
                                    <div class="form-group mt-4 col-md-6">
                                        <label for="labour_id">Select Labour Type</label>
                                        <select name="labour_id[]" id="labour_id" class="form-select" required>
                                            <option value="" disabled selected>Select Labour Type</option>
                                            @foreach($labours as $labour)
                                                <option value="{{ $labour->id }}">{{ $labour->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="row">

                                <div class="form-group mt-4 col-md-4">
                                    <label for="date">Date</label>
                                    <input type="date" name="date[]" class="form-control date-input" required>
                                    <span class="text-danger date-error">
                                        @error('date') {{ $message }} @enderror
                                    </span>
                                </div>
                                <div class="form-group mt-4 col-md-4">
                                    <label for="check_in_time">Check In Time</label>
                                    <input type="time" name="check_in_time[]" class="form-control check-in-time-input" required>
                                    <span class="text-danger">
                                        @error('check_in_time') {{$message}} @enderror
                                    </span>
                                </div>

                                <div class="form-group mt-4 col-md-4">

                                    <label for="check_out_time">Check Out Time</label>
                                    <input type="time" name="check_out_time[]" class="form-control check-out-time-input" required>
                                    <span class="text-danger">
                                        @error('check_out_time') {{$message}} @enderror
                                    </span>
                                </div>
                                </div>
                                <div class="row mb-4">
                                    <div class="form-group mt-4 col-md-6">
                                        <label for="rate">Per Hour Wage Rate</label>
                                        <input type="number" name="rate[]" class="form-control rate-input" step="0.01" min="0" required>
                                        <span class="text-danger">
                                            @error('rate') {{$message}} @enderror
                                        </span>
                                    </div>


                                    <div class="form-group mt-4 col-md-6">
                                        <label for="break_time">Break Time (minutes)</label>
                                        <input type="number" name="break_time[]" class="form-control break-time-input" step="1" min="0" required>
                                        <span class="text-danger">
                                            @error('break_time') {{$message}} @enderror
                                        </span>
                                    </div>

                                </div>
                <button type="button" class="btn btn-danger remove-row">-</button>

            </div>
        `;

        var $employeeContainer = $('#employeeContainer');
        $employeeContainer.append(newRow);

        setEmployeeSearchEvent();

        // Copy data from the latest row to the new row
        var latestRow = $employeeContainer.children('.employee-group').last().prev();
        if (latestRow.length > 0) {
            var latestRate = latestRow.find('.rate-input').val();
            var latestCheckInTime = latestRow.find('.check-in-time-input').val();
            var latestCheckOutTime = latestRow.find('.check-out-time-input').val();
            var latestBreakTime = latestRow.find('.break-time-input').val();

            $employeeContainer.find('.employee-group').last().find('.rate-input').val(latestRate);
            $employeeContainer.find('.employee-group').last().find('.check-in-time-input').val(latestCheckInTime);
            $employeeContainer.find('.employee-group').last().find('.check-out-time-input').val(latestCheckOutTime);
            $employeeContainer.find('.employee-group').last().find('.break-time-input').val(latestBreakTime);
        }
    });

    $(document).on('click', '.remove-row', function () {
        $(this).closest('.employee-group').remove();
        if (checkDuplicateDates()) {
            $('#error_message').text('You cannot select the same date more than once.');
        } else {
            $('#error_message').text('');
        }
    });
});
</script>
@endsection
