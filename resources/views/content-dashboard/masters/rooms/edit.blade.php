@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{asset('assets/css/dropzone.min.css')}}">
@endsection

@section('content')
    @include('layouts.overview',['text' => 'Edit Payment Method', 'icon' => 'mdi mdi-credit-card'])
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        @if (session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif
                        @if (count($errors) > 0)
                          <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                            </button>
                          </div>  
                        @endif
                      <form class="forms-sample" action="{{route('master.room.update', ['id' => $id])}}" method="POST">
                        @csrf
                        @method('PUT')
                        @csrf
                        <div class="form-group">
                           <label for="name">Name Room<sup style="color:red">*</sup></label>
                           <input type="text" name="name" id="name" class="form-control" value="{{$room->name}}" placeholder="Ex: Maneka" required>
                        </div>
                        <a href="{{route('master.room.index')}}" class="btn bg-blue text-white">Back</a>
                        <button type="submit" class="btn mr-2 bg-green text-white d-none" id="btn-save">Submit</button>
                        <button type="button" class="btn mr-2 btn-warning text-white" id="btn-edit">Edit</button>
                      </form>
                    </div>
                  </div>
            </div>
        </div>
    </div>
    <br>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            $('input').prop('readonly', true)
            $('#btn-edit').on('click', function() {
                $('input').prop('readonly', false)
                $(this).addClass('d-none')
                $('#btn-save').removeClass('d-none')
            })
        });
    </script>
@endsection


