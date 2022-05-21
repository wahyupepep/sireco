@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{asset('assets/css/dropzone.min.css')}}">
    <link href="https://cdn.jsdelivr.net/npm/froala-editor@latest/css/froala_editor.pkgd.min.css" rel="stylesheet" type="text/css" />
@endsection

@section('content')
    @include('layouts.overview',['text' => 'Edit Menu', 'icon' => 'mdi mdi-food-fork-drink'])
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
                      <form class="forms-sample" action="{{route('menu.update',['id' => $id])}}" enctype="multipart/form-data" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="menu">Menu <sup style="color:red">*</sup></label>
                            <input type="text" class="form-control" id="menu" placeholder="Masukkan nama menu..." name="menu" value="{{ $menu->name }}" required>
                        </div>
                        <div class="form-group">
                          <label for="category">Kategori <sup style="color:red">*</sup></label>
                          <select class="form-control" id="category" name="category" required>
                           <option value="{{$category->id}}">{{$category->name}}</option>
                         </select>
                        </div>
                        <div class="form-group">
                            <label for="price">Harga <sup style="color:red">*</sup></label>
                            <input type="text" class="form-control" id="price" placeholder="Masukkan harga menu..." name="price" required>
                        </div>
                        <div class="form-group">
                            <div class="dropzone"></div>
                        </div>
                        <div class="form-group">
                            <textarea name="description" id="description" cols="30" rows="10">{{$menu->description}}</textarea>
                        </div>
                        <div class="mb-3">
                          <span class="text-muted" style="font-size:11px">Keterangan: <sup style="color:red">*</sup> (wajib diisi)</span>
                        </div>
                        <a href="{{route('category.index')}}" class="btn bg-blue text-white">Kembali</a>
                        <button type="submit" class="btn mr-2 bg-green text-white">Simpan</button>
                      </form>
                    </div>
                  </div>
            </div>
        </div>
    </div>
    <br>
@endsection

@section('script')
    <script src="{{asset('assets/js/dropzone.min.js')}}"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/froala-editor@latest/js/froala_editor.pkgd.min.js"></script>
    <script>
        Dropzone.autoDiscover = false;
        $(document).ready(function() {
            let uploadedDocumentMap = {}
            let fileName = "{{$fileName}}"
            let fileSize = "{{$fileSize}}"
            let filePath = "{{$filePath}}"
            let request = 'fetch';
            
            let rupiah = document.getElementById('price');
            $('#price').val(formatRupiah("{{$menu->price}}", 'Rp. '))
           
            rupiah.addEventListener('keyup', function(e){
                // tambahkan 'Rp.' pada saat form di ketik
                // gunakan fungsi formatRupiah() untuk mengubah angka yang di ketik menjadi format angka
                rupiah.value = formatRupiah(this.value, 'Rp. ');
            });
            new FroalaEditor('textarea#description');
            $(".dropzone").dropzone({
                maxFiles: 1,
                maxFilesize: 2, // MB
                addRemoveLinks: true,
                acceptedFiles: ".jpg,.jpeg,.png,.gif,.png",
                url: "{{ route('menu.store-image') }}",
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                success: function(file, response) {
                    $('form').append('<input type="hidden" name="category_file" value="' + response.name + '">')
                    request = 'upload'
                    uploadedDocumentMap[file.name] = response.name
                },
                removedfile: function(file) {
                    
                    file.previewElement.remove()

                    let name = ''
                    if(request == 'upload') {
                        if (typeof file.file_name !== 'undefined') {
                            name = file.file_name
                        } else {
                            name = uploadedDocumentMap[file.name]
                        }
                    }else {
                        name = file.name
                    }
                   

                    if (name != '') {
                        $.ajax({
                            type: 'POST',
                            url: "{{ route('menu.delete-image') }}",
                            data: {
                                _token: "{{ csrf_token() }}",
                                name: name
                            },
                            success: function(data) {
                                $('form').find('input[name="category_file"][value="' +
                                    name + '"]').remove()
                            },
                            error: function(err) {
                                console.log(
                                    `Terjadi kesalahan sistem dengan pesan kesalahan  ${err.responseJSON.message}`
                                    );
                            }
                        })
                    }
                    

                },
                init: function () {
                    myDropzone = this;

                    
                    let mockFile = { name: fileName, size: fileSize};

                    // myDropzone.emit("addedfile", mockFile);

                    // myDropzone.emit("thumbnail", mockFile, filePath);

                    // myDropzone.emit("complete", mockFile);
                    if(fileName != '' && fileSize != '') {
                        myDropzone.displayExistingFile(mockFile, filePath);
                    }
                   
               
                }
            });
            $('#category').select2({
                ajax: {
                    url: '{{ route("menu.searchcategory") }}',
                    dataType: 'JSON',
                    delay: 250,
                    data: function(params) {
                        return {
                            search: params.term
                        }
                    },
                    processResults: function (data) {
                    
                        var results = [];
                    
                        $.each(data, function(index, item){
                            results.push({
                                id: item.id,
                                text : item.name
                            });
                        });
                        return{
                            results: results
                        };

                    }
                }

            })
        });
        	/* Fungsi formatRupiah */
		function formatRupiah(angka, prefix){
			var number_string = angka.replace(/[^,\d]/g, '').toString(),
			split   		= number_string.split(','),
			sisa     		= split[0].length % 3,
			rupiah     		= split[0].substr(0, sisa),
			ribuan     		= split[0].substr(sisa).match(/\d{3}/gi);

			// tambahkan titik jika yang di input sudah menjadi angka ribuan
			if(ribuan){
				separator = sisa ? '.' : '';
				rupiah += separator + ribuan.join('.');
			}

			rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
			return prefix == undefined ? rupiah : (rupiah ?  rupiah : '');
		}
    </script>
@endsection


