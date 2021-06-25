<?php

namespace App\Providers;

use App\Company;
use App\ClientSetting;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\OutletResource;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
      if(config('app.env') == "local"){
        \Debugbar::enable();
      }else{
        //production
        \Debugbar::disable();
      }

      Validator::extend('max_uploaded_file_size', function ($attribute, $value, $parameters, $validator) {      
        if(is_array($value)){
          $total_size = array_reduce($value, function ( $sum, $item ) { 
            // each item is UploadedFile Object
            $sum += ($item->getClientSize()); 
            return $sum;
          });
        }else{
          $total_size = $value->getClientSize(); 
          return $total_size;
        }
        
        return $total_size / (1024*1024*1024) < $parameters[0]; 

      });

        Schema::defaultStringLength(191);
        OutletResource::withoutWrapping();

        $fullDomain = $this->app->request->server->all()["HTTP_HOST"];
        $subdomain = explode('.', $fullDomain)[0];
        if($subdomain=='app'){
            date_default_timezone_set('Asia/Kathmandu');
        }else{
           //dd($subdomain);
            $company = Company::where('domain','LIKE', $subdomain)->value('id');
          // dd($company);
            $tz=ClientSetting::where('company_id', $company)->first();
            try{
              if($tz)
                  if($tz->time_zone) date_default_timezone_set($tz->time_zone);
                  else date_default_timezone_set('Asia/Kathmandu');
              else
                  date_default_timezone_set('Asia/Kathmandu');
            }catch(\Exception $e){
              dump($tz);
              die($e->getMessage());
            }
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
