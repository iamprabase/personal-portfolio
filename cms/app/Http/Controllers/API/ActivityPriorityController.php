<?php

namespace App\Http\Controllers\API;

use App\ActivityPriority;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;

class ActivityPriorityController extends Controller
{
    public function __construct()
	{
		$this->middleware('auth:api');
	}
	
    public function index()
    {
        $user = Auth::user();
        $company_id = $user->company_id;
    	$activityPriorities = ActivityPriority::where('company_id',$company_id)->get();
    	$response = array('status'=>true,'message'=>'activityPriorities','data'=>$activityPriorities);
    	return response($response);
    }
}
