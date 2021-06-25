<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Employee;
use App\Stock;
use App\StockDetail;
use Auth;
use DB;
use StdClass;

class StockController extends Controller
{
	public function __construct()
    {
		$this->middleware('auth:api');
    }

    public function index($return = false, $postData = null)
    {
        $postData = $return?$postData:$this->getJsonRequest();
		$user = Auth::user();
		$companyID = $user->company_id;
		$employee = Employee::where('company_id',$companyID)->where('user_id',$user->id)->first();
		$employeeID = $employee->id;
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
                $tempObj->employee_id = $employeeID;
                $tempObj->company_id = $companyID;
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

    public function store($postData = null)
    {
        $postData = $this->getJsonRequest();
        $stock = $this->getArrayValue($postData,"stock");
        $decodedStock = json_decode($stock,true);
        $sproducts = $this->getArrayValue($decodedStock,"sproducts");
        $stockDetails = json_decode($sproducts,true);
		$user = Auth::user();
		$companyID = $user->company_id;
		$employee = Employee::where('company_id',$companyID)->where('user_id',$user->id)->first();
		$employeeID = $employee->id;
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
                    'image'=>$this->getArrayValue($stockDetail,'image'),
                    'image_path'=>$this->getArrayValue($stockDetail,'image_path'),
                    'mrp'=>$this->getArrayValue($stockDetail,'mrp'),
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
		$user = Auth::user();
		$companyID = $user->company_id;
		$employee = Employee::where('company_id',$companyID)->where('user_id',$user->id)->first();
		$employeeID = $employee->id;
        if (empty($rawData)) {
            return $returnItems ? array() : false;
        }

        $arraySyncedData = [];
        $data = json_decode($rawData, true);
        foreach ($data as $key => $stockData) {

            $uniqueID = $this->getArrayValue($stockData,"unique_id");
            $companyID = $companyID;
            $stockID = "";//$this->getArrayValue($stockData, "return_id");
            $clientID = $this->getArrayValue($stockData,"client_id");
            $employeeID = $employeeID;
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
                    'quantity'=>$this->getArrayValue($stockDetail,'changeInValue'),
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
