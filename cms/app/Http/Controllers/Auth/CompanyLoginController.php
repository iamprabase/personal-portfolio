<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;

class CompanyLoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest:company', ['except' => ['logout']]);
    }


    public function showLoginForm()
    {

        return view('auth.company-login');
    }

    public function login(Request $request)
    {

        $this->validate($request, [
            'email' => 'required|email|exists:users,email,is_active,2',
            'password' => 'required|min:6'

        ]);
        $crediantials = array(
            'email' => $request->email,
            'password' => $request->password,
            'is_active' => 2,
        );

        if (Auth::guard('company')->attempt($crediantials, $request->remember)) {

            return redirect()->intended(route('company.dashboard'));
        }


        return redirect()->back()->withInput($request->only('email', 'remember'));
    }

    public function logout()
    {
        Auth::guard('company')->logout();
        return redirect('/company');
    }

}
