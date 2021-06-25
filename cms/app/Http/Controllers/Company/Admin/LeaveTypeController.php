<?php

namespace App\Http\Controllers\Company\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Leave;
use App\Employee;
use App\LeaveType;
Use Auth;
use DB;
use Log;
use View;

class LeaveTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request)
    {
        $company_id = config('settings.company_id');        
        $box = $request->all();        
        $requests=  array();
        parse_str($box['data'], $requests);
        if($requests['name']==""){
            return $data['result']="Can't Create LeaveType with empty name";
        }

        $leaveExists = LeaveType::where('company_id',$company_id)->where('name',$requests['name'])->first();
        if($leaveExists==null){
            $leaveType = new \App\LeaveType;
            $leaveType['name'] = $requests['name'];
            // $bank->status = $request->get('status');
            $leaveType['company_id'] = $company_id;
            $leaveType->save();
            $leaveTypes = LeaveType::where('company_id',$company_id)->orderBy('name','ASC')->get();
            $data['leavetypes'] = View::make('company.settings.ajaxleavetypelists',compact('leaveTypes'))->render();
            $data['result'] = true;
        }else{
            $data['result']=false;
        }
        notifyAppAboutChange($company_id,"leave_type");
        return $data;
    }

    public function update($domain,$id,Request $request)
    {
        $box = $request->all();        
        $requests=  array();
        parse_str($box['data'], $requests);
        $company_id = config('settings.company_id');
        $leaveTypeExists = LeaveType::where('company_id',$company_id)->where('name',$requests['name'])->first();
        if($leaveTypeExists==null){
            $leaveType = LeaveType::findOrFail($id);
            $leaveType->name = $requests['name'];
            $leaveType->save();
            $leaveTypes = LeaveType::where('company_id',$company_id)->orderBy('name','ASC')->get();
            $data['leaveTypes'] = View::make('company.settings.ajaxleavetypelists',compact('leaveTypes'))->render();
            $data['result']=true;
        }else{
            $data['result']=false;
        }

        notifyAppAboutChange($company_id,"leave_type");
        return $data;
    }

    public function destroy($domain,$id,Request $request)
    {
        $leaveType = LeaveType::findOrFail($id);
        if($leaveType->leaves->count()==0){
            $leaveType->delete();
            $company_id = config('settings.company_id');
            $leaveTypes = LeaveType::where('company_id',$company_id)->orderBy('name','ASC')->get();
            $data['leaveTypes'] = View::make('company.settings.ajaxleavetypelists',compact('leaveTypes'))->render();
            $data['result']=true;
        }else{
            $data['result'] = false;
        }

        notifyAppAboutChange($company_id,"leave_type");

        return $data;
    }

   
}