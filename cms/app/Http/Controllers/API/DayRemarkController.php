<?php

namespace App\Http\Controllers\API;

use App\DayRemark;
use App\DayRemarkDetail;
use App\Employee;
use App\Http\Controllers\Controller;
use Auth;
use DB;
use Illuminate\Http\Request;
use Log;

class DayRemarkController extends Controller
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
        $remarks= DayRemark::select('day_remarks.*','day_remark_details.id as remark_detail_id','day_remark_details.remark_details','day_remark_details.created_at as detail_created_at','day_remark_details.updated_at as detail_updated_at','day_remark_details.detail_unique_id')
                    ->leftJoin('day_remark_details','day_remarks.id','day_remark_details.remark_id')
                    ->where("day_remarks.company_id", $companyID)
                    ->where("employee_id", $employeeID)
                    ->whereNull('day_remark_details.deleted_at')
                    ->whereNull('day_remarks.deleted_at')
                    ->get();

        return response(["status" => true, "message" => "Success", "data" => $remarks]);
    }

    public function store($return=false,$postData = null)
    {
        $postData = $this->getJsonRequest();
        $user = Auth::user();
		$company_id = $user->company_id;
		$employee = Employee::where('company_id',$company_id)->where('user_id',$user->id)->first();
		$employeeID = $employee->id;
        $remark = $this->getArrayValue($postData,"remark");
        $remark = json_decode($remark,true);
        if(empty($remark)) $this->sendEmptyResponse();
        
        // DayRemark id,unique_id for update case
        $dayRemarkID = $this->getArrayValue($remark, "id");
        $uniqueID = $this->getArrayValue($remark, "unique_id");
        
        // DayRemarkDetails id,unique_id for update case
        $dayRemarkDetailID = $this->getArrayValue($remark, "remark_detail_id");
        $detailUniqueID = (string)$this->getArrayValue($remark, "detail_unique_id");
        $remark_date = $this->getArrayValue($remark, "remark_date");

        $dayremark = DayRemark::where('company_id',$company_id)
                    ->where('employee_id',$employeeID)
                    ->where(function($query)use($dayRemarkID,$uniqueID){
                        $query = $query->where('id',$dayRemarkID)->orWhere('unique_id',$uniqueID);
                    })->first();

        if(!$dayremark){
            $dayremark = DayRemark::where('company_id',$company_id)->where('employee_id',$employeeID)->where('remark_date',$remark_date)->first();
            if(!$dayremark)
                $dayremark = new DayRemark;
            $dayremark->unique_id   = $uniqueID;
            $dayremark->company_id  = $company_id;
            $dayremark->employee_id = $employeeID;
            $dayremark->remark_date = $this->getArrayValue($remark, "remark_date");
        }
        $dayremark->remarks              = $this->getArrayvalue($remark, "remarks");
        $dayremark->remark_date_unix     = $this->getArrayValue($remark, "remark_date_unix");
        $dayremark->remark_datetime      = $this->getArrayValue($remark, "remark_datetime");
        $dayremark->remark_datetime_unix = $this->getArrayValue($remark, "remark_datetime_unix");
        $dayremark->save();

        // Saving Dayremark Detail options
        $dayRemarkDetail = DayRemarkDetail::where('company_id',$company_id)
                        ->where(function($query)use($dayRemarkDetailID,$detailUniqueID){
                            $query->where('id',$dayRemarkDetailID)->orWhere('detail_unique_id',$detailUniqueID);
                        })->first();
        if(!$dayRemarkDetail){
            $dayRemarkDetail                   =  new DayRemarkDetail;
            $dayRemarkDetail->company_id       = $company_id;
            $dayRemarkDetail->detail_unique_id = $detailUniqueID;
            $dayRemarkDetail->remark_id        = $dayremark->id;
            $dayRemarkDetail->created_at       = $this->getArrayValue($remark, "remark_datetime");
            $dayRemarkDetail->updated_at       = $this->getArrayValue($remark, "remark_datetime");
        }
        $dayRemarkDetail->detail_unique_id = $detailUniqueID;
        $dayRemarkDetail->remark_details = $this->getArrayValue($remark, "remark_details");
        $dayRemarkDetail->save();

        $dayremark->remark_detail_id = $dayRemarkDetail->id;
        $dayremark->remark_details = $dayRemarkDetail->remark_details;

        if($dayRemarkDetailID)
            $msg = "Updated Day Remark";
        else
            $msg = "Added Day Remark";

        $nSaved = saveAdminNotification($company_id, $dayremark->employee_id, date("Y-m-d H:i:s"), $msg, "remark", $dayremark);
        $response = array("status" => true, "message" => $msg, "remark_id" => $dayremark,'remark_detail_id'=>$dayRemarkDetail->id);

        $this->sendResponse($response);
    }

    public function destroy(Request $request)
    {
        $user = Auth::user();
        $company_id = $user->company_id;
        $employee_id = Employee::where('company_id',$company_id)->where('user_id',$user->id)->first()->id;
        
        $dayremarkDetail = DayRemarkDetail::where('company_id',$company_id)->where('id',$request->remark_detail_id)->first();
        if(!$dayremarkDetail)
            return response(['status'=>false,'message'=>'No Dayremark found or no permission access to delete this day remark.']);  
        $dayremark = DayRemark::where('id',$dayremarkDetail->remark_id)->where('employee_id',$employee_id)->first();
        if(!$dayremark)
                return response(['status'=>false,'message'=>'No Dayremark found or no permission access to delete this day remark.']);

        $dayremarkDetail->delete();
        $remarksCount = DayRemarkDetail::where('remark_id',$dayremarkDetail->remark_id)->count();
        if($remarksCount<1){
            $dayremark->delete();
            return response(['status'=>true,'exists'=>false,'message'=>'DayRemark Deleted']);
        }
        return response(['status'=>true,'exists'=>true,'message'=>'DayRemark Deleted']);
    }

    public function syncDayRemark()
    {

        $postData = $this->getJsonRequest();
        $user = Auth::user();
		$companyID = $user->company_id;
		$employee = Employee::where('company_id',$companyID)->where('user_id',$user->id)->first();
		$employeeID = $employee->id;

        $arraySyncedData = $this->manageUnsyncedDayRemark($postData, true);
        
        $response = array("status" => true, "message" => "success", "data" => $arraySyncedData);
        $this->sendResponse($response);

    }

    public function manageUnsyncedDayRemark($postData, $returnItems = false)
    {
        $rawData = $this->getArrayValue($postData, "unsynced_data");
        $user = Auth::user();
		$company_id = $user->company_id;
		$employee = Employee::where('company_id',$company_id)->where('user_id',$user->id)->first();
		$employeeID = $employee->id;

        if (empty($rawData)) {
            return $returnItems ? array() : false;
        }
        $data = json_decode($rawData, true);

        $arraySyncedData = array();

        /*prepare data for saving*/
        foreach ($data as $value) {

            // DayRemark id,unique_id for update case
            $dayRemarkID = $this->getArrayValue($value, "id");
            $uniqueID = $this->getArrayValue($value, "unique_id");
            
            // DayRemarkDetails id,unique_id for update case
            $dayRemarkDetailID = $this->getArrayValue($value, "remark_detail_id");
            $detailUniqueID = (string)$this->getArrayValue($value, "detail_unique_id");

            $dayremark = DayRemark::where('company_id',$company_id)
                        ->where('employee_id',$employeeID)
                        ->where(function($query)use($dayRemarkID,$uniqueID){
                            $query = $query->where('id',$dayRemarkID)->orWhere('unique_id',$uniqueID);
                        })->first();
            $remark_date = $this->getArrayValue($value, "remark_date");

            if(!$dayremark){
                $dayremark = DayRemark::where('company_id',$company_id)->where('employee_id',$employeeID)->where('remark_date',$remark_date)->first();
                if(!$dayremark)
                    $dayremark = new DayRemark;
                $dayremark->unique_id = $uniqueID;
                $dayremark->company_id = $company_id;
                $dayremark->employee_id = $employeeID;
                $dayremark->remark_date = $this->getArrayValue($value, "remark_date");
            }
            $dayremark->remarks = $this->getArrayvalue($value, "remarks");
            $dayremark->remark_date_unix = $this->getArrayValue($value, "remark_date_unix");
            $dayremark->remark_datetime = $this->getArrayValue($value, "remark_datetime");
            $dayremark->remark_datetime_unix = $this->getArrayValue($value, "remark_datetime_unix");
            $dayremark->save();

            // Saving Dayremark Detail options
            $dayRemarkDetail = DayRemarkDetail::where('company_id',$company_id)
                            ->where(function($query)use($dayRemarkDetailID,$detailUniqueID){
                                $query->where('id',$dayRemarkDetailID)->orWhere('detail_unique_id',$detailUniqueID);
                            })->first();
            if(!$dayRemarkDetail){
                $dayRemarkDetail =  new DayRemarkDetail;
                $dayRemarkDetail->company_id = $company_id;
                $dayRemarkDetail->detail_unique_id = $detailUniqueID;
                $dayRemarkDetail->remark_id = $dayremark->id;
                $dayRemarkDetail->created_at = $this->getArrayValue($value, "remark_datetime");
            }
            $dayRemarkDetail->remark_details = $this->getArrayValue($value, "remark_details");
            $dayRemarkDetail->save();

            $dayremark->remark_detail_id = (string)$dayRemarkDetail->id;
            $dayremark->remark_details = $dayRemarkDetail->remark_details;
            $dayremark->detail_unique_id = $detailUniqueID;

            // Log::info('info2', array('PostData2' => print_r($dayremark, true)));


            if($dayRemarkDetailID)
                $msg="Updated Day Remark";
            else
                $msg="Added Day Remark";

            $nSaved = saveAdminNotification($company_id, $employeeID, date("Y-m-d H:i:s"), $msg, "remark", $dayremark);
            array_push($arraySyncedData,$dayremark);
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



    public function oldStore(){
                // $remarkData = array(
        //     'unique_id' => $uniqueID,
        //     'company_id' => $company_id,
        //     'employee_id' => $employeeID,
        //     'remarks' => $this->getArrayvalue($remark, "remarks"),
        //     'remark_date' => $this->getArrayValue($remark, "remark_date"),
        //     'remark_date_unix' => $this->getArrayValue($remark, "remark_date_unix",""),
        //     'remark_datetime' => $this->getArrayValue($remark, "remark_datetime"),
        //     'remark_datetime_unix' => $this->getArrayValue($remark, "remark_datetime_unix","")
        // );

        // if(!empty($dayRemarkID)){

        //     $dayRemark = DayRemark::updateOrCreate(
        //         [
        //             "id" => $dayRemarkID
        //         ],
        //         $remarkData
        //     );
        // } elseif(!empty($uniqueID)){

        //     $dayRemark = DayRemark::updateOrCreate(
        //         [
        //             "unique_id" => $uniqueID
        //         ],
        //         $remarkData
        //     );

        // } 
        
        // $wasRecentlyCreated = $dayRemark->wasRecentlyCreated;
        // $wasChanged = $dayRemark->wasChanged();
        // $isDirty = $dayRemark->isDirty();
        // $exists = $dayRemark->exists;

        // if ($wasRecentlyCreated || $wasChanged || $dayRemark->exists) {

        //     $msg = "";
        //     $savedDayRemark = $remarkData;
        //     $savedDayRemark["id"] = $dayRemark->id;
            
        //     if ($dayRemark->wasRecentlyCreated) {
                
        //         $msg = "Added Day Remarks";
        //     } else {

        //         $msg = "Updated Day Remarks";

        //     }

        // $nSaved = saveAdminNotification($companyID, $employeeID, date("Y-m-d H:i:s"), $msg, "remark", $savedDayRemark);
        // $response = array("status" => true, "message" => $msg, "remark_id" => $dayRemark->id);
        // } else {
        //     $response = array("status" => false, "message" => "Unable to create/update", "remark_id" => "");
            
        // }
        // $this->sendResponse($response);
    }


    public function oldSync(){


            // $uniqueID = $this->getArrayValue($value, "unique_id");
            // $tempArray["unique_id"] = $uniqueID;
            
            // $tempArray["company_id"] = $companyID;            
            // $tempArray["employee_id"] = $employeeID;
 
            // $createdAt = $this->getArrayvalue($value, "created_at");
            // $tempArray["created_at"] = $createdAt; 
            // $remarks = $this->getArrayvalue($value, "remarks");
            // $tempArray["remarks"] = $remarks;
            // $remarkDate = $this->getArrayvalue($value, "remark_date");
            // $tempArray["remark_date"] = $remarkDate;
            // $remarkDateUnix = $this->getArrayvalue($value, "remark_date_unix","");
            // $tempArray["remark_date_unix"] = $remarkDateUnix;
            // $remarkDateTime = $this->getArrayvalue($value, "remark_datetime");
            // $tempArray["remark_datetime"] = $remarkDateTime;
            // $remarkDateTimeUnix = $this->getArrayvalue($value, "remark_datetime_unix","");
            // $tempArray["remark_datetime_unix"] = $remarkDateTimeUnix;


            // $savedID = DayRemark::insertGetId([
            //   "unique_id" => $uniqueID,
            //   "company_id" => $companyID,
            //   "employee_id" => $employeeID,
            //   "remarks" => $remarks,
            //   "remark_date" => $remarkDate,
            //   "remark_date_unix" => $remarkDateUnix,
            //   "remark_datetime" => $remarkDateTime,
            //   "remark_datetime_unix" => $remarkDateTimeUnix,
            //   "created_at" => $createdAt,

            // ]);
            // 
            //             if (!empty($savedID)) {

                // $syncedData = $tempArray;
                // $syncedData['id'] = $savedID;
                // Log::info('info2', array('PostData2' => print_r($syncedData, true)));

                // array_push($arraySyncedData, $syncedData);
            // }
    }

    // public function destroyOld(Request $request)
    // {
    //   $user = Auth::user();
    //   $company_id = $user->company_id;
    //   $employeeID = Employee::where('company_id',$company_id)->where('user_id',$user->id)->first()->id;
    //   if(Auth::user()->isCompanyManager()){
    //     $dayremark = DayRemark::where('company_id',$company_id)->where('id',$request->id)->first();
    //   }else{
    //     $juniors = Employee::EmployeeChilds($employeeID,array());
    //     $dayremark = DayRemark::where('company_id',$company_id)->where('id',$request->id)->whereIn('employee_id',$juniors)->first();
    //   }
    //   if(!$dayremark)
    //     return response(['status'=>false,'error'=>'No Dayremark found or no permission access to delete this day remark.']);

    //   $dayremark->delete();
    //   $nSaved = saveAdminNotification($company_id, $employeeID, date("Y-m-d H:i:s"),'DayRemark Deleted', "remark", $dayremark);
    //   return response(['status'=>true,'message'=>'Dayremark Deleted']);
    // }


}
