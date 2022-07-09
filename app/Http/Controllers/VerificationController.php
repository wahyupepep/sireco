<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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

    public function index()
    {
        return view('content-dashboard.verifications.index');
    }

    public function detailOrder($id)
    {
        return view('content-dashboard.verifications.detail_order');
    }

    public function verifiedDetailOrder($id)
    {
        return view('content-dashboard.verifications.verification_detail_order');
    }
}
