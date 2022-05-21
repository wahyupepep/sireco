@extends('layouts.app')

@section('content')
    @include('layouts.overview',['text' => 'Kategori', 'icon' => 'mdi-bookmark'])
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <a href="{{route('category.add')}}" class="btn btn-sm add mb-4 text-white bg-green"><i class="mdi mdi-plus"></i> Tambah Kategori</a>
                @include('layouts.message')
                <table class="table table-bordered data-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
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

                ajax: "{{ route('category.index') }}",

                columns: [

                    // {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data:'id', name: 'id'},
                    {data: 'name', name: 'name'},

                    {data: 'action', name: 'action', orderable: false, searchable: false},

                ]

            });

            // edit function
            $(document).on('click', '.edit', function() {
                const id = $(this).data('id')
                console.log(id)
            })

            // delete function
            $(document).on('click','.delete', function() {
                const id = $(this).data('id')
                const name = $(this).data('name')
                Swal.fire({
                    title: 'Apakah anda yakin?',
                    text: `Menghapus data kategori ${name}`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Hapus!',
                    cancelButtonText: 'Batal'
                    }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: 'POST',
                            url: "{{ route('category.delete') }}",
                            data: {
                                _token: "{{ csrf_token() }}",
                                id:id
                            },
                            success: function(res) {
                                console.log(res);
                            if(res.code == 200) {
                                    Swal.fire(
                                        'Sukses',
                                        `${res.message}`,
                                        'success'
                                    )
                                    table.ajax.reload()
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
                    }else {
                        return false
                    }
                })
                
            })
        });
</script>
@endsection

