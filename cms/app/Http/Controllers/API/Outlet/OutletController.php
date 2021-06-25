<?php

namespace App\Http\Controllers\API\Outlet;

use App\Scheme;
use Auth;
use App\User;
use Exception;
use App\Outlet;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Resources\OutletResource;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\API\OutletRequest;
use App\Http\Resources\OutletClientResource;

class OutletController extends Controller
{

    public function __construct()
    {
      $this->middleware('auth:api-outlets')->except(['index', 'fetchCities', 'store','schemesForRetailerApp']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    { 
      Log::info(array(
        "Outlet API Response", 
        $request->toArray()
      ));
      $is_email_signin = $request->has('gmail') ? true : false;
      try{
        if($is_email_signin){
          $message = ['gmail.regex' => 'Gmail sign in is only allowed.'];
          $this->validate($request, [
            "gmail" => "required|email|regex:/gmail.com$/",
          ], $message);
          $gmail_id = $request->gmail;
          $outlet = Outlet::whereEmail($gmail_id)->first();
          $loginCredentials = [
            'phone' => $outlet? $outlet->phone : null,
            'password' => $outlet? $outlet->phone : null,
            'company_id' => NULL,
            'outlet_id' => $outlet? $outlet->id : null,
          ];
          $create = [
            'email' => $gmail_id
          ];
        }else{
          $this->validate($request, [
            "phone" => "required|numeric",
          ]);
          $phone = $request->phone;
          $outlet = Outlet::wherePhone($phone)->first();
          $loginCredentials = [
            'phone' => $phone,
            'password' => $outlet? $outlet->phone : null,
            'company_id' => NULL,
            'outlet_id' => $outlet? $outlet->id : null,
          ];
          $create = [
            'phone' => $phone
          ];
        }
        $fetchCitiesRoute = route('api.outlet.country.fetchCities');
        
        if($outlet){
          $id = $outlet->id; 
          $profile_status = $outlet->status;

          if( $profile_status=='New' || $profile_status=='Active' ){
            /** 
             * New or Active denotes users profile is filled completely
             * Allow login for this status
            **/    
            $credentials = $loginCredentials;
            if(Auth::attempt($credentials)){
              $msg = "Logged In Successfully.";
              $status_code = 200;
              
              $user = Auth::user();
              $token = $user->token();
              if($token){
                $token->revoke();
              } 
              $token = $user->createToken($outlet->phone)->accessToken;
              
              $profile_details = $this->getProfile($outlet);
              $suppliers = $this->suppliers($outlet);
              $supplier_link = route('api.outlet.suppliers', [$id]);
              $order_store = route('api.outlet.order.store', [$id]);
  
              $data = array("status"=> $status_code, "msg"=> $msg, "data" => new OutletResource($outlet), "profile_status" => $profile_status, "profile_detail" => $profile_details, 'supplier_route'=>$supplier_link, 'suppliers_details' => $suppliers, 'order_store'=> $order_store, 'fetch_cities_route'=>$fetchCitiesRoute, 'country_city_list'=>$this->getCountryAndCityList($outlet->country), 'access_token' => $token);
            }else{
              $data = array("status"=> false, "msg"=> "Login Failed");
            }
          }elseif($profile_status=='Incomplete'){ 
            $msg = "Please complete your profile setup first.";
            $status_code = 200;
            $store_link = route('api.outlet.profile.store');
            
            $data = array("status" => $status_code, "msg" => $msg, "data" => new OutletResource($outlet), "profile_status" => $profile_status, 'store_route' => $store_link, 'fetch_cities_route'=>$fetchCitiesRoute, 'country_city_list'=>$this->getCountryAndCityList($outlet->country));
          }elseif($profile_status=='Disabled'){ 
            $msg = "Your account has been deactivated.";
            $status_code = false;
            
            $data = array("status" => $status_code, "msg" => $msg, "profile_status" => $profile_status);
          }
          
          return $data;
        } else{
          /** 
           * store user info on first login 
          **/ 
          $outlet = Outlet::create($create);
          $msg = "Please complete your profile setup first.";
          $status_code = 200;
          $store_link = route('api.outlet.profile.store');
          $profile_status = "Incomplete";

          $data = array("status" => $status_code, "msg" => $msg, "data" => new OutletResource($outlet), "profile_status" => $profile_status, 'store_route' => $store_link, 'fetch_cities_route'=>$fetchCitiesRoute, 'country_city_list'=>$this->getCountryAndCityList());
          
          return $data;
        }
      }catch(Exception $e){
        $data = array("msg" => $e->getMessage());
        return response()->json($data); 
      }
    }

    /**
     * Store Outlets Registration Information.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(OutletRequest $request)
    {
      Log::info(array(
        "Outlet API Store", 
        $request->toArray()
      ));
      try{
        $fields = $request->toArray();

        $latLng = strval($request->gps_location);
        $latitude = !empty($latLng)? explode(',', $latLng)[0]:NULL;
        $longitude = !empty($latLng)? explode(',', $latLng)[1]:NULL;
        $fields['latitude'] = $latitude;
        $fields['longitude'] = $longitude;
        $gps_address = NULL;
        if(isset($latitude) && isset($longitude)){ 
          $gps_address = $this->getGPSAddress($latitude, $longitude);
        }
        $fields['gps_location'] = $gps_address;
        $email = $fields['email'];
        $phone = $fields['phone'];
        unset($fields['email']);

        if (in_array(null, $fields, true) || in_array('', $fields, true)) {
          /** if empty fields set Incomplete Status 
            * to restrict moving to dashboard
            * if all fields filled then set New Status
          **/
          $profile_status = "Incomplete";
        }else{
          $profile_status = "New";
          /** 
           * if all fields filled set Unique Code 
          **/
          $unique_code =  time().substr(uniqid(),3,6);
          $fields['unique_code'] = $unique_code;
          $fields['registered_date'] = date('Y-m-d');
        }
        $fields['status'] = $profile_status;
        
        $fields['email'] = $email;
        if(!empty($email)){
          $instance = Outlet::whereEmail($email)->first();
          if(!empty($phone)){
            $instance_phone = Outlet::wherePhone($phone)->first();
            if($instance_phone){
              $data = array("status" => 400, "msg" => "Phone number already in use.");
              return response()->json($data); 
            }
          }
        }elseif(!empty($phone) && empty($email)){
          $instance = Outlet::wherePhone($phone)->first();
        }else{
          $instance = null;
        }

        DB::beginTransaction();
        /** 
         * Outlet Instance already created on first login
         *  update existing instance
        **/
        if($instance) {
          unset($fields['email']);
          if(!empty($instance->unique_code)){
            unset($fields['unique_code']);
            unset($fields['registered_date']);
          }
          $outlet = tap($instance)->update($fields);
          $profile_status = $outlet->status;
        }else{
          $data = array("status" => 400, "msg" => "Invalid Request.");
          return response()->json($data);  
        }

        $id = $outlet->id;
        /** 
         * create user instance for outlets with this status
        **/ 
        if(!$instance->users && ($profile_status=='New' || $profile_status=='Active')){
          User::create([
            'name'=> $outlet->outlet_name,
            'phone'=> $outlet->phone,
            'email'=> $outlet->email,
            'password'=> bcrypt($outlet->phone),
            'outlet_id'=> $id
          ]);
        }
        DB::commit();
        
        $fetchCitiesRoute = route('api.outlet.country.fetchCities');
        if( $profile_status=='New' || $profile_status=='Active' ){
          $msg = "Logged In Successfully.";
          $status_code = 200;
          $credentials = [
            'phone' => $outlet->phone,
            'password' => $outlet->phone,
            'company_id' => NULL,
            'outlet_id'=> $outlet->id
          ];
          if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken($outlet->phone)->accessToken;
            $profile_details = $this->getProfile($outlet);
            $suppliers = $this->suppliers($outlet);
            $supplier_link = route('api.outlet.suppliers', [$id]);
            $order_store = route('api.outlet.order.store', [$id]);

            $data = array("status"=> $status_code, "msg"=> $msg, "data" => new OutletResource($outlet), "profile_status" => $profile_status, "profile_detail" => $profile_details, 'supplier_route'=>$supplier_link, 'suppliers_details' => $suppliers, 'order_store' => $order_store, 'fetch_cities_route'=>$fetchCitiesRoute, 'country_city_list'=>$this->getCountryAndCityList($outlet->country), 'access_token'=>$token);

            return $data;
          }
        }elseif( $profile_status=='Incomplete' ){
          $msg = "Please complete your profile setup first.";
          $status_code = 200;
          $store_link = route('api.outlet.profile.store');
          
          $data = array("status" => $status_code, "msg" => $msg, "data" => new OutletResource($outlet), "profile_status" => $profile_status, 'store_route' => $store_link, 'fetch_cities_route'=>$fetchCitiesRoute, 'country_city_list'=>$this->getCountryAndCityList($outlet->country));

          return $data;
        }
      }catch(Exception $e){
        DB::rollback();
        $data = array("msg" => $e->getMessage());
        return response()->json($data); 
      }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Outlet $outlet)
    {
      try{
        $get_profile_details = $this->getProfile($outlet);
        $data = array("data"=>$get_profile_details['profile'], "update_route"=>$get_profile_details['profile_update_route']);

        return $data;
      }catch(Exception $e){
        $data = array("msg" => $e->getMessage());
        return response()->json($data);  
      }
    }

    private function getProfile($outlet_instance){
      $profile_details = array("profile" => null, "profile_update_route" => null);
      try{
        $profile_details['profile'] = new OutletResource($outlet_instance);
        $profile_details['profile_update_route'] = route('api.outlet.profile.update', [$outlet_instance]);

      }catch(Exception $e){
        $profile_details["error"] = array("msg" => $e->getMessage());
      }

      return $profile_details;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(OutletRequest $request, Outlet $outlet)
    { 
      try{
        if(request()->user()->id!=$outlet->users->id){
          $data = array("status" => 400, 'msg' => 'You aren\'t authorized to make this request.');
          
          return response()->json($data);  
        }
        $fields = $request->toArray();
        $latLng = strval($request->gps_location);
        $latitude = !empty($latLng)? explode(',', $latLng)[0]:NULL;
        $longitude = !empty($latLng)? explode(',', $latLng)[1]:NULL;
        $fields['latitude'] = $latitude;
        $fields['longitude'] = $longitude;
        $gps_address = NULL;
        if(isset($latitude) && isset($longitude)){
          $gps_address = $this->getGPSAddress($latitude, $longitude);
        }

        $fields['image'] = $outlet->image;
        if($request->has('image')){
          $image = $request->image;
          if(!empty($image)){
            if(!empty($outlet->image)){
              $tempImagePath = "cms/storage/app/public/outlets/" . $outlet->email . "/" . $outlet->image;
              unlink($tempImagePath);
            }
            $tempImageName = $this->getImageName();
            $tempImageDir = "outlets/".$fields['email'];
            $decodedData = base64_decode($image);
            $put = Storage::disk('public')->put($tempImageDir . '/' . $tempImageName, $decodedData);
            $fields['image'] = $tempImageName;
          }
        }
        // else{
          // if(!empty($outlet->image)){
          //   $tempImagePath = "cms/storage/app/public/outlets/" . $outlet->email . "/" . $outlet->image;
          //   unlink($tempImagePath);
          // }
          // $fields['image'] = NULL;
        // }
        
        unset($fields['email']);
        $fields['gps_location'] = $gps_address;
        unset($fields['gps_location']);
        unset($fields['latitude']);
        unset($fields['longitude']);
        $updated_instance = tap($outlet)->update($fields);

        $user_instance = Outlet::whereEmail($updated_instance->email)->first()->users;
        if($user_instance){
          $updated = $user_instance->update([
            'name'=> $updated_instance->outlet_name,
            'phone'=> $updated_instance->phone,
            'password'=> bcrypt($updated_instance->phone),
          ]);
        }
        
        $data = array("data" => new OutletResource($updated_instance), 'msg' => 'Profile Updated Successfully.');
        
        return response()->json($data);  
      }catch(Exception $e){
        $data = array("error_msg" => $e->getMessage(), 'msg' => 'Some error occured while processing your request.');
        return response()->json($data);  
      }
      
    }

    public function suppliers(Outlet $outlet){
      try{
        if(request()->user()->id!=$outlet->users->id){
          $data = array("status" => 400, 'msg' => 'You aren\'t authorized to make this request.');
          
          return response()->json($data);  
        }
        
        if($outlet->status=='Disabled'){
          $data = array("status" => 401, 'msg' => 'Your account has been disabled.', "profile_status" => $outlet->status);
          
          return response()->json($data);  
        }

        $company_ids = $outlet->suppliers->pluck('id')->toArray();
        $company_clients = $outlet->clients()->get()->whereIn('company_id', $company_ids);
        $client = OutletClientResource::collection($company_clients);
        
        $data = array("data"=>$client);

        return $data;
      }catch(Exception $e){
        $data = array("error_msg" => $e->getMessage(), 'msg' => 'Some error occured while processing your request.');
        return response()->json($data);  
      }
    }

    private function getGPSAddress($latitude, $longitude){
      try{
        $geocodeFromLatLong = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?latlng='.trim($latitude).','.trim($longitude).'&sensor=false&key=AIzaSyDR6v2elDctrDptLyvTjpTBEs6z7CLSfW8'); 
        $output = json_decode($geocodeFromLatLong);
        $status = $output->status;
        $address = ($status=="OK")?$output->results[1]->formatted_address:'';

      }catch(Exception $e){
        $address = '';
      }

      return $address;
    }

    private function getImageName()
    {
      $imagePrefix = md5(uniqid(time(), true));
      $imageName = $imagePrefix . ".png";
      return $imageName;
    }

    private function getCountryAndCityList($country_id=null)
    {
      $list = array();
      try{
       if(isset($country_id)){
          $list['cities'] = $this->getCities($country_id);
        }else{
          $list['cities'] = null;
        }
        // $list['countries'] = DB::table('countries')->select('id as value', 'name', 'phonecode as phone_ext')->get()->toJson();
      }catch(Exception $e){
        return $e->getMessage();
      }

      return $list;
    }

    private function getCities($country_id){
      try{
        $cities = DB::table('cities')->leftJoin('states', 'states.id', 'cities.state_id')->where('states.country_id', $country_id)->select('cities.id as value', 'cities.name', 'states.country_id')->get()->toJson();

        return $cities;
      }catch(Exception $e){
        return $e->getMessage();
      }

      return $cities;
    }

    public function fetchCities(Request $request)
    {
      $country_id = $request->country_id;
      $cities = $this->getCities($country_id);

      return $cities;
    }

    public function schemesForRetailerApp(Request $request)
    {


        $company_id = $request->company_id;
        $client_id = $request->client_id;


        $schemes = Scheme::where('company_id', $company_id)->get();

        $schemes = $schemes->each(function ($scheme) {
            if ($scheme->start_date > Carbon::now() || $scheme->end_date < Carbon::now()->endOfDay()) {
                $scheme->status = 0;
            }
        });

        return response()->json([
            'schemes' => $schemes
        ]);
    }
}
