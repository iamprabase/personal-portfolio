<?php

namespace App\Http\Controllers\API;

use DB;
use Log;
use Auth;
use App\Bank;
use stdClass;
use App\Employee;
use App\Collection;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CollectionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('permission:collection-create', ['only' => ['create','store']]);
        $this->middleware('permission:collection-view');
        $this->middleware('permission:collection-update', ['only' => ['edit','update']]);
        $this->middleware('permission:collection-delete', ['only' => ['destroy']]);
    }
    
    public function index(Request $request, $tempPostData = null, $return = false)
    {
        $user       = Auth::user();
        $companyID  = $user->company_id;
        $employee   = Employee::where('company_id', $companyID)->where('user_id', $user->id)->first();
        $employeeID = $employee->id;
        
        $offset      = $this->getArrayValue($request, "offset", 0);
        $limit       = $this->getArrayValue($request, "limit", 200);
        $collections = Collection::where('company_id', $companyID)->where('employee_id', $employeeID)->get();
        
        $collections = Collection::select('collections.*', 'clients.company_name', 'banks.name as bank_name')
        ->leftJoin('clients', 'collections.client_id', '=', 'clients.id')
        ->leftJoin('banks', 'collections.bank_id', '=', 'banks.id')
        ->where('collections.company_id', $companyID)
        ->where('collections.employee_id', $employeeID)
        ->get();
        
        if (empty($collections)) {
            if ($return) {
                return array();
            } else {
                $this->sendEmptyResponse();
            }
        }
        
        $finalArray = array();
        foreach ($collections as $key => $value) {
            $imageArray         = getImageArray("collection", $value->id, $companyID, $employeeID);
            $value->image_ids   = json_encode($this->getArrayValue($imageArray, "image_ids"));
            if ($value->image_ids=="null") {
                $value->image_ids = null;
            }
            $value->images      = json_encode($this->getArrayValue($imageArray, "images"));
            if ($value->images=="null") {
                $value->images = null;
            }
            $value->image_paths = json_encode($this->getArrayValue($imageArray, "image_paths"));
            if ($value->image_paths=="null") {
                $value->image_paths = null;
            }
            array_push($finalArray, $value);
        }
        
        $response = array("status" => true, "message" => "Success", "data" => $finalArray);
        
        if ($return) {
            return $finalArray;
        } else {
            return response($response);
        }
    }

    public function fetchChainCollection(Request $request, $tempPostData = null, $return = false)
    {
        $user       = Auth::user();
        $companyID  = $user->company_id;
        $employee   = Employee::where('company_id', $companyID)->where('user_id', $user->id)->first();
        $employeeID = $employee->id;
        
        $offset      = $this->getArrayValue($request, "offset");
        $length       = $this->getArrayValue($request, "data_limit");
        
        $juniorChains = Employee::employeeChilds($employeeID, array());

        $collections = Collection::select('collections.*', 'clients.company_name', 'banks.name as bank_name')
                        ->leftJoin('clients', 'collections.client_id', '=', 'clients.id')
                        ->leftJoin('banks', 'collections.bank_id', '=', 'banks.id')
                        ->where('collections.company_id', $companyID)
                        ->where(function ($query) use ($employee, $juniorChains) {
                            if ($employee->is_admin!=1) {
                                $query->whereIn('collections.employee_id', $juniorChains);
                            }
                        })->whereNull('collections.deleted_at')
                        ->where('collections.id', '>', "$offset")
                        ->orderBy('collections.id', 'asc')
                        ->limit($length)
                        ->get();
        
        if (empty($collections)) {
            if ($return) {
                return array();
            } else {
                $this->sendEmptyResponse();
            }
        }
        
        $finalArray = array();
        foreach ($collections as $key => $value) {
            $imageArray         = getImageArray("collection", $value->id, $companyID, $employeeID);
            $value->image_ids   = json_encode($this->getArrayValue($imageArray, "image_ids"));
            if ($value->image_ids=="null") {
                $value->image_ids = null;
            }
            $value->images      = json_encode($this->getArrayValue($imageArray, "images"));
            if ($value->images=="null") {
                $value->images = null;
            }
            $value->image_paths = json_encode($this->getArrayValue($imageArray, "image_paths"));
            if ($value->image_paths=="null") {
                $value->image_paths = null;
            }
            array_push($finalArray, $value);
        }
        
        $response = array("status" => true, "message" => "Success", "data" => $finalArray);
        
        if ($return) {
            return $finalArray;
        } else {
            return response($response);
        }
    }

    public function fetchCollectionChanges(Request $request, $return = false, $tempPostData = null)
    {
        $postData = $return ? $tempPostData : $this->getJsonRequest();
        $user       = Auth::user();
        $companyID  = $user->company_id;
        $employee   = Employee::where('company_id', $companyID)->where('user_id', $user->id)->first();
        $employeeID = $employee->id;

        $fetchToken = $request->fetch_token;
        $lastFetchObject = DB::table('changes_last_fetched')->where('unique_token', $fetchToken)->first();
        $finalArray = array('created_records'=> array(), 'updated_records'=>array(), 'deleted_records'=>array());
        if (! $lastFetchObject) {
            return array();
        }

        $lastFetchedDatetime = $lastFetchObject->zeroorder_fetch_datetime;
        DB::beginTransaction();
        DB::table('changes_last_fetched')->updateOrInsert(
            ['unique_token' => $fetchToken],
            ['user_id' => $user->id, 'unique_token' => $fetchToken, 'zeroorder_fetch_datetime'=>date('Y-m-d H:i:s')]
        );
        DB::commit();
      
        $juniorChains = Employee::employeeChilds($employeeID, array());

        $collections = Collection::withTrashed()->select('collections.*', 'clients.company_name', 'banks.name as bank_name')
                        ->leftJoin('clients', 'collections.client_id', '=', 'clients.id')
                        ->leftJoin('banks', 'collections.bank_id', '=', 'banks.id')
                        ->where('collections.company_id', $companyID)
                        ->where(function ($query) use ($lastFetchedDatetime) {
                            $query->orWhere('collections.created_at', '>', $lastFetchedDatetime);
                            $query->orWhere('collections.updated_at', '>', $lastFetchedDatetime);
                            $query->orWhere('collections.deleted_at', '>', $lastFetchedDatetime);
                        })
                        ->where(function ($query) use ($employee, $juniorChains) {
                            if ($employee->is_admin!=1) {
                                $query->whereIn('collections.employee_id', $juniorChains);
                            }
                        })
                        ->orderBy('collections.id', 'asc')
                        ->get();
        
        if (!$collections->first()) {
            $response = array("status" => true, "message" => "No records found.", "data" => $finalArray);
            return $response;
        }
        // if (empty($collections)) {
        //   if ($return) return array();
        //   else $this->sendEmptyResponse();
        // }
        foreach ($collections as $key => $value) {
            if ($value->deleted_at) {
                $finalArray['deleted_records'][] = (int)$value->id;

                continue;
            }
            
            $imageArray         = getImageArray("collection", $value->id, $companyID, $employeeID);
            $value->image_ids   = json_encode($this->getArrayValue($imageArray, "image_ids"));
            if ($value->image_ids=="null") {
                $value->image_ids = null;
            }
            $value->images      = json_encode($this->getArrayValue($imageArray, "images"));
            if ($value->images=="null") {
                $value->images = null;
            }
            $value->image_paths = json_encode($this->getArrayValue($imageArray, "image_paths"));
            if ($value->image_paths=="null") {
                $value->image_paths = null;
            }
        
            if ($value->updated_at && ($value->updated_at>$value->created_at)) {
                $finalArray['updated_records'][] = $value;
            } elseif ($value->created_at && !$value->updated_at) {
                $finalArray['created_records'][] = $value;
            }
        }
        
        $response = array("status" => true, "message" => "Success", "data" => $finalArray);
      
        // return response($response);
        if ($return) {
            return $finalArray;
        } else {
            $this->sendResponse($response);
        }
    }
    
    public function store(Request $request)
    {
        $postData   = $this->getJsonRequest();
        $user       = Auth::user();
        $companyID  = $user->company_id;
        $employee   = Employee::where('company_id', $companyID)->where('user_id', $user->id)->first();
        $employeeID = $employee->id;
        
        $collection_id = $this->getarrayValue($postData, "id");
        $unique_id     = (string)$this->getArrayvalue($postData, "unique_id");
        $collection    = Collection::where('company_id', $companyID)
                            ->where(function ($query) use ($collection_id,$unique_id) {
                                $query = $query->where('id', $collection_id)->orWhere('unique_id', $unique_id);
                            })->first();
        if (!$collection) {
            $collection              = new Collection;
            $collection->employee_id = $employeeID;
            $collection->company_id  = $companyID;
            $collection->unique_id   = $this->getArrayvalue($postData, "unique_id");
        }
        //$payment_method = $this->getArrayValue($postData, "payment_method", "N/A");
        $payment_method = $this->getArrayValue($postData, "payment_method");
        if ($payment_method=='Cash' || $payment_method=='Bank Transfer') {
            $payment_status = 'Cleared';
        } else {
            $payment_status = $this->getArrayValue($postData, "payment_status", "Pending");
        }
        
        $collection->client_id           = $this->getarrayValue($postData, "client_id");
        $payment_received = $this->getArrayValue($postData, "payment_received");
        $collection->payment_received    = $request->payment_received ? $request->payment_received : 0;
        $collection->due_payment         = $this->getArrayValue($postData, "due_payment", 0);
        $collection->payment_method      = $payment_method;
        $collection->payment_status      = $payment_status;
        $collection->bank_id             = $this->getArrayValue($postData, "bank_id", 0);
        $collection->cheque_no           = $this->getArrayValue($postData, "cheque_no", "");
        $collection->cheque_date         = $this->getArrayValue($postData, "cheque_date");
        $collection->payment_note        = $this->getArrayValue($postData, "payment_note", "N/A");
        $collection->payment_status_note = $this->getArrayValue($postData, "payment_status_note", "");
        $collection->payment_date        = $this->getArrayValue($postData, "payment_date");
        $collection->next_date           = $this->getArrayValue($postData, "next_date");
        $collection->created_at          = $this->getArrayValue($postData, "created_at");
        $collection->updated_at          = $this->getArrayValue($postData, "updated_at");
        $collection->save();
        
        $images         = $this->getArrayValue($postData, "images");
        $tempImageNames = array();
        $tempImagePaths = array();
        $imageArray     = array();
        
        if (!empty($images)) {
            foreach ($images as $key => $value) {
                $tempImageName = $this->getImageName();
                $tempImageDir  = $this->getImagePath($companyID, 'collection');
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
        //Check For Updated Images
        if ($collection_id) {
            $deleted_images_id = $this->getArrayValue($postData, "deleted_images_id");
            if (!empty($deleted_images_id)) {
                foreach ($deleted_images_id as $deleted_image) {
                    $instance = DB::table('images')->whereId($deleted_image)->first();
                    if ($instance) {
                        $image_path = $instance->image_path;
                        DB:: table('images')->whereId($deleted_image)->delete();
                        unlink('cms/'.$image_path);
                    }
                }
            }
            
            $updated_images = $this->getArrayValue($postData, "edited_images");
            if (!empty($updated_images)) {
                foreach ($updated_images as $key => $value) {
                    $tempImageName = $this->getImageName();
                    $tempImageDir  = $this->getImagePath($companyID, 'collection');
                    $tempImagePath = "/storage/app/public/" . $tempImageDir . "/" . $tempImageName;
                    $put           = \Storage::disk('public')->put($tempImageDir . '/' . $tempImageName, base64_decode($value));
                    DB::table('images')->insert([
                        "type"        => "collection",
                        "type_id"     => $collection_id,
                        "company_id"  => $companyID,
                        "employee_id" => $employeeID,
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
            
        if ($collection) {
            //$nSaved = sendNotification($companyID,$employeeID,"Collection Added","",$createdAt);
            if (!empty($imageArray)) {
                $imageData = array();
                foreach ($imageArray as $imageName => $imagePath) {
                    $tempArray = array();
                    $tempArray["type"]        = "collection";
                    $tempArray["type_id"]     = $collection->id;
                    $tempArray["company_id"]  = $companyID;
                    $tempArray["employee_id"] = $employeeID;
                    $tempArray["image"]       = $imageName;
                    $tempArray["image_path"]  = $imagePath;
                    $tempArray["created_at"]  = $this->getArrayValue($postData, "created_at");
                    array_push($imageData, $tempArray);
                }
                DB:: table('images')->insert($imageData);
            }
            $finalImages = DB::table('images')->where('type', 'collection')->where('type_id', $collection->id)->whereNull('deleted_at')->get();
            foreach ($finalImages as $finalImage) {
                array_push($images_ids, $finalImage->id);
                array_push($images_names, $finalImage->image);
                array_push($images_paths, $finalImage->image_path);
            }
            $collection->image_ids   = json_encode($images_ids);
            if ($collection->image_ids=='[]') {
                $collection->image_ids = null;
            }
            $collection->images      = json_encode($images_names);
            if ($collection->images=='[]') {
                $collection->images = null;
            }
            $collection->image_paths = json_encode($images_paths);
            if ($collection->image_paths=='[]') {
                $collection->image_paths = null;
            }
            
            if ($collection_id) {
                $msg = "Updated Collection";
                if($employeeID != $collection->employee_id){
                  $dataPayload = array("data_type" => "collection", "collection" => $collection, "action" => "update");
                  $msgID = sendPushNotification_(getFBIDs($collection->company_id, null, $collection->employee_id), 3, null, $dataPayload);
                }
                
            } else {
                $msg = "Added Collection";
            }
            
            $sent = saveAdminNotification($companyID, $employeeID, date("Y-m-d H:i:s"), $msg, "collection", $collection);
        }

        $response = array("status" => true, "message" => "successfully saved", "data" => $collection);
        $this->sendResponse($response);
    }
        
    public function destroy(Request $request)
    {
        $user = Auth::user();
        $company_id = $user->company_id;
        $employee = Employee::where('company_id', $company_id)->where('user_id', $user->id)->first();
        $employeeID = $employee->id;
        // if (Auth::user()->isCompanyManager()) {
            $collection = Collection::where('company_id', $company_id)->where('id', $request->id)->first();
        // } else {
        //     $juniors = Employee::EmployeeChilds($employeeID, array());
        //     $collection = Collection::where('company_id', $company_id)->where('id', $request->id)->whereIn('employee_id', $juniors)->first();
        // }
        if (!$collection) {
            return response(['status'=>false,'error'=>'No Collection found or no permission access to delete this collection.']);
        }
        $collection->delete();
        $nSaved = saveAdminNotification($company_id, $employeeID, date("Y-m-d H:i:s"), 'Collection Deleted', "collection", $collection);

        if ($collection->employee_id != $employeeID) {
          $dataPayload = array("data_type" => "collection", "collection" => $collection, "action" => "delete");
          $msgID = sendPushNotification_(getFBIDs($collection->company_id, null, $collection->employee_id), 3, null, $dataPayload);
        }

        return response(['status'=>true,'message'=>'Collection Deleted']);
    }
    
    public function syncCollection()
    {
        $postData = $this->getJsonRequest();
        $arraySyncedData = $this->manageUnsyncedCollection($postData, true);
        $response = array("status" => true, "message" => "success", "data" => $arraySyncedData);
        return response($response);
    }
    
    private function manageUnsyncedCollection($postData, $returnItems = false, $client = null)
    {
        $rawData = $this->getArrayValue($postData, "nonsynced_collection");
        // Log::info('info', array("postData"=>print_r($rawData,true)));
        $user = Auth::user();
        $companyID = $user->company_id;
        $employee = Employee::where('company_id', $companyID)->where('user_id', $user->id)->first();
        $employeeID = $employee->id;
        
        if (empty($rawData)) {
            return $returnItems ? array() : false;
        }
        
        $data = json_decode($rawData, true);
        $arraySyncedData = array();
        // Log::info('info', array("postData"=>print_r($data,true)));
        foreach ($data as $key => $col) {
            $collection_id = $this->getarrayValue($col, "id");
            $unique_id = $this->getArrayvalue($col, "unique_id");
            $collection = Collection::where('company_id', $companyID)->where('id', $collection_id)->orWhere('unique_id', $unique_id)->first();
            $created = false;
            if (!$collection) {
                $collection = new Collection;
                $collection->employee_id = $employeeID;
                $collection->company_id  = $companyID;
                $collection->unique_id   = $this->getArrayvalue($col, "unique_id");
                $created = true;

            }
            //$payment_method = $this->getArrayValue($col, "payment_method", "N/A");
            $payment_method = $this->getArrayValue($col, "payment_method");
            if ($payment_method=='Cash' || $payment_method=='Bank Transfer') {
                $payment_status = 'Cleared';
            } else {
                $payment_status = $this->getArrayValue($col, "payment_status", "Pending");
            }
            
            $collection->client_id           = $this->getarrayValue($col, "client_id");
            $collection->payment_received    = $this->getArrayValue($col, "payment_received") ? $this->getArrayValue($col, "payment_received") : 0;
            // $collection->payment_received    = $this->getArrayValue($col, "payment_received");
            $collection->due_payment         = $this->getArrayValue($col, "due_payment", 0);
            $collection->payment_method      = $payment_method;
            $collection->payment_status      = $payment_status;
            $collection->bank_id             = $this->getArrayValue($col, "bank_id", 0);
            $collection->cheque_no           = $this->getArrayValue($col, "cheque_no", "");
            $collection->cheque_date         = $this->getArrayValue($col, "cheque_date", "");
            $collection->payment_note        = $this->getArrayValue($col, "payment_note", "N/A");
            $collection->payment_status_note = $this->getArrayValue($col, "payment_status_note", "");
            $collection->payment_date        = $this->getArrayValue($col, "payment_date");
            $collection->next_date           = $this->getArrayValue($col, "next_date");
            $collection->created_at          = $this->getArrayValue($col, "created_at");
            $collection->updated_at          = $this->getArrayValue($col, "updated_at");
            $collection->save();

            if ($collection) {
                $imageArray     = array();
                $tempImageNames = array();
                $tempImagePaths = array();
                $images_ids = [];
                $images_names = [];
                $images_paths = [];

                $images = $this->getArrayValue($col, "images");
                if (!empty($images) && $created) {
                  $jsonDecoded = json_decode($images, true);
                    
                  foreach ($jsonDecoded as $key => $value) {
                    $tempImageName = $this->getImageName();
                    $tempImageDir  = $this->getImagePath($companyID, "collection");
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
                      $tempImageArray["type"] = "collection";
                      $tempImageArray["type_id"] = $collection->id;
                      $tempImageArray["company_id"] = $companyID;
                      $tempImageArray["employee_id"] = $employeeID;
                      $tempImageArray["image"] = $imageName;
                      $tempImageArray["image_path"] = $imagePath;
                      $tempImageArray["created_at"] = $collection->created_at;
                      array_push($imageData, $tempImageArray);
                      DB::table('images')->insert($tempImageArray);
                    }
                      // $finalImages = DB::table('images')->where('type', 'collection')->where('type_id', $collection->id)->whereNull('deleted_at')->get();
                      // foreach ($finalImages as $finalImage) {
                      //     array_push($images_ids, $finalImage->id);
                      //     array_push($images_names, $finalImage->image);
                      //     array_push($images_paths, $finalImage->image_path);
                      // }
                      // Log::info('info', array("postData"=>print_r($finalImages,true)));
                  }
                }

                $finalImages = DB::table('images')->where('type', 'collection')->where('type_id', $collection->id)->whereNull('deleted_at')->get();
                if($finalImages->first()){
                  foreach ($finalImages as $finalImage) {
                    array_push($images_ids, $finalImage->id);
                    array_push($images_names, $finalImage->image);
                    array_push($images_paths, $finalImage->image_path);
                  }
                }

                $collection->image_ids   = json_encode($images_ids);
                if ($collection->image_ids=='[]') {
                    $collection->image_ids = null;
                }
                $collection->images      = json_encode($images_names);
                if ($collection->images=='[]') {
                    $collection->images = null;
                }
                $collection->image_paths = json_encode($images_paths);
                if ($collection->image_paths=='[]') {
                  $collection->image_paths = null;
                }

                array_push($arraySyncedData, $collection);
                if ($collection_id) {
                  $msg = "Collection Updated";
                } else {
                  $msg = "Collection Added";
                }
                saveAdminNotification($companyID, $employeeID, date("Y-m-d H:i:s"), $msg, "collection", $collection);
            }
        }//end foreach
        // Log::info('info', array("postData"=>print_r($arraySyncedData,true)));
        return $returnItems ? $arraySyncedData : false;
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
    
    //Common methods
    private function getJsonRequest($isJson = true)
    {
        if ($isJson) {
            return json_decode($this->getFileContent(), true);
        } else {
            return $_POST;
        }
    }
    
    private function getArrayValue($arraySource, $key, $emptyText = null, $trim = false)
    {
        if (is_array($arraySource) && !empty($arraySource[$key])) {
            return $trim == true ? trim($arraySource[$key]) : $arraySource[$key];
        } else {
            return $emptyText;
        }
    }
    
    private function sendEmptyResponse()
    {
        $response = array("status" => true, "message" => "No Record Found", "data" => array());
        echo json_encode($response);
        exit;
    }
    
    private function sendResponse($response)
    {
        echo json_encode($response);
        exit;
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
        if (empty($companyID)) {
            return "";
        }
        $domain = DB::table("companies")->where("id", $companyID)->where("is_active", 2)->pluck("domain")->first();
        if (empty($domain)) {
            return "";
        }
        
        if (empty($imageName)) {
            $imagePath = "uploads/" . $domain . "/" . $module;
        } else {
            $imagePath = "uploads/" . $domain . "/" . $module . "/" . $imageName;
        }
        return $imagePath;
    }
}
