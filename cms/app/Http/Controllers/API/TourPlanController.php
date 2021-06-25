<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\TourPlan;
use App\Employee;
use Auth;
use DB;

class TourPlanController extends Controller
{
    public function __construct()
    {
		$this->middleware('auth:api');
        $this->middleware('permission:tourplan-create', ['only' => ['store']]);
        $this->middleware('permission:tourplan-view');
        $this->middleware('permission:tourplan-update', ['only' => ['store']]);
        $this->middleware('permission:tourplan-delete', ['only' => ['destroy']]);
    }

    public function index($return = false,$postData = null)
    {
        $postData   = $return?$postData:$this->getJsonRequest();
        $user       = Auth::user();
        $companyID  = $user->company_id;
        $employee   = Employee::where('company_id',$companyID)->where('user_id',$user->id)->first();
        $employeeID = $employee->id;
        $offset     = $this->getArrayValue($postData, "offset",0);
        $limit      = $this->getArrayValue($postData, "limit",200);

         /*Check if unsynced data is available . if available first update to tha database */
        $syncStatus = $this->manageUnsyncedTourPlans($postData);

        $tour_plans = TourPlan::where('company_id', $companyID)
            ->where('employee_id',$employeeID)
            ->get()->toArray();

        $response = array("status" => true, "message" => "Success", "data" => $tour_plans);
        if($return){

            return $tour_plans;

        } else {

            $this->sendResponse($response);
        }
    }

    public function fetchPendingTourPlan(Request $request){
      $user = Auth::user();
      $companyID = $user->company_id;
      $employee = Employee::where('company_id',$companyID)->where('user_id',$user->id)->first();
      $employeeID = $employee->id;
      $immediateJuniors = Employee::where('superior', $employee->id)->pluck('id')->toArray();
      
      $tourplans = TourPlan::where('company_id',$companyID)->with(['employee' => function($query){
        $query->select('name', 'id');
      }])->whereIn('employee_id',$immediateJuniors)->where("status", "Pending")->orderBy('id', 'desc')->get()->map(function ($tourplan) {
        return $this->formatTourPlan($tourplan);
      })->toArray();
      
      $immediateJuniors = json_encode($immediateJuniors);


      $response = array("status" => true, "message" => "Success", "data" => $tourplans, "immediateJuniors" => $immediateJuniors);
      $this->sendResponse($response);

    }

    private function formatTourPlan($object){
      try {
        $formatted_data = [
          "id"=> $object->id,
          "unique_id"=> $object->unique_id,
          "company_id"=> $object->company_id,
          "employee_id"=> $object->employee_id,
          "employee_name"=> $object->employee_id ? $object->employee->name : null,
          "start_date"=> $object->start_date,
          "end_date"=> $object->end_date,
          "visit_place"=> $object->visit_place,
          "visit_purpose"=> $object->visit_purpose,
          "remarks"=> $object->remarks,
          "status"=> $object->status,
          "created_at"=> date('Y-m-d H:i:s', strtotime($object->created_at)),
          "updated_at"=> date('Y-m-d H:i:s', strtotime($object->updated_at)),
          "deleted_at"=> date('Y-m-d H:i:s', strtotime($object->deleted_at)),
        ];

        return $formatted_data;
      } catch (\Exception $e) {
          Log::error(array("Format Leave () => "), array($e->getMessage()));
          return array($e->getMessage());
      }
    }


    public function changeTourPlanStatus(Request $request)
    {
      $tourplan = Tourplan::where('id',$request->tourplan_id)->first();
      $user = Auth::user();
      $companyID = $user->company_id;
      $employee = Employee::where('company_id',$companyID)->where('user_id',$user->id)->first();
      $employeeID = $employee->id;
      
      if($tourplan){
        $tourplan->status = $request->status;
        $tourplan->remark = $request->remark;
        $saved = $tourplan->save();
        if (!empty($saved)) {
          $temp = Employee::where('id', $tourplan->employee_id)->first();
          if (!empty($temp)) {
            $notificationData = array(
              "company_id" => $temp->company_id,
              "employee_id" => $temp->id,
              "data_type" => "tourplan",
              "data" => "",
              "action_id" => $tourplan->id,
              "title" => "Tourplan " . $request->status,
              "description" => "Your tourplan has been " . $request->status,
              "created_at" => date('Y-m-d H:i:s'),
              "status" => 1,
              "to" => 1
            );

            $sendingNotificationData = $notificationData;
            $sendingNotificationData["unix_timestamp"] = time();
            if (!empty($temp->firebase_token)) {
              $dataPayload = array("data_type" => "TourPlan", "tourplan" => $tourplan, "action" => "update_status", "tourplan_id" => $tourplan->id, "status" => $tourplan->status, "remark" => $tourplan->remark);
                    
              // $dataPayload = array("data_type" => "tourplan", "tourplan" => $tourplan, "action" => "update_status", "tourplan_id" => $tourplan->id, "status" => $tourplan->status, "remark" => $tourplan->remarks);
              $msgID = sendPushNotification_([$temp->firebase_token], 18, $sendingNotificationData, $dataPayload);
            }
          }
        } 

        return response()->json([
          'message' => 'Tourplan Updated Successfully',
          'data' => $tourplan
        ], 200);
      }

      return response()->json([
        'message' => 'Tourplan Not Found',
        'data' => $tourplan
      ], 422);
    }

