<?php

namespace App\Http\Controllers\API;

use DB;
use Log;
use Auth;
use App\Color;
use App\Order;
use App\Client;
use App\Product;
use App\Category;
use App\Employee;
use App\TourPlan;
use App\RateSetup;
use App\Collection;
use App\RateDetail;
use App\ExpenseType;
use App\OrderDetails;
use App\VisitPurpose;
use App\ProductVariant;
use App\UnitConversion;
use App\ModuleAttribute;
use Illuminate\Http\Request;
use App\TargetSalesmanassign;
use App\Http\Controllers\Controller;

class CommonController extends Controller
{
    public function __construct()
    {
		$this->middleware('auth:api');
    }

    private function getCategory($companyID){
      
      $categories = Category::with(['categoryrates' => function($query) {
        $query->with(['itemrates' => function($subquery){
          $subquery->select('category_rate_type_id', 'product_id', 'product_variant_id as variant_id', 'mrp');
        }])->select('name', 'id', 'category_id');
      }])->whereCompanyId($companyID)
                    ->get(['name', 'id', 'status']);

      return $categories;
    }

    public function fetchCommonData()
    {
        $postData = $this->getJsonRequest();
        $user = Auth::user();
		$companyID = $user->company_id;
		$employee = Employee::where('company_id',$companyID)->where('user_id',$user->id)->first();
		$employeeID = $employee->id;
        $tables = $this->getArrayValue($postData, "tables");
        $exploded = explode(",", $tables);
        //Log::info('info', array("postData in FetchCommondata"=>print_r($postData,true)));

        $data = array();
        $data["clients"] = in_array("clients", $exploded) ? $this->fetchClients(true, $postData) : array();
        $data["employees"] = in_array("employees",$exploded)?$this->fetchEmployees(true,$postData):array();
        $data["products"] = in_array("products", $exploded) ? $this->getProducts($companyID) : array();
        $data["orders"] = in_array("orders", $exploded) ? $this->fetchOrders(true, $postData) : array();
        $data["noorders"] = in_array("no_orders", $exploded) ? $this->fetchNoOrder(true, $postData) : array();
        $data["collections"] = in_array("collections", $exploded) ? $this->fetchCollection(true, $postData) : array();
        $data["meetings"] = in_array("meetings", $exploded) ? $this->fetchMeeting(true, $postData) : array();
        $data["leaves"] = in_array("leaves", $exploded) ? $this->fetchLeave(true, $postData) : array();
        $data["expenses"] = in_array("expenses", $exploded) ? $this->fetchExpense(true, $postData) : array();
        $data["tasks"] = in_array("tasks", $exploded) ? $this->fetchTask(true, $postData) : array();
        $data["holidays"] = in_array("holidays", $exploded) ? $this->fetchHolidays(true, $postData) : array();
        $data["tourplans"] = in_array("tour_plans", $exploded) ? $this->fetchTourPlans(true, $postData) : array();
        $data["leave_types"] = in_array("leave_type", $exploded) ? $this->fetchLeaveTypes(true, $postData) : array();
        $data["activity_priorities"] = in_array("activity_priorities", $exploded) ? $this->fetchActivityPriorities(true, $postData) : array();
        $data["activity_types"] = in_array("activity_types", $exploded) ? $this->fetchActivityTypes(true, $postData) : array();
        $data["expense_types"] = in_array("expense_type", $exploded) ? $this->fetchExpenseTypes(true, $postData) : array();
        $data["module_attributes"] = in_array("module_attributes",$exploded)?$this->fetchModuleAttributes(true,$postData):array();
        $data["unit_conversions"] = in_array("unit_conversions",$exploded)?$this->fetchUnitConversion(true,$postData):array();
        $data["party_rates"] = in_array("party_rates", $exploded) ? $this->getPartyRates($companyID) : array();
        $data["visit_purposes"] = in_array("visit_purposes", $exploded) ? $this->getVisitPurpose($companyID) : array();
        $data["has_target"] = in_array("has_target", $exploded) ? $this->getHasTarget($companyID, $employee->id) : array();
        $data["category"] = in_array("category", $exploded) ? $this->getCategory($companyID) : array();
        
        //Log::info('info', array("data"=>print_r($data,true)));
        $getUnsentAnnouncement = DB::table('unsent_announcement')->where('employee_id', $employeeID)->pluck('announcement_id')->toArray();
        if (!empty($getUnsentAnnouncement)) {
            $fbIDs = DB::table('employees')->where(array(array('company_id', $companyID), array('status', 'Active')))->where('id', $employeeID)->whereNotNull('firebase_token')->pluck('firebase_token');
            if (!empty($fbIDs)) {
                $fetchAnnouncements = DB::table('announcements')->whereIn('id', $getUnsentAnnouncement)->get();
                if(!empty($fetchAnnouncements)){
                  foreach ($fetchAnnouncements as $fetchAnnouncement) {
                      $notificationData = array(
                      'company_id' => $companyID,
                      'employee_id' => $employeeID,
                      'title' => $fetchAnnouncement->title,
                      'description' => $fetchAnnouncement->description,
                      'created_at' => date('Y-m-d H:i:s'),
                      'status' => 1,
                      'to' => 1
                  );
                  $sendingNotificationData = $notificationData;
                  $sendingNotificationData['unix_timestamp'] = time(); //need to manage server/client
                  $sent = sendPushNotification_($fbIDs, 6, $sendingNotificationData, null);
                  $sentStatus = json_decode($sent);
                  if ($sentStatus->success == 1) {
                    DB::table('unsent_announcement')->where('employee_id', $employeeID)->where('announcement_id', $fetchAnnouncement->id)->delete();
                  }
                }
              }
            }
        }

        $response = array("status" => true, "message" => "Success", "data" => $data);
        $this->sendResponse($response);
    }

    public function fetchCommonDataClients(Request $request){
      $user = Auth::user();
      $companyID = $user->company_id;
      $employee = Employee::where('company_id',$companyID)->where('user_id',$user->id)->first();
      $employeeID = $employee->id;
      $offset = $request->offset;
      $limit = $request->limit;

      $clients = Client::select('clients.*', 'countries.name as country_name', 'states.name as state_name', 'cities.name as city_name','marketareas.name as market_area_name','beat_client.beat_id','business_types.business_name')
          ->leftJoin('countries', 'clients.country', '=', 'countries.id')
          ->leftJoin('states', 'clients.state', '=', 'states.id')
          ->leftJoin('cities', 'clients.city', '=', 'cities.id')
          ->leftJoin('marketareas', 'clients.market_area', '=', 'marketareas.id')
          ->leftJoin('beat_client', 'clients.id', '=', 'beat_client.client_id')
          ->leftJoin('business_types','clients.business_id','business_types.id')
          ->offset($offset)->limit($limit)
          ->where('clients.company_id',$companyID)->get();

      $finalArrayWithAllowed = array();

      foreach ($clients as $key => $value) {

          //getting outstanding amount
          $getOrderStatusFlag = ModuleAttribute::where('company_id', $companyID)->where('module_id', 1)->where('order_amt_flag', 1)->pluck('id')->toArray();
          if(!empty($getOrderStatusFlag)){
            $partyOrders =  Order::select('id','delivery_status_id','grand_total')
                          ->where('client_id', $value->id)
                          ->orderBy('created_at', 'desc')
                          ->get();
            $tot_order_amount = $partyOrders->whereIn('delivery_status_id', $getOrderStatusFlag)->sum('grand_total');
          }else{
            $tot_order_amount = 0;
          }          
          $collections = Collection::where('company_id',$companyID)->where('client_id', $value->id)
          ->orderBy('created_at', 'desc')
          ->get();
          $cheque_collection_amount = $collections->where('payment_method', 'Cheque')->where('payment_status', 'Cleared')->sum('payment_received');
          $cash_collection_amount = $collections->where('payment_method', 'Cash')->sum('payment_received');
          $bank_collection_amount = $collections->where('payment_method', 'Bank Transfer')->sum('payment_received');
          $tot_collection_amount = $cheque_collection_amount + $cash_collection_amount + $bank_collection_amount;
          $value->outstanding_amount = number_format(($value->opening_balance + $tot_order_amount - $tot_collection_amount),2);
          // Ending Outstanding amount

          $handles = getClientHandlingData($companyID, $value->id,true);
          $value->employee_ids = json_encode($handles);
          $canHandle = in_array($employeeID, $handles);
          $value->can_handle = $canHandle;

          $access = getClientAccessibleData($companyID, $value->id,true);
          $value->employee_access_ids = json_encode($access);
          $canAccess = in_array($employeeID, $access);
          $value->can_access = $canAccess;


          $party_meta   = DB::table('party_meta')->where('client_id',$value->id)->first();
          if($party_meta)
              $value->custom_fields = $party_meta->cf_value;

          array_push($finalArrayWithAllowed, $value);
      }

      $new_offset = $offset+$limit;
      $next_fetch = Client::select('clients.id')->whereCompanyId($companyID)->offset($new_offset)->limit(1)->first();

      $response = array("status" => true, "message" => "Success", "new_offset" => $new_offset, "next_fetch" => $next_fetch?true:false, "data" => $finalArrayWithAllowed);
      
      return response()->json($response);
      
    }

    private function getVisitPurpose($company_id){
      try{
        
        $visit_purposes = VisitPurpose::whereCompanyId($company_id)->get([
          'id', 'title'
        ])->toArray();

        return $visit_purposes;
      }catch(\Exception $e){
        $response = array("status" => false, "message" => $e->getMessage(), "data" => array());
        return array();
      }
    }

    private function getHasTarget($companyID, $employeeID){
      try{
        $year = date('Y');
        $hasTarget = TargetSalesmanassign::whereCompanyId($companyID)
                      ->whereSalesmanId($employeeID)
                      ->whereYear('created_at', $year)->first();

        return $hasTarget ? 1 : 0;
      }catch(\Exception $e){
        Log::info($e->getMessage());
      }

      return 0;
      
    }

