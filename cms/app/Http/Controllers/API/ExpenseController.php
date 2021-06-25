<?php

namespace App\Http\Controllers\API;

use DB;
use Auth;
use App\Expense;
use App\Employee;
use App\ExpenseType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class ExpenseController extends Controller
{
    public function __construct()
    {
		$this->middleware('auth:api');
        $this->middleware('permission:expense-create', ['only' => ['store']]);
        $this->middleware('permission:expense-view');
        $this->middleware('permission:expense-update', ['only' => ['store']]);
        $this->middleware('permission:expense-delete', ['only' => ['destroy']]);
    }

    public function index($return = false, $tempPostData = null)
    {
        $postData = $return ? $tempPostData : $this->getJsonRequest();
        $user = Auth::user();
        $companyID = $user->company_id;
        $employee = Employee::where('company_id',$companyID)->where('user_id',$user->id)->first();
        $employeeID = $employee->id;

        /*Check if unsynced data is available . if available first update to tha database */
        $syncStatus = $this->manageUnsyncedExpense($postData);

        $expenses = Expense::select('expenses.*', 'clients.company_name')
            ->leftJoin('clients', 'expenses.client_id', '=', 'clients.id')
            ->where('expenses.company_id', $companyID)
            ->where('expenses.employee_id', $employeeID)
            ->get();


        if (empty($expenses)) {
            if ($return) {
                return array();
            } else {

                $this->sendEmptyResponse();
            }
        }

        $finalArray = array();
        foreach ($expenses as $key => $value) {
            $imageArray = getImageArray("expense", $value->id,$companyID,$employeeID);
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

    public function fetchPendingExpense(Request $request){
      $user = Auth::user();
      $companyID = $user->company_id;
      $employee = Employee::where('company_id',$companyID)->where('user_id',$user->id)->first();
      $employeeID = $employee->id;
      $immediateJuniors = Employee::where('superior', $employee->id)->pluck('id')->toArray();
      
      $expenses = Expense::where('company_id',$companyID)->with(['employee' => function($query){
        $query->select('name', 'id');
      }])->with(['exptype' => function($query){
        $query->select('expensetype_name', 'id');
      }])->with('images')->whereIn('employee_id',$immediateJuniors)->where("status", "Pending")->orderby("id", "desc")->get()->map(function ($expense) {
        return $this->formatExpense($expense);
      })->toArray();
      
      $immediateJuniors = json_encode($immediateJuniors);

      $response = array("status" => true, "message" => "Success", "data" => $expenses, "immediateJuniors" => $immediateJuniors);
      $this->sendResponse($response);

    }

    private function formatExpense($expense)
    {
        $formatted_images = $expense->images->map(function ($image) {
            return $this->formatImages($image);
        });
        $image_ids = array();
        $images = array();
        $image_paths = array();
        if (!empty($formatted_images)) {
            foreach ($formatted_images as $image) {
                array_push($image_ids, strval($image['id']));
                array_push($images, $image['image_name']);
                array_push($image_paths, $image['image_path']);
            }
        }

        try {
            $formatted_data = [
                'id' => $expense->id,
                'unique_id' => $expense->unique_id,
                'client_id' => $expense->client_id,
                'employee_id' => $expense->employee_id,
                'employee_name' => $expense->employee->name,
                'expense_type_id' => $expense->expense_type_id,
                'expense_category' => $expense->expense_type_id ? $expense->exptype->expensetype_name : null,
                'expense_amount' => $expense->amount,
                'status' => $expense->status,
                'expense_date' => $expense->expense_date,
                'remark' => $expense->remark,
                'description' => $expense->description,
                "image_ids" => json_encode($image_ids, true),
                "images" => json_encode($images, true),
                "image_paths" => json_encode($image_paths, true),
                'created_at' => date('Y-m-d', strtotime($expense->created_at)),
                'editable' => Auth::user()->can('expense-update'),
                'deleteable' => Auth::user()->can('expense-delete'),
            ];

            return $formatted_data;
        } catch (\Exception $e) {
            Log::error(array("Format Expense () => "), array($e->getMessage()));
            return array();
        }
    }

    private function formatImages($image)
    {
        try {
            // $formatted_data = [
            //   'id' => $image->id,
            //   'image_path' => config('app.ssl_certificate').'://'.$_SERVER['HTTP_HOST'].'/cms'.$image->image_path
            // ];
            $formatted_data = [
                'id' => $image->id,
                'image_name' => $image->image,
                'image_path' => $image->image_path,
            ];

            return $formatted_data;
            return $formatted_data;
        } catch (\Exception $e) {
            Log::error(array("Format Images () => "), array($e->getMessage()));
            Log::info($images);
            return null;
        }
    }


    public function changeExpenseStatus(Request $request)
    {
      $expense = Expense::where('id',$request->expense_id)->first();
      $user = Auth::user();
      $companyID = $user->company_id;
      $employee = Employee::where('company_id',$companyID)->where('user_id',$user->id)->first();
      $employeeID = $employee->id;
      
      if($expense){
        $expense->status = $request->status;
        $expense->remark = $request->remark;
        if($expense->status=='Approved' || $expense->status=="Rejected"){
          $expense->approved_by = $employeeID;
        }
        $saved = $expense->save();
        if (!empty($saved)) {
          $temp = Employee::where('id', $expense->employee_id)->first();
          if (!empty($temp)) {
            $notificationData = array(
              "company_id" => $temp->company_id,
              "employee_id" => $temp->id,
              "data_type" => "expense",
              "data" => "",
              "action_id" => $expense->id,
              "title" => "Expense " . $request->status,
              "description" => "Your expense has been " . $request->status,
              "created_at" => date('Y-m-d H:i:s'),
              "status" => 1,
              "to" => 1
            );

            $sendingNotificationData = $notificationData;
            $sendingNotificationData["unix_timestamp"] = time();
            if (!empty($temp->firebase_token)) {

              $dataPayload = array("action" => "update_status", "expense_id" => $expense->id, "status" => $expense->status, "remark" => $expense->remark, "employee_id" => $employeeID,'approved_by'=>$expense->approved_by);
            
              // $dataPayload = array("data_type" => "expense", "expense" => $expense, "action" => "update_status", "expense_id" => $expense->id, "status" => $expense->status, "remark" => $expense->remarks);
              $msgID = sendPushNotification_([$temp->firebase_token], 7, $sendingNotificationData, $dataPayload);
              Log::info(array("Expense Notification:-", $msgID));
            }
          }
        } 

        return response()->json([
          'message' => 'Expense Updated Successfully',
          'data' => $expense
        ], 200);
      }

      return response()->json([
        'message' => 'Expense Not Found',
        'data' => $expense
      ], 422);
    }

    public function store(Request $request)
    {
        $postData = $this->getJsonRequest();
        $user = Auth::user();
        $companyID = $user->company_id;
        $employee = Employee::where('company_id',$companyID)->where('user_id',$user->id)->first();
        $employeeID = $employee->id;

        $expense_id = $this->getArrayValue($postData,"id");
        $unique_id = (string)$this->getArrayValue($postData,"unique_id");

        // Log::info('info', array("exp id"=>print_r($expense_id,true)));
        // Log::info('info', array("unique id"=>print_r($unique_id,true)));

        $expense = Expense::where('company_id',$companyID)->where(function($q)use($expense_id,$unique_id){
            $q->where('id',$expense_id)->orWhere('id',$unique_id);
        })->first();
        // Log::info("Expense Object");
        // Log::info($expense);
        if(!$expense){
            $expense = new Expense;
            $expense->employee_id = $employeeID;
            $expense->company_id = $companyID;
            $expense->created_at = $this->getArrayValue($postData, "created_at");
        }
        $expense->unique_id  = $unique_id;
        $expense->client_id  = $this->getArrayValue($postData, "client_id");
        $clientUniqueID    = $this->getarrayValue($postData, "client_unique_id");
        if(isset($clientUniqueID) && empty($expense->client_id)) throw new \Exception('Client Id cannot be null');
        $expense->amount  = $this->getArrayValue($postData, "amount");
        $expense->description  = $this->getArrayValue($postData, "description");
        $expense->approved_by  = $this->getArrayValue($postData, "approved_by");
        $expense->remark  = $this->getArrayValue($postData, "remark");
        $expense->status  = $this->getArrayValue($postData, "status");
        $expense->expense_date = $this->getArrayValue($postData, "expense_date");
        $expense->updated_at  = $this->getArrayValue($postData, "updated_at");
        $expense->expense_type_id = $this->getArrayValue($postData, "expense_type_id");
        $expense->save();
        if (!empty($expense->client_id)) {
          $client = DB::table('clients')->where('id', $expense->client_id)->first();
          $expense->company_name = empty($client) ? "" : $client->company_name;
        }
        $images = $this->getArrayValue($postData, "images");
        $tempImageNames = array();
        $tempImagePaths = array();
        $imageArray = array();
        $images_ids   = [];
        $images_names = [];
        $images_paths = [];


        if (!empty($images)) {
            foreach ($images as $key => $value) {
                $tempImageName = $this->getImageName();
                $tempImageDir = $this->getImagePath($companyID, 'expense');
                $tempImagePath = "/storage/app/public/" . $tempImageDir . "/" . $tempImageName;
                $decodedData = base64_decode($value);
                $put = \Storage::disk('public')->put($tempImageDir . '/' . $tempImageName, base64_decode($value));
                array_push($tempImageNames, $tempImageName);
                array_push($tempImagePaths, $tempImagePath);
                $imageArray[$tempImageName] = $tempImagePath;
            }
        }

        //Check For Updated Images
        if($expense_id){
            $deleted_images_id = $this->getArrayValue($postData, "deleted_images_id");
            if(!empty($deleted_images_id)){
                foreach($deleted_images_id as $deleted_image){
                    $instance = DB::table('images')->whereId($deleted_image)->first();
                    if($instance){
                        $image_path = $instance->image_path;
                        DB::table('images')->whereId($deleted_image)->delete();
                        unlink('cms/'.$image_path);
                    }
                }
            }

            $updated_images = $this->getArrayValue($postData, "edited_images");
            // Log::info('info', array("message"=>print_r($updated_images,true)));
            if(!empty($updated_images)){
                foreach ($updated_images as $key => $value) {
                    $tempImageName = $this->getImageName();
                    $tempImageDir  = $this->getImagePath($companyID, 'notes');
                    $tempImagePath = "/storage/app/public/" . $tempImageDir . "/" . $tempImageName;
                    $put = \Storage::disk('public')->put($tempImageDir . '/' . $tempImageName, base64_decode($value));
                    DB::table('images')->insert([
                        "type" => "expense",
                        "type_id" => $expense_id,
                        "company_id" => $companyID,
                        "employee_id" => $expense->employee_id,
                        "image" => $tempImageName,
                        "image_path" => $tempImagePath,
                        "created_at" => $this->getArrayValue($postData, "created_at")
                    ]);
                    array_push($tempImageNames, $tempImageName);
                    array_push($tempImagePaths, $tempImagePath);
                    unset($tempImageName);
                    unset($tempImagePath);
                }
            }
        }

        if ($expense) {

            if (!empty($imageArray)) {
                $imageData = array();
                foreach ($imageArray as $imageName => $imagePath) {
                    $tempArray = array();
                    $tempArray["type"] = "expense";
                    $tempArray["type_id"] = $expense->id;
                    $tempArray["company_id"] = $companyID;
                    $tempArray["employee_id"] = $expense->employee_id;
                    $tempArray["image"] = $imageName;
                    $tempArray["image_path"] = $imagePath;
                    $tempArray["created_at"] = $this->getArrayValue($postData, "created_at");
                    array_push($imageData, $tempArray);
                }
                DB::table('images')->insert($imageData);
            }

            $finalImages = DB::table('images')->where('type','expense')->where('type_id',$expense->id)->whereNull('deleted_at')->get();
            foreach($finalImages as $finalImage){
                array_push($images_ids,$finalImage->id);    
                array_push($images_names,$finalImage->image);    
                array_push($images_paths,$finalImage->image_path);    
            }

            $expense->image_ids   = json_encode($images_ids);
            if($expense->image_ids=='[]'){
                $expense->image_ids = null;
            }
            $expense->images      = json_encode($images_names);
            if($expense->images=='[]'){
                $expense->images = null;
            }
            $expense->image_paths = json_encode($images_paths);
            if($expense->image_paths=='[]'){
                $expense->image_paths = null;
            }

            // $expense->images = $tempImageNames;
            // $expense->image_paths = $tempImagePaths;
            if($expense_id){
                $notificationSaved = saveAdminNotification($companyID, $employeeID, date("Y-m-d H:i:s"), "Updated Expense", "expense", $expense);
                $response = array("status" => true, "message" => "successfully updated", "data" => $expense);

                if($employeeID!=$expense->employee_id){
                  $employee = Employee::findOrFail($expense->employee_id);
                  $notificationData = array(
                      "company_id" => $employee->company_id,
                      "employee_id" => $employee->id,
                      "data_type" => "expense",
                      "data" => "",
                      "action_id" => $expense->id,
                      "title" => "Expense " . $expense->status,
                      "description" => "Your Expense has been Updated",
                      "created_at" => date('Y-m-d H:i:s'),
                      "status" => 1,
                      "to" => 1,
                      "unix_timestamp" => time()
                  );
                  $dataPayload = array("action" => "update", "data_type" => "expense", "expense" => $expense);
                  $sent = sendPushNotification_([$employee->firebase_token], 7, $notificationData, $dataPayload);
                }

            }else{
                $notificationSaved = saveAdminNotification($companyID, $employeeID, date("Y-m-d H:i:s"), "Added Expense", "expense", $expense);
                $response = array("status" => true, "message" => "successfully saved", "data" => $expense);
            }
            $this->sendResponse($response);

        }
        $this->sendEmptyResponse();
    }

    public function destroy(Request $request)
    {
        $user = Auth::user();
        $company_id = $user->company_id;
        $employee = Employee::where('company_id',$company_id)->where('user_id',$user->id)->first();
        $employeeID = $employee->id;
        // if(Auth::user()->isCompanyManager()){
            $expense = Expense::where('company_id',$company_id)->where('id',$request->id)->first();
        // }else{
        //     $juniors = Employee::EmployeeChilds($employeeID,array());
        //     $expense = Expense::where('company_id',$company_id)->where('id',$request->id)->whereIn('employee_id',$juniors)->first();
        // }
        if(!$expense)
            return response(['status'=>false,'error'=>'No Expense found or no permission access to delete this expense.']);
        $expense->delete();

        if ($expense->employee_id!=$employeeID) {
          $employee = Employee::findOrFail($expense->employee_id);
          $sendingExpense = $expense;
          $dataPayload = array("data_type" => "expense", "expense" => $sendingExpense, "action" => "delete", "expense_id" => $sendingExpense->id);
          $msgID = sendPushNotification_([$employee->firebase_token], 7, null, $dataPayload);
        }

        $nSaved = saveAdminNotification($company_id, $employeeID, date("Y-m-d H:i:s"),'Expense Deleted', "expense", $expense);
        return response(['status'=>true,'message'=>'Expense Deleted']);
    }

    public function syncExpense()
    {
        $postData = $this->getJsonRequest();
        $arraySyncedData = $this->manageUnsyncedExpense($postData, true);
        // Log::info('info', array("message"=>print_r($arraySyncedData,true)));
        $response = array("status" => true, "message" => "success", "data" => $arraySyncedData);
        $this->sendResponse($response);
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
        //Log::info('info', array("data "=>print_r($imagePath,true)));
        return $imagePath;
    }

    private function manageUnsyncedExpense($postData, $returnItems = false, $client = null)
    {
        $rawData = $this->getArrayValue($postData, "nonsynced_expense");
        // Log::info('info', array('postData'=>print_r($rawData,true))); 
        $user = Auth::user();
        $companyID = $user->company_id;
        $employee = Employee::where('company_id',$companyID)->where('user_id',$user->id)->first();
        $employeeID = $employee->id;

        if (empty($rawData)) {
            return $returnItems ? array() : false;
        }

        $data = json_decode($rawData, true);
        $arraySyncedData = array();
        $images_ids   = [];
        $images_names = [];
        $images_paths = [];
        foreach ($data as $key => $expense) {

            $expense_id = $this->getArrayValue($expense, "id");
            $unique_id = $this->getArrayValue($expense, "unique_id");

            $exp = Expense::where('company_id',$companyID)->where(function($q)use($expense_id,$unique_id){
                $q->where('id',$expense_id)->orWhere('unique_id',$unique_id);
            })->first();
            if(!$exp){
                $exp = new Expense;
                $exp->company_id = $companyID;
                $exp->employee_id = $employeeID;
                $exp->created_at = date('Y-m-d H:i:s');;
            }
            $exp->unique_id = $this->getArrayValue($expense, "unique_id");
            $exp->client_id = $this->getArrayValue($expense, "client_id");
            $exp->amount = $this->getArrayValue($expense, "amount");
            $exp->description = $this->getArrayValue($expense, "description");
            $exp->approved_by = $this->getArrayValue($expense, "approved_by");
            $exp->remark = $this->getArrayValue($expense, "remark");
            $exp->status = $this->getArrayValue($expense, "status");
            $exp->expense_date = $this->getArrayValue($expense, "expense_date");
            $exp->expense_type_id = $this->getArrayValue($expense, "expense_type_id");
            $exp->updated_at = $this->getArrayValue($expense, "updated_at");
            $exp->expense_type_id = $this->getArrayValue($expense, "expense_type_id");

            $exp->save();

            $expenseClientID = $this->getArrayValue($expense, "client_id");
            $expenseClientUniqueID = $this->getArrayValue($expense, "client_unique_id");

            $images = $this->getArrayValue($expense, "images");
            $imagePaths = $this->getArrayValue($expense, "image_paths");

            if ($exp) {

                //saving images
                if (!empty($imagePaths)) {
                    $jsonDecoded = json_decode($images, true);
                    $imageArray = array();
                    $tempImageNames = array();
                    $tempImagePaths = array();
                    foreach ($jsonDecoded as $key => $value) {
                        $tempImageName = $this->getImageName();
                        $tempImageDir = $this->getImagePath($companyID, "expense");
                        $tempImagePath = "/storage/app/public/" . $tempImageDir . "/" . $tempImageName;
                        $decodedData = base64_decode($value);
                        $put = \Storage::disk('public')->put($tempImageDir . '/' . $tempImageName, base64_decode($value));
                        array_push($tempImageNames, $tempImageName);
                        array_push($tempImagePaths, $tempImagePath);
                        $imageArray[$tempImageName] = $tempImagePath;
                    }

                    if (!empty($imageArray)) {
                        $imageData = array();
                        foreach ($imageArray as $imageName => $imagePath) {
                            $tempImageArray = array();
                            $tempImageArray["type"] = "expense";
                            $tempImageArray["type_id"] = $exp->id;
                            $tempImageArray["company_id"] = $companyID;
                            $tempImageArray["employee_id"] = $exp->employee_id;
                            $tempImageArray["image"] = $imageName;
                            $tempImageArray["image_path"] = $imagePath;
                            $tempImageArray["created_at"] = $this->getArrayValue($expense, "created_at");
                            array_push($imageData, $tempImageArray);
                        }
                        DB::table('images')->insert($imageData);
                    }
                }

                $finalImages = DB::table('images')->where('type','expense')->where('type_id',$exp->id)->whereNull('deleted_at')->get();
                foreach($finalImages as $finalImage){
                    array_push($images_ids,$finalImage->id);    
                    array_push($images_names,$finalImage->image);    
                    array_push($images_paths,$finalImage->image_path);    
                }

                $exp->image_ids   = json_encode($images_ids);
                if($exp->image_ids=='[]'){
                    $exp->image_ids = null;
                }
                $exp->images      = json_encode($images_names);
                if($exp->images=='[]'){
                    $exp->images = null;
                }
                $exp->image_paths = json_encode($images_paths);
                if($exp->image_paths=='[]'){
                    $exp->image_paths = null;
                }

                // $expenseData["id"] = $expense->id;
                // $exp->images = $tempImageNames;
                // $exp->image_paths = $tempImagePaths;
                array_push($arraySyncedData, $exp);
                $returnItems = true;
                if($exp->id){
                    $save = saveAdminNotification($companyID, $employeeID, date("Y-m-d H:i:s"), "Expense Added", "expense", $exp);
                }else{
                    $save = saveAdminNotification($companyID, $employeeID, date("Y-m-d H:i:s"), "Expense Updated", "expense", $exp);
                }
            }
        }

        return $returnItems ? $arraySyncedData : false;
    }

    function getImageArray($type, $typeID,$companyID=null)
	{

	    if (empty($type) || empty($typeID)) return array();
	    $whereArray = empty($companyID)?[['type_id',$typeID],['type',$type]]:[['type_id',$typeID],['type',$type],['company_id',$companyID]];
	    $images = DB::table('images')->where($whereArray)->get()->toArray();
	    if (empty($images)) return array();
	    $finalArray = array();
	    $imageArray = array();
	    $pathArray = array();
	    foreach ($images as $key => $value) {
	        array_push($imageArray, $value->image);
	        array_push($pathArray, $value->image_path);
	    }

	    $finalArray["images"] = $imageArray;
	    $finalArray["image_paths"] = $pathArray;
	    return $finalArray;
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
        $imagePrefix = md5(uniqid(time(), true));
        $imageName = $imagePrefix . ".png";
        return $imageName;
    } 
}
