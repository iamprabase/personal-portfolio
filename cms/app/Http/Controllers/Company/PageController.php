<?php

namespace App\Http\Controllers\Company;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PageController extends Controller
{
    public function home()
    {
        return view('company.admin.dashboard', ['user' => auth()->user()]);
    }
}
