@extends('admin.admin_dashboard')
@section('admin')
<div class="page-content">

    <nav class="page-breadcrumb">
        <ol class="breadcrumb">
            <a href="{{route('add.stats.hours')}}" class="btn btn-inverse-info">Add StatsHours</a>
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
                                    <th>Move</th>
                                    <th>Total Amount</th>
                                    <th>Actions</th>

          </tr>
        </thead>
        <tbody>
            @foreach ($stats as $stat)
            <tr>
                <td>{{ $stat->id }}</td>
                <td>{{ $stat->client->name }}</td>
                <td>{{ $stat->employee->name }}</td>
                <td>{{ $stat->rate }}</td>
                <td>{{ $stat->overtime ? 'Yes' : 'No' }}</td>
                <td>
                        <a href="{{ route('move.To.Workhours',$stat->id)}}" class="btn btn-inverse-info">Move</a>
                </td>
                <td>${{ $stat->total_amount }}</td>
                <td>
                    <a href="{{route('edit.stats.hours',$stat->id)}}" class="btn btn-inverse-warning"><i class="fa fa-edit" style="font-size:24px;color:yellow"></i></a>
                    <a href="{{route('single.stats.hours.details',$stat->id)}}" class="btn btn-inverse-warning"><i class="fa fa-eye" style="font-size:24px;color:yellow"></i></a>
                    <a href="{{route('delete.statshour',$stat->id)}}" class="btn btn-inverse-danger" id="delete"><i class="fa fa-trash" style="font-size:24px;color:red"></i></a>
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
