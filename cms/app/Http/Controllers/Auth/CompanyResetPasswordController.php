<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Password;
use Auth;

class CompanyResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/company';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest:company');
    }

    protected function guard()
    {
        return Auth::guard('company');
    }

    protected function broker()
    {
        return Password::broker('companies');
    }

    public function showResetForm(Request $request, $token = null)
    {
        return view('auth.passwords.reset-company')->with(
            ['token' => $token, 'email' => $request->email]
        );
    }
}
