<?php

namespace App\Http\Controllers;

use App\Models\CategoryMember;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\Crypt;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $members = User::select('id', 'fullname', 'email', 'industry_name', 'package_id')->with('package:id,name')
                ->where(['role' => 4, 'status' => '0'])
                ->get();

            return DataTables::of($members)
                ->addIndexColumn()

                ->addColumn('package', function ($row) {
                    return !is_null($row->package) ? $row->package->name : '-';
                })

                ->addColumn('email_member', function ($row) {
                    return $row->email ?? '-';
                })
                ->addColumn('industry_name', function ($row) {
                    return $row->industry_name ?? '-';
                })

                ->addColumn('action', function ($row) {
                    $btn = '<a href="' . route('member.detail', Crypt::encryptString($row->id)) . '" class="btn btn-info btn-sm"><i class="mdi mdi-eye"></i></a>';
                    return $btn;
                })

                ->rawColumns(['package', 'action', 'email_member', 'industry_name'])

                ->make(true);
        }



        return view('content-dashboard.members.index');
    }

    public function memberData(Request $request)
    {
        $member = User::where('fullname', 'LIKE', "%{$request->search}%")
            ->where([
                'role' => 4, // MEMBER
                'status' => '0' // ACTIVE
            ])->limit(10)->get();

        return json_encode($member);
    }

    public function detail($id)
    {
        $dec_id = Crypt::decryptString($id);
        $member = User::with('package:id,name')->where([
            'role' => 4,
            'status' => '0',
            'id' => $dec_id
        ])->first();

        // return response()->json($member);
        if (empty($member)) {
            abort(404);
        }

        return view('content-dashboard.members.detail', compact('member'));
    }

    public function memberCheckData(Request $request)
    {
        try {
            $member = User::find($request->id, ['id', 'fullname', 'valid_date_member', 'package_id']);

            if (empty($member)) {
                return response()->json([
                    'code' => 404,
                    'status' => false,
                    'message' => 'Data member not found'
                ], 200);
            }

            $count_own_reserved = Reservation::with('member:id,valid_date_member')
                ->where('member_id', $request->id)
                ->whereHas('member', function ($query) {
                    $query->whereDate('valid_date_member', '>=', date('Y-m-d'));
                })
                ->count();

            $package_range = CategoryMember::where('id', $member->package_id)->first(['day']);

            if ($count_own_reserved > 0 && !empty($package_range)) {
                if (($count_own_reserved + 1) <= $package_range->day) {
                    $status = false;
                } else {
                    $status = true;
                }
                $select_package = $status;
            } else {
                $select_package = true;
            }
            return response()->json([
                'code' => 200,
                'status' => false,
                'data' => [
                    'member' => $member,
                    'package_range_day' =>  empty($package_range) ? 0 : $package_range->day,
                    'select_package' => $select_package
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
