@extends('layouts.app')
@section('css')
<style>
    .nav-pills .nav-link.active, .nav-pills .show > .nav-link {
        background-color: #ff395b !important;
        color: white !important;
    }
    .nav-item a{
        color: #ff395b
    }
</style>
@endsection
@section('content')

    @include('layouts.overview',['text' => 'List Order', 'icon' => 'mdi mdi-cart-plus'])
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                    <li class="nav-item">
                      <a class="nav-link active" id="pills-home-tab" data-toggle="pill" href="#pills-home" role="tab" aria-controls="pills-home" aria-selected="true">Active</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" id="pills-profile-tab" data-toggle="pill" href="#pills-profile" role="tab" aria-controls="pills-profile" aria-selected="false">In Progress</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" id="pills-contact-tab" data-toggle="pill" href="#pills-contact" role="tab" aria-controls="pills-contact" aria-selected="false">Completed</a>
                    </li>
                </ul>
            </div>
            <div class="col-md-12 mb-4">
                <div class="tab-content" id="pills-tabContent">
                    <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
                        <table class="table table-striped">
                            <thead>
                              <tr>
                                <th>No</th>
                                <th>No. Invoice </th>
                                <th>Valid Date</th>
                                <th>Chair</th>
                                <th>Action</th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr>
                                <td class="py-1">
                                  1
                                </td>
                                <td> INV/001/A1/2706022</td>
                                <td>
                                    27 June 2022
                                </td>
                                <td> A1</td>
                                <td>
                                    <button type="button" class="btn btn-gradient-info btn-icon btn-detail-order">
                                        <i class="mdi mdi-eye"></i>
                                    </button>
                                </td>
                              </tr>
                              
                            </tbody>
                          </table>
                    </div>
                    <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">
                        {{-- <div class="empty-order">
                            <img src="{{asset('assets/images/hetero.png')}}" alt="hetero-space-logo" width="150" height="150" class="img-responsive img-fluid d-block mx-auto">
                            <h4 class="font-weight-bold text-center mt-3">Order Empty</h4>
                        </div> --}}
                        <table class="table table-striped">
                            <thead>
                              <tr>
                                <th>No</th>
                                <th>No. Invoice </th>
                                <th>Valid Date</th>
                                <th>Chair</th>
                                <th>Action</th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr>
                                <td class="py-1">
                                  1
                                </td>
                                <td> INV/001/A1/2706022</td>
                                <td>
                                    27 June 2022
                                </td>
                                <td> A1</td>
                                <td>
                                    <button type="button" class="btn btn-gradient-success btn-icon ml-2 btn-payment-order">
                                        <i class="mdi mdi-upload"></i>
                                    </button>
                                    <button type="button" class="btn btn-gradient-info btn-icon btn-icon btn-detail-order">
                                        <i class="mdi mdi-eye"></i>
                                    </button>
                                </td>
                              </tr>
                              
                            </tbody>
                          </table>
                    </div>
                    <div class="tab-pane fade" id="pills-contact" role="tabpanel" aria-labelledby="pills-contact-tab">
                        <table class="table table-striped">
                            <thead>
                              <tr>
                                <th>No</th>
                                <th>No. Invoice </th>
                                <th>Valid Date</th>
                                <th>Chair</th>
                                <th>Action</th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr>
                                <td class="py-1">
                                  1
                                </td>
                                <td> INV/001/A1/2706022</td>
                                <td>
                                    27 June 2022
                                </td>
                                <td> A1</td>
                                <td>
                                    <button type="button" class="btn btn-gradient-secondary btn-icon btn-icon btn-detail-order">
                                        <i class="mdi mdi-eye"></i>
                                    </button>
                                </td>
                              </tr>
                              
                            </tbody>
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
            $(document).on('click','.btn-detail-order',function() {
                window.location.href="{{route('seat.detail-order', ['id' => 1])}}"
            })

            $(document).on('click','.btn-payment-order', function() {
              window.location.href="{{route('seat.payment-order', ['id' => 1])}}"
            })
        })
    </script>
@endsection