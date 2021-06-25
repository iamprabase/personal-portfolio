<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\NoOrder;
use App\Employee;
use App\Client;
use DB;
use Log;

class NoOrderController extends Controller
{
	public function __construct()
    {
		$this->middleware('auth:api');
        $this->middleware('permission:zeroorder-create', ['only' => ['store']]);
        $this->middleware('permission:zeroorder-view');
        $this->middleware('permission:zeroorder-update', ['only' => ['store']]);
        $this->middleware('permission:zeroorder-delete', ['only' => ['destroy']]);
    }

    public function index($return = false, $tempPostData = null)
    {

        $postData   = $return ? $tempPostData : $this->getJsonRequest();
        $user       = Auth::user();
        $companyID  = $user->company_id;
        $employee   = Employee::where('company_id',$companyID)->where('user_id',$user->id)->first();
        $employeeID = $employee->id;

        //Check if unsynced data is available . if available first update to tha database
        $syncStatus = $this->manageUnsyncedNoOrder($postData);

        $noOrders = DB::table('no_orders')->select('no_orders.*','clients.id as clientID','clients.name','clients.company_name')
        			->where('no_orders.company_id',$companyID)->leftJoin('clients','no_orders.client_id','clients.id')->where('no_orders.employee_id',$employeeID)->get()->toArray();

        if (empty($noOrders)) {
            if ($return) {
                return array();
            } else {

                $this->sendEmptyResponse();
            }
        }

        $finalArray = array();
        foreach ($noOrders as $key => $value) {
            
            $imageArray         = getImageArray("noorders", $value->id,$companyID,$employeeID);
            $value->image_ids   = json_encode($this->getArrayValue($imageArray,"image_ids"));
            if($value->image_ids=="null"){
                $value->image_ids = null;
            }
            $value->images      = json_encode($this->getArrayValue($imageArray, "images"));
            if($value->images=="null"){
                $value->images = null;
            }
            $value->image_paths = json_encode($this->getArrayValue($imageArray, "image_paths"));
            if($value->image_paths=="null"){
                $value->image_paths = null;
            }
            array_push($finalArray, $value);
        }

        $response = array("status" => true, "message" => "Success", "data" => $finalArray);
        if ($return) {
            return $finalArray;
        } else {
            $this->sendResponse($response);
        }

    }

    public function fetchChainNoOrder($return = false, $tempPostData = null)
    {
        $postData     = $return ? $tempPostData : $this->getJsonRequest();
        $user         = Auth::user();
        $companyID    = $user->company_id;
        $employee     = Employee::where('company_id',$companyID)->where('user_id',$user->id)->first();
        $employeeID   = $employee->id;
        
        //Check if unsynced data is available . if available first update to tha database
        $syncStatus   = $this->manageUnsyncedNoOrder($postData);
        
        $offset       = $this->getArrayValue($postData, "offset");
        $length       = $this->getArrayValue($postData, "data_limit");
        
        $juniorChains = Employee::employeeChilds($employeeID, array());

        $no_orders = NoOrder::leftJoin('clients','no_orders.client_id','clients.id')
                      ->leftJoin('employees', 'employees.id', 'no_orders.employee_id')
                      ->select('no_orders.*','clients.id as clientID','clients.name','clients.company_name', 'employees.name as employee_name')
                      ->where('no_orders.company_id',$companyID)
                      ->where(function($query) use ($employee, $juniorChains) {
                        if($employee->is_admin!=1){
                          $query->whereIn('no_orders.employee_id', $juniorChains);
                        }
                      });

        $no_orders = $no_orders->whereNull('no_orders.deleted_at')
                ->where('no_orders.id', '>', "$offset")
                ->orderBy('no_orders.id', 'asc')
                ->limit($length)
                ->get();
        if (empty($no_orders)) {
            if ($return) return array();
            else $this->sendEmptyResponse();
        }

        $finalArray = array();
        foreach ($no_orders as $key => $value) {
            $imageArray         = getImageArray("noorders", $value->id,$companyID,$employeeID);
            $value->image_ids   = json_encode($this->getArrayValue($imageArray,"image_ids"));
            if($value->image_ids=="null"){
                $value->image_ids = null;
            }
            $value->images      = json_encode($this->getArrayValue($imageArray, "images"));
            if($value->images=="null"){
                $value->images = null;
            }
            $value->image_paths = json_encode($this->getArrayValue($imageArray, "image_paths"));
            if($value->image_paths=="null"){
                $value->image_paths = null;
            }

            $value->employee_name = $value->employees()->withTrashed()->first()?$value->employees()->withTrashed()->first()->name:"";
            array_push($finalArray, $value);
        }
        $response = array("status" => true, "message" => "Success", "data" => $finalArray);
        if ($return) return $finalArray;
        else $this->sendResponse($response);
    }

