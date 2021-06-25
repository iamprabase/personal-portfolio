<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Company;
use \App\Employee;
use \App\User;
use \App\Client;
use \App\Attendance;
use \App\Location;
use \App\RawLocation;
use \App\Order;
use \App\Collection;
use \App\NoOrder;
use \App\Activity;
use \App\ModuleAttribute;
use App\Beat;
use App\BeatVPlan;
use App\BeatPlansDetails;
use App\Leave;
use App\Stock;
use App\StockDetail;
use App\TourPlan;
use App\ProductReturn;
use App\ReturnDetail;
use App\DayRemarks;
use App\CollateralsFolder;
use App\CollateralsFile;
use Eloquent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Spatie\Activitylog\Models\Activity as LogActivity;
use Hash;
use Log;
use stdClass;

class ApiController extends Controller
{

    public function __construct()
    {
        $this->checkApiKey($this->getJsonRequest());
    }

    public function getMobileAppVersionCode(){

        $postData = $this->getJsonRequest();
        
        //$versionCode = defined('MOBILE_APP_VERSION_CODE')?MOBILE_APP_VERSION_CODE:1;
        $versionCode = 65;

        if(empty($versionCode)){

            $response = array("status" => false, "message" => "success", "version_code" => "");
        } else {
            $response = array("status" => true, "message" => "success", "version_code" => $versionCode);
        }
        $this->sendResponse($response);
    }

    /**
     * Can be use for testing and running script as per need
     * @param Request $request
     */
    public function test(Request $request)
    {

        $apiKey = $request->api_key;
        if ($apiKey != "mobile9842034642") $this->sendResponse(array("message" => "invalid key"));
        $this->sendEmptyResponse();
    }


    public function mobileLogin()
    {

        $postData = $this->getJsonRequest();
        $phone = $this->getArrayValue($postData, "phone");
        $domain = $this->getArrayValue($postData, "company");
        $password = $this->getArrayValue($postData, "password");
        $device = $this->getArrayValue($postData, "device");
        $imei = $this->getArrayValue($postData, "imei");
        $changeDevice = $this->getArrayValue($postData, "change_device");

        if (empty($phone) || empty($domain)) $this->sendEmptyResponse();

        if(isset($domain) && isset($phone)) {
            
            $msg = "";
            $companyDetails = Company::where('domain', $domain)->first();
            
            if($companyDetails){
                if($companyDetails->is_verified!=1){
                    $msg = "Company is not verified.";
                }elseif($companyDetails->is_active!=2){
                    $msg = "Your account has been deactivated.";
                }
            }else{
                $msg = "Invalid Company Domain.";
            }
            if(!(empty($msg))){
                $response = array("status" => false, "message" => $msg, "data" => $domain);
                $this->sendResponse($response);
            }else{
                $phoneOrEmail = filter_var($phone, FILTER_VALIDATE_EMAIL);
                $employee_details = Employee::leftJoin('companies', 'employees.company_id', '=', 'companies.id')
                        ->where('companies.domain', $domain)
                        ->where('employees.status', 'Active')
                        ->whereNull('employees.deleted_at')
                        ->select('employees.*', 'companies.domain', 'companies.country','companies.company_name');
                if($phoneOrEmail){
                    $emp_email = $employee_details->where('email', $phone)->count();
                    if($emp_email>0){
                        $employee = $employee_details->where('email', $phone)->where('password', $password)->first();
                    }else{
                        $this->sendResponse(array("status" => false, "message" => "Invalid Email Address.")); 
                    }
                }else{
                    $emp_phone = $employee_details->where('phone', $phone)->count();
                    if($emp_phone>0){
                        $employee = $employee_details->where('phone', $phone)->where('password', $password)->first();
                    }else{
                        $this->sendResponse(array("status" => false, "message" => "Invalid Phone Number.")); 
                    }
                }
                if(!(empty($employee))){
                    $user = User::where('id', $employee->user_id)->where('company_id', $employee->company_id)->first();
                    $isCompanyEmployee = $user->employees()->where('user_id', $employee->user_id)->first();
                    $isCompanyManager = $user->managers()->where('user_id', $employee->user_id)->first();
                }
                if (empty($employee)) {
                    $this->sendResponse(array("status" => false, "message" => "Invalid Password."));
                }elseif($isCompanyManager || $isCompanyEmployee){

                    $currentImeiInDB = getObjectValue($employee,'imei');
                    if(!empty($currentImeiInDB) && ($currentImeiInDB != $imei) && ($changeDevice == "false")){

                        $this->sendResponse(array("status" => true,"message" => "","device_changed" => true));
                    }

                    $companySettings = DB::table('client_settings')->where('company_id', $employee->company_id)->first();
                    $taxTypes = DB::table('tax_types')->select('id', 'company_id', 'name as tax_name', 'percent as tax_percent')->where('company_id', $employee->company_id)->get();
                    $partyTypes = DB::table('partytypes')->where('company_id', $employee->company_id)->get();
                    $marketAreas = DB::table('marketareas')->where('company_id', $employee->company_id)->get();
                    $banks = DB::table('banks')->where('company_id', $employee->company_id)->get();

                    //$employee->country_code     = '+'.$countryCode;
                    $employee->tax_types = empty($taxTypes) ? null : json_encode($taxTypes);
                    $employee->party_types = empty($partyTypes) ? null : json_encode($partyTypes);
                    $employee->company_settings = empty($companySettings) ? null : json_encode($companySettings);
                    $employee->marketareas = empty($marketAreas) ? null : json_encode($marketAreas);
                    $employee->banks = empty($banks) ? null : json_encode($banks);
                    $employee->pendingColorStatus = getColor('Pending', $employee->company_id)['color'];

                    activity()->log('Logged In', $employee->user_id);
                    
                    $fbID = $this->getArrayValue($postData, "firebase_token");
                    if (!empty($fbID)) {

                        $currentTokenInDB = getObjectValue($employee,'firebase_token');
                        
                        $tokenUpdated = DB::table('employees')->where('id', $employee->id)->update(['firebase_token' => $fbID,'device' => $device,'imei' => $imei]);
                        if (!empty($tokenUpdated)) {

                            $sendingEmployee = new stdClass();
                            $sendingEmployee->firebase_token = $fbID;
                            $sendingEmployee->imei = $imei;
                            $sendingEmployee->new_device_detected = (!empty($currentImeiInDB) && ($currentImeiInDB != $imei))?true:false;
                            $dataPayload = array("data_type" => "employee", "employee" => $sendingEmployee, "action" => "logout");
                            if (!empty($currentTokenInDB)) {
                                $msgSent = sendPushNotification_([$currentTokenInDB], 4, null, $dataPayload);
                            }
                        }
                    }
                    $response = array("status" => true, "message" => "success", "data" => $employee);
        
                    $this->sendResponse($response);
                }
            }
       
        }
    }

    public function logoutPreviousDevice()
    {

        $postData = $this->getJsonRequest();
        $employeeID = $this->getArrayValue($postData, "employee_id");
        $tokenFromDevice = $this->getArrayValue($postData, "token");

        if (empty($employeeID) || empty($tokenFromDevice)) $this->sendEmptyResponse();

        $employee = Employee::find($employeeID);
        activity()->log('Logged Out', $employee->user_id);
        $tokenFromDB = $employee->firebase_token;

        $employee->firebase_token = $tokenFromDevice;
        $employee->save();

        $dataPayload = array("data_type" => "employee", "employee" => $employee, "action" => "logout");
        $msgSent = sendPushNotification_([$tokenFromDB], 4, null, $dataPayload);
        $response = array("status" => true, "message" => "success", "data" => $employee);
        $this->sendResponse($response);
    }




    /**
     * Product Related
     */


    /**
     * Fetch Products
     */
    public function fetchProducts()
    {
        $postData = $this->getJsonRequest();
        //Log::info('info', array("postData"=>print_r($postData,true)));
        $companyID = $this->getArrayValue($postData, "company_id");
        $products = $this->getProducts($companyID);
        //Log::info('info', array("products"=>print_r($products,true)));
        $response = array("status" => true, "message" => "success", "data" => $products);
        $this->sendResponse($response);
    }


    /**
     * Location Related
     */

    public function fetchLocation()
    {
        $postData = $this->getJsonRequest();
        $companyID = $this->getArrayValue($postData, "company_id");
        $employeeID = $this->getArrayValue($postData, "employee_id");
        $like = $this->getArrayValue($postData, "date") . "%";
        $locations = DB::table("locations")->where("company_id", $companyID)->where("employee_id", $employeeID)->where("created_at", "like", $like)->get()->toArray();

        $response = array("status" => true, "message" => "success", "data" => $locations);
        //Log::info('info', array("locationData"=>print_r($locationData,true)));
        $this->sendResponse($response);
    }

    public function saveLocation()
    {


        $postData = $this->getJsonRequest();
        //Log::info('info', array("postData"=>print_r($postData,true)));
        $uniqueID = $this->getArrayValue($postData, "unique_id");
        $companyID = $this->getArrayValue($postData, "company_id");
        $employeeID = $this->getArrayValue($postData, "employee_id");
        $locationData = array(
            'unique_id' => $uniqueID,
            'company_id' => $companyID,
            'employee_id' => $employeeID,
            'latitude' => $this->getArrayValue($postData, "latitude"),
            'longitude' => $this->getArrayValue($postData, "longitude"),
            'altitude' => $this->getArrayValue($postData, "altitude", 0.000),
            'accuracy' => $this->getArrayValue($postData, "accuracy"),
            'speed' => $this->getArrayValue($postData, "speed"),
            'speed_accuracy' => $this->getArrayValue($postData, "speed_accuracy"),
            'battery_level' => $this->getArrayValue($postData, "battery_level"),
            'activity' => $this->getArrayValue($postData, "activity"),
            'still' => $this->getArrayValue($postData, "still"),
            'datetime' => $this->getArrayValue($postData, "datetime"),
            'date' => $this->getArrayValue($postData, "date"),
            'unix_timestamp' => $this->getArrayValue($postData, "unix_timestamp")
        );

        //Log::info('info', array("locationData"=>print_r($locationData,true)));

        $updateRecent = $this->getArrayValue($postData, "update_recent");

        if ($updateRecent == "true") {
            //fetch recent location
            $today = date('Y-m-d');
            $recentEmpLocation = RawLocation::select('*')
                ->where('employee_id', $employeeID)
                ->where('company_id', $companyID)
                ->where('date', $today)
                ->orderBy('unix_timestamp', 'desc')
                ->get()
                ->first();
            //Log::info('info', array("recentEmpLocation"=>print_r($recentEmpLocation,true)));
            if (!empty($recentEmpLocation)) {
                $uniqueID = $recentEmpLocation->unique_id;
            }

        }

        $location = RawLocation::updateOrCreate(
            [
                "unique_id" => $uniqueID
            ],
            $locationData
        );
        //Log::info('info', array("locationAfterSaved"=>print_r($locationData,true)));
        if ($location->wasRecentlyCreated || $location->wasChanged()) {
            $response = array("status" => true, "message" => "successfully saved", "data" => $location);
        } else {
            $response = array("status" => true, "message" => "not Saved", "data" => null);
        }

        $this->sendResponse($response);
    }

    private function manageUnsyncedLocation($postData, $returnItems = false)
    {

        $return = $returnItems ? array() : false;
        $rawData = $this->getArrayValue($postData, "unsynced_data");
        $companyID = $this->getArrayValue($postData, "company_id");
        $employeeID = $this->getArrayValue($postData, "employee_id");
        $versionCode = $this->getArrayValue($postData,"version_code");

        Log::info('info', array('manageUnsyncedLocation' => print_r('************************', true)));
        //Log::info('info', array("rawData"=>print_r($rawData,true)));

        $data = json_decode($rawData, true);
        if (empty($data)){

            Log::info('info', array('' => print_r('error in:json_decode ', true)));
            return $return;

        }

        /*******************************************************************/
        /********************* Starts managing data in file*****************/
        $dataGroupedByDate = arrayGroupBy($data, "date");


        foreach ($dataGroupedByDate as $key => $value) {

            $tempDate = $key;
            $decodedContent = array();
            $fileName = getFileName($companyID, $employeeID, $tempDate);
            $isExists = Storage::disk("local")->exists($fileName);
            $fileContent = $isExists ? Storage::get($fileName) : "";
            if (!empty($fileContent)) $decodedContent = json_decode($fileContent);
            $mergedArray = array_merge($decodedContent, $this->combineArrayElements($value));

            $logValue = $companyID.",".$employeeID.",".$versionCode.",".count($value).",".date("Y-m-d h:m:s");

            Log::info('info', array('cid,eid,vcode,count,dt' => print_r($logValue, true)));
            //Log::info('info', array("versionCode" => print_r($versionCode, true)));
            //Log::info('info', array("inside manageUnsyncedLocation count" => print_r(count($value), true)));
            //Log::info('info', array("CompanyID/EmployeeID" => print_r($companyID . "/" . $employeeID, true)));
            $encodedData = json_encode($mergedArray);
            $bytes = Storage::put($fileName, $encodedData);
            if(empty($bytes)){
                $return = $returnItems ? array() : false;
                Log::info('info', array('error' => print_r("error in Storage::put", true)));
                break;

            } else {

                $return = $returnItems ? array() : true;

            }
        }

        /***********************End managing data in file*******************/
        /*******************************************************************/

        return $return;
    }

    private function manageUnsyncedLocationDB($postData, $returnItems = false)
    {

        $return = false;
        $rawData = $this->getArrayValue($postData, "unsynced_data");
        $companyID = $this->getArrayValue($postData, "company_id");
        $employeeID = $this->getArrayValue($postData, "employee_id");

        if (empty($rawData)) {
            return $returnItems ? array() : false;
        }


        $data = json_decode($rawData, true);
        $idMap = DB::transaction(function () use ($data) {
            $tempArray = array();
            $mycounter = 0;
            foreach ($data as $key => $value) {

                $uniqueID = $this->getArrayValue($value, "unique_id");

                $tempCompanyID = $this->getArrayValue($value, "company_id");
                $tempEmployeeID = $this->getArrayValue($value, "employee_id");

                if ($mycounter == 0) {

                    $time1 = time();

                }

                if ($mycounter == count($data) - 1) {
                    $time2 = time();
                    Log::info('info', array("Syncing" => print_r("*****************************************************", true)));
                    Log::info('info', array("companyID" => print_r($tempCompanyID, true)));
                    Log::info('info', array("employeeID" => print_r($tempEmployeeID, true)));
                    Log::info('info', array("time2" => print_r($time2, true)));
                    Log::info('info', array("time1" => print_r($time1, true)));
                    Log::info('info', array("T Diff" => print_r($time2 - $time1, true)));
                }

                $locationData = array(

                    'unique_id' => $uniqueID,
                    'company_id' => $tempCompanyID,
                    'employee_id' => $tempEmployeeID,
                    'raw_latitude' => $this->getArrayValue($value, "latitude"),
                    'latitude' => $this->getArrayValue($value, "latitude"),
                    'raw_longitude' => $this->getArrayValue($value, "longitude"),
                    'longitude' => $this->getArrayValue($value, "longitude"),
                    'altitude' => $this->getArrayValue($value, "altitude", 0.000),
                    'address' => $this->getArrayValue($value, "address"),
                    'unix_timestamp' => $this->getArrayValue($value, "unix_time"),
                    'accuracy' => $this->getArrayValue($value, "accuracy"),
                    'distance_from_last_gps' => $this->getArrayValue($value, "distance_from_last_gps", 0.000),
                    'speed' => $this->getArrayValue($value, "speed", 0.000),
                    'speed_accuracy' => $this->getArrayValue($value, "speed_accuracy"),
                    'battery_level' => $this->getArrayValue($value, "battery_level"),
                    'activity' => $this->getArrayValue($value, "activity"),
                    'still' => $this->getArrayValue($value, "still"),
                    'datetime' => $this->getArrayValue($value, "datetime"),
                    'date' => $this->getArrayValue($value, "date")
                );


                if (!empty($tempCompanyID) && !empty($tempEmployeeID)) {

                    $location = Location::updateOrCreate(
                        [
                            "unique_id" => $uniqueID
                        ],
                        $locationData
                    );

                    if ($location->wasRecentlyCreated || $location->wasChanged || $location->exists) {  //need to add other conditions also
                        array_push($tempArray, $location);
                    }

                }

                if ($mycounter > 100) break;

                $mycounter++;
            }
            return $tempArray;
        });

        if (!empty($idMap)) $return = true;
        return $returnItems ? $idMap : $return;
    }

    public function syncLocations()
    {

        $postData = $this->getJsonRequest();
        $versionCode = $this->getArrayValue($postData, "version_code");
        if (!empty($versionCode) && $versionCode >= 34) {

            $syncedLocation = $this->manageUnsyncedLocation($postData);
            if ($syncedLocation == true) {
                $response = array("status" => true, "message" => "successfully Synced", "data" => array());
            } else {
                $response = array("status" => false, "message" => "Sync Failed", "data" => array());
            }
            $this->sendResponse($response);

        } else {

            $syncedData = $this->manageUnsyncedLocationDB($postData, true);
            $response = array("status" => true, "message" => "successfully Synced", "data" => $syncedData);
            $this->sendResponse($response);
        }

    }


    /**
     *Attendance Related
     */
    public function fetchAttendance()
    {

        $postData = $this->getJsonRequest();
        $companyID = $this->getArrayValue($postData, "company_id");
        $employeeID = $this->getArrayValue($postData, "employee_id");
        //Log::info('info', array('postData' => print_r($postData, true)));


        /*Check if unsynced data is available . if available first update to tha database */
        $syncStatus = $this->manageUnsyncedAttendance($postData);

        $attendances = DB::table('attendances')->where(
            array(
                array("company_id", "=", $companyID),
                array("employee_id", "=", $employeeID)
            )
        )->get()->toArray();

        //Log::info('info', array("attendances"=>print_r($attendances,true)));


        if (empty($attendances)) $this->sendEmptyResponse();
        $response = array("status" => true, "message" => "Success", "data" => $attendances);
        //Log::info('info', array("data"=>print_r($response,true)));
        $this->sendResponse($response);
    }

    private function manageUnsyncedAttendance($postData, $returnItems = false)
    {

        $return = false;
        $rawData = $this->getArrayValue($postData, "unsynced_data");
        $companyID = $this->getArrayValue($postData, "company_id");
        $employeeID = $this->getArrayValue($postData, "employee_id");

        if (empty($rawData)) {
            return $returnItems ? array() : false;
        }

        $data = json_decode($rawData, true);

        $syncedData = array();

        foreach ($data as $key => $value) {

            $uniqueID = $this->getArrayValue($value, "unique_id");
            $atten = Attendance::updateOrCreate(
                [
                    "unique_id" => $uniqueID
                ],

                [
                    "company_id" => $companyID,
                    "employee_id" => $employeeID,
                    "check_datetime" => $this->getArrayvalue($value, "check_datetime"),
                    "adate" => $this->getArrayvalue($value, "adate"),
                    "atime" => $this->getArrayvalue($value, "atime"),
                    "unix_timestamp" => $this->getArrayvalue($value, "unix_time"),
                    "check_type" => $this->getArrayvalue($value, "check_type"),
                    "auto_checkout" => $this->getArrayvalue($value, "auto_checkout"),
                    "latitude" => $this->getArrayvalue($value, "latitude"),
                    "longitude" => $this->getArrayvalue($value, "longitude"),
                    "address" => $this->getArrayvalue($value, "address"),
                    "device" => $this->getArrayvalue($value, "device")
                ]
            );
            //Log::info('info', array("atten inside manageUnsyncedAttendance"=>print_r($atten,true)));

            if ($atten->wasRecentlyCreated || $atten->wasChanged || $atten->exists) {  //need to add other conditions also
                array_push($syncedData, $atten);
                $return = true;
            }
        }

        return $returnItems ? $syncedData : $return;
    }

    public function saveAttendance()
    {
        $postData = $this->getJsonRequest();
        //Log::info('info', array("data "=>print_r($postData,true)));
        $uniqueID = $this->getArrayValue($postData, "unique_id");
        $companyID = $this->getArrayValue($postData, "company_id");
        $attendanceData = array(
            'unique_id' => $uniqueID,
            'company_id' => $companyID,
            'employee_id' => $this->getArrayValue($postData, "employee_id"),
            'check_datetime' => $this->getArrayValue($postData, "created_at"),
            'adate' => $this->getArrayValue($postData, "adate"),
            'atime' => $this->getArrayValue($postData, "atime"),
            'check_type' => $this->getArrayValue($postData, "check_type"),
            'latitude' => $this->getArrayValue($postData, "latitude"),
            'longitude' => $this->getArrayValue($postData, "longitude")
        );
        //$savedID = DB::table('attendances')->insertGetId($attendanceData);
        $atten = Attendance::updateOrCreate(
            [
                "unique_id" => $uniqueID
            ],
            $attendanceData
        );

        if ($atten->wasRecentlyCreated || $atten->wasChanged) {
            $response = array("status" => true, "message" => "successfully saved", "data" => $atten);

        } else {
            $response = array("status" => true, "message" => "not Saved", "data" => "");

        }

        $this->sendResponse($response);
    }

    public function syncAttendances()
    {
        $postData = $this->getJsonRequest();
        $ids = $this->manageUnsyncedAttendance($postData, true);
        //Log::info('info', array("ids"=>print_r($ids,true)));
        $response = array("status" => true, "message" => "successfully Updated", "data" => $ids);
        $this->sendResponse($response);
    }



    /**
     * Client Related
     */


    /**
     * @param bool $return
     * @param null $tempPostData
     * @return array
     */

