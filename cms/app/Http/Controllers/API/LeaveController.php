<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\Leave;
use App\Employee;
use DB;
use Log;

class LeaveController extends Controller
{
    public function __construct()
    {
    	$this->middleware('auth:api');
    	$this->middleware('permission:leave-create', ['only' => ['create','store']]);
        $this->middleware('permission:leave-view');
        $this->middleware('permission:leave-update', ['only' => ['edit','update']]);
        $this->middleware('permission:leave-delete', ['only' => ['destroy']]);
    }

    public function fetchPendingLeave(Request $request){
      $user = Auth::user();
      $companyID = $user->company_id;
      $employee = Employee::where('company_id',$companyID)->where('user_id',$user->id)->first();
      $employeeID = $employee->id;
      $immediateJuniors = Employee::where('superior', $employee->id)->pluck('id')->toArray();
      
      $leaves = Leave::with(['employee' => function($query){
        $query->select('name', 'id');
      }])->with(['leave_type' => function($query){
        $query->select('name', 'id');
      }])->where('company_id',$companyID)->whereIn('employee_id',$immediateJuniors)->where("status", "Pending")->orderby("id", "desc")->get()->map(function ($leave) {
        return $this->formatLeave($leave);
      })->toArray();
      
      $immediateJuniors = json_encode($immediateJuniors);


      $response = array("status" => true, "message" => "Success", "data" => $leaves, "immediateJuniors" => $immediateJuniors);
      $this->sendResponse($response);

    }