    public function fetchNoOrderChanges(Request $request)
    {
        $user                = Auth::user();
        $companyID           = $user->company_id;
        $employee            = Employee::where('company_id', $companyID)->where('user_id', $user->id)->first();
        $employeeID          = $employee->id;
        
        $fetchToken          = $request->fetch_token;
        $lastFetchObject     = DB::table('changes_last_fetched')->where('unique_token', $fetchToken)->first();
        $finalArray          = array('created_records'=> array(), 'updated_records'=>array(), 'deleted_records'=>array());
        if(! $lastFetchObject) return array();
        $lastFetchedDatetime = $lastFetchObject->zeroorder_fetch_datetime;
        DB::beginTransaction();
        DB::table('changes_last_fetched')->updateOrInsert(
            ['unique_token' => $fetchToken],
            ['user_id' => $user->id, 'unique_token' => $fetchToken, 'zeroorder_fetch_datetime'=>date('Y-m-d H:i:s')]
        );
        DB::commit();

        $juniorChains = Employee::employeeChilds($employeeID, array());

        $no_orders = NoOrder::withTrashed()->leftJoin('clients','no_orders.client_id','clients.id')
                        ->select('no_orders.*','clients.id as clientID','clients.name','clients.company_name')
                        ->where('no_orders.company_id',$companyID)
                        ->where(function($query) use($lastFetchedDatetime){
                            $query->orWhere('no_orders.created_at', '>', $lastFetchedDatetime);
                            $query->orWhere('no_orders.updated_at', '>', $lastFetchedDatetime);
                            $query->orWhere('no_orders.deleted_at', '>', $lastFetchedDatetime);
                        })
                        ->where(function($queryHierarchy) use ($employee, $juniorChains) {
                            if($employee->is_admin!=1){
                                $queryHierarchy->whereIn('no_orders.employee_id', $juniorChains);
                            }
                        })->get();
      
        if(!$no_orders->first()) {
          $response = array("status" => true, "message" => "Success", "data" => $finalArray);
          return $response;
        }
        foreach($no_orders as $no_order){
            if($no_order->deleted_at){
                $finalArray['deleted_records'][] = (int)$no_order->id;
                continue;
            }

            $imageArray         = getImageArray("noorders", $no_order->id,$companyID,$employeeID);
            $no_order->image_ids   = json_encode($this->getArrayValue($imageArray,"image_ids"));
            if($no_order->image_ids=="null"){
                $no_order->image_ids = null;
            }
            $no_order->images      = json_encode($this->getArrayValue($imageArray, "images"));
            if($no_order->images=="null"){
                $no_order->images = null;
            }
            $no_order->image_paths = json_encode($this->getArrayValue($imageArray, "image_paths"));
            if($no_order->image_paths=="null"){
                $no_order->image_paths = null;
            }

            $no_order->employee_name = $no_order->employees()->withTrashed()->first()?$no_order->employees()->withTrashed()->first()->name:"";

            if($no_order->updated_at && ($no_order->updated_at>$no_order->created_at)) $finalArray['updated_records'][] = $no_order;
            elseif($no_order->created_at && !$no_order->updated_at) $finalArray['created_records'][] = $no_order;
        }



        $response = array("status" => true, "message" => "Success", "data" => $finalArray);
        return $response;
    }

