@extends('layouts.app')
@section('css')
<style>
    .form-check .form-check-label {
        display: contents !important;
    }
</style>
<link rel="stylesheet" href="{{asset('assets/css/reservations.css')}}">
@endsection
@section('content')
    @include('layouts.overview',['text' => 'Seats', 'icon' => 'mdi mdi-view-agenda'])
    <div class="container">
        <div class="row">
            <div class="container">
                <div class="row mb-4">
                    <input type="hidden" name="chair_code_name" id="chair_code_name">
                    <div class="col-md-3">
                        <label for="date_reservation_start">Date Reservation</label>
                        <input type="date" name="date_reservation_start" id="date_reservation_start" class="form-control mt-2 mb-3" min="{{date('Y-m-d')}}" value="{{date('Y-m-d')}}">
                    </div>
                    <div class="col-md-3">
                        <label for="radio-member">Categoy Member</label>
                        <div class="d-flex">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="member" id="member" value="1" checked>
                                <label class="form-check-label" for="member">Member</label>
                              </div>
                              <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="member" id="not_member" value="0">
                                <label class="form-check-label" for="not_member">Not Member</label>
                              </div>
                        </div>
                    </div>
                </div>
                <div class="row row-member mt-3 mb-4">
                    <div class="col-md-12">
                        <label for="member_name">Member Name</label>
                        <select class="form-control" name="member_name" id="member_name" style="width: 100% !important">
                            <option value="">Select Member</option>
                        </select>

                        
                    </div>
                    <div class="col-md-12 mt-3 package-member d-none">
                        <label for="package_member">Package Member</label>
                        <select class="form-control mt-2 mb-3" id="package_member" name="package_member">
                            @forelse ($category_members as $category_member)
                                <option value="{{$category_member->id}}">{{$category_member->name}}</option>
                            @empty
                                <option value="">Package Not Found</option>
                            @endforelse
                        </select>
                    </div>
                </div>
                <div class="row row-not-member mt-3 mb-4 d-none">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="fullname">Fullname</label>
                            <input type="text" class="form-control" id="fullname" name="fullname" value="{{old('fullname')}}" placeholder="Input your name...">
                            @error('fullname')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" class="form-control" id="username" name="username" value="{{old('username')}}" placeholder="Input you username...">
                            @error('username')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{old('email')}}" placeholder="Input your email...">
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-12 mt-3">
                        <label for="package_member">Package Member</label>
                        <select class="form-control mt-2 mb-3" id="package_member" name="package_member">
                            @forelse ($category_members as $category_member)
                                <option value="{{$category_member->id}}">{{$category_member->name}}</option>
                            @empty
                                <option value="">Package Not Found</option>
                            @endforelse
                        </select>
                    </div>
                </div>   
                <div class="row mb-4">
                    <div class="col-md-1 door-reservation align-items-center justify-content-center d-flex" style="height: 100px; background-color:black">
                        <h3 class="text-white font-weight-bold" style="transform: rotate(90deg)">Door</h2>
                    </div>
                    <div class="col-md-10" id="reservation-chairs">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="text-center">
                                    <label class="{{in_array('chair-1', $seat_codes) ?  "checkbox-button-reserved": "checkbox-button-available"}} shadow btn-chair-1" style="border-radius: 3px">
                                        <span class="p-2 {{in_array('chair-1', $seat_codes) ?  "": "button-reservation"}}" id="chair-1" data-id="chair-1"></span>
                                    </label>
                                    <label class="{{in_array('chair-2', $seat_codes) ?  "checkbox-button-reserved": "checkbox-button-available"}} shadow btn-chair-2" style="border-radius: 3px">
                                        <span class="p-2 {{in_array('chair-2', $seat_codes) ?  "": "button-reservation"}}" id="chair-2" data-id="chair-2"></span>
                                    </label>
                                </div>
                                
                                <div class="table-reservation">
                                    <p class="text-white font-weight-bold mt-3"></p> 
                                </div>
                                
                                <div class="text-center">
                                    <label class="{{in_array('chair-3', $seat_codes) ?  "checkbox-button-reserved": "checkbox-button-available"}} shadow btn-chair-3" style="border-radius: 3px">
                                        <span class="p-2 {{in_array('chair-3', $seat_codes) ?  "": "button-reservation"}}" id="chair-3" data-id="chair-3"></span>
                                    </label>
                                    <label class="{{in_array('chair-4', $seat_codes) ?  "checkbox-button-reserved": "checkbox-button-available"}} shadow btn-chair-4" style="border-radius: 3px">
                                        <span class="p-2 {{in_array('chair-4', $seat_codes) ?  "": "button-reservation"}}" id="chair-4" data-id="chair-4"></span>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-center">
                                    <label class="{{in_array('chair-5', $seat_codes) ?  "checkbox-button-reserved": "checkbox-button-available"}} shadow btn-chair-5" style="border-radius: 3px">
                                        <span class="p-2 {{in_array('chair-5', $seat_codes) ?  "": "button-reservation"}}" id="chair-5" data-id="chair-5"></span>
                                    </label>
                                    <label class="{{in_array('chair-6', $seat_codes) ?  "checkbox-button-reserved": "checkbox-button-available"}} shadow btn-chair-6" style="border-radius: 3px">
                                        <span class="p-2 {{in_array('chair-6', $seat_codes) ?  "": "button-reservation"}}" id="chair-6" data-id="chair-6"></span>
                                    </label>
                                </div>
                                
                                <div class="table-reservation">
                                    <p class="text-white font-weight-bold mt-3"></p> 
                                </div>
                                
                                <div class="text-center">
                                    <label class="{{in_array('chair-7', $seat_codes) ?  "checkbox-button-reserved": "checkbox-button-available"}} shadow btn-chair-7" style="border-radius: 3px">
                                        <span class="p-2 {{in_array('chair-7', $seat_codes) ?  "": "button-reservation"}}" id="chair-7" data-id="chair-7"></span>
                                    </label>
                                    <label class="{{in_array('chair-8', $seat_codes) ?  "checkbox-button-reserved": "checkbox-button-available"}} shadow btn-chair-8" style="border-radius: 3px">
                                        <span class="p-2 {{in_array('chair-8', $seat_codes) ?  "": "button-reservation"}}" id="chair-8" data-id="chair-8"></span>
                                    </label>
                                </div>
                            </div>
                            
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="text-center">
                                    <label class="{{in_array('chair-9', $seat_codes) ?  "checkbox-button-reserved": "checkbox-button-available"}} shadow btn-chair-9" style="border-radius: 3px">
                                        <span class="p-2 {{in_array('chair-9', $seat_codes) ?  "": "button-reservation"}}" id="chair-9" data-id="chair-9"></span>
                                    </label>
                                    <label class="{{in_array('chair-10', $seat_codes) ?  "checkbox-button-reserved": "checkbox-button-available"}} shadow btn-chair-10" style="border-radius: 3px">
                                        <span class="p-2 {{in_array('chair-10', $seat_codes) ?  "": "button-reservation"}}" id="chair-10" data-id="chair-10"></span>
                                    </label>
                                </div>
                                
                                <div class="table-reservation">
                                    <p class="text-white font-weight-bold mt-3"></p> 
                                </div>
                                
                                <div class="text-center">
                                    <label class="{{in_array('chair-11', $seat_codes) ?  "checkbox-button-reserved": "checkbox-button-available"}} shadow btn-chair-11" style="border-radius: 3px">
                                        <span class="p-2 {{in_array('chair-11', $seat_codes) ?  "": "button-reservation"}}" id="chair-11" data-id="chair-11"></span>
                                    </label>
                                    <label class="{{in_array('chair-12', $seat_codes) ?  "checkbox-button-reserved": "checkbox-button-available"}} shadow btn-chair-12" style="border-radius: 3px">
                                        <span class="p-2 {{in_array('chair-12', $seat_codes) ?  "": "button-reservation"}}" id="chair-12" data-id="chair-12"></span>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-center">
                                    <label class="{{in_array('chair-13', $seat_codes) ?  "checkbox-button-reserved": "checkbox-button-available"}} shadow btn-chair-13" style="border-radius: 3px">
                                        <span class="p-2 {{in_array('chair-13', $seat_codes) ?  "": "button-reservation"}}" id="chair-13" data-id="chair-13"></span>
                                    </label>
                                    <label class="{{in_array('chair-14', $seat_codes) ?  "checkbox-button-reserved": "checkbox-button-available"}} shadow btn-chair-14" style="border-radius: 3px">
                                        <span class="p-2 {{in_array('chair-14', $seat_codes) ?  "": "button-reservation"}}" id="chair-14" data-id="chair-14"></span>
                                    </label>
                                </div>
                                
                                <div class="table-reservation">
                                    <p class="text-white font-weight-bold mt-3"></p> 
                                </div>
                                
                                <div class="text-center">
                                    <label class="{{in_array('chair-15', $seat_codes) ?  "checkbox-button-reserved": "checkbox-button-available"}} shadow btn-chair-15" style="border-radius: 3px">
                                        <span class="p-2 {{in_array('chair-15', $seat_codes) ?  "": "button-reservation"}}" id="chair-15" data-id="chair-15"></span>
                                    </label>
                                    <label class="{{in_array('chair-16', $seat_codes) ?  "checkbox-button-reserved": "checkbox-button-available"}} shadow btn-chair-16" style="border-radius: 3px">
                                        <span class="p-2 {{in_array('chair-16', $seat_codes) ?  "": "button-reservation"}}" id="chair-16" data-id="chair-16"></span>
                                    </label>
                                </div>
                            </div>
                            
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="text-center">
                                    <label class="{{in_array('chair-17', $seat_codes) ?  "checkbox-button-reserved": "checkbox-button-available"}} shadow btn-chair-17" style="border-radius: 3px">
                                        <span class="p-2 {{in_array('chair-17', $seat_codes) ?  "": "button-reservation"}}" id="chair-17" data-id="chair-17"></span>
                                    </label>
                                    <label class="{{in_array('chair-18', $seat_codes) ?  "checkbox-button-reserved": "checkbox-button-available"}} shadow btn-chair-18" style="border-radius: 3px">
                                        <span class="p-2 {{in_array('chair-18', $seat_codes) ?  "": "button-reservation"}}" id="chair-18" data-id="chair-18"></span>
                                    </label>
                                </div>
                                
                                <div class="table-reservation">
                                    <p class="text-white font-weight-bold mt-3"></p> 
                                </div>
                                
                                <div class="text-center">
                                    <label class="{{in_array('chair-19', $seat_codes) ?  "checkbox-button-reserved": "checkbox-button-available"}} shadow btn-chair-19" style="border-radius: 3px">
                                        <span class="p-2 {{in_array('chair-19', $seat_codes) ?  "": "button-reservation"}}" id="chair-19" data-id="chair-19"></span>
                                    </label>
                                    <label class="{{in_array('chair-20', $seat_codes) ?  "checkbox-button-reserved": "checkbox-button-available"}} shadow btn-chair-20" style="border-radius: 3px">
                                        <span class="p-2 {{in_array('chair-20', $seat_codes) ?  "": "button-reservation"}}" id="chair-20" data-id="chair-20"></span>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-center">
                                    <label class="{{in_array('chair-21', $seat_codes) ?  "checkbox-button-reserved": "checkbox-button-available"}} shadow btn-chair-21" style="border-radius: 3px">
                                        <span class="p-2 {{in_array('chair-21', $seat_codes) ?  "": "button-reservation"}}" id="chair-21" data-id="chair-21"></span>
                                    </label>
                                    <label class="{{in_array('chair-22', $seat_codes) ?  "checkbox-button-reserved": "checkbox-button-available"}} shadow btn-chair-22" style="border-radius: 3px">
                                        <span class="p-2 {{in_array('chair-22', $seat_codes) ?  "": "button-reservation"}}" id="chair-22" data-id="chair-22"></span>
                                    </label>
                                </div>
                                
                                <div class="table-reservation">
                                    <p class="text-white font-weight-bold mt-3"></p> 
                                </div>
                                
                                <div class="text-center">
                                    <label class="{{in_array('chair-23', $seat_codes) ?  "checkbox-button-reserved": "checkbox-button-available"}} shadow btn-chair-23" style="border-radius: 3px">
                                        <span class="p-2 {{in_array('chair-23', $seat_codes) ?  "": "button-reservation"}}" id="chair-23" data-id="chair-23"></span>
                                    </label>
                                    <label class="{{in_array('chair-24', $seat_codes) ?  "checkbox-button-reserved": "checkbox-button-available"}} shadow btn-chair-24" style="border-radius: 3px">
                                        <span class="p-2 {{in_array('chair-24', $seat_codes) ?  "": "button-reservation"}}" id="chair-24" data-id="chair-24"></span>
                                    </label>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                </div>
         
               
            </div>
        </div>
        <div class="text-right mb-4">
            <button type="button" class="btn btn-success btn-save-reservation shadow">BOOKING</button>
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
        $(document).ready(function(){
            let package_day
            let arrChair = [];
            $('#member_name').select2({
                width: '100%',
                ajax: {
                    url: '{{ route("member.data") }}',
                    dataType: 'JSON',
                    delay: 250,
                    data: function(params) {
                        return {
                            search: params.term
                        }
                    },
                    processResults: function (data) {
                    
                        var results = [];
                        results.push({
                            id: '',
                            text : 'Select Member Name'
                        })
                        $.each(data, function(index, item){
                            results.push({
                                id: item.id,
                                text : item.fullname
                            });
                        });
                        return{
                            results: results
                        };

                    }
                }
            });

            $('#member_name').on('select2:select', function (e) {
                const data = e.params.data
                $.ajax({
                    url: "{{route('member.checkdata')}}",
                    data: {
                        _token: "{{csrf_token()}}",
                        id: data.id
                    },
                    type: 'POST',
                    success: function(res) {
                        if(res.code == 200) {
                            if(res.data.select_package) {
                                $('.package-member').removeClass('d-none')
                            }else {
                                package_day = res.data.package_range_day
                                $('.package-member').addClass('d-none')
                            }
                        }else {
                            Swal.fire('Oops',res.message,'info');
                        }
                    },
                    error: function(err) {
                        Swal.fire('Oops',err.responseJSON.message,'info');
                    }
                });

            });
            

            $('input[type=radio]').on('click', function() {
                if($(this).val() == 1) {
                    $('.row-not-member').addClass('d-none')
                    $('.row-member').removeClass('d-none')
                }else {
                    $('.row-member').addClass('d-none')
                    $('.row-not-member').removeClass('d-none')
                    
                }
                $('#member_name').trigger('change').val('')
                $('#package_member').val(2)
                $('#fullname').val('')
                $('#username').val('')
                $('#email').val('')
            })

            $(document).on('change','#package_member', function() {
                package_day = $(this).val()
                console.log(package_day);
            })

            $(document).on('change','#date_reservation_start', function() {
                arrChair = [];
                const valueDate = $(this).val()
                let html = '';
                $.ajax({
                    url: "{{route('seat.list-seat')}}",
                    type: "POST",
                    data: {
                        _token: "{{csrf_token()}}",
                        valueDate: valueDate
                    },
                   
                    success: function(res) {
                       html += `
                       <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="text-center">
                                    <label class="${res.data.includes('chair-1') ? 'checkbox-button-reserved' : 'checkbox-button-available'} shadow btn-chair-1" style="border-radius: 3px">
                                        <span class="p-2 ${res.data.includes('chair-1') ? '' : 'button-reservation'}" id="chair-1" data-id="chair-1"></span>
                                    </label>
                                    <label class="${res.data.includes('chair-2') ? 'checkbox-button-reserved' : 'checkbox-button-available'} shadow btn-chair-2" style="border-radius: 3px">
                                        <span class="p-2 ${res.data.includes('chair-2') ? '' : 'button-reservation'}" id="chair-2" data-id="chair-2"></span>
                                    </label>
                                </div>
                                
                                <div class="table-reservation">
                                    <p class="text-white font-weight-bold mt-3"></p> 
                                </div>
                                
                                <div class="text-center">
                                    <label class="${res.data.includes('chair-3') ? 'checkbox-button-reserved' : 'checkbox-button-available'} shadow btn-chair-3" style="border-radius: 3px">
                                        <span class="p-2 ${res.data.includes('chair-3') ? '' : 'button-reservation'}" id="chair-3" data-id="chair-3"></span>
                                    </label>
                                    <label class="${res.data.includes('chair-4') ? 'checkbox-button-reserved' : 'checkbox-button-available'} shadow btn-chair-4" style="border-radius: 3px">
                                        <span class="p-2 ${res.data.includes('chair-4') ? '' : 'button-reservation'}" id="chair-4" data-id="chair-4"></span>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-center">
                                    <label class="${res.data.includes('chair-5') ? 'checkbox-button-reserved' : 'checkbox-button-available'} shadow btn-chair-5" style="border-radius: 3px">
                                        <span class="p-2 ${res.data.includes('chair-5') ? '' : 'button-reservation'}" id="chair-5" data-id="chair-5"></span>
                                    </label>
                                    <label class="${res.data.includes('chair-6') ? 'checkbox-button-reserved' : 'checkbox-button-available'} shadow btn-chair-6" style="border-radius: 3px">
                                        <span class="p-2 ${res.data.includes('chair-6') ? '' : 'button-reservation'}" id="chair-6" data-id="chair-6"></span>
                                    </label>
                                </div>
                                
                                <div class="table-reservation">
                                    <p class="text-white font-weight-bold mt-3"></p> 
                                </div>
                                
                                <div class="text-center">
                                    <label class="${res.data.includes('chair-7') ? 'checkbox-button-reserved' : 'checkbox-button-available'} shadow btn-chair-7" style="border-radius: 3px">
                                        <span class="p-2 ${res.data.includes('chair-7') ? '' : 'button-reservation'}" id="chair-7" data-id="chair-7"></span>
                                    </label>
                                    <label class="${res.data.includes('chair-8') ? 'checkbox-button-reserved' : 'checkbox-button-available'} shadow btn-chair-8" style="border-radius: 3px">
                                        <span class="p-2 ${res.data.includes('chair-8') ? '' : 'button-reservation'}" id="chair-8" data-id="chair-8"></span>
                                    </label>
                                </div>
                            </div> 
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="text-center">
                                    <label class="${res.data.includes('chair-9') ? 'checkbox-button-reserved' : 'checkbox-button-available'} shadow btn-chair-9" style="border-radius: 3px">
                                        <span class="p-2 ${res.data.includes('chair-9') ? '' : 'button-reservation'}" id="chair-9" data-id="chair-9"></span>
                                    </label>
                                    <label class="${res.data.includes('chair-10') ? 'checkbox-button-reserved' : 'checkbox-button-available'} shadow btn-chair-10" style="border-radius: 3px">
                                        <span class="p-2 ${res.data.includes('chair-10') ? '' : 'button-reservation'}" id="chair-10" data-id="chair-10"></span>
                                    </label>
                                </div>
                                
                                <div class="table-reservation">
                                    <p class="text-white font-weight-bold mt-3"></p> 
                                </div>
                                
                                <div class="text-center">
                                    <label class="${res.data.includes('chair-11') ? 'checkbox-button-reserved' : 'checkbox-button-available'} shadow btn-chair-11" style="border-radius: 3px">
                                        <span class="p-2 ${res.data.includes('chair-11') ? '' : 'button-reservation'}" id="chair-11" data-id="chair-11"></span>
                                    </label>
                                    <label class="${res.data.includes('chair-12') ? 'checkbox-button-reserved' : 'checkbox-button-available'} shadow btn-chair-12" style="border-radius: 3px">
                                        <span class="p-2 ${res.data.includes('chair-12') ? '' : 'button-reservation'}" id="chair-12" data-id="chair-12"></span>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-center">
                                    <label class="${res.data.includes('chair-13') ? 'checkbox-button-reserved' : 'checkbox-button-available'} shadow btn-chair-13" style="border-radius: 3px">
                                        <span class="p-2 ${res.data.includes('chair-13') ? '' : 'button-reservation'}" id="chair-13" data-id="chair-13"></span>
                                    </label>
                                    <label class="${res.data.includes('chair-14') ? 'checkbox-button-reserved' : 'checkbox-button-available'} shadow btn-chair-14" style="border-radius: 3px">
                                        <span class="p-2 ${res.data.includes('chair-14') ? '' : 'button-reservation'}" id="chair-14" data-id="chair-14"></span>
                                    </label>
                                </div>
                                
                                <div class="table-reservation">
                                    <p class="text-white font-weight-bold mt-3"></p> 
                                </div>
                                
                                <div class="text-center">
                                    <label class="${res.data.includes('chair-15') ? 'checkbox-button-reserved' : 'checkbox-button-available'} shadow btn-chair-15" style="border-radius: 3px">
                                        <span class="p-2 ${res.data.includes('chair-15') ? '' : 'button-reservation'}" id="chair-15" data-id="chair-15"></span>
                                    </label>
                                    <label class="${res.data.includes('chair-16') ? 'checkbox-button-reserved' : 'checkbox-button-available'} shadow btn-chair-16" style="border-radius: 3px">
                                        <span class="p-2 ${res.data.includes('chair-16') ? '' : 'button-reservation'}" id="chair-16" data-id="chair-16"></span>
                                    </label>
                                </div>
                            </div>
                            
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="text-center">
                                    <label class="${res.data.includes('chair-17') ? 'checkbox-button-reserved' : 'checkbox-button-available'} shadow btn-chair-17" style="border-radius: 3px">
                                        <span class="p-2 ${res.data.includes('chair-17') ? '' : 'button-reservation'}" id="chair-17" data-id="chair-17"></span>
                                    </label>
                                    <label class="${res.data.includes('chair-18') ? 'checkbox-button-reserved' : 'checkbox-button-available'} shadow btn-chair-18" style="border-radius: 3px">
                                        <span class="p-2 ${res.data.includes('chair-18') ? '' : 'button-reservation'}" id="chair-18" data-id="chair-18"></span>
                                    </label>
                                </div>
                                
                                <div class="table-reservation">
                                    <p class="text-white font-weight-bold mt-3"></p> 
                                </div>
                                
                                <div class="text-center">
                                    <label class="${res.data.includes('chair-19') ? 'checkbox-button-reserved' : 'checkbox-button-available'} shadow btn-chair-19" style="border-radius: 3px">
                                        <span class="p-2 ${res.data.includes('chair-19') ? '' : 'button-reservation'}" id="chair-19" data-id="chair-19"></span>
                                    </label>
                                    <label class="${res.data.includes('chair-20') ? 'checkbox-button-reserved' : 'checkbox-button-available'} shadow btn-chair-20" style="border-radius: 3px">
                                        <span class="p-2 ${res.data.includes('chair-20') ? '' : 'button-reservation'}" id="chair-20" data-id="chair-20"></span>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-center">
                                    <label class="${res.data.includes('chair-21') ? 'checkbox-button-reserved' : 'checkbox-button-available'} shadow btn-chair-21" style="border-radius: 3px">
                                        <span class="p-2 ${res.data.includes('chair-21') ? '' : 'button-reservation'}" id="chair-21" data-id="chair-21"></span>
                                    </label>
                                    <label class="${res.data.includes('chair-22') ? 'checkbox-button-reserved' : 'checkbox-button-available'} shadow btn-chair-22" style="border-radius: 3px">
                                        <span class="p-2 ${res.data.includes('chair-22') ? '' : 'button-reservation'}" id="chair-22" data-id="chair-22"></span>
                                    </label>
                                </div>
                                
                                <div class="table-reservation">
                                    <p class="text-white font-weight-bold mt-3"></p> 
                                </div>
                                
                                <div class="text-center">
                                    <label class="${res.data.includes('chair-23') ? 'checkbox-button-reserved' : 'checkbox-button-available'} shadow btn-chair-23" style="border-radius: 3px">
                                        <span class="p-2 ${res.data.includes('chair-23') ? '' : 'button-reservation'}" id="chair-23" data-id="chair-23"></span>
                                    </label>
                                    <label class="${res.data.includes('chair-24') ? 'checkbox-button-reserved' : 'checkbox-button-available'} shadow btn-chair-24" style="border-radius: 3px">
                                        <span class="p-2 ${res.data.includes('chair-24') ? '' : 'button-reservation'}" id="chair-24" data-id="chair-24"></span>
                                    </label>
                                </div>
                            </div>
                            
                        </div>
                       `
                       $('#reservation-chairs').html(html)
                    },
                    error: function(err) {
                       
                        Swal.fire('Oops',err.responseJSON.message,'info');
                    }
                })
            })

            $(document).on('click', '.button-reservation',function() {
                const chairId = $(this).data('id')
                const member = $('#member_name').val();
                const categoryMember = $('input[type=radio]:checked').val()

                if(member == '' && categoryMember == 1) {
                    Swal.fire('Oops','Please select member before selected chair','info');
                    return false
                }
                if(package_day == undefined || package_day == 1 || package_day == 2) { // TYPE HARIAN FOR MEMBER
                    if(arrChair.includes(chairId)) {
                        $(`.btn-${chairId}`).removeClass('checkbox-button-reserved')
                        $(`.btn-${chairId}`).addClass('checkbox-button-available')
                        let index = arrChair.indexOf(chairId)
                        arrChair.splice(index, 1)
                    }else {
                        $(`.btn-${chairId}`).addClass('checkbox-button-reserved')
                        $(`.btn-${chairId}`).removeClass('checkbox-button-available')
                        arrChair.push(chairId)
                    }
                }else {
                    if(arrChair.includes(chairId)) {
                        $(`.btn-${chairId}`).removeClass('checkbox-button-reserved')
                        $(`.btn-${chairId}`).addClass('checkbox-button-available')
                        let index = arrChair.indexOf(chairId)
                        arrChair.splice(index, 1)
                    }else {
                        if(arrChair.length == 0) {
                            $(`.btn-${chairId}`).addClass('checkbox-button-reserved')
                            $(`.btn-${chairId}`).removeClass('checkbox-button-available')
                            arrChair.push(chairId)
                        }else {
                            Swal.fire('Oops','Cant select chair more than one','info');
                        }
                    }
                   
                }
                console.log(arrChair);
            })
        })
    </script>
@endsection