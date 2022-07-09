<?php

namespace App\Http\Controllers;

use App\Models\CategoryMember;
use App\Models\HistoryTransaction;
use App\Models\Notification;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Seat;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class SeatController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $category_members = CategoryMember::select('id', 'name')->get();

        $seats = Reservation::select('id', 'seat_code')
            ->whereDate('order_date', date('Y-m-d'))
            ->get();

        $seat_codes = $seats->map(function ($value) {
            return $value->seat_code;
        })->toArray();

        $count_own_reserved = Reservation::with('member:id,valid_date_member')
            ->where('member_id', Auth::user()->id)
            ->whereHas('member', function ($query) {
                $query->whereDate('valid_date_member', '>=', date('Y-m-d'));
            })
            ->count();

        $package_range = CategoryMember::where('id', Auth::user()->package_id)->first(['day']);

        if ($count_own_reserved > 0 && !empty($package_range)) {
            $select_package = ($count_own_reserved + 1) <= $package_range->day;
        } else {
            $select_package = false;
        }


        return view('content-dashboard.seats.index', compact('category_members', 'seat_codes', 'select_package'));
    }

    public function listSeat(Request $request)
    {
        try {
            $seats = Reservation::select('id', 'seat_code')
                ->whereDate('order_date', date('Y-m-d', strtotime($request->valueDate)))
                ->get();

            $seat_codes = $seats->map(function ($value) {
                return $value->seat_code;
            });

            return response()->json([
                'code' => 200,
                'status' => true,
                'message' => 'Successfully get data',
                'data' => $seat_codes
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'code' => 500,
                'status' => false,
                'message' => $th->getMessage() . " on the line " . $th->getLine()
            ], 500);
        }
    }

    public function order(Request $request)
    {
        try {
            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                'valueDate' => 'required',
                'chair' => 'required',
            ], [
                'valueDate.required' => 'Date booking cant empty',
                'chair.required' => 'Chair must be selected',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'code'    => 500,
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], 500);
            }

            // CHECK CHAIR FOR ORDER DATE IS EXIST OR NOT
            $chair_exist = Reservation::where('seat_code', $request->chair)
                ->whereDate('order_date', date('Y-m-d', strtotime($request->valueDate)))
                ->exists();

            if ($chair_exist) {
                return response()->json([
                    'code' => 302,
                    'status' => false,
                    'message' => 'Chair code already exists for date ' . date('Y-m-d', strtotime($request->valueDate))
                ], 200);
            }

            // INSERT TO TABLE RESERVATION
            $reservation = Reservation::create([
                'member_id' => Auth::user()->id,
                'number_invoice' => $this->invoice(),
                'order_date' => date('Y-m-d', strtotime($request->valueDate)),
                'seat_code' => $request->chair,
                'status' => 0, // WAITING
                'room_id' => 1, // ONLY MANEKA NOW
            ]);

            if (!is_null(Auth::user()->package_id)) {
                $package_id = Auth::user()->package_id;
            } else {
                $package_id = $request->packageId;
            }

            $package = CategoryMember::where('id', $package_id)->first();

            if (empty($package)) {
                return response()->json([
                    'code' => 404,
                    'status' => false,
                    'message' => 'Package not found'
                ], 200);
            }

            // CHECKING USER HAVE A VALID DATE MEMBER AND PACKAGE OR NOT OR IF VALID DATE MEMBER EXPIRED
            if ((is_null(Auth::user()->valid_date_member) && is_null(Auth::user()->package_id)) || (strtotime(Auth::user()->valid_date_member) < strtotime(date('Y-m-d')))) {

                $valid_date_member = date('Y-m-d', strtotime(date('Y-m-d')) + $package->convert_time);

                User::where('id', Auth::user()->id)->update([
                    'valid_date_member' => $valid_date_member,
                    'package_id' => $request->packageId
                ]);
            }

            $count_own_reserved = Reservation::with('member:id,valid_date_member')
                ->where('member_id', Auth::user()->id)
                ->whereHas('member', function ($query) {
                    $query->whereDate('valid_date_member', '>=', date('Y-m-d'));
                })
                ->count();

            if ((($count_own_reserved + 1) <= $package->day) && (strtotime(Auth::user()->valid_date_member) >= strtotime(date('Y-m-d')))) {
                $price = 0;
                $discount = 0;
            } else {
                $price = $package->price;
                $discount = $package->discount ?? 0;
            }

            // CREATE HISTORY TRANSACTION
            HistoryTransaction::create([
                'reservation_id'   => $reservation->id,
                'date_transaction' => date('Y-m-d'),
                'price' => $price,
                'discount' => $discount,
            ]);

            // GET DATA USER WITH ROLE SUPER ADMIN AND SPACE MANAGER
            $users = User::select('id')->whereIn('role', [1, 2])->get();

            foreach ($users as $user) {
                Notification::create([
                    'text' => 'Order in with invoice ' .  $reservation->number_invoice,
                    'user_id' => $user->id,
                    'date' => date('Y-m-d'),
                    'reservation_id' => $reservation->id,
                    'read' => 0
                ]);
            }


            if ($reservation) {
                $id = Crypt::encryptString($reservation->id);
                DB::commit();
            }

            return response()->json([
                'code' => 200,
                'status' => true,
                'message' => 'Successfully create booking',
                'data' => [
                    'id' => $id
                ]
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'code' => 500,
                'status' => false,
                'message' => $th->getMessage() . " on the line " . $th->getLine()
            ], 500);
        }
    }

    public function orderSummary($id)
    {
        $dec_id = Crypt::decryptString($id);

        $reservation = Reservation::find($dec_id);

        return view('content-dashboard.seats.order_summary', compact('reservation', 'id'));
    }

    public function confirmOrder(Request $request)
    {
        try {
            $dec_id = $dec_id = Crypt::decryptString($request->id);

            $reservation = Reservation::find($dec_id);

            if (empty($reservation)) {
                return response()->json([
                    'code' => 404,
                    'status' => false,
                    'message' => 'Package not found'
                ], 200);
            }

            $reservation->update(['status' => 1]); // STATUS CONFIRMED

            DB::commit();

            return response()->json([
                'code' => 200,
                'status' => true,
                'message' => 'Successfully confirmed booking',
                'data' => []
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'code' => 500,
                'status' => false,
                'message' => $th->getMessage() . " on the line " . $th->getLine()
            ], 500);
        }
    }

    public function listOrder()
    {
        $active_order = Reservation::select('id', 'number_invoice', 'seat_code', 'order_date', 'payment_file')->where('member_id', Auth::user()->id)
            ->where('status', 2) // STATUS DONE
            ->whereNotNull('payment_file')
            ->whereNotNull('user_id')
            ->whereDate('order_date', date('Y-m-d'))
            ->where('member_id', Auth::user()->id)
            ->orderBy('id', 'desc')
            ->get();

        $progress_order = Reservation::select('id', 'number_invoice', 'seat_code', 'order_date', 'payment_file')->where('member_id', Auth::user()->id)
            ->with('history_transaction')
            ->whereIn('status', [0, 1]) // STATUS DONE
            ->whereNull('user_id')
            ->whereDate('order_date', '>=', date('Y-m-d'))
            ->where('member_id', Auth::user()->id)
            ->orderBy('id', 'desc')
            ->get();

        $complete_order =  Reservation::select('id', 'number_invoice', 'seat_code', 'order_date', 'payment_file')->where('member_id', Auth::user()->id)
            ->where('status', 2) // STATUS DONE
            ->whereNotNull('payment_file')
            ->whereNotNull('user_id')
            ->whereDate('order_date', '<', date('Y-m-d'))
            ->where('member_id', Auth::user()->id)
            ->orderBy('id', 'desc')
            ->get();

        return view('content-dashboard.seats.list_order', compact('active_order', 'progress_order', 'complete_order'));
    }

    public function detailOrder($id)
    {
        $dec_id = Crypt::decryptString($id);
        $reservation = Reservation::findOrFail($dec_id);

        if (!is_null($reservation->notification_admin) || $reservation->notification_admin != '') {
            if ($reservation->notification_admin->read == 0) {
                $reservation->notification_admin->update(['read' => 1]);
            }
        }

        return view('content-dashboard.seats.detail_order', compact('reservation'));
    }

    public function paymentOrder($id)
    {
        $dec_id = Crypt::decryptString($id);
        $reservation = Reservation::findOrFail($dec_id);
        return view('content-dashboard.seats.payment_order', compact('reservation', 'id'));
    }

    public function uploadPayment(Request $request)
    {
        try {
            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                'payment' => 'required|mimes:jpg,bmp,png,jpeg,pdf,svg|file|max:2048',
            ], [
                'payment.required' => 'Upload proof payment cant empty',
                'payment.mimes' => 'Upload proof payment must be jpg/jpeg/png/bmp/svg/pdf',
                'payment.max' => 'Upload proof payment file must be lower than 2048 kb',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'code' => 500,
                    'status' => false,
                    'message' => $validator->errors()->first()
                ], 200);
            }
            $dec_id = Crypt::decryptString($request->order_id);

            $reservation = Reservation::find($dec_id);

            if (empty($reservation)) {
                return response()->json([
                    'code' => 404,
                    'status' => false,
                    'message' => 'Data reservation not found'
                ], 200);
            }

            $destination_path_proof_booking = public_path('/uploads/reservations/');

            if (!file_exists($destination_path_proof_booking)) {
                mkdir($destination_path_proof_booking, 777);
            }

            $file_proof_booking = $request->file('payment');
            $filename_booking = Str::random(34) . '.' .  $file_proof_booking->getClientOriginalExtension();
            $file_proof_booking->move($destination_path_proof_booking, $filename_booking);
            $path_reservation = 'uploads/reservations/';

            $reservation->update(['payment_file' => $path_reservation . $filename_booking]);

            DB::commit();
            return response()->json([
                'code' => 200,
                'status' => true,
                'message' => 'Successfully upload payment'
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'code' => 500,
                'status' => false,
                'message' => $th->getMessage() . " on the line " . $th->getLine()
            ], 500);
        }
    }

    private function invoice()
    {
        $count_reservation = Reservation::whereMonth('order_date', date('m'))->count();

        if ($count_reservation == 0) {
            $invoice = 'PYMT/HS/I/' . date('y') . date('m') . date('d') . '.1';
        } else {
            $count_number = (int)$count_reservation + 1;
            $invoice = 'PYMT/HS/I/' . date('y') . date('m') . date('d') . '.' . $count_number;
        }

        return $invoice;
    }
}
