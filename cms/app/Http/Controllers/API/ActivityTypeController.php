<?php

namespace App\Http\Controllers\API;

use App\ActivityType;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;

class ActivityTypeController extends Controller
{
	public function __construct()
	{
		$this->middleware('auth:api');
	}
	
    public function index()
    {
        $user = Auth::user();
        $company_id = $user->company_id;
    	$activityTypes = ActivityType::where('company_id',$company_id)->get();
    	$response = array('status'=>true,'message'=>'activityTypes','data'=>$activityTypes);
    	return response($response);
    }
}
