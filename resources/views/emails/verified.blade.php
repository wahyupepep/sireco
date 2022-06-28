<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Verified Email</title>
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
    </style>
</head>
<body> 
    <div class="container h-100">
        <div class="row align-items-center h-100">
            <div class="col-lg-12">
                @php
                    if($status == 1) { // verified
                        $src = asset('assets/images/verified.png');
                        $text = 'Your Email Verified';
                    }else if( $status == 2) {
                        $src = asset('assets/images/already-exists.png');
                        $text = 'Your Email Already Verified';
                    }else {
                        $src = asset('assets/images/not-found.png');
                        $text = 'Failed Verified Your Email';
                    }
                @endphp
               
                <img src="{{asset('assets/images/hetero.png')}}" alt="hetero-space" width="200" height="200" class="img-responsive img-fluid d-block mx-auto">
                <img src="{{$src}}" alt="verified" width="300" height="300" class="img-responsive img-fluid d-block mx-auto">
                <h3 class="text-center font-weight-bold">{{$text}}</h3>
            </div>
        </div>
    </div>
</body>
</html>
