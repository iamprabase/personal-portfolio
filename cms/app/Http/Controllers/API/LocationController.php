<?php

namespace App\Http\Controllers\API;

use DB;
use Auth;
use App\Employee;
use App\RawLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class LocationController extends Controller
{
    public function __construct()
    {
		$this->middleware('auth:api');
    }

    public function index()
    {
        $postData = $this->getJsonRequest();
        $user = Auth::user();
		$companyID = $user->company_id;
		$employee = Employee::where('company_id',$companyID)->where('user_id',$user->id)->first();
		$employeeID = $employee->id;
        $like = $this->getArrayValue($postData, "date") . "%";
        $locations = DB::table("locations")->where("company_id", $companyID)->where("employee_id", $employeeID)->where("created_at", "like", $like)->get()->toArray();

        $response = array("status" => true, "message" => "success", "data" => $locations);
        //Log::info('info', array("locationData"=>print_r($locationData,true)));
        $this->sendResponse($response);
    }

    public function store()
    {
        $postData = $this->getJsonRequest();
        $uniqueID = $this->getArrayValue($postData, "unique_id");
        $user = Auth::user();
		$companyID = $user->company_id;
		$employee = Employee::where('company_id',$companyID)->where('user_id',$user->id)->first();
		$employeeID = $employee->id;
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

    private function manageUnsyncedLocation($postData, $returnItems = false)
    {
        ini_set('serialize_precision', 9);
        ini_set('precision', 9);
        $return = $returnItems ? array() : false;
        $rawData = $this->getArrayValue($postData, "unsynced_data");
        $user = Auth::user();
		$companyID = $user->company_id;
		$employee = Employee::where('company_id',$companyID)->where('user_id',$user->id)->first();
		$employeeID = $employee->id;
        $versionCode = $this->getArrayValue($postData,"version_code");

        //Log::info('info', array("rawData"=>print_r($rawData,true)));

        $data = json_decode($rawData, true);
        if (empty($data)){

            Log::info('info', array('' => print_r('error in:json_decode ', true)));
            return $return;

        }

        $dataGroupedByDate = arrayGroupBy($data, "date");

        foreach ($dataGroupedByDate as $key => $value) {

            $tempDate = $key;
            $decodedContent = array();
            $fileName = getFileName($companyID, $employeeID, $tempDate);
            $isExists = Storage::disk("local")->exists($fileName);
            $fileContent = $isExists ? Storage::get($fileName) : "";
            if (!empty($fileContent)) $decodedContent = json_decode($fileContent);
            if(is_array($decodedContent)){
              $mergedArray = array_merge($decodedContent, $this->combineArrayElements($value));
            } else{
              $mergedArray = $this->combineArrayElements($value);
            } 

            $logValue = $companyID.",".$employeeID.",".$versionCode.",".count($value).",".date("Y-m-d h:m:s");
            $encodedData = json_encode($mergedArray);
            $bytes = Storage::put($fileName, $encodedData);
            if(empty($bytes)){
                $return = $returnItems ? array() : false;
                // Log::info('info', array('error' => print_r("error in Storage::put", true)));
                break;

            } else {

                $return = $returnItems ? array() : true;

            }
        }

        return $return;
    }

    private function manageUnsyncedLocationDB($postData, $returnItems = false)
    {

        $return = false;
        $rawData = $this->getArrayValue($postData, "unsynced_data");
        $user = Auth::user();
		$companyID = $user->company_id;
		$employee = Employee::where('company_id',$companyID)->where('user_id',$user->id)->first();
		$employeeID = $employee->id;

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
                    // Log::info('info', array("Syncing" => print_r("*****************************************************", true)));
                    // Log::info('info', array("companyID" => print_r($tempCompanyID, true)));
                    // Log::info('info', array("employeeID" => print_r($tempEmployeeID, true)));
                    // Log::info('info', array("time2" => print_r($time2, true)));
                    // Log::info('info', array("time1" => print_r($time1, true)));
                    // Log::info('info', array("T Diff" => print_r($time2 - $time1, true)));
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
