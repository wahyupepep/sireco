<?php

namespace App\Http\Controllers;

use App\Models\Discount;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DiscountController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Discount::all();

            return DataTables::of($data)

                ->addIndexColumn()
                ->addColumn('name', function ($row) {
                    return $row->name;
                })
                ->addColumn('discount', function ($row) {
                    return $row->discount == 0 ? '-' : $row->discount . '%';
                })
                ->addColumn('start_date', function ($row) {
                    return $row->start_date;
                })
                ->addColumn('valid_date', function ($row) {
                    return $row->valid_date;
                })
                ->addColumn('action', function ($row) {
                    $btn = '<a href="#" class="edit-brand btn btn-gradient-warning btn-sm"><i class="mdi mdi-lead-pencil"></i></a>';
                    $btn .= '<button class="delete-room btn btn-gradient-danger btn-sm ml-1" data-id=' .  Crypt::encryptString($row->id) . ' data-name="' . $row->name . '"><i class="mdi mdi-delete"></i></button>';
                    return $btn;
                })

                ->rawColumns(['name', 'discount', 'start_date', 'valid_date','action'])

                ->make(true);
        }
        return view('content-dashboard.masters.discounts.index');
    }

    public function add()
    {
        return view('content-dashboard.masters.discounts.add');
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'discount' => 'required|integer',
                'start_date' => 'required|date',
                'valid_date' => 'required|date'
            ]);

            if ($validator->fails()) {
                return redirect()->route('master.discount.add')
                    ->withErrors($validator)
                    ->withInput();
            }
            $data_discount = Discount::where('name', 'like', "%{$request->name}%")->exists();


            if ($data_discount) {
                return redirect()->route('master.discount.add')->withErrors('discount already exists')->withInput();
            }

            $store_discount = Discount::create([
                'name' => $request->name,
                'discount' => $request->discount,
                'start_date' => $request->start_date,
                'valid_date' => $request->valid_date,
            ]);

            if ($store_discount) {
                DB::commit();
                return redirect()->route('master.discount.index')->with('status', 'Successfully add discount');
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route('master.discount.add')->withErrors($th->getMessage() . ' on the line ' . $th->getLine())->withInput();
        }
    }
}