    public function fetchClients($return = false, $tempPostData = null)
    {

        $postData = $return ? $tempPostData : $this->getJsonRequest();
        $companyID = $this->getArrayValue($postData, "company_id");
        $employeeID = $this->getArrayValue($postData, "employee_id");

        /*Check if unsynced data is available . if available first update to tha database */
        $syncStatus = $this->manageUnsyncedClients($postData);

        $clients = DB::table('clients')
            ->select('clients.*', 'countries.name as country_name', 'states.name as state_name', 'cities.name as city_name','marketareas.name as market_area_name','beat_client.beat_id')
            ->leftJoin('countries', 'clients.country', '=', 'countries.id')
            ->leftJoin('states', 'clients.state', '=', 'states.id')
            ->leftJoin('cities', 'clients.city', '=', 'cities.id')
            ->leftJoin('marketareas', 'clients.market_area', '=', 'marketareas.id')
            ->leftJoin('beat_client', 'clients.id', '=', 'beat_client.client_id')
            //->where("clients.company_id", $companyID)->where("clients.status", "Active")->whereNull("clients.deleted_at")->get()->toArray();
            ->where("clients.company_id", $companyID)->whereNull("clients.deleted_at")->get()->toArray();
        
        if (empty($clients)) {
            if ($return) {
                return array();
            } else {

                $this->sendEmptyResponse();
            }
        }

        $finalArray = array();
        $finalArrayWithAllowed = array();
        // Log::info('info', array("clients data "=>print_r($clients,true)));

        foreach ($clients as $key => $value) {

            $handles = getClientHandlingData($companyID, $value->id,true);
            $value->employee_ids = json_encode($handles);
            $canHandle = in_array($employeeID, $handles);
            $value->can_handle = $canHandle;
            // array_push($finalArray, $value);
            // if (true) {
            //     array_push($finalArrayWithAllowed, $value);

            // }

            $access = getClientAccessibleData($companyID, $value->id,true);
            $value->employee_access_ids = json_encode($access);
            $canAccess = in_array($employeeID, $access);
            $value->can_access = $canAccess;
            array_push($finalArray, $value);
            array_push($finalArrayWithAllowed, $value);

        }

        $response = array("status" => true, "message" => "Success", "data" => $finalArrayWithAllowed);

        if ($return) {
            return $finalArray;
        } else {
            $this->sendResponse($response);
        }
    }

    private function manageUnsyncedClients($postData, $returnItems = false)
    {

        $rawData = $this->getArrayValue($postData, "nonsynced_data");
        $companyID = $this->getArrayValue($postData, "company_id");
        $employeeID = $this->getArrayValue($postData, "employee_id");
        $employeeName = $this->getArrayValue($postData, "employee_name");

        if (empty($rawData)) return false;
        $data = json_decode($rawData, true);
        //Log::info('info', array("raw clients"=>print_r($data,true)));

        $arraySyncedData = array();
        foreach ($data as $key => $value) {
            $companyName = $this->getArrayValue($value, "company_name");
            $uniqueID = $this->getArrayValue($value, "unique_id");

            //$client = DB::table('client')->where('unique_id', $uniqueID)->first();

            $clientData = array(

                'company_id' => $companyID,
                'unique_id' => $uniqueID,
                'company_name' => $companyName,

                'client_type' => $this->getArrayValue($value, "client_type"),
                'superior' => $this->getArrayValue($value, "superior"),
                'market_area' => $this->getArrayValue($value, "marketarea"),
                
                'name' => $this->getArrayValue($value, "name"),
                'client_code' => $this->getArrayValue($value, "client_code"),
                'website' => $this->getArrayValue($value, "website"),
                'email' => $this->getArrayValue($value, "email"),

                'country' => $this->getArrayValue($value, "country"),
                'state' => $this->getArrayValue($value, "state"),
                'city' => $this->getArrayValue($value, "city"),
                'address_1' => $this->getArrayValue($value, "address_1"),
                'address_2' => $this->getArrayValue($value, "address_2"),

                'pin' => $this->getArrayValue($value, "pin"),
                'phonecode' => $this->getArrayValue($value, "phonecode"),
                'phone' => $this->getArrayValue($value, "phone"),
                'mobile' => $this->getArrayValue($value, "mobile"),
                'pan' => $this->getArrayValue($value, "pan"),
                'about' => $this->getArrayValue($value, "about"),
                'location' => $this->getArrayValue($value, "location"),
                'latitude'   =>$this->getArrayValue($value,"latitude"),
                'longitude'  =>$this->getArrayValue($value,"longitude"),
                'status' => $this->getArrayValue($value, "status"),
                'created_by' => $employeeID
            );

            $client = Client::updateOrCreate(
                [
                    "unique_id" => $uniqueID
                ],
                $clientData
            );
            //Log::info('info', array("client after updateOrCreate"=>print_r($client,true)));

            $wasRecentlyCreated = $client->wasRecentlyCreated;
            $wasChanged = $client->wasChanged();
            $isDirty = $client->isDirty();
            //Log::info('info', array("wasRecentlyCreated"=>print_r($wasRecentlyCreated,true)));
            //Log::info('info', array("wasChanged"=>print_r($wasChanged,true)));
            //Log::info('info', array("isDirty"=>print_r($isDirty,true)));

            if ($wasRecentlyCreated || $wasChanged || $client->exists) {
                array_push($arraySyncedData, $client);

                if ($client->wasRecentlyCreated) {
                    $handleData = array(
                        "company_id" => $companyID,
                        "employee_id" => $employeeID,
                        "client_id" => $client->id,
                        "map_type" => "2"
                    );
                    //Log::info('info', array("handleData"=>print_r($handleData,true)));

                    $handle = DB::table('handles')->where(
                        array(
                            array("company_id", $companyID),
                            array("employee_id", $employeeID),
                            array("client_id", $client->id)
                        )
                    )
                        ->get();
                    if (!empty($handle)) {
                        $handleSaved = DB::table('handles')->insertGetId($handleData);
                    }
                    
                    $superiors = $this->getAllEmployeeSuperior($companyID, $employeeID, $getSuperiors=[]);
                    $employeeInstance = Employee::where('company_id', $companyID)->where('is_admin', 1)->pluck('id')->toArray();
                    if(!empty($employeeInstance)){
                      foreach($employeeInstance as $adminId){
                        if(!in_array($adminId, $superiors)){
                            array_push($superiors, $adminId);
                        }
                      }
                    }
                    $supHandle = DB::table('handles')->where("company_id", $companyID)->whereIn("employee_id", $superiors)->where("client_id", $client->id)->pluck('employee_id')->toArray();
                    foreach($superiors as $superior){

                        if (!in_array($superior, $supHandle)) {
                            $supHandleData = array(
                                "company_id" => $companyID,
                                "employee_id" => $superior,
                                "client_id" => $client->id,
                                "map_type" => "2"
                            );
                            $supHandleSaved = DB::table('handles')->insertGetId($supHandleData);
                        }
                    }
                    $beatID = getArrayValue($value,"beat_id");
                    if(!empty($beatID)){

                        DB::table('beat_client')->updateOrInsert(
                            ['client_id' => $client->id],
                            ['beat_id' => $beatID]
                        );

                    }
                }

                $nSaved = saveAdminNotification($companyID, $employeeID, date("Y-m-d H:i:s"), "Added Party", "client", $client);
            }
        }
        return $returnItems ? $arraySyncedData : true;
    }

    public function saveClient()
    {
        $postData = $this->getJsonRequest(); //Log::info('info', array("saveClient"=>print_r($postData,true)));
        $companyID = $this->getArrayValue($postData, "company_id");

        $employeeID = $this->getArrayValue($postData, "employee_id");
        $employeeName = $this->getArrayValue($postData, "employee_name");
        $companyName = $this->getArrayValue($postData, "company_name");
        $createdAt = $this->getArrayValue($postData, "created_at");
        $uniqueID = $this->getArrayValue($postData,"unique_id");
        $clientID = $this->getArrayValue($postData,"client_id");
        $beatID = $this->getArrayValue($postData,"beat_id");
        $clientData = array(

            'company_id' => $companyID,
            'unique_id' => $uniqueID,
            'company_name' => trim($companyName),
            'client_type' => $this->getArrayValue($postData, "client_type", 0),
            'superior' => $this->getArrayValue($postData, "superior", null),
            'market_area' => $this->getArrayValue($postData, "marketarea", 0),
            'name' => $this->getArrayValue($postData, "name"),
            'client_code' => $this->getArrayValue($postData, "client_code"),
            'website' => $this->getArrayValue($postData, "website"),
            'email' => $this->getArrayValue($postData, "email"),

            'country' => $this->getArrayValue($postData, "country"),
            'state' => $this->getArrayValue($postData, "state"),
            'city' => $this->getArrayValue($postData, "city"),
            'address_1' => $this->getArrayValue($postData, "address_1"),
            'address_2' => $this->getArrayValue($postData, "address_2"),

            'pin' => $this->getArrayValue($postData, "pin"),
            'phonecode' => $this->getArrayValue($postData, "phonecode"),
            'phone' => $this->getArrayValue($postData, "phone"),
            'mobile' => $this->getArrayValue($postData, "mobile"),
            'pan' => $this->getArrayValue($postData, "pan"),
            'about' => $this->getArrayValue($postData, "about"),
            'location' => $this->getArrayValue($postData, "location"),
            'latitude'   =>$this->getArrayValue($postData,"latitude"),
            'longitude'  =>$this->getArrayValue($postData,"longitude"),
            'status' => $this->getArrayValue($postData, "status"),
            'created_by' => $employeeID,
            'created_at' => $createdAt
        );

        if(!empty($uniqueID)){
            $client = Client::updateOrCreate(
                [
                    "unique_id" => $uniqueID
                ],
                $clientData
            );

        } else if(!empty($clientID)){
            $client = Client::updateOrCreate(
                [
                    "id" => $clientID
                ],
                $clientData
            );
        }

        if(!empty($beatID)){
            if(!empty($clientID)){
                $prevBeat =  DB::table('beat_client')->where('client_id',$client->id)->value('beat_id');
                DB::table('beat_client')->where('client_id',$client->id)->delete();
            }
            DB::table('beat_client')->insert([
                'client_id' => $client->id,
                'beat_id' => $beatID,
            ]);
            if(!empty($clientID)){
                if((int)$beatID!=(int)$prevBeat){
                    $today = date('Y-m-d');
                    $clientBeatPlans = BeatPlansDetails::where('plandate', '>=', $today)
                                        ->get();
                    if($clientBeatPlans->count()>0){
                        foreach($clientBeatPlans as $clientBeatPlan){
                            $beatPlanBeats = explode(',',$clientBeatPlan->beat_id);
                            foreach($beatPlanBeats as $beatPlanBeat){
                                if((int)$beatPlanBeat==(int)$prevBeat){
                                    $_beatClients = json_decode($clientBeatPlan->beat_clients, true);
                                    $_beats = explode(',',$clientBeatPlan->beat_id);
                                    if(count($_beatClients[$prevBeat])==1){
                                        if(array_key_exists ($prevBeat, $_beatClients)){
                                            $_beatClients[$beatID][] = $client->id;
                                        }else{
                                            $_beatClients[$beatID] = $_beatClients[$prevBeat];
                                        }
                                        unset($_beatClients[$prevBeat]);
                                    }else{
                                        if(array_key_exists ($beatID, $_beatClients)){
                                            $_beatClients[$beatID][] = $client->id;
                                        }else{
                                            $_beatClients[$beatID][] = $client->id;
                                        }
                                        $_ind = array_search((int)$client->id,$_beatClients[$prevBeat]);
                                        unset($_beatClients[$prevBeat][$_ind]);
                                    }
                                    $newBeatIDs = array();
                                    $newClientIDs = array();
                                    foreach($_beatClients as $key=>$beatClient){
                                        array_push($newBeatIDs, $key);
                                        foreach($beatClient as $btClient){
                                            array_push($newClientIDs, $btClient);    
                                        }
                                    }
                                    $clientBeatPlan->beat_id = implode(',',$newBeatIDs);
                                    $clientBeatPlan->client_id = implode(',',$newClientIDs);
                                    $clientBeatPlan->beat_clients = json_encode($_beatClients);
                                    $clientBeatPlan->save();
                                }
                            }
                        }
                    }
                }         
            }
        }
        
        $wasRecentlyCreated = $client->wasRecentlyCreated;
        $wasChanged = $client->wasChanged();
        $isDirty = $client->isDirty();

        if ($wasRecentlyCreated || $wasChanged || $client->exists) {

            $msg = "";
            $savedClient = $clientData;
            $savedClient["id"] = $client->id;
            
            if ($client->wasRecentlyCreated) {
                $handleData = array(
                    "company_id" => $companyID,
                    "employee_id" => $employeeID,
                    "client_id" => $client->id,
                    "map_type" => "2"
                );

                $handle = DB::table('handles')->where(
                    array(
                        array("company_id", $companyID),
                        array("employee_id", $employeeID),
                        array("client_id", $client->id)
                    )
                )->get();

                if (!empty($handle)) {
                    $handleSaved = DB::table('handles')->insertGetId($handleData);
                }
                $superiors = $this->getAllEmployeeSuperior($companyID, $employeeID, $getSuperiors=[]);
                $employeeInstance = Employee::where('company_id', $companyID)->where('is_admin', 1)->pluck('id')->toArray();
                if(!empty($employeeInstance)){
                  foreach($employeeInstance as $adminId){
                    if(!in_array($adminId, $superiors)){
                        array_push($superiors, $adminId);
                    }
                  }
                }
                if(!empty($superiors)){
                  $supHandle = DB::table('handles')->where("company_id", $companyID)->where("client_id", $client->id)->whereIn("employee_id", $superiors)->pluck('employee_id')->toArray();
                  foreach($superiors as $superior){
  
                      if (!in_array($superior, $supHandle)) {
                          $supHandleData = array(
                              "company_id" => $companyID,
                              "employee_id" => $superior,
                              "client_id" => $client->id,
                              "map_type" => "2"
                          );
                          $supHandleSaved = DB::table('handles')->insertGetId($supHandleData);
                      }
                  }
                }
                $msg = "Added Party";
                $action = "add";
            } else {

                $msg = "Updated Party";
                $action = "update";

            }

            $clientHandlingData = getClientHandlingData($companyID, $client->id,true);
            $encodedHandlingData = json_encode($clientHandlingData);
            $savedClient["employee_ids"] = $encodedHandlingData;
            $savedClient["beat_id"] = $beatID;

            $nSaved = saveAdminNotification($companyID, $employeeID, date("Y-m-d H:i:s"), $msg, "client", $savedClient);
            $sent = sendPushNotification_(getFBIDs($companyID), 10, null, array("data_type" => "client", "employee_id"=>$employeeID,"client" => $savedClient, "action" => $action));

        }

        $response = array("status" => true, "message" => "successfully saved", "data" => $savedClient);
        $this->sendResponse($response);
    }

    public function getAllEmployeeSuperior($cId, $empId, $superiors){
      $company_id = $cId;
      $getSuperior = Employee::where('id', $empId)->where('company_id', $company_id)->first();
      if(!(empty($getSuperior->superior)) && !(in_array($getSuperior->superior, $superiors))){
        $superiors[] = $getSuperior->superior;
        $superiors = $this->getAllEmployeeSuperior($cId, $getSuperior->superior, $superiors);
      }
      return $superiors;
    }

    public function syncClients()
    {

        $postData = $this->getJsonRequest();
        //Log::info('info', array("inside syncClients"=>print_r($postData,true)));
        $clientArray = $this->manageUnsyncedClients($postData, true);
        //Log::info('info', array("synced Clients"=>print_r($clientArray,true)));

        $orderArray = array();
        $noorderArray = array();
        $collectionArray = array();
        $meetingArray = array();
        $expenseArray = array();
        $taskArray = array();

        foreach ($clientArray as $key => $client) { // loop for each synced clients for syncing other child data

            $syncedOrders = $this->manageUnsyncedOrder($postData, true, $client);
            foreach ($syncedOrders as $key => $value) {
                array_push($orderArray, $value);
            }

            $syncedNoOrders = $this->manageUnsyncedNoOrder($postData, true, $client);
            foreach ($syncedNoOrders as $key => $value) {
                array_push($noorderArray, $value);
            }

            $syncedCollections = $this->manageUnsyncedCollection($postData, true, $client);
            foreach ($syncedCollections as $key => $value) {
                array_push($collectionArray, $value);
            }

            $syncedMeetings = $this->manageUnsyncedMeeting($postData, true, $client);
            foreach ($syncedMeetings as $key => $value) {
                array_push($meetingArray, $value);
            }

            $syncedExpenses = $this->manageUnsyncedExpense($postData, true, $client);
            foreach ($syncedExpenses as $key => $value) {
                array_push($expenseArray, $value);
            }

            $syncedTasks = $this->manageUnsyncedTask($postData, true, $client);
            foreach ($syncedTasks as $key => $value) {
                array_push($taskArray, $value);
            }
        }

        //Log::info('info', array("synced Order"=>print_r($orderArray,true)));
        $response = array(
            "status" => true,
            "message" => "success",
            "data" => $clientArray,
            "orders" => $orderArray,
            "no_orders" => $noorderArray,
            "collections" => $collectionArray,
            "meetings" => $meetingArray,
            "expenses" => $expenseArray,
            "tasks" => $taskArray
        );
        $this->sendResponse($response);
    }


    /**
     * Collection Related
     */
    public function fetchCollection($return = false, $tempPostData = null)
    {

        $postData = $this->getJsonRequest();
        //Log::info('info', array("postData in fetchCollection"=>print_r($postData,true)));

        $companyID = $this->getArrayValue($postData, "company_id");
        $employeeID = $this->getArrayValue($postData, "employee_id");

        /*Check if unsynced data is available . if available first update to tha database */
        $syncStatus = $this->manageUnsyncedCollection($postData);

        $collections = DB::table('collections')
            ->select('collections.*', 'clients.company_name','banks.name as bank_name')
            ->leftJoin('clients', 'collections.client_id', '=', 'clients.id')
            ->leftJoin('banks', 'collections.bank_id', '=', 'banks.id')
            ->where('collections.company_id', $companyID)
            ->where('collections.employee_id', $employeeID)
            ->whereNull('collections.deleted_at')
            ->get()->toArray();

        if (empty($collections)) {
            if ($return) {
                return array();
            } else {

                $this->sendEmptyResponse();
            }
        }

        $finalArray = array();
        foreach ($collections as $key => $value) {

            $imageArray = getImageArray("collection", $value->id,$companyID,$employeeID);
            $value->images = json_encode($this->getArrayValue($imageArray, "images"));
            $value->image_paths = json_encode($this->getArrayValue($imageArray, "image_paths"));
            array_push($finalArray, $value);
        }

        $response = array("status" => true, "message" => "Success", "data" => $finalArray);

        if ($return) {
            return $finalArray;
        } else {
            $this->sendResponse($response);
        }
    }

    private function manageUnsyncedCollection($postData, $returnItems = false, $client = null)
    {

        $rawData = $this->getArrayValue($postData, "nonsynced_collection");
        $companyID = $this->getArrayValue($postData, "company_id");
        $employeeID = $this->getArrayValue($postData, "employee_id");

        if (empty($rawData)) {
            return $returnItems ? array() : false;
        }

        $data = json_decode($rawData, true);
        //Log::info('info', array("unsynced collection"=>print_r($data,true)));
        $arraySyncedData = array();
        foreach ($data as $key => $col) {
            $colClientID = $this->getArrayValue($col, "client_id");
            $colClientUniqueID = $this->getArrayValue($col, "client_unique_id");

            if (empty($colClientID)) {
                if ($returnItems && !empty($client)) {
                    $tempClientUniqueID = $client->unique_id;
                    $tempClientID = $client->id;
                    //Log::info('info', array("mData"=>print_r($colClientUniqueID.",".$tempClientUniqueID.",".$tempClientID,true)));
                    if ($colClientUniqueID == $tempClientUniqueID) {
                        $colClientID = $tempClientID;
                    } else {
                        continue;
                    }
                }
            }

            $colID = $this->getArrayValue($col, "collection_id");
            $colUniqueID = $this->getArrayValue($col, "unique_id");
            $images = $this->getArrayValue($col, "images");
            $imagePaths = $this->getArrayValue($col, "image_paths");
            $createdAt = $this->getArrayvalue($col, "created_at");
            $paymentStatus = $this->getArrayvalue($col, "payment_status","Pending");
            $paymentStatusNote = $this->getArrayvalue($col, "payment_status_note","N/A");

            $colTempArray["id"] = $colID;
            $colTempArray["unique_id"] = $colUniqueID;
            $colTempArray["company_id"] = $companyID;
            $colTempArray["employee_id"] = $employeeID;
            $colTempArray["client_id"] = $colClientID;
            $colTempArray["payment_received"] = $this->getArrayvalue($col, "payment_received");
            $colTempArray["payment_method"] = $this->getArrayvalue($col, "payment_method");
            $colTempArray["payment_status"] = $paymentStatus;
            $colTempArray["bank_id"] = $this->getArrayvalue($col, "bank_id");
            $colTempArray["cheque_no"] = $this->getArrayvalue($col, "cheque_no");
            $colTempArray["cheque_date"] = $this->getArrayvalue($col, "cheque_date");
            $colTempArray["due_payment"] = $this->getArrayvalue($col, "due_payment");
            $colTempArray["payment_note"] = $this->getArrayvalue($col, "payment_note");
            $colTempArray["payment_status_note"] = $paymentStatusNote;
            $colTempArray["payment_date"] = $this->getArrayvalue($col, "payment_date");
            $colTempArray["next_date"] = $this->getArrayvalue($col, "next_date");
            $colTempArray["created_at"] = $createdAt;

            //check if already exists
            $alreadyAddedCol = Collection::select('*')
                ->where('employee_id', $employeeID)
                ->where('company_id', $companyID)
                ->where('id', $colID)
                ->get()
                ->first();

            if (!empty($alreadyAddedCol)) {

                $alreadyAddedCol->payment_status = $paymentStatus;
                $alreadyAddedCol->payment_status_note = $paymentStatusNote;
                $alreadyAddedCol->save();

                $arrayAlreadyAddedCol = $alreadyAddedCol->toArray();

                array_push($arraySyncedData, $arrayAlreadyAddedCol);
                saveAdminNotification($companyID, $employeeID, date("Y-m-d H:i:s"), "Changed Cheque Status to ".$paymentStatus, "cheque", $alreadyAddedCol);


            } else {

                $savedID = DB::table('collections')->insertGetId($colTempArray);

                if (!empty($savedID)) {

                    $imageArray = array();
                    $tempImageNames = array();
                    $tempImagePaths = array();

                    //saving images
                    if (!empty($imagePaths)) {
                        $jsonDecoded = json_decode($images, true);
                        //Log::info('info', array("jsonDecoded"=>print_r($jsonDecoded,true)));
                        
                        foreach ($jsonDecoded as $key => $value) {
                            $tempImageName = $this->getImageName();
                            $tempImageDir = $this->getImagePath($companyID, "collection");
                            $tempImagePath = "/storage/app/public/" . $tempImageDir . "/" . $tempImageName;
                            $decodedData = base64_decode($value);
                            $put = \Storage::disk('public')->put($tempImageDir . '/' . $tempImageName, base64_decode($value));
                            array_push($tempImageNames, $tempImageName);
                            array_push($tempImagePaths, $tempImagePath);
                            $imageArray[$tempImageName] = $tempImagePath;
                        }

                        if (!empty($imageArray)) {
                            $imageData = array();
                            foreach ($imageArray as $imageName => $imagePath) {
                                $tempImageArray = array();
                                $tempImageArray["type"] = "collection";
                                $tempImageArray["type_id"] = $savedID;
                                $tempImageArray["company_id"] = $companyID;
                                $tempImageArray["employee_id"] = $employeeID;
                                $tempImageArray["image"] = $imageName;
                                $tempImageArray["image_path"] = $imagePath;
                                $tempImageArray["created_at"] = $createdAt;
                                array_push($imageData, $tempImageArray);
                            }
                            DB::table('images')->insert($imageData);
                        }
                    }

                    $colData = $colTempArray;
                    $colData["id"] = $savedID;
                    $colData["images"] = $tempImageNames;
                    $colData["image_paths"] = $tempImagePaths;
                    array_push($arraySyncedData, $colData);
                    saveAdminNotification($companyID, $employeeID, date("Y-m-d H:i:s"), "Added Collection", "collection", $colData);
                }
            }


            
        }//end foreach
        //Log::info('info', array("arraySyncedData"=>print_r($arraySyncedData,true)));

        return $returnItems ? $arraySyncedData : false;
    }

