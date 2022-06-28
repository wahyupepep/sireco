<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */
    public function index()
    {
        if (empty(Auth::user())) {
            return view('auth.login');
        } else {
            return redirect()->route('home');
        }
    }

    public function checkLogin(Request $request)
    {
        $akun    = $request->input("email");
        $password = $request->input("password");

        $validator = Validator::make($request->all(), [
            'email' => 'required|string',
            'password' => 'required|string',
        ], [
            'email.required' => 'Email is required',
            'email.string' => 'Email must be word',
            'password.required' => 'Password is required',
            'password.string' => 'Password must be word',

        ]);

        if ($validator->fails()) {
            return redirect()->route('manage.login')->with('message', ['status' => 'danger', 'desc' => $validator->errors()->first()]);
        }

        $user = User::where('name', $akun)->orWhere('email', $akun)->first();
        if ($user) {
            if (Hash::check($password, $user->password)) {
                Auth::login($user);
                return redirect()->route('home');
            } else {
                $desc = 'Login gagal. Cek kembali username/email dan password Anda.';
                return redirect()->route('manage.login')->with('message', ['status' => 'danger', 'desc' => $desc]);
            }
        } else {
            $desc = 'Akun yang anda inputkan tidak tersedia.';
            return redirect()->route('manage.login')->with('message', ['status' => 'danger', 'desc' => $desc]);
        }
    }
    // logout::fungsi logout
    public function logout(Request $request)
    {
        Auth::logout();
        return redirect()->route('manage.login');
    }
}
