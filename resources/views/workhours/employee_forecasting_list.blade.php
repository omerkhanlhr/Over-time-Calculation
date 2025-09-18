@extends('admin.admin_dashboard')
@section('admin')

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>

<div class="page-content">
    <div class="col-md-12 col-xl-12 middle-wrapper">
        <div class="row">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('store.forecasting.workhour') }}" method="post" class="forms-sample">
                        @csrf

                        <!-- Client Selection -->
                        <div class="form-group mb-3">
                            <label for="client_search">Select Client</label>
                            <input type="text" name="client_search" id="client_search" class="form-control" placeholder="Search Client" required>
                            <input type="hidden" name="client_id" id="client_select">
                            <div id="client_list"></div>
                        </div>

                        <!-- Date Selection -->
                        <div class="form-group mb-3">
                            <label for="date">Select Date</label>
                            <input type="date" name="date"  id="work_date" class="form-control" required>
                        </div>


<div id="employee_list" class="mt-3">
    <!-- Assigned Employees will be shown here -->
</div>
<button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    // Live Search for Client
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
                            list += `<div class="form-check">
                                        <input class="form-check-input client-radio" type="radio" name="client_radio" value="${client.id}">
                                        <label class="form-check-label">${client.name}</label>
                                    </div>`;
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

    // Select client and set ID
    $(document).on('click', '.client-radio', function () {
        $('#client_search').val($(this).next('label').text());
        $('#client_select').val($(this).val()); // Fix: Set the correct client ID
        $('#client_list').empty();
        fetchEmployees(); // Fetch employees automatically after client selection
    });

    // Fetch employees when client or date changes
    $('#work_date').on('change', function () {
        fetchEmployees();
    });

    // Function to fetch employees
    function fetchEmployees() {
        var client_id = $('#client_select').val();
        var work_date = $('#work_date').val();

        if (client_id && work_date) {
            $.ajax({
                url: "{{ route('fetch.assigned.employees') }}",
                type: "GET",
                data: { client_id: client_id, work_date: work_date },
                success: function (response) {
                    var employeeList = $('#employee_list');
                    employeeList.empty();

                    if (response.length > 0) {
                        var list = '<table class="table"><thead><tr><th>Employee</th><th>Check-In</th><th>Check-Out</th><th>Break Time</th><th>Rate</th></tr></thead><tbody>';
                        $.each(response, function(index, employee) {
                            list += `<tr>
                                <td>${employee.name} (ID: ${employee.id}) <input type="hidden" name="employees[${employee.id}][id]" value="${employee.id}"></td>
                                <td><input type="time" name="employees[${employee.id}][check_in]" class="form-control"></td>
                                <td><input type="time" name="employees[${employee.id}][check_out]" class="form-control"></td>
                                <td><input type="number" name="employees[${employee.id}][break_time]" class="form-control" placeholder="Break (min)"></td>
                                <td><input type="text" name="employees[${employee.id}][rate]" class="form-control" placeholder="Rate"></td>
                            </tr>`;
                        });
                        list += '</tbody></table>';
                        employeeList.html(list);
                    } else {
                        employeeList.html('<p class="text-danger">No employees assigned for this date and client.</p>');
                    }
                },
                error: function() {
                    alert("Error fetching employees.");
                }
            });
        }
    }
});

</script>

@endsection
