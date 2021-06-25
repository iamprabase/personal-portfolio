<?php

namespace App\Http\Controllers\API;

use DB;
use Auth;
use App\Bank;
use stdClass;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DependentController extends Controller
{
    public function __construct()
    {
		$this->middleware('auth:api');
    }

    public function fetchPartyFormDependentData()
    {

        $postData = $this->getJsonRequest();
        $user = Auth::user();
		$companyID = $user->company_id;
        $partyTypes = DB::table('partytypes')->where('company_id', $companyID)->get();
        $marketAreas = DB::table('marketareas')->where('company_id', $companyID)->get();

        $tempObj = new stdClass();
        $tempObj->party_types = empty($partyTypes) ? null : json_encode($partyTypes);
        $tempObj->market_areas = empty($marketAreas) ? null : json_encode($marketAreas);
        $response = array("status" => true, "message" => "Success", "data" => $tempObj);
        $this->sendResponse($response);
    }

    public function fetchCollectionFormDependentData()
    {

        $postData = $this->getJsonRequest();
        $user = Auth::user();
		$companyID = $user->company_id;
        $banks = Bank::where('company_id', $companyID)->get();
        $tempObj = new stdClass();
        $tempObj->banks = empty($banks) ? null : json_encode($banks);
        $response = array("status" => true, "message" => "Success", "data" => $tempObj);
        $this->sendResponse($response);
    }

    public function fetchOrderFormDependentData()
    {

        $postData = $this->getJsonRequest();
        $user = Auth::user();
		$companyID = $user->company_id;
        $offset = $this->getArrayValue($postData, "offset",0);
        $limit = $this->getArrayValue($postData, "limit",100);

        $unitTypes = DB::table('unit_types')->where([['status', '=', 'Active'],['company_id', '=', $companyID]])->offset($offset)->limit($limit)->get()->toArray();
        $response = array("status" => true, "message" => "Success", "data" => $unitTypes);
        $this->sendResponse($response);
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
