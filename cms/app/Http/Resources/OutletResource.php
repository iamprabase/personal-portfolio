<?php

namespace App\Http\Resources;

use App\Outlet;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Resources\Json\JsonResource;

class OutletResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        if(isset($this->country)){
          $country_name = DB::table('countries')->whereId($this->country)->first()->name;
        }else{
          $country_name = NULL;
        }

        if(isset($this->city)){
          $city_name = DB::table('cities')->whereId($this->city)->first()->name;
        }else{
          $city_name = NULL;
        }
        if(isset($this->image)){
          $tempImageDir = $this->email;
          $tempImagePath = "/storage/app/public/outlets/" . $tempImageDir . "/" . $this->image;
        }
        return [
          'id' => $this->id,
          'outlet_name' => $this->outlet_name,
          'contact_person' => $this->contact_person,
          'email' => $this->email,
          'country' => $this->country,
          'country_name' => $country_name,
          'city' => $this->city,
          'city_name' => $city_name,
          'address' => $this->address,
          'phone_ext' => $this->phone_ext,
          'phone' => $this->phone,
          'gps_location' => ($this->latitude && $this->longitude)?$this->latitude.','.$this->longitude:NULL,
          'gps_address' => $this->gps_location, 
          'secret_code' => $this->unique_code,
          'image' => isset($this->image)?'https://'.$_SERVER['HTTP_HOST'].'/cms'.$tempImagePath:NULL,
          'image_name' => $this->image,
          'registered_date' => $this->registered_date
        ];
    }
}
