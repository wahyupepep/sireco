<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Registration Booking Space</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="icon" href="{{asset('assets/images/hetero.png')}}" type="image/png" sizes="16x16">
    <link rel="stylesheet" href="{{asset('assets/css/style.css')}}">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.min.js" integrity="sha384-w1Q4orYjBQndcko6MimVbzY0tgp4pWB4lZ7lr30WKz0vr/aWKhXdBNmNb5D92v7s" crossorigin="anonymous" defer></script>
    <style>
        body {
            height: 800px;
        }
         .button-red {
          background: #aa1c91 !important;
          color: white
        }
        .content-wrapper{
          background: unset !important;
        }

        .button-red:hover {
          color: white;
        }

        .alert-danger {
            color: white;
            background-color: #aa1c91 !important;
            border-color: #aa1c91 !important;
        }

        .close {
          color: white !important;
        }

        .text-gradient-account {
          color: #aa1c91
        }
        .text-gradient-account:hover {
          color:#aa1c91
        }
    </style>
</head>
<body> 
    <div class="containter-fluid">
        <div class="row">
            <div class="col-lg-5 d-flex justify-content-center">
                <div style="top: 40%; bottom: 40%; position: absolute;">
                    <img src="{{ asset('assets/images/hetero.png') }}" class="image-responsive img-fluid" style="width: 300px; height: 300px; object-fit: cover;">
                    <h3 class="font-weight-bold text-center">Infinity In Diversity</h3>
                    <p class="text-muted text-center mt-3">Hetero Space &copy;</p>
                </div>  
            </div>
            <div class="col-lg-7 d-flex justify-content-center align-items-center">
                    <div class="card shadow" style="width: 80%; top: 20%">
                        <div class="card-body">
                            <h4 class="font-weight-bold mb-3">Registration Member</h4>
                            <form method="POST" action="{{ route('manage.inputregistration') }}">
                                @csrf
                                @if(session('message'))
                                    <div class="alert alert-{{session('message')['status']}} shadow">
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                        {{ session('message')['desc'] }}
                                    </div>
                                    <br>
                                @endif
                                <div class="form-group">
                                    <label for="fullname">Fullname</label>
                                    <input type="text" class="form-control" id="fullname" name="fullname" value="{{old('fullname')}}" placeholder="Input your name...">
                                    @error('fullname')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="username">Username</label>
                                    <input type="text" class="form-control" id="username" name="username" value="{{old('username')}}" placeholder="Input you username...">
                                    @error('username')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" value="{{old('email')}}" placeholder="Input your email...">
                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="password">Password</label>
                                    <input type="password" class="form-control" id="password" name="password" placeholder="Input your password...">
                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <button type="submit" class="btn button-red">Submit</button>
                            </form>
                        </div>
                    
                </div>
            </div>
        </div>
    </div>
</body>
</html>