    public function store($return=false,$postData = null)
    {
        $postData        = $this->getJsonRequest();
        $user            = Auth::user();
        $companyID       = $user->company_id;
        $employee        = Employee::where('company_id',$companyID)->where('user_id',$user->id)->first();
        $employeeID      = $employee->id;
        $encodedTourPlan = $this->getArrayValue($postData,"tourplan");
        $decodedTourPlan = json_decode($encodedTourPlan,true);
        $uniqueID        = $this->getArrayValue($decodedTourPlan, "unique_id");
        $tourPlanID      = $this->getArrayValue($decodedTourPlan, "tour_plan_id");
        
        $tourPlanData = array(
            'unique_id'     => $uniqueID,
            'company_id'    => $companyID,
            'employee_id'   => $employeeID,
            'start_date'    => $this->getArrayvalue($decodedTourPlan, "start_date"),
            'end_date'      => $this->getArrayValue($decodedTourPlan, "end_date"),
            'visit_place'   => $this->getArrayValue($decodedTourPlan, "visit_place",""),
            'visit_purpose' => $this->getArrayValue($decodedTourPlan, "visit_purpose",""),
            'status'        => $this->getArrayValue($decodedTourPlan, "status"),
            "remark" => $this->getArrayValue($decodedTourPlan, "remark"),
            'created_at'    => $this->getArrayValue($decodedTourPlan, "created_at")
        );
        //Log::info('info', array('tourPlanData'=>print_r($tourPlanData,true)));

        if(!empty($uniqueID)){
            
            $tourPlan = TourPlan::updateOrCreate(
                [
                    "unique_id" => $uniqueID
                ],
                $tourPlanData
            );

        } elseif(!empty($tourPlanID)){

            $tourPlan = TourPlan::updateOrCreate(
                [
                    "id" => $tourPlanID
                ],
                $tourPlanData
            );
        }
        
        $wasRecentlyCreated = $tourPlan->wasRecentlyCreated;
        $wasChanged = $tourPlan->wasChanged();
        $isDirty = $tourPlan->isDirty();
        $exists = $tourPlan->exists;

        if ($wasRecentlyCreated || $wasChanged || $tourPlan->exists) {

            $msg = "";
            $savedtourPlan = $tourPlanData;
            $savedtourPlan["id"] = $tourPlan->id;
            
            if ($tourPlan->wasRecentlyCreated) {
                
                $msg = "Created Tour Plan";
            } else {

                $msg = "Updated Tour Plan";

            }

            $nSaved = saveAdminNotification($companyID, $employeeID, date("Y-m-d H:i:s"), $msg, "tours", $savedtourPlan);
            $response = array("status" => true, "message" => $msg, "tour_plan_id" => $tourPlan->id);
        } else {
            
            $response = array("status" => false, "message" => "Unable to create/update", "tourplan" => "");
            
        }
        $this->sendResponse($response);

    }

    public function destroy(Request $request)
    {
        $user       = Auth::user();
        $company_id = $user->company_id;
        $employeeID = Employee::where('company_id',$company_id)->where('user_id',$user->id)->first()->id;
        $tourplan   = TourPlan::where('company_id',$company_id)->where('employee_id',$employeeID)->where('id',$request->tour_plan_id)->first();
        if(!$tourplan)
            return response(['status'=>false,'error'=>'No Tourplan found or no permission access to delete this day remark.']);
        $tourplan->delete();
        $nSaved = saveAdminNotification($company_id, $employeeID, date("Y-m-d H:i:s"),'TourPlan Deleted', "tourplan", $tourplan);
        return response(['status'=>true,'message'=>'Tourplan Deleted']);
    }

    public function syncTourPlans()
    {

        $postData        = $this->getJsonRequest();
        $arraySyncedData = $this->manageUnsyncedTourPlans($postData, true);
        $response        = array("status" => true, "message" => "success", "data" => $arraySyncedData);
        $this->sendResponse($response);

    }

    public function manageUnsyncedTourPlans($postData, $returnItems = false)
    {

        $rawData    = $this->getArrayValue($postData, "unsynced_data");
        $user       = Auth::user();
        $companyID  = $user->company_id;
        $employee   = Employee::where('company_id',$companyID)->where('user_id',$user->id)->first();
        $employeeID = $employee->id;

        if (empty($rawData)) {

            return $returnItems ? array() : false;
        }
        $data = json_decode($rawData, true);

        $arraySyncedData = array();

        /*prepare data for saving*/
        foreach ($data as $key => $value) {

            $tempArray["unique_id"]     = $this->getArrayValue($value, "unique_id");
            $tempArray["company_id"]    = $companyID;
            $tempArray["employee_id"]   = $employeeID;
            $tempArray["start_date"]    = $this->getArrayvalue($value, "start_date");
            $tempArray["end_date"]      = $this->getArrayvalue($value, "end_date");
            $tempArray["visit_place"]   = $this->getArrayvalue($value, "visit_place");
            $tempArray["visit_purpose"] = $this->getArrayvalue($value, "visit_purpose");
            $tempArray["status"]        = $this->getArrayvalue($value, "status");
            $tempArray["remark"]        = $this->getArrayvalue($value, "remark");
            $tempArray["created_at"]    = $this->getArrayvalue($value, "created_at");

            $savedID = DB::table('tourplans')->insertGetId($tempArray);

            if (!empty($savedID)) {

                $syncedData             = $tempArray;
                $syncedData['id']       = $savedID;
                array_push($arraySyncedData, $syncedData);
                //saveAdminNotification($companyID, $employeeID, date("Y-m-d H:i:s"), "Added TourPlan", "tourplan", $syncedData);
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
}
