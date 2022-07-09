<?php

namespace App\Http\Controllers;

use App\Models\HistoryTransaction;
use App\Models\Reservation;
use Illuminate\Http\Request;
use DataTables;

class SaleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
        $this->middleware('permission:sales|sales-income');
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Reservation::with(['history_transaction:id,reservation_id,price,discount', 'member' => function ($query) {
                $query->select('id', 'fullname', 'valid_date_member', 'package_id')->with('package:id,name')->whereHas('package');
            }])
                // ->where('status', 2) // DONE
                ->whereNotNull('payment_file')
                ->whereHas('history_transaction')
                ->whereHas('member')
                ->orderBy('id', 'desc')
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()

                ->addColumn('order_date', function ($row) {
                    return date('d M y', strtotime($row->order_date));
                })

                ->addColumn('member_name', function ($row) {
                    return $row->member->fullname;
                })

                ->addColumn('seat_code', function ($row) {
                    return strtoupper($row->seat_code);
                })

                ->addColumn('package', function ($row) {
                    return $row->member->package->name;
                })
                ->addColumn('price', function ($row) {
                    return "IDR " . number_format($row->history_transaction->price, 0, ',', '.');
                })
                ->addColumn('discount', function ($row) {
                    return $row->history_transaction->discount == 0 ? '-' : "IDR " . number_format($row->history_transaction->discount, 0, ',', '.');
                })

                ->rawColumns(['order_date', 'member_name', 'package', 'price', 'discount'])

                ->make(true);
        }


        return view('content-dashboard.sales.index');
    }

    public function getTotalIncome()
    {
        try {
            $total_income = HistoryTransaction::with('reservation')->whereHas('reservation')->sum('price');
            return response()->json([
                'code' => 200,
                'status' => true,
                'message' => 'OK',
                'data' => [
                    'total_income' => "IDR " . number_format($total_income, 0, ',', '.')
                ]
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'code' => 500,
                'status' => false,
                'message' => $th->getMessage() . " on the line " . $th->getLine()
            ], 500);
        }
    }
}
