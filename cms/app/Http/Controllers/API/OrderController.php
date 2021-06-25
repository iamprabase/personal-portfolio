<?php

namespace App\Http\Controllers\API;

use App\OrderScheme;
use App\Scheme;
use DB;
use Auth;
use App\Order;
use App\Client;
use App\Outlet;
use App\Employee;
use Carbon\Carbon;
use App\OrderDetails;
use App\ClientSetting;
use App\ModuleAttribute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Symfony\Component\Debug\Exception\FatalThrowableError;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('permission:order-create', ['only' => ['store']]);
        $this->middleware('permission:order-view');
        $this->middleware('permission:order-update', ['only' => ['update']]);
        $this->middleware('permission:order-delete', ['only' => ['destroy']]);
    }

    public function getOrderById(Request $request){
      $orderID = $request->order_id;

      $orders = Order::where('orders.id', $orderID)
            ->leftJoin('employees', 'orders.employee_id', 'employees.id')
            ->leftJoin('clients', 'orders.client_id', 'clients.id')
            ->leftJoin('client_settings', 'orders.company_id', 'client_settings.company_id')
            ->leftJoin('module_attributes', 'orders.delivery_status_id', 'module_attributes.id')
            ->select('orders.*', 'employees.name as employee_name', 'clients.company_name as company_name', 'clients.name as client_name', 'client_settings.order_prefix', 'module_attributes.id as moduleattributesId', 'module_attributes.title as delivery_status', 'module_attributes.color', 'module_attributes.order_amt_flag', 'module_attributes.order_edit_flag', 'module_attributes.order_delete_flag')
            ->first();
        if ($orders) {
            $moduleAttributes = ModuleAttribute::where('company_id', $orders->company_id)->get();

            $product_level_tax_flag = $orders->product_level_tax_flag;
            $productLines = $this->getProductLines($orders->id, $product_level_tax_flag);
            if ($product_level_tax_flag == 1) {
                $orders->taxes = null;
            } else {
                $orders->taxes = $this->getTaxes($orders->id);
            }
            $orders->orderproducts = $productLines;
            if ($orders->delivery_status) {
                $delivery_status_color = $moduleAttributes->where('title', '=', $orders->delivery_status)->first();
                if ($delivery_status_color) {
                    $orders->delivery_status_color = $delivery_status_color->color;
                } else {
                    $orders->delivery_status_color = null;
                }
            } else {
                $orders->delivery_status_color = null;
            }

            if ($orders->employee_id == 0 && $orders->outlet_id) {
                $orders->employee_name = $orders->outlets()->withTrashed()->first() ? $orders->outlets()->withTrashed()->first()->contact_person . " (O)" : "";
            }
            $scheme_response = $orders->orderScheme->toArray();

            $dataPayload = array("data_type" => "order", "order" => $orders, "scheme_response" => $scheme_response);
            
            return response()->json($dataPayload);
        }


    }

    public function index($return = false, $tempPostData = null)
    {
        $postData = $return ? $tempPostData : $this->getJsonRequest();
        $user = Auth::user();
        $companyID = $user->company_id;
        $employee = Employee::where('company_id', $companyID)->where('user_id', $user->id)->first();
        $employeeID = $employee->id;
        $employeeName = $this->getArrayValue($postData, "employee_name");

        /*Check if unsynced data is available . if available first update to tha database */
        $syncStatus = $this->manageUnsyncedOrder($postData);
        // $fetch_type = $this->getArrayValue($postData, "fetch_type");
        // $startDateFilter = $this->getArrayValue($postData, 'start_date');
        // $endDateFilter = $this->getArrayValue($postData, 'end_date');
        // $sel_parties = $this->getArrayValue($postData, "party_ids");
        // $party_ids = isset($sel_parties)?json_decode($sel_parties):null;
        // $sel_statuses = $this->getArrayValue($postData, 'order_status');
        // $statuses = isset($sel_statuses)?json_decode($sel_statuses):null;
        // $search = $this->getArrayValue($postData, "search");
        // $offset = $this->getArrayValue($postData, "offset");
        // $length = $this->getArrayValue($postData, "length");

        // $getOrders = Order::leftJoin('employees', 'orders.employee_id', 'employees.id')
        //             ->leftJoin('clients', 'orders.client_id', 'clients.id')
        //             ->leftJoin('client_settings', 'orders.company_id', 'client_settings.company_id')
        //             ->leftJoin('module_attributes', 'orders.delivery_status_id', 'module_attributes.id')
        //             ->leftJoin('companies', 'companies.id', 'orders.company_id')
        //             ->where('orders.company_id', $companyID)
        //             ->whereBetween('orders.order_date',[$startDateFilter, $endDateFilter])
        //             ->whereNull('orders.deleted_at');
        //             if($fetch_type=="all"){
        //               if($employee->is_admin==1){
        //                 $getOrders = $getOrders;
        //               }else{
        //                 $juniorChains = Employee::employeeChilds($employeeID, array());
        //                 $getOrders = $getOrders->whereIn('orders.employee_id', $juniorChains);
        //               }
        //             }elseif($fetch_type=="single"){
        //               $getOrders = $getOrders->where('orders.employee_id', $employeeID);
        //             }
        //             if(isset($search)){
        //               $getOrders = $getOrders->where(function ($query) use ($search) {
        //                 $query->orWhere('employees.name', 'LIKE', "%{$search}%");
        //                 $query->orWhere('clients.company_name', 'LIKE', "%{$search}%");
        //                 $query->orWhere('module_attributes.title', 'LIKE', "%{$search}%");
        //                 $query->orWhere(\DB::raw("Concat(client_settings.order_prefix ,orders.order_no)"), 'LIKE', "%{$search}%");
        //               });
        //             }

        //             if(isset($party_ids)){
        //               $getOrders = $getOrders->whereIn('orders.client_id', $party_ids);
        //             }

        //             if(isset($statuses)){
        //               $getOrders = $getOrders->whereIn('orders.delivery_status_id', $statuses);
        //             }
        // $orders = $getOrders
        //           ->offset($offset)
        //           ->limit($length)
        //           ->get(['orders.*', 'employees.name as employee_name', 'clients.company_name as company_name', 'clients.name as client_name', 'client_settings.order_prefix', 'module_attributes.id as moduleattributesId', 'module_attributes.title as delivery_status', 'module_attributes.color', 'module_attributes.order_amt_flag', 'module_attributes.order_edit_flag', 'module_attributes.order_delete_flag', 'companies.company_name as companyName']);

        $orders = Order::leftJoin('employees', 'orders.employee_id', 'employees.id')
            ->leftJoin('clients', 'orders.client_id', 'clients.id')
            ->leftJoin('client_settings', 'orders.company_id', 'client_settings.company_id')
            ->leftJoin('module_attributes', 'orders.delivery_status_id', 'module_attributes.id')
            ->leftJoin('companies', 'companies.id', 'orders.company_id')
            ->select('orders.*', 'employees.name as employee_name', 'clients.company_name as company_name', 'clients.name as client_name', 'client_settings.order_prefix', 'module_attributes.id as moduleattributesId', 'module_attributes.title as delivery_status', 'module_attributes.color', 'module_attributes.order_amt_flag', 'module_attributes.order_edit_flag', 'module_attributes.order_delete_flag', 'companies.company_name as companyName')
            ->where('orders.company_id', $companyID)
            ->where('orders.employee_id', $employeeID)
            ->whereNull('orders.deleted_at')
            ->get();

        if (empty($orders)) {
            if ($return) return array();
            else $this->sendEmptyResponse();
        }

        // $updateable = false;
        // $deleteable = false;
        // $updateDeleteFlag = $user->can('order-status');
        // $finalArray = array("updateDeleteFlag"=>$updateDeleteFlag);
        $finalArray = array();
        $moduleAttributes = ModuleAttribute::where('company_id', $companyID)->get();
        foreach ($orders as $key => $value) {
            if ($value->employee_id == 0) {
                $outlet_exists = $value->outlets;
                if ($outlet_exists) {
                    $value->employee_name = $outlet_exists->contact_person;
                }
            }
            $product_level_tax_flag = $value->product_level_tax_flag;
            $productLines = $this->getProductLines($value->id, $product_level_tax_flag);
            if ($product_level_tax_flag == 1) {
                $value->taxes = null;
            } else {
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
            //   if($updateDeleteFlag){
            //   if($value->order_edit_flag == 1 && $user->can('order-update')){
            //     $updateable = $user->can('order-update');
            //   }
            //   if($value->order_delete_flag == 1 && $user->can('order-delete')){
            //     $deleteable = $user->can('order-delete');
            //   }
            // }

            // $value->updateable =  $updateable;
            // $value->deleteable =  $deleteable;
            array_push($finalArray, $value);
        }
        $response = array("status" => true, "message" => "Success", "data" => $finalArray);
        if ($return) return $finalArray;
        else $this->sendResponse($response);
    }

    public function fetchAdminOrders($return = false, $tempPostData = null)
    {
        $postData = $return ? $tempPostData : $this->getJsonRequest();
        $user = Auth::user();
        $companyID = $user->company_id;
        date_default_timezone_set($this->getTimeZone($companyID));
        $employee = Employee::where('company_id', $companyID)->where('user_id', $user->id)->first();
        $employeeID = $employee->id;
        $employeeName = $this->getArrayValue($postData, "employee_name");

        /*Check if unsynced data is available . if available first update to tha database */
        $syncStatus = $this->manageUnsyncedOrder($postData);
        // $fetch_type = $this->getArrayValue($postData, "fetch_type");
        // $startDateFilter = $this->getArrayValue($postData, 'start_date');
        // $endDateFilter = $this->getArrayValue($postData, 'end_date');
        // $sel_parties = $this->getArrayValue($postData, "party_ids");
        // $party_ids = isset($sel_parties)?json_decode($sel_parties):null;
        // $sel_statuses = $this->getArrayValue($postData, 'order_status');
        // $statuses = isset($sel_statuses)?json_decode($sel_statuses):null;
        // $search = $this->getArrayValue($postData, "search");
        $offset = $this->getArrayValue($postData, "offset");
        $length = $this->getArrayValue($postData, "data_limit");

        // $getOrders = Order::leftJoin('employees', 'orders.employee_id', 'employees.id')
        //             ->leftJoin('clients', 'orders.client_id', 'clients.id')
        //             ->leftJoin('client_settings', 'orders.company_id', 'client_settings.company_id')
        //             ->leftJoin('module_attributes', 'orders.delivery_status_id', 'module_attributes.id')
        //             ->leftJoin('companies', 'companies.id', 'orders.company_id')
        //             ->where('orders.company_id', $companyID)
        //             ->whereBetween('orders.order_date',[$startDateFilter, $endDateFilter])
        //             ->whereNull('orders.deleted_at');
        //             if($fetch_type=="all"){
        //               if($employee->is_admin==1){
        //                 $getOrders = $getOrders;
        //               }else{
        // $juniorChains = Employee::employeeChilds($employeeID, array());
        //                 $getOrders = $getOrders->whereIn('orders.employee_id', $juniorChains);
        //               }
        //             }elseif($fetch_type=="single"){
        //               $getOrders = $getOrders->where('orders.employee_id', $employeeID);
        //             }
        //             if(isset($search)){
        //               $getOrders = $getOrders->where(function ($query) use ($search) {
        //                 $query->orWhere('employees.name', 'LIKE', "%{$search}%");
        //                 $query->orWhere('clients.company_name', 'LIKE', "%{$search}%");
        //                 $query->orWhere('module_attributes.title', 'LIKE', "%{$search}%");
        //                 $query->orWhere(\DB::raw("Concat(client_settings.order_prefix ,orders.order_no)"), 'LIKE', "%{$search}%");
        //               });
        //             }

        //             if(isset($party_ids)){
        //               $getOrders = $getOrders->whereIn('orders.client_id', $party_ids);
        //             }

        //             if(isset($statuses)){
        //               $getOrders = $getOrders->whereIn('orders.delivery_status_id', $statuses);
        //             }
        // $orders = $getOrders
        //           ->offset($offset)
        //           ->limit($length)
        //           ->get(['orders.*', 'employees.name as employee_name', 'clients.company_name as company_name', 'clients.name as client_name', 'client_settings.order_prefix', 'module_attributes.id as moduleattributesId', 'module_attributes.title as delivery_status', 'module_attributes.color', 'module_attributes.order_amt_flag', 'module_attributes.order_edit_flag', 'module_attributes.order_delete_flag', 'companies.company_name as companyName']);
        $juniorChains = Employee::employeeChilds($employeeID, array());
        if ($employee->is_admin == 1) {
            $orders = Order::with('ordertos','orderScheme')->leftJoin('employees', 'orders.employee_id', 'employees.id')
                ->leftJoin('clients', 'orders.client_id', 'clients.id')
                ->leftJoin('client_settings', 'orders.company_id', 'client_settings.company_id')
                ->leftJoin('module_attributes', 'orders.delivery_status_id', 'module_attributes.id')
                ->leftJoin('companies', 'companies.id', 'orders.company_id')
                ->select('orders.*', 'employees.name as employee_name', 'clients.company_name as company_name', 'clients.name as client_name', 'client_settings.order_prefix', 'module_attributes.id as moduleattributesId', 'module_attributes.title as delivery_status', 'module_attributes.color', 'module_attributes.order_amt_flag', 'module_attributes.order_edit_flag', 'module_attributes.order_delete_flag', 'companies.company_name as companyName')
                ->where('orders.company_id', $companyID);
        } else {
            // $juniorChains = Employee::employeeChilds($employeeID, array());
            $employee_handles = DB::table('handles')->where('company_id', $companyID)->where('employee_id', $employeeID)->pluck('client_id')->toArray();
            $order_ids = Order::where('id', '>', "$offset")->whereNotNull('outlet_id')->whereIn('client_id', $employee_handles)->pluck('id')->toArray();

            if (!empty($order_ids)) array_push($juniorChains, 0);

            $orders = Order::with('ordertos','orderScheme')->leftJoin('employees', 'orders.employee_id', 'employees.id')
                ->leftJoin('clients', 'orders.client_id', 'clients.id')
                ->leftJoin('client_settings', 'orders.company_id', 'client_settings.company_id')
                ->leftJoin('module_attributes', 'orders.delivery_status_id', 'module_attributes.id')
                ->leftJoin('companies', 'companies.id', 'orders.company_id')
                ->select('orders.*', 'employees.name as employee_name', 'clients.company_name as company_name', 'clients.name as client_name', 'client_settings.order_prefix', 'module_attributes.id as moduleattributesId', 'module_attributes.title as delivery_status', 'module_attributes.color', 'module_attributes.order_amt_flag', 'module_attributes.order_edit_flag', 'module_attributes.order_delete_flag', 'companies.company_name as companyName')
                ->where('orders.company_id', $companyID)
                ->whereIn('orders.employee_id', $juniorChains)->whereIn('orders.client_id', $employee_handles);
        }
        $orders = $orders->whereNull('orders.deleted_at')
            ->where('orders.id', '>', "$offset")
            ->orderBy('orders.id', 'asc')
            // ->offset($offset)
            ->limit($length)
            ->get();
        if (empty($orders)) {
            if ($return) return array();
            else $this->sendEmptyResponse();
        }

        // $updateable = false;
        // $deleteable = false;
        // $updateDeleteFlag = $user->can('order-status');
        // $finalArray = array("updateDeleteFlag"=>$updateDeleteFlag);
        $finalArray = array();
        $moduleAttributes = ModuleAttribute::where('company_id', $companyID)->get();
        foreach ($orders as $key => $value) {
            $product_level_tax_flag = $value->product_level_tax_flag;
            $productLines = $this->getProductLines($value->id, $product_level_tax_flag);
            if ($product_level_tax_flag == 1) {
                $value->taxes = null;
            } else {
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
            //   if($updateDeleteFlag){
            //   if($value->order_edit_flag == 1 && $user->can('order-update')){
            //     $updateable = $user->can('order-update');
            //   }
            //   if($value->order_delete_flag == 1 && $user->can('order-delete')){
            //     $deleteable = $user->can('order-delete');
            //   }
            // }

            // $value->updateable =  $updateable;
            // $value->deleteable =  $deleteable;
            $allow_overall_discount = true;
            if ($value->employee_id != 0 && !$value->outlet_id) {
                $value->employee_name = $value->employee()->withTrashed()->first() ? $value->employee()->withTrashed()->first()->name : "";
            } elseif ($value->outlet_id && $value->employee_id == 0) {
                $allow_overall_discount = false;
                $value->employee_name = $value->outlets()->withTrashed()->first() ? $value->outlets()->withTrashed()->first()->contact_person . " (O)" : "";
            } elseif ($value->outlet_id && $value->employee_id) {
                $allow_overall_discount = false;
                $value->employee_name = $value->employee()->withTrashed()->first() ? $value->employee()->withTrashed()->first()->name : "";
            } else {
                $allow_overall_discount = false;
                $value->employee_name = "";
            }
            $value->allow_overall = $allow_overall_discount;
            $value->order_to_company_name = $value->order_to ? $value->ordertos->company_name : null;
            $value->schemeResponse  = $value->schemeResponse ? $value->schemeResponse->toArray() : null;
            array_push($finalArray, $value);
        }
        $response = array("status" => true, "message" => "Success", "count" => $orders->count(), "employee_ids" => $juniorChains, "data" => $finalArray);
        if ($return) return $finalArray;
        else $this->sendResponse($response);
    }

    public function fetchOrderChanges(Request $request, $return = false, $tempPostData = null)
    {
        $postData = $return ? $tempPostData : $this->getJsonRequest();
        $user = Auth::user();
        $companyID = $user->company_id;
        date_default_timezone_set($this->getTimeZone($companyID));
        $employee = Employee::where('company_id', $companyID)->where('user_id', $user->id)->first();
        $employeeID = $employee->id;
        // $old_token = $request->old_token;
        $fetchToken = $request->fetch_token;
        $lastFetchObject = DB::table('changes_last_fetched')->where('unique_token', $fetchToken)->first();
        $finalArray = array('created_records' => array(), 'updated_records' => array(), 'deleted_records' => array());
        if (!$lastFetchObject) return array();

        $lastFetchedDatetime = $lastFetchObject->order_fetch_datetime;
        DB::beginTransaction();
        // if($old_token){
        //   DB::table('changes_last_fetched')->where('unique_token', $old_token)->delete();
        // }
        // $unique_code = time().substr(uniqid(),3,6);
        DB::table('changes_last_fetched')->updateOrInsert(
            ['unique_token' => $fetchToken],
            ['user_id' => $user->id, 'unique_token' => $fetchToken, 'order_fetch_datetime' => date('Y-m-d H:i:s')]
        );
        DB::commit();
        /*Check if unsynced data is available . if available first update to tha database */
        // $syncStatus = $this->manageUnsyncedOrder($postData);
        // $offset = $this->getArrayValue($postData, "offset");
        // $length = $this->getArrayValue($postData, "data_limit");
        $juniorChains = Employee::employeeChilds($employeeID, array());
        $employee_handles = DB::table('handles')->where('company_id', $companyID)->where('employee_id', $employeeID)->pluck('client_id')->toArray();
        $order_ids = Order::whereIn('client_id', $employee_handles)->where('employee_id', 0)->pluck('id')->toArray();
        if (!empty($order_ids)) {
            array_push($juniorChains, 0);
        }
        $orders = DB::table('orders')->leftJoin('employees', 'orders.employee_id', 'employees.id')
            ->leftJoin('clients', 'orders.client_id', 'clients.id')
            ->leftJoin('client_settings', 'orders.company_id', 'client_settings.company_id')
            ->leftJoin('module_attributes', 'orders.delivery_status_id', 'module_attributes.id')
            ->leftJoin('companies', 'companies.id', 'orders.company_id')
            ->select('orders.*', 'employees.name as employee_name', 'clients.company_name as company_name', 'clients.name as client_name', 'client_settings.order_prefix', 'module_attributes.id as moduleattributesId', 'module_attributes.title as delivery_status', 'module_attributes.color', 'module_attributes.order_amt_flag', 'module_attributes.order_edit_flag', 'module_attributes.order_delete_flag', 'companies.company_name as companyName')
            ->where('orders.company_id', $companyID)
            ->where(function ($query) use ($lastFetchedDatetime) {
                $query->orWhere('orders.created_at', '>', $lastFetchedDatetime);
                $query->orWhere('orders.updated_at', '>', $lastFetchedDatetime);
                $query->orWhere('orders.deleted_at', '>', $lastFetchedDatetime);
            })->where(function ($queryHierarchy) use ($employee, $juniorChains, $employee_handles) {
                if ($employee->is_admin != 1) {
                    $queryHierarchy->whereIn('orders.employee_id', $juniorChains)->whereIn('orders.client_id', $employee_handles);;
                }
            })
            ->orderBy('orders.id', 'asc')
            ->get();
        if (empty($orders)) {
            if ($return) return array();
            else $this->sendEmptyResponse();
        }
        $moduleAttributes = ModuleAttribute::where('company_id', $companyID)->get();
        foreach ($orders as $key => $value) {
            $value->id = (int)$value->id;
            if ($value->deleted_at) {
                $finalArray['deleted_records'][] = (int)$value->id;

                continue;
            }
            $product_level_tax_flag = $value->product_level_tax_flag;
            $productLines = $this->getProductLines($value->id, $product_level_tax_flag);
            if ($product_level_tax_flag == 1) {
                $value->taxes = null;
            } else {
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
            // if($value->employee_id!=0){
            //   $value->employee_name = $value->employee()->withTrashed()->first()?$value->employee()->withTrashed()->first()->name:"";
            // }else{
            //   $value->employee_name = "";
            // }
            $allow_overall_discount = true;
            if ($value->employee_id != 0 && !$value->outlet_id) {
                $employee_instance = $this->getOrderCreatorInstance('employees', $value->employee_id);
                $value->employee_name = $employee_instance ? $employee_instance->name : "";
            } elseif ($value->outlet_id && $value->employee_id == 0) {
                $allow_overall_discount = false;
                $outlet_instance = $this->getOrderCreatorInstance('outlets', $value->outlet_id);
                $value->employee_name = $outlet_instance ? $outlet_instance->contact_person . " (O)" : "";
            } elseif ($value->outlet_id && $value->employee_id) {
                $allow_overall_discount = false;
                $value->employee_name = $value->employee()->withTrashed()->first() ? $value->employee()->withTrashed()->first()->name : "";
            } else {
                $allow_overall_discount = false;
                $value->employee_name = "";
            }
            $value->allow_overall = $allow_overall_discount;

            if (!empty($value->updated_at) && (empty($value->deleted_at) && $value->updated_at != $value->created_at)) $finalArray['updated_records'][] = $value;
            elseif (isset($value->created_at) && ($value->deleted_at == NULL && $value->updated_at == $value->created_at)) $finalArray['created_records'][] = $value;
        }
        $response = array("status" => true, "message" => "Success", "data" => $finalArray);
        if ($return) return $finalArray;
        else $this->sendResponse($response);
    }

    private function getTimeZone($company_id)
    {
        try {
            // $setting = ClientSetting::whereCompanyId($company_id)->first();
            // if($setting->time_zone) $timezone = $setting->time_zone;
            // else
            $timezone = 'Asia/Kathmandu';

            return $timezone;
        } catch (\Exception $e) {
          Log::info(array("getTimeZone OrderController API", $e->getMessage()));
            return 'Asia/Kathmandu';
        }
    }

    private function getOrderCreatorInstance($tablename, $id)
    {
        try {
            $instance = DB::table($tablename)->where('id', $id)->first();

            return $instance;
        } catch (\Exception $e) {
            
          Log::info(array("getOrderCreatorInstance OrderController API", $e->getMessage()));

            return NULL;
        }
    }

    /**
     * Unused
     */
    public function updateOrderStatus($return = false, $tempPostData = null)
    {
        $postData = $return ? $tempPostData : $this->getJsonRequest();
        $user = Auth::user();
        $companyID = $user->company_id;
        $orderId = $this->getArrayValue($postData, "order_id");
        $orderStatusId = $this->getArrayValue($postData, "delivery_status_id");
        $orderDispatchDate = $this->getArrayValue($postData, "delivery_date");
        $orderDispatchPlace = $this->getArrayValue($postData, "delivery_place");
        $transportName = $this->getArrayValue($postData, "transport_name");
        $transportNumber = $this->getArrayValue($postData, "transport_number");
        $billtyNumber = $this->getArrayValue($postData, "billty_number");
        $dispatchNote = $this->getArrayValue($postData, "delivery_note");

        $order = Order::where('company_id', $companyID)->where('id', $orderId)->first();

        if ($order) {
            $order->delivery_status_id = $orderStatusId;
            $order->delivery_date = $orderDispatchDate;
            $order->delivery_place = $orderDispatchPlace;
            $order->transport_name = $transportName;
            $order->transport_number = $transportNumber;
            $order->billty_number = $billtyNumber;
            $order->delivery_note = $dispatchNote;
            $order->update();
            $response = array("status" => true, "message" => "Order Status Updated.");
        } else {
            $response = array("status" => false, "message" => "Order Not Found.");
        }
        $this->sendResponse($response);
    }

    private function enabledAccounting($company_id)
    {
        try {

            $clientSetting = ClientSetting::whereCompanyId($company_id)->first();

            if ($clientSetting) {
                return $clientSetting->accounting;
            } else {
                return 0;
            }
        } catch (Exception $e) {
          Log::info(array("enabledAccounting OrderController API", $e->getMessage()));

            return 0;
        }
    }

    private function getQtyAmt($company_id)
    {
        try {

            $clientSetting = ClientSetting::whereCompanyId($company_id)->first();

            if ($clientSetting) {
                return "$clientSetting->order_with_amt";
            } else {
                return 0;
            }
        } catch (Exception $e) {
          Log::info(array("getQtyAmt OrderController API", $e->getMessage()));

            return 0;
        }
    }

    private function getOutstandingAmount($client)
    {
        try {
            $orders = $client->orders;
            $getOrderStatusFlag = ModuleAttribute::where('company_id', $client->company_id)->where('module_id', 1)->where('order_amt_flag', 1)->pluck('id')->toArray();
            if (!empty($getOrderStatusFlag)) {
                $tot_order_amount = $orders->whereIn('delivery_status_id', $getOrderStatusFlag)->sum('grand_total');
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

            return $outstading_amount;
        } catch (Exception $e) {
          Log::info(array("getOutstandingAmount OrderController API", $e->getMessage()));
            return 0;
        }
    }

    public function store(Request $request)
    {
        $postData = $this->getJsonRequest();
        $user = Auth::user();
        $companyID = $user->company_id;
        $employee = Employee::where('company_id', $companyID)->where('user_id', $user->id)->first();
        $employeeID = $employee->id;
        $employeeName = $this->getArrayValue($postData, "employee_name");
        $orderProducts = $this->getArrayValue($postData, "orderproducts");
        $taxes = $this->getArrayValue($postData, "taxes");
        $createdAt = $this->getArrayValue($postData, "created_at");
        $product_level_discount_flag = $this->getArrayValue($postData, "product_level_discount_flag");
        $product_level_tax_flag = $this->getArrayValue($postData, "product_level_tax_flag");
        $commit = false;

        $pendingModuleAttribute = ModuleAttribute::where('company_id', $companyID)->where('title', 'Pending')->first();
        $orderID = $this->getArrayValue($postData, "order_id");
        try {
            DB::beginTransaction();
            $notText = "Added Order";
            $notEid = $employeeID;
            $outlet_order = false;
            if ($orderID) {
                $notText = "Updated Order";
                $uniqId = $this->getArrayValue($postData, "unique_id");
                $order = Order::where('company_id', $companyID)->where('id', $orderID)->orWhere('unique_id', $uniqId)->first();
                if ($order) {
                    $notEid = $order->employee_id;
                    $orderNumber = $order->order_no;
                    if ($product_level_tax_flag == 1) {
                        $order_details = $order->orderdetails;
                        foreach ($order_details as $orderdetail) {
                            $orderdetail->taxes()->detach();
                        }
                    }
                    if (isset($order->outlet_id)) {
                        $outlet_order = true;
                        if ($order->client_id != $this->getArrayvalue($postData, "client_id")) {
                            $order->employee_id = $employeeID;
                            $order->outlet_id = NULL;
                        }
                    }
                    OrderDetails::where('order_id', $orderID)->delete();
                } else {
                    $order = new Order;
                    $orderNumber = getOrderNo($companyID);
                    $order->delivery_status_id = $pendingModuleAttribute->id;
                    $order->employee_id = $employeeID;
                    $order->outlet_id = NULL;
                }
            } else {
                $order = new Order;
                $orderNumber = getOrderNo($companyID);
                $order->delivery_status_id = $pendingModuleAttribute->id;
                $order->employee_id = $employeeID;
                $order->outlet_id = NULL;
            }
            $order->unique_id = $this->getArrayValue($postData, "unique_id");
            $order->company_id = $companyID;
            $order->client_id = $this->getArrayvalue($postData, "client_id");
            $order->order_to = $this->getArrayvalue($postData, "order_to") == 'null' ? null : $this->getArrayvalue($postData, "order_to");
            if ($this->getArrayvalue($postData, "client_id")) {
                $client = Client::where('company_id', $companyID)->where('id', $this->getArrayvalue($postData, "client_id"))->first();
                if ($client)
                    $order->due_date = Carbon::parse($this->getArrayvalue($postData, "order_date"))->addDays($client->credit_days)->format('Y-m-d');
            }
            $order->order_no = $orderNumber;
            $order->order_date = $this->getArrayvalue($postData, "order_date");
            $order->tax = $this->getArrayValue($postData, "tax");
            $order->discount = $this->getArrayValue($postData, "discount");
            $order->discount_type = $this->getArrayValue($postData, "discount_type");
            $order->product_level_discount_flag = "$product_level_discount_flag";
            $order->product_level_tax_flag = "$product_level_tax_flag";
            $order->order_note = $this->getArrayValue($postData, "order_note");
            $order->created_at = $createdAt;

            $tot_amount = floatval($this->getArrayValue($postData, "tot_amount"));
            $order->tot_amount = "$tot_amount";
            $grand_total = floatval($this->getArrayValue($postData, "grand_total"));
            $order->grand_total = "$grand_total";
            // if($order->client_id){
            //   $client = Client::find($order->client_id);

            //   if($client){
            //     $credit_days = $client->credit_days;
            //     $order_with_qty_amt = $this->getQtyAmt($client->company_id);
            //     $accounting = $this->enabledAccounting($client->company_id);
            //     if(isset($client->credit_limit)){
            //       $outstading_amount = $this->getOutstandingAmount($client);
            //       if(($client->credit_limit-($outstading_amount+$grand_total))<0 && $order_with_qty_amt!=1 && $accounting==1) {
            //         $response = array("status" => false, "msg" => "Insufficient credit limit. Orders cannot be placed.", "data" => null);
            //         $this->sendResponse($response);
            //       }
            //     }
            //   }
            // }

            $mil = $this->getArrayvalue($postData, "unix_timestamp");
            $seconds = ceil($mil / 1000);
            $order->order_datetime = date("Y-m-d H:i:s", $seconds);

            $latitude = $this->getArrayValue($postData, "latitude");
            $longitude = $this->getArrayValue($postData, "longitude");
            if (isset($latitude) && isset($longitude)) {
                $order->latitude = $latitude;
                $order->longitude = $longitude;
            }
            if ($order->product_level_discount_flag == 1) {
                $order->discount = $this->getArrayValue($postData, "pdiscount_sum");
            }
            if (!isset($orderID)) {
              $checkDuplicateOrderNumberExists = Order::CheckDuplicateOrderNumberExist($order->order_no, $companyID);
              if($checkDuplicateOrderNumberExists['exists']){
                $order->order_no = $checkDuplicateOrderNumberExists['new_order_no'];
              }
            }
            $saved = $order->save();

            $schemeResponse = array();
            if (isset($request->schemes)) {
                $schemes = $request->schemes;
                $decodeSchemes = json_decode($schemes);
                foreach (json_decode($decodeSchemes, true) as $scheme) {
                    $order_scheme = new OrderScheme;
                    $order_scheme->company_id = $companyID;
                    $order_scheme->scheme_id = $scheme['scheme_id'];
                    $order_scheme->is_amount = $scheme['amount'] === 'null' ? 0 : 1;
                    $order_scheme->discount_amount = $scheme['amount'];
                    $order_scheme->free_item = $scheme['items'];
                    $order_scheme->order_id = $order->id;
                    $findScheme = Scheme::find($scheme['scheme_id']);
                    $order_scheme->product_id = isset($findScheme->offered_product) ? $findScheme->offered_product : null;
                    $order_scheme->variant_id = isset($findScheme->offered_product_variant) ? $findScheme->offered_product_variant : null;
                    $order_scheme->save();
                    $schemeResponse[] = $order_scheme->refresh();
                }
            }

            // Get Applied Taxes on Order
            // Applicable when Overall Tax
            if (!empty($taxes)) {
                $decodedTaxes = json_decode($taxes, true);
                $tempArray = [];
                if ($product_level_tax_flag == 0) {
                    // Remove taxes in case if update
                    if (isset($orderID)) {
                        $order->taxes()->detach();
                    }

                    if (!empty($decodedTaxes)) {
                        foreach ($decodedTaxes as $tax) {
                            $tempArray[] = [
                                'order_id' => $order->id,
                                'tax_type_id' => $tax['id'],
                                'tax_name' => $tax['name'],
                                'tax_percent' => $tax['percent']
                            ];
                        }
                        DB::table('tax_on_orders')->insert($tempArray);
                    }
                }
            }
            if (!$orderID)
                $orderID = $order->id;
            if ($saved)
                activity()->log('Created Order', $employee->user_id);
            $orderData['id'] = $orderID;
            if ($order->delivery_status_id) {
                $oModuleAttribute = ModuleAttribute::where('company_id', $companyID)->where('id', $order->delivery_status_id)->first();
                $orderData['color'] = $oModuleAttribute->color;
            } else {
                $orderData['color'] = getColor('Pending')['color'];
            }
            $orderData['order_no'] = $orderNumber;
            $savedOrder = $orderData;

            // Total Amount with Tax
            $total_tax_applied_amt = 0;
            // Total tax amount
            $total_tax_applied = 0;

            if (isset($orderID)) {
                if (!empty($orderProducts)) {
                    $opArray = json_decode($orderProducts, true);
                    if (!empty($opArray) && is_array($opArray)) {
                        $opFinalArray = array();
                        foreach ($opArray as $key => $v) {
                            $temp = array();
                            $mrp = floatval($this->getArrayValue($v, "mrp"));
                            $rate = floatval($this->getArrayValue($v, "applied_rate"));
                            //floatval($this->getArrayValue($v, "rate"));
                            $quantity = floatval($this->getArrayValue($v, "quantity"));
                            $ptotal_amt = floatval($this->getArrayValue($v, "ptotal_amt"));
                            $pfinal_amount = floatval($this->getArrayValue($v, "pfinal_amount"));
                            $pdiscount = floatval($this->getArrayValue($v, "pdiscount"));
                            $pdiscount_type = $this->getArrayValue($v, "pdiscount_type");

                            if (!isset($pdiscount)) $pdiscount = 0;

                            $ptotal_amt = $pfinal_amount;

                            $orderProductIds = $this->getArrayValue($v, "ordersproduct_id");
                            $temp["order_id"] = $orderID;
                            $temp["product_id"] = $this->getArrayValue($v, "product_id");
                            $temp["product_name"] = $this->getArrayValue($v, "product_name");
                            $temp["product_variant_id"] = $this->getArrayValue($v, "product_variant_id");
                            $temp["product_variant_name"] = $this->getArrayValue($v, "product_variant_name");
                            $temp["mrp"] = "$mrp";
                            $temp["unit"] = $this->getArrayValue($v, "unit");
                            $temp["unit_name"] = $this->getArrayValue($v, "unit_name");
                            $temp["unit_symbol"] = $this->getArrayValue($v, "unit_symbol");
                            $temp["rate"] = "$rate";
                            $temp["quantity"] = "$quantity";
                            $temp["amount"] = "$pfinal_amount";
                            $temp["pdiscount"] = "$pdiscount";
                            $temp["pdiscount_type"] = isset($pdiscount_type) ? $pdiscount_type : "Amt";
                            $temp["ptotal_amt"] = "$pfinal_amount";
                            $temp["variant_colors"] = $this->getArrayValue($v, "variant_colors");
                            $temp["short_desc"] = $this->getArrayValue($v, "short_desc");

                            $orderDetailId = OrderDetails::insertGetId($temp);
                            if ($orderDetailId) {
                                $commit = true;
                                $updated_rate = $pfinal_amount;
                                array_push($opFinalArray, $temp);
                                if ($product_level_tax_flag == 1) {
                                    $tax_applied = floatval(0.0);
                                    $ptaxes = $this->getArrayValue($v, "total_tax");
                                    $decodeValue = json_decode($ptaxes, true);
                                    if (!empty($decodeValue)) {
                                        foreach ($decodeValue as $key => $ptax) {
                                            $tax_applied += floatval($ptax['percent']);
                                            DB::table('tax_on_orderproducts')->insert([
                                                "orderproduct_id" => $orderDetailId,
                                                "tax_type_id" => $ptax['id'],
                                                "product_id" => $temp["product_id"],
                                            ]);
                                        }
                                    }
                                }
                            }
                        }
                        if (true) {
                            if ($orderID) {
                                $notificationData = array(
                                    "company_id" => $companyID,
                                    "employee_id" => $employeeID,
                                    "title" => "Order Added",
                                    "description" => "Order No: " . $orderNumber,
                                    "created_at" => $createdAt,
                                    "status" => 1,
                                    "to" => 0
                                );
                                $nSaved = saveAdminNotification($companyID, $employeeID, date("Y-m-d H:i:s"), $notText, "order", $orderData);
                                if ($notEid == 0) $superiors = DB::table('handles')->where('client_id', $order->client_id)->pluck('employee_id')->toArray();
                                else $superiors = Employee::employeeParents($notEid, array());
                                $partyHandles = DB::table('handles')->where('client_id', $order->client_id)->whereNotIn('employee_id', $superiors)->pluck('employee_id')->toArray();
                                $superiors = array_merge($partyHandles, $superiors);
                                if ($notEid == $employeeID) $superiors = array_diff($superiors, array($notEid));
                                
                                
                                if ($notText == "Added Order") {
                                 $this->orderNotification($companyID, $superiors, $orderID, $schemeResponse, "add");

                                } elseif ($notText == "Updated Order") {
                                  $this->orderNotification($companyID, $superiors, $orderID,$schemeResponse, "update");

                                }
                            }
                        }

                    }
                }
            }

            $status = (!empty($orderID) && $commit) ? true : false;
            if ($commit) {
                DB::commit();
            }

            $response = array("status" => true, "message" => "successfully saved", "data" => $savedOrder,'schemes' => $schemeResponse);
            $this->sendResponse($response);
        } catch (Exception $e) {
            
          Log::info(array("store OrderController API", $e->getMessage()));
            DB::rollback();
            $response = array("status" => false, "message" => "failed saving order", "data" => $e->getMessage());
            $this->sendResponse($response);
        }
    }

    public function update(Request $request)
    {
        $postData = $this->getJsonRequest();
        $user = Auth::user();
        $companyID = $user->company_id;
        $employee = Employee::where('company_id', $companyID)->where('user_id', $user->id)->first();
        $employeeID = $employee->id;
        $employeeName = $this->getArrayValue($postData, "employee_name");
        $orderProducts = $this->getArrayValue($postData, "orderproducts");
        $taxes = $this->getArrayValue($postData, "taxes");
        $createdAt = $this->getArrayValue($postData, "created_at");
        $product_level_discount_flag = $this->getArrayValue($postData, "product_level_discount_flag");
        $product_level_tax_flag = $this->getArrayValue($postData, "product_level_tax_flag");
        $commit = false;

        $pendingModuleAttribute = ModuleAttribute::where('company_id', $companyID)->where('title', 'Pending')->first();
        $orderID = $this->getArrayValue($postData, "order_id");
        try {
            DB::beginTransaction();
            $notText = "Added Order";
            $notEid = $employeeID;
            $outlet_order = false;
            if ($orderID) {
                $notText = "Updated Order";
                $uniqId = $this->getArrayValue($postData, "unique_id");
                $order = Order::where('company_id', $companyID)->where('id', $orderID)->orWhere('unique_id', $uniqId)->first();
                if ($order) {
                    $orderID = $order->id;
                    $notEid = $order->employee_id;
                    $orderNumber = $order->order_no;
                    if ($product_level_tax_flag == 1) {
                        $order_details = $order->orderdetails;
                        foreach ($order_details as $orderdetail) {
                            $orderdetail->taxes()->detach();
                        }
                    }
                    if (isset($order->outlet_id)) {
                        $outlet_order = true;
                        if ($order->client_id != $this->getArrayvalue($postData, "client_id")) {
                            $order->employee_id = $employeeID;
                            $order->outlet_id = NULL;
                        }
                    }
                    OrderDetails::where('order_id', $orderID)->delete();
                } else {
                    $order = new Order;
                    $orderNumber = getOrderNo($companyID);
                    $order->delivery_status_id = $pendingModuleAttribute->id;
                    $order->employee_id = $employeeID;
                    $order->outlet_id = NULL;
                }
            } else {
                $order = new Order;
                $orderNumber = getOrderNo($companyID);
                $order->delivery_status_id = $pendingModuleAttribute->id;
                $order->employee_id = $employeeID;
                $order->outlet_id = NULL;
            }
            $order->unique_id = $this->getArrayValue($postData, "unique_id");
            $order->company_id = $companyID;
            $order->client_id = $this->getArrayvalue($postData, "client_id");
            $order->order_to = $this->getArrayvalue($postData, "order_to") == 'null' ? null : $this->getArrayvalue($postData, "order_to");
            if ($this->getArrayvalue($postData, "client_id")) {
                $client = Client::where('company_id', $companyID)->where('id', $this->getArrayvalue($postData, "client_id"))->first();
                if ($client)
                    $order->due_date = Carbon::parse($this->getArrayvalue($postData, "order_date"))->addDays($client->credit_days)->format('Y-m-d');
            }
            $order->order_no = $orderNumber;
            $order->order_date = $this->getArrayvalue($postData, "order_date");
            $order->tax = $this->getArrayValue($postData, "tax");
            $order->discount = $this->getArrayValue($postData, "discount");
            $order->discount_type = $this->getArrayValue($postData, "discount_type");
            $order->product_level_discount_flag = "$product_level_discount_flag";
            $order->product_level_tax_flag = "$product_level_tax_flag";
            $order->order_note = $this->getArrayValue($postData, "order_note");
            // $order->created_at = $createdAt;

            $tot_amount = floatval($this->getArrayValue($postData, "tot_amount"));
            $order->tot_amount = "$tot_amount";
            $grand_total = floatval($this->getArrayValue($postData, "grand_total"));
            $order->grand_total = "$grand_total";
            // if($order->client_id){
            //   $client = Client::find($order->client_id);

            //   if($client){
            //     $credit_days = $client->credit_days;
            //     $order_with_qty_amt = $this->getQtyAmt($client->company_id);
            //     $accounting = $this->enabledAccounting($client->company_id);
            //     if(isset($client->credit_limit)){
            //       $outstading_amount = $this->getOutstandingAmount($client);
            //       if(($client->credit_limit-($outstading_amount+$grand_total))<0 && $order_with_qty_amt!=1 && $accounting==1) {
            //         $response = array("status" => false, "msg" => "Insufficient credit limit. Orders cannot be placed.", "data" => null);
            //         $this->sendResponse($response);
            //       }
            //     }
            //   }
            // }

            $mil = $this->getArrayvalue($postData, "unix_timestamp");
            $seconds = ceil($mil / 1000);
            $order->order_datetime = date("Y-m-d H:i:s", $seconds);

            $latitude = $this->getArrayValue($postData, "latitude");
            $longitude = $this->getArrayValue($postData, "longitude");
            if (isset($latitude) && isset($longitude)) {
                $order->latitude = $latitude;
                $order->longitude = $longitude;
            }
            if ($order->product_level_discount_flag == 1) {
                $order->discount = $this->getArrayValue($postData, "pdiscount_sum");
            }
            $saved = $order->save();

            if (!$order->orderScheme->IsEmpty()) {
                foreach ($order->orderScheme as $scheme) {
                    $scheme->delete();
                }
            }

            $schemeCreated = array();
            if (isset($request->schemes)) {
                $schemes = $request->schemes;
                $decodeSchemes = json_decode($schemes);
                foreach (json_decode($decodeSchemes, true) as $scheme) {
                    $order_scheme = new OrderScheme;
                    $order_scheme->company_id = $companyID;
                    $order_scheme->scheme_id = $scheme['scheme_id'];
                    $order_scheme->is_amount = $scheme['amount'] === 'null' ? 0 : 1;
                    $order_scheme->discount_amount = $scheme['amount'];
                    $order_scheme->free_item = $scheme['items'];
                    $order_scheme->order_id = $order->id;
                    $findScheme = Scheme::find($scheme['scheme_id']);
                    $order_scheme->product_id = isset($findScheme->offered_product) ? $findScheme->offered_product : null;
                    $order_scheme->variant_id = isset($findScheme->offered_product_variant) ? $findScheme->offered_product_variant : null;
                    $order_scheme->save();
                    $schemeCreated[] = $order_scheme->refresh();
                }
            }

            // Get Applied Taxes on Order
            // Applicable when Overall Tax
            if (!empty($taxes)) {
                $decodedTaxes = json_decode($taxes, true);
                $tempArray = [];
                if ($product_level_tax_flag == 0) {
                    // Remove taxes in case if update
                    if (isset($orderID)) {
                        $order->taxes()->detach();
                    }

                    if (!empty($decodedTaxes)) {
                        foreach ($decodedTaxes as $tax) {
                            $tempArray[] = [
                                'order_id' => $order->id,
                                'tax_type_id' => $tax['id'],
                                'tax_name' => $tax['name'],
                                'tax_percent' => $tax['percent']
                            ];
                        }
                        DB::table('tax_on_orders')->insert($tempArray);
                    }
                }
            }
            // if (!$orderID)
            $orderID = $order->id;
            if ($saved)
                activity()->log('Created Order', $employee->user_id);
            $orderData['id'] = $orderID;
            if ($order->delivery_status_id) {
                $oModuleAttribute = ModuleAttribute::where('company_id', $companyID)->where('id', $order->delivery_status_id)->first();
                $orderData['color'] = $oModuleAttribute->color;
            } else {
                $orderData['color'] = getColor('Pending')['color'];
            }
            $orderData['order_no'] = $orderNumber;
            $savedOrder = $orderData;

            // Total Amount with Tax
            $total_tax_applied_amt = 0;
            // Total tax amount
            $total_tax_applied = 0;

            if (isset($orderID)) {
                if (!empty($orderProducts)) {
                    $opArray = json_decode($orderProducts, true);
                    if (!empty($opArray) && is_array($opArray)) {
                        $opFinalArray = array();
                        foreach ($opArray as $key => $v) {
                            $temp = array();
                            $mrp = floatval($this->getArrayValue($v, "mrp"));
                            $rate = floatval($this->getArrayValue($v, "applied_rate"));
                            //floatval($this->getArrayValue($v, "rate"));
                            $quantity = floatval($this->getArrayValue($v, "quantity"));
                            $ptotal_amt = floatval($this->getArrayValue($v, "ptotal_amt"));
                            $pfinal_amount = floatval($this->getArrayValue($v, "pfinal_amount"));
                            $pdiscount = floatval($this->getArrayValue($v, "pdiscount"));
                            $pdiscount_type = $this->getArrayValue($v, "pdiscount_type");

                            if (!isset($pdiscount)) $pdiscount = 0;

                            $ptotal_amt = $pfinal_amount;

                            $orderProductIds = $this->getArrayValue($v, "ordersproduct_id");
                            $temp["order_id"] = $orderID;
                            $temp["product_id"] = $this->getArrayValue($v, "product_id");
                            $temp["product_name"] = $this->getArrayValue($v, "product_name");
                            $temp["product_variant_id"] = $this->getArrayValue($v, "product_variant_id");
                            $temp["product_variant_name"] = $this->getArrayValue($v, "product_variant_name");
                            $temp["mrp"] = "$mrp";
                            $temp["unit"] = $this->getArrayValue($v, "unit");
                            $temp["unit_name"] = $this->getArrayValue($v, "unit_name");
                            $temp["unit_symbol"] = $this->getArrayValue($v, "unit_symbol");
                            $temp["rate"] = "$rate";
                            $temp["quantity"] = "$quantity";
                            $temp["amount"] = "$pfinal_amount";
                            $temp["pdiscount"] = "$pdiscount";
                            $temp["pdiscount_type"] = isset($pdiscount_type) ? $pdiscount_type : "Amt";
                            $temp["ptotal_amt"] = "$pfinal_amount";
                            $temp["variant_colors"] = $this->getArrayValue($v, "variant_colors");
                            $temp["short_desc"] = $this->getArrayValue($v, "short_desc");

                            $orderDetailId = OrderDetails::insertGetId($temp);
                            if ($orderDetailId) {
                                $commit = true;
                                $updated_rate = $pfinal_amount;
                                array_push($opFinalArray, $temp);
                                if ($product_level_tax_flag == 1) {
                                    $tax_applied = floatval(0.0);
                                    $ptaxes = $this->getArrayValue($v, "total_tax");
                                    $decodeValue = json_decode($ptaxes, true);
                                    if (!empty($decodeValue)) {
                                        foreach ($decodeValue as $key => $ptax) {
                                            $tax_applied += floatval($ptax['percent']);
                                            DB::table('tax_on_orderproducts')->insert([
                                                "orderproduct_id" => $orderDetailId,
                                                "tax_type_id" => $ptax['id'],
                                                "product_id" => $temp["product_id"],
                                            ]);
                                        }
                                    }
                                }
                            }
                        }
                        if (true) {
                            if ($orderID) {
                                $notificationData = array(
                                    "company_id" => $companyID,
                                    "employee_id" => $employeeID,
                                    "title" => "Order Added",
                                    "description" => "Order No: " . $orderNumber,
                                    "created_at" => $createdAt,
                                    "status" => 1,
                                    "to" => 0
                                );
                                $nSaved = saveAdminNotification($companyID, $employeeID, date("Y-m-d H:i:s"), $notText, "order", $orderData);
                                if ($notEid == 0) $superiors = DB::table('handles')->where('client_id', $order->client_id)->pluck('employee_id')->toArray();
                                else $superiors = Employee::employeeParents($notEid, array());
                                $partyHandles = DB::table('handles')->where('client_id', $order->client_id)->whereNotIn('employee_id', $superiors)->pluck('employee_id')->toArray();
                                $superiors = array_merge($partyHandles, $superiors);
                                if ($notEid == $employeeID) $superiors = array_diff($superiors, array($notEid));


                                if ($notText == "Added Order") {
                                    $this->orderNotification($companyID, $superiors, $orderID, $schemeCreated,"add");
                                } elseif ($notText == "Updated Order") {
                                    $this->orderNotification($companyID, $superiors, $orderID, $schemeCreated,"update");
                                }
                            }
                        }

                    }
                }
            }

            $status = (!empty($orderID) && $commit) ? true : false;
            if ($commit) {
                DB::commit();
            }

            $response = array("status" => true, "message" => "successfully saved", "data" => $savedOrder, 'schemes' => $schemeCreated);
            $this->sendResponse($response);
        } catch (Exception $e) {
          Log::info(array("update OrderController API", $e->getMessage()));
            DB::rollback();
            $response = array("status" => false, "message" => "failed saving order", "data" => $e->getMessage());
            $this->sendResponse($response);
        }
    }

       private function orderNotification($companyID, $employeeIDs, $orderID, $schemeResponse, $action)
    {
      $user = Auth::user();
      $companyID = $user->company_id;
      $employee = Employee::where('company_id', $companyID)->where('user_id', $user->id)->first();
      $loggedInEmployee = $employee->id;
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

        }
        // $msgID = sendPushNotification_($fbIds, 1, null, $dataPayload);
    }


    public function syncOrders()
    {
        $postData = $this->getJsonRequest();
        $arraySyncedData = $this->manageUnsyncedOrder($postData, true);
        $syncedData = array_key_exists('synced_data', $arraySyncedData) ? $arraySyncedData['synced_data'] : null;
        $unsyncedData = array_key_exists('unsynced_data', $arraySyncedData) ? $arraySyncedData['unsynced_data'] : null;
        $response = array("status" => true, "message" => "success", "data" => $syncedData, "unsynced_data" => $unsyncedData);
        $this->sendResponse($response);
    }

    private function manageUnsyncedOrder($postData, $returnItems = false, $client = null)
    {
        $rawData = $this->getArrayValue($postData, "nonsynced_orders");
        $user = Auth::user();
        $companyID = $user->company_id;
        $employee = Employee::where('company_id', $companyID)->where('user_id', $user->id)->first();
        $loggedEmployeeId = $employee->id;
        $employeeID = $employee->id;

        if (empty($rawData)) return $returnItems ? array() : false;

        $data = json_decode($rawData, true);
        $synced_data = array();
        $unsynced_data = array();
        // $arraySyncedData = array();
        foreach ($data as $key => $order) {
            try {
                $employeeID = $employee->id;
                $orderClientID = (int)$this->getArrayValue($order, "client_id");
                $orderClientUniqueID = $this->getArrayValue($order, "client_unique_id");
                $orderUniqueID = $this->getArrayvalue($order, "unique_id");
                $orderID = $this->getArrayValue($order, "id");
                $orderNumber = (int)$this->getArrayValue($order, "order_no");
                $product_level_discount_flag = $this->getArrayValue($order, "product_level_discount_flag") ? $this->getArrayValue($order, "product_level_discount_flag") : 0;
                $product_level_tax_flag = $this->getArrayValue($order, "product_level_tax_flag") ? $this->getArrayValue($order, "product_level_tax_flag") : 0;
                $tempOrderArray["delivery_status_id"] = $this->getArrayvalue($order, "delivery_status_id");

                if (!isset($orderClientID)) {
                    if ($returnItems && !empty($client)) {
                        $tempClientUniqueID = $client->unique_id;
                        $tempClientID = $client->id;
                        if ($orderClientUniqueID == $tempClientUniqueID) $orderClientID = $tempClientID;
                        else continue;
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
                $outlet_id = NULL;
                $outlet_order = false;

                if (isset($orderID)) {
                    $notText = "Updated Order";
                    $order_instance = Order::where('company_id', $companyID)->where('id', $orderID)->first();
                    if(!$order_instance) continue;
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
                            $outlet_id = NULL;
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
                $tempOrderArray["order_to"] = $this->getArrayvalue($order, "order_to") == 'null' ? null : $this->getArrayvalue($order, "order_to");

                $tempOrderArray["order_no"] = $orderNumber;
                $tempOrderArray["order_date"] = $this->getArrayvalue($order, "order_date");

                if ($this->getArrayvalue($order, "client_id")) {
                    $client = Client::where('company_id', $companyID)->where('id', $this->getArrayvalue($order, "client_id"))->first();
                    if ($client)
                        $tempOrderArray["due_date"] = Carbon::parse($this->getArrayvalue($order, "order_date"))->addDays($client->credit_days)->format('Y-m-d');
                }
                $tot_amount = floatval($this->getArrayvalue($order, "tot_amount"));
                $tempOrderArray["tot_amount"] = "$tot_amount";
                $grand_total = floatval($this->getArrayvalue($order, "grand_total"));

                // if($orderClientID){
                //   $client = Client::find($orderClientID);

                //   if($client){
                //     $credit_days = $client->credit_days;
                //     $order_with_qty_amt =  $this->getQtyAmt($client->company_id);
                //     $accounting = $this->enabledAccounting($client->company_id);
                //     if(isset($client->credit_limit)){
                //       $outstading_amount = $this->getOutstandingAmount($client);
                //       if(($client->credit_limit-($outstading_amount+$grand_total))<0 && $order_with_qty_amt!=1 && $accounting==1) {
                //         // $response = array("status" => false, "msg" => "Insufficient credit limit.", "data" => null);
                //         // $this->sendResponse($response);
                //         array_push($unsynced_data, $order);
                //         continue;
                //       }
                //     }
                //   }
                // }

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
                            "description" => "Order {$orderPrefix}{$order_get->order_no} status has been changed to {$order_get->title}.",
                            "created_at" => date('Y-m-d H:i:s'),
                            "status" => 1,
                            "to" => 1,
                            "unix_timestamp" => time()
                        );
                        $dataPayload = array("data_type" => "order", "order" => $order_get->id, "scheme_response" => array(), "action" => "update_status");
                        $sent = sendPushNotification_($fbID, 1, $notificationData, $dataPayload);
                        saveAdminNotification($companyID, $loggedEmployeeId, date("Y-m-d H:i:s"), "Updated Status", "order", $order_get);
                        $saveAdminStatusUpdateNotf = false;
                        $notText = "Updated Status";
                    }

                    $orderData = $tempOrderArray;
                    $orderData["id"] = $orderID;
                    // array_push($arraySyncedData, $orderData);
                    array_push($synced_data, $orderData);
                    if($saveAdminStatusUpdateNotf) saveAdminNotification($companyID, $loggedEmployeeId, date("Y-m-d H:i:s"), $notText, "order", $orderData);
                    if ($notEid != 0) {
                        $superiors = Employee::employeeParents($notEid, array());
                        if ($notEid == $loggedEmployeeId) $superiors = array_diff($superiors, array($notEid));
                    } else {
                        $superiors = DB::table('handles')->where('client_id', $orderClientID)->pluck('employee_id')->toArray();
                    }

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
                                $rate = floatval($this->getArrayValue($v, "applied_rate"));
                                $quantity = floatval($this->getArrayValue($v, "quantity"));
                                $ptotal_amt = floatval($this->getArrayValue($v, "ptotal_amt"));
                                $pdiscount = floatval($this->getArrayValue($v, "pdiscount"));
                                $pdiscount_type = $this->getArrayValue($v, "pdiscount_type");
                                $pfinal_amount = $this->getArrayValue($v, "pfinal_amount");

                                if (!isset($pdiscount)) $pdiscount = 0;

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
            } catch (\Exception $e) {
                Log::error(array("Order Sync Order Order Controller", $order, $e->getMessage(), $e->getCode(), $e->getLine()));
                return array($e->getMessage());
            }
        }

        $arraySyncedData['synced_data'] = $synced_data;
        $arraySyncedData['unsynced_data'] = $unsynced_data;

        return $returnItems ? $arraySyncedData : true;
    }

    public function destroy(Request $request)
    {
        $user = Auth::user();
        $id = $request->id;
        $companyID = $user->company_id;
        $employee = Employee::where('company_id', $companyID)->where('user_id', $user->id)->first();
        $order = Order::where('company_id', $companyID)->where('id', $id)->first();
        $orderdetails = $order->orderdetails;
        $finalArray = Order::select('orders.*', 'module_attributes.id as module_attributesID', 'module_attributes.title', 'module_attributes.color', 'module_attributes.order_amt_flag', 'module_attributes.order_edit_flag', 'module_attributes.order_delete_flag', 'client_settings.order_prefix')->leftJoin('client_settings', 'orders.company_id', 'client_settings.company_id')->leftJoin('module_attributes', 'orders.delivery_status_id', 'module_attributes.id')->where('orders.company_id', $companyID)->where('module_attributes.order_delete_flag', '1')->where('orders.id', $request->id)->first();
        $employeeId = $finalArray->employee_id;
        $clientId = $finalArray->client_id;
        $object_instance = $finalArray;
        if ($order->product_level_tax_flag == 0) {
            $order->taxes()->detach();
        } else {
            foreach ($orderdetails as $orderdetail) {
                $orderdetail->taxes()->detach();
            }
        }
        if ($order) {
            DB::table('orderproducts')->where('order_id', $order->id)->delete();
            $notEid = $order->employee_id;
            if ($notEid == 0) $superiors = DB::table('handles')->where('client_id', $clientId)->pluck('employee_id')->toArray();
            else $superiors = Employee::employeeParents($notEid, array());
            if ($notEid == $employee->id) $superiors = array_diff($superiors, array($notEid));

            $partyHandles = DB::table('handles')->where('client_id', $object_instance->client_id)->whereNotIn('employee_id', $superiors)->pluck('employee_id')->toArray();

            $superiors = array_merge($partyHandles, $superiors);

            $fbIDs = DB::table('employees')->where(array(array('company_id', $object_instance->company_id), array('status', 'Active')))->whereIn('id', $superiors)->where('id', '<>', $employee->id)->whereNotNull('firebase_token')->pluck('firebase_token');

            $dataPayload = array("data_type" => "order", "order" => $id, "action" => "delete");

            $notificationAlert = array();
            $orderPrefix = $object_instance->order_prefix;
            Log::info(array("Order Prefix", $orderPrefix));
            $time = time();
            $notificationAlert[] = array(
                "company_id" => $object_instance->company_id,
                "employee_id" => $employeeId,
                "data_type" => "order",
                "data" => "",
                "action_id" => $id,
                "title" => "Order Deleted",
                "description" => "Order {$orderPrefix}{$object_instance->order_no} has been deleted.",
                "created_at" => date('Y-m-d H:i:s'),
                "status" => 1,
                "to" => 1,
                "unix_timestamp" => "$time"
            );

            $msgID = sendPushNotification_($fbIDs, 1, $notificationAlert, $dataPayload);

            saveAdminNotification($object_instance->company_id, $employee->id, date("Y-m-d H:i:s"), "Deleted Order", "order", $object_instance);

            if (!$order->orderScheme->IsEmpty()) {
                foreach ($order->orderScheme as $scheme) {
                    $scheme->delete();
                }
            }

            $order->delete();
            $response = array("status" => true, "message" => "Order Deleted");
        } else {
            $response = array("status" => false, "message" => "Order Not found");
        }
        return response($response);
    }

    private function getProductLines($orderID, $tax_flag)
    {
        if (empty($orderID)) {
            return null;
        }

        $orderProducts = OrderDetails::select('orderproducts.*', 'products.product_name', 'products.short_desc')
            ->join('products', 'products.id', '=', 'orderproducts.product_id')
            ->where('order_id', $orderID)
            ->get();//->toArray();
        $discount_flag = DB::table('orders')->where('id', $orderID)->first();
        if ($tax_flag == 1 || $discount_flag->product_level_discount_flag == 0) {
            foreach ($orderProducts as $orderProduct) {
                if ($discount_flag->product_level_discount_flag == 0) {
                    $orderProduct->mrp = $orderProduct->rate;
                }
                if ($tax_flag == 1) {
                    $taxes = $orderProduct->taxes()->withTrashed()->get();
                    if ($taxes->count() == 0) {
                        $orderProduct->total_tax = null;
                    } else {
                        $orderProduct->total_tax = json_encode($taxes->toArray());
                    }
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

    //common methods
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
}
