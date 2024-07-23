@extends('admin.admin_dashboard')
@section('admin')
<div class="page-content">


    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
<div class="card">
  <div class="card-body">
    <h6 class="card-title">Client Invoices</h6>

    <div class="table-responsive">
      <table id="dataTableExample" class="table">
        <thead>
          <tr>
            <th>Invoice ID</th>
            <th>Toatal Amount</th>
            <th>Status</th>
            <th>Payment Date</th>
          </tr>
        </thead>
        <tbody>
            @foreach ($invoices as $invoice)


          <tr>
            <td>
                <a href="{{route('show.invoice',$invoice->id)}}">{{$invoice->id}}</a>
            </td>
            <td>{{$invoice->total_amount}}</td>
            <td>{{$invoice->status}}</td>
            <td>{{$invoice->date}}</td>

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
