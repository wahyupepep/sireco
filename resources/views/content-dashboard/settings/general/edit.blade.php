@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{asset('assets/css/dropzone.min.css')}}">
    <style>
        .img-setting {
            width: 150px;
            height: 150px;
            border-radius: 10px;
            object-fit: cover;
            margin: 1rem;
        }

        .cursor-pointer {
            cursor: pointer;
        }
    </style>
@endsection

@section('content')
    @include('layouts.overview',['text' => 'Pengaturan Umum', 'icon' => 'mdi mdi-settings'])
    <div class="container">
        <form class="forms-sample" action="{{route('setting.general.update')}}" enctype="multipart/form-data" method="POST">
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
                            <input type="hidden" name="about_id" id="about_id" value="{{$enc_id}}">
                            <div class="form-group">
                                <label for="title">Judul Website <sup style="color:red">*</sup></label>
                                <input type="text" class="form-control" id="title" placeholder="Masukkan judul untuk nama website..." name="title" value="{{ $about->title }}" required>
                            </div>
                            <div class="form-group">
                                <label for="meta_title">Meta Title <sup style="color:red">*</sup></label>
                                <input type="text" class="form-control" id="meta_title" placeholder="Masukkan meta title untuk website..." name="meta_title" value="{{ $about->meta_title }}" required>
                            </div>
                            <div class="form-group">
                                <label for="meta_description">Meta Description <sup style="color:red">*</sup></label>
                                <textarea class="form-control"name="meta_description" id="meta_description" cols="30" rows="10" placeholder="Masukkan meta description untuk website...">{{ $about->meta_description}}</textarea>
                            </div>
                            <div class="form-group">
                                <label for="copyright">Copyright <sup style="color:red">*</sup></label>
                                <input type="text" class="form-control" id="copyright" placeholder="Masukkan copyright untuk website..." name="copyright" value="{{ $about->copyright }}" required>
                            </div>
                        
                            <div class="form-group">
                                <label for="image">Gambar Website <sup style="color:red">*</sup></label>
                               
                                <div class="image-preview d-flex">

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
                                <input type="text" class="form-control" id="owner_name" placeholder="Masukkan nama owner..." name="owner_name" value="{{ $about->owner }}" required>
                            </div>
                            <div class="form-group">
                                <label for="owner_statement">Prakata Owner <sup style="color:red">*</sup></label>
                                <textarea class="form-control"name="owner_statement" id="owner_statement" cols="30" rows="10" placeholder="Masukkan prakata owner...">{{ $about->owner_statement}}</textarea>
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
                                <input type="email" class="form-control" id="email" placeholder="Masukkan email perusahaan..." name="email" value="{{ $about->email }}" required>
                            </div>
                            <div class="form-group">
                                <label for="phone">No.telp <sup style="color:red">*</sup></label>
                                <input type="text" class="form-control" id="phone" placeholder="Masukkan telepon perusahaan..." name="phone" value="{{ $about->phone_company }}" required>
                            </div>                      
                            <div class="form-group">
                                <label for="address">Alamat <sup style="color:red">*</sup></label>
                                <textarea class="form-control" name="address" id="address" cols="30" rows="10" placeholder="Masukkan alamat perusahaan...">{{ $about->address }}</textarea>
                            </div>  
                            <div class="form-group">
                                <label for="phone_order">No.telp Order <sup style="color:red">*</sup></label>
                                <input type="text" class="form-control" id="phone_order" placeholder="Masukkan telepon whatsapp untuk order..." name="phone_order" value="{{ $about->phone_order }}" required>
                            </div> 
                            <div class="form-group">
                                <label for="txt_order">Teks untuk order <sup style="color:red">*</sup></label>
                                <textarea class="form-control" name="txt_order" id="txt_order" cols="30" rows="10" placeholder="Masukkan kata-kata untuk customer order di whatsapp...">{{ $about->txt_phone_order}}</textarea>
                            </div>                     
                            <div class="form-group">
                                <label for="phone_kemitraan">No.telp Kemitraan <sup style="color:red">*</sup></label>
                                <input type="text" class="form-control" id="phone_kemitraan" placeholder="Masukkan telepon whatsapp untuk kemitraan..." name="phone_kemitraan" value="{{ $about->phone_mitra }}" required>
                            </div>  
                            <div class="form-group">
                                <label for="txt_kemitraan">Teks untuk kemitraan <sup style="color:red">*</sup></label>
                                <textarea class="form-control" name="txt_kemitraan" id="txt_kemitraan" cols="30" rows="10" placeholder="Masukkan kata-kata untuk kemitraan di whatsapp...">{{ $about->txt_phone_mitra}}</textarea>
                            </div> 
                            <div class="form-group">
                                <label for="maps">Link Google Maps <sup style="color:red">*</sup></label>
                                <input type="text" class="form-control" id="maps" placeholder="Masukkan link google maps perusahaan..." name="maps" value="{{ $about->url_maps }}" required>
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
                                <input type="text" class="form-control" id="facebook" placeholder="Masukkan link facebook perusahaan..." name="facebook" value="{{ $about->url_facebook }}" required>
                            </div>
                            <div class="form-group">
                                <label for="instagram">Link Instagram <sup style="color:red">*</sup></label>
                                <input type="text" class="form-control" id="instagram" placeholder="Masukkan link instagram perusahaan..." name="instagram" value="{{ $about->url_instagram }}" required>
                            </div>                      
                            <div class="form-group">
                                <label for="whatsapp">Link Whatsapp <sup style="color:red">*</sup></label>
                                <input type="text" class="form-control" id="whatsapp" placeholder="Masukkan link whatsapp perusahaan..." name="whatsapp" value="{{ $about->url_whatsapp }}" required>
                            </div>                      
                            <div class="mb-3">
                            <span class="text-muted" style="font-size:11px">Keterangan: <sup style="color:red">*</sup> (wajib diisi)</span>
                            </div>
                        </div>
                    </div>   
                </div>  
            </div>
            @forelse ($collection_data_images as $data_image )
                <input type="hidden" name="category_file_seo[]" class="category_file_seo" value="{{$data_image['fileName']}}" data-type="{{$data_image['type']}}">
            @empty
               <input type="hidden" name="category_file_seo[]" class="category_file_seo" value="0">
            @endforelse
            <input type="file" id="fileInput" class="d-none" />
            <div class="text-right button-submit">
                <button type="button" class="btn mr-2 bg-green text-white mt-4 btn-edit">Edit</button>
            </div>
           
        </form>
    </div>
    <br>
