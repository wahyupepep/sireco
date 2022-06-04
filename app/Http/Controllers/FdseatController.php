<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FdseatController extends Controller
{
    public function index()
    {
        return view('content-dashboard.fdseats.index');
        
    }
}
