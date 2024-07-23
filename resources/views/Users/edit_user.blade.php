@extends('admin.admin_dashboard')
@section('admin')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <div class="page-content">

        <div class="col-md-8 col-xl-8 middle-wrapper">
            <div class="row">
                <div class="card">
                    <div class="card-body">

                        <h6 class="card-title">Update User</h6>

                        <form id="myForm" method="post" action="{{ route('update.user') }}" class="forms-sample">
                            @csrf
                            <input type="hidden" name="id" value="{{$user->id}}">
                            <div class="form-group mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text"
                                    class="form-control"
                                    name="name" id="name" value="{{$user->name}}" >
                                    <span class="text-danger">
                                        @error('name')
                                            {{$message}}
                                        @enderror
                                    </span>

                            </div>
                            <div class="form-group mb-3">
                                <label for="name" class="form-label">Email</label>
                                <input type="email"
                                    class="form-control"
                                    name="email" id="email" value="{{$user->email}}">
                                    <span class="text-danger">
                                        @error('email')
                                            {{$message}}
                                        @enderror
                                    </span>

                            </div>
                            <div class="form-group mt-3">
                                <label for="status" class="form-label">Role</label>
                                <select name="role" id="exampleFormControlSelect1" class="form-select">

                                    <option selected disabled>Select Role</option>

                                    <option value="admin"{{$user->role=='admin'? 'selected' : ''}}>Admin</option>
                                    <option value="user"{{$user->role=='user'? 'selected' : ''}}>User</option>

                                </select>
                            </div>


                            <button type="submit" class="btn btn-primary me-2 mt-2">Update User</button>
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
