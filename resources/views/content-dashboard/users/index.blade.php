@extends('layouts.app')

@section('content')
    @include('layouts.overview',['text' => 'Pengguna', 'icon' => 'mdi-account'])
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <a href="{{route('user.add')}}" class="btn btn-sm bg-green text-white add mb-4"><i class="mdi mdi-plus"></i> Tambah User</a>
                @include('layouts.message')
                <table class="table table-bordered data-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Hak Akses</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
@section('script')
<script>
      $(function () {
        let table = $('.data-table').DataTable({

            processing: true,

            serverSide: true,

            ajax: "{{ route('user.index') }}",

            columns: [

                {data: 'DT_RowIndex', name: 'DT_RowIndex'},

                {data: 'name', name: 'name'},

                {data: 'email', name: 'email'},
                {data: 'role', name: 'role'},

                {data: 'action', name: 'action', orderable: false, searchable: false},

            ]

        });

        $(document).on('click', '.reset-user', function() {
            let id = $(this).data('id');
            let name = $(this).data('name');
            Swal.fire({
                title: 'Apakah anda yakin',
                text: `Akan reset password dari user ${name}`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#ff0000',
                confirmButtonText: 'Iya, lanjutkan!', 
                cancelButtonColor: 'Batal'
             }).then((result) => {
                if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('user.reset') }}",
                    type: "put",
                    data: {id:id,_token: $('meta[name="csrf-token"]').attr('content')},
                    success: function(data) {
                    if(data.code == 200) {
                        Swal.fire(
                            'Berhasil!',
                            'berhasil mereset password ' + name,
                            'success'
                        )
                       
                    }else {
                        Swal.fire(
                            'Terjadi Kesalahan!',
                            `dengan pesan kesalahan ${e.message}`,
                            'error'
                        )
                    }
                    },
                    error: function(e) {
                        Swal.fire(
                            'Terjadi Kesalahan',
                            `${e.responseText}`,
                            'error'
                        )
                    }
                })
                }
            })
        })
        
        $(document).on('click','.delete-user', function() {
            let id = $(this).data('id');
            let name = $(this).data('name');
            Swal.fire({
                title: 'Apakah anda yakin',
                text: `Akan Menghapus data dari ${name}`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#ff0000',
                confirmButtonText: 'Iya, lanjutkan!', 
                cancelButtonColor: 'Batal'
             }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('user.delete') }}",
                        type: "delete",
                        data: {id:id,_token: $('meta[name="csrf-token"]').attr('content')},
                        success: function(data) {
                        if(data.code == 200) {
                            Swal.fire(
                                'Berhasil Terhapus!',
                                'data berhasil terhapus.',
                                'success'
                            )
                            table.ajax.reload()
                        }else {
                            Swal.fire(
                                'Terjadi Kesalahan!',
                                `dengan pesan kesalahan ${e.message}`,
                                'error'
                            )
                        }
                        },
                        error: function(e) {
                            Swal.fire(
                                'Terjadi Kesalahan',
                                `${e.responseText}`,
                                'error'
                            )
                        }
                    })
                }
            })
        })
    });
</script>
@endsection

