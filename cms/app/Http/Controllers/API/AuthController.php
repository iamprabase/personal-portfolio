<?php

namespace App\Http\Controllers\API;

use App\Jobs\SendDemoLoginMail;
use DB;
use Log;
use Auth;
use App\Bank;
use stdClass;
use App\Company;
use App\Employee;
use App\Attendance;
use App\BusinessType;
use App\ClientSetting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;


class AuthController extends Controller
{

    public function login(Request $request)
    {
      $phone =$request->phone;
      $domain = $request->company;
      $password = $request->password;
      $device = $request->device;
      $imei = $request->imei;
      $changeDevice = $request->change_device;

        if(isset($domain) && isset($phone)) { 
          $msg = "";
          $companyDetails = Company::where('domain', $domain)->first();
          
          if($companyDetails){
            if($companyDetails->is_verified!=1) $msg = "Company is not verified.";
            elseif($companyDetails->is_active==1) $msg = "Your account has been disabled.";
            elseif($companyDetails->is_active==0) $msg = "Sorry, your subscription has ended.";
          }else{
            $msg = "Invalid Company Domain.";
          }

          if(!(empty($msg))){
            $response = array("status" => false, "message" => $msg, "data" => $domain);
            $this->sendResponse($response);
          }

          $phoneOrEmail = filter_var($phone, FILTER_VALIDATE_EMAIL);
          if($phoneOrEmail) $field = 'email';
          else $field = 'phone';

          $credentials = [
            $field => $phone,
            'password' => $password,
            'company_id' => $companyDetails->id,
            'deleted_at' => null
          ];
          $company_id = $companyDetails->id;

          $employee = Employee::where('company_id',$companyDetails->id)->where(function($q)use($phone){
            $q = $q->where('phone',$phone)->orWhere('email',$phone);
          })->where('status','Active')->first();
          if(!$employee){
            return response(['status'=>false,'message'=>'Invalid user or user has been deactivated']);
          }

          if(auth()->attempt($credentials)) {
            $user = Auth::user();
            $employee = Employee::where('user_id',$user->id)->first();
            if($employee->status=='Inactive'){
              $this->sendResponse(array("status" => false, "message" => "Your account has been deactivated."));
            }
            $subsAlerts = array('display'=> false, 'dismiss' => false, 'msg'=>"");
            $subsMsg = array("display" => false, "msg" => "");
            
            if (empty($employee)) {
              $this->sendResponse(array("status" => false, "message" => "Invalid Password."));
            }else{
              $currentImeiInDB = $employee->imei;
              if(!empty($currentImeiInDB) && ($currentImeiInDB != $imei) && ($changeDevice == "false") && $user->is_logged_in == 1) $this->sendResponse(array("status" => true,"message" => "","device_changed" => true));

              $user->is_logged_in = 1;
              $user->update();

              $companySettings =  DB::table('client_settings')
                                  ->select('client_settings.*','countries.name as country_name','states.name as state_name','cities.name as city_name')
                                  ->leftJoin('countries','client_settings.country','countries.id')
                                  ->leftJoin('states','client_settings.state','states.id')
                                  ->leftJoin('cities','client_settings.city','cities.id')
                                  ->where('company_id', $employee->company_id)->first();

              $taxTypes = DB::table('tax_types')->select('id', 'company_id', 'name as tax_name', 'percent as tax_percent', 'default_flag', 'deleted_at')->where('company_id', $employee->company_id)->whereNull('deleted_at')->get();
              $partyTypes = DB::table('partytypes')->where('company_id', $employee->company_id)->get();
              $marketAreas = DB::table('marketareas')->where('company_id', $employee->company_id)->get();
              $banks = Bank::where('company_id', $employee->company_id)->get();

              $employee->domain = $companyDetails->domain;
              $employee->country = $companyDetails->country;
              $employee->company_name = $companyDetails->company_name;
              //$employee->country_code     = '+'.$countryCode;
              $employee->tax_types = empty($taxTypes) ? null : json_encode($taxTypes);
              $employee->party_types = empty($partyTypes) ? null : $partyTypes;//json_encode($partyTypes);
              $employee->company_settings = empty($companySettings) ? null : json_encode($companySettings);
              $employee->marketareas = empty($marketAreas) ? null : json_encode($marketAreas);
              $employee->banks = empty($banks) ? null : json_encode($banks);
              $employee->pendingColorStatus = getColor('Pending', $employee->company_id)['color'];
              $immediateJuniors = Employee::where('superior', $employee->id)->pluck('id')->toArray();
              $employee->isLowestInChain = empty($immediateJuniors) && !$employee->is_admin? true : false;
              $employee->immediateJuniors = json_encode($immediateJuniors);
              
              activity()->log('Logged In', $employee->user_id);

              $fbID = $this->getArrayValue($request->all(), "firebase_token");
              $checkOut = false;
              $multiple_login = false;
              if (!empty($fbID)) {

                $currentTokenInDB = getObjectValue($employee,'firebase_token');
                
                $tokenUpdated = DB::table('employees')->where('id', $employee->id)->update(['firebase_token' => $fbID,'device' => $device,'imei' => $imei]);
                if (!empty($tokenUpdated)) {
                  $sendingEmployee = new stdClass();
                  $sendingEmployee->firebase_token = $fbID;
                  $sendingEmployee->imei = $imei;
                  $sendingEmployee->new_device_detected = (!empty($currentImeiInDB) && ($currentImeiInDB != $imei))?true:false;
                  $multiple_login = true;
                  $dataPayload = array("data_type" => "employee", "employee" => $sendingEmployee, "action" => "logout", "msg" => "We detected a new login. You are being logged out from this device.");
                  if (!empty($currentTokenInDB)) {
                      $msgSent = sendPushNotification_([$currentTokenInDB], 4, null, $dataPayload);
                  }
                  $currentDateTime = date('Y-m-d H:i:s');
                  $currentDate = date('Y-m-d', strtotime($currentDateTime));
                  $currentTime = date('H:i:s', strtotime($currentDateTime));
                  $isCheckedIn = Attendance::whereCompanyId($company_id)
                                  ->whereEmployeeId($employee->id)
                                  ->where('adate', '=', $currentDate)->whereCheckType(1)
                                  ->where('check_datetime', '<=', $currentDateTime )
                                  ->orderBy('id', 'desc')->first();
                  if($isCheckedIn){
                    $isCheckedOut = Attendance::whereCompanyId($company_id)
                                  ->whereEmployeeId($employee->id)
                                  ->where('adate', '=', $currentDate)->whereCheckType(2)
                                  ->whereBetween('check_datetime', [$isCheckedIn->check_datetime, $currentDateTime] )
                                  ->first();
                    if(!$isCheckedOut){
                      $checkOut = true;
                      $unix_timestamp = round(microtime(true)*1000);
                      $datetime = date('Y-m-d H:i:s', $unix_timestamp/1000);
                      $date = date('Y-m-d', strtotime($datetime));
                      $time = date('H:i:s', strtotime($datetime));
                      Attendance::updateOrCreate(
                        [
                            "unique_id" => md5(uniqid())
                        ],

                        [
                            "company_id" => $company_id,
                            "employee_id" => $employee->id,
                            "check_datetime" => $datetime,
                            "adate" => $date,
                            "atime" => $time,
                            "unix_timestamp" => $unix_timestamp,
                            "check_type" => 2,
                            "device" => $device
                        ]
                      );
                    }
                  }
                }
              }
            }

            $data['status'] = true;
            $data['checkout'] = $checkOut;
            $data['multiple_login'] = $multiple_login;
            $data['message'] = "success";
            if(Auth::user()->token()){
              $accessToken = Auth::user()->token();
              $accessToken->revoke();
            }
            $data['token'] = $user->createToken('accessToken')->accessToken;               
            $unique_code = time().substr(uniqid(),3,6);
            date_default_timezone_set($this->getTimeZone($company_id));
            $datetime = date('Y-m-d H:i:s');
            DB::table('changes_last_fetched')->updateOrInsert(
              ['unique_token' => $unique_code],
              ['user_id' => $user->id, 'unique_token' => $unique_code, 'order_fetch_datetime'=>$datetime, 'zeroorder_fetch_datetime'=>$datetime, 'collection_fetch_datetime'=>$datetime, 'activity_fetch_datetime'=> $datetime ]
            );
            $data['fetch_token'] = $unique_code;
            $data['data'] = $employee;
            $data['permissions'] = [];

            
            $permissions = Permission::where('is_mobile',1)->where(function($q)use($company_id){
                $q = $q->where('permission_type','Global')->orWhere('company_id',$company_id);
            })->get();
            foreach($permissions as $permission){
              if($permission->permission_type=='Company')
                $data['permissions']['pt-'.$permission->name] = ($user->hasPermissionTo($permission->id))?'1':'0';
              else
                $data['permissions'][$permission->name] = ($user->hasPermissionTo($permission->id))?'1':'0';
            }
            
            $data['subsMsg'] = $subsMsg;
            $data['subsAlerts'] = $subsAlerts;
            $data['businessTypes'] = BusinessType::where('company_id',$companyDetails->id)->get();
            return response($data);
          }else{
            return response(
                [
                    'status'=>false,
                    'message'=>'Invalid credentials.'
                ]
            );
          }
          return response($postData);
        }
    }