    public function store()
    {
        $postData = $this->getJsonRequest();
        $user = Auth::user();
        $companyID = $user->company_id;
        $employee = Employee::where('company_id',$companyID)->where('user_id',$user->id)->first();
        $employeeID = $employee->id;

        $noOrder_id = $this->getArrayValue($postData, "noOrder_id");
        $unique_id = $this->getArrayValue($postData, "unique_id");

        $noOrder = NoOrder::where('company_id',$companyID)->where(function($query)use($noOrder_id,$unique_id){
                                    $query = $query->where('id',$noOrder_id)->orWhere('unique_id',$unique_id);
                                })->first();
        $addedUpdated = "update";
        $notEid = $employeeID;
        if(!$noOrder){
            $noOrder              = new NoOrder;
            $noOrder->employee_id = $employeeID;
            $noOrder->company_id  = $companyID;
            $noOrder->unique_id   = $this->getArrayvalue($postData,"unique_id");
            $addedUpdated = "add";
            $noOrder->unix_timestamp = $this->getArrayvalue($postData, "unix_timestamp");
        }else{
          $notEid = $noOrder->employee_id;
        }

        $employeeName            = $this->getArrayValue($postData, "employee_name");
        $dateTime                = $this->getArrayValue($postData, "datetime");
        
        $noOrder->client_id      = $this->getArrayvalue($postData, "client_id");
        $noOrder->remark         = $this->getArrayValue($postData, "remark");
        $noOrder->datetime       = $dateTime;
        $noOrder->date           = $this->getArrayvalue($postData, "date");
        $noOrder->created_at     = $this->getArrayvalue($postData, "datetime");
        $noOrder->save();
        $noOrder = NoOrder::where('id', $noOrder->id)->select('id', 'unique_id', 'company_id', 'employee_id', 'client_id', 'remark', 'unix_timestamp', 'datetime', 'date')->first();

        $noOrder->employee_name = $employeeName;

        $images         = $this->getArrayValue($postData, "images");
        $tempImageNames = array();
        $tempImagePaths = array();
        $imageArray     = array();
        // Log::info('info', array("message"=>print_r($images,true)));

        if (!empty($images)) {
            $images = json_decode($images);
            foreach ($images as $key => $value) {
                $tempImageName = $this->getImageName();
                $tempImageDir  = $this->getImagePath($companyID, 'noorders');
                $tempImagePath = "/storage/app/public/" . $tempImageDir . "/" . $tempImageName;
                $decodedData   = base64_decode($value);
                $put           = \Storage::disk('public')->put($tempImageDir . '/' . $tempImageName, base64_decode($value));
                    
                array_push($tempImageNames, $tempImageName);
                array_push($tempImagePaths, $tempImagePath);
                $imageArray[$tempImageName] = $tempImagePath;
            }
        }
        $images_ids   = [];
        $images_names = [];
        $images_paths = [];
        if($noOrder_id){
            $deleted_images_id = $this->getArrayValue($postData, "deleted_images_id");
            if(!empty($deleted_images_id)){
                foreach($deleted_images_id as $deleted_image){
                    $instance = DB::table('images')->whereId($deleted_image)->first();
                    if($instance){
                        $image_path = $instance->image_path;
                        DB:: table('images')->whereId($deleted_image)->delete();
                        unlink('cms/'.$image_path);
                    }
                }
            }

            $updated_images = json_decode($this->getArrayValue($postData, "edited_images"));
            if(!empty($updated_images)){
                foreach ($updated_images as $key => $value) {
                    $tempImageName = $this->getImageName();
                    $tempImageDir  = $this->getImagePath($companyID, 'noorders');
                    $tempImagePath = "/storage/app/public/" . $tempImageDir . "/" . $tempImageName;
                    $put           = \Storage::disk('public')->put($tempImageDir . '/' . $tempImageName, base64_decode($value));
                    DB::table('images')->insert([
                        "type"        => "noorders",
                        "type_id"     => $noOrder_id,
                        "company_id"  => $companyID,
                        "employee_id" => $noOrder->employee_id,
                        "image"       => $tempImageName,
                        "image_path"  => $tempImagePath,
                        "created_at"  => $this->getArrayValue($postData, "created_at")
                        ]);
                    array_push($tempImageNames, $tempImageName);
                    array_push($tempImagePaths, $tempImagePath);
                    unset($tempImageName);
                    unset($tempImagePath);
                }
            }
        }

        if($noOrder){
            if (!empty($imageArray)) {
                $imageData = array();
                foreach ($imageArray as $imageName => $imagePath) {
                    $tempArray = array();
                    $tempArray["type"]        = "noorders";
                    $tempArray["type_id"]     = $noOrder->id;
                    $tempArray["company_id"]  = $companyID;
                    $tempArray["employee_id"] = $noOrder->employee_id;
                    $tempArray["image"]       = $imageName;
                    $tempArray["image_path"]  = $imagePath;
                    $tempArray["created_at"]  = $this->getArrayValue($postData, "created_at");
                    array_push($imageData, $tempArray);
                }
                DB:: table('images')->insert($imageData);
            }

            $client = Client::where('id',$noOrder->client_id)->first();
            if($client){
                $noOrder->name = $client->name;
                $noOrder->company_name = $client->company_name;
            }
            // Log::info('info', array("message"=>print_r($noOrder,true)));

            $finalImages = DB::table('images')->where('type','noorders')->where('type_id',$noOrder->id)->whereNull('deleted_at')->get();
            foreach($finalImages as $finalImage){
                array_push($images_ids,$finalImage->id);    
                array_push($images_names,$finalImage->image);    
                array_push($images_paths,$finalImage->image_path);    
            }
           
            $noOrder->image_ids   = json_encode($images_ids);
            if($noOrder->image_ids=='[]'){
                $noOrder->image_ids = null;
            }
            $noOrder->images      = json_encode($images_names);
            if($noOrder->images=='[]'){
                $noOrder->images = null;
            }
            $noOrder->image_paths = json_encode($images_paths);
            if($noOrder->image_paths=='[]'){
                $noOrder->image_paths = null;
            }
            
            if($noOrder_id)
                $msg = "Updated Zero Order";
            else
                $msg = "Added Zero Order";

            $sent = saveAdminNotification($companyID, $employeeID, date("Y-m-d H:i:s"), $msg, "noorders", $noOrder);
            
            $superiors = Employee::employeeParents($noOrder->employee_id, array());
            if($notEid == $employeeID) $superiors = array_diff($superiors, array($notEid));
            $fbIDs = DB::table('employees')->where(array(array('company_id', $companyID), array('status', 'Active')))->whereNotNull('firebase_token')->whereIn('id', $superiors)->pluck('firebase_token');

            $dataPayload = array("data_type" => "noorder", "noorder" => $noOrder, "action" => $addedUpdated);
            $msgID = sendPushNotification_($fbIDs, 36, null, $dataPayload);

            if($noOrder->employee_id!=$employeeID){
              $employee = Employee::findOrFail($noOrder->employee_id);
              $notificationData = array(
                  "company_id" => $employee->company_id,
                  "employee_id" => $employee->id,
                  "data_type" => "noorder",
                  "data" => "",
                  "action_id" => $noOrder->id,
                  "title" => "noorder " . $noOrder->status,
                  "description" => "Your noorder has been Updated",
                  "created_at" => date('Y-m-d H:i:s'),
                  "status" => 1,
                  "to" => 1,
                  "unix_timestamp" => time()
                );
                
                $superiors = Employee::employeeParents($noOrder->employee_id, array());
                $fbIDs = DB::table('employees')->where(array(array('company_id', $companyID), array('status', 'Active')))->whereNotNull('firebase_token')->whereIn('id', $superiors)->pluck('firebase_token');
    
                $dataPayload = array("action" => "update", "data_type" => "noorder", "noorder" => $noOrder);
                $sent = sendPushNotification_($fbIDs, 36, $notificationData, $dataPayload);
            }

        }
        $response = array("status" => true, "message" => "successfully saved", "data" => $noOrder);
        $this->sendResponse($response);
    }

