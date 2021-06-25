<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Holiday;
use Auth;

class HolidayController extends Controller
{
    public function __construct()
    {
		$this->middleware('auth:api');
    }

    public function index($return = false, $postData=null)
    {

        $postData = $return?$postData:$this->getJsonRequest();
        $user = Auth::user();
        $companyID = $user->company_id;
        $offset = $this->getArrayValue($postData, "offset",0);
        $limit = $this->getArrayValue($postData, "limit",1500);

        $holidays = Holiday::where([['company_id', '=', $companyID]])->whereNull("deleted_at")->offset($offset)->limit($limit)->get()->toArray();
        $response = array("status" => true, "message" => "Success", "data" => $holidays);
        if($return){
            return $holidays;

        } else {

            $this->sendResponse($response);
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

}