    public function fetchPartyRates(Request $request){
      try{
        $getRates = $this->getPartyRates($request->company_id);
  
        $response = array("status" => true, "message" => "Success", "data" => $getRates);
        return $response;

      }catch(\Exception $e){
        $response = array("status" => false, "message" => $e->getMessage(), "data" => array());
        return $response;

      }
    }

    private function getPartyRates($company_id) {
      $party_rates = RateSetup::where('rates.company_id', $company_id)->leftJoin('rate_details', 'rate_details.rate_id', 'rates.id')
                ->get(['rates.name as rate_name', 'rates.id as rate_id', 'rate_details.product_id', 'rate_details.variant_id', 'rate_details.mrp']);
      $rates = array();
      if($party_rates->first()){
        if($party_rates->first()->product_id){
          foreach($party_rates as $rate){
            $object = array('product_id'=> $rate->product_id, 'variant_id'=> $rate->variant_id, 'mrp'=> round($rate->mrp, 2));
            $rates[$rate->rate_id]['id'] = $rate->rate_id;
            $rates[$rate->rate_id]['name'] = $rate->rate_name;
            $rates[$rate->rate_id]['details'][] = $object;
          }
        }else{
          $rates[$party_rates->first()->rate_id]['id'] = $party_rates->first()->rate_id;
          $rates[$party_rates->first()->rate_id]['name'] = $party_rates->first()->rate_name;
          $rates[$party_rates->first()->rate_id]['details'][] = "";

        }
      }

      $tempArray = array();
      foreach($rates as $key=>$value){
        array_push($tempArray, $value);
      }
      
      return $tempArray;
    }

    public function fetchModuleAttributes($return=false,$tempPostData = null)
    {
        $postData = $return ? $tempPostData : $this->getJsonRequest();
        $user = Auth::user();
        $companyID = $user->company_id;
        $moduleAttributes = DB::table('module_attributes')->where('company_id',$companyID)->whereNull('deleted_at')->get();
        return $moduleAttributes;
    }