    public function saveCollection()
    {
        $postData = $this->getJsonRequest();
        //Log::info('info', array("data "=>print_r($postData,true)));
        $companyID = $this->getarrayValue($postData, "company_id");
        $employeeID = $this->getarrayValue($postData, "employee_id");
        $clientID = $this->getarrayValue($postData, "client_id");
        $images = $this->getArrayValue($postData, "images");
        $createdAt = $this->getArrayValue($postData, "created_at");

        $tempImageNames = array();
        $tempImagePaths = array();

        $imageArray = array();

        if (!empty($images)) {
            $jsonDecoded = json_decode($images, true);
            foreach ($jsonDecoded as $key => $value) {
                $tempImageName = $this->getImageName();
                $tempImageDir = $this->getImagePath($companyID, 'collection');
                $tempImagePath = "/storage/app/public/" . $tempImageDir . "/" . $tempImageName;
                //Log::info('info', array("tempImagePath"=>print_r($tempImagePath,true)));
                $decodedData = base64_decode($value);
                $put = \Storage::disk('public')->put($tempImageDir . '/' . $tempImageName, base64_decode($value));
                //$put = \File::put($tempImagePath, base64_decode($value));
                //Log::info('info', array("mPut"=>print_r($put,true)));
                array_push($tempImageNames, $tempImageName);
                array_push($tempImagePaths, $tempImagePath);
                $imageArray[$tempImageName] = $tempImagePath;
            }
        }

        $collectionData = array(

            'company_id' => $companyID,
            'employee_id' => $employeeID,
            'client_id' => $clientID,
            'unique_id'     =>$this->getArrayvalue($postData,"unique_id"),
            'payment_received' => $this->getArrayValue($postData, "payment_received"),
            'due_payment' => $this->getArrayValue($postData, "due_payment", 0),
            'payment_method' => $this->getArrayValue($postData, "payment_method", "N/A"),
            'payment_status' => $this->getArrayValue($postData, "payment_status", "Pending"),
            'bank_id' => $this->getArrayValue($postData, "bank_id", 0),
            'cheque_no' => $this->getArrayValue($postData, "cheque_no", ""),
            'cheque_date' => $this->getArrayValue($postData, "cheque_date", ""),
            'payment_note' => $this->getArrayValue($postData, "payment_note", "N/A"),
            'payment_status_note' => $this->getArrayValue($postData, "payment_status_note",""),
            //'images'         =>implode(",",$tempImageNames),
            //'image_paths'   =>implode(",",$tempImagePaths),
            'payment_date' => $this->getArrayValue($postData, "payment_date"),
            'next_date' => $this->getArrayValue($postData, "next_date"),
            'created_at' => $createdAt,
            'updated_at' => $this->getArrayValue($postData, "updated_at")
        );
        $savedID = DB::table('collections')->insertGetId($collectionData);

        if ($savedID) {
            //$nSaved = sendNotification($companyID,$employeeID,"Collection Added","",$createdAt);
            if (!empty($imageArray)) {
                $imageData = array();
                foreach ($imageArray as $imageName => $imagePath) {
                    $tempArray = array();
                    $tempArray["type"] = "collection";
                    $tempArray["type_id"] = $savedID;
                    $tempArray["company_id"] = $companyID;
                    $tempArray["employee_id"] = $employeeID;
                    $tempArray["image"] = $imageName;
                    $tempArray["image_path"] = $imagePath;
                    $tempArray["created_at"] = $this->getArrayValue($postData, "created_at");
                    array_push($imageData, $tempArray);
                }
                DB::table('images')->insert($imageData);
            }

            $collectionData['id'] = $savedID;
            $collectionData['images'] = json_encode($tempImageNames);
            $collectionData['image_paths'] = json_encode($tempImagePaths);
            $sent = saveAdminNotification($companyID, $employeeID, date("Y-m-d H:i:s"), "Added Collection", "collection", $collectionData);
        }

        $response = array("status" => true, "message" => "successfully saved", "data" => $collectionData);
        $this->sendResponse($response);
    }

    public function syncCollection()
    {
        $postData = $this->getJsonRequest();
        $arraySyncedData = $this->manageUnsyncedCollection($postData, true);
        $response = array("status" => true, "message" => "success", "data" => $arraySyncedData);
        $this->sendResponse($response);
    }


    /**
     * Meeting Related
     */


    public function fetchMeeting($return = false, $tempPostData = null)
    {

        $postData = $return ? $tempPostData : $this->getJsonRequest();

        $companyID = $this->getArrayValue($postData, "company_id");
        $employeeID = $this->getArrayValue($postData, "employee_id");

        /*Check if unsynced data is available . if available first update to tha database */
        $syncStatus = $this->manageUnsyncedMeeting($postData);

        /*$meetings = DB::table('meetings')
            ->select('meetings.*', 'images.image', 'images.image_path', 'clients.company_name')
            ->leftJoin('images', 'meetings.id', '=', 'images.type_id')
            ->leftJoin('clients', 'meetings.client_id', '=', 'clients.id')
            ->where('meetings.company_id', $companyID)
            ->where('meetings.employee_id', $employeeID)
            ->orderBy('created_at', 'desc')
            ->get();*/

        $meetings = DB::table('meetings')
            ->select('meetings.*', 'clients.company_name','employees.name as employee_name')
            ->leftJoin('employees','meetings.employee_id','employees.id')
            ->leftJoin('clients', 'meetings.client_id', '=', 'clients.id')
            ->where('meetings.company_id', $companyID)
            ->where('meetings.employee_id', $employeeID)
            ->orderBy('created_at', 'desc')
            ->get()->toArray();
        //Log::info('info', array("meetings"=>print_r($meetings,true)));

        if (empty($meetings)) {
            if ($return) {
                return array();
            } else {

                $this->sendEmptyResponse();
            }
        }

        $finalArray = array();
        foreach ($meetings as $key => $value) {
            $imageArray = getImageArray("notes", $value->id,$companyID,$employeeID);
            $value->images = json_encode($this->getArrayValue($imageArray, "images"));
            $value->image_paths = json_encode($this->getArrayValue($imageArray, "image_paths"));
            array_push($finalArray, $value);
        }

        $response = array("status" => true, "message" => "Success", "data" => $finalArray);

        if ($return) {
            return $meetings;
        } else {
            $this->sendResponse($response);
        }
    }

    private function manageUnsyncedMeeting($postData, $returnItems = false, $client = null)
    {

        $rawData = $this->getArrayValue($postData, "nonsynced_meeting");
        $companyID = $this->getArrayValue($postData, "company_id");
        $employeeID = $this->getArrayValue($postData, "employee_id");

        if (empty($rawData)) {
            return $returnItems ? array() : false;
        }
        $data = json_decode($rawData, true);
        //Log::info('info', array("meetingData Before Synced"=>print_r($data,true)));
        //Log::info('info', array("client"=>print_r($client,true)));
        $arraySyncedData = array();
        foreach ($data as $key => $meeting) {

            $meetingClientID = $this->getArrayValue($meeting, "client_id");
            $meetingClientUniqueID = $this->getArrayValue($meeting, "client_unique_id");

            if (empty($meetingClientID)) {
                if ($returnItems && !empty($client)) {
                    $tempClientUniqueID = $client->unique_id;
                    $tempClientID = $client->id;
                    //Log::info('info', array("mData"=>print_r($meetingClientUniqueID.",".$tempClientUniqueID.",".$tempClientID,true)));
                    if ($meetingClientUniqueID == $tempClientUniqueID) {
                        $meetingClientID = $tempClientID;
                    } else {
                        continue;
                    }
                }
            }

            $noteUniqueID = $this->getArrayValue($meeting, "unique_id");
            $images     = $this->getArrayValue($meeting,"images");
            $imagePaths = $this->getArrayValue($meeting,"image_paths");
            $createdAt = date('Y-m-d H:i:s');
            $tempMeetingArray["unique_id"] = $noteUniqueID;
            $tempMeetingArray["company_id"] = $companyID;
            $tempMeetingArray["employee_id"] = $employeeID;
            $tempMeetingArray["client_id"] = $meetingClientID;
            $tempMeetingArray["checkintime"] = $this->getArrayvalue($meeting, "checkintime");
            $tempMeetingArray["meetingdate"] = $this->getArrayvalue($meeting, "meetingdate");
            $tempMeetingArray["remark"] = $this->getArrayvalue($meeting, "remark");
            $tempMeetingArray["comm_medium"] = $this->getArrayvalue($meeting, "comm_medium");
            $tempMeetingArray["latitude"] = $this->getArrayvalue($meeting, "latitude");
            $tempMeetingArray["longitude"] = $this->getArrayvalue($meeting, "longitude");
            $tempMeetingArray["created_at"] = $createdAt;           

            //Check if already exists
            $alreadyAddedNote = DB::table('meetings')
            ->where('company_id',$companyID)
            ->where('employee_id',$employeeID)
            ->where('unique_id',$noteUniqueID)
            ->get()
            ->first();

            if(!empty($alreadyAddedNote)){
                // Do something


                // $arrayAlreadyAddedCol = $alreadyAddedCol->toArray();

                // array_push($arraySyncedData, $arrayAlreadyAddedCol);
                // saveAdminNotification($companyID, $employeeID, date("Y-m-d H:i:s"), "Changed Cheque Status to ".$paymentStatus, "cheque", $alreadyAddedCol);
            }else{
                $savedID = DB::table('meetings')->insertGetId($tempMeetingArray);
                if (!empty($savedID)) {

                  $imageArray = array();
                  $tempImageNames = array();
                  $tempImagePaths = array();

                  //saving images
                  if (!empty($imagePaths)) {
                      $jsonDecoded = json_decode($images, true);
                      //Log::info('info', array("jsonDecoded"=>print_r($jsonDecoded,true)));
                      
                      foreach ($jsonDecoded as $key => $value) {
                          $tempImageName = $this->getImageName();
                          $tempImageDir = $this->getImagePath($companyID, "notes");
                          $tempImagePath = "/storage/app/public/" . $tempImageDir . "/" . $tempImageName;
                          $decodedData = base64_decode($value);
                          $put = \Storage::disk('public')->put($tempImageDir . '/' . $tempImageName, base64_decode($value));
                          array_push($tempImageNames, $tempImageName);
                          array_push($tempImagePaths, $tempImagePath);
                          $imageArray[$tempImageName] = $tempImagePath;
                      }

                      if (!empty($imageArray)) {
                          $imageData = array();
                          foreach ($imageArray as $imageName => $imagePath) {
                              $tempImageArray = array();
                              $tempImageArray["type"] = "notes";
                              $tempImageArray["type_id"] = $savedID;
                              $tempImageArray["company_id"] = $companyID;
                              $tempImageArray["employee_id"] = $employeeID;
                              $tempImageArray["image"] = $imageName;
                              $tempImageArray["image_path"] = $imagePath;
                              $tempImageArray["created_at"] = $createdAt;
                              array_push($imageData, $tempImageArray);
                          }
                          DB::table('images')->insert($imageData);
                      }
                  }
                  $meetingData = $tempMeetingArray;
                  $meetingData["id"] = $savedID;
                  $meetingData["images"] = $tempImageNames;
                  $meetingData["image_paths"] = $tempImagePaths;
                  array_push($arraySyncedData, $meetingData);
                  saveAdminNotification($companyID, $employeeID, date("Y-m-d H:i:s"), "Added Note", "notes", $meetingData);
                }
            }

        }
        return $returnItems ? $arraySyncedData : false;
    }

    public function saveMeeting($postData = null)
    {
        $postData = $this->getJsonRequest();
        $companyID = $this->getarrayValue($postData, "company_id");
        $employeeID = $this->getarrayValue($postData, "employee_id");
        $clientID = $this->getarrayValue($postData, "client_id");
        $images = $this->getArrayValue($postData, "images");
        $audio = $this->getArrayValue($postData, "audio");
        $createdAt = $this->getArrayValue($postData, "created_at");

        $tempImageNames = array();
        $tempImagePaths = array();
        $tempAudioPath = Null;
        if (!empty($images)) {
            foreach ($images as $key => $value) {
                $tempImageName = $this->getImageName();
                $tempImageDir = $this->getImagePath($companyID, 'notes');
                $tempImagePath = "/storage/app/public/" . $tempImageDir . "/" . $tempImageName;
                $put = \Storage::disk('public')->put($tempImageDir . '/' . $tempImageName, base64_decode($value));
                array_push($tempImageNames, $tempImageName);
                array_push($tempImagePaths, $tempImagePath);
                $imageArray[$tempImageName] = $tempImagePath;
            }
        }

        if(!empty($audio)){
            $tempAudioName = md5(uniqid(mt_rand(), true)).'.mp3';
            $tempAudioDir = $this->getImagePath($companyID, "notes");
            //Log::info('info', array("audio dir"=>print_r($tempAudioDir,true)));
            //Log::info('info', array("audio dir"=>print_r($tempAudioName,true)));
            $tempAudioPath = $tempAudioDir . "/" . $tempAudioName;
            $put = \Storage::disk('public')->put($tempAudioDir . '/' . $tempAudioName, base64_decode($audio));
        }

        $meetingData = array(

            'company_id' => $companyID,
            'employee_id' => $employeeID,
            'client_id' => $clientID,
            'checkintime' => $this->getArrayValue($postData, "checkintime"),
            'meetingdate' => $this->getArrayValue($postData, "meetingdate"),
            'remark' => $this->getArrayValue($postData, "remark", "N/A"),
            'latitude' => $this->getArrayValue($postData, "latitude"),
            'longitude' => $this->getArrayValue($postData, "longitude"),
            'audio_note' =>$tempAudioPath,
            'created_at' => $createdAt,
            'updated_at' => $this->getArrayValue($postData, "updated_at")
        );
        $savedID = DB::table('meetings')->insertGetId($meetingData);

        if ($savedID) {
            $meetingData["id"] = $savedID;

            if (!empty($imageArray)) {
                $imageData = array();
                foreach ($imageArray as $imageName => $imagePath) {
                    $tempArray = array();
                    $tempArray["type"] = "notes";
                    $tempArray["type_id"] = $savedID;
                    $tempArray["company_id"] = $companyID;
                    $tempArray["employee_id"] = $employeeID;
                    $tempArray["image"] = $imageName;
                    $tempArray["image_path"] = $imagePath;
                    $tempArray["created_at"] = $this->getArrayValue($postData, "created_at");
                    array_push($imageData, $tempArray);
                }
                DB::table('images')->insert($imageData);
            }
            if(!empty($images)){
                $meetingData['images'] = json_encode($tempImageNames);
                $meetingData['image_paths'] = json_encode($tempImagePaths);                
            }
            $notificationData = array(
                "company_id" => $companyID,
                "employee_id" => $employeeID,
                "title" => "Note Added",
                "description" => "",
                "created_at" => $createdAt,
                "status" => 1,
                "to" => 0
            );
            $nSaved = saveAdminNotification($companyID, $employeeID, date("Y-m-d H:i:s"), "Added Note", "notes", $meetingData);
        }
        $response = array("status" => true, "message" => "successfully saved", "data" => $meetingData);
        $this->sendResponse($response);
    }

    public function syncMeeting()
    {

        $postData = $this->getJsonRequest();
        $arraySyncedData = $this->manageUnsyncedMeeting($postData, true);
        $response = array("status" => true, "message" => "success", "data" => $arraySyncedData);
        $this->sendResponse($response);
    }


    /**
     * Order Related
     */

    public function fetchOrders($return = false, $tempPostData = null)
    {

        $postData = $return ? $tempPostData : $this->getJsonRequest();
        $companyID = $this->getArrayValue($postData, "company_id");
        $employeeID = $this->getArrayValue($postData, "employee_id");
        $employeeName = $this->getArrayValue($postData, "employee_name");

        /*Check if unsynced data is available . if available first update to tha database */
        $syncStatus = $this->manageUnsyncedOrder($postData);


        $orders = DB::table('orders')
            ->select('orders.*', 'clients.name', 'clients.company_name')
            ->join('clients', 'clients.id', '=', 'orders.client_id')
            ->where('orders.company_id', $companyID)
            ->where('orders.employee_id', $employeeID)
            ->get()->toArray();
        // $orders = DB::table('orders')
        //     ->select('orders.*', 'clients.name', 'clients.company_name','module_attributes.color as delivery_status_color')
        //     ->join('clients', 'clients.id', '=', 'orders.client_id')
        //     ->leftJoin('module_attributes','orders.delivery_status','module_attributes.title')
        //     ->where('orders.company_id', $companyID)
        //     ->where('orders.employee_id', $employeeID)
        //     ->get()->toArray();

        if (empty($orders)) {
            if ($return) {
                return array();
            } else {

                $this->sendEmptyResponse();
            }
        }

        $finalArray = array();
        $moduleAttributes =  ModuleAttribute::where('company_id', $companyID)->get();
        foreach ($orders as $key => $value) {

            $productLines = $this->getProductLines($value->id);
            $taxes = $this->getTaxes($value->id);
            $value->orderproducts = $productLines;
            $value->taxes = $taxes;
            if($value->delivery_status){
                $delivery_status_color = $moduleAttributes->where('title', '=',$value->delivery_status)->first();
                if($delivery_status_color){
                    $value->delivery_status_color = $delivery_status_color->color;    
                }else{
                    $value->delivery_status_color = NULL;
                }
            }else{
                $value->delivery_status_color = NULL;
            }
            array_push($finalArray, $value);
        }
        $response = array("status" => true, "message" => "Success", "data" => $finalArray);
        if ($return) {
            return $finalArray;
        } else {
            $this->sendResponse($response);
        }

    }

    private function manageUnsyncedOrder($postData, $returnItems = false, $client = null)
    {

        $rawData = $this->getArrayValue($postData, "nonsynced_orders");
        $companyID = $this->getArrayValue($postData, "company_id");
        $employeeID = $this->getArrayValue($postData, "employee_id");
        $createdAt = $this->getArrayValue($postData, "created_at");
        $employeeName = $this->getArrayValue($postData, "employee_name");


        if (empty($rawData)) return $returnItems ? array() : false;

        $data = json_decode($rawData, true);

        $arraySyncedData = array();
        foreach ($data as $key => $order) {

            $orderClientID = $this->getArrayValue($order, "client_id");
            $orderClientUniqueID = $this->getArrayValue($order, "client_unique_id");

            if (empty($orderClientID)) {
                if ($returnItems && !empty($client)) {
                    $tempClientUniqueID = $client->unique_id;
                    $tempClientID = $client->id;
                    if ($orderClientUniqueID == $tempClientUniqueID) {
                        $orderClientID = $tempClientID;
                    } else {
                        continue;
                    }
                }
            }

            $orderNumber = getOrderNo($companyID);
            $orderUniqueID = $this->getArrayvalue($order, "unique_id");
            $tempOrderArray["unique_id"] = $orderUniqueID;
            $tempOrderArray["company_id"] = $companyID;
            $tempOrderArray["employee_id"] = $employeeID;
            $tempOrderArray["client_id"] = $orderClientID;
            $tempOrderArray["order_no"] = $orderNumber;
            $tempOrderArray["order_date"] = $this->getArrayvalue($order, "order_date");
            $tempOrderArray["tot_amount"] = $this->getArrayvalue($order, "tot_amount");
            $tempOrderArray["tax"] = $this->getArrayvalue($order, "tax");
            $tempOrderArray["discount"] = $this->getArrayvalue($order, "discount");
            $tempOrderArray["discount_type"] = $this->getArrayvalue($order, "discount_type");
            $tempOrderArray["grand_total"] = $this->getArrayvalue($order, "grand_total");
            $tempOrderArray["payment_received"] = $this->getArrayvalue($order, "payment_received");
            $tempOrderArray["due_payment"] = $this->getArrayvalue($order, "due_payment");
            $tempOrderArray["payment_method"] = $this->getArrayvalue($order, "payment_method");
            $tempOrderArray["delivery_status"] = $this->getArrayvalue($order, "delivery_status");
            $tempOrderArray["order_note"] = $this->getArrayvalue($order, "order_note");
            $tempOrderArray["order_date"] = $this->getArrayvalue($order, "order_date");
            $tempOrderArray["created_at"] = $this->getArrayvalue($order, "created_at");

            //check if already exists
            $alreadyAddedOrder = Order::select('*')
                ->where('employee_id', $employeeID)
                ->where('company_id', $companyID)
                ->where('unique_id', $orderUniqueID)
                ->get()
                ->first();

            if (!empty($alreadyAddedOrder)) {
                array_push($arraySyncedData, $alreadyAddedOrder->toArray());
                continue;
            }

            $orderID = DB::table('orders')->insertGetId($tempOrderArray);

            //Saving child Data
            if (!empty($orderID)) {

                $orderData = $tempOrderArray;
                $orderData["id"] = $orderID;
                array_push($arraySyncedData, $orderData);
                saveAdminNotification($companyID, $employeeID, date("Y-m-d H:i:s"), "Added Order", "order", $orderData);

                //Saving taxes 
                $taxes = $this->getArrayValue($order, "taxes");
                if (!empty($taxes)) {
                    $taxes = json_decode($taxes, true);
                    $taxOnOrderArray = array();
                    foreach ($taxes as $tax) {
                        $tempArrayTax = array(
                            'order_id' => $orderID,
                            'tax_name' => $this->getArrayValue($tax, "tax_name"),
                            'tax_percent' => $this->getArrayValue($tax, "tax_percent")
                        );
                        array_push($taxOnOrderArray, $tempArrayTax);
                    }
                    DB::table('tax_on_orders')->insert($taxOnOrderArray);
                }

                //Saving OrderProducts
                $orderProducts = $this->getArrayValue($order, "orderproducts");
                if (!empty($orderProducts)) {
                    $op = json_decode($orderProducts, true);
                    if (is_array($op) && !empty($op)) {
                        $opFinalArray = array();
                        foreach ($op as $k => $v) {
                            $opTemp = array();
                            $opTemp["order_id"] = $orderID;
                            $opTemp["product_id"] = $this->getArrayValue($v, "product_id");
                            $opTemp["product_name"] = $this->getArrayValue($v, "product_name");
                            $opTemp["short_desc"] = $this->getArrayValue($v, "short_desc");
                            $opTemp["product_variant_id"] = $this->getArrayValue($v, "product_variant_id");
                            $opTemp["product_variant_name"] = $this->getArrayValue($v, "product_variant_name");
                            $opTemp["mrp"] = $this->getArrayValue($v, "mrp");
                            $opTemp["unit"] = $this->getArrayValue($v, "unit");
                            $opTemp["unit_name"] = $this->getArrayValue($v, "unit_name");
                            $opTemp["unit_symbol"] = $this->getArrayValue($v, "unit_symbol");
                            $opTemp["rate"] = $this->getArrayValue($v, "rate");
                            $opTemp["quantity"] = $this->getArrayValue($v, "quantity");
                            $opTemp["amount"] = $this->getArrayValue($v, "ptotal_amt");
                            $opTemp["pdiscount"] = $this->getArrayValue($v, "pdiscount");
                            $opTemp["pdiscount_type"] = $this->getArrayValue($v, "pdiscount_type");
                            $opTemp["ptotal_amt"] = $this->getArrayValue($v, "ptotal_amt");
                            $opTemp["created_at"] = $this->getArrayValue($v, "created_at");
                            array_push($opFinalArray, $opTemp);
                        }
                        //Log::info('info', array("opFinalArray "=>print_r($opFinalArray,true)));

                        $batchSaved = DB::table('orderproducts')->insert($opFinalArray);
                    }
                }
            }
        }

        //Log::info('info', array("arraySyncedData"=>print_r($arraySyncedData,true)));

        return $returnItems ? $arraySyncedData : true;
    }

