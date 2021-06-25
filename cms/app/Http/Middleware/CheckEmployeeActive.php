<?php

namespace App\Http\Middleware;

use Closure;
use App\Employee;

class CheckEmployeeActive
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
            $employee = Employee::where('user_id', auth()->user()->id)->first();

            if($employee->status=="Inactive"){
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
