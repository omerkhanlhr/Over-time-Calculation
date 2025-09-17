@extends('admin.admin_dashboard')
@section('admin')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>

<div class="page-content">
    <div class="col-md-12">
        <div class="row">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('store.work.hours') }}" method="post">
                        @csrf
                        <div class="form-group mb-3 mt-3">
                            <label for="client_search">Select Client</label>
                            <input type="text" name="client_search" id="client_search" class="form-control" placeholder="Search Client" style="width: 50%" required>
                            <input type="hidden" name="client_id" id="client_id">
                            <div id="client_list"></div>
                        </div>


                        <table class="table table-bordered" id="workHourTable">
                            <thead>
                                <tr>
                                    <th>Employee</th>
                                    <th>Labour Type</th>
                                    <th>Date</th>
                                    <th>Check In</th>
                                    <th>Check Out</th>
                                    <th>Hours</th>
                                    <th>Minutes</th>
                                    <th>Rate</th>
                                    <th>Break Time</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="work-hour-row">
                                    <td>

                                            <input type="text" name="employee_search" class="form-control employee_search" placeholder="Search Employee" required>
                                            <input type="hidden" name="employee_id[]" class="employee_id">
                                            <div class="employee_list"></div>


                                    </td>
                                    <td>
                                        <select name="labour_id[]" class="form-select labour-type-input" required>
                                            <option value="" disabled selected>Select Labour Type</option>
                                            @foreach($labours as $labour)
                                                <option value="{{ $labour->id }}">{{ $labour->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td><input type="date" name="date[]" class="form-control date-input" required></td>
                                    <td><input type="time" name="check_in_time[]" class="form-control check-in-time-input"></td>
                                    <td><input type="time" name="check_out_time[]" class="form-control check-out-time-input"></td>
                                    <td>
                                        <select name="hours[]" class="form-select">
                                            <option value="" selected>Select Hours</option>
                                            @for ($i = 0; $i <= 23; $i++)
                                                <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}">{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}</option>
                                            @endfor
                                        </select>
                                    </td>
                                    <td>
                                        <select name="minutes[]" class="form-select">
                                            <option value="" selected>Select Minutes</option>
                                            @for ($i = 0; $i <= 59; $i++)
                                                <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}">{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}</option>
                                            @endfor
                                        </select>
                                    </td>
                                    <td><input type="number" name="rate[]" class="form-control rate-input" step="0.01" min="0" required></td>
                                    <td><input type="number" name="break_time[]" class="form-control break-time-input" step="1" min="0" required></td>
                                </tr>
                            </tbody>
                        </table>
                        <div id="error_message" class="text-danger mb-3"></div>
                        <button type="button" class="btn btn-success add-row mb-2 mt-2">+</button>
                        &nbsp;&nbsp;&nbsp;&nbsp;
                        <button type="submit" class="btn btn-primary mb-2 mt-2">Submit</button>
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

    $(document).ready(function () {
    function validateTimes() {
        let isValid = true;
        $('#error_message').text(''); // Clear error message
        $('.work-hour-row').each(function (index, group) {
            const checkInTime = $(group).find('.check-in-time-input').val();
            const checkOutTime = $(group).find('.check-out-time-input').val();
            const hours = $(group).find('select[name="hours[]"]').val();
            const minutes = $(group).find('select[name="minutes[]"]').val();

            if ((checkInTime || checkOutTime) && (hours || minutes)) {
                $('#error_message').text('You can select either check-in/out times or hours/minutes, not both.');
                isValid = false;
                return false; // Break the loop
            }
        });
        return isValid;
    }

    // Validate on form submission
    $('form').on('submit', function (e) {
        if (!validateTimes()) {
            e.preventDefault(); // Prevent form submission
        }
    });

    // Validate on input change
    $(document).on('change', '.check-in-time-input, .check-out-time-input, select[name="hours[]"], select[name="minutes[]"]', function () {
        validateTimes();
    });
});

 setEmployeeSearchEvent();

 $('.add-row').on('click', function () {
    var newRow = `
        <tr class="work-hour-row">
            <td>
                <input type="text" name="employee_search[]" class="form-control employee_search" placeholder="Search Employee">
                <input type="hidden" name="employee_id[]" class="employee_id">
                <div class="employee_list"></div>
            </td>
            <td>
                <select name="labour_id[]" class="form-select labour-type-input" required>
                    <option value="" disabled selected>Select Labour Type</option>
                    @foreach($labours as $labour)
                        <option value="{{ $labour->id }}">{{ $labour->name }}</option>
                    @endforeach
                </select>
            </td>
            <td><input type="date" name="date[]" class="form-control date-input" required></td>
            <td><input type="time" name="check_in_time[]" class="form-control check-in-time-input"></td>
            <td><input type="time" name="check_out_time[]" class="form-control check-out-time-input"></td>
            <td>
                <select name="hours[]" class="form-select">
                    <option value="" selected>Select Hours</option>
                    @for ($i = 0; $i <= 23; $i++)
                        <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}">{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}</option>
                    @endfor
                </select>
            </td>
            <td>
                <select name="minutes[]" class="form-select">
                    <option value="" selected>Select Minutes</option>
                    @for ($i = 0; $i <= 59; $i++)
                        <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}">{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}</option>
                    @endfor
                </select>
            </td>
            <td><input type="number" name="rate[]" class="form-control rate-input" step="0.01" min="0" required></td>
            <td><input type="number" name="break_time[]" class="form-control break-time-input" step="1" min="0" required></td>
            <td><button type="button" class="btn btn-danger remove-row">-</button></td>
        </tr>
    `;

    var $tableBody = $('#workHourTable tbody');
    $tableBody.append(newRow);

    setEmployeeSearchEvent();

    // Copy data from the last row
    var lastRow = $tableBody.find('.work-hour-row').last().prev();
    if (lastRow.length > 0) {
        var lastRate = lastRow.find('.rate-input').val();
        var lastCheckIn = lastRow.find('.check-in-time-input').val();
        var lastCheckOut = lastRow.find('.check-out-time-input').val();
        var lastBreak = lastRow.find('.break-time-input').val();
        var lastdate = lastRow.find('.date-input').val();
        var lastlabourtype = lastRow.find('.labour-type-input').val();

        $tableBody.find('.work-hour-row').last().find('.rate-input').val(lastRate);
        $tableBody.find('.work-hour-row').last().find('.check-in-time-input').val(lastCheckIn);
        $tableBody.find('.work-hour-row').last().find('.check-out-time-input').val(lastCheckOut);
        $tableBody.find('.work-hour-row').last().find('.break-time-input').val(lastBreak);
        $tableBody.find('.work-hour-row').last().find('.date-input').val(lastdate);
        $tableBody.find('.work-hour-row').last().find('.labour-type-input').val(lastlabourtype);
    }

    // Ensure only rows after the first one have a remove button
    $('.remove-row').show();
    $tableBody.find('.work-hour-row').first().find('.remove-row').hide();
});

// Remove row functionality
$(document).on('click', '.remove-row', function () {
    $(this).closest('.work-hour-row').remove();
    if ($('#workHourTable tbody .work-hour-row').length === 1) {
        $('.remove-row').hide();
    }
});





});
</script>
@endsection