    public function destroy(Request $request)
    {
        $user = Auth::user();
        $company_id = $user->company_id;
        $employee = Employee::where('company_id',$company_id)->where('user_id',$user->id)->first();
        $employeeID = $employee->id;
        $notEid = $employeeID;
        // if(Auth::user()->isCompanyManager()){
            $noOrder = NoOrder::where('company_id',$company_id)->where('id',$request->id)->first();
        // }else{
        //     $juniors = Employee::EmployeeChilds($employeeID,array());
        //     $noOrder = NoOrder::where('company_id',$company_id)->where('id',$request->id)->whereIn('employee_id',$juniors)->first();
        // }
        if(!$noOrder)
        return response(['status'=>false,'error'=>'No Collection found or no permission access to delete this NoOrder.']);
        $empID = $noOrder->employee_id;
        $deleted = $noOrder->delete();
        $notEid = $noOrder->employee_id;

        if($deleted){
            DB::table('images')->where('company_id',$company_id)->where('type','noorders')->where('type_id',$noOrder->id)->update(['deleted_at'=>date('Y-m-d h:i:s')]);
        }
        $nSaved = saveAdminNotification($company_id, $employeeID, date("Y-m-d H:i:s"),'Zero Order Deleted', "NoOrder", $noOrder);

        $superiors = Employee::employeeParents($empID, array());
        if($notEid==$employeeID) $superiors = array_diff($superiors, array($notEid));
        $fbIDs = DB::table('employees')->where(array(array('company_id', $company_id), array('status', 'Active')))->whereNotNull('firebase_token')->whereIn('id', $superiors)->pluck('firebase_token');

        $dataPayload = array("data_type" => "noorder", "noorder" => $request->id, "action" => "delete", "noorder_id" => $request->id);
        $msgID = sendPushNotification_($fbIDs, 36, null, $dataPayload);

        $newdataPayload = array("data_type" => "noorder", "noorder" => $request->id, "action" => "delete", "noorder_id" => $request->id);
        sendPushNotification_(getFBIDs($company_id, null, $empID), 36, null, $newdataPayload);

        return response(['status'=>true,'message'=>'Zero Order Deleted']);
    }

