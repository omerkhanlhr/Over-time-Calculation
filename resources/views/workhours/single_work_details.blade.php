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

                <span class="h4 ms-3 text">{{ $workhour->client->name }}</span>
              </div>
            </div>
            <div class="mt-3">
              <label class="tx-11 fw-bolder mb-0 text-uppercase">Employee Name</label>
              <p class="text-muted">{{ $workhour->employee->name }}</p>
            </div>
            <div class="mt-3">
              <label class="tx-11 fw-bolder mb-0 text-uppercase">Date</label>
              <p class="text-muted">

                {{ $workhour->work_date }}

            </p>
            </div>
            <div class="mt-3">
              <label class="tx-11 fw-bolder mb-0 text-uppercase">Rate</label>
              <p class="text-muted">

                {{ $workhour->rate }}

            </p>
            </div>
            <div class="mt-3">
              <label class="tx-11 fw-bolder mb-0 text-uppercase">Start Time</label>
              <p class="text-muted">

                {{ $workhour->start_time }}

            </p>
            </div>
            <div class="mt-3">
              <label class="tx-11 fw-bolder mb-0 text-uppercase">End Time</label>
              <p class="text-muted">

                {{ $workhour->end_time }}

            </p>
            </div>
            <div class="mt-3">
              <label class="tx-11 fw-bolder mb-0 text-uppercase">Daily Work Time</label>
              <p class="text-muted">

                {{ $workhour->daily_workhours }}

            </p>
            </div>

            <div class="mt-3">
              <label class="tx-11 fw-bolder mb-0 text-uppercase">Break Time</label>
              <p class="text-muted">

                {{ $workhour->break_time }} mins

            </p>
            </div>
            <div class="mt-3">
              <label class="tx-11 fw-bolder mb-0 text-uppercase">Over Time</label>
              <p class="text-muted">

                 {{ $workhour->overtime ? 'Yes' : 'No' }}

            </p>
            </div>
            <div class="mt-3">
              <label class="tx-11 fw-bolder mb-0 text-uppercase">Stats Over Time</label>
              <p class="text-muted">

                 {{ $workhour->stats_overtime ? 'Yes' : 'No' }}

            </p>
            </div>
            <div class="mt-3">
              <label class="tx-11 fw-bolder mb-0 text-uppercase">Over Time</label>
              <p class="text-muted">

                 {{ $workhour->daily_overtime  }}

            </p>
            </div>
            <div class="mt-3">
              <label class="tx-11 fw-bolder mb-0 text-uppercase">Stats Over Time</label>
              <p class="text-muted">

                 {{ $workhour->stats_overtime_hours  }}

            </p>
            </div>
            <div class="mt-3">
              <label class="tx-11 fw-bolder mb-0 text-uppercase">Total Amount</label>
              <p class="text-muted">

                 {{ $workhour->total_amount }}

            </p>
            </div>



          </div>
        </div>
      </div>
          </div>

      <!-- middle wrapper end -->
      <!-- right wrapper start -->
      </div>
      <!-- right wrapper end -->




@endsection