    public function getMobileAppVersionCode(){

        $postData = $this->getJsonRequest();
        
        $versionCode = env('MOBILE_APP_VERSION_CODE');

        if(empty($versionCode)){
            $response = array("status" => false, "message" => "success", "version_code" => "");
        } else {
            $response = array("status" => true, "message" => "success", "version_code" => $versionCode);
        }
        $this->sendResponse($response);
    }

    private function getTimeZone($company_id){
      try{
        // $setting = ClientSetting::whereCompanyId($company_id)->first();
        // if($setting->time_zone) $timezone = $setting->time_zone;
        // else 
        $timezone = 'Asia/Kathmandu';

        return $timezone;
      }catch(\Exception $e){
        Log::error($e->getMessage());
        return 'Asia/Kathmandu';
      }
    }

    //common methods
    private function sendEmptyResponse()
    {
        $response = array("status" => true, "message" => "No Record Found", "data" => array());
        echo json_encode($response);
        exit;
    }

    private function getArrayValue($arraySource, $key, $emptyText = null, $trim = FALSE)
    {
        if (is_array($arraySource) && !empty($arraySource[$key])) {
            return $trim == TRUE ? trim($arraySource[$key]) : $arraySource[$key];
        } else {
            return $emptyText;
        }
    }

    private function sendResponse($response)
    {
        echo json_encode($response);
        exit;
    }

    private function getJsonRequest($isJson = true)
    {
        if ($isJson) {
            return json_decode($this->getFileContent(), true);
        } else {
            return $_POST;
        }
    }

    private function getFileContent()
    {
        return file_get_contents('php://input');
    }

    public function demoLoginMail(Request $request){
      $email_id = $request->email_id;
      SendDemoLoginMail::dispatch($email_id);
      return response()->json([
          'status' => 200,
          'message' => 'It might take some minutes to send mail.'
      ]);
    }

    
}
