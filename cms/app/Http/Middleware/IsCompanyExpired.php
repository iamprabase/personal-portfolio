<?php

namespace App\Http\Middleware;

use Closure;
use App\Company;
use Illuminate\Support\Facades\Auth;

class IsCompanyExpired
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    { 
        $company_id = Auth::user()->company_id;
        $status = Company::find($company_id)->is_active;

        if($status==0) return redirect()->intended(route('company.admin.home', ['domain' => domain()])); 

        return $next($request);
    }
}