    private function formatLeave($object){
      try {
        $formatted_data = [
          "id"=> $object->id,
          "unique_id"=> $object->unique_id,
          "company_id"=> $object->company_id,
          "leave_title"=> $object->leave_title,
          "leavetype"=> $object->leavetype,
          "leavetype_name"=> $object->leave_type? $object->leave_type->first()->name:null,
          "employee_id"=> $object->employee_id,
          "employee_name"=> $object->employee_id ? $object->employee->name : null,
          "employee_type"=> $object->employee_type,
          "start_date"=> $object->start_date,
          "end_date"=> $object->end_date,
          "leave_desc"=> $object->leave_desc,
          "remarks"=> $object->remarks,
          "status"=> $object->status,
          "approved_by"=> $object->approved_by,
          "status_reason"=> $object->status_reason,
          "leave_status_change"=> $object->leave_status_change,
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


    public function changeLeaveStatus(Request $request)
    {
      $leave = Leave::where('id',$request->leave_id)->first();
      $user = Auth::user();
      $companyID = $user->company_id;
      $employee = Employee::where('company_id',$companyID)->where('user_id',$user->id)->first();
      $employeeID = $employee->id;
      
      if($leave){
        $leave->status = $request->status;
        $leave->status_reason = $request->remark;
        if($leave->status=='Approved' || $leave->status=="Rejected"){
          $leave->approved_by = $employeeID;
        }
        $saved = $leave->save();
        if (!empty($saved)) {
          $temp = Employee::where('id', $leave->employee_id)->first();
          if (!empty($temp)) {
            $notificationData = array(
              "company_id" => $temp->company_id,
              "employee_id" => $temp->id,
              "data_type" => "leave",
              "data" => "",
              "action_id" => $leave->id,
              "title" => "Leave " . $request->status,
              "description" => "Your Leave has been " . $request->status,
              "created_at" => date('Y-m-d H:i:s'),
              "status" => 1,
              "to" => 1
            );

            $sendingNotificationData = $notificationData;
            $sendingNotificationData["unix_timestamp"] = time();
            if (!empty($temp->firebase_token)) {

              $dataPayload = array("data_type" => "Leave", "leave" => $leave, "action" => "update_status", "leave_id" => $leave->id, "status" => $leave->status, "remark" => $leave->remarks);
              $msgID = sendPushNotification_([$temp->firebase_token], 2, $sendingNotificationData, $dataPayload);
            }
          }
        } 

        return response()->json([
          'message' => 'Leave Updated Successfully',
          'data' => $leave
        ], 200);
      }

      return response()->json([
        'message' => 'Leave Not Found',
        'data' => $leave
      ], 422);
    }


    public function index($return = false, $tempPostData = null)
    {
        $postData = $return ? $tempPostData : $this->getJsonRequest();
        $user = Auth::user();
        $companyID = $user->company_id;
        $employee = Employee::where('company_id',$companyID)->where('user_id',$user->id)->first();
        $employeeID = $employee->id;
        /*Check if unsynced data is available . if available first update to tha database */
        $syncStatus = $this->manageUnsyncedLeave($postData);

        $leaves = Leave::where('company_id',$companyID)->where('employee_id',$employeeID)->get()->toArray();

        if (empty($leaves)) {
            if ($return) {
                return array();
            } else {

                $this->sendEmptyResponse();
            }
        }

        $response = array("status" => true, "message" => "Success", "data" => $leaves);

        if ($return) {
            return $leaves;
        } else {
            $this->sendResponse($response);
        }
    }

    public function store()
    {
        //Log::info('info', array("saveLeave"=>print_r("Inside saveLeave",true)));
        $postData = $this->getJsonRequest();
        $user = Auth::user();
        $companyID = $user->company_id;
        $employee = Employee::where('company_id',$companyID)->where('user_id',$user->id)->first();
        $employeeID = $employee->id;
        $createdAt = $this->getArrayValue($postData, "created_at");
        $uniqueID = $this->getArrayValue($postData,"unique_id");
        $leaveID = $this->getArrayValue($postData,"leave_id");

        $leaveData = array(

            'unique_id' => $uniqueID,
            'company_id' => $companyID,
            'employee_id' => $employeeID,
            'start_date' => $this->getArrayvalue($postData, "start_date"),
            'end_date' => $this->getArrayValue($postData, "end_date"),
            'leavetype' => $this->getArrayValue($postData, "leavetype"),
            'leave_desc' => $this->getArrayValue($postData, "leave_desc"),
            'status' => $this->getArrayValue($postData, "status"),
            'status_reason' => $this->getArrayValue($postData, "remark"),
            'created_at' => $this->getArrayValue($postData, "created_at")
        );

        if(!empty($uniqueID)){
            
            $leave = Leave::updateOrCreate(
                [
                    "unique_id" => $uniqueID
                ],
                $leaveData
            );

        } elseif(!empty($leaveID)){

            $leave = Leave::updateOrCreate(
                [
                    "id" => $leaveID
                ],
                $leaveData
            );
        }
        
        $wasRecentlyCreated = $leave->wasRecentlyCreated;
        $wasChanged = $leave->wasChanged();
        $isDirty = $leave->isDirty();
        $exists = $leave->exists;
        //Log::info('info', array("leave flags"=>print_r("wasRecentlyCreated/wasChanged/isDirty/exists:".$wasRecentlyCreated." ,".$wasChanged." ,".$isDirty." ,".$exists,true)));

        if ($wasRecentlyCreated || $wasChanged || $leave->exists) {

            $msg = "";
            $savedLeave = $leaveData;
            $savedLeave["id"] = $leave->id;
            
            if ($leave->wasRecentlyCreated) {
                
                $msg = "Applied For Leave";
            } else {

              $msg = "Updated Leave";

              /**
               * Update From EveryOne Notification
               */
              if($employeeID != $leave->employee_id){
                $temp = Employee::where('company_id',$companyID)->where('id',$leave->employee_id)->first();
                if (!empty($temp)) {
                  $notificationData = array(
                    "company_id" => $temp->company_id,
                    "employee_id" => $temp->id,
                    "data_type" => "leave",
                    "data" => "",
                    "action_id" => $leave->id,
                    "title" => "Leave " . $leave->status,
                    "description" => "Your Leave has been updated",
                    "created_at" => date('Y-m-d H:i:s'),
                    "status" => $leave->status,
                    "to" => 1
                  );
                  $sendingNotificationData = $notificationData;
                  $sendingNotificationData["unix_timestamp"] = time();
  
                  if (!empty($temp->firebase_token)) {
                    $dataPayload = array("data_type" => "leave", "leave" => $leave, "action" => "update");
                    $msgID = sendPushNotification_([$temp->firebase_token], 2, $sendingNotificationData, $dataPayload);
                  }
                }
              }
            }

            $nSaved = saveAdminNotification($companyID, $employeeID, date("Y-m-d H:i:s"), $msg, "leave", $savedLeave);
            $response = array("status" => true, "message" => "successfully saved", "leave_id" => $leave->id);
            $this->sendResponse($response);
        } else {

            $this->sendEmptyResponse();
        }

    }

    public function syncLeave()
    {
        $postData = $this->getJsonRequest();
        $arraySyncedLeave = $this->manageUnsyncedLeave($postData, true);
        $response = array("status" => true, "message" => "success", "data" => $arraySyncedLeave);
        $this->sendResponse($response);
    }

    private function manageUnsyncedLeave($postData, $returnItems = false)
    {

        $rawData = $this->getArrayValue($postData, "unsynced_data");
        $user = Auth::user();
        $companyID = $user->company_id;
        $employee = Employee::where('company_id',$companyID)->where('user_id',$user->id)->first();
        $employeeID = $employee->id;

        if (empty($rawData)) {

            return $returnItems ? array() : false;
        }
        $data = json_decode($rawData, true);

        $arraySyncedData = array();

        /*prepare data for saving*/
        foreach ($data as $key => $value) {

            $leave_id = $this->getArrayValue($value, "leave_id");
            $unique_id = $this->getArrayValue($value, "unique_id");
            $leave = Leave::where('company_id',$companyID)->where('id',$leave_id)->orWhere('unique_id',$unique_id)->first();
            if(!$leave){
                $leave = new Leave();
                $leave->created_at = $this->getArrayvalue($value, "created_at"); 
                $leave->employee_id = $employeeID;
                $leave->unique_id = $unique_id;    
            }
            $leave->company_id = $companyID;
            $leave->leavetype = $this->getArrayvalue($value, "leavetype");;
            $leave->start_date = $this->getArrayvalue($value, "start_date");
            $leave->end_date = $this->getArrayvalue($value, "end_date");
            $leave->leave_desc = $this->getArrayvalue($value, "leave_desc");
            $leave->status = $this->getArrayvalue($value, "status");
            $leave->status_reason = $this->getArrayvalue($value, "remark");
            $leave->save();
            
            if ($leave) {
                $syncedData = $leave;
                array_push($arraySyncedData, $syncedData);
                saveAdminNotification($companyID, $employeeID, date("Y-m-d H:i:s"), "Applied For Leave", "leave", $syncedData);
            }
        }

        return $returnItems ? $arraySyncedData : true;
    }

    public function destroy()
    {
        $postData = $this->getJsonRequest();
        $leaveID = getArrayValue($postData,"leave_id");
        $user = Auth::user();
        $companyID = $user->company_id;
        $employee = Employee::where('company_id',$companyID)->where('user_id',$user->id)->first();
        $employeeID = $employee->id;
        $leave = Leave::find(getArrayValue($postData,"leave_id"));

        $response = array("status" => false, "message" => "Delete Fail");

        if(!empty($leave)){

            $deleted = $leave->delete();
            if($deleted){

                if($employeeID!=$leave->employee_id){
                  $temp = Employee::where('id', $leave->employee_id)->first();
                  $dataPayload = array("data_type" => "Leave", "leave" => $leave->id, "action" => "delete");
                  $sent = sendPushNotification_([$temp->firebase_token], 2, null, $dataPayload);
                }

                $nSaved = saveAdminNotification($companyID, $employeeID, date("Y-m-d H:i:s"), "Cancelled Leave", "leave", $leave);
                $response = array("status" => true, "message" => "successfully Deleted", "leave_id" => $leave->id);
            } else {

                $response = array("status" => true, "message" => "Delete Fail", "leave_id" => $leave->id);

            }
        }

        $this->sendResponse($response);
    }

    //common methods
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

    private function sendEmptyResponse()
    {
        $response = array("status" => true, "message" => "No Record Found", "data" => array());
        echo json_encode($response);
        exit;
    }
}
