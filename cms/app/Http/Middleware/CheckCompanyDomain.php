<?php

namespace App\Http\Middleware;

use Closure;

class CheckCompanyDomain
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

        $subdomain = $request->route('subdomain');
        $company = \App\Company::where('domain', $subdomain)->first();
        if (!$company) {
            return abort(404, 'We cannot find the company, you are looking for.');
        }elseif($company->is_verified==0){
            return abort(404,'Your Company is not verified. Please vefify.');
        }
        // elseif($company->is_verified==1 && $company->is_active==1 ){
        //     return abort(404,'Your Company is not Active. Please contact your Administrator.');
        // }
        return $next($request);
    }
}