    private function getProductLines($orderID)
    {

        if (empty($orderID)) return null;

        $orderProducts = DB::table('orderproducts')
            ->select('orderproducts.*', 'products.product_name', 'products.short_desc')
            ->join('products', 'products.id', '=', 'orderproducts.product_id')
            ->where('order_id', $orderID)
            ->get()->toArray();
        return empty($orderProducts) ? null : json_encode($orderProducts);
    }

    private function getTaxes($orderID)
    {

        if (empty($orderID)) return null;

        $taxes = DB::table('tax_on_orders')->where('order_id', $orderID)->get()->toArray();
        return empty($taxes) ? null : json_encode($taxes);
    }

    public function saveOrder()
    {

        $postData = $this->getJsonRequest(); //Log::info('info', array("postDataOfOrder"=>print_r($postData,true)));
        $orderProducts = $this->getArrayValue($postData, "orderproducts");
        $companyID = $this->getArrayValue($postData, "company_id");
        $employeeID = $this->getArrayValue($postData, "employee_id");
        $employeeName = $this->getArrayValue($postData, "employee_name");
        $taxes = $this->getArrayValue($postData, "taxes"); //Log::info('info', array("taxes"=>print_r($taxes,true)));
        $orderNumber = getOrderNo($companyID);
        $createdAt = $this->getArrayValue($postData, "created_at");

        $orderData = array(

            'unique_id' => $this->getArrayValue($postData, "unique_id"),
            'company_id' => $companyID,
            'employee_id' => $this->getArrayValue($postData, "employee_id"),
            'client_id' => $this->getArrayvalue($postData, "client_id"),
            'order_no' => $orderNumber,
            'tot_amount' => $this->getArrayValue($postData, "tot_amount"),
            'tax' => $this->getArrayValue($postData, "tax"),
            'discount' => $this->getArrayValue($postData, "discount"),
            'discount_type' => $this->getArrayValue($postData, "discount_type"),
            'grand_total' => $this->getArrayValue($postData, "grand_total"),
            'payment_received' => $this->getArrayValue($postData, "payment_received"),
            'due_payment' => $this->getArrayValue($postData, "due_payment"),
            'payment_method' => $this->getArrayValue($postData, "payment_method"),
            'delivery_status' => $this->getArrayValue($postData, "delivery_status"),
            'order_note' => $this->getArrayValue($postData, "order_note"),
            'order_date' => $this->getArrayvalue($postData, "order_date"),
            'cancel_note' => $this->getArrayValue($postData, "cancel_note"),
            'created_at' => $createdAt,
        );
        //Log::info('info', array("orderData"=>print_r($orderData,true)));
        $orderID = DB::table('orders')->insertGetId($orderData);
        $empUserID = Employee::where('company_id', $companyID)->where('id', $employeeID)->first(['user_id']);
        activity()->log('Created Order',  $empUserID->user_id);
        $orderData['id'] = $orderID;
        $orderData['color'] = getColor('Pending')['color'];
        $savedOrder = $orderData;

        if (!empty($orderID)) {
            if (!empty($taxes)) {
                $taxes = json_decode($taxes);
                $tempArray = [];
                foreach ($taxes as $tax) {
                    $tempArray[] = [
                        'order_id' => $orderID,
                        'tax_name' => $tax->tax_name,
                        'tax_percent' => $tax->tax_percent
                    ];
                }
                DB::table('tax_on_orders')->insert($tempArray);
            }


            if (!empty($orderProducts)) {

                $opArray = json_decode($orderProducts, true);
                if (!empty($opArray) && is_array($opArray)) {
                    $opFinalArray = array();
                    foreach ($opArray as $key => $v) {
                        $temp = array();
                        $temp["order_id"] = $orderID;
                        $temp["product_id"] = $this->getArrayValue($v, "product_id");
                        $temp["product_name"] = $this->getArrayValue($v, "product_name");
                        $temp["short_desc"] = $this->getArrayValue($v, "short_desc");
                        $temp["product_variant_id"] = $this->getArrayValue($v, "product_variant_id");
                        $temp["product_variant_name"] = $this->getArrayValue($v, "product_variant_name");
                        $temp["mrp"] = $this->getArrayValue($v, "mrp");
                        $temp["unit"] = $this->getArrayValue($v, "unit");
                        $temp["unit_name"] = $this->getArrayValue($v, "unit_name");
                        $temp["unit_symbol"] = $this->getArrayValue($v, "unit_symbol");
                        $temp["rate"] = $this->getArrayValue($v, "rate");
                        $temp["quantity"] = $this->getArrayValue($v, "quantity");
                        $temp["amount"] = $this->getArrayValue($v, "ptotal_amt");
                        $temp["pdiscount"] = $this->getArrayValue($v, "pdiscount");
                        $temp["pdiscount_type"] = $this->getArrayValue($v, "pdiscount_type");
                        $temp["ptotal_amt"] = $this->getArrayValue($v, "ptotal_amt");
                        $temp["created_at"] = $createdAt;
                        array_push($opFinalArray, $temp);
                    }

                    $batchSaved = DB::table('orderproducts')->insert($opFinalArray);

                    if ($batchSaved) {
                        if ($orderID) {
                            $notificationData = array(
                                "company_id" => $this->getArrayValue($postData, "company_id"),
                                "employee_id" => $this->getArrayValue($postData, "employee_id"),
                                "title" => "Order Added",
                                "description" => "Order No: " . $orderNumber,
                                "created_at" => $createdAt,
                                "status" => 1,
                                "to" => 0
                            );
                            $nSaved = saveAdminNotification($companyID, $employeeID, date("Y-m-d H:i:s"), "Added Order", "order", $orderData);

                        }
                    }
                }
            }
        }

        $status = (!empty($orderID) && !empty($batchSaved)) ? true : false;

        $response = array("status" => true, "message" => "successfully saved", "data" => $savedOrder);
        $this->sendResponse($response);
    }

    public function syncOrders()
    {

        $postData = $this->getJsonRequest();
        $arraySyncedData = $this->manageUnsyncedOrder($postData, true);
        $response = array("status" => true, "message" => "success", "data" => $arraySyncedData);
        $this->sendResponse($response);

    }

    public function fetchNoOrder($return = false, $tempPostData = null)
    {

        $postData = $return ? $tempPostData : $this->getJsonRequest();
        $companyID = $this->getArrayValue($postData, "company_id");
        $employeeID = $this->getArrayValue($postData, "employee_id");

        //Check if unsynced data is available . if available first update to tha database
        $syncStatus = $this->manageUnsyncedNoOrder($postData);


        $noOrders = DB::table('no_orders')
            ->select('no_orders.*', 'clients.name', 'clients.company_name')
            ->join('clients', 'clients.id', '=', 'no_orders.client_id')
            ->where('no_orders.company_id', $companyID)
            ->where('no_orders.employee_id', $employeeID)
            ->get()->toArray();


        if (empty($noOrders)) {
            if ($return) {
                return array();
            } else {

                $this->sendEmptyResponse();
            }
        }
        //Log::info('info', array("noOrders" => print_r($noOrders, true)));
        $response = array("status" => true, "message" => "Success", "data" => $noOrders);
        if ($return) {
            return $noOrders;
        } else {
            $this->sendResponse($response);
        }

    }

    public function saveNoOrder()
    {

        $postData = $this->getJsonRequest();
        $companyID = $this->getArrayValue($postData, "company_id");
        $employeeID = $this->getArrayValue($postData, "employee_id");
        $employeeName = $this->getArrayValue($postData, "employee_name");
        $dateTime = $this->getArrayValue($postData, "datetime");

        $NoOrderData = array(

            'unique_id' => $this->getArrayValue($postData, "unique_id"),
            'company_id' => $companyID,
            'employee_id' => $this->getArrayValue($postData, "employee_id"),
            'client_id' => $this->getArrayvalue($postData, "client_id"),
            'remark' => $this->getArrayValue($postData, "remark"),
            'datetime' => $dateTime,
            'date' => $this->getArrayvalue($postData, "date"),
            'unix_timestamp' => $this->getArrayvalue($postData, "unix_timestamp"),
            'created_at' => $this->getArrayvalue($postData, "datetime"),
        );

        $noOrderID = DB::table('no_orders')->insertGetId($NoOrderData);
        $NoOrderData['id'] = $noOrderID;
        saveAdminNotification($companyID, $employeeID, date("Y-m-d H:i:s"), "Added No Order", "noorders", $NoOrderData);
        $response = array("status" => true, "message" => "successfully saved", "data" => $NoOrderData);
        $this->sendResponse($response);
    }

    public function syncNoOrder()
    {

        $postData = $this->getJsonRequest();
        //Log::info('info', array("postData syncNoOrder" => print_r($postData, true)));
        $arraySyncedData = $this->manageUnsyncedNoOrder($postData, true);
        $response = array("status" => true, "message" => "success", "data" => $arraySyncedData);
        $this->sendResponse($response);
    }

    private function manageUnsyncedNoOrder($postData, $returnItems = false, $client = null)
    {

        $rawData = $this->getArrayValue($postData, "nonsynced_no_orders");
        $companyID = $this->getArrayValue($postData, "company_id");
        $employeeID = $this->getArrayValue($postData, "employee_id");
        $employeeName = $this->getArrayValue($postData, "employee_name");


        if (empty($rawData)) return $returnItems ? array() : false;

        $data = json_decode($rawData, true);

        $arraySyncedData = array();
        foreach ($data as $key => $noOrder) {

            $noOrderClientID = $this->getArrayValue($noOrder, "client_id");
            $noOrderClientUniqueID = $this->getArrayValue($noOrder, "client_unique_id");

            if (empty($noOrderClientID)) {

                if ($returnItems && !empty($client)) {

                    $tempClientUniqueID = $client->unique_id;
                    $tempClientID = $client->id;
                    if ($noOrderClientUniqueID == $tempClientUniqueID) {
                        $noOrderClientID = $tempClientID;
                    } else {
                        continue;
                    }
                }
            }

            $dateTime = $this->getArrayvalue($noOrder, "datetime");

            $noOrderUniqueID = $this->getArrayvalue($noOrder, "unique_id");
            $tempNoOrderArray["unique_id"] = $noOrderUniqueID;
            $tempNoOrderArray["company_id"] = $companyID;
            $tempNoOrderArray["employee_id"] = $employeeID;
            $tempNoOrderArray["client_id"] = $noOrderClientID;
            $tempNoOrderArray["remark"] = $this->getArrayValue($noOrder, "remark");
            $tempNoOrderArray["date"] = $this->getArrayvalue($noOrder, "date");
            $tempNoOrderArray["datetime"] = $dateTime;
            $tempNoOrderArray["created_at"] = $this->getArrayvalue($noOrder, "datetime");
            $tempNoOrderArray["unix_timestamp"] = $this->getArrayvalue($noOrder, "unix_timestamp");

            //check if already exists
            $alreadyAddedNoOrder = NoOrder::select('*')
                ->where('employee_id', $employeeID)
                ->where('company_id', $companyID)
                ->where('unique_id', $noOrderUniqueID)
                ->get()
                ->first();

            if (!empty($alreadyAddedNoOrder)) {
                array_push($arraySyncedData, $alreadyAddedNoOrder->toArray());
                continue;
            }

            $noOrderID = DB::table('no_orders')->insertGetId($tempNoOrderArray);
            if (!empty($noOrderID)) {

                $orderData = $tempNoOrderArray;
                $orderData["id"] = $noOrderID;
                array_push($arraySyncedData, $orderData);
                saveAdminNotification($companyID, $employeeID, date("Y-m-d H:i:s"), "Added No Order", "noorders", $orderData);
            }
        }

        return $returnItems ? $arraySyncedData : true;
    }


    /**
     * Leave Related
     */

     public function fetchLeaveTypes($return = false, $tempPostData = null)
    {

        $postData = $return ? $tempPostData : $this->getJsonRequest();

        $companyID = $this->getArrayValue($postData, "company_id");
        $leaveTypes = DB::table('leave_type')->where(
            array(
                array("company_id", "=", $companyID)
            )
        )->get()->toArray();

        if (empty($leaveTypes)) {
            if ($return) {
                return array();
            } else {

                $this->sendEmptyResponse();
            }
        }

        $response = array("status" => true, "message" => "Success", "data" => $leaveTypes);

        if ($return) {
            return $leaveTypes;
        } else {
            $this->sendResponse($response);
        }
    }

    public function fetchLeave($return = false, $tempPostData = null)
    {

        $postData = $return ? $tempPostData : $this->getJsonRequest();

        $companyID = $this->getArrayValue($postData, "company_id");
        $employeeID = $this->getArrayValue($postData, "employee_id");

        /*Check if unsynced data is available . if available first update to tha database */
        $syncStatus = $this->manageUnsyncedLeave($postData);

        $leaves = DB::table('leaves')->where(
            array(
                array("company_id", "=", $companyID),
                array("employee_id", "=", $employeeID)
            )
        )->get()->toArray();

        if (empty($leaves)) {
            if ($return) {
                return array();
            } else {

                $this->sendEmptyResponse();
            }
        }

        $response = array("status" => true, "message" => "Success", "data" => $leaves);

        if ($return) {
            return $leaves;
        } else {
            $this->sendResponse($response);
        }
    }

    private function manageUnsyncedLeave($postData, $returnItems = false)
    {

        $rawData = $this->getArrayValue($postData, "unsynced_data");
        $companyID = $this->getArrayValue($postData, "company_id");
        $employeeID = $this->getArrayValue($postData, "employee_id");

        if (empty($rawData)) {

            return $returnItems ? array() : false;
        }
        $data = json_decode($rawData, true);

        $arraySyncedData = array();

        /*prepare data for saving*/
        foreach ($data as $key => $value) {

            $createdAt = $this->getArrayvalue($value, "created_at");
            $tempArray["unique_id"] = $this->getArrayValue($value, "unique_id");
            $tempArray["company_id"] = $companyID;
            $tempArray["employee_id"] = $employeeID;
            $tempArray["leavetype"] = $this->getArrayvalue($value, "leavetype");;
            $tempArray["start_date"] = $this->getArrayvalue($value, "start_date");
            $tempArray["end_date"] = $this->getArrayvalue($value, "end_date");
            $tempArray["leave_desc"] = $this->getArrayvalue($value, "leave_desc");
            $tempArray["remarks"] = $this->getArrayvalue($value, "remark");
            $tempArray["status"] = $this->getArrayvalue($value, "status");
            $tempArray["status_reason"] = $this->getArrayvalue($value, "status");
            $tempArray["created_at"] = $createdAt;

            $savedID = DB::table('leaves')->insertGetId($tempArray);

            if (!empty($savedID)) {
                $syncedData = $tempArray;
                $syncedData['id'] = $savedID;
                array_push($arraySyncedData, $syncedData);
                saveAdminNotification($companyID, $employeeID, date("Y-m-d H:i:s"), "Applied For Leave", "leave", $syncedData);
            }
        }

        return $returnItems ? $arraySyncedData : true;
    }

    public function saveLeave()
    {
        //Log::info('info', array("saveLeave"=>print_r("Inside saveLeave",true)));
        $postData = $this->getJsonRequest();
        $companyID = $this->getArrayValue($postData, "company_id");
        $employeeID = $this->getArrayValue($postData, "employee_id");
        $createdAt = $this->getArrayValue($postData, "created_at");
        $uniqueID = $this->getArrayValue($postData,"unique_id");
        $leaveID = $this->getArrayValue($postData,"leave_id");

        $leaveData = array(

            'unique_id' => $uniqueID,
            'company_id' => $this->getArrayValue($postData, "company_id"),
            'employee_id' => $this->getArrayValue($postData, "employee_id"),
            'start_date' => $this->getArrayvalue($postData, "start_date"),
            'end_date' => $this->getArrayValue($postData, "end_date"),
            'leavetype' => $this->getArrayValue($postData, "leavetype"),
            'leave_desc' => $this->getArrayValue($postData, "leave_desc"),
            'status' => $this->getArrayValue($postData, "status"),
            'created_at' => $this->getArrayValue($postData, "created_at")
        );

        if(!empty($uniqueID)){
            
            $leave = Leave::updateOrCreate(
                [
                    "unique_id" => $uniqueID
                ],
                $leaveData
            );

        } elseif(!empty($leaveID)){

            $leave = Leave::updateOrCreate(
                [
                    "id" => $leaveID
                ],
                $leaveData
            );
        }
        
        $wasRecentlyCreated = $leave->wasRecentlyCreated;
        $wasChanged = $leave->wasChanged();
        $isDirty = $leave->isDirty();
        $exists = $leave->exists;
        //Log::info('info', array("leave flags"=>print_r("wasRecentlyCreated/wasChanged/isDirty/exists:".$wasRecentlyCreated." ,".$wasChanged." ,".$isDirty." ,".$exists,true)));

        if ($wasRecentlyCreated || $wasChanged || $leave->exists) {

            $msg = "";
            $savedLeave = $leaveData;
            $savedLeave["id"] = $leave->id;
            
            if ($leave->wasRecentlyCreated) {
                
                $msg = "Applied For Leave";
            } else {

                $msg = "Updated Leave";

            }

            $nSaved = saveAdminNotification($companyID, $employeeID, date("Y-m-d H:i:s"), $msg, "leave", $savedLeave);
            $response = array("status" => true, "message" => "successfully saved", "leave_id" => $leave->id);
            $this->sendResponse($response);
        } else {

            $this->sendEmptyResponse();
        }

    }

    public function deleteLeave(){

        $postData = $this->getJsonRequest();
        //Log::info('info', array("postData"=>print_r($postData,true)));
        $leaveID = getArrayValue($postData,"leave_id");
        $companyID = getArrayValue($postData,"company_id");
        $employeeID = getArrayValue($postData,"employee_id");
        $leave = Leave::find(getArrayValue($postData,"leave_id"));

        $response = array("status" => false, "message" => "Delete Fail");

        if(!empty($leave)){

            $deleted = $leave->delete();
            if($deleted){

                $nSaved = saveAdminNotification($companyID, $employeeID, date("Y-m-d H:i:s"), "Cancelled Leave", "leave", $leave);
                $response = array("status" => true, "message" => "successfully Deleted", "leave_id" => $leave->id);
            } else {

                $response = array("status" => true, "message" => "Delete Fail", "leave_id" => $leave->id);

            }
        }

        $this->sendResponse($response);
    }

    public function syncLeave()
    {

        $postData = $this->getJsonRequest();
        $arraySyncedLeave = $this->manageUnsyncedLeave($postData, true);
        $response = array("status" => true, "message" => "success", "data" => $arraySyncedLeave);

        $this->sendResponse($response);
    }




    /**
     * Expence Related
     */


    /**
     * @param bool $return
     * @param null $tempPostData
     * @return array
     */
    public function fetchExpense($return = false, $tempPostData = null)
    {

        $postData = $return ? $tempPostData : $this->getJsonRequest();
        $companyID = $this->getArrayValue($postData, "company_id");
        $employeeID = $this->getArrayValue($postData, "employee_id");

        /*Check if unsynced data is available . if available first update to tha database */
        $syncStatus = $this->manageUnsyncedExpense($postData);

        $expenses = DB::table('expenses')
            ->select('expenses.*', 'clients.company_name')
            ->leftJoin('clients', 'expenses.client_id', '=', 'clients.id')
            ->where('expenses.company_id', $companyID)
            ->where('expenses.employee_id', $employeeID)
            ->get()->toArray();

        if (empty($expenses)) {
            if ($return) {
                return array();
            } else {

                $this->sendEmptyResponse();
            }
        }

        $finalArray = array();
        foreach ($expenses as $key => $value) {
            $imageArray = getImageArray("expense", $value->id,$companyID,$employeeID);
            $value->images = json_encode($this->getArrayValue($imageArray, "images"));
            $value->image_paths = json_encode($this->getArrayValue($imageArray, "image_paths"));
            array_push($finalArray, $value);
        }
        $response = array("status" => true, "message" => "Success", "data" => $finalArray);

        if ($return) {
            return $finalArray;
        } else {
            $this->sendResponse($response);
        }
    }

