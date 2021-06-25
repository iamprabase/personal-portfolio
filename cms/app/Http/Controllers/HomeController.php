<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');

    }

    public function home(Request $request)
    {
        $subdomain = $request->route('subdomain');
        if ($subdomain) {
            if (auth()->check() && auth()->user()->isCompanyManager()) {
                return redirect()->intended(route('company.admin.home', ['domain' => $subdomain]));
            } else {

                auth()->logout();
                return redirect()->route('company.login', ['domain' => $subdomain]);

            }
        } else {
            if (auth()->check() && auth()->user()->isCompanyManager()) {
                return redirect()->intended(route('company.admin.home', ['domain' => domain()]));
            } else {

                auth()->logout();
                return redirect()->route('company.login', ['domain' => domain()]);

            }
        }
    }
}
