@extends('layouts.app')

@section('content')
    <style>
        .table td img {
            border-radius: 0;
        }
    </style>
    @include('layouts.overview',['text' => 'Master Discount', 'icon' => 'mdi mdi-account-group-outline'])
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <a href="{{route('master.discount.add')}}" class="btn btn-sm add mb-4 text-white bg-green"><i class="mdi mdi-plus"></i>Add Discount</a>
                @include('layouts.message')
                <table class="table table-bordered data-table">
                    <thead>
                        <tr>
                            <th>No</th> 
                            <th>Name</th>
                            <th>Discount</th>
                            <th>Start Date</th>
                            <th>Valid Date</th>
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

                ajax: "{{ route('master.discount.index') }}",

                columns: [

                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'name', name: 'name'},
                    {data: 'discount', name: 'discount'},
                    {data: 'start_date', name: 'start_date'},
                    {data: 'valid_date', name: 'valid_date'},
                    {data: 'action', name: 'action', orderable: false, searchable: false},

                ]

            });

            $(document).on('click','.delete-discount', function() {
                const id = $(this).data('id')
                const name = $(this).data('name')
                Swal.fire({
                    title: 'Are you sure?',
                    text: `Remove discount ${name}`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Remove!',
                    cancelButtonText: 'Cancel'
                    }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: 'DELETE',
                            url: "{{ route('master.discount.delete') }}",
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