    private function manageUnsyncedExpense($postData, $returnItems = false, $client = null)
    {
        //Log::info('info', array("inside manageUnsyncedExpense"=>print_r($postData,true)));

        $rawData = $this->getArrayValue($postData, "nonsynced_expense");
        $companyID = $this->getArrayValue($postData, "company_id");
        $employeeID = $this->getArrayValue($postData, "employee_id");
        //$clientID   = $this->getArrayValue($postData,"client_id");

        if (empty($rawData)) {
            return $returnItems ? array() : false;
        }

        $data = json_decode($rawData, true);
        $arraySyncedData = array();
        /*prepare data for saving*/
        foreach ($data as $key => $expense) {

            $expenseClientID = $this->getArrayValue($expense, "client_id");
            $expenseClientUniqueID = $this->getArrayValue($expense, "client_unique_id");

            if (empty($expenseClientID)) {
                if ($returnItems && !empty($client)) {
                    $tempClientUniqueID = $client->unique_id;
                    $tempClientID = $client->id;
                    if ($expenseClientUniqueID == $tempClientUniqueID) {
                        $expenseClientID = $tempClientID;
                    } else {
                        continue;
                    }
                }
            }

            $createdAt = date('Y-m-d H:i:s');
            $images = $this->getArrayValue($expense, "images");
            $imagePaths = $this->getArrayValue($expense, "image_paths");

            $expenseData = array(
                'unique_id' => $this->getArrayValue($expense, "unique_id"),
                'company_id' => $companyID,
                'employee_id' => $employeeID,
                'employee_type' => 'Employee',
                'client_id' => $expenseClientID,
                'amount' => $this->getArrayvalue($expense, "amount"),
                'description' => $this->getArrayValue($expense, "description"),
                'approved_by' => $this->getArrayValue($expense, "approved_by"),
                'remark' => $this->getArrayValue($expense, "remark"),
                'status' => $this->getArrayValue($expense, "status"),
                'created_at' => $createdAt,
                'updated_at' => $this->getArrayValue($expense, "updated_at")
            );

            $savedID = DB::table('expenses')->insertGetId($expenseData);


            if (!empty($savedID)) {

                //saving images
                if (!empty($imagePaths)) {
                    $jsonDecoded = json_decode($images, true);
                    $imageArray = array();
                    $tempImageNames = array();
                    $tempImagePaths = array();
                    foreach ($jsonDecoded as $key => $value) {
                        $tempImageName = $this->getImageName();
                        $tempImageDir = $this->getImagePath($companyID, "expense");
                        $tempImagePath = "/storage/app/public/" . $tempImageDir . "/" . $tempImageName;
                        $decodedData = base64_decode($value);
                        $put = \Storage::disk('public')->put($tempImageDir . '/' . $tempImageName, base64_decode($value));
                        array_push($tempImageNames, $tempImageName);
                        array_push($tempImagePaths, $tempImagePath);
                        $imageArray[$tempImageName] = $tempImagePath;
                    }

                    if (!empty($imageArray)) {
                        $imageData = array();
                        foreach ($imageArray as $imageName => $imagePath) {
                            $tempImageArray = array();
                            $tempImageArray["type"] = "expense";
                            $tempImageArray["type_id"] = $savedID;
                            $tempImageArray["company_id"] = $companyID;
                            $tempImageArray["employee_id"] = $employeeID;
                            $tempImageArray["image"] = $imageName;
                            $tempImageArray["image_path"] = $imagePath;
                            $tempImageArray["created_at"] = $createdAt;
                            array_push($imageData, $tempImageArray);
                        }
                        DB::table('images')->insert($imageData);
                    }
                }

                $expenseData["id"] = $savedID;
                $expenseData["images"] = $tempImageNames;
                $expenseData["image_paths"] = $tempImagePaths;
                array_push($arraySyncedData, $expenseData);
                $save = saveAdminNotification($companyID, $employeeID, date("Y-m-d H:i:s"), "Added Expense", "expense", $expenseData);
            }

        }

        return $returnItems ? $arraySyncedData : false;
    }

    public function saveExpense()
    {
        $postData = $this->getJsonRequest();
        //Log::info('info', array("postData"=>print_r($postData,true)));
        $companyID = $this->getArrayValue($postData, "company_id");
        $employeeID = $this->getArrayValue($postData, "employee_id");

        //Log::info('info', array("Employee IDDDDD"=>print_r($employeeID,true)));
        $clientID = $this->getArrayValue($postData, "client_id");
        $images = $this->getArrayValue($postData, "images");
        $createdAt = $this->getArrayValue($postData, "created_at");

        $tempImageNames = array();
        $tempImagePaths = array();
        $imageArray = array();

        if (!empty($images)) {
            $jsonDecoded = json_decode($images, true);
            foreach ($jsonDecoded as $key => $value) {
                $tempImageName = $this->getImageName();
                $tempImageDir = $this->getImagePath($companyID, 'expense');
                $tempImagePath = "/storage/app/public/" . $tempImageDir . "/" . $tempImageName;
                $decodedData = base64_decode($value);
                $put = \Storage::disk('public')->put($tempImageDir . '/' . $tempImageName, base64_decode($value));
                array_push($tempImageNames, $tempImageName);
                array_push($tempImagePaths, $tempImagePath);
                $imageArray[$tempImageName] = $tempImagePath;
            }
        }


        $expenseData = array(
            'company_id' => $companyID,
            'employee_id' => $employeeID,
            'client_id' => $clientID,
            'amount' => $this->getArrayvalue($postData, "amount"),
            'description' => $this->getArrayValue($postData, "description"),
            'approved_by' => $this->getArrayValue($postData, "approved_by"),
            'remark' => $this->getArrayValue($postData, "remark"),
            'status' => $this->getArrayValue($postData, "status"),
            'created_at' => $createdAt,
            'updated_at' => $this->getArrayValue($postData, "updated_at"),
            'employee_type' => 'Employee'
        );

        $savedID = DB::table('expenses')->insertGetId($expenseData);

        if ($savedID) {


            if (!empty($imageArray)) {
                $imageData = array();
                foreach ($imageArray as $imageName => $imagePath) {
                    $tempArray = array();
                    $tempArray["type"] = "expense";
                    $tempArray["type_id"] = $savedID;
                    $tempArray["company_id"] = $companyID;
                    $tempArray["employee_id"] = $employeeID;
                    $tempArray["image"] = $imageName;
                    $tempArray["image_path"] = $imagePath;
                    $tempArray["created_at"] = $createdAt;
                    array_push($imageData, $tempArray);
                }
                DB::table('images')->insert($imageData);
            }


            $expenseData["id"] = $savedID;
            $expenseData["images"] = $tempImageNames;
            $expenseData["image_paths"] = $tempImagePaths;
            $notificationSaved = saveAdminNotification($companyID, $employeeID, date("Y-m-d H:i:s"), "Added Expense", "expense", $expenseData);

            $response = array("status" => true, "message" => "successfully saved", "data" => $expenseData);
            $this->sendResponse($response);

        } else {

            $this->sendEmptyResponse();

        }

    }

    public function syncExpense()
    {
        $postData = $this->getJsonRequest();
        //Log::info('info', array("inside syncExpense"=>print_r($postData,true)));
        $arraySyncedData = $this->manageUnsyncedExpense($postData, true);
        //Log::info('info', array("arraySyncedData"=>print_r($arraySyncedData,true)));
        $response = array("status" => true, "message" => "success", "data" => $arraySyncedData);
        $this->sendResponse($response);

    }


    /**
     * Task Related
     */
    public function fetchTask($return = false, $tempPostData = null)
    {

        $postData = $return ? $tempPostData : $this->getJsonRequest();
        //Log::info('info', array("postData"=>print_r($postData,true)));
        $companyID = $this->getArrayValue($postData, "company_id");
        $employeeID = $this->getArrayValue($postData, "employee_id");

        /*Check if unsynced data is available . if available first update to tha database */
        $syncStatus = $this->manageUnsyncedTask($postData);

        $tasks = DB::table('tasks')
            ->select('tasks.*', 'clients.company_name')
            ->leftJoin('clients', 'clients.id', '=', 'tasks.client_id')
            ->where('tasks.company_id', $companyID)
            ->Where('assigned_to', $employeeID)
            ->get()->toArray();

        if (empty($tasks)) {
            if ($return) {
                return array();
            } else {

                $this->sendEmptyResponse();
            }
        }


        $response = array("status" => true, "message" => "Success", "data" => $tasks);
        if ($return) {
            return $tasks;
        } else {
            $this->sendResponse($response);
        }
    }

    private function manageUnsyncedTask($postData, $returnItems = false, $client = null)
    {

        $rawData = $this->getArrayValue($postData, "nonsynced_task");
        $companyID = $this->getArrayValue($postData, "company_id");
        $employeeID = $this->getArrayValue($postData, "employee_id");
        $clientID = $this->getArrayValue($postData, "client_id");

        if (empty($rawData)) {
            return $returnItems ? array() : false;
        }

        $data = json_decode($rawData, true);

        $arraySyncedData = array();

        /*prepare data for saving*/
        foreach ($data as $key => $task) {
            //Log::info('info', array("task inside unsynced task"=>print_r($task,true)));

            $taskClientID = $this->getArrayValue($task, "client_id");
            $taskClientUniqueID = $this->getArrayValue($task, "client_unique_id");

            if (empty($taskClientID)) {
                if ($returnItems && !empty($client)) {
                    $tempClientUniqueID = $client->unique_id;
                    $tempClientID = $client->id;
                    if ($taskClientUniqueID == $tempClientUniqueID) {
                        $taskClientID = $tempClientID;
                    } else {
                        continue;
                    }
                }
            }

            $createdAt = date('Y-m-d H:i:s');
            $taskData = array(
                'unique_id' => $this->getArrayvalue($task, "unique_id"),
                'company_id' => $companyID,
                'client_id' => $taskClientID,
                'title' => $this->getArrayvalue($task, "title"),
                'due_date' => $this->getArrayValue($task, "due_date"),
                'description' => $this->getArrayValue($task, "description"),
                'priority' => $this->getArrayValue($task, "priority"),
                'assigned_from_type' => $this->getArrayValue($task, "assigned_from_type"),
                'assigned_from' => $this->getArrayValue($task, "assigned_from"),
                'assigned_to' => $this->getArrayValue($task, "assigned_to"),
                'status' => $this->getArrayValue($task, "status"),
            );

            $taskID = $this->getArrayValue($task, "task_id");

            if (!empty($taskID)) {
                $taskData['updated_at'] = $createdAt;
                $updated = DB::table('tasks')->where('id', $taskID)->update($taskData);
                $taskData["id"] = $taskID;
                saveAdminNotification($companyID, $employeeID, date("Y-m-d H:i:s"), "Updated Task", "task", $taskData);
                array_push($arraySyncedData, $taskData);

            } else {

                $taskData["created_at"] = $createdAt;
                $savedID = DB::table('tasks')->insertGetId($taskData);

                if (!empty($savedID)) {
                    $taskData["id"] = $savedID;
                    array_push($arraySyncedData, $taskData);
                    saveAdminNotification($companyID, $employeeID, $createdAt, "Task Added", "task", $taskData);
                }
            }
        }
        /*

    */

        return $returnItems ? $arraySyncedData : true;
    }

    public function saveTask()
    {
        $postData = $this->getJsonRequest();
        //Log::info('info', array("postData Inside Save Task"=>print_r($postData,true)));
        $taskID = $this->getArrayValue($postData, "id");
        $companyID = $this->getArrayValue($postData, "company_id");
        $employeeID = $this->getArrayValue($postData, "employee_id");
        $clientID = $this->getArrayValue($postData, "client_id");
        $createdAt = $this->getArrayValue($postData, "created_at");

        $taskData = array(
            'company_id' => $companyID,
            'client_id' => $clientID,
            'title' => $this->getArrayvalue($postData, "title"),
            'due_date' => $this->getArrayValue($postData, "due_date"),
            'description' => $this->getArrayValue($postData, "description"),
            'priority' => $this->getArrayValue($postData, "priority"),
            'assigned_from_type' => $this->getArrayValue($postData, "assigned_from_type"),
            'assigned_from' => $this->getArrayValue($postData, "assigned_from"),
            'assigned_to' => $this->getArrayValue($postData, "assigned_to"),
            'status' => $this->getArrayValue($postData, "status"),
            'created_at' => $createdAt,
        );

        if (!empty($taskID)) {

            $updated = DB::table('tasks')->where('id', $taskID)->update($taskData);
            $taskData["id"] = $taskID;
            saveAdminNotification($companyID, $employeeID, date("Y-m-d H:i:s"), "Updated Task", "task", json_encode($taskData), $description = "");
            $response = array("status" => true, "message" => "successfully saved", "data" => $taskData);

        } else {

            $savedID = DB::table('tasks')->insertGetId($taskData);
            $taskData["id"] = $savedID;
            saveAdminNotification($companyID, $employeeID, date("Y-m-d H:i:s"), "Added Task", "task", json_encode($taskData), $description = "");
            $response = array("status" => true, "message" => "successfully saved", "data" => $taskData);
        }

        $this->sendResponse($response);
    }

    public function syncTask()
    {

        $postData = $this->getJsonRequest();
        $arraySyncedData = $this->manageUnsyncedTask($postData, true);
        $response = array("status" => true, "message" => "success", "data" => $arraySyncedData);
        $this->sendResponse($response);

    }


    /**
     * Returns and Day Remark  Related
    */
   
    public function fetchReturnReason($return = false, $postData = null){

        $postData = $return?$postData:$this->getJsonRequest();
        $companyID = $this->getArrayValue($postData, "company_id");
        $offset = $this->getArrayValue($postData, "offset",0);
        $limit = $this->getArrayValue($postData, "limit",200);
        $returnreasons = DB::table('returnreasons')
            ->where("returnreasons.company_id", $companyID)
            ->offset($offset)
            ->limit($limit)
            ->get()->toArray();
        $response = array("status" => true, "message" => "Success", "data" => $returnreasons);
        if($return){
            return $returnreasons;
        } else {
            $this->sendResponse($response);
        }
    }

    public function fetchReturns($return= false,$postData = null){
        $postData = $return?$postData:$this->getJsonRequest();
        $companyID = $this->getArrayValue($postData, "company_id");
        $employeeID = $this->getArrayValue($postData, "employee_id");
        $offset = $this->getArrayValue($postData, "offset",0);
        $limit = $this->getArrayValue($postData, "limit",200);
        $prodReturns = DB::table('returns')
        ->select('returns.id as return_id','returns.company_id as company_id','returns.employee_id','returns.return_date','returns.superior','returns.client_id','returns.created_at as ret_created_at','clients.company_name as company_name', 'return_details.*', 'products.brand', 'products.category_id', 'brands.name as brand_name', 'categories.name as category_name','returnreasons.name as return_reason')
        ->leftJoin('clients','returns.client_id','clients.id')
        ->leftJoin('return_details','returns.id','return_details.return_id')
        ->leftJoin('returnreasons', 'return_details.reason', 'returnreasons.id')
        ->leftJoin('products', 'return_details.product_id','products.id')
        ->leftJoin('brands', 'products.brand','brands.id')
        ->leftJoin('categories', 'products.category_id','categories.id')
        ->where("returns.company_id", $companyID)
        ->where("returns.employee_id", $employeeID)
        ->get()->toArray();
        $finalArray = array();

        if(!empty($prodReturns)){
            $grouped = arrayGroupBy($prodReturns,'return_id',true);
            
            foreach ($grouped as $key => $value) {

                $tempObj = new StdClass();
                $tempObj->id = $key;

                $rproductsArray = array();
                foreach ($value as $key1 => $value1) {
                    $rproduct = array();
                    $rProduct['id'] = getObjectValue($value1,"id");
                    $rProduct['return_id'] = getObjectValue($value1,"return_id");
                    $rProduct['brand_name'] = getObjectValue($value1,"brand_name");
                    $rProduct['category_name'] = getObjectValue($value1,"category_name");
                    $rProduct['product_id'] = getObjectValue($value1,"product_id");
                    $rProduct['product_name'] = getObjectValue($value1,"product_name");
                    $rProduct['variant_id'] = getObjectValue($value1,"variant_id");
                    $rProduct['variant_name'] = getObjectValue($value1,"variant_name");
                    $rProduct['unit_id'] = getObjectValue($value1,"unit_id");
                    $rProduct['unit_name'] = getObjectValue($value1,"unit_name");
                    $rProduct['unit_symbol'] = getObjectValue($value1,"unit_symbol");
                    $rProduct['quantity'] = getObjectValue($value1,"quantity");
                    $rProduct['reason'] = getObjectValue($value1,"reason");
                    $rProduct['return_reason'] = getObjectValue($value1,"return_reason");
                    $rProduct['batch_no'] = getObjectValue($value1,"batch_no");
                    $rProduct['expiry_date'] = getObjectValue($value1,"expiry_date");
                    $rProduct['mfg_date'] = getObjectValue($value1,"mfg_date");
                    $rProduct['image'] = getObjectValue($value1,"image");
                    $rProduct['image_path'] = getObjectValue($value1,"image_path");
                    array_push($rproductsArray,$rProduct);
                    
                }
                $tempObj->return_date = getObjectValue($value1,"return_date");
                $tempObj->employee_id = getObjectValue($value1,"employee_id");
                $tempObj->client_id = getObjectValue($value1,"client_id");
                $tempObj->superior = getObjectValue($value1,"superior");

                $tempObj->company_id = getObjectValue($value1,"company_id");
                $tempObj->company_name = getObjectValue($value1,"company_name");
                $tempObj->created_at = getObjectValue($value1,"ret_created_at");
                $tempObj->rproducts = json_encode($rproductsArray);

                array_push($finalArray,$tempObj);
            }

        }
        

        $response = array("status" => true, "message" => "Success", "data" => $finalArray);
        if($return){

            return $finalArray;

        } else {

            $this->sendResponse($response);
        }
    }

    public function saveReturns($postData=null){

        $postData = $this->getJsonRequest();
        $prodReturns = $this->getArrayValue($postData,"returnsdata");
        $prodReturn = json_decode($prodReturns, true);
        $returnDetail = $this->getArrayValue($prodReturn,"rproducts");
        $returnDetails = json_decode($returnDetail, true);
        
        $companyID = $this->getArrayValue($prodReturn, "company_id");
        $returnID = null;//$this->getArrayValue($prodReturn, "return_id");
        $clientID = $this->getArrayValue($prodReturn,"client_id");
        $superiorClientID = $this->getArrayValue($prodReturn,"superior");
        $employeeID = $this->getArrayValue($prodReturn, "employee_id");
        $returnDate = $this->getArrayValue($prodReturn,"return_date");
        $returnDateUnix = $this->getArrayValue($prodReturn,"return_date_unix");
        $createdAt = $this->getArrayValue($prodReturn, "created_at");

        $return = ProductReturn::insertGetId(['company_id'=>$companyID,'client_id'=>$clientID,'superior'=>$superiorClientID,'employee_id'=>$employeeID,'return_date'=>$returnDate,'return_unixtime'=>$returnDateUnix, 'created_at'=>$createdAt, 'updated_at'=>$createdAt]);

        if(isset($return)){
            $returnID = $return;
            $batchArray = array();
            foreach($returnDetails as $returnDetail){
                $sdData = array(
                    'return_id'=>$returnID,
                    'product_id'=>$this->getArrayValue($returnDetail,'product_id'),
                    'product_name'=>$this->getArrayValue($returnDetail,'product_name'),
                    'variant_id'=>$this->getArrayValue($returnDetail,'variant_id'),
                    'variant_name'=>$this->getArrayValue($returnDetail,'variant_name'),
                    'unit_id'=> $this->getArrayValue($returnDetail,'unit'),
                    'unit_name'=>$this->getArrayValue($returnDetail,'unit_name'),
                    'unit_symbol'=>$this->getArrayValue($returnDetail,'unit_symbol'),
                    'quantity'=>$this->getArrayValue($returnDetail,'quantity'),
                    'reason'=>$this->getArrayValue($returnDetail,'reason'),
                    'image'=>$this->getArrayValue($returnDetail,'image'),
                    'image_path'=>$this->getArrayValue($returnDetail,'image_path'),
                    'mfg_date'=>$this->getArrayValue($returnDetail,'mfg_date'),
                    'batch_no'=>$this->getArrayValue($returnDetail,'batch_no'),
                    'expiry_date'=>getArrayValue($returnDetail,'expiry_date'),
                    'created_at'=> $createdAt,
                    'updated_at'=>$createdAt
                );
                array_push($batchArray,$sdData);
            }

            $batchSaved = DB::table('return_details')->insert($batchArray);
            $tempObj = new stdClass();
            $tempObj->id = $returnID;
            $tempObj->company_id = $companyID;
            $tempObj->employee_id = $employeeID;
            $tempObj->return_date = $returnDate;
            $tempObj->return_date_unix = $returnDateUnix;
            
            $response = array("status" => true, "message" => "Success", "data" => $tempObj);

            $this->sendResponse($response);
        }
    }

    public function syncReturns()
    {

        $postData = $this->getJsonRequest();
        $arraySyncedReturns = $this->manageUnsyncedReturns($postData, true);
        $response = array("status" => true, "message" => "success", "data" => $arraySyncedReturns);

        $this->sendResponse($response);
    }

