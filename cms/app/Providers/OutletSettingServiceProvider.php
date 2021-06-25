<?php

namespace App\Providers;

use Config;
use App\ClientOutletSetting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class OutletSettingServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
      // only use the Settings package if the Settings table is present in the database
      if (!\App::runningInConsole() && count(Schema::getColumnListing('client_outlet_settings'))) {
        $company_id = config('settings.company_id');
        
        $settings = ClientOutletSetting::whereCompanyId($company_id)->first();

        if(!empty($settings)){
          foreach ($settings->toArray() as $key=>$setting)
          {
            Config::set('client_outlet_settings.'.$key, $setting);
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
      $this->app->bind('client_outlet_settings', function ($app) {
        return new ClientOutletSetting();
      });
      $loader = \Illuminate\Foundation\AliasLoader::getInstance();
      $loader->alias('ClientOutletSetting', ClientOutletSetting::class);
    }
}