    public function fetchUnitConversion($return=false,$tempPostData = null)
    {
        $postData = $return ? $tempPostData : $this->getJsonRequest();
        $user = Auth::user();
        $companyID = $user->company_id;
        $unitConversion = UnitConversion::where('unit_conversions.company_id', $companyID)
                                  ->leftJoin('unit_types as U', 'U.id', 'unit_conversions.unit_type_id')
                                  ->leftJoin('unit_types as T', 'T.id', 'unit_conversions.converted_unit_type_id')
                                  ->select('unit_conversions.*', 'U.symbol as unit_name', 'T.symbol as converted_unit_name')
                                  ->orderby('unit_conversions.unit_type_id', 'asc')
                                  ->get();
        // $unitConversion = DB::table('unit_conversions')->where('company_id',$companyID)->whereNull('deleted_at')->get();
        return $unitConversion;
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
            ->whereNULL("beats.deleted_at")
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

    private function fetchEmployees($return = false, $tempPostData = null){
        $user = Auth::user();
        $company_id = $user->company_id;
        $authEmp = Employee::where('company_id',$company_id)->where('user_id',$user->id)->first();
        $employees = Employee::select('employees.id','employees.user_id','employees.company_id','employees.is_admin','employees.role','employees.firebase_token','employees.device','employees.imei','employees.name','employees.employee_code','employees.employee_code1','employees.employeegroup','employees.country_code','employees.employeegroup','employees.country_code','employees.alt_country_code','employees.e_country_code','employees.phone','employees.email','employees.firebase_token','employees.image','employees.image_path','employees.b_date','employees.gender','employees.status','employees.client_ids','employees.designation','employees.local_add','employees.per_add','employees.recent_location','employees.superior','employees.e_name','employees.father_name','employees.a_phone','employees.e_phone','employees.e_relation','employees.total_salary','employees.permitted_leave','employees.doj','employees.lwd','employees.acc_holder','employees.acc_number','employees.bank_id','employees.ifsc_code','employees.pan','employees.pan','employees.branch','employees.resume','employees.offer_letter','employees.joining_letter','employees.contract','employees.id_proof','employees.created_at','employeegroups.name as group_name')->where('employees.company_id',$company_id)
            ->leftJoin('employeegroups','employees.employeegroup','employeegroups.id')
            ->where('employees.status','Active')->get();        
    	return $employees;
    }

    public function fetchClients($return = false, $tempPostData = null)
    {

        $postData = $return ? $tempPostData : $this->getJsonRequest();
        $user = Auth::user();
		$companyID = $user->company_id;
		$employee = Employee::where('company_id',$companyID)->where('user_id',$user->id)->first();
		$employeeID = $employee->id;

        /*Check if unsynced data is available . if available first update to tha database */
        $syncStatus = $this->manageUnsyncedClients($postData);
        $handle_data_emp = DB::table('handles')->where('employee_id', $employeeID)
                            ->pluck('client_id')->toArray();
        $accessible_data_emp = DB::table('accessibility_link')->where('employee_id', $employeeID)
                            ->pluck('client_id')->toArray();
        $fetch_client_id = array_merge($handle_data_emp, $accessible_data_emp);
        $clients = DB::table('clients')
            ->select('clients.*', 'countries.name as country_name', 'states.name as state_name', 'cities.name as city_name','marketareas.name as market_area_name','beat_client.beat_id')
            ->leftJoin('countries', 'clients.country', '=', 'countries.id')
            ->leftJoin('states', 'clients.state', '=', 'states.id')
            ->leftJoin('cities', 'clients.city', '=', 'cities.id')
            ->leftJoin('marketareas', 'clients.market_area', '=', 'marketareas.id')
            ->leftJoin('beat_client', 'clients.id', '=', 'beat_client.client_id')
            ->whereIn("clients.id", $fetch_client_id)
            ->where("clients.company_id", $companyID)->whereNull("clients.deleted_at")->get()->toArray();
        
        if (empty($clients)) {
            if ($return) {
                return array();
            } else {

                $this->sendEmptyResponse();
            }
        }

        $finalArray = array();
        // $finalArrayWithAllowed = array();
        $getOrderStatusFlag = ModuleAttribute::where('company_id', $companyID)->where('module_id', 1)->where('order_amt_flag', 1)->pluck('id')->toArray();
        foreach ($clients as $key => $value) {

            //getting outstanding amount
            // $getOrderStatusFlag = ModuleAttribute::where('company_id', $companyID)->where('module_id', 1)->where('order_amt_flag', 1)->pluck('id')->toArray();
            $tot_order_amount = 0;
            if(!empty($getOrderStatusFlag)){
              $partyOrders =  Order::select('id','delivery_status_id','grand_total')
                            ->where('client_id', $value->id)
                            ->orderBy('created_at', 'desc')
                            ->get();
              if($partyOrders->first()){
                $tot_order_amount = $partyOrders->whereIn('delivery_status_id', $getOrderStatusFlag)->sum('grand_total');
              }
            }          
            $collections = Collection::where('company_id', $companyID)->where('client_id', $value->id)
                            ->orderBy('created_at', 'desc')
                            ->get();
            $cheque_collection_amount = 0;
            $cash_collection_amount = 0;
            $bank_collection_amount = 0;
            if($collections->first()){
              $cheque_collection_amount = $collections->where('payment_method', 'Cheque')->where('payment_status', 'Cleared')->sum('payment_received');
              $cash_collection_amount = $collections->where('payment_method', 'Cash')->sum('payment_received');
              $bank_collection_amount = $collections->where('payment_method', 'Bank Transfer')->sum('payment_received');
            }
            $tot_collection_amount = $cheque_collection_amount + $cash_collection_amount + $bank_collection_amount;
            $value->outstanding_amount = number_format(($value->opening_balance + $tot_order_amount - $tot_collection_amount),2);
            // Ending Outstanding amount

            $handles = getClientHandlingData($companyID, $value->id,true);
            $value->employee_ids = json_encode($handles);
            $canHandle = in_array($employeeID, $handles);
            $value->can_handle = $canHandle;
            if($value->superior){
                $superior = Client::where('id', $value->superior)->first();
                $value->superior_name = $superior ? $superior->company_name : null;
            } 
            else{
                $value->superior_name = null;
            } 

            $access = getClientAccessibleData($companyID, $value->id,true);
            $value->employee_access_ids = json_encode($access);
            $canAccess = in_array($employeeID, $access);
            $value->can_access = $canAccess;

            $party_meta   = DB::table('party_meta')->where('client_id',$value->id)->first();
            if($party_meta)
                $value->custom_fields = $party_meta->cf_value;
            $client = Client::find($value->id);
            $value->appliedcategoryrates = $client->appliedcategoryrates()->get(['id', 'category_id', 'name'])->pluck('id')->toJson();

            array_push($finalArray, $value);
            // array_push($finalArrayWithAllowed, $value);

        }
        $finalArrayWithAllowed = $finalArray;
        $response = array("status" => true, "message" => "Success", "data" => $finalArrayWithAllowed);
       
        if ($return) {
            
            return $finalArray;
        } else {
            
            $this->sendResponse($response);
        }
    } 

    // public function fetchClients($return = false, $tempPostData = null)
    // {

    //     $postData = $return ? $tempPostData : $this->getJsonRequest();
    //     $user = Auth::user();
		// $companyID = $user->company_id;
		// $employee = Employee::where('company_id',$companyID)->where('user_id',$user->id)->first();
		// $employeeID = $employee->id;

    //     /*Check if unsynced data is available . if available first update to tha database */
    //     $syncStatus = $this->manageUnsyncedClients($postData);

    //     $clients = DB::table('clients')
    //         ->select('clients.*', 'countries.name as country_name', 'states.name as state_name', 'cities.name as city_name','marketareas.name as market_area_name','beat_client.beat_id')
    //         ->leftJoin('countries', 'clients.country', '=', 'countries.id')
    //         ->leftJoin('states', 'clients.state', '=', 'states.id')
    //         ->leftJoin('cities', 'clients.city', '=', 'cities.id')
    //         ->leftJoin('marketareas', 'clients.market_area', '=', 'marketareas.id')
    //         ->leftJoin('beat_client', 'clients.id', '=', 'beat_client.client_id')
    //         //->where("clients.company_id", $companyID)->where("clients.status", "Active")->whereNull("clients.deleted_at")->get()->toArray();
    //         ->where("clients.company_id", $companyID)->whereNull("clients.deleted_at")->get()->toArray();
        
    //     if (empty($clients)) {
    //         if ($return) {
    //             return array();
    //         } else {

    //             $this->sendEmptyResponse();
    //         }
    //     }

    //     $finalArray = array();
    //     $finalArrayWithAllowed = array();
    //     // Log::info('info', array("clients data "=>print_r($clients,true)));

    //     foreach ($clients as $key => $value) {

    //         //getting outstanding amount
    //         $getOrderStatusFlag = ModuleAttribute::where('company_id', $companyID)->where('module_id', 1)->where('order_amt_flag', 1)->pluck('id')->toArray();
    //         if(!empty($getOrderStatusFlag)){
    //           $partyOrders =  Order::select('id','delivery_status_id','grand_total')
    //                         ->where('client_id', $value->id)
    //                         ->orderBy('created_at', 'desc')
    //                         ->get();
    //           $tot_order_amount = $partyOrders->whereIn('delivery_status_id', $getOrderStatusFlag)->sum('grand_total');
    //         }else{
    //           $tot_order_amount = 0;
    //         }          
    //         $collections = Collection::where('company_id', $companyID)->where('client_id', $value->id)
    //         ->orderBy('created_at', 'desc')
    //         ->get();
    //         $cheque_collection_amount = $collections->where('payment_method', 'Cheque')->where('payment_status', 'Cleared')->sum('payment_received');
    //         $cash_collection_amount = $collections->where('payment_method', 'Cash')->sum('payment_received');
    //         $bank_collection_amount = $collections->where('payment_method', 'Bank Transfer')->sum('payment_received');
    //         $tot_collection_amount = $cheque_collection_amount + $cash_collection_amount + $bank_collection_amount;
    //         $value->outstanding_amount = number_format(($value->opening_balance + $tot_order_amount - $tot_collection_amount),2);
    //         // Ending Outstanding amount

    //         $handles = getClientHandlingData($companyID, $value->id,true);
    //         $value->employee_ids = json_encode($handles);
    //         $canHandle = in_array($employeeID, $handles);
    //         $value->can_handle = $canHandle;

    //         $access = getClientAccessibleData($companyID, $value->id,true);
    //         $value->employee_access_ids = json_encode($access);
    //         $canAccess = in_array($employeeID, $access);
    //         $value->can_access = $canAccess;

    //         $party_meta   = DB::table('party_meta')->where('client_id',$value->id)->first();
    //         if($party_meta)
    //             $value->custom_fields = $party_meta->cf_value;


    //         array_push($finalArray, $value);
    //         array_push($finalArrayWithAllowed, $value);

    //     }

    //     $response = array("status" => true, "message" => "Success", "data" => $finalArrayWithAllowed);

    //     if ($return) {
    //         return $finalArray;
    //     } else {
    //         $this->sendResponse($response);
    //     }
    // }

    public function fetchClientSetting($return = false,$postData = null){
        $postData = $return?$postData:$this->getJsonRequest();
        $user = Auth::user();
        $companyID = $user->company_id;
        $clientSettings = DB::table('client_settings')->where('company_id',$companyID)->first();
        $response = array("status" => true, "message" => "Success", "data" => $clientSettings);
        if($return){
            return $clientSettings;
        } else {
            $this->sendResponse($response);
        }
    }

    private function manageUnsyncedClients($postData, $returnItems = false)
    {

        $rawData = $this->getArrayValue($postData, "nonsynced_data");
        $user = Auth::user();
		$companyID = $user->company_id;
		$employee = Employee::where('company_id',$companyID)->where('user_id',$user->id)->first();
		$employeeID = $employee->id;
        $employeeName = $this->getArrayValue($postData, "employee_name");

        if (empty($rawData)) return false;
        $data = json_decode($rawData, true);
        //Log::info('info', array("raw clients"=>print_r($data,true)));

        $arraySyncedData = array();
        foreach ($data as $key => $value) {
            $companyName = $this->getArrayValue($value, "company_name");
            $uniqueID = $this->getArrayValue($value, "unique_id");

            //$client = DB::table('client')->where('unique_id', $uniqueID)->first();
            $client_type = $this->getArrayValue($value, "client_type");
            if($client_type==null){
                $client_type=0;
            }

            $clientData = array(

                'company_id' => $companyID,
                'unique_id' => $uniqueID,
                'company_name' => $companyName,

                'client_type' => $client_type,
                'superior' => $this->getArrayValue($value, "superior"),
                'market_area' => $this->getArrayValue($value, "marketarea"),
                
                'name' => $this->getArrayValue($value, "name"),
                'client_code' => $this->getArrayValue($value, "client_code"),
                'website' => $this->getArrayValue($value, "website"),
                'email' => $this->getArrayValue($value, "email"),

                'country' => $this->getArrayValue($value, "country"),
                'state' => $this->getArrayValue($value, "state"),
                'city' => $this->getArrayValue($value, "city"),
                'address_1' => $this->getArrayValue($value, "address_1"),
                'address_2' => $this->getArrayValue($value, "address_2"),

                'pin' => $this->getArrayValue($value, "pin"),
                'phonecode' => $this->getArrayValue($value, "phonecode"),
                'phone' => $this->getArrayValue($value, "phone"),
                'mobile' => $this->getArrayValue($value, "mobile"),
                'pan' => $this->getArrayValue($value, "pan"),
                'about' => $this->getArrayValue($value, "about"),
                'location' => $this->getArrayValue($value, "location"),
                'latitude'   =>$this->getArrayValue($value,"latitude"),
                'longitude'  =>$this->getArrayValue($value,"longitude"),
                'status' => $this->getArrayValue($value, "status"),
                'created_by' => $employeeID
            );

            $client = Client::updateOrCreate(
                [
                    "unique_id" => $uniqueID
                ],
                $clientData
            );
            //Log::info('info', array("client after updateOrCreate"=>print_r($client,true)));

            $wasRecentlyCreated = $client->wasRecentlyCreated;
            $wasChanged = $client->wasChanged();
            $isDirty = $client->isDirty();
            //Log::info('info', array("wasRecentlyCreated"=>print_r($wasRecentlyCreated,true)));
            //Log::info('info', array("wasChanged"=>print_r($wasChanged,true)));
            //Log::info('info', array("isDirty"=>print_r($isDirty,true)));

            if ($wasRecentlyCreated || $wasChanged || $client->exists) {
                $client['unique_id'] = $uniqueID;
                array_push($arraySyncedData, $client);

                if ($client->wasRecentlyCreated) {
                    $handleData = array(
                        "company_id" => $companyID,
                        "employee_id" => $employeeID,
                        "client_id" => $client->id,
                        "map_type" => "2"
                    );
                    //Log::info('info', array("handleData"=>print_r($handleData,true)));

                    $handle = DB::table('handles')->where(
                        array(
                            array("company_id", $companyID),
                            array("employee_id", $employeeID),
                            array("client_id", $client->id)
                        )
                    )
                        ->get();
                    if (!empty($handle)) {
                        $handleSaved = DB::table('handles')->insertGetId($handleData);
                    }
                    
                    $superiors = $this->getAllEmployeeSuperior($companyID, $employeeID, $getSuperiors=[]);
                    $employeeInstance = Employee::where('company_id', $companyID)->where('is_admin', 1)->pluck('id')->toArray();
                    if(!empty($employeeInstance)){
                      foreach($employeeInstance as $adminId){
                        if(!in_array($adminId, $superiors)){
                            array_push($superiors, $adminId);
                        }
                      }
                    }
                    $supHandle = DB::table('handles')->where("company_id", $companyID)->whereIn("employee_id", $superiors)->where("client_id", $client->id)->pluck('employee_id')->toArray();
                    foreach($superiors as $superior){

                        if (!in_array($superior, $supHandle)) {
                            $supHandleData = array(
                                "company_id" => $companyID,
                                "employee_id" => $superior,
                                "client_id" => $client->id,
                                "map_type" => "2"
                            );
                            $supHandleSaved = DB::table('handles')->insertGetId($supHandleData);
                        }
                    }
                    $beatID = getArrayValue($value,"beat_id");
                    if(!empty($beatID)){

                        DB::table('beat_client')->updateOrInsert(
                            ['client_id' => $client->id],
                            ['beat_id' => $beatID]
                        );

                    }
                }

                $nSaved = saveAdminNotification($companyID, $employeeID, date("Y-m-d H:i:s"), "Added Party", "client", $client);
            }
        }
        return $returnItems ? $arraySyncedData : true;
    }

    private function getProducts()
    {
    	$user = Auth::user();
		$companyID = $user->company_id;

        //todo need to be managed properly with other parameters like limit
        $finalArray = array();

        $products = DB::table('products')
            ->select('products.*', 'categories.name as category_name','brands.name as brand_name', 'unit_types.name as unit_name', 'unit_types.symbol as unit_symbol')
            ->leftJoin('brands', 'brands.id', '=', 'products.brand')
            ->leftJoin('categories', 'categories.id', '=', 'products.category_id')
            ->leftJoin('unit_types', 'unit_types.id', '=', 'products.unit')
            ->where("products.company_id", $companyID)
            ->where("products.status", "Active")
            ->get()->toArray();

        $pv = DB::table('product_variants')->whereNull("product_variants.deleted_at")
            ->select('product_variants.*', 'unit_types.name as unit_name', 'unit_types.symbol as unit_symbol')
            ->leftJoin('unit_types', 'unit_types.id', '=', 'product_variants.unit')
            ->where("product_variants.company_id", $companyID)
            ->get()->toArray();

        foreach($pv as $key=>$p){
          $p->variant_colors = $this->getColors($p->id);
        }

        // $pv2 = ProductVariant::leftJoin('unit_types', 'unit_types.id', '=', 'product_variants.unit')
        //     ->select('product_variants.*', 'unit_types.name as unit_name', 'unit_types.symbol as unit_symbol')
        //     ->where("product_variants.company_id", $companyID)
        //     ->get();
        // foreach($pv2 as $v){
        //   if($v->colors->count()>0)
        //     $v->variant_colors = json_encode($v->colors->pluck('value')->toArray());
        //   else
        //     $v->variant_colors = null;
        // }

        // $pvA = (object)$pv2->toArray();
        // $pvA = json_decode(json_encode($pv2), FALSE);
        // $pvAGroupedByProductID = arrayGroupBy($pvA,"product_id",true); 
        // Log::info(print_r($pvA, true));
        // Log::info(print_r($pvAGroupedByProductID, true));
        $pvGroupedByProductID = arrayGroupBy($pv,"product_id",true); 
        // Log::info(print_r($pv, true));
        // Log::info(print_r($pvGroupedByProductID, true));

        foreach ($products as $key => $value) {
            $tempObj = $value;
            $tempPVProductID = getObjectValue($value,"id");
            $tempObj->product_variants = getArrayValue($pvGroupedByProductID,$tempPVProductID);
            $productInstance = Product::find($tempPVProductID);
            if($productInstance){
              $instance = $productInstance->taxes;
              if($instance->count()>0){
                $tempObj->product_tax = json_encode($instance->toArray(), true);
              }else{
                $tempObj->product_tax = null;
              }
              $conversions = $productInstance->conversions;
              if($conversions->count()>0){
                $converted = $conversions->toArray();
                $conversion_relations = array();
                foreach($converted as $conversion){
                  $conversion_units = $this->getUnitName($conversion['unit_type_id'], $companyID);
                  $conversion['unit_name'] = isset($conversion_units)?$conversion_units['unit_name']:null;
                  $conversion['unit_symbol'] = isset($conversion_units)?$conversion_units['unit_symbol']:null;
                  $converted_units = $this->getUnitName($conversion['converted_unit_type_id'], $companyID);
  
                  $conversion['converted_unit_name'] = isset($converted_units)?$converted_units['unit_name']:null;
                  $conversion['converted_unit_symbol'] = isset($converted_units)?$converted_units['unit_symbol']:null;
                  array_push($conversion_relations, $conversion);
                }
                $tempObj->conversion = json_encode($conversion_relations, true);
              }else{
                $tempObj->conversion = null;
              }
              array_push($finalArray,$tempObj);

            }
        }   

        return $finalArray;

        //return $products;
    }

    private function getUnitName($id, $company_id)
    {
        $unit = DB::table('unit_types')->where('company_id', $company_id)->where('id', $id)->first();
        if ($unit){
          $units= array();
          $units["unit_name"] = $unit->name;
          $units["unit_symbol"] = $unit->symbol;

          return $units;
        }
        else{
          return NULL;
        }
    }

     private function getColors($id){
      $colorIds = DB::table('color_product_variant')->where('product_variant_id', $id)->pluck('color_id')->toArray();
      if(empty($colorIds)){
        return null;
      }else{
        $colors = Color::whereIn('id', $colorIds)->pluck('value')->toArray();
        return json_encode($colors);
      }
    }

    private function getProductLines($orderID, $tax_flag)
    {

        if (empty($orderID)) return null;

        $orderProducts = OrderDetails::select('orderproducts.*', 'products.product_name', 'products.short_desc')
            ->join('products', 'products.id', '=', 'orderproducts.product_id')
            ->where('order_id', $orderID)
            ->get();
        if($tax_flag==1){
          foreach($orderProducts as $orderProduct){
            $taxes = $orderProduct->taxes()->withTrashed()->get();
            if($taxes->count()==0){
              $orderProduct->total_tax = null;
            }else{
              $orderProduct->total_tax = json_encode($taxes->toArray());
            }
          }
        }
        $orderProducts = $orderProducts->toArray();
        return empty($orderProducts) ? null : json_encode($orderProducts);
    }

    private function getTaxes($orderID)
    {

        if (empty($orderID)) return null;

        $order_instance = Order::findOrFail($orderID);
        $taxes = $order_instance->taxes()->withTrashed()->get()->toArray();
        return empty($taxes) ? null : json_encode($taxes);
    }

    public function fetchOrders($return = false, $tempPostData = null)
    {
        $postData = $return ? $tempPostData : $this->getJsonRequest();
        $user = Auth::user();
        $companyID = $user->company_id;
        $employee = Employee::where('company_id',$companyID)->where('user_id',$user->id)->first();
        $employeeID = $employee->id;
        $employeeName = $this->getArrayValue($postData, "employee_name");

        /*Check if unsynced data is available . if available first update to tha database */
        $syncStatus = $this->manageUnsyncedOrder($postData);

        $orders = Order::leftJoin('employees', 'orders.employee_id', 'employees.id')
                  ->leftJoin('clients', 'orders.client_id', 'clients.id')
                  ->leftJoin('client_settings', 'orders.company_id','client_settings.company_id')
                  ->leftJoin('module_attributes','orders.delivery_status_id','module_attributes.id')
                  ->select('orders.*', 'employees.name as employee_name', 'clients.company_name as company_name', 'clients.name as client_name','client_settings.order_prefix','module_attributes.id as moduleattributesId','module_attributes.title as delivery_status','module_attributes.color','module_attributes.order_amt_flag','module_attributes.order_edit_flag','module_attributes.order_delete_flag')
                  ->where('orders.company_id', $companyID)
                                    ->where('orders.employee_id', $employeeID)
                                    ->get();

        if (empty($orders)) {
            if ($return) {
                return array();
            } else {

                $this->sendEmptyResponse();
            }
        }

        $finalArray = array();
        $moduleAttributes =  ModuleAttribute::where('company_id', $companyID)->get();
        // foreach ($orders as $key => $value) {

        //     $productLines = $this->getProductLines($value->id);
        //     $taxes = $this->getTaxes($value->id);
        //     $value->orderproducts = $productLines;
        //     $value->taxes = $taxes;
        //     if($value->delivery_status){
        //         $delivery_status_color = $moduleAttributes->where('title', '=',$value->delivery_status)->first();
        //         if($delivery_status_color){
        //             $value->delivery_status_color = $delivery_status_color->color;    
        //         }else{
        //             $value->delivery_status_color = NULL;
        //         }
        //     }else{
        //         $value->delivery_status_color = NULL;
        //     }
        //     array_push($finalArray, $value);
        // }
        foreach ($orders as $key => $value) {
          $productLines = $this->getProductLines($value->id, $value->product_level_tax_flag);
          if($value->product_level_tax_flag==1 && !empty($decodedLines)){
            $decodedLines = json_decode($productLines, true);
            $orderLineTaxes = $this->getOrderLineTaxes(array_column($decodedLines, 'id'));
            $value->orderLineTaxes = $orderLineTaxes;
          }else{
            $value->orderLineTaxes = null;
            $value->taxes = $this->getTaxes($value->id);
          }
          $value->orderproducts = $productLines;
          if ($value->delivery_status) {
              $delivery_status_color = $moduleAttributes->where('title', '=', $value->delivery_status)->first();
              if ($delivery_status_color) {
                  $value->delivery_status_color = $delivery_status_color->color;
              } else {
                  $value->delivery_status_color = null;
              }
          } else {
              $value->delivery_status_color = null;
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

    private function getOrderLineTaxes($orderID){
      if (empty($orderID)) {
          return null;
      }
      $taxes = DB::table('tax_on_orderproducts')->whereIn('orderproduct_id', $orderID)->get()->toArray();
      return empty($taxes) ? null : json_encode($taxes);
    }

    private function manageUnsyncedOrder($postData, $returnItems = false, $client = null)
     {
      $rawData = $this->getArrayValue($postData, "nonsynced_orders");
      $user = Auth::user();
      $companyID = $user->company_id;
      $employee = Employee::where('company_id', $companyID)->where('user_id', $user->id)->first();
      $employeeID = $employee->id;
      // $createdAt = $this->getArrayValue($postData, "created_at");
      // $employeeName = $this->getArrayValue($postData, "employee_name");

      if (empty($rawData)) return $returnItems ? array() : false;

      $data = json_decode($rawData, true);

      $arraySyncedData = array();
      try{
        foreach ($data as $key => $order) {
          $orderClientID = (int)$this->getArrayValue($order, "client_id");
          $orderClientUniqueID = $this->getArrayValue($order, "client_unique_id");
          $orderUniqueID = $this->getArrayvalue($order, "unique_id");
          $orderID = $this->getArrayValue($order, "id");
          $orderNumber = (int)$this->getArrayValue($order, "order_no");
          $product_level_discount_flag = $this->getArrayValue($order, "product_level_discount_flag")?$this->getArrayValue($order, "product_level_discount_flag"):0;
          $product_level_tax_flag = $this->getArrayValue($order, "product_level_tax_flag")?$this->getArrayValue($order, "product_level_tax_flag"):0;
          $tempOrderArray["delivery_status_id"] = $this->getArrayvalue($order, "delivery_status_id");
  
          if (!isset($orderClientID)) {
            if ($returnItems && !empty($client)) {
              $tempClientUniqueID = $client->unique_id;
              $tempClientID = $client->id;
              if ($orderClientUniqueID == $tempClientUniqueID) $orderClientID = $tempClientID;
              else continue;
            }
          }
          
          if (isset($orderID)) {
            $order_instance = Order::where('company_id', $companyID)->where('id', $orderID)->first();
            if ($product_level_tax_flag==1) {
              $order_details = $order_instance->orderdetails;
              foreach ($order_details as $orderdetail) {
                $orderdetail->taxes()->detach();
              }
            }
            OrderDetails::where('order_id', $orderID)->delete();
            $employeeID = $order_instance->employee_id;
          } else {
            $orderNumber = getOrderNo($companyID);
            $pendingModuleAttribute = ModuleAttribute::where('company_id', $companyID)->where('title', 'Pending')->first();
            $tempOrderArray["delivery_status_id"] = $pendingModuleAttribute->id;
          }
          // $orderUniqueID = $this->getArrayvalue($order, "unique_id");
          $tempOrderArray["unique_id"] = $orderUniqueID;
          $tempOrderArray["company_id"] = $companyID;
          $tempOrderArray["employee_id"] = $employeeID;
          $tempOrderArray["client_id"] = $orderClientID;
          $tempOrderArray["order_no"] = $orderNumber;
          $tempOrderArray["order_date"] = $this->getArrayvalue($order, "order_date");
          
          if($this->getArrayvalue($order, "client_id")){
              $client = Client::where('company_id',$companyID)->where('id',$this->getArrayvalue($order, "client_id"))->first();
              if($client)
                $tempOrderArray["due_date"] = Carbon::parse($this->getArrayvalue($order, "order_date"))->addDays($client->credit_days)->format('Y-m-d');
          }
          $tot_amount = floatval($this->getArrayvalue($order, "tot_amount"));
          $tempOrderArray["tot_amount"] = "$tot_amount";
          $grand_total = floatval($this->getArrayvalue($order, "grand_total"));
          $tempOrderArray["tax"] = $this->getArrayvalue($order, "tax");
          $tempOrderArray["discount"] = $this->getArrayvalue($order, "discount");
          $tempOrderArray["discount_type"] = $this->getArrayvalue($order, "discount_type");
          $tempOrderArray["grand_total"] = "$grand_total";
          $tempOrderArray["delivery_status"] = $this->getArrayvalue($order, "delivery_status");
          // $tempOrderArray["delivery_status_id"] = $this->getArrayvalue($order, "delivery_status_id");
          
          $tempOrderArray['delivery_date'] = $this->getArrayValue($order, "delivery_date");
          $tempOrderArray['delivery_place'] = $this->getArrayValue($order, "delivery_place");
          $tempOrderArray['transport_name'] = $this->getArrayValue($order, "transport_name");
          $tempOrderArray['transport_number'] = $this->getArrayValue($order, "transport_number");
          $tempOrderArray['billty_number'] = $this->getArrayValue($order, "billty_number");
          $tempOrderArray['delivery_note'] = $this->getArrayValue($order, "delivery_note");
  
          // $tempOrderArray["order_note"] = $this->getArrayvalue($order, "order_note");
          $tempOrderArray["created_at"] = $this->getArrayvalue($order, "created_at");
          $tempOrderArray["product_level_discount_flag"] = "$product_level_discount_flag";
          $tempOrderArray["product_level_tax_flag"] = "$product_level_tax_flag";
          
          $mil = $this->getArrayvalue($order, "unix_timestamp");
          $seconds = ceil($mil / 1000);
          $order["order_datetime"] = date("Y-m-d H:i:s", $seconds);
          $latitude = $this->getArrayValue($order, "latitude");
          $longitude = $this->getArrayValue($order, "longitude");
          if(isset($latitude) && isset($longitude)){
            $tempOrderArray["latitude"] = $latitude;
            $tempOrderArray["longitude"] = $longitude;
          }
  
          //check if already exists
          // $alreadyAddedOrder = Order::select('*')
          //                     ->where('employee_id', $employeeID)
          //                     ->where('company_id', $companyID)
          //                     ->where('unique_id', $orderUniqueID)
          //                     ->get()
          //                     ->first();
  
          // if (!empty($alreadyAddedOrder)) {
          //   array_push($arraySyncedData, $alreadyAddedOrder->toArray());
          //   continue;
          // }
  
          // if(isset($orderID)){
          //   $alreadyAddedOrder = Order::find($orderID);
          //   if (!empty($alreadyAddedOrder)) {
          //     array_push($arraySyncedData, $alreadyAddedOrder->toArray());
          //     continue;
          //   }
          // }
          // Log::info($tempOrderArray);
          if($orderID){
            $update_order = Order::find($orderID);
            $update_order->update($tempOrderArray);
          }else{
            $orderID = Order::insertGetId($tempOrderArray);
          }
          // $orderID = Order::insertGetId($tempOrderArray);
          // $orderCreateOrUpdated = Order::updateOrCreate(['id' => $orderID], $tempOrderArray);
          // $orderID = $orderCreateOrUpdated->id;
  
          if (isset($orderID)) {
            $orderData = $tempOrderArray;
            $orderData["id"] = $orderID;
            array_push($arraySyncedData, $orderData);
            saveAdminNotification($companyID, $employeeID, date("Y-m-d H:i:s"), "Added Order", "order", $orderData);
            
            //Saving taxes
            $taxes = $this->getArrayValue($order, "taxes");
            if (!empty($taxes) && $product_level_tax_flag==0) {
              $taxes = json_decode($taxes, true);
              $taxOnOrderArray = array();
              foreach ($taxes as $tax) {
                $tempArrayTax = array(
                  'order_id' => $orderID,
                  'tax_type_id' => $this->getArrayValue($tax, "id"),
                  'tax_name' => $this->getArrayValue($tax, "name"),
                  'tax_percent' => $this->getArrayValue($tax, "percent")
                );
                array_push($taxOnOrderArray, $tempArrayTax);
              }
              DB::table('tax_on_orders')->insert($taxOnOrderArray);
            }
            
            //Saving OrderProducts
            $orderProducts = $this->getArrayValue($order, "orderproducts");
  
            if (!empty($orderProducts)) {
              $op = json_decode($orderProducts, true);
              if (is_array($op) && !empty($op)) {
                $opFinalArray = array();
                $total_tax_applied_amt = 0;
                $total_tax_applied = 0;
                foreach ($op as $k => $v) {
                  $opTemp = array();
                  $mrp = floatval($this->getArrayValue($v, "mrp"));
                  $rate = floatval($this->getArrayValue($v, "rate"));
                  $quantity = floatval($this->getArrayValue($v, "quantity"));
                  $ptotal_amt = floatval($this->getArrayValue($v, "ptotal_amt"));
                  $pdiscount = floatval($this->getArrayValue($v, "pdiscount"));
                  $pdiscount_type = $this->getArrayValue($v, "pdiscount_type");
  
                  if(!isset($pdiscount)) $pdiscount = 0;
  
                  if($product_level_discount_flag==1){
                    switch($pdiscount_type){
                      case "Amt":
                        $rate = $rate-$pdiscount; 
                        $ptotal_amt = $rate*$quantity; 
                        break;
                      case "%":
                        $rate = $rate-(($pdiscount/100)*$rate);
                        $ptotal_amt = $rate*$quantity;
                        break;
                      case "oAmt":
                        $rate = $rate;
                        $ptotal_amt = $rate*$quantity-$pdiscount;
                        break;
                      default:
                        break;
                    }
                  }
  
                  $opTemp["order_id"] = $orderID;
                  $opTemp["product_id"] = $this->getArrayValue($v, "product_id");
                  $opTemp["product_name"] = $this->getArrayValue($v, "product_name");
                  $opTemp["product_variant_id"] = $this->getArrayValue($v, "product_variant_id");
                  $opTemp["product_variant_name"] = $this->getArrayValue($v, "product_variant_name");
                  $opTemp["mrp"] = $mrp;
                  $opTemp["unit"] = $this->getArrayValue($v, "unit");
                  $opTemp["unit_name"] = $this->getArrayValue($v, "unit_name");
                  $opTemp["unit_symbol"] = $this->getArrayValue($v, "unit_symbol");
                  $opTemp["rate"] = $rate;
                  $opTemp["quantity"] = $quantity;
                  $opTemp["amount"] = $ptotal_amt;
                  $opTemp["short_desc"] = $this->getArrayValue($v, "short_desc");
                  if($product_level_discount_flag==1){
                    $opTemp["pdiscount"] = $pdiscount;
                    $opTemp["pdiscount_type"] = $pdiscount_type;
                  }
                  $opTemp["ptotal_amt"] = $ptotal_amt;
                  $opTemp["variant_colors"] = $this->getArrayValue($v, "variant_colors");
                  $opTemp["created_at"] = $this->getArrayValue($v, "created_at");
                  array_push($opFinalArray, $opTemp);
                  $batchSaved = OrderDetails::insertGetId($opTemp);
                  if($batchSaved){
                    if($product_level_tax_flag==1){
                      $tax_applied = floatval(0.0);
                      $ptaxes = $this->getArrayValue($v, "total_tax");
                      $decodeValue = json_decode($ptaxes, true);
                      if (!empty($decodeValue)) {
                        foreach ($decodeValue as $key=>$ptax) {
                          $tax_applied += floatval($ptax['percent']);
                          DB::table('tax_on_orderproducts')->insert([
                            "orderproduct_id" => $batchSaved,
                            "tax_type_id" => $ptax['id'],
                            "product_id" => $opTemp["product_id"],
                          ]);
                        }
                        $tax_amount = floatval($tax_applied/100.00)*$ptotal_amt;
                        $total_tax_applied = $total_tax_applied + $tax_amount;
                        $amount_with_tax = $ptotal_amt+$tax_amount;
                        $total_tax_applied_amt = $total_tax_applied_amt + $amount_with_tax;
                        OrderDetails::find($batchSaved)->update([
                          "amount" => "$amount_with_tax",
                          "ptotal_amt" => "$amount_with_tax"
                        ]);
                      }
                    }
                  }
                }
                if($product_level_tax_flag==1){
                  DB::beginTransaction();
                  $order_instance = Order::find($orderID);
                  $order_instance->tax = "$total_tax_applied";
                  $commit = $order_instance->save();
                  DB::commit();
                }
              }
            }
          }
        }
      }catch(\Exception $e){
        return $e->getMessage();
      }

      return $returnItems ? $arraySyncedData : true;
    }

    public function fetchNoOrder($return = false, $tempPostData = null)
    {

        $postData = $return ? $tempPostData : $this->getJsonRequest();
        $user = Auth::user();
		$companyID = $user->company_id;
		$employee = Employee::where('company_id',$companyID)->where('user_id',$user->id)->first();
		$employeeID = $employee->id;

        //Check if unsynced data is available . if available first update to tha database
        $syncStatus = $this->manageUnsyncedNoOrder($postData);


        $noOrders = DB::table('no_orders')
            ->select('no_orders.*', 'clients.name', 'clients.company_name')
            ->join('clients', 'clients.id', '=', 'no_orders.client_id')
            ->where('no_orders.company_id', $companyID)
            ->where('no_orders.employee_id', $employeeID)
            ->get()->toArray();


        if (empty($noOrders)) {
            if ($return) {
                return array();
            } else {

                $this->sendEmptyResponse();
            }
        }
        $response = array("status" => true, "message" => "Success", "data" => $noOrders);
        if ($return) {
            return $noOrders;
        } else {
            $this->sendResponse($response);
        }

    }

    private function manageUnsyncedNoOrder($postData, $returnItems = false, $client = null)
    {

        $rawData = $this->getArrayValue($postData, "nonsynced_no_orders");
        $user = Auth::user();
		$companyID = $user->company_id;
		$employee = Employee::where('company_id',$companyID)->where('user_id',$user->id)->first();
		$employeeID = $employee->id;
        $employeeName = $this->getArrayValue($postData, "employee_name");


        if (empty($rawData)) return $returnItems ? array() : false;

        $data = json_decode($rawData, true);

        $arraySyncedData = array();
        foreach ($data as $key => $noOrder) {

            $noOrderClientID = $this->getArrayValue($noOrder, "client_id");
            $noOrderClientUniqueID = $this->getArrayValue($noOrder, "client_unique_id");

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

            $dateTime = $this->getArrayvalue($noOrder, "datetime");

            $noOrderUniqueID = $this->getArrayvalue($noOrder, "unique_id");
            $tempNoOrderArray["unique_id"] = $noOrderUniqueID;
            $tempNoOrderArray["company_id"] = $companyID;
            $tempNoOrderArray["employee_id"] = $employeeID;
            $tempNoOrderArray["client_id"] = $noOrderClientID;
            $tempNoOrderArray["remark"] = $this->getArrayValue($noOrder, "remark");
            $tempNoOrderArray["date"] = $this->getArrayvalue($noOrder, "date");
            $tempNoOrderArray["datetime"] = $dateTime;
            $tempNoOrderArray["created_at"] = $this->getArrayvalue($noOrder, "datetime");
            $tempNoOrderArray["unix_timestamp"] = $this->getArrayvalue($noOrder, "unix_timestamp");

            //check if already exists
            $alreadyAddedNoOrder = NoOrder::select('*')
                ->where('employee_id', $employeeID)
                ->where('company_id', $companyID)
                ->where('unique_id', $noOrderUniqueID)
                ->get()
                ->first();

            if (!empty($alreadyAddedNoOrder)) {
                array_push($arraySyncedData, $alreadyAddedNoOrder->toArray());
                continue;
            }

            $noOrderID = DB::table('no_orders')->insertGetId($tempNoOrderArray);
            if (!empty($noOrderID)) {

                $orderData = $tempNoOrderArray;
                $orderData["id"] = $noOrderID;
                array_push($arraySyncedData, $orderData);
                saveAdminNotification($companyID, $employeeID, date("Y-m-d H:i:s"), "Added No Order", "noorders", $orderData);
            }
        }

        return $returnItems ? $arraySyncedData : true;
    }

    public function fetchCollection($return = false, $tempPostData = null)
    {

        $postData = $this->getJsonRequest();

        $user = Auth::user();
		$companyID = $user->company_id;
		$employee = Employee::where('company_id',$companyID)->where('user_id',$user->id)->first();
		$employeeID = $employee->id;

        /*Check if unsynced data is available . if available first update to tha database */
        $syncStatus = $this->manageUnsyncedCollection($postData);

        $collections = DB::table('collections')
            ->select('collections.*', 'clients.company_name','banks.name as bank_name')
            ->leftJoin('clients', 'collections.client_id', '=', 'clients.id')
            ->leftJoin('banks', 'collections.bank_id', '=', 'banks.id')
            ->where('collections.company_id', $companyID)
            ->where('collections.employee_id', $employeeID)
            ->whereNull('collections.deleted_at')
            ->get()->toArray();

        if (empty($collections)) {
            if ($return) {
                return array();
            } else {

                $this->sendEmptyResponse();
            }
        }

        $finalArray = array();
        foreach ($collections as $key => $value) {

            $imageArray = getImageArray("collection", $value->id,$companyID,$employeeID);
            $value->image_ids   = json_encode($this->getArrayValue($imageArray,"image_ids"));
            $value->images = json_encode($this->getArrayValue($imageArray, "images"));
            $value->image_paths = json_encode($this->getArrayValue($imageArray, "image_paths"));
            array_push($finalArray, $value);
        }

        $response = array("status" => true, "message" => "Success", "data" => $finalArray);

        if ($return) {
            return $finalArray;
        } else {
            $this->sendResponse($response);
        }
    }

    private function manageUnsyncedCollection($postData, $returnItems = false, $client = null)
    {

        $rawData = $this->getArrayValue($postData, "nonsynced_collection");
        $user = Auth::user();
		$companyID = $user->company_id;
		$employee = Employee::where('company_id',$companyID)->where('user_id',$user->id)->first();
		$employeeID = $employee->id;

        if (empty($rawData)) {
            return $returnItems ? array() : false;
        }

        $data = json_decode($rawData, true);
        $arraySyncedData = array();
        foreach ($data as $key => $col) {
            $colClientID = $this->getArrayValue($col, "client_id");
            $colClientUniqueID = $this->getArrayValue($col, "client_unique_id");

            if (empty($colClientID)) {
                if ($returnItems && !empty($client)) {
                    $tempClientUniqueID = $client->unique_id;
                    $tempClientID = $client->id;
                    if ($colClientUniqueID == $tempClientUniqueID) {
                        $colClientID = $tempClientID;
                    } else {
                        continue;
                    }
                }
            }

            $colID = $this->getArrayValue($col, "collection_id");
            $colUniqueID = $this->getArrayValue($col, "unique_id");
            $images = $this->getArrayValue($col, "images");
            $imagePaths = $this->getArrayValue($col, "image_paths");
            $createdAt = $this->getArrayvalue($col, "created_at");
            $paymentStatus = $this->getArrayvalue($col, "payment_status","Pending");
            $paymentStatusNote = $this->getArrayvalue($col, "payment_status_note","N/A");

            $colTempArray["id"] = $colID;
            $colTempArray["unique_id"] = $colUniqueID;
            $colTempArray["company_id"] = $companyID;
            $colTempArray["employee_id"] = $employeeID;
            $colTempArray["client_id"] = $colClientID;
            $colTempArray["payment_received"] = $this->getArrayvalue($col, "payment_received");
            $colTempArray["payment_method"] = $this->getArrayvalue($col, "payment_method");
            $colTempArray["payment_status"] = $paymentStatus;
            $colTempArray["bank_id"] = $this->getArrayvalue($col, "bank_id");
            $colTempArray["cheque_no"] = $this->getArrayvalue($col, "cheque_no");
            $colTempArray["cheque_date"] = $this->getArrayvalue($col, "cheque_date");
            $colTempArray["due_payment"] = $this->getArrayvalue($col, "due_payment");
            $colTempArray["payment_note"] = $this->getArrayvalue($col, "payment_note");
            $colTempArray["payment_status_note"] = $paymentStatusNote;
            $colTempArray["payment_date"] = $this->getArrayvalue($col, "payment_date");
            $colTempArray["next_date"] = $this->getArrayvalue($col, "next_date");
            $colTempArray["created_at"] = $createdAt;

            //check if already exists
            $alreadyAddedCol = Collection::select('*')
                ->where('employee_id', $employeeID)
                ->where('company_id', $companyID)
                ->where('id', $colID)
                ->get()
                ->first();

            if (!empty($alreadyAddedCol)) {

                $alreadyAddedCol->payment_status = $paymentStatus;
                $alreadyAddedCol->payment_status_note = $paymentStatusNote;
                $alreadyAddedCol->save();

                $arrayAlreadyAddedCol = $alreadyAddedCol->toArray();

                array_push($arraySyncedData, $arrayAlreadyAddedCol);
                saveAdminNotification($companyID, $employeeID, date("Y-m-d H:i:s"), "Changed Cheque Status to ".$paymentStatus, "cheque", $alreadyAddedCol);


            } else {

                $savedID = DB::table('collections')->insertGetId($colTempArray);

                if (!empty($savedID)) {

                    $imageArray = array();
                    $tempImageNames = array();
                    $tempImagePaths = array();

                    //saving images
                    if (!empty($imagePaths)) {
                        $jsonDecoded = json_decode($images, true);
                        
                        foreach ($jsonDecoded as $key => $value) {
                            $tempImageName = $this->getImageName();
                            $tempImageDir = $this->getImagePath($companyID, "collection");
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
                                $tempImageArray["type"] = "collection";
                                $tempImageArray["type_id"] = $savedID;
                                $tempImageArray["company_id"] = $companyID;
                                $tempImageArray["employee_id"] = $employeeID;
                                $tempImageArray["image"] = $imageName;
                                $tempImageArray["image_path"] = $imagePath;
                                $tempImageArray["created_at"] = $createdAt;
                                array_push($imageData, $tempImageArray);
                            }
                            DB::table('images')->insert($imageData);
                        }
                    }

                    $colData = $colTempArray;
                    $colData["id"] = $savedID;
                    $colData["images"] = $tempImageNames;
                    $colData["image_paths"] = $tempImagePaths;
                    array_push($arraySyncedData, $colData);
                    saveAdminNotification($companyID, $employeeID, date("Y-m-d H:i:s"), "Added Collection", "collection", $colData);
                }
            }


            
        }//end foreach

        return $returnItems ? $arraySyncedData : false;
    }

    public function fetchMeeting($return = false, $tempPostData = null)
    {

        $postData = $return ? $tempPostData : $this->getJsonRequest();

        $user = Auth::user();
		$companyID = $user->company_id;
		$employee = Employee::where('company_id',$companyID)->where('user_id',$user->id)->first();
		$employeeID = $employee->id;

        /*Check if unsynced data is available . if available first update to tha database */
        $syncStatus = $this->manageUnsyncedMeeting($postData);

        $meetings = DB::table('meetings')
            ->select('meetings.*', 'clients.company_name','employees.name as employee_name')
            ->leftJoin('employees','meetings.employee_id','employees.id')
            ->leftJoin('clients', 'meetings.client_id', '=', 'clients.id')
            ->where('meetings.company_id', $companyID)
            ->where('meetings.employee_id', $employeeID)
            ->whereNull('meetings.deleted_at')
            ->orderBy('created_at', 'desc')
            ->get()->toArray();

        if (empty($meetings)) {
            if ($return) {
                return array();
            } else {

                $this->sendEmptyResponse();
            }
        }

        $finalArray = array();
        foreach ($meetings as $key => $value) {
            $imageArray = getImageArray("notes", $value->id,$companyID,$employeeID);
            $value->image_ids   = json_encode($this->getArrayValue($imageArray,"image_ids"));
            $value->images = json_encode($this->getArrayValue($imageArray, "images"));
            $value->image_paths = json_encode($this->getArrayValue($imageArray, "image_paths"));
            array_push($finalArray, $value);
        }

        $response = array("status" => true, "message" => "Success", "data" => $finalArray);

        if ($return) {
            return $meetings;
        } else {
            $this->sendResponse($response);
        }
    }

    private function manageUnsyncedMeeting($postData, $returnItems = false, $client = null)
    {

        $rawData = $this->getArrayValue($postData, "nonsynced_meeting");
        $user = Auth::user();
		$companyID = $user->company_id;
		$employee = Employee::where('company_id',$companyID)->where('user_id',$user->id)->first();
		$employeeID = $employee->id;

        if (empty($rawData)) {
            return $returnItems ? array() : false;
        }
        $data = json_decode($rawData, true);
        $arraySyncedData = array();
        foreach ($data as $key => $meeting) {

            $meetingClientID = $this->getArrayValue($meeting, "client_id");
            $meetingClientUniqueID = $this->getArrayValue($meeting, "client_unique_id");

            if (empty($meetingClientID)) {
                if ($returnItems && !empty($client)) {
                    $tempClientUniqueID = $client->unique_id;
                    $tempClientID = $client->id;
                    if ($meetingClientUniqueID == $tempClientUniqueID) {
                        $meetingClientID = $tempClientID;
                    } else {
                        continue;
                    }
                }
            }

            $noteUniqueID = $this->getArrayValue($meeting, "unique_id");
            $images     = $this->getArrayValue($meeting,"images");
            $imagePaths = $this->getArrayValue($meeting,"image_paths");
            $createdAt = date('Y-m-d H:i:s');
            $tempMeetingArray["unique_id"] = $noteUniqueID;
            $tempMeetingArray["company_id"] = $companyID;
            $tempMeetingArray["employee_id"] = $employeeID;
            $tempMeetingArray["client_id"] = $meetingClientID;
            $tempMeetingArray["checkintime"] = $this->getArrayvalue($meeting, "checkintime");
            $tempMeetingArray["meetingdate"] = $this->getArrayvalue($meeting, "meetingdate");
            $tempMeetingArray["remark"] = $this->getArrayvalue($meeting, "remark");
            $tempMeetingArray["comm_medium"] = $this->getArrayvalue($meeting, "comm_medium");
            $tempMeetingArray["latitude"] = $this->getArrayvalue($meeting, "latitude");
            $tempMeetingArray["longitude"] = $this->getArrayvalue($meeting, "longitude");
            $tempMeetingArray["created_at"] = $createdAt;           

            //Check if already exists
            $alreadyAddedNote = DB::table('meetings')
            ->where('company_id',$companyID)
            ->where('employee_id',$employeeID)
            ->where('unique_id',$noteUniqueID)
            ->get()
            ->first();

            if(!empty($alreadyAddedNote)){
                // Do something

            }else{
                $savedID = DB::table('meetings')->insertGetId($tempMeetingArray);
                if (!empty($savedID)) {

                  $imageArray = array();
                  $tempImageNames = array();
                  $tempImagePaths = array();

                  //saving images
                  if (!empty($imagePaths)) {
                      $jsonDecoded = json_decode($images, true);
                      //Log::info('info', array("jsonDecoded"=>print_r($jsonDecoded,true)));
                      
                      foreach ($jsonDecoded as $key => $value) {
                          $tempImageName = $this->getImageName();
                          $tempImageDir = $this->getImagePath($companyID, "notes");
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
                              $tempImageArray["type"] = "notes";
                              $tempImageArray["type_id"] = $savedID;
                              $tempImageArray["company_id"] = $companyID;
                              $tempImageArray["employee_id"] = $employeeID;
                              $tempImageArray["image"] = $imageName;
                              $tempImageArray["image_path"] = $imagePath;
                              $tempImageArray["created_at"] = $createdAt;
                              array_push($imageData, $tempImageArray);
                          }
                          DB::table('images')->insert($imageData);
                      }
                  }
                  $meetingData = $tempMeetingArray;
                  $meetingData["id"] = $savedID;
                  $meetingData["images"] = $tempImageNames;
                  $meetingData["image_paths"] = $tempImagePaths;
                  array_push($arraySyncedData, $meetingData);
                  saveAdminNotification($companyID, $employeeID, date("Y-m-d H:i:s"), "Added Note", "notes", $meetingData);
                }
            }

        }
        return $returnItems ? $arraySyncedData : false;
    }

    public function fetchLeave($return = false, $tempPostData = null)
    {

        $postData = $return ? $tempPostData : $this->getJsonRequest();

        $user = Auth::user();
		$companyID = $user->company_id;
		$employee = Employee::where('company_id',$companyID)->where('user_id',$user->id)->first();
		$employeeID = $employee->id;

        /*Check if unsynced data is available . if available first update to tha database */
        $syncStatus = $this->manageUnsyncedLeave($postData);

        $leaves = DB::table('leaves')->where(
            array(
                array("company_id", "=", $companyID),
                array("employee_id", "=", $employeeID)
            )
        )->get()->toArray();

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

            $createdAt = $this->getArrayvalue($value, "created_at");
            $tempArray["unique_id"] = $this->getArrayValue($value, "unique_id");
            $tempArray["company_id"] = $companyID;
            $tempArray["employee_id"] = $employeeID;
            $tempArray["leavetype"] = $this->getArrayvalue($value, "leavetype");;
            $tempArray["start_date"] = $this->getArrayvalue($value, "start_date");
            $tempArray["end_date"] = $this->getArrayvalue($value, "end_date");
            $tempArray["leave_desc"] = $this->getArrayvalue($value, "leave_desc");
            $tempArray["remarks"] = $this->getArrayvalue($value, "remark");
            $tempArray["status"] = $this->getArrayvalue($value, "status");
            $tempArray["status_reason"] = $this->getArrayvalue($value, "status");
            $tempArray["created_at"] = $createdAt;

            $savedID = DB::table('leaves')->insertGetId($tempArray);

            if (!empty($savedID)) {
                $syncedData = $tempArray;
                $syncedData['id'] = $savedID;
                array_push($arraySyncedData, $syncedData);
                saveAdminNotification($companyID, $employeeID, date("Y-m-d H:i:s"), "Applied For Leave", "leave", $syncedData);
            }
        }

        return $returnItems ? $arraySyncedData : true;
    }

    public function fetchExpense($return = false, $tempPostData = null)
    {

        $postData = $return ? $tempPostData : $this->getJsonRequest();
        $user = Auth::user();
		$companyID = $user->company_id;
		$employee = Employee::where('company_id',$companyID)->where('user_id',$user->id)->first();
		$employeeID = $employee->id;

        /*Check if unsynced data is available . if available first update to tha database */
        $syncStatus = $this->manageUnsyncedExpense($postData);

        $expenses = DB::table('expenses')
            ->select('expenses.*', 'clients.company_name')
            ->leftJoin('clients', 'expenses.client_id', '=', 'clients.id')
            ->where('expenses.company_id', $companyID)
            ->where('expenses.employee_id', $employeeID)
            ->whereNull('expenses.deleted_at')
            ->get()->toArray();

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

            // $imageArray = getImageArray("expense", $value->id,$companyID,$employeeID);
            // $value->images = json_encode($this->getArrayValue($imageArray, "images"));
            // $value->image_paths = json_encode($this->getArrayValue($imageArray, "image_paths"));
            array_push($finalArray, $value);
        }
        $response = array("status" => true, "message" => "Success", "data" => $finalArray);

        if ($return) {
            return $finalArray;
        } else {
            $this->sendResponse($response);
        }
    }