     public function manageUnsyncedReturns($postData, $returnItems)
    {
      $rawData = $this->getArrayValue($postData, "nonsynced_returns");

      $employeeID = $this->getArrayValue($postData, "employee_id");
      $companyID = $this->getArrayValue($postData, "company_id");
      if (empty($rawData)) {
          return $returnItems ? array() : false;
      }

      $arraySyncedData = [];
      $data = json_decode($rawData, true);
      foreach ($data as $key => $returnData) {

        $uniqueID = $this->getArrayValue($returnData,"unique_id");
        $companyID = $this->getArrayValue($returnData, "company_id");
        $clientID = $this->getArrayValue($returnData,"client_id");
        $superiorClientID = $this->getArrayValue($returnData,"superior");
        $employeeID = $this->getArrayValue($returnData, "employee_id");
        $returnDate = $this->getArrayValue($returnData,"return_date");
        $returnDateUnix = $this->getArrayValue($returnData,"return_date_unix");
        $createdAt = $this->getArrayValue($returnData, "created_at");

        $returnDetail = $this->getArrayValue($returnData, "rproducts");
        $returnDetails = json_decode($returnDetail, true);

        $return = ProductReturn::insertGetId(['company_id'=>$companyID,'client_id'=>$clientID,'superior'=>$superiorClientID,'employee_id'=>$employeeID,'return_date'=>$returnDate,'return_unixtime'=>$returnDateUnix, 'created_at'=>$createdAt, 'updated_at'=>$createdAt]);

        $batchArray = array();
        if (isset($return)) {
          $returnID = $return;
          foreach ($returnDetails as $returnDetail) {
              $sdData = array(
              'return_id'=>$returnID,
              'product_id'=>$this->getArrayValue($returnDetail, 'product_id'),
              'product_name'=>$this->getArrayValue($returnDetail, 'product_name'),
              'variant_id'=>$this->getArrayValue($returnDetail, 'variant_id'),
              'variant_name'=>$this->getArrayValue($returnDetail, 'variant_name'),
              'unit_id'=> $this->getArrayValue($returnDetail, 'unit'),
              'unit_name'=>$this->getArrayValue($returnDetail, 'unit_name'),
              'unit_symbol'=>$this->getArrayValue($returnDetail, 'unit_symbol'),
              'quantity'=>$this->getArrayValue($returnDetail, 'quantity'),
              'reason'=>$this->getArrayValue($returnDetail, 'reason'),
              'image'=>$this->getArrayValue($returnDetail, 'image'),
              'image_path'=>$this->getArrayValue($returnDetail, 'image_path'),
              'mfg_date'=>$this->getArrayValue($returnDetail, 'mfg_date'),
              'batch_no'=>$this->getArrayValue($returnDetail, 'batch_no'),
              'expiry_date'=>getArrayValue($returnDetail, 'expiry_date'),
              'created_at'=> $createdAt,
              'updated_at'=>$createdAt,
          );
              array_push($batchArray, $sdData);
          }
          $batchSaved = DB::table('return_details')->insert($batchArray);

          $tempArray = array();
          $tempArray['uniqueID'] = $uniqueID;
          $tempArray['ReturnID'] = $returnID;


          array_push($arraySyncedData, $tempArray);
        }
      }
      return $returnItems ? $arraySyncedData : false;
    }

    public function fetchDayRemark($return= false,$postData = null){
        $postData = $return?$postData:$this->getJsonRequest();
        $companyID = $this->getArrayValue($postData, "company_id");
        $employeeID = $this->getArrayValue($postData, "employee_id");
        $offset = $this->getArrayValue($postData, "offset",0);
        $limit = $this->getArrayValue($postData, "limit",200);
        $remarks= DayRemarks::where("company_id", $companyID)
                    ->where("employee_id", $employeeID)
                    ->get()
                    ->toArray();
        //Log::info('info', array("remarks"=>print_r($remarks,true)));
        $response = array("status" => true, "message" => "Success", "data" => $remarks);
        if($return){

            return $remarks;

        } else {

            $this->sendResponse($response);
        }
    }

    public function saveDayRemark($return=false,$postData = null)
    {
        $postData = $this->getJsonRequest();
        $companyID = $this->getArrayValue($postData, "company_id");
        $employeeID = $this->getArrayValue($postData, "employee_id");
        $remark = $this->getArrayValue($postData,"remark");
        $remark = json_decode($remark,true);
        if(empty($remark)) $this->sendEmptyResponse();
        $uniqueID = $this->getArrayValue($remark, "unique_id");
        $remarkID = $this->getArrayValue($remark, "remark_id");
        
        $remarkData = array(
            'unique_id' => $uniqueID,
            'company_id' => $this->getArrayValue($remark, "company_id"),
            'employee_id' => $this->getArrayValue($remark, "employee_id"),
            'remarks' => $this->getArrayvalue($remark, "remarks"),
            'remark_date' => $this->getArrayValue($remark, "remark_date"),
            'remark_date_unix' => $this->getArrayValue($remark, "remark_date_unix",""),
            'remark_datetime' => $this->getArrayValue($remark, "remark_datetime"),
            'remark_datetime_unix' => $this->getArrayValue($remark, "remark_datetime_unix","")
        );
        if(!empty($uniqueID)){

            $dayRemark = DayRemarks::updateOrCreate(
                [
                    "unique_id" => $uniqueID
                ],
                $remarkData
            );

        } elseif(!empty($dayRemarkID)){

            $dayRemark = DayRemarks::updateOrCreate(
                [
                    "id" => $dayRemarkID
                ],
                $remarkData
            );
        }
        
        $wasRecentlyCreated = $dayRemark->wasRecentlyCreated;
        $wasChanged = $dayRemark->wasChanged();
        $isDirty = $dayRemark->isDirty();
        $exists = $dayRemark->exists;

        if ($wasRecentlyCreated || $wasChanged || $dayRemark->exists) {

            $msg = "";
            $savedDayRemark = $remarkData;
            $savedDayRemark["id"] = $dayRemark->id;
            
            if ($dayRemark->wasRecentlyCreated) {
                
                $msg = "Added Day Remarks";
            } else {

                $msg = "Updated Day Remarks";

            }

            $nSaved = saveAdminNotification($companyID, $employeeID, date("Y-m-d H:i:s"), $msg, "remark", $savedDayRemark);
            $response = array("status" => true, "message" => $msg, "remark_id" => $dayRemark->id);
        } else {
            $response = array("status" => false, "message" => "Unable to create/update", "remark_id" => "");
            
        }
        $this->sendResponse($response);

    }

    public function syncDayRemark()
    {

        $postData = $this->getJsonRequest();
        $companyID = $this->getArrayValue($postData,"company_id");
        $employeeID = $this->getArrayValue($postData,"employee_id");

        $arraySyncedData = $this->manageUnsyncedDayRemark($postData, true);
        
        $response = array("status" => true, "message" => "success", "data" => $arraySyncedData);
        $this->sendResponse($response);

    }

    public function manageUnsyncedDayRemark($postData, $returnItems = false)
    {

        $rawData = $this->getArrayValue($postData, "unsynced_data");
        $companyID = $this->getArrayValue($postData, "company_id");
        $employeeID = $this->getArrayValue($postData, "employee_id");

        if (empty($rawData)) {

            return $returnItems ? array() : false;
        }
        $data = json_decode($rawData, true);

        // Log::info('info', array('PostData' => print_r("Manage Unsync", true)));

        $arraySyncedData = array();

        $msg = "Added Day Remark.";

        /*prepare data for saving*/
        foreach ($data as $value) {

            $uniqueID = $this->getArrayValue($value, "unique_id");
            $tempArray["unique_id"] = $uniqueID;
            
            $tempArray["company_id"] = $companyID;            
            $tempArray["employee_id"] = $employeeID;
 
            $createdAt = $this->getArrayvalue($value, "created_at");
            $tempArray["created_at"] = $createdAt; 
            $remarks = $this->getArrayvalue($value, "remarks");
            $tempArray["remarks"] = $remarks;
            $remarkDate = $this->getArrayvalue($value, "remark_date");
            $tempArray["remark_date"] = $remarkDate;
            $remarkDateUnix = $this->getArrayvalue($value, "remark_date_unix","");
            $tempArray["remark_date_unix"] = $remarkDateUnix;
            $remarkDateTime = $this->getArrayvalue($value, "remark_datetime");
            $tempArray["remark_datetime"] = $remarkDateTime;
            $remarkDateTimeUnix = $this->getArrayvalue($value, "remark_datetime_unix","");
            $tempArray["remark_datetime_unix"] = $remarkDateTimeUnix;


            $savedID = DayRemarks::insertGetId([
              "unique_id" => $uniqueID,
              "company_id" => $companyID,
              "employee_id" => $employeeID,
              "remarks" => $remarks,
              "remark_date" => $remarkDate,
              "remark_date_unix" => $remarkDateUnix,
              "remark_datetime" => $remarkDateTime,
              "remark_datetime_unix" => $remarkDateTimeUnix,
              "created_at" => $createdAt,

            ]);

            if (!empty($savedID)) {

                $syncedData = $tempArray;
                $syncedData['id'] = $savedID;
                // Log::info('info2', array('PostData2' => print_r($syncedData, true)));
                $nSaved = saveAdminNotification($companyID, $employeeID, date("Y-m-d H:i:s"), $msg, "remark", $syncedData);

                array_push($arraySyncedData, $syncedData);
            }
        }

        return $returnItems ? $arraySyncedData : true;
    }

    public function fetchOrderFulFillments($return= false,$postData = null){
        
    }


    public function fetchBeatPlans($return = false, $postData = null)
    {

        $postData = $return?$postData:$this->getJsonRequest();
        $companyID = $this->getArrayValue($postData, "company_id");
        $employeeID = $this->getArrayValue($postData, "employee_id");
        $date = $this->getArrayValue($postData,"date");
        $offset = $this->getArrayValue($postData, "offset",0);
        $limit = $this->getArrayValue($postData, "limit",200);


        $beatplans = DB::table('beatvplans')
            ->select('beatvplans.company_id as company_id','beatvplans.employee_id as employee_id','beatvplans.status as status', 'beatplansdetails.*')
            ->leftJoin('beatplansdetails', 'beatplansdetails.beatvplan_id', '=', 'beatvplans.id')
            ->where("beatvplans.company_id", $companyID)
            ->where("beatvplans.employee_id", $employeeID)
            //->where("beatvplans.status", "Approved")
            ->get()->toArray();
        $beat_name = array();
        foreach($beatplans as $beatplan){
            $beatIds = $beatplan->beat_id;
            $arrayBeatIds = explode(",",$beatIds);
            foreach($arrayBeatIds as $beat_id){
                $beat = Beat::where('id',$beat_id)->first();
                $beat_name[$beatplan->beatvplan_id] = getObjectValue($beat,"name","");
            }
        }
        // Log::info('info', array("data "=>print_r($beat_name,true)));

        $response = array("status" => true, "message" => "Success", "data" => $beatplans,"beats"=>$beat_name);
        if($return){

            return $beatplans;

        } else {

            $this->sendResponse($response);
        }
    }

    public function syncBeatplan()
    {
        $postData = $this->getJsonRequest();
        //Log::info('info', array("data "=>print_r($postData,true)));
        $arraySyncedData = $this->manageUnsyncedBeatplan($postData, true);
        $response = array("status" => true, "message" => "success", "data" => $arraySyncedData);
        $this->sendResponse($response);
    }

    public function manageUnsyncedBeatplan($postData, $returnItems = false)
    {
        $rawData = $this->getArrayValue($postData, "nonsynced_beatplan");
        $companyID = $this->getArrayValue($postData, "company_id");
        $employeeID = $this->getArrayValue($postData, "employee_id");
        $status = $this->getArrayValue($postData, "status");
        
        if (empty($rawData)) {
            return $returnItems ? array() : false;
        }

        $data = json_decode($rawData, true);
        $arraySyncedData = [];
        foreach ($data as $key => $bp) {

          $unique_id = $this->getArrayValue($bp, "unique_id");
          $wasChanged = false;
   
          $beatplanData = array(            
            "company_id" => $companyID,
            "employee_id" => $employeeID,
            "status" => $status,
            "unique_id" => $unique_id
          );

          $beatplan = BeatVPlan::updateOrCreate(
              [
                  "unique_id" => $unique_id,
              ],
              $beatplanData
          );

          $wasCreated = $beatplan->wasRecentlyCreated;

          // $wasChanged = $beatplan->wasChanged(); 

          if($wasCreated == "1"){ 
            $beatplanDetails = new BeatPlansDetails();
            $beatplanDetails->title = $this->getArrayValue($bp,"title");
            $beatplanDetails->beatvplan_id = $beatplan->id;
            $beatplanDetails->beat_id = $this->getArrayValue($bp,"beat_id");   
            $beatplanDetails->client_id = $this->getArrayValue($bp,"client_id");   
            $beatplanDetails->plandate = $this->getArrayValue($bp,"plandate");   
            // $beatplanDetails->planenddate = $this->getArrayValue($bp,"planenddate");   
            $beat_clients_array = $this->makeBeatClientArray($beatplanDetails->beat_id, $beatplanDetails->client_id);
            $beatplan->beat_clients = $beat_clients_array;  
            $beatplanDetails->plan_from_time = $this->getArrayValue($bp,"plan_from_time");   
            $beatplanDetails->plan_to_time = $this->getArrayValue($bp,"plan_to_time");
            $beatplanDetails->remark = $this->getArrayValue($bp,"remark");   
            $beatplanDetails->save();
            $arraySyncedData[$unique_id][$beatplanDetails->id]['created']=true;
          }
          // Log::info('info', array("beatplan_id "=>print_r($beatplan->id,true)));
          $beatDetail_id = $this->getArrayValue($bp, "beatDetail_id");
          if($beatDetail_id!=null){
            $beatplanDetails = BeatPlansDetails::where('id',$beatDetail_id)->where('beatvplan_id',$beatplan->id)->first();
            if($beatplanDetails!=null){
                //Log::info('info', array("data "=>print_r($beatplanDetails,true)));
                $beatplanDetails->title = $this->getArrayValue($bp,"title");
                $beatplanDetails->beatvplan_id = $beatplan->id;
                $beatplanDetails->beat_id = $this->getArrayValue($bp,"beat_id");   
                $beatplanDetails->client_id = $this->getArrayValue($bp,"client_id");  
                $beat_clients_array = $this->makeBeatClientArray($beatplanDetails->beat_id, $beatplanDetails->client_id);
                $beatplan->beat_clients = $beat_clients_array;  
                $beatplanDetails->plandate = $this->getArrayValue($bp,"plandate");   
                $beatplanDetails->planenddate = $this->getArrayValue($bp,"planenddate");   
                $beatplanDetails->plan_from_time = $this->getArrayValue($bp,"plan_from_time");   
                $beatplanDetails->plan_to_time = $this->getArrayValue($bp,"plan_to_time");
                $beatplanDetails->remark = $this->getArrayValue($bp,"remark");   
                $beatplanDetails->save();
                $arraySyncedData[$unique_id][$beatplanDetails->id]['updated']=true;
                $wasChanged = true;                
            }    
          }else{
              $wasChanged = false;
          }   
          //Log::info('info', array("created "=>print_r($wasCreated,true)));
          //Log::info('info', array("changed "=>print_r($wasChanged,true)));

          if($wasCreated == "1" || $wasChanged == true){
            $arraySyncedData[$unique_id]['saved']=true;
          }else{
            $arraySyncedData[$unique_id]['saved']=false;
          }
        }
        // Log::info('info', array("data "=>print_r($arraySyncedData,true)));
        return $returnItems ? $arraySyncedData : false;
    }

    public function fetchBeats($return = false, $postData = null)
    {

        $postData = $return?$postData:$this->getJsonRequest();
        //Log::info('info', array("postData"=>print_r($postData,true)));
        $companyID = $this->getArrayValue($postData, "company_id");
        $employeeID = $this->getArrayValue($postData, "employee_id");
        $offset = $this->getArrayValue($postData, "offset",0);
        $limit = $this->getArrayValue($postData, "limit",200);

        //todo need to remanaged
        
        $finalArray = array();
        $beats = DB::table('beats')
            ->select('beats.*', 'beat_client.client_id','beat_client.beat_id')
            ->leftJoin('beat_client', 'beat_client.beat_id', '=', 'beats.id')
            ->where("beats.company_id", $companyID)
            ->where("beats.status", "Active")
            ->get()->toArray();
        $beatsGroupedByID = arrayGroupBy($beats,"id",true);
        foreach ($beatsGroupedByID as $key => $value) {

            $tempClientIDs = array();
            foreach ($value as $k => $v) {
                array_push($tempClientIDs,getObjectValue($v,"client_id"));
            }

            $v->client_id = $tempClientIDs;

            array_push($finalArray,$v);
        }
        // Log::info('info', array("data "=>print_r($finalArray,true)));
        $response = array("status" => true, "message" => "Success", "data" => $finalArray);
        if($return){

            return $finalArray;

        } else {

            $this->sendResponse($response);
        }
    }

    public function fetchBeatsParties($return = false, $postData = null)
    {

        $postData = $return?$postData:$this->getJsonRequest();
        //Log::info('info', array("postData"=>print_r($postData,true)));
        $companyID = $this->getArrayValue($postData, "company_id");
        $employeeID = $this->getArrayValue($postData, "employee_id");
        $offset = $this->getArrayValue($postData, "offset",0);
        $limit = $this->getArrayValue($postData, "limit",200);

        //todo need to remanaged

        //start of pasted

        $handles =  DB::table('handles')
          ->where('employee_id', $employeeID)
          ->pluck('client_id')->toArray();


        $clients = Client::where('company_id', $companyID)
          ->where('status', 'Active')
          ->orderBy('company_name', 'asc')
          ->get();
        $beat_clients = Client::select('clients.company_name', 'clients.id','beats.name as beat_name','beats.id as beatid')
                ->leftJoin('beat_client','clients.id','beat_client.client_id')
                ->leftJoin('beats','beats.id','beat_client.beat_id')
                ->whereIn('clients.id', $handles)
                ->where('clients.company_id', $companyID)
                ->where('clients.status', 'Active')
                ->orderBy('beats.name', 'desc')
                ->orderBy('company_name', 'asc')
                ->get();
        $beats_list = array();  
        foreach($beat_clients as $beat_client){
          if($beat_client->beatid!=''){
            $beats_list[$beat_client->beatid]['name']=$beat_client->beat_name;
            $beats_list[$beat_client->beatid]['id']=$beat_client->beatid;
            $beats_list[$beat_client->beatid]['clients'][$beat_client->id]=$beat_client->company_name;
          }else{
            $beats_list[0]['name']='Unspecified';
            $beats_list[0]['id']='0';
            $beats_list[0]['clients'][$beat_client->id]=$beat_client->company_name;
                }
        }
        $response = array("status" => true, "message" => "Success", "data" => $beats_list);
        if($return){

            return $beats_list;

        } else {

            $this->sendResponse($response);
        }
    }

    public function saveBeatplan($postData = null)
    {
        $postData = $this->getJsonRequest();
        $companyID = $this->getArrayValue($postData, "company_id");
        $employeeID = $this->getArrayValue($postData, "employee_id");
        $beatDetailID = $this->getArrayValue($postData, "beat_detail_id");
        if($beatDetailID!=null){
          $beatplan = BeatPlansDetails::where('id',$beatDetailID)->first();
          $title = "Updated BeatPlan";
        }else{          
          $beatvplan = new BeatVPlan();
          $beatvplan->company_id = $companyID;
          $beatvplan->employee_id = $employeeID;  
          $beatvplan->status = "Approved";  
          $beatvplan->save();
          $beatplan = new BeatPlansDetails();
          $title = "Created BeatPlan";
          $beatplan->beatvplan_id = $beatvplan->id;
        }
        $beatplan->title = $this->getArrayValue($postData,"title");
        $beatplan->beat_id = $this->getArrayValue($postData,"beat_id");
        $beatID = $beatplan->beat_id;
        if($beatplan->beat_id == null){
            $beatplan->beat_id = "0";   $beatID = "0";      
        }   
        $beatplan->client_id = $this->getArrayValue($postData,"client_id");   
        $beat_clients_array = $this->makeBeatClientArray( $beatID, $beatplan->client_id);
        $beatplan->beat_clients = $beat_clients_array;
        $beatplan->plandate = $this->getArrayValue($postData,"plandate");   
        $beatplan->planenddate = $this->getArrayValue($postData,"planenddate");   
        $beatplan->plan_from_time = $this->getArrayValue($postData,"plan_from_time");   
        $beatplan->plan_to_time = $this->getArrayValue($postData,"plan_to_time");
        $beatplan->remark = $this->getArrayValue($postData,"remark");  
        $beatplan->save();

        $nSaved = saveAdminNotification($companyID, $employeeID, date("Y-m-d H:i:s"), $title, "beatplan", $beatplan);
        
        $response = array("status" => true, "message" => "Success", "data" => $beatplan);
        $this->sendResponse($response);
    }

    function makeBeatClientArray($beat_id, $client_id){
        $beat_clients = array();
        $beatIDS = explode(',',$beat_id);
        $clientIDs = explode(',',$client_id);
        foreach($beatIDS as $bt_id){
            if($bt_id==0){
                $beatClients = DB::table('beat_client')->whereIn('beat_id', $beatIDS)
                ->pluck('client_id')->toArray();
                foreach($clientIDs as $ct_id){
                    if(!(in_array($ct_id, $beatClients))){
                        $beat_clients[$bt_id][] = $ct_id;
                    }
                }
            }else{
                $beat_client_ids = DB::table('beat_client')->where('beat_id', $bt_id)
                                    ->pluck('client_id')->toArray();
                foreach($clientIDs as $ct_id){
                    if(in_array($ct_id, $beat_client_ids)){
                        $beat_clients[$bt_id][] = $ct_id;
                    }
                }
            }
        }

        return json_encode((object)$beat_clients); 
    }

    /* Collateeral Related */
    public function fetchCollateralFiles($return = false, $postData = null)
    {
      $postData = $return?$postData:$this->getJsonRequest();
      $companyID = $this->getArrayValue($postData, "company_id");
      $offset = $this->getArrayValue($postData, "offset",0);
      $limit = $this->getArrayValue($postData, "limit",200);

      $collaterals = CollateralsFolder::where('collateral_folders.company_id', $companyID)
                    ->get(['collateral_folders.id as folderId', 'collateral_folders.folder_name as folderName']);
      $folderIds = $collaterals->pluck('folderId')->toArray();
      $collateralFiles = CollateralsFile::whereIn('collateral_folder_id', $folderIds)->get()->toArray();
      $collateralFolders = $collaterals->toArray();
      $returnArray['folders'] = $collateralFolders;
      $returnArray['files'] = $collateralFiles;

      $response = array("status" => true, "message" => "Success", "data" => $returnArray);
      if($return){
        return $collaterals;
      } else {
        $this->sendResponse($response);
      }
    }
    
    /**
     * Stock  Related
     */
    

