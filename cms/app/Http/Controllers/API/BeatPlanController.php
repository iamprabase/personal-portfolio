<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\BeatVPlan;
use App\BeatPlansDetails;
use App\Employee;
use App\Beat;
use Auth;
use DB;

class BeatPlanController extends Controller
{

	public function __construct()
    {
		$this->middleware('auth:api');
        $this->middleware('permission:beat-plan-create', ['only' => ['store']]);
        $this->middleware('permission:beat-plan-view');
        $this->middleware('permission:beat-plan-update', ['only' => ['store']]);
        $this->middleware('permission:beat-plan-delete', ['only' => ['destroy']]);
    }

    

    public function fetchBeatPlans($return = false, $postData = null)
    {

        $postData = $return?$postData:$this->getJsonRequest();
        $user = Auth::user();
		$companyID = $user->company_id;
		$employee = Employee::where('company_id',$companyID)->where('user_id',$user->id)->first();
		$employeeID = $employee->id;
        $date = $this->getArrayValue($postData,"date");
        $offset = $this->getArrayValue($postData, "offset",0);
        $limit = $this->getArrayValue($postData, "limit",200);

        $beatplans = DB::table('beatvplans')
            ->select('beatvplans.company_id as company_id','beatvplans.employee_id as employee_id','beatvplans.status as status', 'beatplansdetails.*')
            ->leftJoin('beatplansdetails','beatvplans.id','beatplansdetails.beatvplan_id')
            ->where("beatvplans.company_id", $companyID)
            ->where("beatvplans.employee_id", $employeeID)
            ->get()->toArray();
        $beat_name = array();
        foreach($beatplans as $beatplan){
            $beatIds = $beatplan->beat_id;
            $arrayBeatIds = explode(",",$beatIds);
            foreach($arrayBeatIds as $beat_id){
                $beat = Beat::where('id',$beat_id)->first();
                $beat_name[$beatplan->beatvplan_id] = getObjectValue($beat,"name","");
            }
        }
        // Log::info('info', array("data "=>print_r($beat_name,true)));

        $response = array("status" => true, "message" => "Success", "data" => $beatplans,"beats"=>$beat_name);
        if($return){

            return $beatplans;

        } else {

            $this->sendResponse($response);
        }
    }

    public function fetchBeats($return = false, $postData = null)
    {

        $postData = $return?$postData:$this->getJsonRequest();
        $user = Auth::user();
		$companyID = $user->company_id;
		$employee = Employee::where('company_id',$companyID)->where('user_id',$user->id)->first();
		$employeeID = $employee->id;
        $offset = $this->getArrayValue($postData, "offset",0);
        $limit = $this->getArrayValue($postData, "limit",200);

        $finalArray = array();
        $beats = DB::table('beats')
            ->leftJoin('beat_client', 'beat_client.beat_id', '=', 'beats.id')
            ->leftJoin('cities', 'cities.id', 'beats.city_id')
            ->select('beats.*', 'beat_client.client_id','beat_client.beat_id', 'cities.name as city_name')
            ->where("beats.company_id", $companyID)
            ->where("beats.status", "Active")
            ->get()->toArray();
        $beatsGroupedByID = arrayGroupBy($beats,"id",true);
        foreach ($beatsGroupedByID as $key => $value) {

            $tempClientIDs = array();
            foreach ($value as $k => $v) {
                array_push($tempClientIDs,getObjectValue($v,"client_id"));
            }

            $v->client_id = $tempClientIDs;

            array_push($finalArray,$v);
        }
        $response = array("status" => true, "message" => "Success", "data" => $finalArray);
        if($return){

            return $finalArray;

        } else {

            $this->sendResponse($response);
        }
    }

