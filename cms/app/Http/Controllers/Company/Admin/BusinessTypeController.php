<?php

namespace App\Http\Controllers\Company\Admin;

use App\BusinessType;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use View;

class BusinessTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store($domain,Request $request)
    {
    	$company_id = config('settings.company_id');
    	$businessType = BusinessType::where('company_id',$company_id)->where('business_name',$request->name)->first();
    	if($businessType){
    		$data= ['status'=>false,'message'=>'BusinessType already exists'];
    	}else{
    		$businessType = new BusinessType;
    		$businessType->business_name = $request->name;
    		$businessType->company_id = $company_id;
    		$businessType->save();
    		$business_types = BusinessType::where('company_id',$company_id)->orderBy('business_name','ASC')->get();
            $businessTypes = View::make('company.settings.ajaxbusinesslists',compact('business_types'))->render();
    		$data= ['status'=>true,'message'=>'BusinessType Added Successfully','businessTypes'=>$businessTypes];
        $dataPayload = array("data_type" => "business_type", "business_type" => $businessType, "action" => "add");
        $msgID = sendPushNotification_(getFBIDs($company_id), 29, null, $dataPayload);
      }
    	return $data;
    }

    public function update($domain,$id,Request $request)
    {
    	$company_id = config('settings.company_id');
    	$businessType = BusinessType::where('company_id',$company_id)->where('business_name',$request->name)->where('id','!=',$id)->first();
    	if($businessType){
    		$data= ['status'=>false,'message'=>'BusinessType already exists'];
    	}else{
    		$businessType = BusinessType::where('company_id',$company_id)->where('id',$id)->first();
    		$businessType->business_name = $request->name;
    		$businessType->company_id = $company_id;
    		$businessType->save();
    		$business_types = BusinessType::where('company_id',$company_id)->orderBy('business_name','ASC')->get();
            $businessTypes = View::make('company.settings.ajaxbusinesslists',compact('business_types'))->render();
        $data= ['status'=>true,'message'=>'BusinessType Updated Successfully','businessTypes'=>$businessTypes];
        $dataPayload = array("data_type" => "business_type", "business_type" => $businessType, "action" => "update");
        $msgID = sendPushNotification_(getFBIDs($company_id), 29, null, $dataPayload);
    	}
    	return $data;
    }

    public function destroy($domain,$id)
    {
    	$company_id = config('settings.company_id');
    	$businessType = BusinessType::where('company_id',$company_id)->where('id',$id)->first();
    	if($businessType){
    		if(count($businessType->clients)>0){
    			$data= ['status'=>false,'message'=>'Sorry! Could not delete this BusinessType because it been used by clients'];
    		}else{
          $businessType->delete();
	    		$business_types = BusinessType::where('company_id',$company_id)->orderBy('business_name','ASC')->get();
          $businessTypes = View::make('company.settings.ajaxbusinesslists',compact('business_types'))->render();
	    		$data= ['status'=>true,'message'=>'BusinessType Deleted Successfully','businessTypes'=>$businessTypes];
          $dataPayload = array("data_type" => "business_type", "business_type" => $businessType, "action" => "delete");
          $msgID = sendPushNotification_(getFBIDs($company_id), 29, null, $dataPayload);
    		}
    	}else{
    		$data= ['status'=>false,'message'=>'Sorry! Request BusinessType was not found.'];
    	}
    	return $data;
    }
}
