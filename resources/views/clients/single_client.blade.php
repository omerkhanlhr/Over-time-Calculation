@extends('admin.admin_dashboard')
@section('admin')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<div class="page-content">
    <div class="row profile-body">
      <!-- left wrapper start -->
      <div class="d-none d-md-block col-md-8 col-xl-8 left-wrapper">
        <div class="card rounded">
          <div class="card-body">
            <div class="d-flex align-items-center justify-content-between mb-2">

              <div>

                <span class="h4 ms-3 text">{{ $client->name}}</span>
              </div>
            </div>
            <div class="mt-3">
              <label class="tx-11 fw-bolder mb-0 text-uppercase">Email</label>
              <p class="text-muted">{{$client->email}}</p>
            </div>
            <div class="mt-3">
              <label class="tx-11 fw-bolder mb-0 text-uppercase">Company</label>
              <p class="text-muted">

                {{$client->company}}

            </p>
            </div>


          </div>
        </div>
      </div>
          </div>
          <h5 class="card-title mt-3 mb-3">Work Details</h5>
          <div class="table-responsive">
            <table id="dataTableExample" class="table">
              <thead>
                <tr>
                    <th>Date</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Daily Work Hours</th>
                    <th>Daily Overtime</th>
                    <th>Overtime</th>
                    <th>Total Amount</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($client->workHours as $workhour)
                <tr>
                    <td>{{ Carbon\Carbon::parse($workhour->work_date)->translatedFormat('j F Y') }}</td>
                    <td>{{ $workhour->start_time }}</td>
                    <td>{{ $workhour->end_time }}</td>
                    <td>{{ $workhour->daily_workhours }}</td>
                    <td>{{ $workhour->daily_overtime }}</td>
                    <td>{{ $workhour->is_overtime ? 'Yes' : 'No' }}</td>
                    <td>{{ $workhour->total_amount }}</td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
      <!-- middle wrapper end -->
      <!-- right wrapper start -->
      </div>
      <!-- right wrapper end -->




@endsection