    private function manageUnsyncedExpense($postData, $returnItems = false, $client = null)
    {
        //Log::info('info', array("inside manageUnsyncedExpense"=>print_r($postData,true)));

        $rawData = $this->getArrayValue($postData, "nonsynced_expense");
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
        foreach ($data as $key => $expense) {

            $expenseClientID = $this->getArrayValue($expense, "client_id");
            $expenseClientUniqueID = $this->getArrayValue($expense, "client_unique_id");

            if (empty($expenseClientID)) {
                if ($returnItems && !empty($client)) {
                    $tempClientUniqueID = $client->unique_id;
                    $tempClientID = $client->id;
                    if ($expenseClientUniqueID == $tempClientUniqueID) {
                        $expenseClientID = $tempClientID;
                    } else {
                        continue;
                    }
                }
            }

            $createdAt = date('Y-m-d H:i:s');
            $images = $this->getArrayValue($expense, "images");
            $imagePaths = $this->getArrayValue($expense, "image_paths");

            $expenseData = array(
                'unique_id' => $this->getArrayValue($expense, "unique_id"),
                'company_id' => $companyID,
                'employee_id' => $employeeID,
                'employee_type' => 'Employee',
                'client_id' => $expenseClientID,
                'amount' => $this->getArrayvalue($expense, "amount"),
                'description' => $this->getArrayValue($expense, "description"),
                'approved_by' => $this->getArrayValue($expense, "approved_by"),
                'remark' => $this->getArrayValue($expense, "remark"),
                'status' => $this->getArrayValue($expense, "status"),
                'created_at' => $createdAt,
                'updated_at' => $this->getArrayValue($expense, "updated_at")
            );

            $savedID = DB::table('expenses')->insertGetId($expenseData);


            if (!empty($savedID)) {

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
                            $tempImageArray["type_id"] = $savedID;
                            $tempImageArray["company_id"] = $companyID;
                            $tempImageArray["employee_id"] = $employeeID;
                            $tempImageArray["image"] = $imageName;
                            $tempImageArray["image_path"] = $imagePath;
                            $tempImageArray["created_at"] = $createdAt;
                            array_push($imageData, $tempImageArray);
                        }
                        DB::table('images')->insert($imageData);
                    }
                }

