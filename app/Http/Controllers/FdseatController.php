<?php

namespace App\Http\Controllers;

use App\Models\CategoryMember;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
}
