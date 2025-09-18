@extends('admin.admin_dashboard')
@section('admin')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<div class="page-content">

    <div class="col-md-8 col-xl-8 middle-wrapper">
        <div class="row">
            <div class="card">
                <div class="card-body">

                    <h4 class="card-title">Add Employee</h4>

                    <form action="{{ route('save.employee') }}" method="post" class="forms-sample" enctype="multipart/form-data">
                        @csrf
                        <div id="employee-container">
                            <div class="employee-group">
                                <div class="row">
                                    <div class="form-group mb-3 col-md-6">
                                        <label for="name" class="form-label">Employee Name</label>
                                        <input type="text" class="form-control" name="employee_name[]" value="">
                                    </div>
                                    <div class="form-group mb-3 col-md-6">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" name="email[]" value="">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group mb-3 col-md-6">
                                        <label for="phone" class="form-label">Phone</label>
                                        <input type="number" class="form-control" name="phone[]" value="">
                                    </div>
                                    <div class="form-group mb-3 col-md-6">
                                        <label for="address" class="form-label">Address</label>
                                        <input type="text" class="form-control" name="address[]" value="">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group mb-3 col-md-6">
                                        <label for="sin" class="form-label">Social Insurance Number</label>
                                        <input type="text" class="form-control" name="sin[]" value="">
                                    </div>
                                   
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-success add-row">+</button>
                        &nbsp;&nbsp;&nbsp;&nbsp;
                        <button type="submit" class="btn btn-primary">Add Employee</button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $('.add-row').on('click', function () {
        var newRow = `
        <div class="employee-group">
            <div class="row">
                <div class="form-group mb-3 col-md-6">
                    <label class="form-label">Employee Name</label>
                    <input type="text" class="form-control" name="employee_name[]" value="">
                </div>
                <div class="form-group mb-3 col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="email[]" value="">
                </div>
            </div>
            <div class="row">
                <div class="form-group mb-3 col-md-6">
                    <label class="form-label">Phone</label>
                    <input type="number" class="form-control" name="phone[]" value="">
                </div>
                <div class="form-group mb-3 col-md-6">
                    <label class="form-label">Address</label>
                    <input type="text" class="form-control" name="address[]" value="">
                </div>
            </div>
            <div class="row">
                <div class="form-group mb-3 col-md-6">
                    <label class="form-label">Social Insurance Number</label>
                    <input type="text" class="form-control" name="sin[]" value="">
                </div>

            </div>
            <button type="button" class="btn btn-danger mb-3 remove-row">-</button>
        </div>`;
        $('#employee-container').append(newRow);
    });

    $(document).on('click', '.remove-row', function () {
        $(this).closest('.employee-group').remove();
    });
</script>
@endsection
