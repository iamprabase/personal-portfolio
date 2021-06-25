<?php

namespace App\Http\Middleware;


use Closure;
use Illuminate\Support\Facades\Log;


class IsModuleEnabled
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next,  $module)
    {


        if($module=="beatplans"){
            if (getClientSetting()->beat==1) {
                return $next($request);
            }
        }elseif($module=="tourplans"){
            if (getClientSetting()->tour_plans==1) {
                return $next($request);
            }
        }elseif($module=="stockreport"){
            if (getClientSetting()->stock_report==1) {
                return $next($request);
            }
        }elseif($module=="returns"){
            if (getClientSetting()->returns==1) {
                return $next($request);
            }
        }elseif($module=="employees"){
            if (getClientSetting()->employee==1) {
                return $next($request);
            }
        }elseif($module=="products"){
            if (getClientSetting()->product==1) {
                return $next($request);
            }
        }elseif($module=="clients"){
            if (getClientSetting()->party==1) {
                return $next($request);
            }
        }elseif($module=="orders"){
            if (getClientSetting()->orders==1) {
                return $next($request);
            }
        }elseif($module=="collections"){
            if (getClientSetting()->collections==1) {
                return $next($request);
            }
        }elseif($module=="notes"){
            if (getClientSetting()->notes==1) {
                return $next($request);
            }
        }elseif($module=="activities"){
            if (getClientSetting()->activities==1) {
                return $next($request);
            }
        }elseif($module=="expenses"){
            if (getClientSetting()->expenses==1) {
                return $next($request);
            }
        }elseif($module=="leaves"){
            if (getClientSetting()->leaves==1) {
                return $next($request);
            }
        }elseif($module=="announcement"){
            if (getClientSetting()->announcement==1) {
                return $next($request);
            }
        }elseif($module=="remarks"){
            if (getClientSetting()->remarks==1) {
                return $next($request);
            }
        }elseif($module=="zero_orders"){
            if (getClientSetting()->zero_orders==1) {
                return $next($request);
            }
        }elseif($module=="custom_module"){
            if (getClientSetting()->custom_module==1) {
                return $next($request);
            }
        }elseif($module=="schemes"){
            if (getClientSetting()->schemes==1) {
                return $next($request);
            }
        }elseif($module=="odometer"){
            if (getClientSetting()->odometer_report==1) {
                return $next($request);
            }
        }

        if($module=="party_wise_rate_setup"){
            if (getClientSetting()->party_wise_rate_setup==1) {
                return $next($request);
            }
        }

        if($module=="unit_conversion"){
            if (getClientSetting()->unit_conversion==1) {
                return $next($request);
            }
        }

        if($module=="visit_module"){
            if (getClientSetting()->visit_module==1) {
                return $next($request);
            }
        }

        if($module=="targets"){
            if (getClientSetting()->targets==1) {
                return $next($request);
            }
        }

        if($module=="targets_rep"){
            if (getClientSetting()->targets_rep==1) {
                return $next($request);
            }
        }


        if($module=="category_wise_rate_setup"){
            if (getClientSetting()->category_wise_rate_setup==1) {
                return $next($request);
            }
        }

        if($module=="analytics_new"){
            if (getClientSetting()->targets_rep==1) {
                return $next($request);
            }
        }

        if($module=="import"){
          if (getClientSetting()->import==1) {
              return $next($request);
          }
        }



        return redirect()->back();
    }
}
