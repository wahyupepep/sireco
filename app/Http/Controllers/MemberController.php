<?php

namespace App\Http\Controllers;

use App\Models\CategoryMember;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('content-dashboard.members.index');
    }

    public function memberData(Request $request)
    {
        $member = User::where('fullname', 'LIKE', "%{$request->search}%")
            ->where([
                'role' => 3, // MEMBER
                'status' => '1' // ACTIVE
            ])->limit(10)->get();

        return json_encode($member);
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

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
