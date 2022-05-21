<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login CMS Satu Pintu Kitchen</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="icon" href="{{asset('assets/images/setiawan.png')}}" type="image/png" sizes="16x16">
    <link rel="stylesheet" href="{{asset('assets/css/login.css')}}">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.min.js" integrity="sha384-w1Q4orYjBQndcko6MimVbzY0tgp4pWB4lZ7lr30WKz0vr/aWKhXdBNmNb5D92v7s" crossorigin="anonymous" defer></script>
</head>
<body>
    
    <div class="container-fluid px-1 px-md-5 px-lg-1 px-xl-5 py-5 mx-auto">
            <div class="row d-flex">
                <div class="col-lg-6">
                    <div class="card1 pb-5">
                        <div class="row"> </div>
                        <div class="row px-3 justify-content-center mt-4 mb-5 border-line"> 
                            <br>
                            <img src="{{asset('assets/images/setiawan.png')}}" class="logo">  
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <br>
                   <br>
                    <div class="card2 card border-0 px-4 py-5 mt-5 shadow">
                        @if(session('message'))
                        <div class="alert alert-{{session('message')['status']}}">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            {{ session('message')['desc'] }}
                        </div>
                        @endif
                        <form method="POST" action="{{ route('manage.checklogin') }}">
                            @csrf
                            <div class="row px-3"> 
                                <label class="mb-1">
                                    <h6 class="mb-0 text-sm">Email Address</h6>
                                </label> 
                                <input id="email" class="mb-3 @error('email') is-invalid @enderror" type="text" name="email" placeholder="Enter a valid email address" value="{{ old('email') }}"> </div>
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            <div class="row px-3"> 
                                <label class="mb-1">
                                    <h6 class="mb-0 text-sm">Password</h6>
                                </label> 
                                <input type="password" name="password" placeholder="Enter password" class="@error('password') is-invalid @enderror"> 
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="row mb-3 px-3 py-3"> <button type="submit" class="btn btn-blue text-center">Login</button> </div>
                        </form>
                        
                    </div>
                </div>
            </div>
    </div>
</body>
</html>
