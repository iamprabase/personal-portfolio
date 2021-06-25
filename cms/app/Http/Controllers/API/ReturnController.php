<?php

namespace App\Http\Controllers\API;

use DB;
use Auth;
use StdClass;
use App\Employee;
use App\ProductReturn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class ReturnController extends Controller
{
    public function __construct()
    {
		$this->middleware('auth:api');
    }

    public function index($return= false,$postData = null){
        $postData = $return?$postData:$this->getJsonRequest();
		$user = Auth::user();
		$companyID = $user->company_id;
		$employee = Employee::where('company_id',$companyID)->where('user_id',$user->id)->first();
		$employeeID = $employee->id;
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

    public function fetchReturnReason($return = false, $postData = null){

        $postData = $return?$postData:$this->getJsonRequest();
		$user = Auth::user();
		$companyID = $user->company_id;
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

    public function saveReturns($postData=null){

        $postData = $this->getJsonRequest();
        Log::info(array("Online Returns:- ", $postData));
        $prodReturns = $this->getArrayValue($postData,"returnsdata");
        $prodReturn = json_decode($prodReturns, true);
        $returnDetail = $this->getArrayValue($prodReturn,"rproducts");
        $returnDetails = json_decode($returnDetail, true);

        $user = Auth::user();
		$companyID = $user->company_id;
		$employee = Employee::where('company_id',$companyID)->where('user_id',$user->id)->first();
		$employeeID = $employee->id;
        
        $returnID = null;//$this->getArrayValue($prodReturn, "return_id");
        $clientID = $this->getArrayValue($prodReturn,"client_id");
        $superiorClientID = $this->getArrayValue($prodReturn,"superior");
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
        Log::info(array("Returns:- ", $postData));
        $arraySyncedReturns = $this->manageUnsyncedReturns($postData, true);
        $response = array("status" => true, "message" => "success", "data" => $arraySyncedReturns);

        $this->sendResponse($response);
    }

    public function manageUnsyncedReturns($postData, $returnItems)
    {
    	$rawData = $this->getArrayValue($postData, "nonsynced_returns");
    	$user = Auth::user();
    	$companyID = $user->company_id;
    	$employee = Employee::where('company_id',$companyID)->where('user_id',$user->id)->first();
    	$employeeID = $employee->id;
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

        try{
  
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
        }catch(\Exception $e){
          Log::info($uniqueID);
          Log::info($clientID);
        }
    	}
    	return $returnItems ? $arraySyncedData : false;
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
