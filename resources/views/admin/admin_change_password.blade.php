@extends('admin.admin_dashboard')
@section('admin')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<div class="page-content">
    <div class="row profile-body">
      <!-- left wrapper start -->
      <div class="d-none d-md-block col-md-4 col-xl-4 left-wrapper">
        <div class="card rounded">
          <div class="card-body">
            <div class="d-flex align-items-center justify-content-between mb-2">

              <div>
                <img class="wd-100 rounded-circle" src="{{(!empty($profile->photo)) ?
               url('images/admin_images/'.$profile->photo):url('images/no_image.jpg') }}" alt="profile">
                <span class="h4 ms-3 text">{{ $profile->name}}</span>
              </div>
            </div>
            <div class="mt-3">
              <label class="tx-11 fw-bolder mb-0 text-uppercase">Email:</label>
              <p class="text-muted">{{$profile->email}}</p>
            </div>
            <div class="mt-3">
              <label class="tx-11 fw-bolder mb-0 text-uppercase">Role</label>
              <p class="text-muted">{{$profile->role}}</p>
            </div>
            <div class="mt-3 d-flex social-links">
              <a href="javascript:;" class="btn btn-icon border btn-xs me-2">
                <i data-feather="github"></i>
              </a>
              <a href="javascript:;" class="btn btn-icon border btn-xs me-2">
                <i data-feather="twitter"></i>
              </a>
              <a href="javascript:;" class="btn btn-icon border btn-xs me-2">
                <i data-feather="instagram"></i>
              </a>
            </div>
          </div>
        </div>
      </div>
      <!-- left wrapper end -->
      <!-- middle wrapper start -->
      <div class="col-md-8 col-xl-8 middle-wrapper">
        <div class="row">
            <div class="card">
                <div class="card-body">

                                  <h6 class="card-title">Update Admin Password</h6>

                                  <form method="post" action="{{route('update.admin_password')}}" class="forms-sample">
                                    @csrf
                                    <div class="mb-3">
                                          <label for="name" class="form-label">Old Password</label>
                                          <input type="password" class="form-control @error('old_password')
                                        is-invalid @enderror" name="old_password" id="old_password" autocomplete="off">
                                          @error('old_password')
                                        <span class="text-danger">
                                            {{$message}}
                                        </span>
                                          @enderror
                                      </div>
                                    <div class="mb-3">
                                          <label for="name" class="form-label">New Password</label>
                                          <input type="password" class="form-control @error('new_password')
                                        is-invalid @enderror" name="new_password" id="new_password" autocomplete="off">
                                          @error('new_password')
                                        <span class="text-danger">
                                            {{$message}}
                                        </span>
                                          @enderror
                                      </div>
                                    <div class="mb-3">
                                          <label for="name" class="form-label">Confirm Password</label>
                                          <input type="password" class="form-control" name="new_password_confirmation" id="new_password_confirmation" autocomplete="off">
                                      </div>

                                      <button type="submit" class="btn btn-primary me-2">Update</button>
                                  </form>

                </div>
              </div>
              </div>
            </div>
          </div>

      <!-- middle wrapper end -->
      <!-- right wrapper start -->
      </div>
      <!-- right wrapper end -->
    </div>

        </div>


@endsection
