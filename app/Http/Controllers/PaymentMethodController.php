<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PaymentMethodController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = PaymentMethod::all();

            return DataTables::of($data)

                ->addIndexColumn()
                ->addColumn('name', function ($row) {
                    return $row->name;
                })
                ->addColumn('action', function ($row) {
                    // $user = User::find(Auth::user()->id);
                    // if ($user->hasRole('super admin')) {
                    //     $btn = '<a href="' . route('merk.edit', Crypt::encryptString($row->id)) . '" class="edit-brand btn btn-gradient-warning btn-sm"><i class="mdi mdi-lead-pencil"></i></a>';
                    //     $btn .= '<button class="delete-brand btn btn-gradient-danger btn-sm ml-1" data-id=' .  Crypt::encryptString($row->id) . ' data-name="' . $row->name . '"><i class="mdi mdi-delete"></i></button>';
                    //     return $btn;
                    // } else {
                    //     return '<button class="btn btn-gradient-info btn-sm ml-1" title="Silahkan hubungi super admin untuk edit dan delete"><i class="mdi mdi-lock"></i></button>';
                    // }
                    $btn = '<a href="' . route('master.payment_method.edit', Crypt::encryptString($row->id)) . '" class="edit-brand btn btn-gradient-warning btn-sm"><i class="mdi mdi-lead-pencil"></i></a>';
                    $btn .= '<button class="delete-payment-method btn btn-gradient-danger btn-sm ml-1" data-id=' .  Crypt::encryptString($row->id) . ' data-name="' . $row->name . '"><i class="mdi mdi-delete"></i></button>';
                    return $btn;
                })

                ->rawColumns(['name', 'action'])

                ->make(true);
        }
        return view('content-dashboard.masters.payment_methods.index');
    }

    public function add()
    {
        return view('content-dashboard.masters.payment_methods.add');
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'account_number' => 'required|numeric',
                'account_name' => 'required|string'
            ]);

            if ($validator->fails()) {
                return redirect()->route('master.payment_method.add')
                    ->withErrors($validator)
                    ->withInput();
            }

            $data_payment_method = PaymentMethod::where('name', 'like', "%{$request->name}%")->exists();

            if ($data_payment_method) {
                return redirect()->route('master.payment_method.index')->withErrors('Payment method already exists')->withInput();
            }

            $store_payment_method = PaymentMethod::create([
                'name' => $request->name,
                'account_number' => $request->account_number,
                'account_name' => $request->account_name
            ]);

            if ($store_payment_method) {
                DB::commit();
                return redirect()->route('master.payment_method.index')->with('status', 'Successfully add payment methods');
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route('master.payment_method.index')->withErrors($th->getMessage() . ' on the line ' . $th->getLine())->withInput();
        }
    }

    public function edit($id)
    {
        try {
            $payment_method = PaymentMethod::find(Crypt::decryptString($id), ['id', 'name', 'account_name', 'account_number']);

            if (empty($payment_method)) {
                return redirect()->route('master.payment_method.index')->withErrors('Data Payment Method Not Found')->withInput();
            }

            return view('content-dashboard.masters.payment_methods.edit', compact('payment_method', 'id'));
        } catch (\Throwable $th) {
            return redirect()->route('master.payment_method.index')->withErrors($th->getMessage() . ' on the line ' . $th->getLine())->withInput();
        }
    }

    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'account_number' => 'required|numeric',
                'account_name' => 'required|string'
            ]);

            if ($validator->fails()) {
                return redirect()->route('master.payment_method.edit', ['id' => $id])
                    ->withErrors($validator)
                    ->withInput();
            }

            $payment_method = PaymentMethod::find(Crypt::decryptString($id));

            if (empty($payment_method)) {
                return redirect()->route('master.payment_method.index')->withErrors('Data Payment Method Not Found')->withInput();
            }

            $payment_method->update([
                'name' => $request->name,
                'account_number' => $request->account_number,
                'account_name' => $request->account_name
            ]);

            DB::commit();

            return redirect()->route('master.payment_method.index')->with('status', 'Successfully update payment methods');
        } catch (\Throwable $th) {
            return redirect()->route('master.payment_method.index')->withErrors($th->getMessage() . ' on the line ' . $th->getLine())->withInput();
        }
    }

    public function delete(Request $request)
    {
        try {
            DB::beginTransaction();

            $payment_method = PaymentMethod::find(Crypt::decryptString($request->id));

            if (empty($payment_method)) {
                return response()->json([
                    'code' => 404,
                    'success' => false,
                    'message' => 'Data not found'
                ]);
            }

            $payment_method->delete();

            DB::commit();

            return response()->json([
                'code' => 200,
                'success' => true,
                'message' => 'Successfully delete data payment method'
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'code' => 500,
                'success' => false,
                'message' => $th->getMessage() . ' on the line ' . $th->getLine()
            ], 500);
        }
    }
}
