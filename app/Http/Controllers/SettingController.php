<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

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

    public function changePassword(Request $request)
    {
        try {
            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                'password_old' => 'required|string',
                'password_new' => 'required|string|min:8',
                'confirm_password_new' => 'required|string',
            ], [
                'password_old.required' => 'Old password is required',
                'password_old.string' => 'Old password must be word',
                'password_new.required' => 'New password is required',
                'password_new.string' => 'New password must be word',
                'password_new.min' => 'New password recommendation more than 8 character',
                'confirm_password_new.required' => 'Confirm password new is required',
                'confirm_password_new.string' => 'Confirm password new must be word',
            ]);

            if ($validator->fails()) {
                return redirect()->route('setting.password')->with('message', ['status' => 'danger', 'desc' => $validator->errors()->first()]);
            }

            $member = User::find(Auth::user()->id);

            if (!Hash::check($request->password_old, $member->password)) {
                return redirect()->route('setting.password')->with('message', ['status' => 'danger', 'desc' => 'Old password not match']);
            }

            if ($request->password_new != $request->confirm_password_new) {
                return redirect()->route('setting.password')->with('message', ['status' => 'danger', 'desc' => 'Confirm new password not match with new password']);
            }

            // UPDATE PASSWORD BY MEMBER
            $member->update(['password' => bcrypt($request->password_new)]);
            DB::commit();
            return redirect()->route('setting.password')->with('message',  ['status' => 'success', 'desc' => 'Successfully update your password']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route('setting.password')->with('message', ['status' => 'danger', 'desc' => $th->getMessage() . ' on the line ' . $th->getLine()]);
        }
    }
}
