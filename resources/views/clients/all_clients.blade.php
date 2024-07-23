@extends('admin.admin_dashboard')
@section('admin')
<div class="page-content">

    <nav class="page-breadcrumb">
        <ol class="breadcrumb">
            <a href="{{route('add.client')}}" class="btn btn-inverse-info">Add Client</a>
            &nbsp;&nbsp;&nbsp;&nbsp;
            <a href="{{route('export.client')}}" class="btn btn-success">Export Clients</a>
        </ol>
    </nav>

    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
<div class="card">
  <div class="card-body">
    <h6 class="card-title">All Clients</h6>

    <div class="table-responsive">
      <table id="dataTableExample" class="table">
        <thead>
          <tr>
            <th>Sr.No.</th>
            <th>Name</th>
            <th>Email</th>
            <th>Invoices</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
            @foreach ($clients as $key=>$client )


          <tr>
            <td>{{ $key+1}}</td>
            <td>
    <a href="{{route('single.client',$client->id)}}">{{$client->name}}</a>
            </td>
            <td>{{$client->contact_email}}</td>
            <td>
                <a href="{{route('view.invoice',$client->id)}}" class="btn btn-inverse-info" ><i class="fa fa-eye"></i></a>

            </td>
            <td>
                <a href="{{route('edit.client',$client->id)}}" class="btn btn-inverse-warning"><i class="fa fa-edit" style="font-size:24px;color:yellow"></i></a>
                <a href="{{route('delete.client',$client->id)}}" class="btn btn-inverse-danger" id="delete"><i class="fa fa-trash" style="font-size:24px;color:red"></i></a>


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
