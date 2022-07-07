<?php

namespace App\Http\Controllers;

use App\Models\CategoryMember;
use App\Models\HistoryTransaction;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class FdseatController extends Controller
{
    public function index()
    {
        $category_members = CategoryMember::select('id', 'name')->get();

        $seats = Reservation::select('id', 'seat_code')
            ->whereDate('order_date', date('Y-m-d'))
            ->get();

        $seat_codes = $seats->map(function ($value) {
            return $value->seat_code;
        })->toArray();



        return view('content-dashboard.fdseats.index', compact('category_members', 'seat_codes'));
    }

    public function saveBooking(Request $request)
    {
        // return response()->json($request->all());
        try {
            DB::beginTransaction();
            if ($request->member) {
                $validator = Validator::make($request->all(), [
                    'date_reservation_start' => 'required',
                    'member_name' => 'required',
                    'arrChair' => 'required',
                ], [
                    'date_reservation_start.required' => 'Date booking cant empty',
                    'member_name.required' => 'Member must be selected',
                    'chair.required' => 'Chair must be selected',
                ]);
            } else {
                $validator = Validator::make($request->all(), [
                    'date_reservation_start' => 'required',
                    'fullname' => 'required',
                    'username' => 'required',
                    'email' => 'required',
                    'arrChair' => 'required',
                ], [
                    'date_reservation_start.required' => 'Date booking cant empty',
                    'fullname.required' => 'fullname cant empty',
                    'username.required' => 'username cant empty',
                    'email.required' => 'email cant empty',
                    'package_member.required' => 'package must be selected',
                    'arrChair.required' => 'chair must be selected',
                ]);
            }

            if ($validator->fails()) {
                return response()->json([
                    'code'    => 500,
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], 500);
            }

            // CHECK USER EMAIL ALREADY EXIST OR NOT
            $check_user = User::where('email', $request->email)->exists();

            if ($check_user) {
                return response()->json([
                    'code'    => 404,
                    'success' => false,
                    'message' => 'Email user already exist'
                ], 500);
            }
            // CHECK CHAIR ALREADY USE FOR THE DATE REQUEST OR NOT?
            $arr_seats = explode(',', $request->arrChair);
            $arr_checking_seat = collect([]);

            foreach ($arr_seats as $seat) {
                $reservation = Reservation::whereDate('order_date', $request->date_reservation_start)->where('seat_code', $seat)->exists();
                $arr_checking_seat->push($reservation);
            }

            // CHECKING DATA ARR CHECKING SEAT HAVE TRUE?
            $filter_checking_seat_true = $arr_checking_seat->filter(function ($value) {
                return $value;
            })->count();

            if ($filter_checking_seat_true > 0) {
                return response()->json([
                    'code'    => 302,
                    'success' => false,
                    'message' => 'Chair already booking'
                ]);
            }


            $invoice = $this->invoice();
            // INSERT TO TABLE USER
            if ($request->member == 0) { // CONDITION NOT MEMBER

                $package = CategoryMember::find($request->package_not_member);
                $user = User::create([
                    'fullname' => $request->fullname,
                    'name' => $request->username,
                    'email' => $request->email,
                    'password' => bcrypt($request->email),
                    'role' => 3, // MEMBER
                    'status' => '1', // ACTIVE,
                    'valid_date_member' => date('Y-m-d', strtotime(date('Y-m-d')) + $package->convert_time),
                    'package_id' => $package->id
                ]);

                // INSERT TO TABLE RESERVATIONS AND HISTORY RESERVATION
                foreach ($arr_seats as $seat) {

                    $reservation = Reservation::create([
                        'member_id' => $request->member_name == '' ? $user->id : $user,
                        'number_invoice' => $invoice,
                        'order_date' => date('Y-m-d H:i:s', strtotime($request->date_reservation_start)),
                        'seat_code' => $seat,
                        'room_id' => 1,
                    ]);

                    HistoryTransaction::create([
                        'reservation_id' => $reservation->id,
                        'date_transaction' => date('Y-m-d'),
                        'price' => $package->price,
                        'discount' => $package->discount ?? 0
                    ]);
                }
            } else {

                // GET DATA USER
                $user = User::find($request->member_name);

                $package_member = $request->package_member == '' ? $user->package_id : $request->package_member;


                $package = CategoryMember::find($package_member);

                // CHECKING USER HAVE A VALID DATE MEMBER AND PACKAGE OR NOT OR IF VALID DATE MEMBER EXPIRED
                if ((is_null($user->valid_date_member) && is_null($user->package_id)) || (strtotime($user->valid_date_member) < strtotime(date('Y-m-d')))) {

                    $valid_date_member = date('Y-m-d', strtotime(date('Y-m-d')) + $package->convert_time);

                    User::where('id', $user->id)->update([
                        'valid_date_member' => $valid_date_member,
                        'package_id' => $package->id
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

                foreach ($arr_seats as $seat) {

                    $reservation = Reservation::create([
                        'member_id' => $user->id,
                        'number_invoice' => $invoice,
                        'order_date' => date('Y-m-d H:i:s', strtotime($request->date_reservation_start)),
                        'seat_code' => $seat,
                        'room_id' => 1,
                    ]);

                    // CREATE HISTORY TRANSACTION
                    HistoryTransaction::create([
                        'reservation_id'   => $reservation->id,
                        'date_transaction' => date('Y-m-d'),
                        'price' => $price,
                        'discount' => $discount,
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'code' => 200,
                'status' => true,
                'message' => 'Successfully create booking',
                'data' => [
                    'id' => Crypt::encryptString($reservation->id)
                ]
            ], 200);
        } catch (\Throwable $th) {
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
