@extends('layouts.app')

@section('content')
    <style>
        .table td img {
            border-radius: 0;
        }
    </style>
    @include('layouts.overview',['text' => 'Payment Verification', 'icon' => 'mdi mdi-view-agenda'])
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                
            </div>
        </div>
    </div>
@endsection