@extends('layouts.app')

@section('content')
    <style>
        .nav-pills .nav-link.active, .nav-pills .show > .nav-link {
            background-color: #ff395b !important;
            color: white !important;
        }
        .nav-item a{
            color: #ff395b
        }

        .table {
          width: 100% !important;
        }
    </style>
    @include('layouts.overview',['text' => 'Payment Verification', 'icon' => 'mdi mdi-view-agenda'])
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                    <li class="nav-item">
                      <a class="nav-link active" id="pills-home-tab" data-toggle="pill" href="#pills-home" role="tab" aria-controls="pills-home" aria-selected="true">All</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" id="pills-contact-tab" data-toggle="pill" href="#pills-contact" role="tab" aria-controls="pills-contact" aria-selected="false">Completed</a>
                    </li>
                </ul>
            </div>
            <div class="col-md-12 mb-4">
                <div class="tab-content" id="pills-tabContent">
                    <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
                        <table class="table table-striped data-table-all">
                            <thead>
                              <tr>
                                <th>No</th>
                                <th>No. Invoice </th>
                                <th>Valid Date</th>
                                <th>Chair</th>
                                <th>Action</th>
                              </tr>
                            </thead>
                            <tbody>
                              
                              
                            </tbody>
                          </table>
                    </div>
                    <div class="tab-pane fade" id="pills-contact" role="tabpanel" aria-labelledby="pills-contact-tab">
                        <table class="table table-striped data-table-complete">
                            <thead>
                              <tr>
                                <th>No</th>
                                <th>No. Invoice </th>
                                <th>Valid Date</th>
                                <th>Chair</th>
                                <th>Action</th>
                              </tr>
                            </thead>
                            <tbody>
                             
                              
                            </tbody>
                          </table>
                    </div>
                </div>
            </div>
              
             
        </div>
    </div>
@endsection
@section('script')
    <script>
        $(document).ready(function() {
            let table = $('.data-table-all').DataTable({

                processing: true,

                serverSide: true,

                ajax: "{{ route('verification.index') }}",

                columns: [

                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'number_invoice', name: 'number_invoice'},
                    {data: 'date_order', name: 'date_order'},
                    {data: 'chair_code', name: 'chair_code'},
                    {data: 'action', name: 'action', orderable: false, searchable: false},

                ]

            });
            let tableComplete = $('.data-table-complete').DataTable({

                processing: true,

                serverSide: true,

                ajax: "{{ route('verification.complete') }}",

                columns: [

                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'number_invoice', name: 'number_invoice'},
                    {data: 'date_order', name: 'date_order'},
                    {data: 'chair_code', name: 'chair_code'},
                    {data: 'action', name: 'action', orderable: false, searchable: false},

                ]

            });

            $(document).on('click','.btn-detail-order',function() {
                let url = "{{route('verification.detail-order', ':id')}}"
                url = url.replace(':id', $(this).data('id'))
                window.location.href= url
            })

            $(document).on('click','.btn-verify', function(){
                let url = "{{route('verification.verified-order', ':id')}}"
                url = url.replace(':id', $(this).data('id'))
                window.location.href = url
            })

        })
    </script>
@endsection