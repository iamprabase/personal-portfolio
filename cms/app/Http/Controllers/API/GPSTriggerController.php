<?php

namespace App\Http\Controllers\API;

use App\Employee;
use App\GPSTrigger;
use App\Http\Controllers\Controller;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Log;

class GPSTriggerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function sync()
    {
        $postData = $this->getJsonRequest();
        $arraySyncedData = $this->manageUnsyncedGPS($postData, true);
        $response = array("status" => true, "message" => "success", "data" => $arraySyncedData);
        return response($response);
    }

    private function manageUnsyncedGPS($postData, $returnItems = false)
    {
        $user = Auth::user();
        $rawData = $this->getArrayValue($postData, "nonsynced_gpstriggers");
        $employee = Employee::where('user_id',$user->id)->first();
        $empID = $employee->id;
        $company_id = $user->company_id;
        $data = json_decode($rawData, true);
        $arraySyncedData = [];
        if(!empty($data)){
            foreach ($data as $key => $value) {
                $colTempArray["employee_id"] = $empID;
                $colTempArray["company_id"] = $company_id;
                $colTempArray["status"] = $value['status'];
                $colTempArray["trigger_date_time"] = Carbon::createFromTimestampMs($value["trigger_date_time"]);
                $colTempArray['created_at'] = Carbon::now();
                $colTempArray['updated_at'] = Carbon::now();
                array_push($arraySyncedData,$colTempArray);
            }
        }
        GPSTrigger::insert($arraySyncedData);
        return $returnItems ? $arraySyncedData : false;
    }

    private function getJsonRequest($isJson = true)
    {
        if ($isJson) {
            return json_decode($this->getFileContent(), true);
        } else {
            return $_POST;
        }
    }

    private function getArrayValue($arraySource, $key, $emptyText = null, $trim = FALSE)
    {
        if (is_array($arraySource) && !empty($arraySource[$key])) {
            return $trim == TRUE ? trim($arraySource[$key]) : $arraySource[$key];
        } else {
            return $emptyText;
        }
    }

    private function getFileContent()
    {
        return file_get_contents('php://input');
    }

}
