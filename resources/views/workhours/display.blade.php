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
                                    <th>Rate</th>
                                    <th>Overtime</th>
                                    <th>Total Amount</th>
                                    <th>Actions</th>

          </tr>
        </thead>
        <tbody>
            @foreach ($workhours as $workhour)
            <tr>
                <td>{{ $workhour->id }}</td>
                <td>{{ $workhour->client->name }}</td>
                <td>{{ $workhour->employee->name }}</td>
                <td>{{ $workhour->rate }}</td>
                <td>{{ $workhour->overtime ? 'Yes' : 'No' }}</td>
                <td>${{ $workhour->total_amount }}</td>
                <td>
                    <a href="{{route('edit.work.hours',$workhour->id)}}" class="btn btn-inverse-warning"><i class="fa fa-edit" style="font-size:24px;color:yellow"></i></a>
                    <a href="{{route('single.work.hours.details',$workhour->id)}}" class="btn btn-inverse-warning"><i class="fa fa-eye" style="font-size:24px;color:yellow"></i></a>
                </td>
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
