<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\Reservation;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class VerificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    function __construct()
    {
        $this->middleware('permission:verification-list|verification-detail-order|verification-order');
    }

    public function index(Request $request)
    {

        if ($request->ajax()) {
            $verifications = Reservation::select('id', 'number_invoice', 'order_date', 'seat_code')->where('status', '!=', 2)
                ->orderBy('id', 'desc')
                ->get();
            return DataTables::of($verifications)

                ->addIndexColumn()

                ->addColumn('date_order', function ($row) {
                    return date('d M y', strtotime($row->order_date));
                })
                ->addColumn('chair_code', function ($row) {
                    return strtoupper($row->seat_code);
                })
                ->addColumn('action', function ($row) {

                    $btn = '<button class="btn btn-gradient-info btn-icon btn-detail-order" data-id="' . Crypt::encryptString($row->id) . '">
                            <i class="mdi mdi-eye"></i>
                    </button>';
                    return $btn;
                })

                ->rawColumns(['date_order', 'action', 'chair_code'])

                ->make(true);
        }

        return view('content-dashboard.verifications.index');
    }

    public function complete(Request $request)
    {

        if ($request->ajax()) {
            $verifications = Reservation::select('id', 'number_invoice', 'order_date', 'seat_code')->where('status', 2)
                ->orderBy('id', 'desc')
                ->get();
            return DataTables::of($verifications)

                ->addIndexColumn()

                ->addColumn('date_order', function ($row) {
                    return date('d M y', strtotime($row->order_date));
                })
                ->addColumn('chair_code', function ($row) {
                    return strtoupper($row->seat_code);
                })
                ->addColumn('action', function ($row) {

                    $btn = '<button class="btn btn-gradient-info btn-icon btn-verify" data-id="' . Crypt::encryptString($row->id) . '">
                            <i class="mdi mdi-eye"></i>
                    </button>';
                    return $btn;
                })

                ->rawColumns(['date_order', 'action', 'chair_code'])

                ->make(true);
        }
    }

    public function detailOrder($id)
    {
        $dec_id = Crypt::decryptString($id);

        $reservation = Reservation::find($dec_id);
        // return response()->json($reservation->notification_admin);
        if (empty($reservation)) {
            abort(404);
        }

        if ($reservation->notification_admin->read == 0) {
            $reservation->notification_admin->update(['read' => 1]);
        }

        if ($reservation->status == 2) {
            return view('content-dashboard.verifications.verification_detail_order', compact('reservation'));
        }



        return view('content-dashboard.verifications.detail_order', compact('reservation', 'id'));
    }

    public function verifiedDetailOrder($id)
    {
        $dec_id = Crypt::decryptString($id);

        $reservation = Reservation::find($dec_id);

        if (empty($reservation)) {
            abort(404);
        }

        if ($reservation->status != 2) {
            // UPDATE STATUS TO DONE
            $reservation->update(['status' => 2, 'user_id' => Auth::user()->id]);

            // CREATE NOTIFICATION TO USER, FOR INFORMATION HIS ORDER VERIFIED

            Notification::create([
                'text' => 'Your Order ' . $reservation->number_invoice . ' is verified',
                'date' => date('Y-m-d'),
                'user_id' => $reservation->member_id,
                'reservation_id' => $reservation->id,
                'read' => 0
            ]);
        }

        return view('content-dashboard.verifications.verification_detail_order', compact('reservation'));
    }
}
