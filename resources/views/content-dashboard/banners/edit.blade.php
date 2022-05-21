@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{asset('assets/css/dropzone.min.css')}}">
@endsection

@section('content')
    @include('layouts.overview',['text' => 'Edit Banner', 'icon' => 'mdi mdi-view-agenda'])
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
                      <form class="forms-sample" action="{{route('banner.update',['id' => $id])}}" enctype="multipart/form-data" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="title">Judul <sup style="color:red">*</sup></label>
                            <input type="text" class="form-control" id="title" placeholder="Masukkan judul banner..." name="title" value="{{ $banner->title }}" required>
                        </div>
                        <div class="form-group">
                            <label for="link">Link <sup style="color:red">(optional)</sup></label>
                            <input type="text" class="form-control" id="link" placeholder="Masukkan link banner..." name="link" value="{{ $banner->link }}">
                        </div>
                        <div class="form-group">
                            <label for="sort">Prioritas <sup style="color:red">*</sup></label>
                            <select class="form-control" id="sort" name="sort" required>
                                @forelse ($data_sort as $key => $item_sort)
                                    <option value="{{$data_sort[$key]}}" {{ $banner->sort == $data_sort[$key] ? 'selected' : ''}}>{{$data_sort[$key]}}</option>
                                @empty
                                    <option value="">Data Not Found</option> 
                                @endforelse
                            
                           </select>
                          </div>
                        <div class="form-group">
                            <div class="dropzone"></div>
                        </div>
                        <div class="mb-3">
                          <span class="text-muted" style="font-size:11px">Keterangan: <sup style="color:red">*</sup> (wajib diisi)</span>
                        </div>
                        <a href="{{route('banner.index')}}" class="btn bg-blue text-white">Kembali</a>
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
    <script>
        Dropzone.autoDiscover = false;
        $(document).ready(function() {
            let uploadedDocumentMap = {}
            let fileName = "{{$fileName}}"
            let fileSize = "{{$fileSize}}"
            let filePath = "{{$filePath}}"
            let request = 'fetch';
            
            $(".dropzone").dropzone({
                maxFiles: 1,
                maxFilesize: 2, // MB
                addRemoveLinks: true,
                acceptedFiles: ".jpg,.jpeg,.png,.gif,.png",
                url: "{{ route('banner.store-image') }}",
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
                            url: "{{ route('banner.delete-image') }}",
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
        });
    </script>
@endsection