    public function syncNoOrder()
    {
        $postData = $this->getJsonRequest();
        $arraySyncedData = $this->manageUnsyncedNoOrder($postData, true);
        $response = array("status" => true, "message" => "success", "data" => $arraySyncedData);
        $this->sendResponse($response);
    }

    private function manageUnsyncedNoOrder($postData, $returnItems = false, $client = null)
    {

        $rawData      = $this->getArrayValue($postData, "nonsynced_no_orders");
        $user         = Auth::user();
        $companyID    = $user->company_id;
        $employee     = Employee::where('company_id',$companyID)->where('user_id',$user->id)->first();
        $employeeID   = $employee->id;
        $employeeName = $this->getArrayValue($postData, "employee_name");


        if (empty($rawData)) return $returnItems ? array() : false;

        $data = json_decode($rawData, true);

        $arraySyncedData = array();
        foreach ($data as $key => $val) {

            $noOrderClientID       = $this->getArrayValue($val, "client_id");
            $noOrderClientUniqueID = $this->getArrayValue($val, "client_unique_id");

            if (empty($noOrderClientID)) {

                if ($returnItems && !empty($client)) {

                    $tempClientUniqueID = $client->unique_id;
                    $tempClientID = $client->id;
                    if ($noOrderClientUniqueID == $tempClientUniqueID) {
                        $noOrderClientID = $tempClientID;
                    } else {
                        continue;
                    }
                }
            }

            $noOrder_id = $this->getarrayValue($val, "noOrder_id");
            $unique_id = $this->getArrayvalue($val,"unique_id");
            
            $noOrder = NoOrder::where('company_id',$companyID)->where('id',$noOrder_id)->orWhere('unique_id',$unique_id)->first();

            //$noOrder = NoOrder::where('company_id',$companyID)->where('employee_id',$employeeID)->where(function($query)use($noOrder_id,$unique_id){
            //    $query = $query->where('id',$noOrder_id)->orWhere('unique_id',$unique_id);
            //})->first();
            $addedUpdated = "update";
            $notEid = $employeeID;
            $created = true;
            if(!$noOrder){
                $noOrder = new NoOrder;
                $noOrder->employee_id = $employeeID;
                $noOrder->company_id  = $companyID;    
                $noOrder->unique_id   = $this->getArrayvalue($val,"unique_id");
                $noOrder->created_at  = $this->getArrayvalue($val, "datetime");
                $addedUpdated = "add";
                $noOrder->unix_timestamp = $this->getArrayvalue($val, "unix_timestamp");
            }else{
              $notEid = $noOrder->employee_id;
              $created = false;
            }
            $noOrderUniqueID = $this->getArrayvalue($val, "unique_id");
            $dateTime = $this->getArrayvalue($val, "datetime");

            $noOrder->client_id      = $noOrderClientID;
            $noOrder->remark         = $this->getArrayValue($val, "remark");
            $noOrder->date           = $this->getArrayvalue($val, "date");
            $noOrder->datetime       = $dateTime;
            
            $noOrder->save();

            if($noOrder){
              $noOrder = NoOrder::where('id', $noOrder->id)->select('id', 'unique_id', 'company_id', 'employee_id', 'client_id', 'remark', 'unix_timestamp', 'datetime', 'date')->first();

                $imageArray     = array();
                $tempImageNames = array();
                $tempImagePaths = array();
                $images_ids = [];
                $images_names = [];
                $images_paths = [];

                $images = $this->getArrayValue($val, "images");
                if (!empty($images) && $created) {
                    $jsonDecoded = json_decode($images, true);
                    
                    foreach ($jsonDecoded as $key => $value) {
                        $tempImageName = $this->getImageName();
                        $tempImageDir  = $this->getImagePath($companyID, "noorders");
                        $tempImagePath = "/storage/app/public/" . $tempImageDir . "/" . $tempImageName;
                        $decodedData   = base64_decode($value);
                        $put = \Storage::disk('public')->put($tempImageDir . '/' . $tempImageName, base64_decode($value));
                        array_push($tempImageNames, $tempImageName);
                        array_push($tempImagePaths, $tempImagePath);
                        $imageArray[$tempImageName] = $tempImagePath;
                    }

                    // Log::info('info', array("after saved image"=>print_r($imageArray,true)));
                    
                    if (!empty($imageArray)) {
                        $imageData = array();
                        foreach ($imageArray as $imageName => $imagePath) {
                            $tempImageArray = array();
                            $tempImageArray["type"] = "noorders";
                            $tempImageArray["type_id"] = $noOrder->id;
                            $tempImageArray["company_id"] = $companyID;
                            $tempImageArray["employee_id"] = $employeeID;
                            $tempImageArray["image"] = $imageName;
                            $tempImageArray["image_path"] = $imagePath;
                            $tempImageArray["created_at"] = $noOrder->created_at;
                            array_push($imageData, $tempImageArray);
                        }
                        DB::table('images')->insert($imageData);
                        // $finalImages = DB::table('images')->where('type','noorders')->where('type_id',$noOrder->id)->whereNull('deleted_at')->get();
                        // foreach($finalImages as $finalImage){
                        //     array_push($images_ids,$finalImage->id);    
                        //     array_push($images_names,$finalImage->image);    
                        //     array_push($images_paths,$finalImage->image_path);    
                        // }
                    }
                }

                // DB::table('images')->insert($imageData);
                $finalImages = DB::table('images')->where('type','noorders')->where('type_id',$noOrder->id)->whereNull('deleted_at')->get();
                if($finalImages->first()){
                  foreach($finalImages as $finalImage){
                      array_push($images_ids,$finalImage->id);    
                      array_push($images_names,$finalImage->image);    
                      array_push($images_paths,$finalImage->image_path);    
                  }
                }

                $client = Client::where('id',$noOrder->client_id)->first();
                if($client){
                    $noOrder->name = $client->name;
                    $noOrder->company_name = $client->company_name;
                }
                $noOrder->image_ids   = json_encode($images_ids);
                if($noOrder->image_ids=='[]'){
                    $noOrder->image_ids = null;
                }
                $noOrder->images      = json_encode($images_names);
                if($noOrder->images=='[]'){
                    $noOrder->images = null;
                }
                $noOrder->image_paths = json_encode($images_paths);
                if($noOrder->image_paths=='[]'){
                    $noOrder->image_paths = null;
                }
                // Log::info('info', array("message"=>print_r($noOrder,true)));
                array_push($arraySyncedData, $noOrder);
                // Log::info("After Push");
                // Log::info(print_r($noOrder, true));
                if($noOrder_id)
                    $msg = "Zero Order Updated";
                else
                    $msg = "Zero Order Added";

                saveAdminNotification($companyID, $employeeID, date("Y-m-d H:i:s"), "Added Zero Order", "noorders", $noOrder);

                $superiors = Employee::employeeParents($noOrder->employee_id, array());
                if($notEid == $employeeID) $superiors = array_diff($superiors, array($notEid));
                $fbIDs = DB::table('employees')->where(array(array('company_id', $companyID), array('status', 'Active')))->whereNotNull('firebase_token')->whereIn('id', $superiors)->pluck('firebase_token');

                $dataPayload = array("data_type" => "noorder", "noorder" => $noOrder, "action" => $addedUpdated);
                $msgID = sendPushNotification_($fbIDs, 36, null, $dataPayload);
            }
        }
        return $returnItems ? $arraySyncedData : true;
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

    private function getImageName()
    {
        $imagePrefix = md5(uniqid(mt_rand(), true));
        $imageName = $imagePrefix . ".png";
        return $imageName;
    }
    
    private function getImagePath($companyID, $module = "common", $imageName = "")
    {
        if (empty($companyID)) return "";
        $domain = DB::table("companies")->where("id", $companyID)->where("is_active", 2)->pluck("domain")->first();
        if (empty($domain)) return "";
        
        if (empty($imageName)) {
            $imagePath = "uploads/" . $domain . "/" . $module;
        } else {
            $imagePath = "uploads/" . $domain . "/" . $module . "/" . $imageName;
        }
        return $imagePath;
    }

}