@extends('layouts.app')

@section('content')
    <style>
        .table td img {
            border-radius: 0;
        }
    </style>
    @include('layouts.overview',['text' => 'Sales', 'icon' => 'mdi mdi-view-agenda'])
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                @include('layouts.message')
                <table class="table table-bordered data-table">
                    <thead>
                        <tr>
                            <th>No</th> 
                            <th>Invoice Number</th>
                            <th>Date Order</th>
                            <th>Member Name</th>
                            <th>Seat Code</th>
                            <th>Package</th>
                            <th>Price</th>
                            <th>Discount</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="8" class="text-right">
                                <span class="font-weight-bold text-right">Total : <span id="sum_price"></span></span>
                            </td>
                        </tr>
                    </tfoot>
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

            ajax: "{{ route('sale.index') }}",

            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                {data: 'number_invoice', name: 'number_invoice'},
                {data: 'order_date', name: 'order_date'},
                {data: 'member_name', name: 'member_name'},
                {data: 'seat_code', name: 'seat_code'},
                {data: 'package', name: 'package'},
                {data: 'price', name: 'price'},
                {data: 'discount', name: 'discount'},
            ]

        });
        $('#sum_price').html(totalIncome())
    });

    function totalIncome() {
        let total = 0;
        $.ajax({
            url: "{{route('sale.total-income')}}",
            type: "POST",
            async: false,
            data: {
                _token: "{{csrf_token()}}",
            },
            success: function(res) {
                if(res.code == 200) {
                    total = res.data.total_income
                }
            },
            error: function(err) {
                Swal.fire('Oops',err.responseJSON.message,'info');
            }
        });
        return total;
    }
</script>
@endsection