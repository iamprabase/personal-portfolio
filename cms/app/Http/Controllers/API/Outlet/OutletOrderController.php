<?php

namespace App\Http\Controllers\API\Outlet;

use App\Order;
use App\Client;
use App\Outlet;
use App\Company;
use App\Product;
use App\TaxType;
use App\UnitTypes;
use App\OrderDetails;
use App\ClientSetting;
use App\ProductVariant;
use App\ModuleAttribute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Resources\ClientOrderResource;
use App\Http\Requests\API\OutletOrderRequest;

class OutletOrderController extends Controller
{
    public function __construct()
    {
      $this->middleware('auth:api-outlets');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Client $client)
    {
      $data = array("status"=> 200, "msg"=> "Order Fetched Successfully", "data" => ClientOrderResource::collection($client->orders));

      return $data;
    }

    private function enabledAccounting($company_id){
      try{
        
        $clientSetting = ClientSetting::whereCompanyId($company_id)->first();

        if($clientSetting){
          return $clientSetting->accounting;
        }else{
          return 0;
        }
      }catch(Exception $e){
        Log::info($e->getMessage());

        return 0;
      }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(OutletOrderRequest $request, Outlet $outlet)
    {   
      try{
        if(request()->user()->id!=$outlet->users->id){
          $data = array("status" => 400, 'msg' => 'You aren\'t authorized to make this request.');
          
          return response()->json($data);  
        }
        $company_id = $request->company_id;
        $outlet_id = $outlet->id;
        $pendingModuleAttribute = ModuleAttribute::where('company_id', $company_id)->where('title', 'Pending')->first();
        $client_id = $request->client_id;
        $order_no = getOrderNo($company_id);
        $latitude = $request->latitude;
        $longitude = $request->longitude;
        $sub_total = $request->sub_total_amount; //maps to total_amount
        $tax_amount = $request->tax_amount;
        $discount = $request->discount;
        $discount_type = $request->discount_type;
        $grand_total = $request->grand_total;
        $order_datetime = date('Y-m-d H:i:s');
        $order_date = date('Y-m-d', strtotime($order_datetime));
        $credit_days = 0;
        if($client_id){
          $client = Client::find($client_id);
          if($client)
            $credit_days = $client->credit_days;
          
          if($client){
            $order_with_qty_amt = "$request->order_with_qty_amt";
            $accounting = $this->enabledAccounting($client->company_id);
            if(isset($client->credit_limit)){
              $outstading_amount = $this->getOutstandingAmount($client);
              if(($client->credit_limit-($outstading_amount+$grand_total))<0 && $order_with_qty_amt!=1 && $accounting==1) {
                $response = array("status"=> false, "msg"=> "Insufficient credit limit.", "data" => null);
          
                return $response;
              }
            }
          }  
        }
        $due_date = date('Y-m-d', strtotime($order_date. ' + '.$credit_days .'days'));
        $delivery_status = $pendingModuleAttribute->title;
        $delivery_status_id = $pendingModuleAttribute->id;
        $product_level_tax = $request->product_level_tax;
        $product_level_discount = $request->product_level_discount;

        DB::beginTransaction();

        $save_order = Order::create([
          "company_id"=> $company_id, "client_id"=> $client_id, "order_no"=> $order_no,
          "latitude"=> $latitude, "longitude"=> $longitude, "tot_amount"=> $sub_total, "tax"=> $tax_amount,
          "discount"=> $discount, "discount_type"=> $discount_type, "grand_total"=> $grand_total, 
          "order_date"=> $order_date, "due_date"=> $due_date, "order_datetime"=> $order_datetime, 
          "delivery_status_id"=> $delivery_status_id, "product_level_tax_flag"=> $product_level_tax, "product_level_discount_flag"=> $product_level_discount , "outlet_id"=> $outlet->id 
        ]);

        $orderdetails = $request->orderdetails;
        if($save_order){
          $order_id = $save_order->id;

          if($product_level_tax==0){
            $applied_taxes = $request->applied_tax;
            if(!empty($applied_taxes)){
              foreach($applied_taxes as $applied_tax){
                $tax_id = $applied_tax['id'];
                $tax_name = $applied_tax['name'];
                $tax_percent = $applied_tax['percent'];
                DB::table('tax_on_orders')->insert([
                  'order_id' => $order_id,
                  'tax_type_id' => $tax_id,
                  'tax_name' => $tax_name,
                  'tax_percent' => $tax_percent,
                ]); 
              }
            }
          }

          foreach($orderdetails as $orderdetail){
            $product_id = $orderdetail['product_id'];
            $product_name = $orderdetail['product_name'];
            $variant_id = $orderdetail['variant_id'];
            $variant_name = $orderdetail['variant_name'];
            $variant_colors = $orderdetail['variant_colors'];
            $mrp = $orderdetail['mrp'];
            $brand = $orderdetail['brand'];
            $unit = $orderdetail['unit_id'];
            $unit_name = $orderdetail['unit_name'];
            $unit_symbol = $orderdetail['unit_symbol'];
            $rate = $orderdetail['rate'];
            $quantity = $orderdetail['quantity'];
            $amount = $orderdetail['amount'];
            $applied_taxes = $orderdetail['applied_tax'];
            // if($product_level_tax==1){
            //   $applied_taxes = $orderdetail['applied_tax'];
            //   if(!empty($applied_taxes)){
            //     $tax_ids = array_column($applied_taxes, 'id');
            //     $tax_percentages = TaxType::where('company_id', $company_id)->whereIn('id', $tax_ids)->sum('percent');
            //     $amount = ($tax_percentages/100)*$amount + $amount;
            //   }
            // }
            $pdiscount = $orderdetail['pdiscount'];
            $pdiscount_type = $orderdetail['pdiscount_type'];
            $short_desc = $orderdetail['short_desc'];

            $save_order_details = OrderDetails::create([
              "order_id"=> $order_id, "product_id"=> $product_id, "product_name"=> $product_name, "mrp"=> $mrp,
              "brand"=> $brand, "unit"=> $unit, "unit_name"=> $unit_name, "unit_symbol"=> $unit_symbol, "rate"=> $rate,
              "quantity"=> $quantity, "amount"=> $amount, "pdiscount"=> $pdiscount, "pdiscount_type"=> $pdiscount_type,
              "ptotal_amt"=> $amount, "short_desc"=> $short_desc, "product_variant_id"=> $variant_id, "product_variant_name"=> $variant_name, "variant_colors"=> $variant_colors
            ]);

            if($save_order_details){
              if(!empty($applied_taxes)){
                foreach($applied_taxes as $applied_tax){
                  $tax_id = $applied_tax['id'];
                  DB::table('tax_on_orderproducts')->insert([
                    'orderproduct_id' => $save_order_details->id,
                    'tax_type_id' => $tax_id,
                    'product_id' => $product_id,
                  ]); 
                }
              }
            }
          }
        }

        DB::commit();
        $notification_sent = $this->orderNotification($company_id, $outlet_id, $order_id, "add");
        $data = array("status"=> 200, "msg"=> "Order Saved Successfully", "data" => new ClientOrderResource($save_order));
        
        return $data;
      }catch(Exception $e){
        $data = array("msg" => $e->getMessage());
        return response()->json($data); 
      }
    }

    private function orderNotification($companyID, $outletID, $orderID, $action)
    {
      try{

        $orders = Order::where('orders.id', $orderID)
                  ->where('orders.company_id', $companyID)
                  ->where('orders.outlet_id', $outletID)
                  ->leftJoin('outlets', 'orders.employee_id', 'outlets.id')
                  ->leftJoin('clients', 'orders.client_id', 'clients.id')
                  ->leftJoin('client_settings', 'orders.company_id','client_settings.company_id')
                  ->leftJoin('module_attributes','orders.delivery_status_id','module_attributes.id')
                  ->select('orders.*', 'outlets.outlet_name as employee_name', 'clients.company_name as company_name', 'clients.name as client_name','client_settings.order_prefix','module_attributes.id as moduleattributesId','module_attributes.title as delivery_status','module_attributes.color','module_attributes.order_amt_flag','module_attributes.order_edit_flag','module_attributes.order_delete_flag')
                  ->first();
        
        $moduleAttributes =  ModuleAttribute::where('company_id', $companyID)->get();
        
        $product_level_tax_flag = $orders->product_level_tax_flag;
        $productLines = $this->getProductLines($orders->id, $product_level_tax_flag);
        if($product_level_tax_flag==1){
          $orders->taxes = null;
        }else{
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

        if($orders->employee_id==0 && $orders->outlet_id) $orders->employee_name = $orders->outlets()->withTrashed()->first()?$orders->outlets()->withTrashed()->first()->contact_person." (O)":"";
        $employee_handles = DB::table('handles')->where('company_id', $companyID)->where('client_id', $orders->client_id)->pluck('employee_id')->toArray();
        $fbIDs = DB::table('employees')->where(array(array('company_id', $companyID), array('status', 'Active')))->whereNotNull('firebase_token')->whereIn('id', $employee_handles)->pluck('firebase_token');
        $dataPayload = array("data_type" => "order", "order" => $orders, "scheme_response" => array(), "action" => $action);
        $msgID = sendPushNotification_($fbIDs, 1, null, $dataPayload);
        return $msgID;
      }catch(\Exception $e){
        Log::info($e->getMessage());
        return 1;
      }
    }
    private function getProductLines($orderID, $tax_flag)
    {
      if (empty($orderID)) {
          return null;
      }

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
      if (empty($orderID)) {
          return null;
      }
      $order_instance = Order::findOrFail($orderID);
      $taxes = $order_instance->taxes()->withTrashed()->get()->toArray();
      return empty($taxes) ? null : json_encode($taxes);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Order $order)
    {
      if(request()->user()->id!=$order->clients->outlets->users->id){
        $data = array("status" => 400, 'msg' => 'You aren\'t authorized to make this request.');
        
        return response()->json($data);  
      }
      try{
        $editable = $order->module_status->order_edit_flag;
        if($editable==0){
          $data = array("status"=> 422, "msg"=> "Order can\'t be edited.", "data" => new ClientOrderResource($order));
        
          return $data;
        }
        if($order->orderScheme->count() > 0){
          $data = array("status"=> 422, "msg"=> "Order can\'t be edited.", "data" => new ClientOrderResource($order));
        
          return $data;
        }
        $company_id = $order->company_id;
        $client_id = (int)$request->client_id;
        $outlet_id = (int)$request->outlet_id;
        $latitude = $request->latitude;
        $longitude = $request->longitude;
        $sub_total = $request->sub_total_amount;
        $tax_amount = $request->tax_amount;
        $discount = $request->discount_calculated;
        $discounted = isset($discount)?$discount:0;
        $discount_type = $order->discount_type;
        if($discount_type=="%"){
          $discount = $discount * 100 / $sub_total;
          $discount_amt = $discounted;//(($discounted * $sub_total)/100);  
        }else{
          $discount_amt =  $discounted;
        }
        $grand_total = $sub_total - $discount_amt + $tax_amount;
        $product_level_tax = $order->product_level_tax_flag;
        $product_level_discount = $order->product_level_discount_flag;

        if($client_id){
          $client = Client::find($client_id);
          if($client)
            $credit_days = $client->credit_days;
          
          if($client){
            $order_with_qty_amt = "$request->order_with_qty_amt";
            $accounting = $this->enabledAccounting($client->company_id);
            if(isset($client->credit_limit)){
              $outstading_amount = $this->getOutstandingAmount($client);
              if(($client->credit_limit-($outstading_amount+$grand_total))<0 && $order_with_qty_amt!=1 && $accounting==1) {
                $response = array("status"=> false, "msg"=> "Insufficient credit limit.", "data" => null);
          
                return $response;
              }
            }
          }  
        }

        DB::beginTransaction();

        $save_order = tap($order)->update([
          "client_id"=> $client_id, "outlet_id"=> $outlet_id, "employee_id"=> 0, "latitude"=> $latitude, "longitude"=> $longitude, "tot_amount"=> $sub_total, "tax"=> $tax_amount, "discount"=> $discount, "discount_type"=> $discount_type, "grand_total"=> $grand_total, "product_level_tax_flag"=> $product_level_tax, "product_level_discount_flag"=> $product_level_discount]);
        
        if($save_order){
          $order_id = $save_order->id;
          if($product_level_tax==1){
            $oldOrderDetails = $order->orderdetails;
            foreach($oldOrderDetails as $oldOrderDetail){
              $oldOrderDetail->taxes()->detach();
            }
          }
          OrderDetails::where('order_id', $order_id)->delete();
          if($product_level_tax==0){
            $order->taxes()->detach();
            $applied_taxes = $request->applied_tax;
            if(!empty($applied_taxes)){
              foreach($applied_taxes as $applied_tax){
                $tax_id = $applied_tax['id'];
                $tax_instance = TaxType::withTrashed()->find($tax_id);
                $tax_name = $tax_instance->name;
                $tax_percent = $applied_tax['percent'];
                DB::table('tax_on_orders')->insert([
                  'order_id' => $order_id,
                  'tax_type_id' => $tax_id,
                  'tax_name' => $tax_name,
                  'tax_percent' => $tax_percent,
                ]); 
              }
            }
          }

          $orderdetails = $request->orderdetails;
          foreach($orderdetails as $orderdetail){
            $product_id = $orderdetail['product_id'];
            $product_instance = Product::withTrashed()->find($product_id);
            $product_name = $product_instance->product_name;
            $brand = $product_instance->brand;
            $variant_id = $orderdetail['variant_id'];
            $variant_colors = $orderdetail['variant_colors'];
            $rate = $orderdetail['rate'];
            $quantity = $orderdetail['quantity'];
            if($variant_id){
              $variant_instance = ProductVariant::find($variant_id);
              $variant_name = $variant_instance->variant;
              // $mrp = $variant_instance->mrp;
            }else{
              $variant_name = NULL;
              // $mrp = $product_instance->mrp;
            }
            $mrp = $orderdetail['mrp'];
            $unit = $orderdetail['unit_id'];
            $unit_instance = DB::table('unit_types')->where('id', $unit)->first();
            if($unit_instance){
              $unit_name = $unit_instance->name;
              $unit_symbol = $unit_instance->symbol;
            }else{
              $unit_name = "per unit";
              $unit_symbol = "per unit";
            }
            if($product_level_discount==1){
              $pdiscount = isset($orderdetail['pdiscount'])?$orderdetail['pdiscount'] : 0;
              $pdiscount_type = $orderdetail['pdiscount_type'];
              switch($pdiscount_type){
                case "Amt":
                  $discounted_rate = $mrp - $pdiscount; 
                  $sub_total = $discounted_rate * $quantity; 
                  break;
                case "%":
                  $discounted_rate = $mrp-(($pdiscount*$mrp)/100);
                  $sub_total = $discounted_rate * $quantity;
                  break;
                case "oAmt":
                  $sub_total = ($mrp*$quantity) - $pdiscount;
                  break;
                default:
                  break;
              }
            }else{
              $pdiscount = NULL;
              $pdiscount_type = "Amt";
              $sub_total = $rate * $quantity;
            }

            if($product_level_tax==1){
              $applied_taxes = $orderdetail['applied_tax'];
              if(!empty($applied_taxes)){
                $total_tax_percent = 0;
                foreach($applied_taxes as $applied_tax){
                  $total_tax_percent += $applied_tax['percent']; 
                }
                $amount = $sub_total + (($total_tax_percent*$sub_total)/100);
              }else{
                $amount = $sub_total;
              }
            }else{
              $amount = $sub_total;
            }
            
            $save_order_details = OrderDetails::create([
              "order_id"=> $order_id, "product_id"=> $product_id, "product_name"=> $product_name, "product_variant_id"=> $variant_id, "product_variant_name"=> $variant_name, "variant_colors"=> $variant_colors, "rate"=> $rate, "mrp"=> $mrp, "quantity"=> $quantity, "brand"=> $brand, "unit"=> $unit, "unit_name"=> $unit_name, "unit_symbol"=> $unit_symbol, "amount"=> $amount, "pdiscount"=> $pdiscount, "pdiscount_type"=> $pdiscount_type,
              "ptotal_amt"=> $amount
            ]);

            if($product_level_tax==1 && $save_order_details){
              if(!empty($applied_taxes)){
                foreach($applied_taxes as $applied_tax){
                  $tax_id = $applied_tax['id'];
                  DB::table('tax_on_orderproducts')->insert([
                    'orderproduct_id' => $save_order_details->id,
                    'tax_type_id' => $tax_id,
                    'product_id' => $product_id,
                  ]); 
                }
              }
            }
          }
        }

        DB::commit();
        $instance_order = Order::find($save_order->id);
        $data = array("status"=> 200, "msg"=> "Order Updated Successfully", "data" => new ClientOrderResource($instance_order));
        $notification_sent = $this->orderNotification($company_id, $outlet_id, $order_id, "update");
        return $data;
      }catch(Exception $e){
        $data = array("msg" => $e->getMessage());
        return response()->json($data); 
      }
    }

    private function getOutstandingAmount($client){
      try{
        $orders = $client->orders;
        $getOrderStatusFlag = ModuleAttribute::where('company_id', $client->company_id)->where('module_id', 1)->where('order_amt_flag', 1)->pluck('id')->toArray();
        if (!empty($getOrderStatusFlag)) {
          $tot_order_amount = $orders->whereIn('delivery_status_id', $getOrderStatusFlag)->sum('grand_total');
        } else {
          $tot_order_amount = 0;
        }
        
        $collections = $client->collections;
        if($collections){
          $cheque_collection_amount = $collections->where('payment_method', 'Cheque')->where('payment_status', 'Cleared')->sum('payment_received');
          $cash_collection_amount = $collections->where('payment_method', 'Cash')->sum('payment_received');
          $bank_collection_amount = $collections->where('payment_method', 'Bank Transfer')->sum('payment_received');
        }else{
          $cheque_collection_amount = 0;
          $cash_collection_amount = 0;
          $bank_collection_amount = 0;
        }
        $tot_collection_amount = $cheque_collection_amount + $cash_collection_amount + $bank_collection_amount;
        
        $outstading_amount = $client->opening_balance + $tot_order_amount - $tot_collection_amount;

        return $outstading_amount;
      }catch(Exception $e){
        Log::info($e->getMessasge());
        return 0;
      }
    }

    /*
      ** Old Update controller with all data send from APP not in use  
    */
    public function updateOld(Request $request, Order $order)
    {
      try{
        $editable = $order->module_status->order_edit_flag;
        if($editable==0){
          $data = array("status"=> 422, "msg"=> "Order can\'t be edited.", "data" => new ClientOrderResource($order));
        
          return $data;
        }
        $client_id = $request->client_id;
        $outlet_id = $request->outlet_id;
        $latitude = $request->latitude;
        $longitude = $request->longitude;
        $sub_total = $request->sub_total_amount;
        $tax_amount = $request->tax_amount;
        $discount = $request->discount;
        $discount_type = $request->discount_type;
        $grand_total = $request->grand_total;
        $product_level_tax = $request->product_level_tax;
        $product_level_discount = $request->product_level_discount;

        DB::beginTransaction();

        $save_order = tap($order)->update([
          "client_id"=> $client_id, "outlet_id"=> $outlet_id, "latitude"=> $latitude, "longitude"=> $longitude, "tot_amount"=> $sub_total, "tax"=> $tax_amount, "discount"=> $discount, "discount_type"=> $discount_type, "grand_total"=> $grand_total, "product_level_tax_flag"=> $product_level_tax, "product_level_discount_flag"=> $product_level_discount]);
        
        $orderdetails = $request->orderdetails;
        if($save_order){
          $order_id = $save_order->id;
          OrderDetails::where('order_id', $order_id)->delete();
          if($product_level_tax==0){
            $applied_taxes = $request->applied_tax;
            foreach($applied_taxes as $applied_tax){
              $tax_id = $applied_tax['id'];
              $tax_name = $applied_tax['name'];
              $tax_percent = $applied_tax['percent'];
              DB::table('tax_on_orders')->insert([
                'order_id' => $order_id,
                'tax_type_id' => $tax_id,
                'tax_name' => $tax_name,
                'tax_percent' => $tax_percent,
              ]); 
            }
          }

          foreach($orderdetails as $orderdetail){
            $product_id = $orderdetail['product_id'];
            $product_name = $orderdetail['product_name'];
            $variant_id = $orderdetail['variant_id'];
            $variant_name = $orderdetail['variant_name'];
            $variant_colors = $orderdetail['variant_colors'];
            $mrp = $orderdetail['mrp'];
            $brand = $orderdetail['brand'];
            $unit = $orderdetail['unit_id'];
            $unit_name = $orderdetail['unit_name'];
            $unit_symbol = $orderdetail['unit_symbol'];
            $rate = $orderdetail['rate'];
            $quantity = $orderdetail['quantity'];
            $amount = $orderdetail['amount'];
            $pdiscount = $orderdetail['pdiscount'];
            $pdiscount_type = $orderdetail['pdiscount_type'];
            $short_desc = $orderdetail['short_desc'];

            $save_order_details = OrderDetails::create([
              "order_id"=> $order_id, "product_id"=> $product_id, "product_name"=> $product_name, "mrp"=> $mrp,
              "brand"=> $brand, "unit"=> $unit, "unit_name"=> $unit_name, "unit_symbol"=> $unit_symbol, "rate"=> $rate,
              "quantity"=> $quantity, "amount"=> $amount, "pdiscount"=> $pdiscount, "pdiscount_type"=> $pdiscount_type,
              "ptotal_amt"=> $amount, "short_desc"=> $short_desc, "product_variant_id"=> $variant_id, "product_variant_name"=> $variant_name, "variant_colors"=> $variant_colors
            ]);

            if($product_level_tax==1 && $save_order_details){
              $applied_taxes = $orderdetail['applied_tax'];
              foreach($applied_taxes as $applied_tax){
                $tax_id = $applied_tax['id'];
                DB::table('tax_on_orderproducts')->insert([
                  'orderproduct_id' => $save_order_details->id,
                  'tax_type_id' => $tax_id,
                  'product_id' => $product_id,
                ]); 
              }
            }
          }
        }

        DB::commit();
        
        $data = array("status"=> 200, "msg"=> "Order Updated Successfully", "data" => new ClientOrderResource($save_order));
        
        return $data;
      }catch(Exception $e){
        $data = array("msg" => $e->getMessage());
        return response()->json($data); 
      }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Order $order)
    {
      try{
        $deletable = $order->module_status->order_delete_flag;
        if($deletable==0){
          $data = array("status"=> 422, "msg"=> "Order can't be deleted", "data" => new ClientOrderResource($order));
        
          return $data;
        }
        $company_id = $order->company_id;
        $outlet_id = $order->outlet_id;
        $order_id = $order->id;
        DB::beginTransaction();
        $object_instance = new ClientOrderResource($order);
        
        $orders = Order::where('orders.id', $order_id)
                  ->where('orders.company_id', $company_id)
                  ->where('orders.outlet_id', $outlet_id)
                  ->leftJoin('outlets', 'orders.employee_id', 'outlets.id')
                  ->leftJoin('clients', 'orders.client_id', 'clients.id')
                  ->leftJoin('client_settings', 'orders.company_id', 'client_settings.company_id')
                  ->leftJoin('module_attributes', 'orders.delivery_status_id', 'module_attributes.id')
                  ->select('orders.*', 'outlets.outlet_name as employee_name', 'clients.company_name as company_name', 'clients.name as client_name', 'client_settings.order_prefix', 'module_attributes.id as moduleattributesId', 'module_attributes.title as delivery_status', 'module_attributes.color', 'module_attributes.order_amt_flag', 'module_attributes.order_edit_flag', 'module_attributes.order_delete_flag')
                  ->whereNull('orders.deleted_at')
                  ->first();

        $order->orderdetails()->delete();
        $order->delete();        
        DB::commit();
        $data = array("status"=> 200, "msg"=> "Order Deleted Successfully", "data" => $object_instance);
        
        $employee_handles = DB::table('handles')->where('company_id', $company_id)->where('client_id', $orders->client_id)->pluck('employee_id')->toArray();
        
        $fbIDs = DB::table('employees')->where(array(array('company_id', $company_id), array('status', 'Active')))->whereNotNull('firebase_token')->whereIn('id', $employee_handles)->pluck('firebase_token');
        $dataPayload = array("data_type" => "order", "order" => $orders, "action" => "delete");
        $notification_sent = sendPushNotification_($fbIDs, 1, null, $dataPayload);

        return $data;
      }catch(Exception $e){
        $data = array("msg" => $e->getMessage());

        return response()->json($data); 
      }
    }
}
