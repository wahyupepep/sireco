@extends('layouts.app')
@section('content')
    @include('layouts.overview',['text' => 'Order Summary', 'icon' => 'mdi mdi-cart-outline'])
    <div class="container">
        <div class="row">
            <div class="card" style="width: 100%">
                <div class="row p-4">
                    <div class="col-md-4 d-flex justify-content-center align-items-center">
                        <img src="{{asset('assets/images/hetero.png')}}" alt="hetero-space-logo" width="150" height="150">
                    </div>
                    <div class="col-md-8">
                        <table class="table">
                              <input type="hidden" name="order_id" id="order_id" value="{{$id}}">
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
                                <td class="font-weight-bold" style="color:red; font-size: 20px">Total</td>
                                <td>:</td>
                                @php
                                    $total = $reservation->history_transaction->price - ($reservation->history_transaction->discount ?? 0);
                                @endphp
                                <td style="color:red; font-size: 20px" class="font-weight-bold">{{"IDR " . number_format($total,0,',','.')}}</td>
                              </tr>
                        </table>
                        <hr>
                        <div class="terms-condtions">
                            <h6 class="font-weight-bold ml-2">Terms on conditions</h6>
                            <ul>
                                <li>complete payment in 30 minutes. If it exceeds that time the order will be automatically canceled.</li>
                                <li>An invoice will be issued once the payment has been successfully confirmed and the order cannot be changed afterward.</li>
                                <li>Payment settlement must be completed according to the bill.</li>
                            </ul>
                        </div>

                        <div class="payment methods">
                            <h6 class="payment-methods font-weight-bold ml-2">Payment Methods</h6>
                            <table style="max-width:640px;padding:0px 20px;margin:10px 0px;background-color:#ffffff;font-size:0.9em;width:100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#F2F2F2">
                                <tbody>
                               
                                    <tr>
                                        <td style="padding:10px 25px" align="left" valign="middle"><span style="font-family:-apple-system,BlinkMacSystemFont,'Segoe UI','Roboto','Ubuntu','Open Sans','Helvetica Neue',sans-serif;font-size:medium;color:#888888"><img src="https://ci6.googleusercontent.com/proxy/vT32UKmlOWGQQ5bpjUudb910AVUpiS78tEPvEO9Hoa_xlET8_SoMNNGTlQmWx-zn1dbplTBJTS30pywswype0uH7F_qpMw=s0-d-e1-ft#https://static.domainesia.com/assets/images/bca.png" alt="bca" height="40px" class="CToWUd"></span></td>
                                        <td style="text-align:left" align="left" valign="middle">
                                        <p><span style="font-family:-apple-system,BlinkMacSystemFont,'Segoe UI','Roboto','Ubuntu','Open Sans','Helvetica Neue',sans-serif;font-size:medium;color:#888888"><strong>Bank BCA</strong> </span><br><span style="font-family:-apple-system,BlinkMacSystemFont,'Segoe UI','Roboto','Ubuntu','Open Sans','Helvetica Neue',sans-serif;font-size:medium;color:#888888"> 12345xxxx a/n Hetero Space</span></p>
                                        </td>
                                    </tr>
                                
                                </tbody>
                            </table>
                        </div>
                        <div class="order mt-4">
                            <div class="text-right">
                                <button type="button" class="btn btn-success btn-lg font-weight-bold shadow btn-confirm-order">Confirm Order</button>
                            </div>
                        </div>
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
                $.ajax({
                    url: "{{route('seat.confirm-order')}}",
                    type: "POST",
                    data: {
                        _token: "{{csrf_token()}}",
                        id: $('#order_id').val()
                    },
                    beforeSend: function() {
                        $(this).prop('disabled', true)
                    },
                    success: function(res) {
                        if(res.code == 200) {
                            window.location.href = "{{route('seat.list-order')}}"
                        }else {
                            Swal.fire('Oops',res.message,'info');
                        }
                        $(this).prop('disabled', false)
                    },
                    error: function(err) {
                        $(this).prop('disabled', false)
                        Swal.fire('Oops',err.responseJSON.message,'info');
                    }
                });
                
            })
        })
    </script>
@endsection