@extends('layouts.app')
@section('css')
<link rel="stylesheet" href="{{asset('assets/css/reservations.css')}}">
@endsection
@section('content')
    @include('layouts.overview',['text' => 'Reservation', 'icon' => 'mdi mdi-view-agenda'])
    <div class="container">
        <div class="row">
            <div class="container">
                <div class="row mb-4">
                    <div class="col-md-3">
                        <label for="date_reservation_start">Date Reservation</label>
                        <input type="date" name="date_reservation_start" id="date_reservation_start" class="form-control mt-2 mb-3">
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-2 door-reservation align-items-center justify-content-center d-flex">
                        <h3 class="text-white font-weight-bold" style="transform: rotate(90deg)">Door</h2>
                    </div>
                    <div class="col-md-10">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="text-center">
                                    <label class="checkbox-button-available shadow" style="border-radius: 3px">
                                        <span class="p-2 button-reservation" id="chair-1" data-id="chair-1"></span>
                                    </label>
                                    <label class="checkbox-button-reserved shadow" style="border-radius: 3px">
                                        <span class="p-2 button-reservation" id="chair-2" data-id="chair-2"></span>
                                    </label>
                                </div>
                                
                                <div class="table-reservation">
                                    <p class="text-white font-weight-bold mt-3">Table One</p> 
                                </div>
                                
                                <div class="text-center">
                                    <label class="checkbox-button-available shadow" style="border-radius: 3px">
                                        <span class="p-2 button-reservation" id="chair-3" data-id="chair-3"></span>
                                    </label>
                                    <label class="checkbox-button-available shadow" style="border-radius: 3px">
                                        <span class="p-2 button-reservation" id="chair-4" data-id="chair-4"></span>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-center">
                                    <label class="checkbox-button-available shadow" style="border-radius: 3px">
                                        <span class="p-2 button-reservation" id="chair-5" data-id="chair-5"></span>
                                    </label>
                                    <label class="checkbox-button-available shadow" style="border-radius: 3px">
                                        <span class="p-2 button-reservation" id="chair-6" data-id="chair-6"></span>
                                    </label>
                                </div>
                                
                                <div class="table-reservation">
                                    <p class="text-white font-weight-bold mt-3">Table Two</p> 
                                </div>
                                
                                <div class="text-center">
                                    <label class="checkbox-button-available shadow" style="border-radius: 3px">
                                        <span class="p-2 button-reservation" id="chair-7" data-id="chair-7"></span>
                                    </label>
                                    <label class="checkbox-button-available shadow" style="border-radius: 3px">
                                        <span class="p-2 button-reservation" id="chair-8" data-id="chair-8"></span>
                                    </label>
                                </div>
                            </div>
                            
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="text-center">
                                    <label class="checkbox-button-available shadow" style="border-radius: 3px">
                                        <span class="p-2 button-reservation" id="chair-9" data-id="chair-9"></span>
                                    </label>
                                    <label class="checkbox-button-available shadow" style="border-radius: 3px">
                                        <span class="p-2 button-reservation" id="chair-10" data-id="chair-10"></span>
                                    </label>
                                </div>
                                
                                <div class="table-reservation">
                                    <p class="text-white font-weight-bold mt-3">Table Three</p> 
                                </div>
                                
                                <div class="text-center">
                                    <label class="checkbox-button-available shadow" style="border-radius: 3px">
                                        <span class="p-2 button-reservation" id="chair-11" data-id="chair-11"></span>
                                    </label>
                                    <label class="checkbox-button-available shadow" style="border-radius: 3px">
                                        <span class="p-2 button-reservation" id="chair-12" data-id="chair-12"></span>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-center">
                                    <label class="checkbox-button-available shadow" style="border-radius: 3px">
                                        <span class="p-2 button-reservation" id="chair-13" data-id="chair-13"></span>
                                    </label>
                                    <label class="checkbox-button-available shadow" style="border-radius: 3px">
                                        <span class="p-2 button-reservation" id="chair-14" data-id="chair-14"></span>
                                    </label>
                                </div>
                                
                                <div class="table-reservation">
                                    <p class="text-white font-weight-bold mt-3">Table Four</p> 
                                </div>
                                
                                <div class="text-center">
                                    <label class="checkbox-button-available shadow" style="border-radius: 3px">
                                        <span class="p-2 button-reservation" id="chair-15" data-id="chair-15"></span>
                                    </label>
                                    <label class="checkbox-button-available shadow" style="border-radius: 3px">
                                        <span class="p-2 button-reservation" id="chair-16" data-id="chair-16"></span>
                                    </label>
                                </div>
                            </div>
                            
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="text-center">
                                    <label class="checkbox-button-available shadow" style="border-radius: 3px">
                                        <span class="p-2 button-reservation" id="chair-17" data-id="chair-17"></span>
                                    </label>
                                    <label class="checkbox-button-available shadow" style="border-radius: 3px">
                                        <span class="p-2 button-reservation" id="chair-18" data-id="chair-18"></span>
                                    </label>
                                </div>
                                
                                <div class="table-reservation">
                                    <p class="text-white font-weight-bold mt-3">Table Five</p> 
                                </div>
                                
                                <div class="text-center">
                                    <label class="checkbox-button-available shadow" style="border-radius: 3px">
                                        <span class="p-2 button-reservation" id="chair-19" data-id="chair-19"></span>
                                    </label>
                                    <label class="checkbox-button-available shadow" style="border-radius: 3px">
                                        <span class="p-2 button-reservation" id="chair-20" data-id="chair-20"></span>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-center">
                                    <label class="checkbox-button-available shadow" style="border-radius: 3px">
                                        <span class="p-2 button-reservation" id="chair-21" data-id="chair-21"></span>
                                    </label>
                                    <label class="checkbox-button-available shadow" style="border-radius: 3px">
                                        <span class="p-2 button-reservation" id="chair-22" data-id="chair-22"></span>
                                    </label>
                                </div>
                                
                                <div class="table-reservation">
                                    <p class="text-white font-weight-bold mt-3">Table Six</p> 
                                </div>
                                
                                <div class="text-center">
                                    <label class="checkbox-button-available shadow" style="border-radius: 3px">
                                        <span class="p-2 button-reservation" id="chair-23" data-id="chair-23"></span>
                                    </label>
                                    <label class="checkbox-button-available shadow" style="border-radius: 3px">
                                        <span class="p-2 button-reservation" id="chair-24" data-id="chair-24"></span>
                                    </label>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                </div>
         
               
            </div>
        </div>
    </div>
    <div class="modal" id="modal-reservation" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
          <div class="modal-content">
            <div class="modal-header">
              {{-- <h5 class="modal-title">Modal title</h5> --}}
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <p class="text-center p-4">Are you sure for pick chair <span class="code-chair font-weight-bold"></span></p>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-info btn-select-chair">Select</button>
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
          </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        $(document).ready(function() {
            $(document).on('click','.button-reservation', function() {
                const chair = $(this).data('id')
                $('.code-chair').html(chair.toUpperCase())
                $('#modal-reservation').modal('show')
            })

            $(document).on('click', '.btn-select-chair', function() {
                window.location.href = "{{route('seat.order-summary', ['id' => 1])}}"
            })
        })
    </script>
@endsection