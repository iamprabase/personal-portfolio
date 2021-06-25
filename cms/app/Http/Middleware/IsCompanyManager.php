<?php

namespace App\Http\Middleware;

use Closure;

class IsCompanyManager
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
        if (auth()->check() && (auth()->user()->isCompanyManager())) {
            return $next($request);
        }
        if ($subdomain) {
            return redirect()->route('company.admin.home', ['domain' => $subdomain]);
        }
        return view('company.login');
    }
}
