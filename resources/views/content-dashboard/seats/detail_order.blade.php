@extends('layouts.app')
@section('content')
    @include('layouts.overview',['text' => 'Detail Order', 'icon' => 'mdi mdi-cart-outline'])
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
                                <td class="font-weight-bold">No. INV</td>
                                <td>:</td>
                                <td>INV/001/A1/2706022</td>
                              </tr>
                              <tr>
                                <td class="font-weight-bold">Name</td>
                                <td>:</td>
                                <td>Wahyu Febrianto Pepep</td>
                              </tr>
                              <tr>
                                <td class="font-weight-bold">Date</td>
                                <td>:</td>
                                <td>27 Juni 2022</td>
                              </tr>
                              <tr>
                                <td class="font-weight-bold">Duration</td>
                                <td>:</td>
                                <td>2 Days</td>
                              </tr>
                              <tr>
                                <td class="font-weight-bold">Price</td>
                                <td>:</td>
                                <td>IDR 120.000</td>
                              </tr>
                              <tr>
                                <td class="font-weight-bold">Discount</td>
                                <td>:</td>
                                <td>-</td>
                              </tr>
                              <tr>
                                <td class="font-weight-bold">Chair</td>
                                <td>:</td>
                                <td>
                                    <h6 style="color: #aa1c91" class="font-weight-bold"> A1 </h6>
                                </td>
                              </tr>
                              <tr>
                                <td class="font-weight-bold" style="color:green; font-size: 20px">Total</td>
                                <td>:</td>
                                <td style="color:green; font-size: 20px" class="font-weight-bold">IDR 120.000 <span class="badge badge-success ml-3 font-weight-bold">PAID</span></td>
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