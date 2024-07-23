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

                                  <h6 class="card-title">Update Admin Profile</h6>

                                  <form method="POST" action="{{route('update.admin_profile')}}" enctype="multipart/form-data" class="forms-sample">
                                    @csrf
                                    <div class="mb-3">
                                          <label for="name" class="form-label">Name</label>
                                          <input type="text" class="form-control" name="name" value="{{$profile->name}}" id="name" placeholder="Email">
                                      </div>
                                      <div class="mb-3">
                                        <label for="email" class="form-label">Email address</label>
                                        <input type="email" class="form-control" name="email" value="{{$profile->email}}" id="exampleInputEmail1" placeholder="Email">
                                    </div>
                                      <div class="mb-3">
                                        <label for="email" class="form-label">Photo</label>
                                        <input class="form-control" name="photo" type="file" id="image">
                                        </div>
                                      <div class="mb-3">
                                        <label for="email" class="form-label">Photo</label>
                                        <img id="showImage" class="wd-80 rounded-circle" src="{{(!empty($profile->photo)) ?
                                          url('images/admin_images/'.$profile->photo):url('images/no_image.jpg') }}" alt="profile">

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

<script type="text/javascript">
$(document).ready(function(){
  $('#image').change(function(e){
    var reader=new FileReader();
    reader.onload=function(e){
    $('#showImage').attr('src',e.target.result);
    }
    reader.readAsDataURL(e.target.files['0']);
  })
})
</script>

@endsection
