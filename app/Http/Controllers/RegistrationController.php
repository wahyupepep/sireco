<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RegistrationController extends Controller
{
    public function registration()
    {
        return view('auth.registration');
    }

    public function inputRegistration(Request $request)
    {
        try {

            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                'fullname' => 'required|string',
                'username' => 'required|string',
                'email' => 'required|string',
                'password' => 'required|string',
            ], [
                'fullname.required' => 'Fullname is required',
                'fullname.string' => 'Email must be word',
                'username.required' => 'Username is required',
                'username.string' => 'Username must be word',
                'email.required' => 'Email is required',
                'email.string' => 'Email must be word',
                'password.required' => 'Password is required',
                'password.string' => 'Password must be word',

            ]);

            if ($validator->fails()) {
                return redirect()->route('manage.registration')->with('message', ['status' => 'danger', 'desc' => $validator->errors()->first()]);
            }

            $member = User::where('email', $request->email)->exists();

            if ($member) {
                return redirect()->route('manage.registration')->with('message', ['status' => 'danger', 'desc' => 'Email already exist']);
            }

            $insert_member = User::create([
                'fullname' => $request->fullname,
                'name' => $request->username,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'role' => 3 // Member role
            ]);

            if ($insert_member) {
                $details = [

                    'link' => route('email.verified', ['id' => Crypt::encryptString($insert_member->id)]),
                ];

                \Mail::to($request->email)->send(new \App\Mail\RegistrationMail($details));

                DB::commit();

                return redirect()->route('manage.login')->with('status', 'Successfully create your account');
            } else {
                return redirect()->route('manage.registration')->with('message', ['status' => 'danger', 'desc' => 'Failed to create your account']);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route('manage.registration')->with('message', ['status' => 'danger', 'desc' => $th->getMessage() . ' on the line ' . $th->getLine()]);
        }
    }

    public function emailVerified($id)
    {
        $dec_id = Crypt::decryptString($id);

        $member = User::find($dec_id);

        if (empty($member)) {
            return view('emails.verified', ['status' => 0]);
        }

        if (!is_null($member->email_verified_at)) {
            return view('emails.verified', ['status' => 2]);
        }

        $member->update(['email_verified_at' => date('Y-m-d H:i:s')]);

        return view('emails.verified', ['status' => 1]);
    }
}
