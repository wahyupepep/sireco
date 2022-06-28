<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Registration Member from Hetero Space</title>
</head>
<body>
    <img src="{{asset('assets/images/hetero.png')}}" alt="hetero-space" width="200" height="200" style="display: block; margin-left:auto; margin-right:auto">
    <div style="margin-top: 1rem; text-align:center">
        <p>Thank you for registration member on hetero space</p>
    </div>
    <div style="margin-top: 1rem; text-align:center">
        <p>Please, verify your email with click link below</p>
        <a href="{{$details['link']}}" target="_blank">{{$details['link']}}</a>
    </div>
    {{-- <h1>{{ $details['title'] }}</h1>
    <p>{{ $details['body'] }}</p>
   
    <p>Thank you</p> --}}
</body>
</html>