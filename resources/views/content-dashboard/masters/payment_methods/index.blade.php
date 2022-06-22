@extends('layouts.app')

@section('content')
    <style>
        .table td img {
            border-radius: 0;
        }
    </style>
    @include('layouts.overview',['text' => 'Master Payment Method', 'icon' => 'mdi mdi-credit-card'])
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <a href="{{route('master.payment_method.add')}}" class="btn btn-sm add mb-4 text-white bg-green"><i class="mdi mdi-plus"></i>Add Payment Method</a>
                @include('layouts.message')
                <table class="table table-bordered data-table">
                    <thead>
                        <tr>
                            <th>No</th> 
                            <th>Payment Method</th>
                            <th>Account Name</th>
                            <th>Account Number</th>
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

                ajax: "{{ route('master.payment_method.index') }}",

                columns: [

                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'name', name: 'name'},
                    {data: 'account_name', name: 'account_name'},
                    {data: 'account_number', name: 'account_number'},
                    {data: 'action', name: 'action', orderable: false, searchable: false},

                ]

            });

            $(document).on('click','.delete-payment-method', function() {
                const id = $(this).data('id')
                const name = $(this).data('name')
                Swal.fire({
                    title: 'Are you sure?',
                    text: `Remove payment method ${name}`,
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
                            url: "{{ route('master.payment_method.delete') }}",
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

