<?php

namespace App\Http\Controllers\API;

use Log;
use Auth;
use App\Activity;
use App\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\ActivityRequest;

class ActivityController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('permission:activity-create', ['only' => ['create','store']]);
        $this->middleware('permission:activity-view');
        $this->middleware('permission:activity-update', ['only' => ['edit','update']]);
        $this->middleware('permission:activity-status', ['only' => ['changeStatus']]);
        $this->middleware('permission:activity-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $companyID = $user->company_id;
        $authEmp = Employee::where('company_id', $companyID)->where('user_id', $user->id)->first();
        $empId = $authEmp->id;
        $offset = $this->getArrayValue($request->all(), "offset", 0);
        $limit = $this->getArrayValue($request->all(), "data_limit", 200);


        if (!(Auth::user()->isCompanyManager())) {
            $juniors = Employee::EmployeeChilds($authEmp->id, array());
        }

        $activities = Activity::select('activities.*', 'activity_types.id as activity_type_id', 'activity_types.name as type_name', 'activity_priorities.id as activity_priority_id', 'activity_priorities.name as priority_name', 'emptbl1.name as assigned_to_name', 'emptbl2.name as created_by_name')
                    ->leftJoin('employees as emptbl1', 'activities.assigned_to', 'emptbl1.id')
                    ->leftJoin('employees as emptbl2', 'activities.created_by', 'emptbl2.id')
                    ->leftJoin('activity_types', 'activities.type', 'activity_types.id')
                    ->leftJoin('activity_priorities', 'activities.priority', 'activity_priorities.id')
                    ->where("activities.company_id", $companyID);
        if ($request->type=="juniors") {
            if (!(Auth::user()->isCompanyManager())) {
                $activities = $activities->where(function ($q) use ($juniors,$empId) {
                    $q = $q->whereIn('activities.created_by', $juniors)->whereIn('activities.assigned_to', $juniors, 'or')->orWhere('activities.created_by', $empId)->orWhere('activities.assigned_to', $empId);
                });
            }
        } else {
            $activities = $activities->where(function ($q) use ($empId) {
                $q = $q->where('activities.created_by', $empId)->orWhere('activities.assigned_to', $empId);
            });
        }

        $activities = $activities->where('activities.id', '>', "$offset")->orderBy('activities.id', 'ASC')->limit($limit)->get()->toArray();
        $response = array("status" => true, "message" => "Success", "data" => $activities);
        return response($response);
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

    public function fetchActivitiesChanges(Request $request, $return = false, $tempPostData = null)
    {
        $postData = $return ? $tempPostData : $this->getJsonRequest();
        $user = Auth::user();
        $companyID = $user->company_id;
        $authEmp = Employee::where('company_id', $companyID)->where('user_id', $user->id)->first();
        $empId = $authEmp->id;

        $offset = $this->getArrayValue($postData, "offset");
        $length = $this->getArrayValue($postData, "data_limit");

        $fetchToken = $request->fetch_token;
        $lastFetchObject = DB::table('changes_last_fetched')->where('unique_token', $fetchToken)->first();
        $finalArray = array('created_records'=> array(), 'updated_records'=>array(), 'deleted_records'=>array());
        if (! $lastFetchObject) {
            return array();
        }

        $lastFetchedDatetime = $lastFetchObject->activity_fetch_datetime;
        DB::beginTransaction();
        DB::table('changes_last_fetched')->updateOrInsert(
            ['unique_token' => $fetchToken],
            ['user_id' => $user->id, 'unique_token' => $fetchToken, 'activity_fetch_datetime'=>date('Y-m-d H:i:s')]
        );
        DB::commit();
      
        if (!(Auth::user()->isCompanyManager())) {
            $juniors = Employee::EmployeeChilds($authEmp->id, array());
        }
        $activities = Activity::withTrashed()->select('activities.*', 'activity_types.id as activity_type_id', 'activity_types.name as type_name', 'activity_priorities.id as activity_priority_id', 'activity_priorities.name as priority_name', 'emptbl1.name as assigned_to_name', 'emptbl2.name as created_by_name')
                    ->leftJoin('activity_types', 'activities.type', 'activity_types.id')
                    ->leftJoin('activity_priorities', 'activities.priority', 'activity_priorities.id')
                    ->leftJoin('employees as emptbl1', 'activities.assigned_to', 'emptbl1.id')
                    ->leftJoin('employees as emptbl2', 'activities.created_by', 'emptbl2.id')
                    ->where("activities.company_id", $companyID)
                    ->orderBy('completion_datetime', 'ASC')
                    ->where(function ($query) use ($lastFetchedDatetime) {
                        if ($lastFetchedDatetime) {
                            $query->orWhere('activities.created_at', '>', $lastFetchedDatetime);
                            $query->orWhere('activities.updated_at', '>', $lastFetchedDatetime);
                            $query->orWhere('activities.deleted_at', '>', $lastFetchedDatetime);
                        }
                    });
        if ($request->type=="juniors") {
            if (!(Auth::user()->isCompanyManager())) {
                $activities = $activities->where(function ($q) use ($juniors,$empId) {
                    $q = $q->whereIn('activities.created_by', $juniors)->whereIn('activities.assigned_to', $juniors, 'or')->orWhere('activities.created_by', $empId)->orWhere('activities.assigned_to', $empId);
                });
            }
        } else {
            $activities = $activities->where(function ($q) use ($empId) {
                $q = $q->where('activities.created_by', $empId)->orWhere('activities.assigned_to', $empId);
            });
        }

        $activities = $activities->where('activities.id', '>', "$offset")
                ->orderBy('activities.id', 'asc')
                ->limit($length)->get();

        if (empty($activities)) {
            if ($return) {
                return array();
            } else {
                $this->sendEmptyResponse();
            }
        }
      
        foreach ($activities as $activity) {
            if ($activity->deleted_at) {
                $finalArray['deleted_records'][] = (int)$activity->id;

                continue;
            }

            if ($activity->updated_at && ($activity->updated_at>$activity->created_at)) {
                $finalArray['updated_records'][] = $activity;
            } elseif ($activity->created_at && ($activity->updated_at==$activity->created_at)) {
                $finalArray['created_records'][] = $activity;
            }
        }
        $response = array("status" => true, "message" => "Success", "data" => $finalArray);
      
        if ($return) {
            return $finalArray;
        } else {
            $this->sendResponse($response);
        }
    }

    public function store(ActivityRequest $request)
    {
        $user = Auth::user();
        $companyID = $user->company_id;
        $employee = Employee::where('user_id', $user->id)->where('company_id', $companyID)->select('id')->first();
        $employeeID = $employee->id;
        if ($request->activity_id) {
            $activity = Activity::where('id', $request->activity_id)->where('company_id', $companyID)->first();
            $activity->unique_id = $request->unique_id;
            $title = "Updated Activity";
            $created = false;
            $status = "Activity has been updated";
            /**
           * If updated assigned to to another chain user
           * than send deleted to this chain
           */
            $previous_assigned_to = (int)$activity->assigned_to;
        } else {
            $activity = new Activity();
            $activity->created_by = $employeeID;
            $activity->unique_id = $request->unique_id;
            $activity->created_by = $employeeID;
            $title = "Added Activity";
            $created = true;
            $status = "A new activity has been assigned to you";
        }
        if ($request->completion_datetime) {
            $activity->completion_datetime = $request->completion_datetime;
        } else {
            $activity->completion_datetime = null;
        }
        $activity->type = $request->type;
        $activity->title = $request->title;
        $activity->note = $request->notes;
        $activity->start_datetime = $request->start_datetime;
        $activity->duration = $request->duration;
        $activity->priority = $request->priority;
        $activity->assigned_to = $request->assigned_to;
        $activity->client_id = $request->client_id;
        $clientUniqueID    = $request->client_unique_id;
        if(isset($clientUniqueID) && empty($request->client_id)) throw new \Exception('Client Id cannot be null');
        $activity->completed_by = $request->completed_by;
        $activity->company_id = $companyID;
        $savedActivity = $activity->save();

        if ($savedActivity) {
            $assignedTo = $activity->assigned_to;
            if (!$created) {
              $nSaved = saveAdminNotification($companyID, $employeeID, date("Y-m-d H:i:s"), "Activity Updated", "activities", $activity);
            } else {
              $nSaved = saveAdminNotification($companyID, $employeeID, date("Y-m-d H:i:s"), "Activity Added", "activities", $activity);
            }

            $assigned_to_superiors = Employee::employeeParents((int)$activity->assigned_to, array());

            $created_by_superiors = $activity->assigned_to==$activity->created_by?array():Employee::employeeParents((int)$activity->created_by, array());

            $merged_superiors = array_unique(array_merge($assigned_to_superiors, $created_by_superiors));
            
            $mergedSuperiorsTokens = Employee::where('company_id', $companyID)->whereIn('id', $merged_superiors)->pluck('firebase_token', 'id')->toArray();

            if (!empty($mergedSuperiorsTokens)) {
                $notificationData = array(
                    "company_id" => $companyID,
                    "employee_id" => $assignedTo,
                    "data_type" => "activity",
                    "data" => "",
                    "action_id" => $activity->id,
                    "title" => $status,
                    "description" => $activity->title,
                    "created_at" => date('Y-m-d H:i:s'),
                    "status" => 1,
                    "to" => 1,
                    "unix_timestamp" => time()
                );
                $createdByNotfData = $notificationData;                
                $assignedToNotfData = $notificationData;

                $activity->company_name = getObjectValue($activity->client, "company_name", "N/A");
                $activity->priority_name = getObjectValue($activity->activityPriority, "name", "");
                $activity->type_name = getObjectValue($activity->activityType, "name", "");

                if (!$created) {
                  $dataPayload = array("data_type" => "activity", "activity" => $activity, "action" => "update");
                  if ($previous_assigned_to!=$activity->assigned_to) {
                    $previous_chain = Employee::employeeSeniors($previous_assigned_to, array());
                    $notf_to_send_ids = array_diff($previous_chain, $merged_superiors);
                    $current_similarity = array_intersect($previous_chain, $merged_superiors);
                    $previousChainSuperiorsTokens = Employee::where('company_id', $companyID)->whereIn('id', $notf_to_send_ids)->pluck('firebase_token')->toArray();
                    if(!empty($previousChainSuperiorsTokens)){
                      $payload = array("data_type" => "activity", "activity" => $activity, "action" => "delete");
                      sendPushNotification_($previousChainSuperiorsTokens, 17, null, $payload);
                    }
                    /***
                     * Send add to current chain
                     * $assigned_to_superiors
                    */
                    $payloadForCurreentChain = array("data_type" => "activity", "activity" => $activity, "action" => "add");
                    $currentFBToken = array();
                    $currentCreatedByToken = array();
                    $currentAssignedToToken = array();
                    $nonEmpId = array();

                    foreach($mergedSuperiorsTokens as $empId=>$token){
                      if((int)$empId==(int)$activity->created_by && $activity->created_by!=$activity->assigned_to){
                        array_push($currentCreatedByToken, $token);
                        array_push($nonEmpId, $empId);
                      }elseif((int)$empId==(int)$activity->assigned_to){
                        array_push($currentAssignedToToken, $token);
                        array_push($nonEmpId, $empId);
                      }else{
                        array_push($currentFBToken, $token);
                      }
                      unset($mergedSuperiorsTokens[$empId]);
                    }
                    // sendPushNotification_($currentFBToken, 17, null, $payloadForCurreentChain);
                    $notData = $notificationData;
                    $checkAdmin = Employee::find($activity->assigned_to);
                    if($employeeID!=$activity->assigned_to && $checkAdmin->is_admin!=1){
                      $notData["title"] = "A new activity has been assigned to you";
                      if(in_array($activity->assigned_to, $created_by_superiors)) sendPushNotification_($currentAssignedToToken, 17, $notData, $dataPayload);
                      else sendPushNotification_($currentAssignedToToken, 17, $notData, $payloadForCurreentChain);
                    }

                    if($employeeID!=$activity->created_by && $activity->assigned_to!=$activity->created_by){
                      sendPushNotification_($currentCreatedByToken, 17, $notificationData, $dataPayload);
                    }

                    $mergedSuperiorsAdminTokens = Employee::where('company_id', $companyID)->whereIn('id', $merged_superiors)->where('is_admin', 1)->orWhereIn('id', $current_similarity)->whereNotIn('id',$nonEmpId)->pluck('firebase_token', 'id')->toArray();
                    sendPushNotification_(array_values($mergedSuperiorsAdminTokens), 17, null, $dataPayload);
                    $currentFBToken = array_diff($currentFBToken, $mergedSuperiorsAdminTokens);

                    sendPushNotification_($currentFBToken, 17, null, $payloadForCurreentChain);
                  }
                } else {
                  $dataPayload = array("data_type" => "activity", "activity" => $activity, "action" => "add");

                  $assigned_to_token = $mergedSuperiorsTokens[$activity->assigned_to];
                  /**
                   * if self logged in and self assigned than don't send notification to self
                  */
                  if($activity->assigned_to == $employeeID) $assignedToNotfData = null;
                  unset($mergedSuperiorsTokens[$activity->assigned_to]);

                  if(array_key_exists($employeeID, $mergedSuperiorsTokens)) unset($mergedSuperiorsTokens[$employeeID]);

                  sendPushNotification_(array_values($mergedSuperiorsTokens), 17, null, $dataPayload);
                  
                  if($assigned_to_token && $activity->assigned_to != $employeeID) sendPushNotification_(array($assigned_to_token), 17, $assignedToNotfData, $dataPayload);
                }

                if(!empty(array_values($mergedSuperiorsTokens)) && !$created){
                  if(array_key_exists($activity->assigned_to, $mergedSuperiorsTokens)){
                    $assigned_to_token = $mergedSuperiorsTokens[$activity->assigned_to];
                    unset($mergedSuperiorsTokens[$activity->assigned_to]);
                    if($activity->assigned_to != $employeeID) sendPushNotification_(array($assigned_to_token), 17, $assignedToNotfData, $dataPayload);
                  }
                  
                  if(array_key_exists($activity->created_by, $mergedSuperiorsTokens)){
                    if($activity->created_by!=$activity->assigned_to && $activity->created_by!=$employeeID){
                      $created_by_token = $mergedSuperiorsTokens[$activity->created_by];
                      unset($mergedSuperiorsTokens[$activity->created_by]);
                      sendPushNotification_(array($created_by_token), 17, $createdByNotfData, $dataPayload);
                    }
                  }
                  sendPushNotification_(array_values($mergedSuperiorsTokens), 17, null, $dataPayload);
                }
            }
        }

        $response = array("status" => true, "message" => "Success", "data" => $activity);
        $this->sendResponse($response);
    }//end store

    public function show($id)
    {
        $user = Auth::user();
        $companyID = $user->company_id;
        $employee = Employee::where('user_id', $user->id)->where('company_id', $companyID)->select('id')->first();
        $empId = $employee->id;
        $activity = Activity::where('company_id', $companyID)->where(function ($q) use ($empId) {
            $q = $q->where('created_by', $empId)->orWhere('assigned_to', $empId);
        })->where('id', $id)->first();
        if ($activity) {
            $response = array("status" => true, "message" => "Success", "data" => $activity);
        } else {
            $response = array("status" => true, "message" => "Activity not found", "data" => $activity);
        }
        return response($response);
    }//end show

    public function update(ActivityRequest $request, $id)
    {
        $user = Auth::user();
        $companyID = $user->company_id;
        $employee = Employee::where('user_id', $user->id)->where('company_id', $companyID)->select('id')->first();
        $employeeID = $employee->id;
        $activity = Activity::where('company_id', $companyID)->where('id', $id)->first();
        $activity->type = $request->type;
        $activity->title = $request->title;
        $activity->note = $request->notes;
        $activity->start_datetime = $request->start_datetime;
        $activity->duration = $request->duration;
        $activity->priority = $request->priority;
        $activity->assigned_to = $request->assigned_to;
        $activity->created_by = $employeeID;
        $activity->client_id = $request->client_id;
        $activity->company_id = $companyID;
        $savedActivity = $activity->save();

        $createdBy = $activity->created_by;
        $assignedTo = $activity->assigned_to;

        if ($employeeID == $createdBy) {
            $notifyTo = $assignedTo;
        } elseif ($employeeID == $assignedTo) {
            $notifyTo = $createdBy;
        }

        if (($employeeID == $createdBy) && ($createdBy == $assignedTo)) {
            $notifyTo = null;
            $nSaved = saveAdminNotification($companyID, $employeeID, date("Y-m-d H:i:s"), $title, "activities", $activity);
        }
        $assigned_to_superiors = Employee::employeeParents($activity->assigned_to, array());

        $created_by_superiors = $activity->assigned_to==$activity->created_by?array():Employee::employeeParents($activity->created_by, array());

        $merged_superiors = array_unique(array_merge($assigned_to_superiors, $created_by_superiors));
        
        $mergedSuperiorsTokens = Employee::where('company_id', $companyID)->whereIn('id', $merged_superiors)->pluck('firebase_token', 'id')->toArray();


        // $sendNotification = ($notifyTo != 0) && (!empty($notifyTo));

        if (!empty($mergedSuperiorsTokens)) {
            //send notification to employee
            // $fbID = getFBIDs($companyID, null, $notifyTo);

            $notificationData = array(
                    "company_id" => $companyID,
                    "employee_id" => $assignedTo,
                    "data_type" => "activity",
                    "data" => "",
                    "action_id" => $activity->id,
                    "title" => "A new activity has been assigned to you",
                    "description" => $activity->title,
                    "created_at" => date('Y-m-d H:i:s'),
                    "status" => 1,
                    "to" => 1,
                    "unix_timestamp" => time()
                );

            $activity->company_name = getObjectValue($activity->client, "company_name", "N/A");
            $activity->priority_name = getObjectValue($activity->activityPriority, "name", "");
            $activity->type_name = getObjectValue($activity->activityType, "name", "");

            $dataPayload = array("data_type" => "activity", "activity" => $activity, "action" => "update");
            // $sent = sendPushNotification_($fbID, 17, $notificationData, $dataPayload);

            $assigned_to_token = $mergedSuperiorsTokens[$activity->assigned_to];
            unset($mergedSuperiorsTokens[$activity->assigned_to]);

            $sent = sendPushNotification_(array_values($mergedSuperiorsTokens), 17, null, $dataPayload);
                
            $sent_only_to_assigned_to = sendPushNotification_(array($assigned_to_token), 17, $notificationData, $dataPayload);
        }

        $response = array("status" => true, "message" => "Success", "data" => $activity);
        $this->sendResponse($response);
    }//end store

    public function changeStatus(ActivityRequest $request)
    {
        return response($request);
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $companyID = $user->company_id;
        $employee = Employee::where('user_id', $user->id)->where('company_id', $companyID)->select('id', 'is_admin', 'firebase_token')->first();
        $employeeID = $employee->id;
        if ($employee->is_admin==1) {
            $activity = Activity::where('company_id', $companyID)->where('id', $id)->first();
        } else {
            $activity = Activity::where('company_id', $companyID)->where('created_by', $employeeID)->where('id', $id)->first();
        }
        if (!$activity) {
            return response(["status"=>false,"message"=>"No activity found"]);
        }
        $response = array("status" => false, "message" => "Delete Fail", "activity_id" => $id);

        if ($activity) {
            $activity_instance = $activity;
            $assignedTo = $activity->assigned_to; 
            $deleted = $activity->delete();
            if ($deleted) {
                $assigned_to_superiors = Employee::employeeParents((int)$activity_instance->assigned_to, array());

                $created_by_superiors = $activity_instance->assigned_to==$activity_instance->created_by?array():Employee::employeeParents((int)$activity_instance->created_by, array());

                $merged_superiors = array_unique(array_merge($assigned_to_superiors, $created_by_superiors));
                
                $mergedSuperiorsTokens = Employee::where('company_id', $companyID)->whereIn('id', $merged_superiors)->pluck('firebase_token', 'id')->toArray();

                $nSaved = saveAdminNotification($companyID, $employeeID, date("Y-m-d H:i:s"), "Deleted Activity", "activities", $activity);

                if (!empty($mergedSuperiorsTokens)) {
                    $notificationData = array(
                        "company_id" => $companyID,
                        "employee_id" => $assignedTo,
                        "data_type" => "activity",
                        "data" => "",
                        "action_id" => $activity_instance->id,
                        "title" => "Your activity has been deleted.",
                        "description" => $activity_instance->note,
                        "created_at" => date('Y-m-d H:i:s'),
                        "status" => 1,
                        "to" => 1,
                        "unix_timestamp" => time()
                    );
    
                    $activity_instance->company_name = getObjectValue($activity_instance->client, "company_name", "N/A");
                    $activity_instance->priority_name = getObjectValue($activity_instance->activityPriority, "name", "");
                    $activity_instance->type_name = getObjectValue($activity_instance->activityType, "name", "");
    
                    $dataPayload = array("data_type" => "activity", "activity" => $activity_instance, "action" => "delete");

                    $assigned_to_token = $mergedSuperiorsTokens[$activity_instance->assigned_to];
                    unset($mergedSuperiorsTokens[$activity_instance->assigned_to]);

                    if($activity_instance->assigned_to!=$activity_instance->created_by && $activity_instance->created_by!=$employeeID){
                      $created_by_token = $mergedSuperiorsTokens[$activity_instance->created_by];
                      unset($mergedSuperiorsTokens[$activity_instance->created_by]);
                      sendPushNotification_(array($created_by_token), 17, $notificationData, $dataPayload);
                    }

                    $sent = sendPushNotification_(array_values($mergedSuperiorsTokens), 17, null, $dataPayload);

                    if($employeeID==$activity->assigned_to) $notificationData = null;
                    if($employeeID!=$activity->assigned_to){
                      $sent_only_to_assigned_to = sendPushNotification_(array($assigned_to_token), 17, $notificationData, $dataPayload);
                    }

                    // $sent = sendPushNotification_($mergedSuperiorsTokens, 17, $notificationData, $dataPayload);
                }
                $response = array("status" => true, "message" => "Successfully Deleted", "activity_id" => $activity_instance->id);
            } else {
                $response = array("status" => false, "message" => "Delete Fail", "activity_id" => $activity_instance->id);
            }
        }
        return response($response);
    }

    public function syncActivities(Request $request)
    {
        $arraySyncedData = $this->manageUnsyncedActivities($request, true);
        $response = array("status" => true, "message" => "success", "data" => $arraySyncedData);
        return response($response);
    }

    public function manageUnsyncedActivities($request, $returnItems = false)
    {
        $user= Auth::user();
        $companyID = $user->company_id;
        $employee = Employee::where('user_id', $user->id)->where('company_id', $companyID)->select('id')->first();
        $employeeID = $employee->id;
        $rawData = $request->nonsynced_activities;
        $companyID = $user->company_id;
        
        if (empty($rawData)) {
            return $returnItems ? array() : false;
        }

        $arraySyncedData = [];
        $data = json_decode($rawData, true);
        foreach ($data as $key => $u_activity) {
            $unique_id = $this->getArrayValue($u_activity, "unique_id");
            $activity_id = $this->getArrayValue($u_activity, "id");
            $completion_datetime =  $this->getArrayValue($u_activity, "completion_datetime");
            if ($completion_datetime=="") {
                $completion_datetime=null;
            }

            $type = $this->getArrayValue($u_activity, "activity_type_id");
            if($this->getArrayValue($u_activity, "type")){
              $type = $this->getArrayValue($u_activity, "type");
            }
            
            $activityData = array(
                "unique_id" => $unique_id,
                "type" => $type,
                "title" => $this->getArrayValue($u_activity, "title"),
                "note" => $this->getArrayValue($u_activity, "note"),
                "start_datetime" => $this->getArrayValue($u_activity, "start_datetime"),
                "duration" => $this->getArrayValue($u_activity, "duration"),
                "priority" => $this->getArrayValue($u_activity, "priority"),
                "assigned_to" => $this->getArrayValue($u_activity, "assigned_to"),
                "created_by" => $this->getArrayValue($u_activity, "created_by"),
                "completed_by" => $completion_datetime?$employeeID:null,//$this->getArrayValue($u_activity, "completed_by"),
                "client_id" => $this->getArrayValue($u_activity, "client_id"),
                "created_at" => $this->getArrayValue($u_activity, "created_at"),
                "updated_at" => $this->getArrayValue($u_activity, "updated_at"),
                "company_id" => $companyID,
                "completion_datetime" => $completion_datetime,
            );

            if ($activity_id) {
                unset($activityData["created_by"]);
            }
            
            $activity = Activity::where('id', $activity_id)->orWhere('unique_id', $unique_id)->first();
            $created = false;

            if ($activity !== null) {
                $activity->update($activityData);
            } else {
                $activity = Activity::create($activityData);
                $created = true;
            }

            // $tempArray = [];
            // if ($activity->created_by!=$employeeID) {
            //     array_push($tempArray, $activity->created_by);
            // }
            // if ($activity->assigned_to!=$employeeID) {
            //     array_push($tempArray, $activity->assigned_to);
            // }

            $wasRecentlyCreated = $activity->wasRecentlyCreated;
            $wasChanged = $activity->wasChanged();
            $isDirty = $activity->isDirty();
            $exists = $activity->exists;
            // $mergedSuperiorsTokens = [];
            // $mergedSuperiorsTokens = Employee::where('company_id',$companyID)->whereIn('id',$tempArray)->pluck('firebase_token');

            $assigned_to_superiors = Employee::employeeParents((int)$activity->assigned_to, array());

            $created_by_superiors = $activity->assigned_to==$activity->created_by?array():Employee::employeeParents((int)$activity->created_by, array());

            $merged_superiors = array_unique(array_merge($assigned_to_superiors, $created_by_superiors));
            
            $mergedSuperiorsTokens = Employee::where('company_id', $companyID)->whereIn('id', $merged_superiors)->pluck('firebase_token', 'id')->toArray();

            if ($wasRecentlyCreated || $wasChanged || $activity->exists || !$created) {
                array_push($arraySyncedData, $activity);

                $title = "Updated Activity";
                $savedActivity = $activityData;
                $savedActivity["id"] = $activity->id;
                $status = "Your activity has been updated";

                $nSaved = saveAdminNotification($companyID, $employeeID, date("Y-m-d H:i:s"), $title, "activities", $savedActivity);
            }else{
              $status = "A new activity has been assigned to you";
              $title = "Added Activity";
              $savedActivity = $activityData;
              $savedActivity["id"] = $activity->id;
              $nSaved = saveAdminNotification($companyID, $employeeID, date("Y-m-d H:i:s"), $title, "activities", $savedActivity);
            }
            
            if (!empty($mergedSuperiorsTokens)) {
                $notificationData = array(
                    "company_id" => $companyID,
                    "employee_id" => $activity->assigned_to,
                    "data_type" => "activity",
                    "data" => "",
                    "action_id" => $activity->id,
                    "title" => $status,
                    "description" => $activity->title,
                    "created_at" => date('Y-m-d H:i:s'),
                    "status" => 1,
                    "to" => 1,
                    "unix_timestamp" => time()
                );
                $activity->priority_name = getObjectValue($activity->activityPriority, "name", "");
                $activity->type_name = getObjectValue($activity->activityType, "name", "");
                $activity->completed_by_name = $activity->completedByEmployee()->withTrashed()->first()?$activity->completedByEmployee()->withTrashed()->first()->name:"";
                $activity->created_by_name = $activity->createdByEmployee()->withTrashed()->first()?$activity->createdByEmployee()->withTrashed()->first()->name:"";
                $activity->assigned_to_name = $activity->assignedTo()->withTrashed()->first()?$activity->assignedTo()->withTrashed()->first()->name:"";
                
                if (!$created) {
                    $dataPayload = array("data_type" => "activity", "activity" => $activity, "action" => "update");
                    $assigned_to_token = $mergedSuperiorsTokens[$activity->assigned_to];
                    unset($mergedSuperiorsTokens[$activity->assigned_to]);   
                    if($activity->created_by!=$activity->assigned_to){
                      $created_by_token = $mergedSuperiorsTokens[$activity->created_by];
                      unset($mergedSuperiorsTokens[$activity->created_by]);
                      if($activity->created_by!=$employeeID) sendPushNotification_(array($created_by_token), 17, $notificationData, $dataPayload);
                    }
                    if(!empty(array_values($mergedSuperiorsTokens))) $sent = sendPushNotification_(array_values($mergedSuperiorsTokens), 17, null, $dataPayload);

                    if($activity->assigned_to!=$employeeID) {
                      $sent_only_to_assigned_to = sendPushNotification_(array($assigned_to_token), 17, $notificationData, $dataPayload);
                    }              
                } else {
                    $dataPayload = array("data_type" => "activity", "activity" => $activity, "action" => "add");

                    $assigned_to_token = $mergedSuperiorsTokens[$activity->assigned_to];
                    unset($mergedSuperiorsTokens[$activity->assigned_to]);

                    if(array_key_exists($employeeID, $mergedSuperiorsTokens)) unset($mergedSuperiorsTokens[$employeeID]);
                    
                    $sent = sendPushNotification_(array_values($mergedSuperiorsTokens), 17, null, $dataPayload);
                    if($activity->assigned_to!=$employeeID) $sent_only_to_assigned_to = sendPushNotification_(array($assigned_to_token), 17, $notificationData, $dataPayload);
                }
            }
        }
        return $returnItems ? $arraySyncedData : false;
    }

    private function getArrayValue($arraySource, $key, $emptyText = null, $trim = false)
    {
        if (is_array($arraySource) && !empty($arraySource[$key])) {
            return $trim == true ? trim($arraySource[$key]) : $arraySource[$key];
        } else {
            return $emptyText;
        }
    }

    private function sendResponse($response)
    {
        echo json_encode($response);
        exit;
    }

    private function getSuperiorsTokens($createdBy, $assignedTo)
    {
        $user = Auth::user();
        $company_id = $user->company_id;
        $authEmp = Employee::where('company_id', $company_id)->where('user_id', $user->id)->first();

        $createdByEmployeeSuperiors = [];
        $assignedToEmployeeSuperiors = [];
        $createdByEmployeeSuperiors = $this->getAllEmployeeSuperior($createdBy, $createdByEmployeeSuperiors);
        $assignedToEmployeeSuperiors = $this->getAllEmployeeSuperior($assignedTo, $createdByEmployeeSuperiors);
        $mergedSuperiors = array_unique(array_merge($createdByEmployeeSuperiors, $assignedToEmployeeSuperiors));
        array_push($mergedSuperiors, $createdBy, $assignedTo);
        $mergedSuperiors = array_diff($mergedSuperiors, [$authEmp->id]);
        $tokens = Employee::whereIn('id', $mergedSuperiors)->whereNotNull('firebase_token')->pluck('firebase_token')->toArray();
        return $tokens;
    }

    private function getAllEmployeeSuperior($empId, $superiors)
    {
        $company_id = Auth::user()->company_id;
        $getSuperior = Employee::where('id', $empId)->where('company_id', $company_id)->first();
        if (!(empty($getSuperior->superior)) && !(in_array($getSuperior->superior, $superiors))) {
            $superiors[] = $getSuperior->superior;
            $superiors = $this->getAllEmployeeSuperior($getSuperior->superior, $superiors);
        }
        return $superiors;
    }
}
