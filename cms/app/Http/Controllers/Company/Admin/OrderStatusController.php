<?php

namespace App\Http\Controllers\Company\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\ModuleAttribute;
use Auth;
use View;
use DB;

class OrderStatusController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        $company_id = config('settings.company_id');
        $moduleAttributes = ModuleAttribute::where('company_id', $company_id)
                            ->where('module_id',1)->get();
        return view('company.orders.orderstatus', compact('moduleAttributes'));
    }
    
    public function store(Request $request)
    {
        $customMessages = [
            'name.unique' => 'The order status already exists.',
        ];
        $company_id = config('settings.company_id');
        $this->validate($request, [
            'name' => 'required|unique:module_attributes,title,NULL,id,deleted_at,NULL,company_id,'.$company_id, 
        ], $customMessages);
        $orderstatus = new ModuleAttribute();
        $orderstatus->company_id = $company_id;
        $orderstatus->module_id = 1;
        $orderstatus->title = $request->name;
        $orderstatus->color = $request->color;
        if($request->order_amt_flag=="on"){
            $orderstatus->order_amt_flag = 1;
        }else{
            $orderstatus->order_amt_flag = 0;
        }
        if($request->os_editable_flag=="on"){
            $orderstatus->order_edit_flag = 1;
        }else{
            $orderstatus->order_edit_flag = 0;            
        }
        if($request->os_deleteable_flag=="on"){
            $orderstatus->order_delete_flag = 1;
        }else{
            $orderstatus->order_delete_flag = 0;
        }

        $savedStatus = $orderstatus->save();
        if ($savedStatus) {
          $fbIDs = DB::table('employees')->where(array(array('company_id', $company_id), array('status', 'Active')))->whereNotNull('firebase_token')->pluck('firebase_token');
          $dataPayload = array("data_type" => "orderstatus", "orderstatus" => $orderstatus, "action" => "add");
          $sent = sendPushNotification_($fbIDs, 25, null, $dataPayload);
        }
        return $orderstatus;
    }

    public function update(Request $request)
    {
        $customMessages = [
            'name.unique' => 'The order status already exists.',
        ];

        $company_id = config('settings.company_id');
        $this->validate($request, [
            'name' => 'required|unique:module_attributes,title,'.$request->id.',id,deleted_at,NULL,company_id,'.$company_id,
        ], $customMessages);
        $orderstatus = ModuleAttribute::findOrFail($request->id);
        $order_exists = \App\Order::where('company_id', $company_id)->where('delivery_status', $orderstatus->title)->limit(1)->get();
        if($orderstatus->title==$request->name){
            $orderstatus->company_id = $company_id;
            $orderstatus->module_id = 1;
            if($order_exists->count()==0){ 
                $orderstatus->title = $request->name;
            }
            $orderstatus->color = $request->color;
            if($request->ed_order_amt_flag=="on"){
                $orderstatus->order_amt_flag = 1;
            }else{
                $orderstatus->order_amt_flag = 0;
            }                
            if($request->os_editable_flag=="on"){
                $orderstatus->order_edit_flag = 1;
            }else{
                $orderstatus->order_edit_flag = 0;            
            }
            if($request->os_deleteable_flag=="on"){
                $orderstatus->order_delete_flag = 1;
            }else{
                $orderstatus->order_delete_flag = 0;
            }
            $updatedStatus = $orderstatus->update();
            if ($updatedStatus) {
              $fbIDs = DB::table('employees')->where(array(array('company_id', $company_id), array('status', 'Active')))->whereNotNull('firebase_token')->pluck('firebase_token');
              $dataPayload = array("data_type" => "orderstatus", "orderstatus" => $orderstatus, "action" => "update");
              $sent = sendPushNotification_($fbIDs, 25, null, $dataPayload);
            }

            // $moduleAttributes = ModuleAttribute::where('company_id', $company_id)
            //                     ->where('module_id',1)->get();
            // $moduleAttributes_added = ModuleAttribute::where('company_id', $company_id)
            //                             ->where('module_id',1)->where('default','0')->get();

            // $moduleAttributes_default = ModuleAttribute::where('default','1')->get();
            // $moduleAttributes = $moduleAttributes_added->merge($moduleAttributes_default)->sortBy('title');
            $moduleAttributes = ModuleAttribute::where('company_id', $company_id)
                ->where('module_id',1)->get();
            $view =  View::make('company.orders._partial',compact('moduleAttributes'))->render();
        }else{
            // throw new \ErrorException('Cannot update. Order Status is being used somewhere else.');
            return response()->json([
                "errors" => ["name"=> ["Name cannot be edited. This status has been set in one of order status."]],
                "message" => "The given data was invalid.",
            ], 422);
        }
        return $view;
    }


    public function delete(Request $request)
    {
        $company_id = config('settings.company_id');
        $orderstatus = ModuleAttribute::findOrFail($request->id);
        $order_exists = \App\Order::where('company_id', $company_id)->where('delivery_status', $orderstatus->title)->limit(1)->get();
        if($order_exists->count()==0){
            $deleted = $orderstatus->delete();
            if ($deleted) {
              $fbIDs = DB::table('employees')->where(array(array('company_id', $company_id), array('status', 'Active')))->whereNotNull('firebase_token')->pluck('firebase_token');
              $dataPayload = array("data_type" => "orderstatus", "orderstatus" => $orderstatus, "action" => "delete");
              $sent = sendPushNotification_($fbIDs, 25, null, $dataPayload);
            }

        }else{
            $deleted = false;
        }
        if($deleted){
            $data['msg'] = "Deleted Successfully.";
        }else{
            $data['msg'] = "Cannot Delete as this status has been used in orders.";
        }
        // $moduleAttributes = ModuleAttribute::where('company_id', $company_id)
        //                     ->where('module_id',1)->get();
        // $moduleAttributes_added = ModuleAttribute::where('company_id', $company_id)
        //                             ->where('module_id',1)->where('default','0')->get();

        // $moduleAttributes_default = ModuleAttribute::where('default','1')->get();
        // $moduleAttributes = $moduleAttributes_added->merge($moduleAttributes_default)->sortBy('title');
        $moduleAttributes = ModuleAttribute::where('company_id', $company_id)
                ->where('module_id',1)->get();
        $data['view'] =  View::make('company.orders._partial',compact('moduleAttributes'))->render();
        return $data;
        
    }
}
