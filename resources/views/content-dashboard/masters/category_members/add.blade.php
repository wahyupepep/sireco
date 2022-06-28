@extends('layouts.app')

@section('content')
    @include('layouts.overview',['text' => 'Add Member Type', 'icon' => 'mdi mdi-account-group-outline'])
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
                      <form class="forms-sample" action="{{route('master.category_member.store')}}" method="POST">
                        @csrf
                        <div class="form-group">
                           <label for="name">Member Type<sup style="color:red">*</sup></label>
                           <select class="form-control" id="name" name="name" required>
                              @forelse ($type_members as $key => $type_member)
                                  <option value="{{$type_member}}">{{$type_member}}</option>
                              @empty
                                  <option value="">Data Type Member Not Found</option>
                              @endforelse
                          </select>
                        </div>
                        <div class="form-group">
                           <label for="price">Price <sup style="color:red">*</sup></label>
                           <input type="text" name="price" id="price" class="form-control" value="{{old('price')}}" placeholder="Ex: 120000" required>
                        </div>
                        {{-- <div class="form-group">
                           <label for="discount">Discount</label>
                           <input type="number" name="discount" id="discount" class="form-control" value="{{old('discount')}}" placeholder="Ex: John Doe" max="100">
                        </div> --}}
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
          var rupiah = document.getElementById("price");
          let discount = document.getElementById("discount");
          rupiah.addEventListener("keyup", function(e) {
            // tambahkan 'Rp.' pada saat form di ketik
            // gunakan fungsi formatRupiah() untuk mengubah angka yang di ketik menjadi format angka
            rupiah.value = formatRupiah(this.value, "Rp. ");
          });

          discount.addEventListener("keyup", function(e) {
             if(discount.value > 100) {
               discount.value = 100
             }
          })

          /* Fungsi formatRupiah */
          function formatRupiah(angka, prefix) {
            var number_string = angka.replace(/[^,\d]/g, "").toString(),
              split = number_string.split(","),
              sisa = split[0].length % 3,
              rupiah = split[0].substr(0, sisa),
              ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            // tambahkan titik jika yang di input sudah menjadi angka ribuan
            if (ribuan) {
              separator = sisa ? "." : "";
              rupiah += separator + ribuan.join(".");
            }

            rupiah = split[1] != undefined ? rupiah + "," + split[1] : rupiah;
            return prefix == undefined ? rupiah : rupiah ? rupiah : "";
          }
      })
    </script>
@endsection


