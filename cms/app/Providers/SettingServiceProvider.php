<?php

namespace App\Providers;

use Config;
use App\ClientSetting;
use App\Setting;
use App\Company;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class SettingServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
      if (!\App::runningInConsole()) 
      {
        if(isset($_SERVER['HTTP_HOST']) && !empty($_SERVER['HTTP_HOST'])){
        $parsedUrl = $_SERVER['HTTP_HOST'];
        $host = explode('.', $parsedUrl);
        $subdomain = $host[0];
        if($subdomain=='app' || $subdomain=='deltasalesapp')
        {
          $adminsettings = Setting::all();
          foreach ($adminsettings as $setting)
          {
            $object = json_decode(json_encode($setting));              
            foreach ($object as $key => $value)
            { 
              Config::set('settings.'.$key, $value);
            }
          }
        }else
        {
          $company = Company::where('domain', $subdomain)->first();
          if ($company) {        
          $clientsettings = ClientSetting::select('client_settings.*','company_plan.plan_id','plans.name','plans.description','plans.users','plans.duration','plans.duration_in')
          ->join('company_plan','company_plan.company_id','client_settings.company_id')
          ->join('plans','company_plan.plan_id','plans.id')
          ->where('client_settings.company_id', $company->id)->get();

          foreach ($clientsettings as $setting)
          {
            $object = json_decode(json_encode($setting));              
            foreach ($object as $key => $value)
            { 
              Config::set('settings.'.$key, $value);
            }
          }
        }
      }
        }
    }
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
