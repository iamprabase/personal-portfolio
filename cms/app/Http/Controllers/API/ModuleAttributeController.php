<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Auth;

class ModuleAttributeController extends Controller
{
    public function __construct()
    {
    	$this->middleware('auth:api');
    }

    public function index(Request $request)
    {
    	$user = Auth::user();
        $companyID = $user->company_id;
        $offset = $this->getArrayValue($request->all(), "offset",0);
        $limit = $this->getArrayValue($request->all(), "limit",200);
        $moduleAttributes = DB::table('module_attributes')->where('company_id',$companyID)->whereNull('deleted_at')->get();
    	$response = array('status'=>true,'message'=>'products','data'=>$moduleAttributes);
    	return response($response);
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
}
