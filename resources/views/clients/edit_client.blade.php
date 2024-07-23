@extends('admin.admin_dashboard')
@section('admin')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <div class="page-content">

        <div class="col-md-8 col-xl-8 middle-wrapper">
            <div class="row">
                <div class="card">
                    <div class="card-body">

                        <h6 class="card-title">Edit Client</h6>

                        <form id="myForm" method="post" action="{{ route('update.client',$client->id) }}" class="forms-sample">
                            @csrf
                            <div class="form-group mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text"
                                    class="form-control"
                                    name="name" id="name" value="{{$client->name}}">


                            </div>
                            <div class="form-group mb-3">
                                <label for="name" class="form-label">Email</label>
                                <input type="email"
                                    class="form-control"
                                    name="email" id="email" value="{{$client->contact_email}}">


                            </div>
                            <div class="form-group mb-3">
                                <label for="name" class="form-label">Company</label>
                                <input type="text"
                                    class="form-control"
                                    name="company" id="company" value="{{$client->company}}">

                            </div>
                            <div class="form-group mb-3">
                                <label for="name" class="form-label">Phone</label>
                                <input type="tel" id="phone" name="phone" class="form-control" value="{{$client->phone}}" pattern="[0-9]{3}-[0-9]{2}-[0-9]{3}">

                            </div>
                            <div class="form-group mb-3">
                                <label for="name" class="form-label">Address</label>
                                <textarea name="address" id="address" class="form-control" rows="3">
                                    {{$client->address}}
                                </textarea>


                            </div>

                            <button type="submit" class="btn btn-primary me-2">Update Client</button>
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
