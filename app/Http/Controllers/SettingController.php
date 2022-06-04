<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function password()
    {
        return view('content-dashboard.settings.password');
    }

    public function profile()
    {
        return view('content-dashboard.settings.profile');
    }
}
