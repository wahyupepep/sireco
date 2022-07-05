@extends('layouts.app')
@section('content')
    @include('layouts.overview',['text' => 'Detail Order', 'icon' => 'mdi mdi-cart-outline'])
    <style>
      .table th img, .table td img {
          width: 300px !important;
          height: 400px !important;
          border-radius: 10px !important;
          object-fit: cover !important;
      }
    </style>
    <div class="container">
        <div class="row">
            <div class="card" style="width: 100%">
                <div class="row p-4">
                    <div class="col-md-4 d-flex justify-content-center align-items-center">
                        <img src="{{asset('assets/images/hetero.png')}}" alt="hetero-space-logo" width="150" height="150">
                    </div>
                    <div class="col-md-8">
                        <table class="table">
                          <tr>
                            <td class="font-weight-bold">Name</td>
                            <td>:</td>
                            <td>{{$reservation->member->fullname}}</td>
                          </tr>
                          <tr>
                            <td class="font-weight-bold">Date</td>
                            <td>:</td>
                            <td>{{date('d M Y', strtotime($reservation->order_date))}}</td>
                          </tr>
                          <tr>
                            <td class="font-weight-bold">Seats</td>
                            <td>:</td>
                            <td>{{strtoupper($reservation->seat_code)}}</td>
                          </tr>
                          <tr>
                            <td class="font-weight-bold">Duration</td>
                            <td>:</td>
                            <td>{{$reservation->member->package->name}} ({{$reservation->member->package->day}}x)</td>
                          </tr>
                          <tr>
                            <td class="font-weight-bold">Price</td>
                            <td>:</td>
                            <td>{{"IDR " . number_format($reservation->history_transaction->price,0,',','.')}}</td>
                          </tr>
                          <tr>
                            <td class="font-weight-bold">Discount</td>
                            <td>:</td>
                            <td>{{$reservation->history_transaction->discount == 0 ? '-' : $reservation->history_transaction->discount }}</td>
                          </tr>
                          <tr>
                            @php
                                $total = $reservation->history_transaction->price - ($reservation->history_transaction->discount ?? 0);
                            @endphp
                            
                            @if ($total > 0)
                              <td class="font-weight-bold" style="color:red; font-size: 20px">Total</td>
                              <td>:</td>
                              <td style="color:red; font-size: 20px" class="font-weight-bold">{{"IDR " . number_format($total,0,',','.')}}</td>
                            @else
                              <td class="font-weight-bold" style="color:green; font-size: 20px">Total</td>
                              <td>:</td>
                              <td style="color:green; font-size: 20px" class="font-weight-bold">{{"IDR " . number_format($total,0,',','.')}}  <span class="badge badge-success ml-3 font-weight-bold">PAID</span></td>  
                            @endif
                            
                          </tr>
                          <tr>
                            <td>
                              @if (is_null($reservation->payment_file))
                                <img src="{{asset('assets/images/no-preview-available.png')}}" alt="preview-upload-payment">
                              @else
                                <img src="{{asset($reservation->payment_file)}}" alt="preview-upload-payment">
                              @endif
                             
                            </td>
                          </tr>
                          <tr>
                            <td>
                              <a href="{{route('seat.list-order')}}" class="btn btn-warning btn-back-list">Back</a>
                            </td>
                          </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        $(document).ready(function() {
            $(document).on('click','.btn-confirm-order', function() {
                window.location.href = "{{route('seat.list-order')}}"
            })
        })
    </script>
@endsection