                $expenseData["id"] = $savedID;
                $expenseData["images"] = $tempImageNames;
                $expenseData["image_paths"] = $tempImagePaths;
                array_push($arraySyncedData, $expenseData);
                $save = saveAdminNotification($companyID, $employeeID, date("Y-m-d H:i:s"), "Added Expense", "expense", $expenseData);
            }

        }

        return $returnItems ? $arraySyncedData : false;
    }

    public function fetchTask($return = false, $tempPostData = null)
    {

        $postData = $return ? $tempPostData : $this->getJsonRequest();
        //Log::info('info', array("postData"=>print_r($postData,true)));
        $user = Auth::user();
		$companyID = $user->company_id;
		$employee = Employee::where('company_id',$companyID)->where('user_id',$user->id)->first();
		$employeeID = $employee->id;

        /*Check if unsynced data is available . if available first update to tha database */
        $syncStatus = $this->manageUnsyncedTask($postData);

        $tasks = DB::table('tasks')
            ->select('tasks.*', 'clients.company_name')
            ->leftJoin('clients', 'clients.id', '=', 'tasks.client_id')
            ->where('tasks.company_id', $companyID)
            ->Where('assigned_to', $employeeID)
            ->get()->toArray();

        if (empty($tasks)) {
            if ($return) {
                return array();
            } else {

                $this->sendEmptyResponse();
            }
        }


        $response = array("status" => true, "message" => "Success", "data" => $tasks);
        if ($return) {
            return $tasks;
        } else {
            $this->sendResponse($response);
        }
    }

    private function manageUnsyncedTask($postData, $returnItems = false, $client = null)
    {

        $rawData = $this->getArrayValue($postData, "nonsynced_task");
        $user = Auth::user();
		$companyID = $user->company_id;
		$employee = Employee::where('company_id',$companyID)->where('user_id',$user->id)->first();
		$employeeID = $employee->id;
        $clientID = $this->getArrayValue($postData, "client_id");

        if (empty($rawData)) {
            return $returnItems ? array() : false;
        }

        $data = json_decode($rawData, true);

        $arraySyncedData = array();

        /*prepare data for saving*/
        foreach ($data as $key => $task) {

            $taskClientID = $this->getArrayValue($task, "client_id");
            $taskClientUniqueID = $this->getArrayValue($task, "client_unique_id");

            if (empty($taskClientID)) {
                if ($returnItems && !empty($client)) {
                    $tempClientUniqueID = $client->unique_id;
                    $tempClientID = $client->id;
                    if ($taskClientUniqueID == $tempClientUniqueID) {
                        $taskClientID = $tempClientID;
                    } else {
                        continue;
                    }
                }
            }

            $createdAt = date('Y-m-d H:i:s');
            $taskData = array(
                'unique_id' => $this->getArrayvalue($task, "unique_id"),
                'company_id' => $companyID,
                'client_id' => $taskClientID,
                'title' => $this->getArrayvalue($task, "title"),
                'due_date' => $this->getArrayValue($task, "due_date"),
                'description' => $this->getArrayValue($task, "description"),
                'priority' => $this->getArrayValue($task, "priority"),
                'assigned_from_type' => $this->getArrayValue($task, "assigned_from_type"),
                'assigned_from' => $this->getArrayValue($task, "assigned_from"),
                'assigned_to' => $this->getArrayValue($task, "assigned_to"),
                'status' => $this->getArrayValue($task, "status"),
            );

            $taskID = $this->getArrayValue($task, "task_id");

            if (!empty($taskID)) {
                $taskData['updated_at'] = $createdAt;
                $updated = DB::table('tasks')->where('id', $taskID)->update($taskData);
                $taskData["id"] = $taskID;
                saveAdminNotification($companyID, $employeeID, date("Y-m-d H:i:s"), "Updated Task", "task", $taskData);
                array_push($arraySyncedData, $taskData);

            } else {

                $taskData["created_at"] = $createdAt;
                $savedID = DB::table('tasks')->insertGetId($taskData);

                if (!empty($savedID)) {
                    $taskData["id"] = $savedID;
                    array_push($arraySyncedData, $taskData);
                    saveAdminNotification($companyID, $employeeID, $createdAt, "Task Added", "task", $taskData);
                }
            }
        }

        return $returnItems ? $arraySyncedData : true;
    }

    public function fetchHolidays($return = false, $postData=null)
    {

        $postData = $return?$postData:$this->getJsonRequest();
        $user = Auth::user();
		$companyID = $user->company_id;
        $offset = $this->getArrayValue($postData, "offset",0);
        $limit = $this->getArrayValue($postData, "limit",1500);

        $holidays = DB::table('holidays')->where([['company_id', '=', $companyID]])->whereNull("deleted_at")->offset($offset)->limit($limit)->get()->toArray();
        $response = array("status" => true, "message" => "Success", "data" => $holidays);
        if($return){

            return $holidays;

        } else {

            $this->sendResponse($response);
        } 
    }

    public function fetchTourPlans($return = false,$postData = null)
    {
        $postData = $return?$postData:$this->getJsonRequest();
        $user = Auth::user();
		$companyID = $user->company_id;
		$employee = Employee::where('company_id',$companyID)->where('user_id',$user->id)->first();
		$employeeID = $employee->id;
        $offset = $this->getArrayValue($postData, "offset",0);
        $limit = $this->getArrayValue($postData, "limit",200);

         /*Check if unsynced data is available . if available first update to tha database */
        $syncStatus = $this->manageUnsyncedTourPlans($postData);

        if($employeeID){
            $tour_plans = TourPlan::where('company_id', $companyID)
                ->where('employee_id',$employeeID)
                ->get()->toArray();
        }else{
            $tour_plans = TourPlan::where('company_id', $companyID)
                ->get()->toArray();
        }

        $response = array("status" => true, "message" => "Success", "data" => $tour_plans);
        if($return){

            return $tour_plans;

        } else {

            $this->sendResponse($response);
        }
    }

    public function manageUnsyncedTourPlans($postData, $returnItems = false)
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

            $tempArray["unique_id"] = $this->getArrayValue($value, "unique_id");
            $tempArray["company_id"] = $companyID;
            $tempArray["employee_id"] = $employeeID;
            $tempArray["start_date"] = $this->getArrayvalue($value, "start_date");
            $tempArray["end_date"] = $this->getArrayvalue($value, "end_date");
            $tempArray["visit_place"] = $this->getArrayvalue($value, "visit_place");
            $tempArray["visit_purpose"] = $this->getArrayvalue($value, "visit_purpose");
            $tempArray["status"] = $this->getArrayvalue($value, "status");
            $tempArray["created_at"] = $this->getArrayvalue($value, "created_at");

            $savedID = DB::table('tourplans')->insertGetId($tempArray);

            if (!empty($savedID)) {

                $syncedData = $tempArray;
                $syncedData['id'] = $savedID;
                array_push($arraySyncedData, $syncedData);
                //saveAdminNotification($companyID, $employeeID, date("Y-m-d H:i:s"), "Added TourPlan", "tourplan", $syncedData);
            }
        }

        return $returnItems ? $arraySyncedData : true;
    }

    public function fetchLeaveTypes($return = false, $tempPostData = null)
    {

        $postData = $return ? $tempPostData : $this->getJsonRequest();

        $user = Auth::user();
		$companyID = $user->company_id;
        $leaveTypes = DB::table('leave_type')->where(
            array(
                array("company_id", "=", $companyID)
            )
        )->get()->toArray();

        if (empty($leaveTypes)) {
            if ($return) {
                return array();
            } else {

                $this->sendEmptyResponse();
            }
        }

        $response = array("status" => true, "message" => "Success", "data" => $leaveTypes);

        if ($return) {
            return $leaveTypes;
        } else {
            $this->sendResponse($response);
        }
    }

    public function fetchActivityTypes($return = false, $tempPostData = null)
    {
        $postData = $return ? $tempPostData : $this->getJsonRequest();

        $user = Auth::user();
        $companyID = $user->company_id;
        $activityTypes = DB::table('activity_types')->where("company_id", $companyID)->get()->toArray();

        if (empty($activityTypes)) {
            if ($return) {
                return array();
            } else {

                $this->sendEmptyResponse();
            }
        }

        $response = array("status" => true, "message" => "Success", "data" => $activityTypes);

        if ($return) {
            return $activityTypes;
        } else {
            $this->sendResponse($response);
        }

    }

    public function fetchActivityPriorities($return = false, $tempPostData = null)
    {
        $postData = $return ? $tempPostData : $this->getJsonRequest();

        $user = Auth::user();
        $companyID = $user->company_id;
        $activityPriorities = DB::table('activity_priorities')->where("company_id", $companyID)->get()->toArray();

        if (empty($activityPriorities)) {
            if ($return) {
                return array();
            } else {

                $this->sendEmptyResponse();
            }
        }

        $response = array("status" => true, "message" => "Success", "data" => $activityPriorities);

        if ($return) {
            return $activityPriorities;
        } else {
            $this->sendResponse($response);
        }

    }

    public function fetchExpenseTypes($return = false, $tempPostData = null)
    {

        $postData = $return ? $tempPostData : $this->getJsonRequest();

        $user = Auth::user();
        $companyID = $user->company_id;
        $expenseTypes = ExpenseType::where('company_id',$companyID)->get()->toArray();

        if (empty($expenseTypes)) {
            if ($return) {
                return array();
            } else {

                $this->sendEmptyResponse();
            }
        }

        $response = array("status" => true, "message" => "Success", "data" => $expenseTypes);

        if ($return) {
            return $expenseTypes;
        } else {
            $this->sendResponse($response);
        }
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
