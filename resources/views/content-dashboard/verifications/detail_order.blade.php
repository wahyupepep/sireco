@extends('layouts.app')
@section('content')
    <style>
        .detail-payment img {
            border-radius: 10px;
            cursor: pointer;
        }
    </style>
    @include('layouts.overview',['text' => 'Detail order', 'icon' => 'mdi mdi-cart-outline'])
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
                                <td>{{date('d M y', strtotime($reservation->order_date))}}</td>
                              </tr>
                              <tr>
                                <td class="font-weight-bold">Seats</td>
                                <td>:</td>
                                <td>{{strtoupper($reservation->seat_code)}}</td>
                              </tr>
                              <tr>
                                <td class="font-weight-bold">Package</td>
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
                        </table>
                        <hr>
                        <div class="terms-condtions">
                            <h6 class="font-weight-bold ml-2">Payment Proof</h6>
                            <div class="detail-payment ml-2">
                                @if (!is_null($reservation->payment_file))
                                    <img src="{{asset($reservation->payment_file)}}" alt="example-proof-payment" width="150" height="150">
                                    <button type="button" class="btn btn-success btn-lg font-weight-bold shadow btn-verify ml-3">Verification</button>
                                @else
                                <img src="{{asset('assets/images/not-found.png')}}" alt="example-proof-payment" width="200" height="auto" class="d-block mx-auto"> 
                                <p class="font-weight-bold text-center">proof of payment has not been uploaded</p>
                                @endif
                                
                                {{-- <button type="button" class="btn btn-success btn-lg font-weight-bold shadow btn-verify ml-3">Verification</button> --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="modal-img-proof-payment" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Payment Proof</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                </div>
                <div class="modal-body">
                    <img src="" alt="payment proof" style="width:100%; height: auto; object-fit:cover">
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

            $(document).on('click', '.detail-payment img', function() {
                const urlImg =$(this).attr('src')
                $('#modal-img-proof-payment img').attr('src', urlImg)
                $('#modal-img-proof-payment').modal('show')
            })

            $(document).on('click','.btn-verify', function(){
                let url = "{{route('verification.verified-order',':id')}}"
                url = url.replace(':id', "{{$id}}")
                window.location.href = url
            })
        })

       
    </script>
@endsection