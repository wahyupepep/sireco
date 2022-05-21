@extends('layouts.app')

@section('content')
    @include('layouts.overview',['text' => 'Hak Akses', 'icon' => 'mdi-lock'])
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                      <h4 class="card-title mb-3">Tambah Hak Akses</h4>
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
                      <form class="forms-sample" action="{{route('role.store')}}" method="POST">
                        @csrf
                        <div class="form-group">
                          <label for="name">Nama <sup style="color:red">*</sup></label>
                          <input type="text" class="form-control" id="name" placeholder="Contoh: Super Admin ..." name="name" value="{{ old('name') }}" required>
                        </div>
                        <div class="form-group">
                            @foreach ($permissions as $permission)
                            <div class="form-check">
                                <label class="form-check-label">   
                                    <input type="checkbox" class="form-check-input" name="permission[]" value="{{$permission->id}}"> {{$permission->name}} <i class="input-helper"></i>
                                </label>
                            </div>
                            @endforeach
                        </div>
                      
                        <div class="mb-3">
                          <span class="text-muted" style="font-size:11px">Keterangan: <sup style="color:red">*</sup> (wajib diisi)</span>
                        </div>
                        <a href="{{route('role.index')}}" class="btn btn-blue-material">Kembali</a>
                        <button type="submit" class="btn bg-green mr-2 text-white">Submit</button>
                      </form>
                    </div>
                  </div>
            </div>
        </div>
    </div>
    <br>
@endsection