@endsection

@section('script')
    <script src="{{asset('assets/js/dropzone.min.js')}}"></script>
    <script>
        let row = '';
       
        $(document).ready(function() {
            $('input').prop('readonly', true)
            $('textarea').prop('readonly', true)
            
            createElement()

            $(document).on('click', '.dz-image-preview', function() {
                const filename = $(this).data('path')
                const typeFile = $(this).data('type')
                console.log(typeFile);
                const getType = $(`input[value='${filename}']`).data('type')
               
                $('#fileInput').click()
                $("#fileInput").on("change", function(){
                    let data = new FormData();
                
                    data.append('_token',"{{csrf_token()}}")
                    data.append('file_new', this.files[0])   
                    data.append('file_old', filename)
                    data.append('type', getType)
                    data.append('id', $('#about_id').val())

                    $.ajax({
                        type: 'POST',
                        url: "{{ route('setting.general.new-upload') }}",
                        data: data,
                        contentType: false,
                        cache: false,
                        processData: false,
                        success: function(res) {
                            if(res.code == 200) {
                                $(`input[data-type='${getType}']`).val(res.data.filePath);
                                $(`.dz-image-preview[data-type='${typeFile}'] img`).attr('src', res.data.assetFilePath)
                                $(`.dz-image-preview[data-type='${typeFile}']`).attr('data-path', res.data.filePath)
                            }else {
                                Swal.fire(
                                    'Oopss...',
                                    `${res.message}`,
                                    'info'
                                )
                            }
                        },
                        error: function(err) {
                            Swal.fire(
                                'Oopss...',
                                `${err.responseJSON.message}`,
                                'error'
                            )
                        }
                    })
                });
                // consosle.log(data);  
            })

            $(document).on('click','.btn-edit', function() {
                $('input').prop('readonly', false)
                $('textarea').prop('readonly', false)
                $('.preview-image-element').addClass(['dz-image-preview', 'cursor-pointer'])
                $('.change-photo').removeClass('d-none')
                $('.button-submit').html(`
                    <button type="button" class="btn mr-2 btn-danger text-white mt-4 btn-cancel">Batal</button>
                    <button type="submit" class="btn mr-2 bg-green text-white mt-4 btn-submit">Submit</button>
                `)
            })

            $(document).on('click','.btn-cancel', function() {
                $('input').prop('readonly', true)
                $('textarea').prop('readonly', true)
                $('.preview-image-element').removeClass(['dz-image-preview','cursor-pointer'])
                $('.change-photo').addClass('d-none')
                $('.button-submit').html(`
                    <button type="button" class="btn mr-2 bg-green text-white mt-4 btn-edit">Edit</button>
                `)
            })
        })

        function createElement() {
            let row = ''
            $.ajax({
                type: 'POST',
                url: "{{ route('setting.general.preview-image') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                    id: "{{$enc_id}}"
                },
                success: function(res) {
                    res.data.src.map((el,i) => {
                        row += `<div data-path="${el.file}" data-type="${el.type}" class="preview-image-element">
                    
                        <img src="${el.filePath}" class="img-responsive img-fluid img-setting">
                        <p class="text-center change-photo d-none">Ganti Foto</p>
                        </div>
                        `
                    })
                    $('.image-preview').html(row)
                },
                error: function(err) {
                    alert(
                        `Terjadi kesalahan sistem dengan pesan kesalahan  ${err.responseJSON.message}`
                        );
                }
            })
        }
    </script>
@endsection


