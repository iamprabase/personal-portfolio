<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\CustomField;

class CustomFieldController extends Controller
{
    public function __construct()
    {
    	$this->middleware('auth:api');
    }

    public function index()
    {
    	$user = Auth::user();
    	$company_id = $user->company_id;
    	$custom_fields = CustomField::where('company_id',$company_id)->where('status',1)->where('for','Party')->get();
    	return response(['status'=>true,'data'=>$custom_fields]);
    }
}