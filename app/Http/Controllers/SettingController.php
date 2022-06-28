<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
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

    public function profile($id)
    {
        $dec_id = Crypt::decryptString($id);

        $member = User::find($dec_id);

        $ages = User::AGE;

        $hobbies = User::HOBBY;

        $work_types = User::WORK_TYPE;

        return view('content-dashboard.settings.profile', compact('member', 'id', 'ages', 'hobbies', 'work_types'));
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

    public function updateProfile(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                'fullname' => 'required|string',
                'nik' => 'required|string|max:16',
                'username' => 'required|string',
                'email' => 'required|string',
                'birthdate' => 'required|string',
                'address' => 'required|string',
                'work_type' => 'required|numeric',
                'industry_name' => 'required|string',
                'hobby' => 'required|numeric',
                'phone' => 'required|max:12',
                'age' => 'required|numeric'
            ]);

            if ($validator->fails()) {
                return redirect()->route('setting.profile', ['id' => $id])->with('message', ['status' => 'danger', 'desc' => $validator->errors()->first()]);
            }

            // check phone input is right way or not
            $pattern = "/^[0-9]{12}+$/";

            if (!preg_match($pattern, $request->phone)) {
                return redirect()->route('setting.profile', ['id' => $id])->with('message', ['status' => 'danger', 'desc' => 'Format phone not allowed']);
            }

            $member = User::find(Crypt::decryptString($id));

            if (empty($member)) {
                return redirect()->route('setting.profile', ['id' => $id])->with('message', ['status' => 'danger', 'desc' => 'Member not found']);
            }

            $check_email_exists = User::where('id', '!=', Crypt::decryptString($id))
                ->where('email', $request->email)
                ->exists();

            if ($check_email_exists) {
                return redirect()->route('setting.profile', ['id' => $id])->with('message', ['status' => 'danger', 'desc' => 'Email already exists']);
            }

            $member->update([
                'fullname' => $request->fullname,
                'nik' => $request->nik,
                'name' => $request->username,
                'email' => $request->email,
                'birthdate' => date('Y-m-d', strtotime($request->birthdate)),
                'address' => $request->address,
                'work_type' => $request->work_type,
                'industry_name' => $request->industry_name,
                'hobby' => $request->hobby,
                'phone' => $request->phone,
                'classification_age' => $request->age
            ]);

            DB::commit();

            return redirect()->route('setting.profile', ['id' => $id])->with('message', ['status' => 'success', 'desc' => 'Successfully update profile']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route('setting.profile', ['id' => $id])->with('message', ['status' => 'danger', 'desc' => $th->getMessage() . ' on the line ' . $th->getLine()]);
        }
    }
}
