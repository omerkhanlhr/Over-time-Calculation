@extends('admin.admin_dashboard')
@section('admin')
<div class="page-content">

    <nav class="page-breadcrumb">
        <ol class="breadcrumb">
            <a href="{{route('add.work.hours')}}" class="btn btn-inverse-info">Add WorkHours</a>
        </ol>
    </nav>

    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
<div class="card">
  <div class="card-body">
    <h6 class="card-title">Work Hour Details</h6>

    <div class="table-responsive">
      <table id="dataTableExample" class="table">
        <thead>
          <tr>
            <th>ID</th>
                                    <th>Client Name</th>
                                    <th>Employee Name</th>
                                    <th>Date</th>
                                    <th>Start Time</th>
                                    <th>End Time</th>
                                    <th>Daily Overtime</th>
                                    <th>Weekly Overtime</th>
                                    <th>Overtime</th>
                                    <th>Total Amount</th>

          </tr>
        </thead>
        <tbody>
            @foreach ($workhours as $workhour)
            <tr>
                <td>{{ $workhour->id }}</td>
                <td>{{ $workhour->client->name }}</td>
                <td>{{ $workhour->employee->name }}</td>
                <td>{{ $workhour->work_date }}</td>
                <td>{{ $workhour->start_time }}</td>
                <td>{{ $workhour->end_time }}</td>
                <td>{{ $workhour->daily_workhours }}</td>
                <td>{{ $workhour->weekly_workhours }}</td>
                <td>{{ $workhour->overtime ? 'Yes' : 'No' }}</td>
                <td>${{ $workhour->total_amount }}</td>
            </tr>
        @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
        </div>
    </div>

</div>

@endsection
