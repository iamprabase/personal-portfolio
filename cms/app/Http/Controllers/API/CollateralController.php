<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\Employee;
use App\CollateralsFile;
use App\CollateralsFolder;
use DB;

class CollateralController extends Controller
{
    public function __construct()
    {
		$this->middleware('auth:api');
    }

    public function fetchCollateralFiles($return = false, $postData = null)
    {
    	$postData = $return?$postData:$this->getJsonRequest();
    	$user = Auth::user();
    	$companyID = $user->company_id;
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