    public function fetchBeatsParties($return = false, $postData = null)
    {

        $postData = $return?$postData:$this->getJsonRequest();
        $user = Auth::user();
		$companyID = $user->company_id;
		$employee = Employee::where('company_id',$companyID)->where('user_id',$user->id)->first();
		$employeeID = $employee->id;
        $offset = $this->getArrayValue($postData, "offset",0);
        $limit = $this->getArrayValue($postData, "limit",200);

        //todo need to remanaged

        //start of pasted

        $handles =  DB::table('handles')
          ->where('employee_id', $employeeID)
          ->pluck('client_id')->toArray();


        $clients = Client::where('company_id', $companyID)
          ->where('status', 'Active')
          ->orderBy('company_name', 'asc')
          ->get();
        $beat_clients = Client::select('clients.company_name', 'clients.id','beats.name as beat_name','beats.id as beatid')
                ->leftJoin('beat_client','clients.id','beat_client.client_id')
                ->leftJoin('beats','beats.id','beat_client.beat_id')
                ->whereIn('clients.id', $handles)
                ->where('clients.company_id', $companyID)
                ->where('clients.status', 'Active')
                ->orderBy('beats.name', 'desc')
                ->orderBy('company_name', 'asc')
                ->get();
        $beats_list = array();  
        foreach($beat_clients as $beat_client){
          if($beat_client->beatid!=''){
            $beats_list[$beat_client->beatid]['name']=$beat_client->beat_name;
            $beats_list[$beat_client->beatid]['id']=$beat_client->beatid;
            $beats_list[$beat_client->beatid]['clients'][$beat_client->id]=$beat_client->company_name;
          }else{
            $beats_list[0]['name']='Unspecified';
            $beats_list[0]['id']='0';
            $beats_list[0]['clients'][$beat_client->id]=$beat_client->company_name;
                }
        }
        $response = array("status" => true, "message" => "Success", "data" => $beats_list);
        if($return){

            return $beats_list;

        } else {

            $this->sendResponse($response);
        }
    }

    public function store($postData = null)
    {
        $postData = $this->getJsonRequest();
        $user = Auth::user();
		$companyID = $user->company_id;
		$employee = Employee::where('company_id',$companyID)->where('user_id',$user->id)->first();
		$employeeID = $employee->id;
        $beatDetailID = $this->getArrayValue($postData, "beat_detail_id");
        if($beatDetailID!=null){
          $beatplan = BeatPlansDetails::where('id',$beatDetailID)->first();
          $title = "Updated BeatPlan";
        }else{          
          $beatvplan = new BeatVPlan();
          $beatvplan->company_id = $companyID;
          $beatvplan->employee_id = $employeeID;  
          $beatvplan->status = "Approved";  
          $beatvplan->save();
          $beatplan = new BeatPlansDetails();
          $title = "Created BeatPlan";
          $beatplan->beatvplan_id = $beatvplan->id;
        }
        $beatplan->title = $this->getArrayValue($postData,"title");
        $beatplan->beat_id = $this->getArrayValue($postData,"beat_id");
        $beatID = $beatplan->beat_id;
        if($beatplan->beat_id == null){
            $beatplan->beat_id = "0";   $beatID = "0";      
        }   
        $beatplan->client_id = $this->getArrayValue($postData,"client_id");   
        $beat_clients_array = $this->makeBeatClientArray( $beatID, $beatplan->client_id);
        $beatplan->beat_clients = $beat_clients_array;
        $beatplan->plandate = $this->getArrayValue($postData,"plandate");   
        $beatplan->planenddate = $this->getArrayValue($postData,"planenddate");   
        $beatplan->plan_from_time = $this->getArrayValue($postData,"plan_from_time");   
        $beatplan->plan_to_time = $this->getArrayValue($postData,"plan_to_time");
        $beatplan->remark = $this->getArrayValue($postData,"remark");  
        $beatplan->save();

        $nSaved = saveAdminNotification($companyID, $employeeID, date("Y-m-d H:i:s"), $title, "beatplan", $beatplan);
        
        $response = array("status" => true, "message" => "Success", "data" => $beatplan);
        $this->sendResponse($response);
    }

    private function makeBeatClientArray($beat_id, $client_id){
        $beat_clients = array();
        $beatIDS = explode(',',$beat_id);
        $clientIDs = explode(',',$client_id);
        foreach($beatIDS as $bt_id){
            if($bt_id==0){
                $beatClients = DB::table('beat_client')->whereIn('beat_id', $beatIDS)
                ->pluck('client_id')->toArray();
                foreach($clientIDs as $ct_id){
                    if(!(in_array($ct_id, $beatClients))){
                        $beat_clients[$bt_id][] = $ct_id;
                    }
                }
            }else{
                $beat_client_ids = DB::table('beat_client')->where('beat_id', $bt_id)
                                    ->pluck('client_id')->toArray();
                foreach($clientIDs as $ct_id){
                    if(in_array($ct_id, $beat_client_ids)){
                        $beat_clients[$bt_id][] = $ct_id;
                    }
                }
            }
        }

        return json_encode((object)$beat_clients); 
    }

    public function syncBeatplan()
    {
        $postData = $this->getJsonRequest();
        //Log::info('info', array("data "=>print_r($postData,true)));
        $arraySyncedData = $this->manageUnsyncedBeatplan($postData, true);
        $response = array("status" => true, "message" => "success", "data" => $arraySyncedData);
        $this->sendResponse($response);
    }