    public function fetchStock($return = false, $postData = null)
    {
        $postData = $return?$postData:$this->getJsonRequest();
        $companyID = $this->getArrayValue($postData, "company_id");
        $employeeID = $this->getArrayValue($postData, "employee_id");
        $clientID = $this->getArrayValue($postData, "client_id");
        $stock_date = $this->getArrayValue($postData,"stock_date");
        $offset = $this->getArrayValue($postData, "offset",0);
        $limit = $this->getArrayValue($postData, "limit",200);

        $stocks= DB::table('stocks')
        ->select('stocks.id as stock_id','stocks.company_id as company_id','stocks.employee_id','stocks.stock_date','clients.company_name as company_name','stock_details.*')
        ->leftJoin('clients','stocks.client_id','clients.id')
        ->leftJoin('stock_details','stocks.id','stock_details.stock_id')
        ->where("stocks.company_id", $companyID)
        ->where("stocks.employee_id", $employeeID)
        ->get()->toArray();

        
        $finalArray = array();

        if(!empty($stocks)){
            $grouped = arrayGroupBy($stocks,'stock_id',true);
            
            foreach ($grouped as $key => $value) {

                $tempObj = new StdClass();
                $tempObj->id = $key;

                $sproductsArray = array();
                foreach ($value as $key1 => $value1) {
                    $sproduct = array();
                    $sProduct['id'] = getObjectValue($value1,"id");
                    $sProduct['product_id'] = getObjectValue($value1,"product_id");
                    $sProduct['product_name'] = getObjectValue($value1,"product_name");
                    $sProduct['variant_id'] = getObjectValue($value1,"variant_id");
                    $sProduct['variant_name'] = getObjectValue($value1,"variant_name");
                    $sProduct['changeInValue'] = getObjectValue($value1,"quantity");
                    $sProduct['unit_id'] = getObjectValue($value1,"unit_id");
                    $sProduct['unit_name'] = getObjectValue($value1,"unit_name");
                    $sProduct['unit_symbol'] = getObjectValue($value1,"unit_symbol");
                    $sProduct['image'] = getObjectValue($value1,"image");
                    $sProduct['image_path'] = getObjectValue($value1,"image_path");
                    array_push($sproductsArray,$sProduct);
                    
                }
                $tempObj->stock_date = getObjectValue($value1,"stock_date");
                $tempObj->employee_id = getObjectValue($value1,"employee_id");

                $tempObj->company_id = getObjectValue($value1,"company_id");
                $tempObj->company_name = getObjectValue($value1,"company_name");

                $tempObj->sproducts = json_encode($sproductsArray);

                array_push($finalArray,$tempObj);
            }

        }
        

        $response = array("status" => true, "message" => "Success", "data" => $finalArray);
        if($return){

            return $finalArray;

        } else {

            $this->sendResponse($response);
        }
    }

    public function saveStock($postData = null)
    {
        $postData = $this->getJsonRequest();
        $stock = $this->getArrayValue($postData,"stock");
        $decodedStock = json_decode($stock,true);
        $sproducts = $this->getArrayValue($decodedStock,"sproducts");
        $stockDetails = json_decode($sproducts,true);

        $companyID = $this->getArrayValue($postData, "company_id");
        $employeeID = $this->getArrayValue($postData, "employee_id");
        $stockID = $this->getArrayValue($decodedStock, "stock_id");
        $clientID = $this->getArrayValue($decodedStock,"client_id");
        $stockDate = $this->getArrayValue($decodedStock,"stock_date");
        $stockDateUnix = $this->getArrayValue($decodedStock,"stock_date_unix");

        $stock = Stock::updateOrCreate(['id'=>$stockID],['company_id'=>$companyID,'client_id'=>$clientID,'employee_id'=>$employeeID,'stock_date'=>$stockDate,'stock_date_unix'=>$stockDateUnix]);
        $employee = Employee::findOrFail($employeeID);
        if($employee)
            activity()->log('Created Stock', $employee->user_id);
        $wasCreatedStock = $stock->wasRecentlyCreated;
        $wasChangedStock = $stock->wasChanged(); 
        //Log::info('info', array("stockDetails"=>print_r($stockDetails,true)));
        if($wasCreatedStock == true){
            $stockID = $stock->id;
            $batchArray = array();
            foreach($stockDetails as $stockDetail){
                $sdData = array(
                    'stock_id'=>$stockID,
                    'product_id'=>$this->getArrayValue($stockDetail,'product_id'),
                    'product_name'=>$this->getArrayValue($stockDetail,'product_name'),
                    'variant_id'=>$this->getArrayValue($stockDetail,'variant'),
                    'variant_name'=>$this->getArrayValue($stockDetail,'variant_name'),
                    'unit_id'=>$this->getArrayValue($stockDetail,'unit'),
                    'unit_name'=>$this->getArrayValue($stockDetail,'unit_name'),
                    'unit_symbol'=>$this->getArrayValue($stockDetail,'unit_symbol'),
                    'quantity'=>$this->getArrayValue($stockDetail,'changeInValue'),
                    //'quantity'=>$this->getArrayValue($stockDetail,'quantity'),
                    'image'=>$this->getArrayValue($stockDetail,'image'),
                    'image_path'=>$this->getArrayValue($stockDetail,'image_path'),
                    'mrp'=>$this->getArrayValue($stockDetail,'mrp'),
                    'total_amount'=>getArrayValue($stockDetail,'mrp')
                );
                array_push($batchArray,$sdData);
            }
            //Log::info('info', array("batchArray"=>print_r($batchArray,true)));
            $batchSaved = DB::table('stock_details')->insert($batchArray);
            $tempObj = new stdClass();
            $tempObj->id = $stockID;
            $tempObj->company_id = $companyID;
            $tempObj->employee_id = $employeeID;
            $tempObj->stock_date = $stockDate;
            $tempObj->stock_date_unix = $stockDateUnix;
            $response = array("status" => true, "message" => "Success", "data" => $tempObj);
            $this->sendResponse($response);


        }else{

            
        }
        
    }

    public function syncStock()
    {

        $postData = $this->getJsonRequest();
        $arraySyncedStock = $this->manageUnsyncedStock($postData, true);
        $response = array("status" => true, "message" => "success", "data" => $arraySyncedStock);

        $this->sendResponse($response);
    }

    private function manageUnsyncedStock($postData, $returnItems = false, $client = null)
    {
        $rawData = $this->getArrayValue($postData, "nonsynced_stocks");

        $employeeID = $this->getArrayValue($postData, "employee_id");
        $companyID = $this->getArrayValue($postData, "company_id"); 
        if (empty($rawData)) {
            return $returnItems ? array() : false;
        }

        $arraySyncedData = [];
        $data = json_decode($rawData, true);
        foreach ($data as $key => $stockData) {

            $uniqueID = $this->getArrayValue($stockData,"unique_id");
            $companyID = $this->getArrayValue($stockData, "company_id");
            $stockID = "";//$this->getArrayValue($stockData, "return_id");
            $clientID = $this->getArrayValue($stockData,"client_id");
            $employeeID = $this->getArrayValue($stockData, "employee_id");
            $stockDate = $this->getArrayValue($stockData,"stock_date");
            $stockDateUnix = $this->getArrayValue($stockData,"stock_date_unix");
            
            $stocks = Stock::updateOrCreate(['id'=>$stockID],['company_id'=>$companyID,'client_id'=>$clientID,'employee_id'=>$employeeID,'stock_date'=>$stockDate,'stock_date_unix'=>$stockDateUnix]);

            $stockDetail = $this->getArrayValue($stockData,"sproducts");
            $stockDetails = json_decode($stockDetail, true);
            $stockID = $stocks->id;
            $batchArray = array();
            foreach($stockDetails as $stockDetail){
                $sdData = array(
                    'stock_id'=>$stockID,
                    'product_id'=>$this->getArrayValue($stockDetail,'product_id'),
                    'product_name'=>$this->getArrayValue($stockDetail,'product_name'),
                    'variant_id'=>$this->getArrayValue($stockDetail,'variant'),
                    'variant_name'=>$this->getArrayValue($stockDetail,'variant_name'),
                    'unit_id'=> $this->getArrayValue($stockDetail,'unit'),
                    'unit_name'=>$this->getArrayValue($stockDetail,'unit_name'),
                    'unit_symbol'=>$this->getArrayValue($stockDetail,'unit_symbol'),
                    'quantity'=>$this->getArrayValue($stockDetail,'quantity'),
                    'image'=>$this->getArrayValue($stockDetail,'image'),
                    'image_path'=>$this->getArrayValue($stockDetail,'image_path'),
                    'mfg_date'=>$this->getArrayValue($stockDetail,'mfg_date'),
                    'batch_no'=>$this->getArrayValue($stockDetail,'batch_no'),
                    'expiry_date'=>getArrayValue($stockDetail,'expiry_date'),
                    'created_at'=>$stocks->created_at
                );
                array_push($batchArray,$sdData);
            }
            $batchSaved = DB::table('stock_details')->insert($batchArray);

            $tempArray = array();
            $tempArray['uniqueID'] = $uniqueID;
            $tempArray['stockID'] = $stocks->id;


            array_push($arraySyncedData,$tempArray);
        }
        return $returnItems ? $arraySyncedData : false;
    }




    /****
        Tour Plans Related
    ****/
    public function fetchTourPlans($return = false,$postData = null)
    {
        $postData = $return?$postData:$this->getJsonRequest();
        $companyID = $this->getArrayValue($postData, "company_id");
        $employeeID = $this->getArrayValue($postData, "employee_id");
        $offset = $this->getArrayValue($postData, "offset",0);
        $limit = $this->getArrayValue($postData, "limit",200);

         /*Check if unsynced data is available . if available first update to tha database */
        $syncStatus = $this->manageUnsyncedTourPlans($postData);

        if($employeeID){
            $tour_plans = TourPlan::where('company_id', $companyID)
                ->where('employee_id',$employeeID)
                ->get()->toArray();
        }else{
            $tour_plans = TourPlan::where('company_id', $companyID)
                ->get()->toArray();
        }

        $response = array("status" => true, "message" => "Success", "data" => $tour_plans);
        if($return){

            return $tour_plans;

        } else {

            $this->sendResponse($response);
        }
    }

    public function saveTourPlans($return=false,$postData = null)
    {
        $postData = $this->getJsonRequest();
        $companyID = $this->getArrayValue($postData, "company_id");
        $employeeID = $this->getArrayValue($postData, "employee_id");
        $encodedTourPlan = $this->getArrayValue($postData,"tourplan");
        $decodedTourPlan = json_decode($encodedTourPlan,true);
        $uniqueID = $this->getArrayValue($decodedTourPlan, "unique_id");
        $tourPlanID = $this->getArrayValue($decodedTourPlan, "tour_plan_id");
        
        $tourPlanData = array(
            'unique_id' => $uniqueID,
            'company_id' => $this->getArrayValue($decodedTourPlan, "company_id"),
            'employee_id' => $this->getArrayValue($decodedTourPlan, "employee_id"),
            'start_date' => $this->getArrayvalue($decodedTourPlan, "start_date"),
            'end_date' => $this->getArrayValue($decodedTourPlan, "end_date"),
            'visit_place' => $this->getArrayValue($decodedTourPlan, "visit_place",""),
            'visit_purpose' => $this->getArrayValue($decodedTourPlan, "visit_purpose",""),
            'status' => $this->getArrayValue($decodedTourPlan, "status"),
            'created_at' => $this->getArrayValue($decodedTourPlan, "created_at")
        );
        //Log::info('info', array('tourPlanData'=>print_r($tourPlanData,true)));

        if(!empty($uniqueID)){
            
            $tourPlan = TourPlan::updateOrCreate(
                [
                    "unique_id" => $uniqueID
                ],
                $tourPlanData
            );

        } elseif(!empty($tourPlanID)){

            $tourPlan = TourPlan::updateOrCreate(
                [
                    "id" => $tourPlanID
                ],
                $tourPlanData
            );
        }
        
        $wasRecentlyCreated = $tourPlan->wasRecentlyCreated;
        $wasChanged = $tourPlan->wasChanged();
        $isDirty = $tourPlan->isDirty();
        $exists = $tourPlan->exists;

        if ($wasRecentlyCreated || $wasChanged || $tourPlan->exists) {

            $msg = "";
            $savedtourPlan = $tourPlanData;
            $savedtourPlan["id"] = $tourPlan->id;
            
            if ($tourPlan->wasRecentlyCreated) {
                
                $msg = "Created Tour Plan";
            } else {

                $msg = "Updated Tour Plan";

            }

            $nSaved = saveAdminNotification($companyID, $employeeID, date("Y-m-d H:i:s"), $msg, "tours", $savedtourPlan);
            $response = array("status" => true, "message" => $msg, "tour_plan_id" => $tourPlan->id);
        } else {
            
            $response = array("status" => false, "message" => "Unable to create/update", "tourplan" => "");
            
        }
        $this->sendResponse($response);

    }

    public function syncTourPlans()
    {

        $postData = $this->getJsonRequest();
        $arraySyncedData = $this->manageUnsyncedTourPlans($postData, true);
        $response = array("status" => true, "message" => "success", "data" => $arraySyncedData);
        $this->sendResponse($response);

    }

    public function manageUnsyncedTourPlans($postData, $returnItems = false)
    {

        $rawData = $this->getArrayValue($postData, "unsynced_data");
        $companyID = $this->getArrayValue($postData, "company_id");
        $employeeID = $this->getArrayValue($postData, "employee_id");

        if (empty($rawData)) {

            return $returnItems ? array() : false;
        }
        $data = json_decode($rawData, true);

        $arraySyncedData = array();

        /*prepare data for saving*/
        foreach ($data as $key => $value) {

            $tempArray["unique_id"] = $this->getArrayValue($value, "unique_id");
            $tempArray["company_id"] = $companyID;
            $tempArray["employee_id"] = $employeeID;
            $tempArray["start_date"] = $this->getArrayvalue($value, "start_date");
            $tempArray["end_date"] = $this->getArrayvalue($value, "end_date");
            $tempArray["visit_place"] = $this->getArrayvalue($value, "visit_place");
            $tempArray["visit_purpose"] = $this->getArrayvalue($value, "visit_purpose");
            $tempArray["status"] = $this->getArrayvalue($value, "status");
            $tempArray["created_at"] = $this->getArrayvalue($value, "created_at");

            $savedID = DB::table('tourplans')->insertGetId($tempArray);

            if (!empty($savedID)) {

                $syncedData = $tempArray;
                $syncedData['id'] = $savedID;
                array_push($arraySyncedData, $syncedData);
                //saveAdminNotification($companyID, $employeeID, date("Y-m-d H:i:s"), "Added TourPlan", "tourplan", $syncedData);
            }
        }

        return $returnItems ? $arraySyncedData : true;
    }

    /***
     * Tour Plan End
    ***/











     /**
     * Activities  Related
     */
    public function fetchEmployees($return=false,$postData = null)
    {

        $postData = $return?$postData:$this->getJsonRequest();
        $companyID = $this->getArrayValue($postData, "company_id");
        $offset = $this->getArrayValue($postData, "offset",0);
        $limit = $this->getArrayValue($postData, "limit",200);

        $employees = Employee::select('employees.*', 'employeegroups.name as group_name')
            ->leftJoin('employeegroups', 'employeegroups.id', '=', 'employeegroup')
            ->where('employees.status','Active')
            ->where("employeegroups.company_id", $companyID)->offset($offset)->limit($limit)->get()->toArray();
        //Log::info('info', array("data "=>print_r($employees,true)));

        $response = array("status" => true, "message" => "Success", "data" => $employees);
        if($return){
            return $employees;
        } else {
            $this->sendResponse($response);
        }
    }


    public function fetchActivities($return = false, $postData = null)
    {
        $postData = $return?$postData:$this->getJsonRequest();
        $companyID = $this->getArrayValue($postData, "company_id");
        $assignedTo = $this->getArrayValue($postData, "assigned_to");
        $createdBy = $this->getArrayValue($postData, "created_by");

        $offset = $this->getArrayValue($postData, "offset",0);
        $limit = $this->getArrayValue($postData, "limit",200);

        $activities = Activity::select('activities.*', 'activity_types.id as activity_type_id','activity_types.name as type_name','activity_priorities.id as activity_priority_id','activity_priorities.name as priority_name')
            ->leftJoin('activity_types', 'activity_types.id', '=', 'activities.type')
            ->leftJoin('activity_priorities', 'activity_priorities.id', '=', 'activities.priority')
            ->where("activities.company_id", $companyID)
            ->where(function($query) use($createdBy,$assignedTo){
                    $query = $query->orWhere("activities.assigned_to", $assignedTo)->orWhere("activities.created_by",$createdBy);
            });
        $activities = $activities->offset($offset)->limit($limit)->get()->toArray();
        // Log::info('info', array("data "=>print_r($activities,true)));
        $response = array("status" => true, "message" => "Success", "data" => $activities);
        if($return){
            return $activities;
        } else {
            $this->sendResponse($response);
        }
    }

    public function deleteActivity(){

        $postData = $this->getJsonRequest();
        //Log::info('info', array("postData inside deleteActivity"=>print_r($postData,true)));
        $activityID = getArrayValue($postData,"activity_id");
        $companyID = getArrayValue($postData,"company_id");
        $employeeID = getArrayValue($postData,"employee_id");
        $activity = Activity::where('company_id',$companyID)->where('created_by',$employeeID)->where('id',$activityID)->first();
        ////Log::info('info', array("activity"=>print_r($activity,true)));
        $response = array("status" => false, "message" => "Delete Fail");

        if(!empty($activity)){

            $deleted = $activity->delete();
            if($deleted){

                //$nSaved = saveAdminNotification($companyID, $employeeID, date("Y-m-d H:i:s"), "Your activity has been deleted", "Activity", $activity);
                $createdBy = $activity->created_by;
                $assignedTo = $activity->assigned_to;

                if($employeeID == $createdBy){
                    $notifyTo = $assignedTo;
                } else if($employeeID == $assignedTo){
                    $notifyTo = $createdBy;
                }

                if(($employeeID == $createdBy) && ($createdBy == $assignedTo)){

                    $notifyTo = null;
                    $nSaved = saveAdminNotification($companyID, $employeeID, date("Y-m-d H:i:s"), "Deleted Activity", "activities", $activity);
                } 

                $sendNotification = ($notifyTo != 0) && (!empty($notifyTo));

                if($sendNotification){
                    //send notification to employee
                    //Log::info('info', array("notifyTo"=>print_r($notifyTo,true)));
                    $fbID = getFBIDs($companyID, null, $notifyTo);
    
                    $notificationData = array(
                        "company_id" => $companyID,
                        "employee_id" => $assignedTo,
                        "data_type" => "activity",
                        "data" => "",
                        "action_id" => $activity->id,
                        "title" => "Your activity has been deleted.",
                        "description" => $activity->note,
                        "created_at" => date('Y-m-d H:i:s'),
                        "status" => 1,
                        "to" => 1,
                        "unix_timestamp" => time()
                    );
    
                    $activity->company_name = getObjectValue($activity->client,"company_name","N/A");
                    $activity->priority_name = getObjectValue($activity->activityPriority,"name","");
                    $activity->type_name = getObjectValue($activity->activityType,"name","");
    
                    $dataPayload = array("data_type" => "activity", "activity" => $activity, "action" => "delete");
                    $sent = sendPushNotification_($fbID, 17, $notificationData, $dataPayload);
    
                }
    
                
                $response = array("status" => true, "message" => "Successfully Deleted", "activity_id" => $activity->id);


            } else {

                $response = array("status" => true, "message" => "Delete Fail", "activity_id" => $activity->id);
            }
        }

        $this->sendResponse($response);   
    }

    public function fetchActivityTypes($return = false, $postData = null){
        $postData = $return?$postData:$this->getJsonRequest();
        $companyID = $this->getArrayValue($postData, "company_id");
        $offset = $this->getArrayValue($postData, "offset",0);
        $limit = $this->getArrayValue($postData, "limit",200);

        $activity_types = DB::table('activity_types')
            ->select('activity_types.*')
            ->where("activity_types.company_id", $companyID)
            ->offset($offset)
            ->limit($limit)
            ->get()->toArray();
        $response = array("status" => true, "message" => "Success", "data" => $activity_types);
        if($return){
            return $activity_types;
        } else {
            $this->sendResponse($response);
        }
    }

    public function fetchActivityPriorities($return = false, $postData = null){
        $postData = $return?$postData:$this->getJsonRequest();
        $companyID = $this->getArrayValue($postData, "company_id");
        $offset = $this->getArrayValue($postData, "offset",0);
        $limit = $this->getArrayValue($postData, "limit",200);

        $activity_priorities = DB::table('activity_priorities')
            ->select('activity_priorities.*')
            ->where("activity_priorities.company_id", $companyID)
            ->offset($offset)
            ->limit($limit)
            ->get()->toArray();
        $response = array("status" => true, "message" => "Success", "data" => $activity_priorities);
        if($return){
            return $activity_priorities;
        } else {
            $this->sendResponse($response);
        }
    }

    public function fetchClientSetting($return = false,$postData = null){
        $postData = $return?$postData:$this->getJsonRequest();
        $companyID = $this->getArrayValue($postData,"company_id");
        $clientSettings = DB::table('client_settings')->where('company_id',$companyID)->first();
        $response = array("status" => true, "message" => "Success", "data" => $clientSettings);
        if($return){
            return $clientSettings;
        } else {
            $this->sendResponse($response);
        }
    }

