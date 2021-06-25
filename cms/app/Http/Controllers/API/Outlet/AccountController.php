<?php

namespace App\Http\Controllers\API\Outlet;

use Exception;
use App\Client;
use App\ClientSetting;
use App\ModuleAttribute;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AccountController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth:api-outlets');
  }

  private function getCurrency($companyID, $property){
    try{
      $settings = ClientSetting::whereCompanyId($companyID)->first();

      $currency_symbol = $settings->$property;

      return $currency_symbol;
    }catch(Excepption $e){
      return 'Currency Symbol';
    }
  }

  public function index(Client $client)
  {

    try{
      $currency = $this->getCurrency($client->company_id, 'currency_symbol');
      $orders = $client->orders;
      $getOrderStatusFlag = ModuleAttribute::where('company_id', $client->company_id)->where('module_id', 1)->where('order_amt_flag', 1)->pluck('id')->toArray();
      if (!empty($getOrderStatusFlag)) {
        $tot_order_amount = $orders->whereIn('delivery_status_id', $getOrderStatusFlag)->sum('grand_total');
      } else {
        $tot_order_amount = 0;
      }
      $order_amount = number_format($tot_order_amount,2);

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
      $collection_amount = number_format($tot_collection_amount,2);

      $outstading_amount = number_format(($client->opening_balance + $tot_order_amount - $tot_collection_amount),2);
      $credit_limit = $client->credit_limit?$currency.' '.number_format($client->credit_limit,2):"N/A";
      $opening_balance = number_format($client->opening_balance, 2);
      
      $due_amount = number_format($client->due_amount, 2);
      $available_balance = number_format($client->closing_balance, 2);

    //   $account_info["outstading_amount"] = $currency.' '.$outstading_amount;
    //   $account_info["credit_limit"] = $credit_limit;
    //   $account_info["opening_balance"] = $currency.' '.$opening_balance;
    //   $account_info["order_amount"] = $currency.' '.$order_amount;
    //   $account_info["collection_amount"] = $currency.' '.$collection_amount;
      
      $account_info["overdue_amount"] = $currency.' '.$due_amount;
      $account_info["credit_limit"] = $credit_limit;
      $account_info["available_balance"] = $currency.' '.$available_balance;
      

      $data = array("status"=> 200, "msg"=> "Account Status", "data" => $account_info);

      return $data;
    }catch(Exception $e){
      $data = array("status"=> 400, "msg"=> $e->getMessage(), "data" => array());

      return $data;
    }
    

    return $data;
  }
}
