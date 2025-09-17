@extends('admin.admin_dashboard')
@section('admin')
<div class="page-content">

     <nav class="page-breadcrumb">
        <ol class="breadcrumb">
            <a href="{{route('add.type')}}" class="btn btn-inverse-info">Add Type</a>
            &nbsp;&nbsp;&nbsp;&nbsp;
        </ol>
    </nav>

    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
<div class="card">
  <div class="card-body">
    <h6 class="card-title">All Labours Typesg</h6>

    <div class="table-responsive">
      <table id="dataTableExample" class="table">
        <thead>
          <tr>
            <th>Sr.No.</th>
            <th>Name</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
            @foreach ($labours as $key=> $labour )


          <tr>
            <td>{{ $key+1 }}</td>
            <td>{{ $labour->name }}</td>

            <td>
                <a href="{{route('edit.type',$labour->id)}}" class="btn btn-inverse-warning"><i class="fa fa-edit" style="font-size:24px;color:yellow"></i></a>
                <a href="{{route('delete.type',$labour->id)}}" class="btn btn-inverse-danger" id="delete"><i class="fa fa-trash" style="font-size:24px;color:red"></i></a>
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
