<?php

namespace App\Http\Middleware;

use Closure;
use App\Company;

class CheckIsActive
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
        if (auth()->check())
        {
           if(auth()->User()->isCompanyMember()){
            if (auth()->User()->is_active != '2')
            {
                auth()->logout();
                return redirect()->to('/')->with('warning', 'Your session has expired because your account is deactivated.');
            }

            $company_id = auth()->User()->company_id;
            $status = Company::find($company_id)->is_active;

            if($status==1){
              auth()->logout();
              return redirect()->to('/')->with('warning', 'Your session has expired.');
            };
        }else{
            auth()->logout();
            return redirect('/admin');
        }

        }
        return $next($request);
    }
}
