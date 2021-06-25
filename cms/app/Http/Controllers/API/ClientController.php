<?php

namespace App\Http\Controllers\API;

use Log;
use App\Beat;
use App\Note;
use App\Image;
use App\Order;
use App\Stock;
use App\Client;
use App\Company;
use App\Expense;
use App\NoOrder;
use App\Activity;
use App\Employee;
use App\Collection;
use App\ClientVisit;
use App\CustomField;
use App\PartyUpload;
use App\OrderDetails;
use App\ClientSetting;
use App\Traits\Upload;
use App\ModuleAttribute;
use App\BeatPlansDetails;
use App\PartyUploadFolder;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\ClientCategoryRateType;
use Illuminate\Http\UploadedFile;
use App\Repository\CustomCheckApi;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\File;

class ClientController extends Controller
{
    use Upload;
    private $company_id;
    private $auth_user;
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware(function ($request, $next) {
            $this->auth_user = Auth::user();
            $this->company_id = $this->auth_user->company_id;
            // $this->employee = $this->user->employee;
            // $this->employee_id = $this->employee->id;

            return $next($request);
        });
        $this->middleware('permission:party-create', ['only' => ['saveClient']]);
        $this->middleware('permission:party-view');
        $this->middleware('permission:party-update', ['only' => ['saveClient']]);
        $this->middleware('permission:party-delete', ['only' => ['destroy']]);
    }

    private function sendFalseResponse(){
    	// sending a data.
      // increment me until it works
      $spacer_size = 8; 
      // send 8kb of new line to browser (default), just make sure that this new line will not affect your code.
      // if you have output compression, make sure your data will reach >8KB.
      echo str_pad('', (1024 * $spacer_size), "\n"); 
      if(ob_get_level()) ob_end_clean(); // end output buffering
    }

    public function index(Request $request, $return = false)
    {
        $user = Auth::user();
        $companyID = $user->company_id;
        $employee = Employee::where('company_id', $companyID)->where('user_id', $user->id)->first();
        $employeeID = $employee->id;
        $handledClients = DB::table('handles')->where('employee_id', $employeeID)->pluck('client_id')->toArray();
        $acessibleClients = DB::table('accessibility_link')->where('employee_id', $employeeID)->pluck('client_id')->toArray();
        // $fetch_client_ids = array_unique(array_merge($handledClients, $acessibleClients));
        $outstanding_amt_calculation = DB::table('client_settings')->whereCompanyId($companyID)->first()->outstanding_amt_calculation;

        $clients = Client::select('clients.*', 'countries.name as country_name', 'states.name as state_name', 'cities.name as city_name', 'marketareas.name as market_area_name', 'beat_client.beat_id', 'business_types.business_name')
            ->leftJoin('countries', 'clients.country', '=', 'countries.id')
            ->leftJoin('states', 'clients.state', '=', 'states.id')
            ->leftJoin('cities', 'clients.city', '=', 'cities.id')
            ->leftJoin('marketareas', 'clients.market_area', '=', 'marketareas.id')
            ->leftJoin('beat_client', 'clients.id', '=', 'beat_client.client_id')
            ->leftJoin('business_types', 'clients.business_id', 'business_types.id')
            // ->whereIn('clients.id', $fetch_client_ids)
            ->where('clients.company_id', $companyID)->get();
        if (empty($clients)) {
            if ($return) {
                return array();
            } else {

                $this->sendEmptyResponse();
            }
        }

        $finalArray = array();
        $finalArrayWithAllowed = array();

        foreach ($clients as $key => $value) {
            if($outstanding_amt_calculation == 1){
              $value->outstanding_amount = $value->due_amount;
            }else{
              //getting outstanding amount
              $getOrderStatusFlag = ModuleAttribute::where('company_id', $companyID)->where('module_id', 1)->where('order_amt_flag', 1)->pluck('id')->toArray();
              if (!empty($getOrderStatusFlag)) {
                  $partyOrders = Order::select('id', 'delivery_status_id', 'grand_total')
                      ->where('client_id', $value->id)
                      ->orderBy('created_at', 'desc')
                      ->get();
                  $tot_order_amount = $partyOrders->whereIn('delivery_status_id', $getOrderStatusFlag)->sum('grand_total');
              } else {
                  $tot_order_amount = 0;
              }
              $collections = Collection::where('company_id', $companyID)->where('client_id', $value->id)
                  ->orderBy('created_at', 'desc')
                  ->get();
              $cheque_collection_amount = $collections->where('payment_method', 'Cheque')->where('payment_status', 'Cleared')->sum('payment_received');
              $cash_collection_amount = $collections->where('payment_method', 'Cash')->sum('payment_received');
              $bank_collection_amount = $collections->where('payment_method', 'Bank Transfer')->sum('payment_received');
              $tot_collection_amount = $cheque_collection_amount + $cash_collection_amount + $bank_collection_amount;
              $value->outstanding_amount = number_format(($value->opening_balance + $tot_order_amount - $tot_collection_amount), 2);
            }
            // Ending Outstanding amount

            $handles = getClientHandlingData($companyID, $value->id, true);
            $value->employee_ids = json_encode($handles);
            $canHandle = in_array($employeeID, $handles);
            $value->can_handle = $canHandle;
            if ($value->superior) {
                $superior = Client::where('id', $value->superior)->first();
                $value->superior_name = $superior ? $superior->company_name : null;
            } else {
                $value->superior_name = null;
            }
            $access = getClientAccessibleData($companyID, $value->id, true);
            $value->employee_access_ids = json_encode($access);
            $canAccess = in_array($employeeID, $access);
            $value->can_access = $canAccess;

            $party_meta = DB::table('party_meta')->where('client_id', $value->id)->first();
            if ($party_meta) {
                $value->custom_fields = $party_meta->cf_value;
            }

            $last_order_date =  Order::where('client_id', $value->id)->orderBy('created_at', 'desc')->first();
            $value->last_order_date = $last_order_date ? $last_order_date->order_date : null;
            $value->credit_limit = is_null($value->credit_limit) ? "0": $value->credit_limit;
            $value->appliedcategoryrates = $value->appliedcategoryrates()->get(['id', 'category_id', 'name'])->pluck('id')->toJson();
            
            

            array_push($finalArray, $value);
            array_push($finalArrayWithAllowed, $value);
            /**
             * Send False response to server 
             * to avoid cloudfare error
             * First response after processing 1000 parties
             * and then 2000 party 
             * and then 3000 party 
             * and then 4000 party 
             * and then 5000 party
             */
            if($key == 1000){
            	$this->sendFalseResponse();
            }elseif($key == 2000){
            	$this->sendFalseResponse();
            }elseif($key == 3000){
            	$this->sendFalseResponse();
            }elseif($key == 4000){
            	$this->sendFalseResponse();
            }elseif($key == 5000){
            	$this->sendFalseResponse();
            }
        }

        $response = array("status" => true, "message" => "Success", "data" => $finalArrayWithAllowed);
        if ($return) {
            return $finalArray;
        } else {
            return response($response);
        }
    }

    public function saveClient(Request $request)
    {
        $user = Auth::user();
        $companyID = $user->company_id;
        $employee = Employee::where('company_id', $companyID)->where('user_id', $user->id)->first();
        $employeeID = $employee->id;
        $company = Company::where('id', $companyID)->first();
        $uniqueID = $request->unique_id;
        $clientID = $request->client_id;
        // if($clientID){
        //     $client = Client::where('company_id',$companyID)->where('id',$clientID)->first();
        if ($clientID) {
            $client = Client::where('company_id', $companyID)->where('id', $clientID)->orWhere('unique_id', $uniqueID)->first();
            DB::table('client_category_rate_types')->where('client_id', $clientID)->delete();
        } else {
            $client = new Client();
            $clientSettings = ClientSetting::where('company_id', $companyID)->select('credit_days')->first();
            $client->credit_days = $clientSettings->credit_days;
        }
        $beatID = $request->beat_id;
        if ($request->has('image')) {
            $image = $request->image;
            if ($request->image) {
                $tempImageName = $this->getImageName();
                $tempImageDir = $this->getImagePath($companyID, 'clients');
                $tempImagePath = "/storage/app/public/" . $tempImageDir . "/" . $tempImageName;
                $decodedData = base64_decode($image);
                $path = \Storage::disk('public')->put($tempImageDir . '/' . $tempImageName, $decodedData);
            } else {
                $tempImageName = null;
                $tempImagePath = null;
            }
        }

        $client->company_id = $companyID;
        $client->unique_id = $uniqueID;
        $client->company_name = trim($request->company_name);
        $client->client_type = isset($request->client_type) ? $request->client_type : 0;
        $client->superior = $request->superior;
        $client->market_area = $request->marketarea;
        $client->name = $request->name;
        $client->client_code = $request->client_code;
        $client->website = $request->website;
        $client->email = $request->email;

        $client->country = $request->country;
        $client->state = $request->state;
        $citySel = $request->city;
        $client->city = $citySel;
        if ($request->image) {
            $client->image = $tempImageName;
            $client->image_path = $tempImagePath;
        }
        $client->address_1 = $request->address_1;
        $client->address_2 = $request->address_2;
        $client->business_id = $request->business_id;

        $client->pin = $request->pin;
        $client->phonecode = $request->phonecode;
        $client->phone = $request->phone;
        $client->mobile = $request->mobile;
        $client->pan = $request->pan;
        $client->about = $request->about;
        $client->location = $request->location;
        $client->latitude = $request->latitude;
        $client->longitude = $request->longitude;
        $client->status = $request->status;
        $client->created_by = $employeeID;
        $client->save();

        if ($client) {

            if (!empty($beatID)) {
                if (!empty($clientID)) {
                    $prevBeat = DB::table('beat_client')->where('client_id', $client->id)->value('beat_id');
                    DB::table('beat_client')->where('client_id', $client->id)->delete();
                }
                DB::table('beat_client')->insert([
                    'client_id' => $client->id,
                    'beat_id' => $beatID,
                ]);
                if (!empty($clientID)) {
                    if ((int) $beatID != (int) $prevBeat) {
                        $today = date('Y-m-d');
                        $clientBeatPlans = BeatPlansDetails::where('plandate', '>=', $today)
                            ->get();
                        if ($clientBeatPlans->count() > 0) {
                            foreach ($clientBeatPlans as $clientBeatPlan) {
                                $beatPlanBeats = explode(',', $clientBeatPlan->beat_id);
                                foreach ($beatPlanBeats as $beatPlanBeat) {
                                    if ((int) $beatPlanBeat == (int) $prevBeat) {
                                        $_beatClients = json_decode($clientBeatPlan->beat_clients, true);
                                        $_beats = explode(',', $clientBeatPlan->beat_id);
                                        if (is_array($_beatClients) && !empty($_beatClients)) {
                                            if (count($_beatClients[$prevBeat]) == 1) {
                                                if (array_key_exists($prevBeat, $_beatClients)) {
                                                    $_beatClients[$beatID][] = $client->id;
                                                } else {
                                                    $_beatClients[$beatID] = $_beatClients[$prevBeat];
                                                }
                                                unset($_beatClients[$prevBeat]);
                                            } else {
                                                if (array_key_exists($beatID, $_beatClients)) {
                                                    $_beatClients[$beatID][] = $client->id;
                                                } else {
                                                    $_beatClients[$beatID][] = $client->id;
                                                }
                                                $_ind = array_search((int) $client->id, $_beatClients[$prevBeat]);
                                                unset($_beatClients[$prevBeat][$_ind]);
                                            }
                                            $newBeatIDs = array();
                                            $newClientIDs = array();
                                            foreach ($_beatClients as $key => $beatClient) {
                                                array_push($newBeatIDs, $key);
                                                foreach ($beatClient as $btClient) {
                                                    array_push($newClientIDs, $btClient);
                                                }
                                            }
                                            $clientBeatPlan->beat_id = implode(',', $newBeatIDs);
                                            $clientBeatPlan->client_id = implode(',', $newClientIDs);
                                            $clientBeatPlan->beat_clients = json_encode($_beatClients);
                                            $clientBeatPlan->save();
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                if (isset($citySel)) {
                    if ($citySel != 0) {
                        $beatInstance = Beat::where('id', $beatID)->first();
                        $getBeatClients = DB::table('beat_client')->where('beat_id', $beatID)->pluck('client_id')->toArray();
                        $clientsBeats = Client::where('company_id', $companyID)->whereIn('id', $getBeatClients)->whereNotNull('city')->distinct('city')->pluck('city')->toArray();
                        if ($beatInstance && sizeof($clientsBeats) == 1) {
                            if ($citySel == $clientsBeats[0]) {
                                if (!isset($beatInstance->city_id)) {
                                    $beatInstance->city_id = $citySel;
                                    $beatInstance->update();
                                }
                            }
                        }
                    }
                }
            }

            $msg = "";
            $savedClient = $client;
            $savedClient["id"] = $client->id;

            if ($client->wasRecentlyCreated) {
                $handleData = array(
                    "company_id" => $companyID,
                    "employee_id" => $employeeID,
                    "client_id" => $client->id,
                    "map_type" => "2",
                );

                $handle = DB::table('handles')->where(
                    array(
                        array("company_id", $companyID),
                        array("employee_id", $employeeID),
                        array("client_id", $client->id),
                    )
                )->get();

                if (!empty($handle)) {
                    $handleSaved = DB::table('handles')->insertGetId($handleData);
                }
                $superiors = $this->getAllEmployeeSuperior($companyID, $employeeID, $getSuperiors = []);
                $employeeInstance = Employee::where('company_id', $companyID)->where('is_admin', 1)->pluck('id')->toArray();
                if (!empty($employeeInstance)) {
                    foreach ($employeeInstance as $adminId) {
                        if (!in_array($adminId, $superiors)) {
                            array_push($superiors, $adminId);
                        }
                    }
                }
                if (!empty($superiors)) {
                    $supHandle = DB::table('handles')->where("company_id", $companyID)->where("client_id", $client->id)->whereIn("employee_id", $superiors)->pluck('employee_id')->toArray();
                    foreach ($superiors as $superior) {

                        if (!in_array($superior, $supHandle)) {
                            $supHandleData = array(
                                "company_id" => $companyID,
                                "employee_id" => $superior,
                                "client_id" => $client->id,
                                "map_type" => "2",
                            );
                            $supHandleSaved = DB::table('handles')->insertGetId($supHandleData);
                        }
                    }
                }
                $msg = "Added Party";
                $action = "add";
            } else {

                $msg = "Updated Party";
                $action = "update";

            }

            $clientHandlingData = getClientHandlingData($companyID, $client->id, true);
            $encodedHandlingData = json_encode($clientHandlingData);
            $savedClient["employee_ids"] = $encodedHandlingData;
            $savedClient["beat_id"] = $beatID;
            if ($request->image) {
                $savedClient['image'] = $tempImageName;
                $savedClient['image_path'] = $tempImagePath;
            }

            if ($request->custom_fields) {

                $custom_fields = json_decode($request->custom_fields);
                $reqCustomFields = [];
                foreach ($custom_fields as $cf_key => $cf_value) {
                    $reqCustomFields[$cf_key] = $cf_value;
                }

                $customRequest = new Request($reqCustomFields);

                $party_meta = DB::table('party_meta')->where('client_id', $client->id)->first();
                if ($party_meta) {
                    $collec = CustomField::where('company_id', $companyID)->where('for', '=', 'Party')->withTrashed()->get();
                    $imagesFilesCollections = $collec->where('type', 'Multiple Images');
                    foreach ($imagesFilesCollections as $imageCollection) {
                        if (!$customRequest[$imageCollection->slug]) {
                            $customRequest->merge([
                                $imageCollection->slug => true,
                            ]);
                        }
                    }

                    $imagesFilesCollections = $collec->where('type', 'File');
                    foreach ($imagesFilesCollections as $imageCollection) {
                        if (!$customRequest[$imageCollection->slug]) {
                            $customRequest->merge([
                                $imageCollection->slug => true,
                            ]);
                        }
                    }

                    $array_field = (new CustomCheckApi($collec, $customRequest, $party_meta, $companyID))->check();

                    $decodedArray = json_decode($party_meta->cf_value);
                    if (isset($array_field) && !empty($array_field)):
                        DB::table('party_meta')->where('client_id', $client->id)->update(['cf_value' => json_encode($array_field)]);
                    endif;
                } else {
                    $collec = CustomField::where('for', '=', 'Party')->where('add', '=', 'yes')->get();

                    if ($collec->count()):
                        $array_field = (new CustomCheckApi($collec, $customRequest, null, $companyID))->check();
                    endif;

                    if (isset($array_field) && !empty($array_field)):
                        DB::table('party_meta')->insert([
                            'client_id' => $client->id,
                            'cf_value' => json_encode($array_field),
                        ]);
                    endif;
                }

                $party_meta = DB::table('party_meta')->where('client_id', $client->id)->first();
                if ($party_meta) {
                    $savedClient['custom_fields'] = $party_meta->cf_value;
                }

            }

            if($request->has('category_rates')){
              $category_rates = json_decode($request->category_rates);
              if(count($category_rates) > 0){
                $records = array();
                foreach($category_rates as $category_rate){
                  array_push($records, array('client_id' => $client->id, 'category_rate_type_id' => $category_rate));
                }
                ClientCategoryRateType::insert($records);
              }
            }

            $savedClient['category_rate_ids'] = $client->appliedcategoryrates()->get(['id', 'category_id', 'name'])->pluck('id')->toJson();

            $nSaved = saveAdminNotification($companyID, $employeeID, date("Y-m-d H:i:s"), $msg, "client", $savedClient);
            $sent = sendPushNotification_(getFBIDs($companyID), 10, null, array("data_type" => "client", "employee_id" => $employeeID, "client" => $savedClient, "action" => $action));

        }

        $response = array("status" => true, "message" => "successfully saved", "data" => $savedClient);
        return response($response);

    }

    public function fetchClientAccountingDetails(Request $request){
      $clientId = $request->client_id;
      $orderId = $request->order_id;

      return $this->getOutstandingAmount($clientId, $orderId);
    }

    private function getOutstandingAmount($clientId, $orderId = null)
    {
        try {
            $client = Client::find($clientId);
            $outstanding_amt_calculation = DB::table('client_settings')->whereCompanyId($client->company_id)->first()->outstanding_amt_calculation;
          if($outstanding_amt_calculation == 1){
            $outstading_amount = $client->due_amount;
          }else{
            $orders = $client->orders;
            $getOrderStatusFlag = ModuleAttribute::where('company_id', $client->company_id)->where('module_id', 1)->where('order_amt_flag', 1)->pluck('id')->toArray();
            if (!empty($getOrderStatusFlag)) {
                if ($orderId) {
                    $tot_order_amount = $orders->whereIn('delivery_status_id', $getOrderStatusFlag)->where('id', '<>', $orderId)->sum('grand_total');
                } else {
                    $tot_order_amount = $orders->whereIn('delivery_status_id', $getOrderStatusFlag)->sum('grand_total');
                }
            } else {
                $tot_order_amount = 0;
            }

            $collections = $client->collections;
            if ($collections) {
                $cheque_collection_amount = $collections->where('payment_method', 'Cheque')->where('payment_status', 'Cleared')->sum('payment_received');
                $cash_collection_amount = $collections->where('payment_method', 'Cash')->sum('payment_received');
                $bank_collection_amount = $collections->where('payment_method', 'Bank Transfer')->sum('payment_received');
            } else {
                $cheque_collection_amount = 0;
                $cash_collection_amount = 0;
                $bank_collection_amount = 0;
            }
            $tot_collection_amount = $cheque_collection_amount + $cash_collection_amount + $bank_collection_amount;

            $outstading_amount = $client->opening_balance + $tot_order_amount - $tot_collection_amount;

          }
            $last_order =  Order::where('client_id', $clientId)->orderBy('created_at', 'desc')->first();
            $due_or_overdue_text = "Due Amount";
            if($last_order){
              $last_order_date = $last_order->order_date;
              $numOfDays = $client->credit_days > 1 ? "+".$client->credit_days." days": "+".$client->credit_days." day";
              $lastCreditDate = date('Y-m-d', strtotime($numOfDays, strtotime($last_order_date)));
              if($lastCreditDate < date('Y-m-d')) $due_or_overdue_text = "OverDue Amount";
            }

            return response()->json([
              'status'=> 200,
              'credit_limit' => $client->credit_limit,
              'outstanding_amount' => strval($outstading_amount),
              'due_overdue' => $due_or_overdue_text
            ]);
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            return response()->json([
              'message' => $e->getMessage()
            ]);
        }
    }

    public function syncClients()
    {

        $postData = $this->getJsonRequest();
        $clientArray = array();
        $orderArray = array();
        $noorderArray = array();
        $collectionArray = array();
        $meetingArray = array();
        $expenseArray = array();
        $taskArray = array();
        $activityArray = array();
        $visitArray = array();
        $stockArray = array();
        try {
            $clientArray = $this->manageUnsyncedClients($postData, true);

            foreach ($clientArray as $key => $client) { // loop for each synced clients for syncing other child data

                try {
                    $syncedOrders = $this->manageUnsyncedOrder($postData, true, $client);
                    foreach ($syncedOrders as $key => $value) {
                        array_push($orderArray, $value);
                    }
                } catch (\Exception $e) {
                  Log::error( array("Sync Client Order Error Mess>>Code>>Line", $e->getMessage(), $e->getCode(), $e->getLine()) );
                }
                try {
                    $syncedNoOrders = $this->manageUnsyncedNoOrder($postData, true, $client);
                    foreach ($syncedNoOrders as $key => $value) {
                        array_push($noorderArray, $value);
                    }
                } catch (\Exception $e) {
                  Log::error( array("Sync Client NoOrder Error Mess>>Code>>Line", $e->getMessage(), $e->getCode(), $e->getLine()) );
                }

                try {
                    $syncedCollections = $this->manageUnsyncedCollection($postData, true, $client);
                    foreach ($syncedCollections as $key => $value) {
                        array_push($collectionArray, $value);
                    }
                } catch (\Exception $e) {
                  Log::error( array("Sync Client Collection Error Mess>>Code>>Line", $e->getMessage(), $e->getCode(), $e->getLine()) );
                }

                try {
                    $syncedMeetings = $this->manageUnsyncedMeeting($postData, true, $client);
                    foreach ($syncedMeetings as $key => $value) {
                        array_push($meetingArray, $value);
                    }

                } catch (\Exception $e) {
                  Log::error( array("Sync Client Note Error Mess>>Code>>Line", $e->getMessage(), $e->getCode(), $e->getLine()) );
                }

                try {
                    $syncedExpenses = $this->manageUnsyncedExpense($postData, true, $client);
                    foreach ($syncedExpenses as $key => $value) {
                        array_push($expenseArray, $value);
                    }
                } catch (\Exception $e) {
                  Log::error( array("Sync Client Expense Error Mess>>Code>>Line", $e->getMessage(), $e->getCode(), $e->getLine()) );
                }

                try {
                    $syncedTasks = $this->manageUnsyncedTask($postData, true, $client);
                    foreach ($syncedTasks as $key => $value) {
                        array_push($taskArray, $value);
                    }
                } catch (\Exception $e) {
                  Log::error( array("Sync Client Task Error Mess>>Code>>Line", $e->getMessage(), $e->getCode(), $e->getLine()) );
                }

                try {
                    $syncedActivity = $this->manageUnsyncedActivity($postData, true, $client);
                    foreach ($syncedActivity as $key => $value) {
                        array_push($activityArray, $value);
                    }
                } catch (\Exception $e) {
                  Log::error( array("Sync Client Activity Error Mess>>Code>>Line", $e->getMessage(), $e->getCode(), $e->getLine()) );
                }

                try {
                    $syncedVisit = $this->manageUnsyncedVisit($postData, true, $client);
                    foreach ($syncedVisit as $key => $value) {
                        array_push($visitArray, $value);
                    }
                } catch (\Exception $e) {
                  Log::error( array("Sync Client Visit Error Mess>>Code>>Line", $e->getMessage(), $e->getCode(), $e->getLine()) );
                }

                try {
                    $syncedStock = $this->manageUnsyncedStock($postData, true, $client);
                    foreach ($syncedStock as $key => $value) {
                        array_push($stockArray, $value);
                    }
                } catch (\Exception $e) {
                  Log::error( array("Sync Client Stock Error Mess>>Code>>Line", $e->getMessage(), $e->getCode(), $e->getLine()) );
                }
            }
        } catch (\Exception $e) {
            Log::error( array("Sync Client Error Mess>>Code>>Line", $e->getMessage(), $e->getCode(), $e->getLine()) );
        }

        $response = array(
            "status" => true,
            "message" => "success",
            "data" => $clientArray,
            "orders" => $orderArray,
            "no_orders" => $noorderArray,
            "collections" => $collectionArray,
            "meetings" => $meetingArray,
            "expenses" => $expenseArray,
            "tasks" => $taskArray,
            "activities" => $activityArray,
            "visits" => $visitArray,
            "stocks" => $stockArray,
        );

        $this->sendResponse($response);
    }

    private function manageUnsyncedExpense($postData, $returnItems = false, $client = null)
    {
        $rawData = $this->getArrayValue($postData, "nonsynced_expense");
        $user = Auth::user();
        $companyID = $user->company_id;
        $employee = Employee::where('company_id', $companyID)->where('user_id', $user->id)->first();
        $employeeID = $employee->id;

        //$clientID   = $this->getArrayValue($postData,"client_id");

        if (empty($rawData)) {
            return $returnItems ? array() : false;
        }

        $data = json_decode($rawData, true);
        $arraySyncedData = array();
        /*prepare data for saving*/
        foreach ($data as $key => $expense) {
            $images_ids = [];
            $images_names = [];
            $images_paths = [];

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
                'expense_date' => $this->getArrayValue($expense, "expense_date"),
                'expense_type_id' => $this->getArrayValue($expense, "expense_type_id"),
                'approved_by' => $this->getArrayValue($expense, "approved_by"),
                'remark' => $this->getArrayValue($expense, "remark"),
                'status' => $this->getArrayValue($expense, "status"),
                'created_at' => $createdAt,
                'updated_at' => $this->getArrayValue($expense, "updated_at"),
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
                // $expenseData["images"] = $tempImageNames;
                // $expenseData["image_paths"] = $tempImagePaths;

                $finalImages = DB::table('images')->where('type', 'expense')->where('type_id', $savedID)->whereNull('deleted_at')->get();
                foreach ($finalImages as $finalImage) {
                    array_push($images_ids, $finalImage->id);
                    array_push($images_names, $finalImage->image);
                    array_push($images_paths, $finalImage->image_path);
                }

                $expenseData["image_ids"] = json_encode($images_ids);
                if ($expenseData["image_ids"] == '[]') {
                    $expenseData["image_ids"] = null;
                }
                $expenseData["images"] = json_encode($images_names);
                if ($expenseData["images"] == '[]') {
                    $expenseData["images"] = null;
                }
                $expenseData["image_paths"] = json_encode($images_paths);
                if ($expenseData["image_paths"] == '[]') {
                    $expenseData["image_paths"] = null;
                }

                array_push($arraySyncedData, $expenseData);
                $save = saveAdminNotification($companyID, $employeeID, date("Y-m-d H:i:s"), "Added Expense", "expense", $expenseData);
            }

        }

        return $returnItems ? $arraySyncedData : false;
    }

    public function manageUnsyncedVisit($postData, $returnItems = false, $client = null)
    {
        $rawData = $this->getArrayValue($postData, "nonsynced_visits");
        $user = Auth::user();
        $companyID = $user->company_id;
        $employee = Employee::where('company_id', $companyID)->where('user_id', $user->id)->first();
        $employeeID = $employee->id;

        if (empty($rawData)) {
            return $returnItems ? array() : false;
        }

        $data = json_decode($rawData, true);
        $arraySyncedData = array();

        foreach ($data as $key => $visit_data) {
            try {
                $visitClientID = $this->getArrayValue($visit_data, "client_id");
                $visitClientUniqueID = $this->getArrayValue($visit_data, "client_unique_id");

                if (empty($visitClientID)) {
                    if ($returnItems && !empty($client)) {
                        $tempClientUniqueID = $client->unique_id;
                        $tempClientID = $client->id;
                        if ($visitClientUniqueID == $tempClientUniqueID) {
                            $visitClientID = $tempClientID;
                        } else {
                            continue;
                        }
                    }
                }

                DB::beginTransaction();
                $unique_id = $visit_data['unique_id'];
                $companyId = $companyID;
                $client_id = $visitClientID;
                $employee_id = $employeeID;
                $date = $visit_data['date'];
                $start_time = $visit_data['start_time'];
                $end_time = $visit_data['end_time'];
                $visit_purpose = $visit_data['visit_purpose'];
                $comments = $visit_data['comments'];
                $images = json_decode($visit_data['images'], true);

                $data = array(
                    'unique_id' => $unique_id,
                    'company_id' => $companyId,
                    'client_id' => $client_id,
                    'employee_id' => $employee_id,
                    'date' => $date,
                    'start_time' => $start_time,
                    'end_time' => $end_time,
                    'visit_purpose_id' => $visit_purpose,
                    'comments' => $comments,
                );

                if (!empty($unique_id)) {
                    $unique_to_unique_id = ClientVisit::whereCompanyId($companyId)->where('unique_id', $unique_id)->first();
                    if (!$unique_to_unique_id) {
                        $object_instance = ClientVisit::create($data);
                    } else {
                        $object_instance = $unique_to_unique_id;
                    }

                } else {
                    $object_instance = ClientVisit::create($data);
                }

                $image_ids = array();
                $images_names = array();
                $images_paths = array();

                if (!empty($images) && !$unique_to_unique_id) {
                    foreach ($images as $key => $value) {
                        $tempImageName = time() . $key . substr(uniqid(), 3, 6);
                        $tempImageDir = $this->getImagePath($companyId, 'client_visits', $tempImageName);
                        $tempImagePath = "/storage/app/public/" . $tempImageDir . "/" . $tempImageName;
                        $put = Storage::disk('public')->put($tempImageDir . '/' . $tempImageName, base64_decode($value));

                        $insert_image = Image::create([
                            "type" => "client_visits",
                            "type_id" => $object_instance->id,
                            "company_id" => $companyId,
                            "employee_id" => $employee_id,
                            "image" => $tempImageName,
                            "image_path" => $tempImagePath,
                        ]);

                        array_push($image_ids, $insert_image->id);
                        array_push($images_names, $insert_image->image);
                        array_push($images_paths, $insert_image->image_path);

                    }
                } elseif (!empty($images) && $unique_to_unique_id) {
                    $insert_image_data = Image::whereCompanyId($companyId)->whereType("client_visits")->whereTypeId($unique_to_unique_id->id)->get(['id', 'image', 'image_path']);
                    if ($insert_image_data->first()) {
                        foreach ($insert_image_data as $image_data) {
                            array_push($image_ids, $image_data->id);
                            array_push($images_names, $image_data->image);
                            array_push($images_paths, $image_data->image_path);
                        }
                    }
                }
                DB::commit();
                $object_instance->image_ids = json_encode($image_ids);
                $object_instance->images_names = json_encode($images_names);
                $object_instance->images_paths = json_encode($images_paths);

                array_push($arraySyncedData, $object_instance);
            } catch (\Exception $e) {
                Log::error($e->getMessage());
                Log::info($visit_data);
                array_push($unsynced_data, $visit_data);
            }
        }

        return $returnItems ? $arraySyncedData : false;
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

    private function manageUnsyncedMeeting($postData, $returnItems = false, $client = null)
    {

        $rawData = $this->getArrayValue($postData, "nonsynced_meeting");
        $user = Auth::user();
        $companyID = $user->company_id;
        $employee = Employee::where('company_id', $companyID)->where('user_id', $user->id)->first();
        $employeeID = $employee->id;

        if (empty($rawData)) {
            return $returnItems ? array() : false;
        }
        $data = json_decode($rawData, true);
        $arraySyncedData = array();

        foreach ($data as $key => $meeting) {
            $images_ids = [];
            $images_names = [];
            $images_paths = [];

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
            $images = $this->getArrayValue($meeting, "images");
            $imagePaths = $this->getArrayValue($meeting, "image_paths");
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
                ->where('company_id', $companyID)
                ->where('employee_id', $employeeID)
                ->where('unique_id', $noteUniqueID)
                ->get()
                ->first();

            if (!empty($alreadyAddedNote)) {
                // Do something

                // $arrayAlreadyAddedCol = $alreadyAddedCol->toArray();

                // array_push($arraySyncedData, $arrayAlreadyAddedCol);
                // saveAdminNotification($companyID, $employeeID, date("Y-m-d H:i:s"), "Changed Cheque Status to ".$paymentStatus, "cheque", $alreadyAddedCol);
            } else {
                $savedID = DB::table('meetings')->insertGetId($tempMeetingArray);
                if (!empty($savedID)) {

                    $imageArray = array();
                    $tempImageNames = array();
                    $tempImagePaths = array();

                    //saving images
                    if (!empty($imagePaths)) {
                        if (!empty($images)) {
                            $jsonDecoded = json_decode($images, true);

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
                            $finalImages = DB::table('images')->where('type', 'notes')->where('type_id', $savedID)->whereNull('deleted_at')->get();
                            foreach ($finalImages as $finalImage) {
                                array_push($images_ids, $finalImage->id);
                                array_push($images_names, $finalImage->image);
                                array_push($images_paths, $finalImage->image_path);
                            }

                        }
                    }
                    $meetingData = $tempMeetingArray;
                    $meetingData["id"] = $savedID;
                    // $meetingData["images"] = $tempImageNames;
                    // $meetingData["image_paths"] = $tempImagePaths;

                    $meetingData["image_ids"] = json_encode($images_ids);
                    if ($meetingData["image_ids"] == '[]') {
                        $meetingData["image_ids"] = null;
                    }
                    $meetingData["images"] = json_encode($images_names);
                    if ($meetingData["images"] == '[]') {
                        $meetingData["images"] = null;
                    }
                    $meetingData["image_paths"] = json_encode($images_paths);
                    if ($meetingData["image_paths"] == '[]') {
                        $meetingData["image_paths"] = null;
                    }

                    array_push($arraySyncedData, $meetingData);
                    saveAdminNotification($companyID, $employeeID, date("Y-m-d H:i:s"), "Added Note", "notes", $meetingData);
                }
            }

        }
        return $returnItems ? $arraySyncedData : false;
    }

    private function manageUnsyncedCollection($postData, $returnItems = false, $client = null)
    {

        $rawData = $this->getArrayValue($postData, "nonsynced_collection");
        $user = Auth::user();
        $companyID = $user->company_id;
        $employee = Employee::where('company_id', $companyID)->where('user_id', $user->id)->first();
        $employeeID = $employee->id;

        if (empty($rawData)) {
            return $returnItems ? array() : false;
        }

        $data = json_decode($rawData, true);
        $arraySyncedData = array();
        foreach ($data as $key => $col) {
            $images_ids = [];
            $images_names = [];
            $images_paths = [];

            $colClientID = $this->getArrayValue($col, "client_id");
            $colClientUniqueID = $this->getArrayValue($col, "client_unique_id");

            if (empty($colClientID)) {
                if ($returnItems && !empty($client)) {
                    $tempClientUniqueID = $client->unique_id;
                    $tempClientID = $client->id;
                    //Log::info('info', array("mData"=>print_r($colClientUniqueID.",".$tempClientUniqueID.",".$tempClientID,true)));
                    if ($colClientUniqueID == $tempClientUniqueID) {
                        $colClientID = $tempClientID;
                    } else {
                        continue;
                    }
                }
            }
            $payment_method = $this->getArrayValue($col, "payment_method");
            if ($payment_method == 'Cash' || $payment_method == 'Bank Transfer') {
                $payment_status = 'Cleared';
            } else {
                $payment_status = $this->getArrayValue($col, "payment_status", "Pending");
            }
            $colID = $this->getArrayValue($col, "collection_id");
            $colUniqueID = $this->getArrayValue($col, "unique_id");
            $images = $this->getArrayValue($col, "images");
            $imagePaths = $this->getArrayValue($col, "image_paths");
            $createdAt = $this->getArrayvalue($col, "created_at");
            $paymentStatus = $this->getArrayvalue($col, "payment_status", "Pending");
            $paymentStatusNote = $this->getArrayvalue($col, "payment_status_note", "N/A");

            $colTempArray["id"] = $colID;
            $colTempArray["unique_id"] = $colUniqueID;
            $colTempArray["company_id"] = $companyID;
            $colTempArray["employee_id"] = $employeeID;
            $colTempArray["client_id"] = $colClientID;
            $colTempArray["payment_received"] = $this->getArrayvalue($col, "payment_received");
            //$colTempArray["payment_method"] = $this->getArrayvalue($col, "payment_method");
            // $colTempArray["payment_status"] = $paymentStatus;
            $colTempArray["payment_method"] = $payment_method;
            $colTempArray["payment_status"] = $payment_status;
            $colTempArray["bank_id"] = $this->getArrayvalue($col, "bank_id");
            $colTempArray["cheque_no"] = $this->getArrayvalue($col, "cheque_no");
            $colTempArray["cheque_date"] = $this->getArrayvalue($col, "cheque_date");
            $colTempArray["due_payment"] = $this->getArrayValue($col, "due_payment", 0);
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
                saveAdminNotification($companyID, $employeeID, date("Y-m-d H:i:s"), "Changed Cheque Status to " . $paymentStatus, "cheque", $alreadyAddedCol);

            } else {

                $savedID = DB::table('collections')->insertGetId($colTempArray);

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

                            $finalImages = DB::table('images')->where('type', 'collection')->where('type_id', $savedID)->whereNull('deleted_at')->get();
                            foreach ($finalImages as $finalImage) {
                                array_push($images_ids, $finalImage->id);
                                array_push($images_names, $finalImage->image);
                                array_push($images_paths, $finalImage->image_path);
                            }
                        }
                    }

                    $colData = $colTempArray;
                    $colData["id"] = $savedID;
                    // $colData["images"] = $tempImageNames;
                    // $colData["image_paths"] = $tempImagePaths;

                    $colData["image_ids"] = json_encode($images_ids);
                    if ($colData["image_ids"] == '[]') {
                        $colData["image_ids"] = null;
                    }
                    $colData["images"] = json_encode($images_names);
                    if ($colData["images"] == '[]') {
                        $colData["images"] = null;
                    }
                    $colData["image_paths"] = json_encode($images_paths);
                    if ($colData["image_paths"] == '[]') {
                        $colData["image_paths"] = null;
                    }

                    array_push($arraySyncedData, $colData);
                    saveAdminNotification($companyID, $employeeID, date("Y-m-d H:i:s"), "Added Collection", "collection", $colData);
                }
            }

        } //end foreach
        //Log::info('info', array("arraySyncedData"=>print_r($arraySyncedData,true)));

        return $returnItems ? $arraySyncedData : false;
    }

    private function manageUnsyncedClients($postData, $returnItems = false)
    {
        $rawData = $this->getArrayValue($postData, "nonsynced_data");
        $user = Auth::user();
        $companyID = $user->company_id;
        $employee = Employee::where('company_id', $companyID)->where('user_id', $user->id)->first();
        $employeeID = $employee->id;
        $employeeName = $this->getArrayValue($postData, "employee_name");

        if (empty($rawData)) {
            return false;
        }

        $data = json_decode($rawData, true);
        // Log::info('info', array("raw clients"=>print_r($data,true)));

        $arraySyncedData = array();
        foreach ($data as $key => $value) {
            $companyName = $this->getArrayValue($value, "company_name");
            $uniqueID = $this->getArrayValue($value, "unique_id");
            if (array_key_exists("image", $value)) {
                $image = $this->getArrayValue($value, "image");
                if (!empty($image)) {
                    $tempImageName = $this->getImageName();
                    $tempImageDir = $this->getImagePath($companyID, 'clients');
                    $tempImagePath = "/storage/app/public/" . $tempImageDir . "/" . $tempImageName;
                    $decodedData = base64_decode($image);
                    $path = \Storage::disk('public')->put($tempImageDir . '/' . $tempImageName, $decodedData);
                } else {
                    $tempImageName = null;
                    $tempImagePath = null;
                }
            } else {
                $tempImageName = null;
                $tempImagePath = null;
            }

            $client = Client::where('company_id', $companyID)->where('unique_id', $uniqueID)->first();
            if ($client) {
                $credit_days = $client->credit_days;
                $action = "update";
                DB::table('client_category_rate_types')->where('client_id', $client->id)->delete();

            } else {
                $clientSettings = ClientSetting::where('company_id', $companyID)->select('credit_days')->first();
                $credit_days = $clientSettings->credit_days;
                $action = "add";
            }
            // Log::info('info', array("message"=>print_r($credit_days,true)));

            //$client = DB::table('client')->where('unique_id', $uniqueID)->first();
            $client_type = $this->getArrayValue($value, "client_type");
            if ($client_type == null) {
                $client_type = 0;
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
                'image' => $tempImageName,
                'image_path' => $tempImagePath,
                'address_1' => $this->getArrayValue($value, "address_1"),
                'address_2' => $this->getArrayValue($value, "address_2"),
                'business_id' => $this->getArrayValue($value, "business_id"),
                'credit_days' => $credit_days,
                'pin' => $this->getArrayValue($value, "pin"),
                'phonecode' => $this->getArrayValue($value, "phonecode"),
                'phone' => $this->getArrayValue($value, "phone"),
                'mobile' => $this->getArrayValue($value, "mobile"),
                'pan' => $this->getArrayValue($value, "pan"),
                'about' => $this->getArrayValue($value, "about"),
                'location' => $this->getArrayValue($value, "location"),
                'latitude' => $this->getArrayValue($value, "latitude"),
                'longitude' => $this->getArrayValue($value, "longitude"),
                'status' => $this->getArrayValue($value, "status"),
                'created_by' => $employeeID,
            );

            $client = Client::updateOrCreate(
                [
                    "unique_id" => $uniqueID,
                ],
                $clientData
            );
            $wasRecentlyCreated = $client->wasRecentlyCreated;
            $wasChanged = $client->wasChanged();
            $isDirty = $client->isDirty();

            if ($wasRecentlyCreated || $wasChanged || $client->exists) {
                $client['unique_id'] = $uniqueID;
                array_push($arraySyncedData, $client);

                if ($client->wasRecentlyCreated) {
                    $handleData = array(
                        "company_id" => $companyID,
                        "employee_id" => $employeeID,
                        "client_id" => $client->id,
                        "map_type" => "2",
                    );
                    //Log::info('info', array("handleData"=>print_r($handleData,true)));

                    $handle = DB::table('handles')->where(
                        array(
                            array("company_id", $companyID),
                            array("employee_id", $employeeID),
                            array("client_id", $client->id),
                        )
                    )
                        ->get();
                    if (!empty($handle)) {
                        $handleSaved = DB::table('handles')->insertGetId($handleData);
                    }

                    $superiors = $this->getAllEmployeeSuperior($companyID, $employeeID, $getSuperiors = []);
                    $employeeInstance = Employee::where('company_id', $companyID)->where('is_admin', 1)->pluck('id')->toArray();
                    if (!empty($employeeInstance)) {
                        foreach ($employeeInstance as $adminId) {
                            if (!in_array($adminId, $superiors)) {
                                array_push($superiors, $adminId);
                            }
                        }
                    }
                    $supHandle = DB::table('handles')->where("company_id", $companyID)->whereIn("employee_id", $superiors)->where("client_id", $client->id)->pluck('employee_id')->toArray();
                    foreach ($superiors as $superior) {

                        if (!in_array($superior, $supHandle)) {
                            $supHandleData = array(
                                "company_id" => $companyID,
                                "employee_id" => $superior,
                                "client_id" => $client->id,
                                "map_type" => "2",
                            );
                            $supHandleSaved = DB::table('handles')->insertGetId($supHandleData);
                        }
                    }
                    $beatID = getArrayValue($value, "beat_id");
                    if (!empty($beatID)) {

                        DB::table('beat_client')->updateOrInsert(
                            ['client_id' => $client->id],
                            ['beat_id' => $beatID]
                        );

                        $citySel = $this->getArrayValue($value, "city");
                        if (isset($citySel)) {
                            if ($citySel != 0) {
                                $beatInstance = Beat::where('id', $beatID)->first();
                                $getBeatClients = DB::table('beat_client')->where('beat_id', $beatID)->pluck('client_id')->toArray();
                                $clientsBeats = Client::where('company_id', $companyID)->whereIn('id', $getBeatClients)->whereNotNull('city')->distinct('city')->pluck('city')->toArray();
                                if ($beatInstance && sizeof($clientsBeats) == 1) {
                                    if ($citySel == $clientsBeats[0]) {
                                        if (!isset($beatInstance->city_id)) {
                                            $beatInstance->city_id = $citySel;
                                            $beatInstance->update();
                                        }
                                    }
                                }
                            }
                        }

                    }
                    $custom_fields = json_decode($this->getArrayValue($value, "custom_fields"));
                    // Log::info('info', array("raw clients"=>print_r($custom_fields,true)));
                    // Log::info('info', array("sync custom fields"=>print_r($custom_fields,true)));
                    if (!empty($custom_fields)) {

                        //$custom_fields = json_decode($request->custom_fields);
                        //Log::info('info', array("Sync custom fields"=>print_r($custom_fields,true)));
                        $reqCustomFields = [];
                        foreach ($custom_fields as $cf_key => $cf_value) {
                            $reqCustomFields[$cf_key] = $cf_value;
                        }

                        $customRequest = new Request($reqCustomFields);

                        $party_meta = DB::table('party_meta')->where('client_id', $client->id)->first();
                        if ($party_meta) {
                            $collec = CustomField::where('company_id', $companyID)->where('for', '=', 'Party')->where('status', 1)->get();
                            $imagesFilesCollections = $collec->where('type', 'Multiple Images');
                            foreach ($imagesFilesCollections as $imageCollection) {
                                if (!$request[$imageCollection->slug]) {
                                    $request->merge([
                                        $imageCollection->slug => true,
                                    ]);
                                }
                            }

                            $imagesFilesCollections = $collec->where('type', 'File');
                            foreach ($imagesFilesCollections as $imageCollection) {
                                if (!$request[$imageCollection->slug]) {
                                    $request->merge([
                                        $imageCollection->slug => true,
                                    ]);
                                }
                            }

                            $array_field = (new CustomCheckApi($collec, $customRequest, $party_meta, $companyID))->check();

                            $decodedArray = json_decode($party_meta->cf_value);
                            if (isset($array_field) && !empty($array_field)):
                                DB::table('party_meta')->where('client_id', $client->id)->update(['cf_value' => json_encode($array_field)]);
                            endif;
                        } else {
                            $collec = CustomField::where('for', '=', 'Party')->where('add', '=', 'yes')->where('status', 1)->get();

                            if ($collec->count()):
                                $array_field = (new CustomCheckApi($collec, $customRequest, null, $companyID))->check();
                            endif;

                            if (isset($array_field) && !empty($array_field)):
                                DB::table('party_meta')->insert([
                                    'client_id' => $client->id,
                                    'cf_value' => json_encode($array_field),
                                ]);
                            endif;
                        }
                        $party_meta = DB::table('party_meta')->where('client_id', $client->id)->first();
                        if ($party_meta) {
                            $client->custom_fields = $party_meta->cf_value;
                        }

                    }

                    $clientHandlingData = getClientHandlingData($companyID, $client->id, true);
                    $encodedHandlingData = json_encode($clientHandlingData);
                    $client->employee_ids = $encodedHandlingData;

                    $category_rates = json_decode($this->getArrayValue($value, "category_rates"));
                    if(isset($category_rates)){
                      if(count($category_rates) > 0){
                        $records = array();
                        foreach($category_rates as $category_rate){
                          array_push($records, array('client_id' => $client->id, 'category_rate_type_id' => $category_rate));
                        }
                        ClientCategoryRateType::insert($records);
                      }
                    }

                    $savedClient['category_rate_ids'] = $client->appliedcategoryrates()->get(['id', 'category_id', 'name'])->pluck('id')->toJson();

                    sendPushNotification_(getFBIDs($companyID), 10, null, array("data_type" => "client", "employee_id" => $employeeID, "client" => $client, "action" => $action));

                }

                $nSaved = saveAdminNotification($companyID, $employeeID, date("Y-m-d H:i:s"), "Added Party", "client", $client);
            }
        }
        return $returnItems ? $arraySyncedData : true;
    }

    private function manageUnsyncedOrder($postData, $returnItems = false, $client = null)
    {
        $rawData = $this->getArrayValue($postData, "nonsynced_orders");
        $user = Auth::user();
        $companyID = $user->company_id;
        $employee = Employee::where('company_id', $companyID)->where('user_id', $user->id)->first();
        $employeeID = $employee->id;
        $loggedEmployeeId = $employeeID;
        // $createdAt = $this->getArrayValue($postData, "created_at");
        // $employeeName = $this->getArrayValue($postData, "employee_name");

        if (empty($rawData)) {
            return $returnItems ? array() : false;
        }

        $data = json_decode($rawData, true);

        $arraySyncedData = array();
        try {
            foreach ($data as $key => $order) {
                $orderClientID = (int) $this->getArrayValue($order, "client_id");
                $orderClientUniqueID = $this->getArrayValue($order, "client_unique_id");
                $orderUniqueID = $this->getArrayvalue($order, "unique_id");
                $orderID = $this->getArrayValue($order, "id");
                $orderNumber = (int) $this->getArrayValue($order, "order_no");
                $product_level_discount_flag = $this->getArrayValue($order, "product_level_discount_flag") ? $this->getArrayValue($order, "product_level_discount_flag") : 0;
                $product_level_tax_flag = $this->getArrayValue($order, "product_level_tax_flag") ? $this->getArrayValue($order, "product_level_tax_flag") : 0;
                $tempOrderArray["delivery_status_id"] = $this->getArrayvalue($order, "delivery_status_id");
                if (empty($orderClientID) || $orderClientID == 0) {
                    if ($returnItems && !empty($client)) {
                        $tempClientUniqueID = $client->unique_id;
                        $tempClientID = $client->id;
                        if ($orderClientUniqueID == $tempClientUniqueID) {
                            $orderClientID = $tempClientID;
                        } else {
                            continue;
                        }

                    }
                }

                /**
                 * Admin Notification Text
                 */
                $notText = "Added Order";
                /**
                 * Send Push Notification To Superior of
                 */
                $notEid = $employeeID;
                $outlet_id = null;
                $outlet_order = false;

                if (isset($orderID)) {
                    $notText = "Updated Order";

                    $order_instance = Order::where('company_id', $companyID)->where('id', $orderID)->first();
                    $notEid = $order_instance->employee_id;

                    if ($product_level_tax_flag == 1) {
                        $order_details = $order_instance->orderdetails;
                        foreach ($order_details as $orderdetail) {
                            $orderdetail->taxes()->detach();
                        }
                    } else {
                        $order_instance->taxes()->detach();
                    }
                    OrderDetails::where('order_id', $orderID)->delete();
                    $employeeID = $order_instance->employee_id;
                    $outlet_id = $order_instance->outlet_id;
                    if (isset($order_instance->outlet_id)) {
                        $outlet_order = true;
                        if ($order_instance->client_id != $orderClientID) {
                            $outlet_id = null;
                            $employeeID = $employee->id;
                        }
                    }
                    $orderNumber = $order_instance->order_no;

                } else {
                    $orderNumber = getOrderNo($companyID);
                    $pendingModuleAttribute = ModuleAttribute::where('company_id', $companyID)->where('title', 'Pending')->first();
                    $tempOrderArray["delivery_status_id"] = $pendingModuleAttribute->id;
                }
                // $orderUniqueID = $this->getArrayvalue($order, "unique_id");
                $tempOrderArray["unique_id"] = $orderUniqueID;
                $tempOrderArray["company_id"] = $companyID;
                $tempOrderArray["employee_id"] = $outlet_id ? 0 : $employeeID;
                $tempOrderArray["outlet_id"] = $outlet_id;
                $tempOrderArray["client_id"] = $orderClientID;
                $tempOrderArray["order_no"] = $orderNumber;
                $tempOrderArray["order_date"] = $this->getArrayvalue($order, "order_date");

                if ($this->getArrayvalue($order, "client_id")) {
                    $client = Client::where('company_id', $companyID)->where('id', $this->getArrayvalue($order, "client_id"))->first();
                    if ($client) {
                        $tempOrderArray["due_date"] = Carbon::parse($this->getArrayvalue($order, "order_date"))->addDays($client->credit_days)->format('Y-m-d');
                    }

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

                $tempOrderArray["order_note"] = $this->getArrayvalue($order, "order_note");
                $tempOrderArray["created_at"] = $this->getArrayvalue($order, "created_at");
                $tempOrderArray["product_level_discount_flag"] = "$product_level_discount_flag";
                $tempOrderArray["product_level_tax_flag"] = "$product_level_tax_flag";

                $mil = $this->getArrayvalue($order, "unix_timestamp");
                $seconds = ceil($mil / 1000);
                $order["order_datetime"] = date("Y-m-d H:i:s", $seconds);
                $latitude = $this->getArrayValue($order, "latitude");
                $longitude = $this->getArrayValue($order, "longitude");
                if (isset($latitude) && isset($longitude)) {
                    $tempOrderArray["latitude"] = $latitude;
                    $tempOrderArray["longitude"] = $longitude;
                }
                if ($orderID) {
                    $update_order = Order::find($orderID);
                    if ($product_level_discount_flag == 1) {
                        $tempOrderArray["discount"] = $this->getArrayValue($order, "pdiscount_sum");
                    }
                    $update_order->update($tempOrderArray);
                } else {
                    // $orderID = Order::insertGetId($tempOrderArray);
                    $order_unique_id = Order::where('company_id', $companyID)->where('unique_id', $orderUniqueID)->first();
                    if ($order_unique_id) {
                        $update_order = Order::where('company_id', $companyID)->where('unique_id', $orderUniqueID)->first();
                        $tempOrderArray["order_no"] = $update_order->order_no;
                        if ($product_level_discount_flag == 1) {
                            $tempOrderArray["discount"] = $this->getArrayValue($order, "pdiscount_sum");
                        }
                        $update_order->update($tempOrderArray);
                    } else {
                        $tempOrderArray["order_datetime"] = $this->getArrayvalue($order, "order_datetime");
                        if ($product_level_discount_flag == 1) {
                            $tempOrderArray["discount"] = $this->getArrayValue($order, "pdiscount_sum");
                        }
                        
                        $checkDuplicateOrderNumberExists = Order::CheckDuplicateOrderNumberExist($tempOrderArray['order_no'], $companyID);
                        if($checkDuplicateOrderNumberExists['exists']){
                          $tempOrderArray['order_no'] = $checkDuplicateOrderNumberExists['new_order_no'];
                        }
                        $orderID = Order::insertGetId($tempOrderArray);
                    }
                }
                $schemeResponse = array();
                $saveAdminStatusUpdateNotf = true;
                if (isset($orderID)) {
                    $status_updated = $this->getArrayValue($order, "order_status_update");
                    if ($status_updated) {
                        $order_get = Order::select('orders.*', 'module_attributes.id as maID', 'module_attributes.title', 'module_attributes.color as delivery_status_color', 'clients.id as clientID', 'clients.company_name as client_company_name', 'clients.name as client_name', 'client_settings.order_prefix')->leftJoin('client_settings', 'client_settings.company_id', 'orders.company_id')
                            ->leftJoin('clients', 'orders.client_id', 'clients.id')
                            ->leftJoin('module_attributes', 'orders.delivery_status_id', 'module_attributes.id')
                            ->where('orders.company_id', $companyID)->where('orders.id', $orderID)->first();
                        $order_get->delivery_status = $order_get->title;
                        $schemeResponse = $order_get->orderScheme->toArray();
                        if ($order_get->employee_id == 0) $superiors = DB::table('handles')->where('client_id', $update_order->client_id)->pluck('employee_id')->toArray();
                        else $superiors = Employee::EmployeeParents($notEid, array());

                        $partyHandles = DB::table('handles')->where('client_id', $order_get->client_id)->whereNotIn('employee_id', $superiors)->pluck('employee_id')->toArray();
                        $superiors = array_merge($partyHandles, $superiors);
                        if ($notEid == $loggedEmployeeId && $notEid != 0) $superiors = array_diff($superiors, array($notEid));
                        $fbID = Employee::where('company_id', $companyID)->where('status', 'Active')->whereIn('id', $superiors)->whereNotNull('firebase_token')->where('id', '<>', $loggedEmployeeId)->pluck('firebase_token');
                        $orderPrefix = $order_get->order_prefix;
                        $notificationData = array(
                            "company_id" => $companyID,
                            "employee_id" => $employeeID,
                            "data_type" => "order",
                            "data" => "",
                            "action_id" => $orderID,
                            "title" => "Order " . $order_get->title,
                            "description" => "Order {$orderPrefix}{$order_get->order_no}'s status has been changed to {$order_get->title}.",
                            "created_at" => date('Y-m-d H:i:s'),
                            "status" => 1,
                            "to" => 1,
                            "unix_timestamp" => time(),
                        );
                        $dataPayload = array("data_type" => "order", "order" => $order_get, "action" => "update_status");
                        $sent = sendPushNotification_($fbID, 1, $notificationData, $dataPayload);
                        saveAdminNotification($companyID, $loggedEmployeeId, date("Y-m-d H:i:s"), "Updated Status", "order", $order_get);
                        $saveAdminStatusUpdateNotf = false;
                        $notText = "Updated Status";
                    }
                    $orderData = $tempOrderArray;
                    $orderData["id"] = $orderID;
                    array_push($arraySyncedData, $orderData);
                    if($saveAdminStatusUpdateNotf) saveAdminNotification($companyID, $employeeID, date("Y-m-d H:i:s"), $notText, "order", $orderData);
                    if ($notEid != 0) {
                        $superiors = Employee::employeeParents($notEid, array());
                        if ($notEid == $loggedEmployeeId) {
                            $superiors = array_diff($superiors, array($notEid));
                        }

                    } else {
                        $superiors = DB::table('handles')->where('client_id', $orderClientID)->pluck('employee_id')->toArray();
                    }
                    // if($notText == "Added Order"){
                    //   $this->orderNotification($companyID, $superiors, $orderID, "add");
                    // }elseif($notText == "Updated Order"){
                    //   $this->orderNotification($companyID, $superiors, $orderID, "update");
                    // }
                    $handlesData = DB::table('handles')->where('client_id', $orderClientID)->whereNotIn('employee_id', $superiors)->pluck('employee_id')->toArray();
                    $superiors = array_merge($handlesData, $superiors);
                    if ($notEid == $loggedEmployeeId && $notEid != 0) $superiors = array_diff($superiors, array($notEid));
                    //Saving taxes
                    $taxes = $this->getArrayValue($order, "taxes");
                    if (!empty($taxes) && $product_level_tax_flag == 0) {
                        $taxes = json_decode($taxes, true);
                        $taxOnOrderArray = array();
                        foreach ($taxes as $tax) {
                            $tempArrayTax = array(
                                'order_id' => $orderID,
                                'tax_type_id' => $this->getArrayValue($tax, "id"),
                                'tax_name' => $this->getArrayValue($tax, "name"),
                                'tax_percent' => $this->getArrayValue($tax, "percent"),
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
                                $rate = floatval($this->getArrayValue($v, "applied_rate"));
                                $quantity = floatval($this->getArrayValue($v, "quantity"));
                                $ptotal_amt = floatval($this->getArrayValue($v, "ptotal_amt"));
                                $pfinal_amount = floatval($this->getArrayValue($v, "pfinal_amount"));
                                $pdiscount = floatval($this->getArrayValue($v, "pdiscount"));
                                $pdiscount_type = $this->getArrayValue($v, "pdiscount_type");

                                if (!isset($pdiscount)) {
                                    $pdiscount = 0;
                                }

                                $ptotal_amt = $pfinal_amount;

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
                                $opTemp["amount"] = $pfinal_amount;
                                $opTemp["short_desc"] = $this->getArrayValue($v, "short_desc");
                                if ($product_level_discount_flag == 1) {
                                    $opTemp["pdiscount"] = $pdiscount;
                                    $opTemp["pdiscount_type"] = $pdiscount_type;
                                }
                                $opTemp["ptotal_amt"] = $pfinal_amount;
                                $opTemp["variant_colors"] = $this->getArrayValue($v, "variant_colors");
                                $opTemp["created_at"] = $this->getArrayValue($v, "created_at");
                                array_push($opFinalArray, $opTemp);
                                $batchSaved = OrderDetails::insertGetId($opTemp);
                                if ($batchSaved) {
                                    $updated_rate = $pfinal_amount;
                                    if ($product_level_tax_flag == 1) {
                                        $tax_applied = floatval(0.0);
                                        $ptaxes = $this->getArrayValue($v, "total_tax");
                                        $decodeValue = json_decode($ptaxes, true);
                                        if (!empty($decodeValue)) {
                                            foreach ($decodeValue as $key => $ptax) {
                                                $tax_applied += floatval($ptax['percent']);
                                                DB::table('tax_on_orderproducts')->insert([
                                                    "orderproduct_id" => $batchSaved,
                                                    "tax_type_id" => $ptax['id'],
                                                    "product_id" => $opTemp["product_id"],
                                                ]);
                                            }
                                        }
                                    }

                                }
                            }
                        }
                    }

                    if ($notText == "Added Order") {
                        $this->orderNotification($companyID, $superiors, $orderID, $schemeResponse, "add");
                    } elseif ($notText == "Updated Order") {
                        $this->orderNotification($companyID, $superiors, $orderID, $schemeResponse, "update");
                    }
                }
            }
        } catch (\Exception $e) {
          Log::error(array("Order Sync Client Controller", $data, $e->getMessage(), $e->getCode(), $e->getLine()));
        }

        return $returnItems ? $arraySyncedData : true;
    }

    private function orderNotification($companyID, $employeeIDs, $orderID, $schemeResponse, $action)
    {
      $user = Auth::user();
      $companyID = $user->company_id;
      $employee = Employee::where('company_id', $companyID)->where('user_id', $user->id)->first();
      $loggedInEmployee = $employee->id;
      Log::info(array("Action API", $action));
        $orders = Order::where('orders.id', $orderID)
            ->where('orders.company_id', $companyID)
            // ->leftJoin('employees', 'orders.employee_id', 'employees.id')
            // ->leftJoin('clients', 'orders.client_id', 'clients.id')
            ->leftJoin('client_settings', 'orders.company_id', 'client_settings.company_id')
            ->leftJoin('module_attributes', 'orders.delivery_status_id', 'module_attributes.id')
            ->select('orders.*', 'client_settings.order_prefix', 'module_attributes.id as moduleattributesId', 'module_attributes.title as delivery_status', 'module_attributes.color', 'module_attributes.order_amt_flag', 'module_attributes.order_edit_flag', 'module_attributes.order_delete_flag')
            ->first();
        if ($orders) {
            // $moduleAttributes = ModuleAttribute::where('company_id', $companyID)->get();

            // $product_level_tax_flag = $orders->product_level_tax_flag;
            // $productLines = $this->getProductLines($orders->id, $product_level_tax_flag);
            // if ($product_level_tax_flag == 1) {
            //     $orders->taxes = null;
            // } else {
            //     $orders->taxes = $this->getTaxes($orders->id);
            // }
            // $orders->orderproducts = $productLines;
            // if ($orders->delivery_status) {
            //     $delivery_status_color = $moduleAttributes->where('title', '=', $orders->delivery_status)->first();
            //     if ($delivery_status_color) {
            //         $orders->delivery_status_color = $delivery_status_color->color;
            //     } else {
            //         $orders->delivery_status_color = null;
            //     }
            // } else {
            //     $orders->delivery_status_color = null;
            // }

            // if ($orders->employee_id == 0 && $orders->outlet_id) $orders->employee_name = $orders->outlets()->withTrashed()->first() ? $orders->outlets()->withTrashed()->first()->contact_person . " (O)" : "";
            $dataPayload = array("data_type" => "order", "order" => $orders->id, "scheme_response" => array(), "action" => $action);
            $fbIDs = DB::table('employees')->where(array(array('company_id', $companyID), array('status', 'Active')))->whereNotNull('firebase_token')->whereIn('id', $employeeIDs)->where('id', '<>', $loggedInEmployee)->pluck('firebase_token');
            $notificationAlert = array();
            $orderPrefix = $orders->order_prefix;
            
            switch ($action) {
              case "add":
                $title = "Order Created";
                $description = "Order {$orderPrefix}{$orders->order_no} has been added.";
                break;
              case "update":
                  $title = "Order Updated";
                  $description = "Order {$orderPrefix}{$orders->order_no} has been updated.";
                  break;
              case "update_status":
                $title = "Order " . $orders->delivery_status;
                $description = "Order {$orderPrefix}{$orders->order_no} status has been changed to {$orders->delivery_status}.";
                break;
              default:
                $title = "Order Notification";
                $description = "Order {$orderPrefix}{$orders->order_no}'s.";
                break;
            }

            $notificationAlert[] = array(
                "company_id" => $companyID,
                "employee_id" => $orders->employee_id,
                "data_type" => "order",
                "data" => "",
                "action_id" => $orders->id,
                "title" => $title,
                "description" => $description,
                "created_at" => date('Y-m-d H:i:s'),
                "status" => 1,
                "to" => 1,
                "unix_timestamp" => time()
            );
            $msgID = sendPushNotification_($fbIDs, 1, $notificationAlert, $dataPayload);
            Log::info(array("Notification Status API", $msgID));

        }
        // $msgID = sendPushNotification_($fbIds, 1, null, $dataPayload);
    }

    private function getProductLines($orderID, $tax_flag)
    {
        if (empty($orderID)) {
            return null;
        }

        $orderProducts = OrderDetails::select('orderproducts.*', 'products.product_name', 'products.short_desc')
            ->join('products', 'products.id', '=', 'orderproducts.product_id')
            ->where('order_id', $orderID)
            ->get(); //->toArray();

        if ($tax_flag == 1) {
            foreach ($orderProducts as $orderProduct) {
                $taxes = $orderProduct->taxes()->withTrashed()->get();
                if ($taxes->count() == 0) {
                    $orderProduct->total_tax = null;
                } else {
                    $orderProduct->total_tax = json_encode($taxes->toArray());
                }
            }
        }
        $orderProducts = $orderProducts->toArray();
        return empty($orderProducts) ? null : json_encode($orderProducts);
    }

    private function getTaxes($orderID)
    {
        if (empty($orderID)) {
            return null;
        }
        $order_instance = Order::findOrFail($orderID);
        $taxes = $order_instance->taxes()->withTrashed()->get()->toArray();
        return empty($taxes) ? null : json_encode($taxes);
    }

    private function manageUnsyncedNoOrder($postData, $returnItems = false, $client = null)
    {

        $rawData = $this->getArrayValue($postData, "nonsynced_no_orders");
        $user = Auth::user();
        $companyID = $user->company_id;
        $employee = Employee::where('company_id', $companyID)->where('user_id', $user->id)->first();
        $employeeID = $employee->id;
        $employeeName = $this->getArrayValue($postData, "employee_name");

        if (empty($rawData)) {
            return $returnItems ? array() : false;
        }

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
            $alreadyAddedNoOrder = NoOrder::select('id', 'unique_id', 'company_id', 'employee_id', 'client_id', 'remark', 'unix_timestamp', 'datetime', 'date')
                ->where('employee_id', $employeeID)
                ->where('company_id', $companyID)
                ->where('unique_id', $noOrderUniqueID)
                ->first();
            if (!empty($alreadyAddedNoOrder)) {
                array_push($arraySyncedData, $alreadyAddedNoOrder->toArray());
                continue;
            }

            $noOrderID = DB::table('no_orders')->insertGetId($tempNoOrderArray);

            $notEid = $tempNoOrderArray['employee_id'];

            if (!empty($noOrderID)) {
                $imageArray = array();
                $tempImageNames = array();
                $tempImagePaths = array();
                $images_ids = [];
                $images_names = [];
                $images_paths = [];

                $images = $this->getArrayValue($noOrder, "images");
                if (!empty($images)) {
                    $jsonDecoded = json_decode($images, true);

                    foreach ($jsonDecoded as $key => $value) {
                        $tempImageName = $this->getImageName();
                        $tempImageDir = $this->getImagePath($companyID, "noorders");
                        $tempImagePath = "/storage/app/public/" . $tempImageDir . "/" . $tempImageName;
                        $decodedData = base64_decode($value);
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
                            $tempImageArray["type_id"] = $noOrderID;
                            $tempImageArray["company_id"] = $companyID;
                            $tempImageArray["employee_id"] = $employeeID;
                            $tempImageArray["image"] = $imageName;
                            $tempImageArray["image_path"] = $imagePath;
                            $tempImageArray["created_at"] = $tempNoOrderArray["created_at"];
                            array_push($imageData, $tempImageArray);
                        }
                        DB::table('images')->insert($imageData);
                        $finalImages = DB::table('images')->where('type', 'noorders')->where('type_id', $noOrderID)->whereNull('deleted_at')->get();
                        foreach ($finalImages as $finalImage) {
                            array_push($images_ids, $finalImage->id);
                            array_push($images_names, $finalImage->image);
                            array_push($images_paths, $finalImage->image_path);
                        }
                        // Log::info('info', array("postData"=>print_r($finalImages,true)));
                    }
                }
                $orderData = $tempNoOrderArray;
                $orderData["id"] = $noOrderID;

                $orderData['image_ids'] = json_encode($images_ids);
                if ($orderData['image_ids'] == '[]') {
                    $orderData['image_ids'] = null;
                }
                $orderData['images'] = json_encode($images_names);
                if ($orderData['images'] == '[]') {
                    $orderData['images'] = null;
                }
                $orderData['image_paths'] = json_encode($images_paths);
                if ($orderData['image_paths'] == '[]') {
                    $orderData['image_paths'] = null;
                }

                array_push($arraySyncedData, $orderData);
                saveAdminNotification($companyID, $employeeID, date("Y-m-d H:i:s"), "Added No Order", "noorders", $orderData);

                $superiors = Employee::employeeParents($tempNoOrderArray['employee_id'], array());
                if ($notEid == $employeeID) {
                    $superiors = array_diff($superiors, array($notEid));
                }

                $fbIDs = DB::table('employees')->where(array(array('company_id', $companyID), array('status', 'Active')))->whereNotNull('firebase_token')->whereIn('id', $superiors)->pluck('firebase_token');

                $dataPayload = array("data_type" => "noorder", "noorder" => $this->getNoOrderData($noOrderID, $noOrderClientID), "action" => 'add');
                $msgID = sendPushNotification_($fbIDs, 36, null, $dataPayload);

            }
        }

        return $returnItems ? $arraySyncedData : true;
    }

    private function getNoOrderData($noOrderID, $noOrderClient_id)
    {
        $noOrder = NoOrder::where('id', $noOrderID)->select('id', 'unique_id', 'company_id', 'employee_id', 'client_id', 'remark', 'unix_timestamp', 'datetime', 'date')->first();

        $imageArray = array();
        $tempImageNames = array();
        $tempImagePaths = array();
        $images_ids = [];
        $images_names = [];
        $images_paths = [];
        // DB::table('images')->insert($imageData);
        $finalImages = DB::table('images')->where('type', 'noorders')->where('type_id', $noOrderID)->whereNull('deleted_at')->get();
        if ($finalImages->first()) {
            foreach ($finalImages as $finalImage) {
                array_push($images_ids, $finalImage->id);
                array_push($images_names, $finalImage->image);
                array_push($images_paths, $finalImage->image_path);
            }
        }

        $client = Client::where('id', $noOrderClient_id)->first();
        if ($client) {
            $noOrder->name = $client->name;
            $noOrder->company_name = $client->company_name;
        }
        $noOrder->image_ids = json_encode($images_ids);
        if ($noOrder->image_ids == '[]') {
            $noOrder->image_ids = null;
        }
        $noOrder->images = json_encode($images_names);
        if ($noOrder->images == '[]') {
            $noOrder->images = null;
        }
        $noOrder->image_paths = json_encode($images_paths);
        if ($noOrder->image_paths == '[]') {
            $noOrder->image_paths = null;
        }

        return $noOrder;
    }

    private function manageUnsyncedTask($postData, $returnItems = false, $client = null)
    {

        $rawData = $this->getArrayValue($postData, "nonsynced_task");
        $user = Auth::user();
        $companyID = $user->company_id;
        $employee = Employee::where('company_id', $companyID)->where('user_id', $user->id)->first();
        $employeeID = $employee->id;
        $clientID = $this->getArrayValue($postData, "client_id");

        if (empty($rawData)) {
            return $returnItems ? array() : false;
        }

        $data = json_decode($rawData, true);

        $arraySyncedData = array();

        /*prepare data for saving*/
        foreach ($data as $key => $task) {
            //Log::info('info', array("task inside unsynced task"=>print_r($task,true)));

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
        /*

         */

        return $returnItems ? $arraySyncedData : true;
    }

    private function manageUnsyncedActivity($postData, $returnItems = false, $client = null)
    {
        $rawData = $this->getArrayValue($postData, "nonsynced_activities");
        $user = Auth::user();
        $companyID = $user->company_id;
        $employee = Employee::where('company_id', $companyID)->where('user_id', $user->id)->first();
        $employeeID = $employee->id;

        if (empty($rawData)) {
            return $returnItems ? array() : false;
        }

        $data = json_decode($rawData, true);

        $arraySyncedData = array();

        try {
            foreach ($data as $key => $u_activity) {
                $activity_id = $this->getArrayValue($u_activity, "id");
                $unique_id = $this->getArrayValue($u_activity, "unique_id");
                $activityClientID = $this->getArrayValue($u_activity, "client_id");
                $activityClientUniqueID = $this->getArrayValue($u_activity, "client_unique_id");
                if (empty($activityClientID) && empty($activity_id)) {
                    if ($returnItems && !empty($client)) {
                        $tempClientUniqueID = $client->unique_id;
                        $tempClientID = $client->id;
                        if ($activityClientUniqueID == $tempClientUniqueID) {
                            $activityClientID = $tempClientID;
                        } else {
                            continue;
                        }
                    }
                }
                $completion_datetime = $this->getArrayValue($u_activity, "completion_datetime");
                if ($completion_datetime == "") {
                    $completion_datetime = null;
                }

                $type = $this->getArrayValue($u_activity, "activity_type_id");
                if ($this->getArrayValue($u_activity, "type")) {
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
                    "completed_by" => $completion_datetime ? $employeeID : null,
                    "client_id" => $activityClientID,
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

                $wasRecentlyCreated = $activity->wasRecentlyCreated;
                $wasChanged = $activity->wasChanged();
                $isDirty = $activity->isDirty();
                $exists = $activity->exists;

                $assigned_to_superiors = Employee::employeeParents((int) $activity->assigned_to, array());

                $created_by_superiors = $activity->assigned_to == $activity->created_by ? array() : Employee::employeeParents((int) $activity->created_by, array());

                $merged_superiors = array_unique(array_merge($assigned_to_superiors, $created_by_superiors));

                $mergedSuperiorsTokens = Employee::where('company_id', $companyID)->whereIn('id', $merged_superiors)->pluck('firebase_token', 'id')->toArray();

                if ($wasRecentlyCreated || $wasChanged || $activity->exists || !$created) {
                    array_push($arraySyncedData, $activity);

                    $title = "Updated Activity";
                    $savedActivity = $activityData;
                    $savedActivity["id"] = $activity->id;
                    $status = "Your activity has been updated";

                    $nSaved = saveAdminNotification($companyID, $employeeID, date("Y-m-d H:i:s"), $title, "activities", $savedActivity);
                } else {
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
                        "unix_timestamp" => time(),
                    );
                    $activity->priority_name = getObjectValue($activity->activityPriority, "name", "");
                    $activity->type_name = getObjectValue($activity->activityType, "name", "");
                    $activity->completed_by_name = $activity->completedByEmployee()->withTrashed()->first() ? $activity->completedByEmployee()->withTrashed()->first()->name : "";
                    $activity->created_by_name = $activity->createdByEmployee()->withTrashed()->first() ? $activity->createdByEmployee()->withTrashed()->first()->name : "";
                    $activity->assigned_to_name = $activity->assignedTo()->withTrashed()->first() ? $activity->assignedTo()->withTrashed()->first()->name : "";

                    if (!$created) {
                        $dataPayload = array("data_type" => "activity", "activity" => $activity, "action" => "update");
                        $assigned_to_token = $mergedSuperiorsTokens[$activity->assigned_to];
                        unset($mergedSuperiorsTokens[$activity->assigned_to]);
                        if ($activity->created_by != $activity->assigned_to) {
                            $created_by_token = $mergedSuperiorsTokens[$activity->created_by];
                            unset($mergedSuperiorsTokens[$activity->created_by]);
                            if ($activity->created_by != $employeeID) {
                                sendPushNotification_(array($created_by_token), 17, $notificationData, $dataPayload);
                            }

                        }
                        if (!empty(array_values($mergedSuperiorsTokens))) {
                            $sent = sendPushNotification_(array_values($mergedSuperiorsTokens), 17, null, $dataPayload);
                        }

                        if ($activity->assigned_to != $employeeID) {
                            $sent_only_to_assigned_to = sendPushNotification_(array($assigned_to_token), 17, $notificationData, $dataPayload);
                        }

                    } else {
                        $dataPayload = array("data_type" => "activity", "activity" => $activity, "action" => "add");

                        $assigned_to_token = $mergedSuperiorsTokens[$activity->assigned_to];
                        unset($mergedSuperiorsTokens[$activity->assigned_to]);

                        if (array_key_exists($employeeID, $mergedSuperiorsTokens)) {
                            unset($mergedSuperiorsTokens[$employeeID]);
                        }

                        $sent = sendPushNotification_(array_values($mergedSuperiorsTokens), 17, null, $dataPayload);
                        if ($activity->assigned_to != $employeeID) {
                            $sent_only_to_assigned_to = sendPushNotification_(array($assigned_to_token), 17, $notificationData, $dataPayload);
                        }

                    }
                }
            }

            return $returnItems ? $arraySyncedData : true;
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            return $returnItems ? $arraySyncedData : true;
        }

    }

    private function manageUnsyncedStock($postData, $returnItems = false, $client = null)
    {

        $rawData = $this->getArrayValue($postData, "nonsynced_stocks");
        $user = Auth::user();
        $companyID = $user->company_id;
        $employee = Employee::where('company_id', $companyID)->where('user_id', $user->id)->first();
        $employeeID = $employee->id;

        if (empty($rawData)) {
            return $returnItems ? array() : false;
        }

        $data = json_decode($rawData, true);
        $arraySyncedData = array();
        foreach ($data as $key => $stck) {
            $stckClientID = $this->getArrayValue($stck, "client_id");
            $stckClientUniqueID = $this->getArrayValue($stck, "client_unique_id");

            if (empty($stckClientID)) {
                if ($returnItems && !empty($client)) {
                    $tempClientUniqueID = $client->unique_id;
                    $tempClientID = $client->id;

                    if ($stckClientUniqueID == $tempClientUniqueID) {
                        $stckClientID = $tempClientID;
                    } else {
                        continue;
                    }
                }
            }

            $uniqueID = $this->getArrayValue($stck, "unique_id");
            $stockID = null;//$this->getArrayValue($stck, "id");
            $clientID = $stckClientID;
            $stockDate = $this->getArrayValue($stck, "stock_date");
            $stockDateUnix = $this->getArrayValue($stck, "stock_date_unix");
            DB::beginTransaction();
            $stocks = Stock::updateOrCreate(['id' => $stockID], ['company_id' => $companyID, 'client_id' => $clientID, 'employee_id' => $employeeID, 'stock_date' => $stockDate, 'stock_date_unix' => $stockDateUnix]);
            $stockDetail = $this->getArrayValue($stck, "sproducts");
            $stockDetails = json_decode($stockDetail, true);
            $stockID = $stocks->id;
            $batchArray = array();
            foreach ($stockDetails as $stockDetail) {
                $sdData = array(
                    'stock_id' => $stockID,
                    'product_id' => $this->getArrayValue($stockDetail, 'product_id'),
                    'product_name' => $this->getArrayValue($stockDetail, 'product_name'),
                    'variant_id' => $this->getArrayValue($stockDetail, 'variant'),
                    'variant_name' => $this->getArrayValue($stockDetail, 'variant_name'),
                    'unit_id' => $this->getArrayValue($stockDetail, 'unit'),
                    'unit_name' => $this->getArrayValue($stockDetail, 'unit_name'),
                    'unit_symbol' => $this->getArrayValue($stockDetail, 'unit_symbol'),
                    'quantity' => $this->getArrayValue($stockDetail, 'changeInValue'),
                    'image' => $this->getArrayValue($stockDetail, 'image'),
                    'image_path' => $this->getArrayValue($stockDetail, 'image_path'),
                    'mfg_date' => $this->getArrayValue($stockDetail, 'mfg_date'),
                    'batch_no' => $this->getArrayValue($stockDetail, 'batch_no'),
                    'expiry_date' => getArrayValue($stockDetail, 'expiry_date'),
                    'created_at' => $stocks->created_at,
                );
                array_push($batchArray, $sdData);
            }
            $batchSaved = DB::table('stock_details')->insert($batchArray);
            DB::commit();
            $tempArray = array();
            $tempArray['uniqueID'] = $uniqueID;
            $tempArray['stockID'] = $stockID;
            $tempArray['clientID'] = $clientID;
            $tempArray['employeeID'] = $employeeID;

            array_push($arraySyncedData, $tempArray);

        } //end foreach

        return $returnItems ? $arraySyncedData : false;
    }

    public function getAllEmployeeSuperior($cId, $empId, $superiors)
    {
        $company_id = $cId;
        $getSuperior = Employee::where('id', $empId)->where('company_id', $company_id)->first();
        if (!(empty($getSuperior->superior)) && !(in_array($getSuperior->superior, $superiors))) {
            $superiors[] = $getSuperior->superior;
            $superiors = $this->getAllEmployeeSuperior($cId, $getSuperior->superior, $superiors);
        }
        return $superiors;
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

    public function fetchClientCustomField(Request $request)
    {
        $user = Auth::user();
        $company_id = $user->company_id;
        $reqCustomfield = array();

        $party_meta = DB::table('party_meta')->where('client_id', $request->client_id)->first();

        if ($party_meta) {
            $custom_fields = CustomField::select('id')->where('company_id', $company_id)->where('status', 1)->where('for', 'Party')->get();
            $cf_value = (array) json_decode($party_meta->cf_value);
            $fieldIDs = array_keys($cf_value);
            // return $fieldIDs;
            foreach ($custom_fields as $custom_field) {
                if (in_array($custom_field->id, $fieldIDs)) {
                    $custom_field->custom_value = $cf_value[$custom_field->id];
                    //$custom_field->visible = false;
                    array_push($reqCustomfield, $custom_field);
                    //$reqCustomfield=$custom_field;
                }

            }
        }
        // }

        return response($reqCustomfield);

    }

    public function fetchClientOrders(Request $request)
    {
        $offset = $request->offset;
        $limit = $request->limit;
        $id = $request->client_id;

        try {
            $orders = Order::with(['employee' => function ($query) {
                return $query->select('employees.name', 'employees.id');
            }])->with(['outlets' => function ($query) {
                return $query->select('outlets.contact_person', 'outlets.id');
            }])->with(['clients' => function ($query) {
                return $query->select('clients.company_name', 'clients.id');
            }])->with(['module_status' => function ($query) {
                return $query->select('module_attributes.title', 'module_attributes.color', 'module_attributes.id', 'module_attributes.order_edit_flag', 'module_attributes.order_delete_flag', 'module_attributes.order_amt_flag');
            }])->with('orderdetails')->whereCompanyId($this->company_id)->whereClientId($id)->orderBy('orders.order_date', 'desc')->orderBy('orders.id', 'desc')->offset($offset)->limit($limit)->get()->map(function ($order) {
                return $this->formatOrder($order);
            });
            $next_fetch = Order::whereCompanyId($this->company_id)->whereClientId($id)
                ->offset($offset + $limit)->limit(1)->first();
            $response = [
                "success" => true,
                "offset" => $offset,
                "new_offset" => $offset + $limit,
                "next_fetch" => $next_fetch ? true : false,
                "limit" => $limit,
                "client_id" => $id,
                "data" => $orders,
            ];
        } catch (\Exception $e) {
            Log::error(array("Fetch Client Order () => "), array($e->getMessage()));
            $response = [
                "success" => false,
                "msg" => "Some Error Occured while fetching records.",
                "offset" => $offset,
                "limit" => $limit,
                "client_id" => $id,
                "data" => null,
            ];
        }

        return response()->json($response);

    }

    private function formatOrder($order)
    {
        if ($order->product_level_tax_flag == 0) {
            $taxes = $order->taxes->map(function ($tax) {
                return $this->formatTax($tax);
            });
        }

        try {
            $formatted_data = [
                'id' => $order->id,
                'unique_id' => $order->unique_id,
                'order_no' => $order->order_no,
                'employee_id' => $order->employee_id,
                'outlet_id' => $order->outlet_id,
                'employee_name' => ($order->employee_id == 0 && $order->outlet_id) ? $order->outlets->contact_person . '(O)' : $order->employee->name,
                'client_id' => $order->client_id,
                'order_to' => $order->order_to,
                'order_to_company_name' => $order->order_to ? $order->ordertos->company_name : null,
                'company_name' => $order->clients->company_name,
                'order_date' => $order->order_date,
                'order_time' => date("H:i:s", strtotime($order->order_datetime)),
                'delivery_status_id' => $order->delivery_status_id,
                'delivery_status' => $order->module_status->first()? $order->module_status->title: NULl,
                'delivery_status_color' => $order->module_status->first()? $order->module_status->color: NULl,
                'delivery_status_edit_flag' => $order->module_status->first()? $order->module_status->order_edit_flag: NULl,
                'delivery_status_delete_flag' => $order->module_status->first()? $order->module_status->order_delete_flag: NULl,
                'delivery_status_amt_flag' => $order->module_status->first()? $order->module_status->order_amt_flag: NULl,
                "product_level_discount_flag" => $order->product_level_discount_flag,
                "product_level_tax_flag" => $order->product_level_tax_flag,
                'order_note' => $order->order_note,
                "delivery_date" => $order->delivery_date,
                "delivery_note" => $order->delivery_note,
                "delivery_place" => $order->delivery_place,
                "transport_name" => $order->transport_name,
                "transport_number" => $order->transport_number,
                "billty_number" => $order->billty_number,
                'sub_total' => $order->tot_amount,
                'tax_amount' => $order->tax,
                'taxes' => isset($taxes) ? json_encode($taxes) : null,
                'discount' => $order->discount,
                'discount_type' => $order->discount_type,
                'grand_total' => $order->grand_total,
                'created_at' => date('Y-m-d H:i:s', strtotime($order->created_at)),
                'deleted_at' => date('Y-m-d H:i:s', strtotime($order->deleted_at)),
                'updated_at' => date('Y-m-d H:i:s', strtotime($order->updated_at)),
                'orderproducts' => json_encode($order->orderdetails->map(function ($orderdetail) use ($order) {
                    return $this->formatOrderDetails($orderdetail, $order->product_level_tax_flag);
                })),
                'scheme_response' => $order->orderScheme->toArray(),
                'latitude' => $order->latitude,
                'longitude' => $order->longitude,
                'editable' => $order->order_edit_flag == 1 && Auth::user()->can('order-update'),
                'deleteable' => $order->order_delete_flag == 1 && Auth::user()->can('order-delete'),
            ];
            return $formatted_data;
        } catch (\Exception $e) {
            Log::error(array("Format Order () => "), array($e->getMessage()));
            return array();
        }
    }

    private function formatOrderDetails($orderdetail, $product_level_tax_flag)
    {
        if ($product_level_tax_flag == 1) {
            $taxes = $orderdetail->taxes->map(function ($tax) {
                return $this->formatTax($tax);
            });
        }

        try {
            $formatted_data = [
                'id' => $orderdetail->id,
                "product_id" => $orderdetail->product_id,
                "product_name" => $orderdetail->product_name,
                "product_variant_id" => $orderdetail->product_variant_id,
                "product_variant_name" => $orderdetail->product_variant_name,
                "variant_colors" => $orderdetail->variant_colors,
                "variant_colors" => $orderdetail->variant_colors,
                "brand" => $orderdetail->brand ? $orderdetail->brands->name : null,
                "quantity" => $orderdetail->quantity,
                "change_in_value" => $orderdetail->quantity,
                "unit" => $orderdetail->unit,
                "unit_name" => $orderdetail->unit_name,
                "unit_symbol" => $orderdetail->unit_symbol,
                "mrp" => $orderdetail->mrp,
                "rate" => $orderdetail->rate,
                "total_amount" => $orderdetail->amount,
                "pdiscount" => $orderdetail->pdiscount,
                "pdiscount_type" => $orderdetail->pdiscount_type,
                "taxes" => isset($taxes) ? json_encode($taxes) : null,
                "short_desc" => $orderdetail->short_desc,
                'created_at' => date('Y-m-d H:i:s', strtotime($orderdetail->created_at)),
                'updated_at' => date('Y-m-d H:i:s', strtotime($orderdetail->updated_at)),
                'deleted_at' => $orderdetail->deleted_at ? date('Y-m-d H:i:s', strtotime($orderdetail->deleted_at)) : null,
            ];
            return $formatted_data;
        } catch (\Exception $e) {
            Log::error(array("Format Order Details () => "), array($e->getMessage()));
            return array();
        }
    }

    private function formatTax($tax)
    {
        try {
            $formatted_data = [
                'id' => $tax->id,
                'name' => $tax->name,
                'percent' => $tax->percent,
            ];
        } catch (\Exception $e) {
            Log::error(array("Format Tax () => "), array($e->getMessage()));
            return array();
        }

        return $formatted_data;

    }

    public function fetchClientCollections(Request $request)
    {
        $offset = $request->offset;
        $limit = $request->limit;
        $id = $request->client_id;

        try {
            $collections = Collection::with(['employees' => function ($query) {
                return $query->select('employees.name', 'employees.id');
            }])->with(['client' => function ($query) {
                return $query->select('clients.company_name', 'clients.id');
            }])->with('images')->whereCompanyId($this->company_id)->whereClientId($id)->orderBy('collections.id', 'desc')->offset($offset)->limit($limit)->get()->map(function ($collection) {
                return $this->formatCollection($collection);
            });
            $next_fetch = Collection::whereCompanyId($this->company_id)->whereClientId($id)
                ->offset($offset + $limit)->limit(1)->first();
            $response = [
                "success" => true,
                "offset" => $offset,
                "new_offset" => $offset + $limit,
                "next_fetch" => $next_fetch ? true : false,
                "limit" => $limit,
                "client_id" => $id,
                "data" => $collections,
            ];
        } catch (\Exception $e) {
            Log::error(array("Fetch Client Collection () => "), array($e->getMessage()));
            $response = [
                "success" => false,
                "msg" => "Some Error Occured while fetching records.",
                "offset" => $offset,
                "limit" => $limit,
                "client_id" => $id,
                "data" => null,
            ];
        }

        return response()->json($response);
    }

    private function formatCollection($collection)
    {

        $formatted_images = $collection->images->map(function ($image) {
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
                'id' => $collection->id,
                'unique_id' => $collection->unique_id,
                'client_id' => $collection->client_id,
                'company_name' => $collection->client->company_name,
                'employee_id' => $collection->employee_id,
                'employee_name' => $collection->employees->name,
                'payment_received_amount' => $collection->payment_received,
                'payment_method' => $collection->payment_method,
                'payment_note' => $collection->payment_note,
                'payment_date' => $collection->payment_date,
                'bank_id' => $collection->bank_id,
                'bank_name' => $collection->bank_id ? $collection->bank->name : null,
                'cheque_no' => $collection->cheque_no,
                'cheque_date' => $collection->cheque_date,
                'payment_status' => $collection->payment_status,
                'payment_status_note' => $collection->payment_status_note,
                "image_ids" => json_encode($image_ids, true),
                "images" => json_encode($images, true),
                "image_paths" => json_encode($image_paths, true),
                'editable' => Auth::user()->can('collection-update'),
                'deleteable' => Auth::user()->can('collection-delete'),
            ];

            return $formatted_data;
        } catch (\Exception $e) {
            Log::error(array("Format Collection () => "), array($e->getMessage()));
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

    public function fetchClientExpenses(Request $request)
    {
        $offset = $request->offset;
        $limit = $request->limit;
        $id = $request->client_id;
        $logged_employee_id = Auth::user()->employee->id;
        $lower_chains = Employee::employeeChilds($logged_employee_id, array());

        try {
            $expenses = Expense::with(['employee' => function ($query) {
                return $query->select('employees.name', 'employees.id');
            }])->with(['approvedBy' => function ($query) {
                return $query->select('employees.name', 'employees.id');
            }])->with(['client' => function ($query) {
                return $query->select('clients.company_name', 'clients.id');
            }])->with(['exptype' => function ($query) {
                return $query->select('expense_types.expensetype_name', 'expense_types.id');
            }])->with('images')->whereCompanyId($this->company_id)->whereClientId($id)
            ->whereIn('expenses.employee_id', $lower_chains)
                ->orderBy('expenses.id', 'desc')->offset($offset)->limit($limit)->get()->map(function ($expense) {
                return $this->formatExpense($expense);
                // return $expense;
            });
            $next_fetch = Expense::whereCompanyId($this->company_id)->whereClientId($id)
            ->whereIn('expenses.employee_id', $lower_chains)->offset($offset + $limit)->limit(1)->first();
            $response = [
                "success" => true,
                "offset" => $offset,
                "new_offset" => $offset + $limit,
                "next_fetch" => $next_fetch ? true : false,
                "limit" => $limit,
                "client_id" => $id,
                "data" => $expenses,
            ];
        } catch (\Exception $e) {
            Log::error(array("Fetch Client Expense () => "), array($e->getMessage()));
            $response = [
                "success" => false,
                "msg" => "Some Error Occured while fetching records.",
                "offset" => $offset,
                "limit" => $limit,
                "client_id" => $id,
                "data" => null,
            ];
        }

        return response()->json($response);
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
                'company_name' => $expense->client->company_name,
                'employee_id' => $expense->employee_id,
                'employee_name' => $expense->employee->name,
                'expense_type_id' => $expense->expense_type_id,
                'expense_category' => $expense->expense_type_id ? $expense->exptype->expensetype_name : null,
                'expense_amount' => $expense->amount,
                'status' => $expense->status,
                'approved_rejected_by_name' => $expense->status == 'Approved' || $expense->status == 'Rejected' ? $expense->approvedBy ? $expense->approvedBy->name : null : null,
                'created_at' => date('Y-m-d', strtotime($expense->created_at)),
                'expense_date' => $expense->expense_date,
                'remark' => $expense->remark,
                'description' => $expense->description,
                "image_ids" => json_encode($image_ids, true),
                "images" => json_encode($images, true),
                "image_paths" => json_encode($image_paths, true),
                'editable' => Auth::user()->can('expense-update'),
                'deleteable' => Auth::user()->can('expense-delete'),
            ];

            return $formatted_data;
        } catch (\Exception $e) {
            Log::error(array("Format Expense () => "), array($e->getMessage()));
            return array();
        }
    }

    public function fetchClientZeroOrders(Request $request)
    {
        $offset = $request->offset;
        $limit = $request->limit;
        $id = $request->client_id;

        try {
            $noorders = NoOrder::with(['employees' => function ($query) {
                return $query->select('employees.name', 'employees.id');
            }])->with(['clients' => function ($query) {
                return $query->select('clients.company_name', 'clients.id');
            }])->with('images')->whereCompanyId($this->company_id)->whereClientId($id)->orderBy('no_orders.id', 'desc')->offset($offset)->limit($limit)->get()->map(function ($noorder) {
                return $this->formatZeroOrder($noorder);
                // return $noorder;
            });
            $next_fetch = NoOrder::whereCompanyId($this->company_id)->whereClientId($id)
                ->offset($offset + $limit)->limit(1)->first();
            $response = [
                "success" => true,
                "offset" => $offset,
                "new_offset" => $offset + $limit,
                "next_fetch" => $next_fetch ? true : false,
                "limit" => $limit,
                "client_id" => $id,
                "data" => $noorders,
            ];
        } catch (\Exception $e) {
            Log::error(array("Fetch Client Zero Order () => "), array($e->getMessage()));
            $response = [
                "success" => false,
                "msg" => "Some Error Occured while fetching records.",
                "offset" => $offset,
                "limit" => $limit,
                "client_id" => $id,
                "data" => null,
            ];
        }

        return response()->json($response);
    }

    private function formatZeroOrder($zeroorder)
    {
        $formatted_images = $zeroorder->images->map(function ($image) {
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
                'id' => $zeroorder->id,
                'unique_id' => $zeroorder->unique_id,
                'client_id' => $zeroorder->client_id,
                'company_name' => $zeroorder->clients->company_name,
                'employee_id' => $zeroorder->employee_id,
                'employee_name' => $zeroorder->employees->name,
                'date' => $zeroorder->date,
                'unix_timestamp' => $zeroorder->unix_timestamp,
                'remark' => $zeroorder->remark,
                // 'images' => $zeroorder->images->map(function($image) {
                //               return $this->formatImages($image);
                //             }),
                "image_ids" => empty($image_ids) ? null : json_encode($image_ids, true),
                "images" => empty($images) ? null : json_encode($images, true),
                "image_paths" => empty($image_paths) ? null : json_encode($image_paths, true),
                'editable' => Auth::user()->can('zeroorder-update'),
                'deleteable' => Auth::user()->can('zeroorder-delete'),
            ];

            return $formatted_data;
        } catch (\Exception $e) {
            Log::error(array("Format Zero Order () => "), array($e->getMessage()));
            return array();
        }
    }

    public function fetchClientNotes(Request $request)
    {
        $offset = $request->offset;
        $limit = $request->limit;
        $id = $request->client_id;

        try {
            $notes = Note::with(['employee' => function ($query) {
                return $query->select('employees.name', 'employees.id');
            }])->with(['clients' => function ($query) {
                return $query->select('clients.company_name', 'clients.id');
            }])->with('images')->whereCompanyId($this->company_id)->whereClientId($id)->orderBy('meetings.id', 'desc')->offset($offset)->limit($limit)->get()->map(function ($note) {
                return $this->formatNote($note);
                // return $note;
            });
            $next_fetch = Note::whereCompanyId($this->company_id)->whereClientId($id)
                ->offset($offset + $limit)->limit(1)->first();
            $response = [
                "success" => true,
                "offset" => $offset,
                "new_offset" => $offset + $limit,
                "limit" => $limit,
                "next_fetch" => $next_fetch ? true : false,
                "client_id" => $id,
                "data" => $notes,
            ];
        } catch (\Exception $e) {
            Log::error(array("Fetch Client Note () => "), array($e->getMessage()));
            $response = [
                "success" => false,
                "msg" => "Some Error Occured while fetching records.",
                "offset" => $offset,
                "limit" => $limit,
                "client_id" => $id,
                "data" => null,
            ];
        }

        return response()->json($response);
    }

    private function formatNote($note)
    {
        $formatted_images = $note->images->map(function ($image) {
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
                'id' => $note->id,
                'unique_id' => $note->unique_id,
                'client_id' => $note->client_id,
                'company_name' => $note->clients->company_name,
                'employee_id' => $note->employee_id,
                'employee_name' => $note->employee ? $note->employee->name : "Employee Name",
                'date' => $note->meetingdate,
                'time' => $note->checkintime,
                'remark' => $note->remark,
                "image_ids" => json_encode($image_ids, true),
                "images" => json_encode($images, true),
                "image_paths" => json_encode($image_paths, true),
                'editable' => Auth::user()->can('note-update'),
                'deleteable' => Auth::user()->can('note-delete'),
            ];

            return $formatted_data;
        } catch (\Exception $e) {
            Log::error(array("Format Zero Order () => "), array($e->getMessage()));
            return array();
        }
    }

    public function fetchClientActivities(Request $request)
    {
        $offset = $request->offset;
        $limit = $request->limit;
        $id = $request->client_id;

        try {
            $activities = Activity::with(['createdByEmployee' => function ($query) {
                return $query->select('employees.name', 'employees.id');
            }])->with(['assignedTo' => function ($query) {
                return $query->select('employees.name', 'employees.id');
            }])->with(['completedByEmployee' => function ($query) {
                return $query->select('employees.name', 'employees.id');
            }])->with(['activityType' => function ($query) {
                return $query->select('activity_types.name', 'activity_types.id');
            }])->with(['activityPriority' => function ($query) {
                return $query->select('activity_priorities.name', 'activity_priorities.id');
            }])->with(['client' => function ($query) {
                return $query->select('clients.company_name', 'clients.id');
            }])->whereCompanyId($this->company_id)->whereClientId($id)->orderBy('activities.id', 'desc')->offset($offset)->limit($limit)->get()->map(function ($activity) {
                return $this->formatActivity($activity);
                // return $activity;
            });
            $next_fetch = Activity::whereCompanyId($this->company_id)->whereClientId($id)
                ->offset($offset + $limit)->limit(1)->first();
            $response = [
                "success" => true,
                "offset" => $offset,
                "new_offset" => $offset + $limit,
                "next_fetch" => $next_fetch ? true : false,
                "limit" => $limit,
                "client_id" => $id,
                "data" => $activities,
            ];
        } catch (\Exception $e) {
            Log::error(array("Fetch Client Activity () => "), array($e->getMessage()));
            $response = [
                "success" => false,
                "msg" => "Some Error Occured while fetching records.",
                "offset" => $offset,
                "limit" => $limit,
                "client_id" => $id,
                "data" => null,
            ];
        }

        return response()->json($response);
    }

    private function formatActivity($activity)
    {
        try {
            $formatted_data = [
                'id' => $activity->id,
                'unique_id' => $activity->unique_id,
                'title' => $activity->title,
                'note' => $activity->note,
                'client_id' => $activity->client_id,
                'company_name' => $activity->client->company_name,
                'creator_employee_id' => $activity->created_by,
                'creator_employee_name' => $activity->createdByEmployee->name,
                'assigned_employee_id' => $activity->assigned_to,
                'assigned_employee_name' => $activity->assignedTo->name,
                'completor_employee_id' => $activity->completed_by,
                'completor_employee_name' => $activity->completed_by ? $activity->completedByEmployee->name : null,
                'activity_type_id' => $activity->type,
                'activity_type_name' => $activity->activityType->name,
                'activity_priority_id' => $activity->priority,
                'activity_priority_name' => $activity->activityPriority->name,
                'editeable' => (Auth::user()->isCompanyManager() || Auth::user()->employee->id == $activity->created_by || Auth::user()->employee->id == $activity->assigned_to) && Auth::user()->can('activity-update'),
                'deleteable' => (Auth::user()->isCompanyManager() && Auth::user()->can('activity-delete')) || ($activity->created_by == Auth::user()->employee->id && Auth::user()->can('activity-delete')),
            ];

            return $formatted_data;
        } catch (\Exception $e) {
            Log::error(array("Format Activity () => "), array($e->getMessage()));
            return array();
        }
    }

    public function fetchClientVisits(Request $request)
    {
        $offset = $request->offset;
        $limit = $request->limit;
        $id = $request->client_id;

        try {
            $client_visits = ClientVisit::whereCompanyId($this->company_id)->whereClientId($id)->with(['employee' => function ($query) {
                return $query->select('employees.name', 'employees.id');
            }])->with(['client' => function ($query) {
                return $query->select('clients.company_name', 'clients.id');
            }])->with(['visitpurpose' => function ($query) {
                return $query->select('visit_purposes.title', 'visit_purposes.id');
            }])->with('images')->orderBy('client_visits.id', 'desc')->offset($offset)->limit($limit)->get()->map(function ($client_visit_purpose) {
                return $this->formatClientVisit($client_visit_purpose);
            });
            $next_fetch = ClientVisit::whereCompanyId($this->company_id)->whereClientId($id)
                ->offset($offset + $limit)->limit(1)->first();
            $response = [
                "success" => true,
                "offset" => $offset,
                "new_offset" => $offset + $limit,
                "next_fetch" => $next_fetch ? true : false,
                "limit" => $limit,
                "client_id" => $id,
                "data" => $client_visits,
            ];
        } catch (\Exception $e) {
            Log::error(array("Fetch Client Visit () => "), array($e->getMessage()));
            $response = [
                "success" => false,
                "msg" => "Some Error Occured while fetching records.",
                "offset" => $offset,
                "limit" => $limit,
                "client_id" => $id,
                "data" => null,
            ];
        }

        return response()->json($response);
    }

    private function formatClientVisit($object)
    {
        try {
            $formatted_images = $object->images->map(function ($image) {
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

            $formatted_data = [
                'id' => $object->id,
                'unique_id' => $object->unique_id,
                'client_id' => $object->client_id,
                'client_name' => $object->client->company_name,
                'employee_id' => $object->employee_id,
                'employee_name' => $object->employee->name,
                'visit_purpose_id' => $object->visitpurpose ? $object->visitpurpose->id : null,
                'visit_purpose' => $object->visitpurpose ? $object->visitpurpose->title : null,
                "date" => $object->date,
                "start_time" => $object->start_time,
                "end_time" => $object->end_time,
                "comments" => $object->comments,
                "image_ids" => json_encode($image_ids, true),
                "images" => json_encode($images, true),
                "image_paths" => json_encode($image_paths, true),
            ];

            return $formatted_data;
        } catch (\Exception $e) {
            Log::info($object);
            Log::error(array("Format Client Visit Purpose () => "), array($e->getMessage()));
            return array();
        }
    }

    public function getFiles(Request $request)
    {
        $this->validate($request, [
            'client_id' => 'required',
            'type' => 'required',
        ]);
        $client_id = $request->client_id;
        $type = $request->type;
        $folder_with_details = PartyUploadFolder::with(['client' => function ($query) {
            return $query->select('clients.company_name', 'clients.id');
        }])->whereClientId($client_id)
            ->whereType($type)
            ->with('files')
            ->get()->map(function ($folder_with_detail) {
            return $this->formatUploads($folder_with_detail);
        });
        $data = [
            'status' => 200,
            'message' => 'Success.',
            'data' => $folder_with_details,
        ];
        return response()->json($data);
    }

    private function formatUploads($object)
    {

        if ($object->files->first()) {
            $uploaded_item = $object->files->map(function ($item) {
                return $this->formatUploadedItems($item);
            });
        } else {
            $uploaded_item = null;
        }

        $data = [
            'id' => $object->id,
            'client_id' => $object->client->id,
            'client_name' => $object->client->company_name,
            'folder_name' => $object->name,
            'type' => $object->type,
            'uploaded_items' => $uploaded_item,
        ];

        return $data;
    }

    private function formatUploadedItems($object)
    {

        $data = [
            'id' => $object->id,
            'employee_id' => $object->employee_id,
            'employee_name' => $object->employee->name,
            'file_name' => $object->file_name,
            'original_file_name' => $object->original_file_name,
            'extension' => $object->extension,
            'size_in_bytes' => $object->file_size,
            'path' => Storage::disk('s3')->url($object->url),
            'last_modified' => date('Y-m-d H:i:s', strtotime($object->updated_at)),
        ];

        return $data;
    }

    public function createUploadFolder(Request $request)
    {
        $companyID = $this->company_id;
        $clientID = $request->client_id;
        $type = $request->type;

        // $customMessages = [
        //     'folder_name.required' => 'Please specify name for folder.',
        //     'folder_name.unique' => 'Folder name already exists.',
        // ];

        // $this->validate($request, [
        //     "folder_name" => "required|unique:party_upload_folders,name,NULL,deleted_at,client_id,$clientID,type,$type",
        // ], $customMessages);
        if(!$request->folder_name) return response()->json([
          "message" => "Please specify name for folder.",
          "errors" => [],
          'status' => 422,
        ], 200);

        $checkifFolderExists = PartyUploadFolder::whereClientId($clientID)->whereType($type)->where('name', 'LIKE', $request->folder_name)->first();
        if($checkifFolderExists) return response()->json([
          "message" => "Folder name already exists.",
          "errors" => [],
          'status' => 422,
        ], 200);

        try {
            $folderName = $request->folder_name;
            $folder_instance = new PartyUploadFolder;
            $folder_instance->company_id = $companyID;
            $folder_instance->client_id = $clientID;
            $folder_instance->type = $type;
            $folder_instance->name = $folderName;
            $saved = $folder_instance->save();

            $data = [
                'status' => 200,
                'message' => 'Folder Created successfully.',
                'data' => $this->formatUploads(PartyUploadFolder::whereId($folder_instance->id)->with(['client' => function ($query) {
                    return $query->select('clients.company_name', 'clients.id');
                }])->with('files')->first()),
            ];
        } catch (\Exception $e) {
            $data = [
                'status' => 404,
                'message' => $e->getMessage(),
                'data' => null,
            ];
        }

        return response()->json($data);

    }

    public function updateUploadFolder(Request $request)
    {
        $companyID = $this->company_id;
        $type = $request->type;
        $folderId = $request->folder_id;
        $folder_instance = PartyUploadFolder::find($folderId);
        $folderName = $request->folder_name;
        $clientID = $folder_instance->client_id;

        // $customMessages = [
        //     'folder_name.required' => 'Please specify name for folder.',
        //     'folder_name.unique' => 'Folder name already exists.',
        // ];

        // $this->validate($request, [
        //     "folder_name" => "required|unique:party_upload_folders,name,$folderId,id,deleted_at,NULL,client_id,$clientID,type,$type",
        //     "folder_id" => 'required',
        // ], $customMessages);

        if(!$folderName) return response()->json([
          "message" => "Please specify name for folder.",
          "errors" => [],
          'status' => 422,
        ], 200);

        $checkifFolderExists = PartyUploadFolder::whereClientId($clientID)->where('id', '<>', $folderId)->whereType($type)->where('name', 'LIKE', $folderName)->first();
        if($checkifFolderExists) return response()->json([
          "message" => "Folder name already exists.",
          "errors" => [],
          'status' => 422,
        ], 200);

        try {
            $folder_instance = PartyUploadFolder::find($folderId);
            $folderName = $request->folder_name;
            $folder_instance->company_id = $companyID;
            // $folder_instance->client_id = $clientID;
            // $folder_instance->type = $type;
            $folder_instance->name = $folderName;
            $saved = $folder_instance->save();

            $data = [
                'status' => 200,
                'message' => 'Folder Updated successfully.',
                'data' => $this->formatUploads(PartyUploadFolder::whereId($folderId)->with(['client' => function ($query) {
                    return $query->select('clients.company_name', 'clients.id');
                }])->with('files')->first()),
            ];
        } catch (\Exception $e) {
            $data = [
                'status' => 404,
                'message' => $e->getMessage(),
                'data' => null,
            ];
        }

        return response()->json($data);
    }

    public function deleteUploadFolder(Request $request)
    {
        $companyID = $this->company_id;
        $folderId = $request->folder_id;

        $customMessages = [
            'folder_id.required' => 'Missing required parameters.',
        ];

        $this->validate($request, [
            "folder_id" => "required",
        ], $customMessages);

        try {
            $folder_instance = PartyUploadFolder::findOrFail($folderId);
            $type = $folder_instance->type;
            $clientID = $folder_instance->client_id;
            $hasFiles = $folder_instance->files;
            if ($hasFiles->first()) {
                $companyName = Auth::user()->companyName($companyID)->domain;
                $client = Client::find($clientID);
                if ($client) {
                    $client_name = $client->company_name;
                } else {
                    return response()->json([
                        'data' => null,
                        'status' => 403,
                        'message' => 'Party doesn\'t exists',
                        'type' => $type,
                    ]);
                }
                $aws_upload_folder = env('AWS_UPLOADS');
                $upload_folder = $aws_upload_folder . '/' . $companyName . '/' . $client_name . '/' . $type . '/folder_id_' . $folderId;
                $exists_folder = Storage::disk('s3')->exists($upload_folder);
                if ($exists_folder) {
                    Storage::disk('s3')->deleteDirectory($upload_folder);
                }
                $folder_instance->files()->delete();
            }
            $folder_instance->delete();
            $data = [
                'status' => 200,
                'message' => 'Folder Deleted successfully.',
                'data' => $folder_instance,
            ];
        } catch (\Exception $e) {
            $data = [
                'status' => 404,
                'message' => 'Folder cannot be deleted.',
                'data' => $e->getMessage(),
            ];
        }

        return response()->json($data);

    }

    private function getSettings($company_id)
    {
        $settings = ClientSetting::whereCompanyId($company_id)->first();

        return $settings;
    }

    private function getBase64ImageSize($base64Image)
    { //return memory size in B, KB, MB
        try {
            $size_in_bytes = (int) (strlen(rtrim($base64Image, '=')) * 3 / 4);
            $size_in_kb = $size_in_bytes / 1024;
            $size_in_mb = $size_in_kb / 1024;

            return $size_in_bytes;
        } catch (Exception $e) {
            return $e;
        }
    }

    public function uploadFolderItems(Request $request)
    {
        $customMessages = [
            'chosenUpload.required' => 'Please upload a file.',
        ];

        $this->validate($request, [
            'chosenUpload' => 'required',
        ], $customMessages);
        $company_id = $this->company_id;

        $type = $request->type;
        $uploadedFiles = $request->only('chosenUpload');
        $originalNames = $request->originalName;
        $upload_settings = $this->getSettings($company_id);

        if ($type == "files") {
            $upload_types = $upload_settings->party_file_upload_types;
            $upload_size = $upload_settings->party_file_upload_size;
            $allowed_upload_company = $upload_settings->total_file_size_gb;
        } elseif ($type == "images") {
            $upload_types = $upload_settings->party_image_upload_types;
            $upload_size = $upload_settings->party_image_upload_size;
            $allowed_upload_company = $upload_settings->total_image_size_gb;
        }

        $companyName = Auth::user()->companyName($company_id)->domain;
        $uploadedBy = Auth::user()->employee->id;
        $client_id = $request->client_id;
        $folder_id = $request->folder_id;

        $folder = PartyUploadFolder::find($folder_id);
        if ($folder) {
            $folder_name = $folder->name;
        } else {
            return response()->json([
                "message" => "Folder doesn't exisit.",
                "errors" => [
                    "no_folder" => "Folder doesn't exisit.",
                ],
                'status' => 422,
            ], 200);
        }

        $client = Client::find($client_id);
        if ($client) {
            $client_name = $client->company_name;
        } else {
            return response()->json([
                "message" => "Client doesn't exisit.",
                "errors" => [
                    "no_client" => "Client doesn't exisit.",
                ],
                'status' => 422,
            ], 200);
        }

        if ($folder->files->count() + sizeof($uploadedFiles["chosenUpload"]) > 20) {
            return response()->json([
                "message" => "Cannot Upload more than 20 items in one folder.",
                "errors" => [
                    "max_folder_uploads" => "Cannot Upload more than 20 items in one folder.",
                ],
                'status' => 422,
            ], 200);
        }

        $aws_upload_folder = env('AWS_UPLOADS');
        $upload_folder = $aws_upload_folder . '/' . $companyName . '/' . $client_name . '/' . $type . '/folder_id_' . $folder_id;
        $storedFiles = array();
        $uploadFiles = array();
        $uploaded_files = PartyUploadFolder::whereCompanyId($company_id)->whereType($type)->whereHas('files')->whereNull('deleted_at')->pluck('client_id')->toArray();
        $uploaded_till_now = PartyUpload::whereIn('client_id', $uploaded_files)->sum('file_size');
        foreach ($uploadedFiles["chosenUpload"] as $key => $file) {
            $sum = 0;
            $sum += $uploaded_till_now;

            try {
                $errors = array();

                $fileData = base64_decode($file);
                // save it to temporary dir first.
                $tmpFilePath = sys_get_temp_dir() . '/' . $originalNames[$key];
                file_put_contents($tmpFilePath, $fileData);
                // this just to help us get file info.
                $tmpFile = new File($tmpFilePath);
                $_file = new UploadedFile(
                    $tmpFile->getPathname(),
                    $tmpFile->getFilename(),
                    $tmpFile->getMimeType(),
                    0,
                    true
                );
                $extension = $_file->extension();
                $realname = pathinfo($_file->getClientOriginalName(), PATHINFO_FILENAME);
                $file_size = $_file->getSize();

                $sum += $file_size;
                if (($sum / 1073741824) > $allowed_upload_company) {
                    $errors['max_upload_size'] = 'Maximum uploads reached.';
                    return response()->json([
                        "message" => $errors['max_upload_size'],
                        "errors" => [
                            $errors,
                        ],
                        'status' => 422,
                    ], 200);
                }
                if ($file_size > ($upload_size * 1024) || !in_array($extension, explode(',', $upload_types))) {
                    if ($file_size > ($upload_size * 1024)) {
                        $errors[0] = 'The file ' . $realname . ' must be less than or equal to ' . $upload_size . ' Kb.';
                    } elseif (!in_array($extension, [$upload_types])) {
                        $errors[0] = 'The file ' . $realname . ' must be one of the mentioned types:' . $upload_types;
                    }
                    return response()->json([
                        "message" => $errors[0],
                        "errors" => [
                            $errors,
                        ],
                        'status' => 422,
                    ], 200);
                }
                array_push($uploadFiles, $_file);
            } catch (\Exception $e) {
                Log::info($e->getMessage());
            }
        }

        foreach ($uploadFiles as $_file) {
            try {
                $extension = $_file->extension();
                $realname = pathinfo($_file->getClientOriginalName(), PATHINFO_FILENAME);
                $file_size = $_file->getClientSize();
                $new_name = $realname . "-" . Str::random(25) . "-" . time();

                $data = $this->upload($_file, $upload_folder, 's3', $new_name);
                $file_name = $data['file_name'];
                $file_path = $data['path'];
                if (!empty($file_path)) {
                    $inserted = PartyUpload::create([
                        'party_upload_folder_id' => $folder_id,
                        'client_id' => $client_id,
                        'employee_id' => $uploadedBy,
                        'original_file_name' => $realname,
                        'file_name' => $file_name,
                        'url' => $file_path,
                        'extension' => $extension,
                        'file_size' => $file_size,
                    ]);
                    array_push($storedFiles, $this->formatUploadedItems($inserted));
                }
            } catch (\Exception $e) {
                Log::info($e->getMessage());
            }
        }

        $folder->update([
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return response()->json([
            'status' => 200,
            'message' => 'Successfully Uploaded',
            'data' => $storedFiles,
            'type' => $type,
        ]);
    }

    public function updateFolderItems(Request $request)
    {
        $this->validate($request, [
            'file_id' => 'required',
            'client_id' => 'required',
            'file_name' => 'required',
        ]);
        $file_id = $request->file_id;
        $client_id = $request->client_id;
        try {
            $file_instance = PartyUpload::find($file_id);
            $updateParams = [
                'original_file_name' => $request->file_name,
            ];
            if ($file_instance->folder->type == "files" && !empty($request->chosenUpload)) {
                $company_id = Auth::user()->company_id;
                $upload_settings = $this->getSettings($company_id);

                $upload_types = $upload_settings->party_file_upload_types;
                $upload_size = $upload_settings->file_upload_size;
                $allowed_upload_company = $upload_settings->total_file_size_gb;

                $customMessages = [
                    'chosenUpload.required' => 'Please upload a file.',
                ];

                $this->validate($request, [
                    'chosenUpload' => 'required',
                ], $customMessages);
                $companyName = Auth::user()->companyName($company_id)->domain;
                $client = Client::find($client_id);
                $client_name = $client->company_name;
                $uploadedBy = Auth::user()->employee->id;
                $folder_id = $file_instance->party_upload_folder_id;
                $aws_upload_folder = env('AWS_UPLOADS');
                $upload_folder = $aws_upload_folder . '/' . $companyName . '/' . $client_name . '/' . $file_instance->folder->type . '/folder_id_' . $folder_id;
                $storedFiles = array();

                $uploaded_files = PartyUploadFolder::whereCompanyId($company_id)->whereType($file_instance->folder->type)->whereHas('files')->whereNull('deleted_at')->pluck('client_id')->toArray();
                $uploaded_till_now = PartyUpload::whereIn('client_id', $uploaded_files)->where('id', '<>', $file_id)->sum('file_size');
                $sum = 0;
                $sum += $uploaded_till_now;
                try {
                    $errors = array();
                    $file = $request->chosenUpload;
                    $fileData = base64_decode($file);
                    // save it to temporary dir first.
                    $tmpFilePath = sys_get_temp_dir() . '/' . $request->file_name;
                    file_put_contents($tmpFilePath, $fileData);
                    // this just to help us get file info.
                    $tmpFile = new File($tmpFilePath);
                    $_file = new UploadedFile(
                        $tmpFile->getPathname(),
                        $tmpFile->getFilename(),
                        $tmpFile->getMimeType(),
                        0,
                        true
                    );
                    $extension = $_file->extension();
                    $realname = pathinfo($_file->getClientOriginalName(), PATHINFO_FILENAME);
                    $file_size = $_file->getSize();

                    $sum += $file_size;
                    if (($sum / 1073741824) > $allowed_upload_company) {
                        $errors['max_upload_size'] = 'Maximum uploads reached.';
                        return response()->json([
                            "message" => "Maximum uploads reached.",
                            "errors" => [
                                $errors,
                            ],
                            'status' => 422,
                        ], 200);
                    }
                    if ($file_size > ($upload_size * 1024) || !in_array($extension, explode(',', $upload_types))) {
                        if ($file_size > ($upload_size * 1024)) {
                            $errors[0] = 'The file ' . $realname . ' must be less than or equal to ' . $upload_size . ' Kb.';
                        } elseif (!in_array($extension, explode(',', $upload_types))) {
                            $errors[0] = 'The file ' . $realname . ' must be one of the mentioned types:' . $upload_types;
                        }
                        return response()->json([
                            "message" => $errors[0],
                            "errors" => [
                                $errors,
                            ],
                            'status' => 422,
                        ], 200);
                    }
                    $new_name = $realname . "-" . Str::random(25) . "-" . time();
                    $data = $this->upload($_file, $upload_folder, 's3', $new_name);

                    $file_name = $data['file_name'];
                    $file_path = $data['path'];
                    $updateParams['employee_id'] = $uploadedBy;
                    $updateParams['file_name'] = $file_name;
                    $updateParams['url'] = $file_path;
                    $updateParams['extension'] = $extension;
                    $updateParams['file_size'] = $file_size;
                    $exists_folder = Storage::disk('s3')->exists($file_instance->url);
                    if ($exists_folder) {
                        Storage::disk('s3')->delete($file_instance->url);
                    }
                } catch (\Exception $e) {
                    Log::alert($e->getMessage());
                }
            }
            $file_instance->update($updateParams);

            $folder = PartyUploadFolder::find($file_instance->party_upload_folder_id);
            $folder->update([
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            $data = [
                'data' => $this->formatUploadedItems($file_instance),
                'status' => 200,
                'message' => 'Successfully Updated',
                'type' => $request->type,
            ];
        } catch (\Exception $e) {
            $data = [
                'status' => 404,
                'message' => $e->getMessage(),

            ];
        }
        return response()->json($data);
    }

    public function deleteFolderItems(Request $request)
    {
        $this->validate($request, [
            'file_id' => 'required',
        ]);
        $company_id = $this->company_id;
        $file_id = $request->file_id;
        $companyName = Auth::user()->companyName($company_id)->domain;

        try {
            $file_instance = PartyUpload::findOrFail($file_id);
            $folder_instance = PartyUploadFolder::findOrFail($file_instance->party_upload_folder_id);
            $client_id = $folder_instance->client_id;
            $type = $folder_instance->type;
            $client = Client::find($client_id);
            if ($client) {
                $client_name = $client->company_name;
            } else {
                return response()->json([
                    'status' => 403,
                    'message' => 'Party doesn\'t exists',
                    'data' => null,
                    'type' => $type,
                ]);
            }
            $aws_upload_folder = env('AWS_UPLOADS');
            $upload_folder = $aws_upload_folder . '/' . $companyName . '/' . $client_name . '/' . $type . '/folder_id_' . $file_instance->party_upload_folder_id;
            $exists_folder = Storage::disk('s3')->exists($file_instance->url);

            if ($exists_folder) {
                Storage::disk('s3')->delete($file_instance->url);
            }

            $file_instance->delete();

            $data = [
                'status' => 200,
                'message' => 'Successfully Deleted',
                'data' => $file_instance,
                'type' => $type,
            ];
        } catch (\Exception $e) {
            $data = [
                'status' => 404,
                'message' => $e->getMessage(),
            ];
        }
        return response()->json($data);
    }

}