    public function manageUnsyncedBeatplan($postData, $returnItems = false)
    {
        $rawData = $this->getArrayValue($postData, "nonsynced_beatplan");
        $user = Auth::user();
		$companyID = $user->company_id;
		$employee = Employee::where('company_id',$companyID)->where('user_id',$user->id)->first();
		$employeeID = $employee->id;
        $status = $this->getArrayValue($postData, "status");
        
        if (empty($rawData)) {
            return $returnItems ? array() : false;
        }

        $data = json_decode($rawData, true);
        $arraySyncedData = [];
        foreach ($data as $key => $bp) {

          $unique_id = $this->getArrayValue($bp, "unique_id");
          $wasChanged = false;
   
          $beatplanData = array(            
            "company_id" => $companyID,
            "employee_id" => $employeeID,
            "status" => $status,
            "unique_id" => $unique_id
          );

          $beatplan = BeatVPlan::updateOrCreate(
              [
                  "unique_id" => $unique_id,
              ],
              $beatplanData
          );

          $wasCreated = $beatplan->wasRecentlyCreated;

          // $wasChanged = $beatplan->wasChanged(); 

          if($wasCreated == "1"){ 
            $beatplanDetails = new BeatPlansDetails();
            $beatplanDetails->title = $this->getArrayValue($bp,"title");
            $beatplanDetails->beatvplan_id = $beatplan->id;
            $beatplanDetails->beat_id = $this->getArrayValue($bp,"beat_id");   
            $beatplanDetails->client_id = $this->getArrayValue($bp,"client_id");   
            $beatplanDetails->plandate = $this->getArrayValue($bp,"plandate");   
            // $beatplanDetails->planenddate = $this->getArrayValue($bp,"planenddate");   
            $beat_clients_array = $this->makeBeatClientArray($beatplanDetails->beat_id, $beatplanDetails->client_id);
            $beatplan->beat_clients = $beat_clients_array;  
            $beatplanDetails->plan_from_time = $this->getArrayValue($bp,"plan_from_time");   
            $beatplanDetails->plan_to_time = $this->getArrayValue($bp,"plan_to_time");
            $beatplanDetails->remark = $this->getArrayValue($bp,"remark");   
            $beatplanDetails->save();
            $arraySyncedData[$unique_id][$beatplanDetails->id]['created']=true;
          }
          // Log::info('info', array("beatplan_id "=>print_r($beatplan->id,true)));
          $beatDetail_id = $this->getArrayValue($bp, "beatDetail_id");
          if($beatDetail_id!=null){
            $beatplanDetails = BeatPlansDetails::where('id',$beatDetail_id)->where('beatvplan_id',$beatplan->id)->first();
            if($beatplanDetails!=null){
                //Log::info('info', array("data "=>print_r($beatplanDetails,true)));
                $beatplanDetails->title = $this->getArrayValue($bp,"title");
                $beatplanDetails->beatvplan_id = $beatplan->id;
                $beatplanDetails->beat_id = $this->getArrayValue($bp,"beat_id");   
                $beatplanDetails->client_id = $this->getArrayValue($bp,"client_id");  
                $beat_clients_array = $this->makeBeatClientArray($beatplanDetails->beat_id, $beatplanDetails->client_id);
                $beatplan->beat_clients = $beat_clients_array;  
                $beatplanDetails->plandate = $this->getArrayValue($bp,"plandate");   
                $beatplanDetails->planenddate = $this->getArrayValue($bp,"planenddate");   
                $beatplanDetails->plan_from_time = $this->getArrayValue($bp,"plan_from_time");   
                $beatplanDetails->plan_to_time = $this->getArrayValue($bp,"plan_to_time");
                $beatplanDetails->remark = $this->getArrayValue($bp,"remark");   
                $beatplanDetails->save();
                $arraySyncedData[$unique_id][$beatplanDetails->id]['updated']=true;
                $wasChanged = true;                
            }    
          }else{
              $wasChanged = false;
          }   
          //Log::info('info', array("created "=>print_r($wasCreated,true)));
          //Log::info('info', array("changed "=>print_r($wasChanged,true)));

          if($wasCreated == "1" || $wasChanged == true){
            $arraySyncedData[$unique_id]['saved']=true;
          }else{
            $arraySyncedData[$unique_id]['saved']=false;
          }
        }
        // Log::info('info', array("data "=>print_r($arraySyncedData,true)));
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
