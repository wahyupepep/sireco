@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{asset('assets/css/dropzone.min.css')}}">
    <link href="https://cdn.jsdelivr.net/npm/froala-editor@latest/css/froala_editor.pkgd.min.css" rel="stylesheet" type="text/css" />
@endsection

@section('content')
    @include('layouts.overview',['text' => 'Pengaturan Umum', 'icon' => 'mdi mdi-settings'])
    <div class="container">
        <form class="forms-sample" action="{{route('setting.general.store')}}" enctype="multipart/form-data" method="POST">
            <div class="row">
                <div class="col-md-12">
                    <h5>SEO</h5>
                    <div class="card">
                        <div class="card-body">
                            @include('layouts.message')
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
                        
                            @csrf
                            <div class="form-group">
                                <label for="title">Judul Website <sup style="color:red">*</sup></label>
                                <input type="text" class="form-control" id="title" placeholder="Masukkan judul untuk nama website..." name="title" value="{{ old('title') }}" required>
                            </div>
                            <div class="form-group">
                                <label for="meta_title">Meta Title <sup style="color:red">*</sup></label>
                                <input type="text" class="form-control" id="meta_title" placeholder="Masukkan meta title untuk website..." name="meta_title" value="{{ old('meta_title') }}" required>
                            </div>
                            <div class="form-group">
                                <label for="meta_description">Meta Description <sup style="color:red">*</sup></label>
                                <textarea class="form-control"name="meta_description" id="meta_description" cols="30" rows="10" placeholder="Masukkan meta description untuk website...">{{old('meta_description')}}</textarea>
                            </div>
                            <div class="form-group">
                                <label for="copyright">Copyright <sup style="color:red">*</sup></label>
                                <input type="text" class="form-control" id="copyright" placeholder="Masukkan copyright untuk website..." name="copyright" value="{{ old('copyright') }}" required>
                            </div>
                        
                            <div class="form-group">
                                <label for="image">Gambar Website <sup style="color:red">*</sup></label>
                                <div class="dropzone">
                                    <div class="dz-message" data-dz-message>
                                        <span>Drop your file here</span>
                                        <br>
                                        <br>
                                        <span>1. Favicon 32x32 px</span>
                                        <br>
                                        <br>
                                        <span>2. Logo</span>
                                        <br>
                                        <br>
                                        <span>3. Foto Owner</span>
                                        <br>
                                        <br>
                                        <span>4. Preview Gambar Website</span>
                                        
                                    </div>
                                </div>
                            </div>                       
                            <div class="mb-3">
                                <h6 class="text-muted" style="font-size:11px">
                                    Drop your file here
                                   
                                </h6>
                                <ol class="text-muted">
                                    <li>Favicon 32x32 px</li>
                                    <li>Logo</li>
                                    <li>Foto Owner</li>
                                    <li>Preview Gambar Website untuk share ke sosial media</li>
                                </ol>
                            </div>
                            <div class="mb-3">
                                <span class="text-muted" style="font-size:11px">Keterangan: <sup style="color:red">*</sup> (wajib diisi)</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-md-12">
                    <h5>Owner</h5>
                    <div class="card">
                        <div class="card-body">
                            <div class="form-group">
                                <label for="owner_name">Nama Owner <sup style="color:red">*</sup></label>
                                <input type="text" class="form-control" id="owner_name" placeholder="Masukkan nama owner..." name="owner_name" value="{{ old('owner_name') }}" required>
                            </div>
                            <div class="form-group">
                                <label for="owner_statement">Prakata Owner <sup style="color:red">*</sup></label>
                                <textarea class="form-control"name="owner_statement" id="owner_statement" cols="30" rows="10" placeholder="Masukkan prakata owner...">{{old('owner_statement')}}</textarea>
                            </div>                      
                            <div class="mb-3">
                            <span class="text-muted" style="font-size:11px">Keterangan: <sup style="color:red">*</sup> (wajib diisi)</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-md-12">
                    <h5>Detail</h5>
                    <div class="card">
                        <div class="card-body">
                            <div class="form-group">
                                <label for="email">Email <sup style="color:red">*</sup></label>
                                <input type="email" class="form-control" id="email" placeholder="Masukkan email perusahaan..." name="email" value="{{ old('email') }}" required>
                            </div>
                            <div class="form-group">
                                <label for="phone">No.telp <sup style="color:red">*</sup></label>
                                <input type="text" class="form-control" id="phone" placeholder="Masukkan telepon perusahaan..." name="phone" value="{{ old('phone') }}" required>
                            </div>                      
                            <div class="form-group">
                                <label for="address">Alamat <sup style="color:red">*</sup></label>
                                <textarea class="form-control" name="address" id="address" cols="30" rows="10" placeholder="Masukkan alamat perusahaan...">{{old('address')}}</textarea>
                            </div>  
                            <div class="form-group">
                                <label for="phone_order">No.telp Order <sup style="color:red">*</sup></label>
                                <input type="text" class="form-control" id="phone_order" placeholder="Masukkan telepon whatsapp untuk order..." name="phone_order" value="{{ old('phone_order') }}" required>
                            </div> 
                            <div class="form-group">
                                <label for="txt_order">Teks untuk order <sup style="color:red">*</sup></label>
                                <textarea class="form-control" name="txt_order" id="txt_order" cols="30" rows="10" placeholder="Masukkan kata-kata untuk customer order di whatsapp...">{{old('address')}}</textarea>
                            </div>                     
                            <div class="form-group">
                                <label for="phone_kemitraan">No.telp Kemitraan <sup style="color:red">*</sup></label>
                                <input type="text" class="form-control" id="phone_kemitraan" placeholder="Masukkan telepon whatsapp untuk kemitraan..." name="phone_kemitraan" value="{{ old('txt_order') }}" required>
                            </div>  
                            <div class="form-group">
                                <label for="txt_kemitraan">Teks untuk kemitraan <sup style="color:red">*</sup></label>
                                <textarea class="form-control" name="txt_kemitraan" id="txt_kemitraan" cols="30" rows="10" placeholder="Masukkan kata-kata untuk kemitraan di whatsapp...">{{old('txt_kemitraan')}}</textarea>
                            </div> 
                            <div class="form-group">
                                <label for="maps">Link Google Maps <sup style="color:red">*</sup></label>
                                <input type="text" class="form-control" id="maps" placeholder="Masukkan link google maps perusahaan..." name="maps" value="{{ old('maps') }}" required>
                            </div>                      
                            <div class="mb-3">
                                <span class="text-muted" style="font-size:11px">Keterangan: <sup style="color:red">*</sup> (wajib diisi)</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-md-12">
                    <h5>Sosial Media</h5>
                    <div class="card">
                        <div class="card-body">
                            <div class="form-group">
                                <label for="facebook">Link Facebook <sup style="color:red">*</sup></label>
                                <input type="text" class="form-control" id="facebook" placeholder="Masukkan link facebook perusahaan..." name="facebook" value="{{ old('facebook') }}" required>
                            </div>
                            <div class="form-group">
                                <label for="instagram">Link Instagram <sup style="color:red">*</sup></label>
                                <input type="text" class="form-control" id="instagram" placeholder="Masukkan link instagram perusahaan..." name="instagram" value="{{ old('instagram') }}" required>
                            </div>                      
                            <div class="form-group">
                                <label for="whatsapp">Link Instagram <sup style="color:red">*</sup></label>
                                <input type="text" class="form-control" id="whatsapp" placeholder="Masukkan link whatsapp perusahaan..." name="whatsapp" value="{{ old('whatsapp') }}" required>
                            </div>                      
                            <div class="mb-3">
                            <span class="text-muted" style="font-size:11px">Keterangan: <sup style="color:red">*</sup> (wajib diisi)</span>
                            </div>
                        </div>
                    </div>   
                </div>  
            </div>
            <div class="text-right">
                <button type="submit" class="btn mr-2 bg-green text-white mt-4">Simpan</button>
            </div>
           
        </form>
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
            new FroalaEditor('textarea#description');
            $(".dropzone").dropzone({
                maxFilesize: 2, // MB
                addRemoveLinks: true,
                acceptedFiles: ".jpg,.jpeg,.png,.gif,.png",
                url: "{{ route('setting.general.store-image') }}",
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                success: function(file, response) {
                    $('form').append('<input type="hidden" name="category_file_seo[]" value="' + response.name + '">')
                    uploadedDocumentMap[file.name] = response.name
                },
                removedfile: function(file) {
                    file.previewElement.remove()

                    let name = ''
                    if (typeof file.file_name !== 'undefined') {
                        name = file.file_name
                    } else {
                        name = uploadedDocumentMap[file.name]
                    }

                    if (name != '') {
                        $.ajax({
                            type: 'POST',
                            url: "{{ route('setting.general.delete-image') }}",
                            data: {
                                _token: "{{ csrf_token() }}",
                                name: name
                            },
                            success: function(data) {
                                $('form').find('input[name="category_file_seo[]"][value="' +
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
               
            });

            // $(".dropzone-logo").dropzone({
            //     maxFilesize: 2, // MB
            //     addRemoveLinks: true,
            //     acceptedFiles: ".jpg,.jpeg,.png,.gif,.png",
            //     url: "{{ route('menu.store-image') }}",
            //     headers: {
            //         'X-CSRF-TOKEN': "{{ csrf_token() }}"
            //     },
            //     success: function(file, response) {
            //         $('form').append('<input type="hidden" name="category_file" value="' + response.name + '">')
            //         uploadedDocumentMap[file.name] = response.name
            //     },
            //     removedfile: function(file) {
            //         file.previewElement.remove()

            //         let name = ''
            //         if (typeof file.file_name !== 'undefined') {
            //             name = file.file_name
            //         } else {
            //             name = uploadedDocumentMap[file.name]
            //         }

            //         if (name != '') {
            //             $.ajax({
            //                 type: 'POST',
            //                 url: "{{ route('menu.delete-image') }}",
            //                 data: {
            //                     _token: "{{ csrf_token() }}",
            //                     name: name
            //                 },
            //                 success: function(data) {
            //                     $('form').find('input[name="category_file"][value="' +
            //                         name + '"]').remove()
            //                 },
            //                 error: function(err) {
            //                     console.log(
            //                         `Terjadi kesalahan sistem dengan pesan kesalahan  ${err.responseJSON.message}`
            //                         );
            //                 }
            //             })
            //         }
                    

            //     },
               
            // });
        })
    </script>
@endsection


