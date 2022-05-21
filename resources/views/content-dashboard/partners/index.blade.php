@extends('layouts.app')

@section('content')
    <style>
        .table td img {
            border-radius: 0;
        }
    </style>
    @include('layouts.overview',['text' => 'Mitra', 'icon' => 'mdi mdi-account-multiple'])
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <a href="{{route('partner.add')}}" class="btn btn-sm add mb-4 text-white bg-green"><i class="mdi mdi-plus"></i> Tambah Mitra</a>
                @include('layouts.message')
                <table class="table table-bordered data-table">
                    <thead>
                        <tr>
                            <th>No</th> 
                            <th>Display</th>
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

                ajax: "{{ route('partner.index') }}",

                columns: [

                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data:'image', name: 'image', searchable:false},
                    {data: 'action', name: 'action', orderable: false, searchable: false},

                ]

            });

            // delete function
            $(document).on('click','.delete', function() {
                const id = $(this).data('id')
                Swal.fire({
                    title: 'Apakah anda yakin?',
                    text: `Menghapus data partner ini`,
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
                            url: "{{ route('partner.delete') }}",
                            data: {
                                _token: "{{ csrf_token() }}",
                                id:id
                            },
                            success: function(res) {
                               
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

