@extends('admin.admin_dashboard')
@section('admin')
<div class="page-content">

     <nav class="page-breadcrumb">
        <ol class="breadcrumb">
            <a href="{{route('add.employee')}}" class="btn btn-primary">Add Employee</a>
            &nbsp;&nbsp;&nbsp;&nbsp;
<<<<<<< HEAD
        
=======
          {{-- <a href="{{route('export.employee')}}" class="btn btn-success">Export Employees</a>
          &nbsp;&nbsp;&nbsp;&nbsp;
          <a href="{{route('import.employee')}}" class="btn btn-success">Import Employees</a> --}}
>>>>>>> f00aac00c0b8581246a1b112796c1e2853b27609
        </ol>
    </nav>

    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
<div class="card">
  <div class="card-body">
    <h6 class="card-title">All Employees</h6>

    <div class="table-responsive">
      <table id="dataTableExample" class="table">
        <thead>
          <tr>
            <th>Sr.No.</th>
            <th>Name</th>
            <th>Email</th>
            <th>Id</th>
            <th>Phone</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
<<<<<<< HEAD
            @foreach ($employees as $key=> $employee )
=======
            @foreach ($employees as $key=>$employee )
>>>>>>> f00aac00c0b8581246a1b112796c1e2853b27609


          <tr>
            <td>
            <a href="{{route('single.employee', $employee->id)}}">
<<<<<<< HEAD
                {{ $key+1 }}
=======
                {{ $key+1}}
>>>>>>> f00aac00c0b8581246a1b112796c1e2853b27609
            </a>
            </td>
            <td>{{$employee->name}}</td>
            <td>{{$employee->email}}</td>
            <td>{{$employee->emp_id}}</td>
            <td>{{$employee->phone}}</td>
            <td>
                <a href="{{route('edit.employee',$employee->id)}}" class="btn btn-inverse-warning"><i class="fa fa-edit" style="font-size:24px;color:yellow"></i></a>
                <a href="{{route('delete.employee',$employee->id)}}" class="btn btn-inverse-danger" id="delete"><i class="fa fa-trash" style="font-size:24px;color:red"></i></a>
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
