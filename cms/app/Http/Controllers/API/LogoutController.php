<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\Employee;
use App\User;
use DB;

class LogoutController extends Controller
{
	public function __construct()
	{
		$this->middleware('auth:api');
	}

    public function logoutPreviousDevice()
    {
        $postData = $this->getJsonRequest();
        $user = Auth::user();
        // $accessToken = Auth::user()->token();
        // $accessToken->revoke();
        $companyID = $user->company_id;
        $employee = Employee::where('company_id',$companyID)->where('user_id',$user->id)->first();
        $employeeID = $employee->id;
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

    public function logout()
    {
      $user = Auth::user();
      $accessToken = $user->token();
      $user->is_logged_in = 0;
      activity()->log('Logged Out', $user->id);
      $user->save();
      $accessToken->revoke();


      $this->sendResponse(array("status" => true, "message" => "Logged Out."));
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
}