    public function saveActivity($postData = null)
    {
        $postData = $this->getJsonRequest();
        //Log::info('info', array("postData"=>print_r($postData,true)));
        $activityId = $this->getArrayValue($postData,"activity_id");
        $companyID = $this->getArrayValue($postData,"company_id");
        $employeeID = $this->getArrayValue($postData,"employee_id");

        if($activityId!=null){
          $activity = Activity::where('id',$activityId)->where('company_id',$companyID)->first();
          $title = "Updated Activity";
          $created = false;
        }else{
          $activity = new Activity();
          $activity->created_by = $this->getArrayValue($postData,"created_by");
          $title = "Added Activity";
          $created = true;          
        }
        $activity->title = $this->getArrayValue($postData,"title");
        $activity->type = $this->getArrayValue($postData,"type");
        $activity->note = $this->getArrayValue($postData,"notes");   
        $activity->unique_id = $this->getArrayValue($postData,"unique_id");   
        $activity->start_datetime = $this->getArrayValue($postData,"start_datetime");
        $activity->duration = $this->getArrayValue($postData,"duration");
        $activity->priority = $this->getArrayValue($postData,"priority");
        $activity->assigned_to = $this->getArrayValue($postData,"assigned_to");
        $activity->company_id = $companyID;
        $completion_datetime = $this->getArrayValue($postData,"completion_datetime");
        $activity->client_id = $this->getArrayValue($postData,"client_id");      
        $activity->completion_datetime = $this->getArrayValue($postData,"completion_datetime");      
        $activity->save();

        $createdBy = $activity->created_by;
        $assignedTo = $activity->assigned_to;

        if($employeeID == $createdBy){
            $notifyTo = $assignedTo;
        } else if($employeeID == $assignedTo){
            $notifyTo = $createdBy;
        }

        if(($employeeID == $createdBy) && ($createdBy == $assignedTo)){

            $notifyTo = null;
            $nSaved = saveAdminNotification($companyID, $employeeID, date("Y-m-d H:i:s"), $title, "activities", $activity);
        } 

        $sendNotification = ($notifyTo != 0) && (!empty($notifyTo));


        if($created){

            if($sendNotification){
                //send notification to employee
                $fbID = getFBIDs($companyID, null, $notifyTo);

                $notificationData = array(
                    "company_id" => $companyID,
                    "employee_id" => $assignedTo,
                    "data_type" => "activity",
                    "data" => "",
                    "action_id" => $activity->id,
                    "title" => "A new activity has been assigned to you",
                    "description" => $activity->title,
                    "created_at" => date('Y-m-d H:i:s'),
                    "status" => 1,
                    "to" => 1,
                    "unix_timestamp" => time()
                );

                $activity->company_name = getObjectValue($activity->client,"company_name","N/A");
                $activity->priority_name = getObjectValue($activity->activityPriority,"name","");
                $activity->type_name = getObjectValue($activity->activityType,"name","");

                $dataPayload = array("data_type" => "activity", "activity" => $activity, "action" => "add");
                $sent = sendPushNotification_($fbID, 17, $notificationData, $dataPayload);

            }

        } else {

            if($sendNotification){
                //send notification to employee
                $fbID = getFBIDs($companyID, null, $notifyTo);

                $notificationData = array(
                    "company_id" => $companyID,
                    "employee_id" => $assignedTo,
                    "data_type" => "activity",
                    "data" => "",
                    "action_id" => $activity->id,
                    "title" => "Activity has been Updated",
                    "description" => $activity->note,
                    "created_at" => date('Y-m-d H:i:s'),
                    "status" => 1,
                    "to" => 1,
                    "unix_timestamp" => time()
                );

                $activity->company_name = getObjectValue($activity->client,"company_name","N/A");
                $activity->priority_name = getObjectValue($activity->activityPriority,"name","");
                $activity->type_name = getObjectValue($activity->activityType,"name","");

                $dataPayload = array("data_type" => "activity", "activity" => $activity, "action" => "update");
                $sent = sendPushNotification_($fbID, 17, $notificationData, $dataPayload);

            }

        }


        $response = array("status" => true, "message" => "Success", "data" => $activity);
        $this->sendResponse($response);
    }

    public function syncActivities()
    {
        $postData = $this->getJsonRequest();
        //Log::info('info', array("data "=>print_r($postData,true)));
        $arraySyncedData = $this->manageUnsyncedActivities($postData, true);
        $response = array("status" => true, "message" => "success", "data" => $arraySyncedData);
        $this->sendResponse($response);
    }

    public function manageUnsyncedActivities($postData, $returnItems = false)
    {
        $rawData = $this->getArrayValue($postData, "nonsynced_activities");
        $employeeID = $this->getArrayValue($postData, "employee_id");
        $companyID = $this->getArrayValue($postData, "company_id");
        
        if (empty($rawData)) {
            return $returnItems ? array() : false;
        }

        $arraySyncedData = [];
        $data = json_decode($rawData, true);
        foreach ($data as $key => $u_activity) {

            $unique_id = $this->getArrayValue($u_activity, "unique_id");   
            $activity_id = $this->getArrayValue($u_activity, "actvity_id");
            $completion_datetime =  $this->getArrayValue($u_activity, "completionDateTimeString");
            if($completion_datetime==""){
              $completion_datetime=null;
            }
            //Log::info('info', array("data completion_datetime before"=>print_r($completion_datetime,true)));
            $activityData = array(       
                "unique_id" => $unique_id,
                "type" => $this->getArrayValue($u_activity, "activity_type_id"),
                "title" => $this->getArrayValue($u_activity, "title"),
                "note" => $this->getArrayValue($u_activity, "note"),
                "start_datetime" => $this->getArrayValue($u_activity, "startDateTimeString"),
                "duration" => $this->getArrayValue($u_activity, "duration"),
                "priority" => $this->getArrayValue($u_activity, "priority_id"),
                "assigned_to" => $this->getArrayValue($u_activity, "assignee_id"),
                "created_by" => $this->getArrayValue($u_activity, "assigner_id"),
                "client_id" => $this->getArrayValue($u_activity, "client_id"),
                "company_id" => $companyID,
                "completion_datetime" => $completion_datetime,
            );
            //Log::info('info', array("data completion_datetime after"=>print_r($completion_datetime,true)));

            $activity = Activity::updateOrCreate(
                [
                "id" => $activity_id,
                ],
                $activityData
            );

        
        
            $wasRecentlyCreated = $activity->wasRecentlyCreated;
            $wasChanged = $activity->wasChanged();
            $isDirty = $activity->isDirty();
            $exists = $activity->exists;

            if ($wasRecentlyCreated || $wasChanged || $activity->exists) {

                array_push($arraySyncedData, $activity);

                $title = "";
                $savedActivity = $activityData;
                $savedActivity["id"] = $activity->id;
                
                if ($activity->wasRecentlyCreated) {
                    
                    $title = "Added Activity";
                } else {

                    $title = "Updated Activity";
                }

                $nSaved = saveAdminNotification($companyID, $employeeID, date("Y-m-d H:i:s"), $title, "activities", $savedActivity);
            
            }
        }
        return $returnItems ? $arraySyncedData : false;
    }

    public function sync()
    {

        $postData = $this->getJsonRequest();
        $firstFetch =
        $locationSynced = $this->syncLocation($postData);
        $arrayAttendance = $this->syncAttendance($postData);
        $arrayClient = $this->syncClient($postData);

        $response = array(
            "status" => true,
            "message" => "Successfully Synced",
            "attendance" => $arrayAttendance,
            "location_sync" => $locationSynced,
            "clients" => $arrayClient,
            "data" => array()
        );

        //Log::info('info', array("response to being sent inside sync"=>print_r($response,true)));
        $this->sendResponse($response);
    }

    public function syncAttendance($postData)
    {

        $rawData = $this->getArrayValue($postData, "attendance");
        if (empty($rawData)) return array();

        $companyID = $this->getArrayValue($postData, "company_id");
        $employeeID = $this->getArrayValue($postData, "employee_id");

        if (!empty($rawData)) {
            $data = json_decode($rawData, true);

            /*prepare data for saving*/
            $attenArray = array();
            foreach ($data as $key => $value) {
                //Log::info('info', array("value"=>print_r($value,true)));
                $companyID = $this->getArrayValue($value, "company_id");
                $employeeID = $this->getArrayValue($value, "employee_id");
                $uniqueID = $this->getArrayValue($value, "unique_id");
                $checkType = $this->getArrayValue($value, "check_type");
                $createdAt = $this->getArrayValue($value, "created_at");
                $latitude = $this->getArrayValue($value, "latitude");
                $longitude = $this->getArrayValue($value, "longitude");
                $ip = $this->getArrayValue($value, "ip");
                $remark = $this->getArrayValue($value, "remark");
                $device = $this->getArrayValue($value, "device");


                $temp = array();
                $temp["unique_id"] = $uniqueID;
                $temp["company_id"] = $companyID;
                $temp["employee_id"] = $employeeID;
                $temp["check_type"] = $checkType;
                $temp["created_at"] = $createdAt;
                $temp["check_datetime"] = $createdAt;
                $temp["remark"] = $remark;
                $temp["latitude"] = $latitude;
                $temp["longitude"] = $longitude;
                $temp["ip"] = $ip;
                $temp["remark"] = $remark;
                $temp["device"] = $device;
                array_push($attenArray, $temp);
            }

            $synced = DB::table('attendances')->insert($attenArray);
        }


        $arrayAttendance = DB::table('attendances')->where(
            array(
                array("company_id", "=", $companyID),
                array("employee_id", "=", $employeeID)
            )
        )->get()->toArray();
        return $arrayAttendance;
    }

    private function syncLocation($postData)
    {
        $rawData = $this->getArrayValue($postData, "locations");
        if (empty($rawData)) return false;
        $data = json_decode($rawData, true);
        //Log::info('info', array("synced Location"=>print_r($data,true)));

        /*prepare data for saving*/
        $finalArray = array();
        foreach ($data as $key => $value) {

            $uniqueID = $this->getArrayValue($value, "unique_id");
            $companyID = $this->getArrayValue($value, "company_id");
            $employeeID = $this->getArrayValue($value, "employee_id");
            $createdAt = $this->getArrayValue($value, "created_at");
            $latitude = $this->getArrayValue($value, "latitude");
            $longitude = $this->getArrayValue($value, "longitude");
            $altitude = $this->getArrayValue($value, "altitude");
            $unixTimestamp = $this->getArrayValue($value, "unix_time");


            $temp = array();

            $temp["unique_id"] = $uniqueID;
            $temp["company_id"] = $companyID;
            $temp["employee_id"] = $employeeID;
            $temp["created_at"] = $createdAt;
            $temp["latitude"] = $latitude;
            $temp["longitude"] = $longitude;
            $temp["altitude"] = $altitude;
            $temp["unix_timestamp"] = $unixTimestamp;
            array_push($finalArray, $temp);
        }


        $synced = DB::table('locations')->insert($finalArray);
        if ($synced) return true;
        return false;

    }

    private function syncClient($postData)
    {

        $rawData = $this->getArrayValue($postData, "clients");
        if (empty($rawData)) return array();

        $companyID = $this->getArrayValue($postData, "company_id");
        $employeeID = $this->getArrayValue($postData, "employee_id");

        if (!empty($rawData)) {
            $data = json_decode($rawData, true);

            /*prepare data for saving*/
            $clientArray = array();
            foreach ($data as $key => $value) {
                //Log::info('info', array("value"=>print_r($value,true)));
                $companyID = $this->getArrayValue($value, "company_id");
                $employeeID = $this->getArrayValue($value, "employee_id");

                $uniqueID = $this->getArrayValue($value, "unique_id");
                $name = $this->getArrayValue($value, "name");
                $clientCode = $this->getArrayValue($value, "client_code");
                $mobile = $this->getArrayValue($value, "mobile");
                $email = $this->getArrayValue($value, "email");
                $createdAt = $this->getArrayValue($value, "created_at");


                $temp = array();
                $temp["unique_id"] = $uniqueID;
                $temp["company_id"] = $companyID;
                $temp["name"] = $name;
                $temp["client_code"] = $clientCode;
                $temp["email"] = $email;
                $temp["created_at"] = $createdAt;
                $temp["mobile"] = $mobile;
                $temp["phone"] = $mobile;
                array_push($clientArray, $temp);
            }

            $synced = DB::table('clients')->insert($clientArray);
        }


        $arrayClients = DB::table('clients')->where(
            array(
                array("company_id", "=", $companyID)
            )
        )->get()->toArray();
        return $arrayClients;
    }

    public function fetchCommonData()
    {

        $postData = $this->getJsonRequest();
        $companyID = $this->getArrayValue($postData, "company_id");
        $employeeID = $this->getArrayValue($postData, "employee_id");
        $tables = $this->getArrayValue($postData, "tables");
        $exploded = explode(",", $tables);
        //Log::info('info', array("postData in FetchCommondata"=>print_r($postData,true)));

        $data = array();
        $data["clients"] = in_array("clients", $exploded) ? $this->fetchClients(true, $postData) : array();
        $data["products"] = in_array("products", $exploded) ? $this->getProducts($companyID) : array();
        $data["orders"] = in_array("orders", $exploded) ? $this->fetchOrders(true, $postData) : array();
        $data["noorders"] = in_array("no_orders", $exploded) ? $this->fetchNoOrder(true, $postData) : array();
        $data["collections"] = in_array("collections", $exploded) ? $this->fetchCollection(true, $postData) : array();
        $data["meetings"] = in_array("meetings", $exploded) ? $this->fetchMeeting(true, $postData) : array();
        $data["leaves"] = in_array("leaves", $exploded) ? $this->fetchLeave(true, $postData) : array();
        $data["expenses"] = in_array("expenses", $exploded) ? $this->fetchExpense(true, $postData) : array();
        $data["tasks"] = in_array("tasks", $exploded) ? $this->fetchTask(true, $postData) : array();
        $data["holidays"] = in_array("holidays", $exploded) ? $this->fetchHolidays(true, $postData) : array();
        $data["tourplans"] = in_array("tour_plans", $exploded) ? $this->fetchTourPlans(true, $postData) : array();
        $data["leave_types"] = in_array("leave_type", $exploded) ? $this->fetchLeaveTypes(true, $postData) : array();
        //Log::info('info', array("data"=>print_r($data,true)));
        $getUnsentAnnouncement = DB::table('unsent_announcement')->where('employee_id', $employeeID)->pluck('announcement_id')->toArray();
        if (!empty($getUnsentAnnouncement)) {
            $fbIDs = DB::table('employees')->where(array(array('company_id', $companyID), array('status', 'Active')))->where('id', $employeeID)->whereNotNull('firebase_token')->pluck('firebase_token');
            if (!empty($fbIDs)) {
                $fetchAnnouncements = DB::table('announcements')->whereIn('id', $getUnsentAnnouncement)->get();
                if(!empty($fetchAnnouncements)){
                  foreach ($fetchAnnouncements as $fetchAnnouncement) {
                      $notificationData = array(
                      'company_id' => $companyID,
                      'employee_id' => $employeeID,
                      'title' => $fetchAnnouncement->title,
                      'description' => $fetchAnnouncement->description,
                      'created_at' => date('Y-m-d H:i:s'),
                      'status' => 1,
                      'to' => 1
                  );
                  $sendingNotificationData = $notificationData;
                  $sendingNotificationData['unix_timestamp'] = time(); //need to manage server/client
                  $sent = sendPushNotification_($fbIDs, 6, $sendingNotificationData, null);
                  $sentStatus = json_decode($sent);
                  if ($sentStatus->success == 1) {
                    DB::table('unsent_announcement')->where('employee_id', $employeeID)->where('announcement_id', $fetchAnnouncement->id)->delete();
                  }
                }
              }
            }
        }

        $response = array("status" => true, "message" => "Success", "data" => $data);
        $this->sendResponse($response);
    }


    public function fetchPartyFormDependentData()
    {

        $postData = $this->getJsonRequest();
        $companyID = $this->getArrayValue($postData, "company_id");
        $partyTypes = DB::table('partytypes')->where('company_id', $companyID)->get();
        $marketAreas = DB::table('marketareas')->where('company_id', $companyID)->where('allow_salesman', 1)->get();

        $tempObj = new stdClass();
        $tempObj->party_types = empty($partyTypes) ? null : json_encode($partyTypes);
        $tempObj->market_areas = empty($marketAreas) ? null : json_encode($marketAreas);
        $response = array("status" => true, "message" => "Success", "data" => $tempObj);
        $this->sendResponse($response);
    }

    public function fetchCollectionFormDependentData()
    {

        $postData = $this->getJsonRequest();
        $companyID = $this->getArrayValue($postData, "company_id");

        $banks = DB::table('banks')->where('company_id', $companyID)->get();
        $tempObj = new stdClass();
        $tempObj->banks = empty($banks) ? null : json_encode($banks);
        $response = array("status" => true, "message" => "Success", "data" => $tempObj);
        $this->sendResponse($response);
    }

    public function fetchOrderFormDependentData()
    {

        $postData = $this->getJsonRequest();
        $companyID = $this->getArrayValue($postData, "company_id");
        $offset = $this->getArrayValue($postData, "offset",0);
        $limit = $this->getArrayValue($postData, "limit",100);

        $unitTypes = DB::table('unit_types')->where([['status', '=', 'Active'],['company_id', '=', $companyID]])->offset($offset)->limit($limit)->get()->toArray();
        $response = array("status" => true, "message" => "Success", "data" => $unitTypes);
        $this->sendResponse($response);
    }

    public function fetchHolidays($return = false, $postData=null)
    {

        $postData = $return?$postData:$this->getJsonRequest();
        $companyID = $this->getArrayValue($postData, "company_id");
        $offset = $this->getArrayValue($postData, "offset",0);
        $limit = $this->getArrayValue($postData, "limit",1500);

        $holidays = DB::table('holidays')->where([['company_id', '=', $companyID]])->whereNull("deleted_at")->offset($offset)->limit($limit)->get()->toArray();
        $response = array("status" => true, "message" => "Success", "data" => $holidays);
        if($return){

            return $holidays;

        } else {

            $this->sendResponse($response);
        } 
    }


    private function getProductCategories($companyID = null)
    {
        if ($companyID) {
            $categories = DB::table('categories')->where("company_id", $companyID)->get()->toArray();
        } else {
            $categories = DB::table('categories')->get()->toArray();
        }

        //Log::info('info', array("categories"=>print_r($categories,true)));

        return $categories;
    }

    /**
     * Fetch Products
     * @param null $companyID
     * @return array|null
     */
    private function getProducts($companyID = null)
    {
        if (empty($companyID)) return null;

        //todo need to be managed properly with other parameters like limit
        $finalArray = array();

        $products = DB::table('products')
            ->select('products.*', 'categories.name as category_name','brands.name as brand_name', 'unit_types.name as unit_name', 'unit_types.symbol as unit_symbol')
            ->leftJoin('brands', 'brands.id', '=', 'products.brand')
            ->leftJoin('categories', 'categories.id', '=', 'products.category_id')
            ->leftJoin('unit_types', 'unit_types.id', '=', 'products.unit')
            ->where("products.company_id", $companyID)
            ->where("products.status", "Active")
            ->get()->toArray();

        $pv = DB::table('product_variants')
            ->select('product_variants.*', 'unit_types.name as unit_name', 'unit_types.symbol as unit_symbol')
            ->leftJoin('unit_types', 'unit_types.id', '=', 'product_variants.unit')
            ->where("product_variants.company_id", $companyID)
            ->get()->toArray();

        $pvGroupedByProductID = arrayGroupBy($pv,"product_id",true); 


        foreach ($products as $key => $value) {
            $tempObj = $value;
            $tempPVProductID = getObjectValue($value,"id");
            $tempObj->product_variants = getArrayValue($pvGroupedByProductID,$tempPVProductID);
            array_push($finalArray,$tempObj);
        }   

        return $finalArray;

        //return $products;
    }

    /**
     * @param null $companyID
     * @return array|null
     */
    // private function getEmployees($companyID = null)
    // {
    //     if (empty($companyID)) return null;

    //     $employees = DB::table('employees')
    //         ->select('employees.*', 'categories.name as category_name')
    //         ->leftJoin('employeegroups', 'employeegroups.id', '=', 'employeegroup')
    //         ->where("employeegroup.company_id", $companyID)->get()->toArray();
    //     return $employees;
    // }


    /**
     * @param null $companyID
     * @return mixed
     */
    private function getLeaveTypes($companyID = null)
    {
        $leaveTypes = Cache::rememberForever('cache_leavetypes', function () {
            //Log::info('info', array("mCacheMissed for Levave Type Data"=>print_r("",true)));
            return DB::table('leave_type')->get()->toArray();
        });

        return $leaveTypes;
    }

    private function getCountries()
    {

        $countryData = Cache::rememberForever('countryData', function () {
            //Log::info('info', array("mCacheMissed for country Data"=>print_r("",true)));

            $countries = DB::table('countries')->get()->toArray();

            $states = DB::table('states')->get()->toArray();
            $states = arrayGroupBy($states, 'country_id', true);

            $cities = DB::table('cities')->get()->toArray();
            $cities = arrayGroupBy($cities, 'state_id', true);

            $finalArray = array();

            foreach ($countries as $key => $country) {

                $tempStates = $states[$country->id];
                $tempCities = array();
                $tempArrayStates = array();

                foreach ($tempStates as $key => $tempState) {
                    $tempState->cities = isset($cities[$tempState->id]) ? $cities[$tempState->id] : array();
                    //Log::info('info', array("tempState"=>print_r($tempState,true)));
                    array_push($tempArrayStates, $tempState);
                    $tempCities[$tempState->id] = isset($cities[$tempState->id]) ? $cities[$tempState->id] : array();
                }

                $country->states = $tempArrayStates;
                $country->cities = $tempCities;
                array_push($finalArray, $country);
            }
            return $finalArray;
        });
        return $countryData;
    }


    /**
     * *************************************************
     * ***************common section********************
     * *************************************************
     */


    /**
     * @param $response
     */
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

    private function getArrayValue($arraySource, $key, $emptyText = null, $trim = FALSE)
    {
        if (is_array($arraySource) && !empty($arraySource[$key])) {
            return $trim == TRUE ? trim($arraySource[$key]) : $arraySource[$key];
        } else {
            return $emptyText;
        }
    }

    private function sendEmptyResponse()
    {
        $response = array("status" => true, "message" => "No Record Found", "data" => array());
        echo json_encode($response);
        exit;
    }

    private function checkApiKey($postData)
    {
        return true;
        
        // $apiKey = $this->getArrayValue($postData, "api_key");
        // $companyID = $this->getArrayValue($postData,"company_id");

        // if($apiKey == "qwertyuiopasdfghjklzxcv")return true;


        // $employee = Employee::where('company_id',$companyID)->where('firebase_token',$apiKey)->where('status','Active')->first();

        // if(!empty($employee)){

        //   return true;
        // }else{

        //     $response = array('status' => false, 'message' => 'Invalid API KEY', 'data' => array());
        //     $this->sendResponse($response);
        // }

    }

    private function getImageName()
    {
        $imagePrefix = md5(uniqid(mt_rand(), true));
        $imageName = $imagePrefix . ".png";
        return $imageName;
    }

    private function getImagePath($companyID, $module = "common", $imageName = "")
    {
        if (empty($companyID)) return "";
        $domain = DB::table("companies")->where("id", $companyID)->where("is_active", 2)->pluck("domain")->first();
        if (empty($domain)) return "";

        if (empty($imageName)) {
            $imagePath = "uploads/" . $domain . "/" . $module;
        } else {
            $imagePath = "uploads/" . $domain . "/" . $module . "/" . $imageName;
        }
        return $imagePath;
    }
    

    private function getEmployeeIDs($clientID, $handles)
    {
        $return = null;
        if (empty($clientID) || empty($handles)) return $return;
        if (!isset($handles[$clientID])) return $return;
        $tempArray = array();
        foreach ($handles[$clientID] as $k => $v) {
            array_push($tempArray, $v->employee_id);
        }
        return implode(',', array_unique($tempArray));
    }

    private function combineArrayElements($tempArray)
    {
        $result = array();
        foreach ($tempArray as $key => $value) {
            array_push($result, $value);
        }
        //Log::info('info', array("combinedArrayElement"=>print_r($result,true)));
        return $result;
    }

    
}
