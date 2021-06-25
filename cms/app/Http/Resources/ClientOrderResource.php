<?php

namespace App\Http\Resources;

use Illuminate\Support\Facades\Log;
use App\Http\Resources\OrderDetailsResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientOrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // return parent::toArray($request);
        $order_status = $this->deliverystatus;
        $delivery_status = $order_status['title'];
        $delivery_status_color = $order_status['color'];
        $product_level_tax_flag = $this->product_level_tax_flag;
        $product_level_discount_flag = $this->product_level_discount_flag;
        $order_prefix = $this->companies->clientsettings->order_prefix;
        $module_status_detail = $this->module_status;
        if($this->employee_id==0){
          $editable = $module_status_detail->order_edit_flag==1;
          $deletable = $module_status_detail->order_delete_flag==1;
          $update_route = $editable?route('api.outlet.order.update', [$this->id]):NULL;
          $delete_route = $deletable?route('api.outlet.order.delete', [$this->id]):NULL;
        }else{
          $editable = false;
          $deletable = false;
          $update_route = NULL;
          $delete_route = NULL;
        }
        if($this->employee_id!=0){
          $employee = $this->employee?$this->employee->withTrashed()->first():null;
          if($employee) $employee_name = $employee->name;
          else $employee_name = null;
        }else{
          $outlets = $this->outlets?$this->outlets()->withTrashed()->first():null;
          if($outlets) $employee_name = $outlets->outlet_name;
          else $employee_name = null;
        }
        if($product_level_tax_flag==0){
          $applied_taxes = $this->taxes()->withTrashed()->get();
          $data = [
                    'id' => $this->id,
                    'employee_id' => $this->employee_id,
                    'outlet_id' => $this->outlet_id,
                    'employee_name' => $employee_name,
                    'order_no' => $order_prefix.$this->order_no,
                    'sub_total_amount' => $this->tot_amount,
                    'tax_amount' => isset($this->tax)?$this->tax:"0.00",
                    'discount' => $this->discount,
                    'discount_type' => isset($this->discount_type)?$this->discount_type:"Amt",
                    'grand_total' => $this->grand_total,
                    'order_date' => $this->order_date,
                    'order_time' => isset($this->order_datetime)?date('H:i:s', strtotime($this->order_datetime)):"",
                    'latitude' => $this->latitude,
                    'longitude' => $this->longitude,
                    'delivery_status_id' => $this->delivery_status_id,
                    'delivery_status' => $delivery_status,
                    'delivery_status_color' => $delivery_status_color,
                    'product_level_tax' => $product_level_tax_flag,
                    'product_level_discount' => $product_level_discount_flag,
                    'applied_tax' => !empty($applied_taxes)?TaxResource::collection($applied_taxes):NULL,
                    'orderdetails' => OrderDetailsResource::collection($this->orderdetails),
                    'editable' => $editable,
                    'update_order' => $update_route,
                    'deletable' => $deletable,
                    'delete_order' => $delete_route,
                  ];
        }else{
          $data = [
                    'id' => $this->id,
                    'employee_id' => $this->employee_id,
                    'employee_name' => $this->employee_id!=0?$this->employee->name:$this->outlets()->withTrashed()->first()->outlet_name,
                    'order_no' => $order_prefix.$this->order_no,
                    'sub_total_amount' => $this->tot_amount,
                    'tax_amount' => isset($this->tax)?$this->tax:"0.00",
                    'discount' => $this->discount,
                    'discount_type' => isset($this->discount_type)?$this->discount_type:"Amt",
                    'grand_total' => $this->grand_total,
                    'order_date' => $this->order_date,
                    'order_time' => isset($this->order_datetime)?date('H:i A' ,strtotime($this->order_datetime)):"",
                    'latitude' => $this->latitude,
                    'longitude' => $this->longitude,
                    'delivery_status_id' => $this->delivery_status_id,
                    'delivery_status' => $delivery_status,
                    'delivery_status_color' => $delivery_status_color,
                    'product_level_tax' => $product_level_tax_flag,
                    'product_level_discount' => $product_level_discount_flag,
                    'orderdetails' => OrderDetailsResource::collection($this->orderdetails),
                    'editable' => $editable,
                    'update_order' => $update_route,
                    'deletable' => $deletable,
                    'delete_order' => $delete_route,
                  ];
        }
        return $data;
    }
}
