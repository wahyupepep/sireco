<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login Booking Space</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="icon" href="{{asset('assets/images/hetero.png')}}" type="image/png" sizes="16x16">
    <link rel="stylesheet" href="{{asset('assets/css/style.css')}}">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.min.js" integrity="sha384-w1Q4orYjBQndcko6MimVbzY0tgp4pWB4lZ7lr30WKz0vr/aWKhXdBNmNb5D92v7s" crossorigin="anonymous" defer></script>
    <style>
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
<body style="background-image: url({{ asset('assets/images/maneka.jpg') }}); background-repeat: no-repeat; background-size: cover; "> 

    <div class="container-scroller">
        <div class="container-fluid page-body-wrapper full-page-wrapper">
          <div class="content-wrapper d-flex align-items-center auth">
            <div class="row flex-grow">
              <div class="col-lg-5 mx-auto">
                <div class="auth-form-light text-left p-5">
                  <div class="brand-logo">
                    <img src="{{ asset('assets/images/hetero.png') }}" class="d-block mx-auto">
                  </div>
                  <div class="text-center">
                    <h4>Hai! Untuk Reservasi Kursi</h4>
                    <h6 class="font-weight-light">Silahkan login terlebih dahulu.</h6>
                  </div>
                 
                  <form class="pt-3" method="POST" action="{{ route('manage.checklogin') }}">
                    @if(session('message'))
                    <div class="alert alert-{{session('message')['status']}} shadow">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        {{ session('message')['desc'] }}
                    </div>
                    <br>
                  @endif
                    @csrf
                    <div class="form-group">
                      <input type="email" class="form-control form-control-lg @error('email') is-invalid @enderror" id="email" name="email" placeholder="Email/username" required>
                      @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                      @enderror
                    </div>
                    <div class="form-group">
                      <input type="password" class="form-control form-control-lg @error('password') is-invalid @enderror" id="password" name="password" placeholder="Password" required>
                      @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                      @enderror
                    </div>
                    <div class="mt-3">
                      <button type="submit" class="btn btn-block button-red shadow btn-lg font-weight-medium auth-form-btn">LOGIN</button>
                    </div>
                    <div class="dont-have-account mt-3 text-right">
                      <a href="{{route('manage.registration')}}" class="text-gradient-account" style="font-size: 13px">Doesn't have account?</a>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
          <!-- content-wrapper ends -->
        </div>
        <!-- page-body-wrapper ends -->
      </div>
</body>
</html>
