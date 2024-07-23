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

      <!-- middle wrapper end -->
      <!-- right wrapper start -->
      </div>
      <!-- right wrapper end -->




@endsection
