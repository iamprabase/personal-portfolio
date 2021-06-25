<?php

namespace App\Http\Controllers\API;

use DB;
use Log;
use Auth;
use App\Employee;
use App\Attendance;
use App\Http\Controllers\Controller;

class AttendanceController extends Controller
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
        $employee = Employee::where('company_id', $companyID)->where('user_id', $user->id)->first();
        $employeeID = $employee->id;

        /*Check if unsynced data is available . if available first update to tha database */
        $syncStatus = $this->manageUnsyncedAttendance($postData);

        $attendances = DB::table('attendances')->where(
            array(
                array("company_id", "=", $companyID),
                array("employee_id", "=", $employeeID),
            )
        )->get()->toArray();

        //Log::info('info', array("attendances"=>print_r($attendances,true)));

        if (empty($attendances)) {
            $this->sendEmptyResponse();
        }

        $response = array("status" => true, "message" => "Success", "data" => $attendances);
        //Log::info('info', array("data"=>print_r($response,true)));
        $this->sendResponse($response);
    }

    public function store()
    {
        $postData = $this->getJsonRequest();
        $uniqueID = $this->getArrayValue($postData, "unique_id");
        $user = Auth::user();
        $companyID = $user->company_id;
        $employee = Employee::where('company_id', $companyID)->where('user_id', $user->id)->first();
        $employeeID = $employee->id;
        $address = $this->getArrayValue($postData, "address");
        $latitude = $this->getArrayValue($postData, "latitude");
        $longitude = $this->getArrayValue($postData, "longitude");

        if (!isset($address) && (isset($latitude) && isset($longitude))) {
            $address = $this->getAddress($latitude, $longitude);
        }
        $attendanceData = array(
            'unique_id' => $uniqueID,
            'company_id' => $companyID,
            'employee_id' => $employeeID,
            'check_datetime' => $this->getArrayValue($postData, "created_at"),
            'adate' => $this->getArrayValue($postData, "adate"),
            'atime' => $this->getArrayValue($postData, "atime"),
            'check_type' => $this->getArrayValue($postData, "check_type"),
            'address' => $address,
            'latitude' => $latitude,
            'longitude' => $longitude,
        );
        try {
            $atten = Attendance::updateOrCreate(
              [
                "unique_id" => $uniqueID,
                "employee_id" => $employeeID,
                "check_type" => $this->getArrayValue($postData, "check_type"),
              ],
              $attendanceData
            );

            if ($atten->wasRecentlyCreated || $atten->wasChanged) {
                $response = array("status" => true, "message" => "successfully saved", "data" => $atten);

            } else {
                $response = array("status" => true, "message" => "not Saved", "data" => "");

            }
        } catch (\Exception $e) {
            Log::error(print_r(array("Attendance of EmployeeID:- " . $employeeID, $e->getCode()), true));
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

    private function manageUnsyncedAttendance($postData, $returnItems = false)
    {

      $return = false;
      $rawData = $this->getArrayValue($postData, "unsynced_data");
      $user = Auth::user();
      $companyID = $user->company_id;
      $employee = Employee::where('company_id', $companyID)->where('user_id', $user->id)->first();
      $employeeID = $employee->id;

      if (empty($rawData)) {
        return $returnItems ? array() : false;
      }

      $data = json_decode($rawData, true);

      $syncedData = array();

      foreach ($data as $key => $value) {

        $uniqueID = $this->getArrayValue($value, "unique_id");
        $address = $this->getArrayValue($value, "address");
        $latitude = $this->getArrayValue($value, "latitude");
        $longitude = $this->getArrayValue($value, "longitude");

        if (!isset($address) && (isset($latitude) && isset($longitude))) {
          $address = $this->getAddress($latitude, $longitude);
        }
        try {
            $atten = Attendance::updateOrCreate(
              [
                "unique_id" => $uniqueID,
                "employee_id" => $employeeID,
                "check_type" => $this->getArrayvalue($value, "check_type"),
              ],
              [
                "unique_id" => $uniqueID,
                "company_id" => $companyID,
                "employee_id" => $employeeID,
                "check_datetime" => $this->getArrayvalue($value, "check_datetime"),
                "adate" => $this->getArrayvalue($value, "adate"),
                "atime" => $this->getArrayvalue($value, "atime"),
                "unix_timestamp" => $this->getArrayvalue($value, "unix_time"),
                "check_type" => $this->getArrayvalue($value, "check_type"),
                "auto_checkout" => $this->getArrayvalue($value, "auto_checkout"),
                "latitude" => $latitude,
                "longitude" => $longitude,
                "address" => $address,
                "device" => $this->getArrayvalue($value, "device"),
              ]
            );
            
            if ($atten->wasRecentlyCreated || $atten->wasChanged || $atten->exists) { //need to add other conditions also
              array_push($syncedData, $atten);
            }
        } catch (\Exception $e) {
          Log::error(print_r(array("Attendance of EmployeeID:- " . $employeeID, $e->getMessage()), true));
        }
        $return = true;
      }

      return $returnItems ? $syncedData : $return;
    }

    private function getAddress($latitude, $longitude)
    {
        $url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng=' . trim($latitude) . ',' . trim($longitude) . '&sensor=false&key=AIzaSyDR6v2elDctrDptLyvTjpTBEs6z7CLSfW8';

        $geocode = @file_get_contents($url);
        $json = json_decode($geocode);
        $status = $json->status;
        if ($status == "OK") {
            return $json->results[0]->formatted_address;
        } else {
            return false;
        }
    }

    //common methods
    private function sendEmptyResponse()
    {
        $response = array("status" => true, "message" => "No Record Found", "data" => array());
        echo json_encode($response);
        exit;
    }

    private function getArrayValue($arraySource, $key, $emptyText = null, $trim = false)
    {
        if (is_array($arraySource) && !empty($arraySource[$key])) {
            return $trim == true ? trim($arraySource[$key]) : $arraySource[$key];
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

}
