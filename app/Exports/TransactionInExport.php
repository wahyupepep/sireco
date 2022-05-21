<?php

namespace App\Exports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class TransactionInExport implements FromView, ShouldAutoSize
{
    /**
     * @return \Illuminate\Support\Collection
     */
    // public function collection()
    // {
    //     return Transaction::all();
    // }
    protected $filter_kategori;
    protected $filter_produk;
    protected $filter_tgl_start;
    protected $filter_tgl_end;
    protected $filter_merk;
    protected $filter_user;


    public function __construct($filter_user, $filter_tgl_start, $filter_tgl_end)
    {
        $this->filter_tgl_start    = $filter_tgl_start;
        $this->filter_tgl_end      = $filter_tgl_end;
        $this->filter_user         = $filter_user;
    }

    public function view(): View
    {
        $consume_data = Transaction::with(['transaction_detail' => function ($query) {
            $query->with(['product' => function ($query) {
                $query->with(['get_brand:id,name', 'get_category:id,name,size']);
            }]);
        }, 'get_user:id,name'])->where('flag', 0);

        if ($this->filter_tgl_start != "") {
            $consume_data->whereDate('date_input', '>=', date('Y-m-d', strtotime($this->filter_tgl_start)));
        }
        if ($this->filter_tgl_end != "") {
            $consume_data->whereDate('date_input', '<=', date('Y-m-d', strtotime($this->filter_tgl_end)));
        }
        if ($this->filter_user != "") {
            $consume_data->where('user_id', $this->filter_user);
        }
        $data = $consume_data->orderBy('id', 'desc')->get();

        return view('content-dashboard.barang_masuk.report.excel', [
            'data' => $data,
            'filter_tgl_start' => $this->filter_tgl_start,
            'filter_tgl_end'  => $this->filter_tgl_end,
        ]);
    }
}
