@extends('admin.admin_dashboard')
@section('admin')

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>

<div class="page-content">
    <div class="col-md-8 col-xl-8 middle-wrapper">
        <div class="row">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('store.forecasting.employees') }}" method="post" class="forms-sample">
                        @csrf

                        <!-- Client Selection -->
                        <div class="form-group mb-3">
                            <label for="client_search">Select Client</label>
                            <input type="text" name="client_search" id="client_search" class="form-control" placeholder="Search Client" required>
                            <input type="hidden" name="client_id" id="client_id">
                            <div id="client_list"></div>
                        </div>

                        <!-- Date Selection -->
                        <div class="form-group mb-3">
                            <label for="date">Select Date</label>
                            <input type="date" name="date" id="date" class="form-control" required>
                        </div>

                        <!-- Employee Selection -->
                        <div id="employeeContainer">
                            <div class="employee-group">
                                <div class="form-group mb-3">
                                    <label for="employee_search">Select Employee</label>
                                    <input type="text" name="employee_search" class="form-control employee_search" placeholder="Search Employee" required>
                                    <input type="hidden" name="employee_id[]" class="employee_id">
                                    <div class="employee_list"></div>
                                </div>
                            </div>
                        </div>

                        <button type="button" class="btn btn-success add-row">+ Add Employee</button>
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

    $(document).on('click', '.client-radio', function () {
        $('#client_search').val($(this).next('label').text());
        $('#client_id').val($(this).val());
        $('#client_list').empty();
    });

    // Function for employee search
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

    // Add more employee rows
    $('.add-row').on('click', function () {
        var newRow = `
            <div class="employee-group mt-3">
                <div class="form-group">
                    <label for="employee_search">Select Employee</label>
                    <input type="text" name="employee_search" class="form-control employee_search" placeholder="Search by Name or ID" required>
                    <input type="hidden" name="employee_id[]" class="employee_id">
                    <div class="employee_list"></div>
                </div>
                <button type="button" class="btn btn-danger remove-row">Remove</button>
            </div>
        `;

        $('#employeeContainer').append(newRow);
        setEmployeeSearchEvent();
    });

    // Remove employee row
    $(document).on('click', '.remove-row', function () {
        $(this).closest('.employee-group').remove();
    });
});
</script>

@endsection
