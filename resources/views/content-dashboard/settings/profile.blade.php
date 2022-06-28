@extends('layouts.app')

@section('content')
    <style>
        .table td img {
            border-radius: 0;
        }
    </style>
    @include('layouts.overview',['text' => 'Profile', 'icon' => 'mdi mdi-view-agenda'])
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
                        <form action="{{route('setting.updateprofile',['id' => $id])}}" method="POST">
                            @method('PUT')
                            @csrf
                            <input type="hidden" name="id" id="id" value="{{$id}}">
                            <div class="form-group">
                              <label for="fullname">Fullname</label>
                              <input type="text" class="form-control" id="fullname" name="fullname" value="{{$member->fullname ?? '-'}}" required>
                            </div>
                            <div class="form-group">
                              <label for="nik">NIK</label>
                              <input type="text" class="form-control" id="nik" name="nik" value="{{$member->nik ?? '-'}}" required>
                            </div>
                            <div class="form-group">
                              <label for="username">Username</label>
                              <input type="text" class="form-control" id="username" name="username" value="{{$member->name ?? '-'}}" required>
                            </div>
                            <div class="form-group">
                              <label for="email">Email</label>
                              <input type="email" class="form-control" id="email" name="email" value="{{$member->email ?? '-'}}" required>
                            </div>
                            <div class="form-group">
                              <label for="birthdate">Birth Date</label>
                              <input type="date" class="form-control" id="birthdate" name="birthdate" value="{{$member->birthdate != null ? date('Y-m-d', strtotime($member->birthdate)) : null}}" required>
                            </div>
                            <div class="form-group">
                                <label for="address">Address</label>
                                <textarea class="form-control" id="address" name="address" rows="3" required>{{$member->address ?? '-'}}</textarea>
                            </div>
                            <div class="form-group">
                                <label for="work_type">Work Type</label>
                                <select class="form-control" id="work_type" name="work_type" required>
                                    <option value="">-- Select Work Type --</option>
                                    @forelse ($work_types as $key => $item_type)
                                        <option value="{{$key}}" {{$key === $member->work_type ? 'selected' : ''}}>{{$item_type}}</option>
                                    @empty
                                        <option value="">Work type empty</option>
                                    @endforelse
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="industry_name">Industry Name</label>
                                <input type="text" class="form-control" id="industry_name" name="industry_name" value="{{$member->industry_name ?? '-'}}" required>
                            </div>
                            <div class="form-group">
                                <label for="hobby">Hobby</label>
                                <select class="form-control" id="hobby" name="hobby" required>
                                    <option value="">-- Select Hobby --</option>
                                    @forelse ($hobbies as $key => $hobby)
                                        <option value="{{$key}}" {{$key === $member->hobby ? 'selected' : ''}}>{{$hobby}}</option>
                                    @empty
                                        <option value="">Hobby empty</option>
                                    @endforelse
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="phone">Phone</label>
                                <input type="text" class="form-control" id="phone" name="phone" value="{{$member->phone ?? '-'}}" required>
                                <div id="errmsg"></div>
                            </div>
                            <div class="form-group">
                                <label for="age">Age</label>
                                <select class="form-control" id="age" name="age" required>
                                    <option value="">-- Select Age --</option>
                                    @forelse ($ages as $key => $age)
                                        <option value="{{$key}}" {{$key === $member->age ? 'selected' : ''}}>{{$age}}</option>
                                    @empty
                                        <option value="">Age empty</option>
                                    @endforelse
                                </select>
                            </div>
                            <button type="button" class="btn btn-warning btn-edit">Edit</button>
                            <button type="submit" class="btn btn-primary d-none btn-submit">Submit</button>
                            <button type="button" class="btn btn-secondary d-none btn-cancel">Cancel</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        $(document).ready(function() {
            $('input').prop('disabled', true)
            $('textarea').prop('disabled', true)
            $('select').prop('disabled', true)

            $(document).on('click','.btn-edit', function() {
                $('input').prop('disabled', false)
                $('textarea').prop('disabled', false)
                $('select').prop('disabled', false)
                $(this).addClass('d-none')
                $('.btn-submit').removeClass('d-none')
                $('.btn-cancel').removeClass('d-none')
            })

            $(document).on('click','.btn-cancel', function() {
                $('input').prop('disabled', true)
                $('textarea').prop('disabled', true)
                $('select').prop('disabled', true)
                $(this).addClass('d-none')
                $('.btn-submit').addClass('d-none')
                $('.btn-edit').removeClass('d-none')
            })

            $(document).on('keypress','#phone', function(e) {
                 if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
                    $("#errmsg").html("Number Only").stop().show().fadeOut("slow");
                    return false;
                 }
            })
        })
    </script>
@endsection