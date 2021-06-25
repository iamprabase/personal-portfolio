<?php

namespace App\Http\Controllers\API;

use App\BusinessType;
use App\Company;
use App\Employee;
use App\Http\Controllers\Controller;
use Auth;
use DB;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use stdClass;
use Log;


class CompanyStatusController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function login(Request $request)
    {
      $user = Auth::user();
      $companyDetails = Company::where('id', $user->company_id)->first();
      $domain = $companyDetails->domain;
      $msg = "";
      if($companyDetails->is_active==2){
        $company_status = 'Active';
      }elseif($companyDetails->is_active==1){
        $company_status = 'Disabled';
        $msg = "Your account has been disabled.";
      }elseif($companyDetails->is_active==0){
        $company_status = 'Expired';
        $msg = "Sorry, your subscription has ended.";
      }
      
      if($companyDetails){
        if($companyDetails->is_verified!=1) $msg = "Company is not verified.";
      }else{
        $msg = "Invalid Company Domain.";
      }
      if(!(empty($msg))){
        $response = array("status" => false, "message" => $msg, "data" => $domain);
        $this->sendResponse($response);
      }

      $subsAlerts = array('display'=> false, 'dismiss' => false, 'msg'=>"");
      $subsMsg = array("display" => false, "msg" => "");
      // if($user->managers->first() && $companyDetails->is_active==1){
      //   $msg = 'Sorry, your account has been disabled.';

      //   $response = array("status" => false, "message" => $msg, "data" => $domain);
      //   $this->sendResponse($response);
        
      // }elseif(!$user->managers->first() && $companyDetails->is_active==1){
      //   $msg = 'Account has been disabled.';
        
      //   $response = array("status" => false, "message" => $msg, "data" => $domain);
      //   $this->sendResponse($response);

      // }elseif(!$user->managers->first() && $companyDetails->is_active==0){
      //   $msg = 'Your subscription has ended.';
        
      //   $response = array("status" => false, "message" => $msg, "data" => $domain);
      //   $this->sendResponse($response);
      // }elseif($user->managers->first() && $companyDetails->is_active==0){
      //   $subsAlerts['msg'] = 'Your subscription has ended. Please contact us to enable your account.';
      //   $subsAlerts['dismiss'] = false;
      //   $subsAlerts['display'] = true;
      // }elseif($user->managers->first() && getCompanySubscriptionDate($companyDetails->id)){
      //   $subsAlerts['msg'] = 'Your subscription has ended. Please contact us to make payment.';
      //   $subsAlerts['dismiss'] = true;
      //   $subsAlerts['display'] = true;
      // }
      
      
      // else{
        // if($user->managers->first() && $companyDetails->is_active==0){
        //   $subsAlerts['msg'] = 'Your subscription has ended. Please contact us to enable your account.';
        //   $subsAlerts['dismiss'] = false;
        //   $subsAlerts['display'] = true;
        // }elseif($user->managers->first() && getCompanySubscriptionDate($companyDetails->id)){
        //   $subsAlerts['msg'] = 'Your subscription has ended. Please contact us to make payment.';
        //   $subsAlerts['dismiss'] = true;
        //   $subsAlerts['display'] = true;
        // }
        // else{
            // $subsAlerts = array('display'=> false, 'dismiss' => false, 'msg'=>"");
            // $daysPending = getCompanyPendingDays($companyDetails->id);
            // if ($user->managers->first() && $companyDetails->is_active!=0) {
            //     if ($daysPending['in_range']) {
            //         $subsMsg["display"] = true;
            //         $subsMsg["msg"] = "Hi! Your subscription is about to expire in {$daysPending['num_days']} days. To continue using Delta Sales App after {$daysPending['end_date']}, kindly make payment.";
            //     } elseif (getCompanySubscriptionDate($companyDetails->id) && $user->managers->first()) {
            //         $subsMsg["display"] = true;
            //         $subsMsg["msg"] = "Hi! Your subscription has ended. Please make payment.";
            //     }
            // }

          // }
          $data['subsMsg'] = $subsMsg;
          $data['subsAlerts'] = $subsAlerts;
          
          $response = array("status" => $subsAlerts['display'] && $subsMsg['display'], "message" => $company_status, "data" => $data);
          $this->sendResponse($response);
      // }

    }    

    private function sendResponse($response)
    {
      echo json_encode($response);
      exit;
    }
}
