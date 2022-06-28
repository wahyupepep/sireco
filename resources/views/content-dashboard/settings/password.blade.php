@extends('layouts.app')

@section('content')
    <style>
        .alert-danger {
            color: white;
            background-color: #aa1c91 !important;
            border-color: #aa1c91 !important;
        }

        .close {
          color: white !important;
        }
    </style>
    @include('layouts.overview',['text' => 'Edit Password', 'icon' => 'mdi mdi-view-agenda'])
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-body">
                        @if(session('message'))
                            <div class="alert alert-{{session('message')['status']}} shadow">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                {{ session('message')['desc'] }}
                            </div>
                            <br>
                        @endif
                        <form action="{{route('setting.changepassword')}}" method="POST">
                            @csrf
                            <div class="form-group">
                              <label for="password_old">Old Password</label>
                              <input type="password" class="form-control" id="password_old" name="password_old" placeholder="Old Password...">
                            </div>
                            <div class="form-group">
                              <label for="password_new">Password</label>
                              <input type="password" class="form-control" id="password_new" name="password_new" placeholder="New Password...">
                            </div>
                            <div class="form-group">
                              <label for="confirm_password_new">Confirm Password New</label>
                              <input type="password" class="form-control" id="confirm_password_new" name="confirm_password_new" placeholder="Confirm New Password...">
                            </div>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection