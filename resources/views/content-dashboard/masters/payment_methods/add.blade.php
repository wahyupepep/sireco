@extends('layouts.app')

@section('content')
    @include('layouts.overview',['text' => 'Add Payment Method', 'icon' => 'mdi mdi-credit-card'])
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
                      <form class="forms-sample" action="{{route('master.payment_method.store')}}" method="POST">
                        @csrf
                        <div class="form-group">
                           <label for="name">Name Payment Method/ Bank <sup style="color:red">*</sup></label>
                           <input type="text" name="name" id="name" class="form-control" value="{{old('name')}}" placeholder="Ex: BCA" required>
                        </div>
                        <div class="form-group">
                           <label for="account_number">Account Number <sup style="color:red">*</sup></label>
                           <input type="text" name="account_number" id="account_number" class="form-control" value="{{old('account_number')}}" placeholder="Ex: 12xxxx" required>
                        </div>
                        <div class="form-group">
                           <label for="account_name">Account Name <sup style="color:red">*</sup></label>
                           <input type="text" name="account_name" id="account_name" class="form-control" value="{{old('account_name')}}" placeholder="Ex: John Doe" required>
                        </div>
                        <div class="mb-3">
                          <span class="text-muted" style="font-size:11px">note: <sup style="color:red">*</sup> (required)</span>
                        </div>
                        <a href="{{route('master.payment_method.index')}}" class="btn bg-blue text-white">Back</a>
                        <button type="submit" class="btn mr-2 bg-green text-white">Submit</button>
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
            
        })
    </script>
@endsection


